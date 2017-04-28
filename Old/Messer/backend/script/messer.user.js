// ==UserScript==
// @name            Messer
// @description     Don't mess with Messer!
// @author          God
// @version         0.3.0
// @namespace       http://random.ihostwell.com
//
// @include         https://docs.google.com/forms/*/*/viewform*
// @grant           GM_getResourceText
// @grant           GM_xmlhttpRequest
// --- RESOURCES ---
// @resource        rolar               ./rolar.js
// @resource        harlem              ./harlem.js
// @resource        katamari            ./katamari.js
// @resource        flash               ./flash.js
// @resource        gravity             ./gravity.js
// @resource        ameno               ./ameno.js
// @resource        message             ./message.js
// @resource        nyan                ./nyan.js
// @resource        charlie             ./charlie.js
// @resource        jquery.min          ./jquery.min.js
// ==/UserScript==

(function() {

    var host = "http://random123.esy.es";
    var actionRunning = false;
    var lastTimeRequested = 0;
    var lastUpdateTime = 0;
    var liveRequest = {};

    function selectEffect(list) {
        var probTotal = 0;
        for(var i = 0; i < list.length; i++) {
            probTotal += parseInt(list[i].probability);
        }

        var randVal = Math.floor(Math.random() * probTotal);

        probTotal = 0;
        for(i = 0; i < list.length; i++) {
            probTotal += parseInt(list[i].probability);

            if(probTotal > randVal) {
                return i;
            }
        }

        return -1;
    }

    function loadedRequest(response)
    {
        if(response.readyState==4 && response.status==200)
        {
            if(!actionRunning)
            {
                var effectList = JSON.parse(response.responseText);
                var selectedEffect = selectEffect(effectList);

                var effect = typeof effectList[selectedEffect] != "undefined" ? effectList[selectedEffect] : {"name": "None", "codename": "none", "probability": 0, "resources": []};
                console.log(effect.name);

                if(effect.codename != "none")
                {
                    actionRunning = true;

                    document.body.style.minHeight = (window.innerHeight || document.documentElement.clientHeight ||
                                                     document.getElementsByTagName('body')[0].clientHeight) - 20 + "px";

                    injectEffectSettings(effect.settings);

                    for(var i=0; i < effect.resources.length; i++)
                    {
                        injectResource(effect.resources[i]);
                    }
                }
            }
        }
    }

    function clickedInput()
    {
        GM_xmlhttpRequest({
            "method": "GET",
            "url": host+"/messer/get.php?settings=1",
            "onload": loadedRequest
        });

        input.removeEventListener("focus", clickedInput, true);
    }

    function checkLiveTrigger(response)
    {
        if(response.readyState==4)
        {
            console.log("Got response");

            if(response.status==200)
            {
                var remoteExec = JSON.parse(response.responseText);
                if(typeof remoteExec == "undefined")
                {
                    setTimeout(waitForLiveTrigger, 10000);
                    return;
                }

                if(remoteExec.gotLive)
                {
                    lastUpdateTime = remoteExec.lastUpdateTime;

                    if(remoteExec.action == "execute" && !actionRunning)
                    {
                        input.removeEventListener("focus", clickedInput, true);

                        var effect = remoteExec.effect;
                        console.log(effect.name);

                        if(effect.codename != "none")
                        {
                            actionRunning = true;

                            document.body.style.minHeight = (window.innerHeight || document.documentElement.clientHeight ||
                                                             document.getElementsByTagName('body')[0].clientHeight) - 20 + "px";

                            injectEffectSettings(effect.settings);

                            for(var i=0; i < effect.resources.length; i++)
                            {
                                injectResource(effect.resources[i]);
                            }
                        }
                    }
                    else if(remoteExec.action == "stop")
                    {
                        unsafeWindow.onbeforeunload = null;
                        location.reload();
                        return;
                    }

                    waitForLiveTrigger();
                }
                else if(remoteExec.lastUpdateTime)
                {
                    lastUpdateTime = remoteExec.lastUpdateTime;
                    waitForLiveTrigger();
                }
                else
                {
                    setTimeout(waitForLiveTrigger(),1000);
                }
            }
            else
            {
                setTimeout(waitForLiveTrigger(),30000);
            }
        }
    }

    function waitForLiveTrigger()
    {
        console.log("Request...");

        var date = new Date();
        lastTimeRequested = date.getTime();

        if(typeof liveRequest.abort === 'function')
        {
            liveRequest.abort();
        }
        liveRequest = GM_xmlhttpRequest({
            "method": "GET",
            "url": host+"/messer/get.php?waitForTrigger=1&lastUpdateTime="+String(lastUpdateTime),
            "onreadystatechange": checkLiveTrigger
        });
    }

    function verifyWeAreLive() {
        var date = new Date();
        if(date.getTime() - lastTimeRequested > 35000)
        {
            console.log("Starting live check...");
            waitForLiveTrigger();
        }
    }

    function getLastUpdateTime()
    {
        GM_xmlhttpRequest({
            "method": "GET",
            "url": host+"/messer/get.php?getLastUpdateTime=1",
            "onreadystatechange": function(response)
            {
                if(response.readyState==4)
                {
                    if(response.status==200)
                    {
                        lastUpdateTime = parseInt(response.responseText);

                        verifyWeAreLive();
                        setInterval(verifyWeAreLive, 20000);
                    }
                    else
                    {
                        setTimeout(getLastUpdateTime(), 30000);
                    }
                }
            }
        });
    }

    function injectScript(scriptContents) {
        var s = document.createElement('script');
        s.textContent = scriptContents;
        document.head.appendChild(s);
        s.onload = function() {
            s.parentNode.removeChild(s);
        };
    }

    function injectResource(resourceName) {
        injectScript(GM_getResourceText(resourceName));
    }

    function injectEffectSettings(effectSettings) {
        var text = "effectSettings = " + JSON.stringify(effectSettings) + ";";
        injectScript(text);
    }

    var input = document.getElementsByTagName("input")[0];
    input.addEventListener("focus", clickedInput, true);

    getLastUpdateTime();


    function testSelectEffect(numberOfRuns) {
        var request = new XMLHttpRequest();
        request.open("GET", "http://random.ihostwell.com/messer/messer.json", false);
        request.send(null);
        var testList = JSON.parse(request.responseText);

        for(var i = 0; i < testList.length; i++) {
            testList[i].timesSelected = 0;
        }

        for(i = 0; i < numberOfRuns; i++) {
            testList[selectEffect(testList)].timesSelected += 1;
        }

        console.log("Tested " + numberOfRuns + " times.\n");
        console.log(testList);
    }

})();
