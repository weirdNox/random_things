/*
    Effect: Je suis Charlie
    Made with <3 for Messer!
*/

(function() {

var header = document.getElementsByClassName("ss-form-title")[0];
header.style.transition = "background 2s, height 2s";
header.style.height = header.offsetHeight + "px";

loadFont();

setTimeout(function() {
    header.style.paddingLeft = "0";
    header.style.paddingRight = "0";
    header.textContent = "";
    header.style.background = "black";
    header.style.height = "540px";
    continueJob();
}, 1000);

function continueJob() {
    var container = document.createElement('div');
    container.className = "container";
    container.style.fontFamily = "'Yanone Kaffeesatz', sans-serif";
    // Center vertically
    container.style.position = "relative";
    container.style.top = "50%";
    container.style.transform = "translateY(-50%)";
    container.style.opacity = 0;
    container.style.transition = "opacity 2s";
    container.style.fontWeight = "bold";

    var jeSuis = document.createElement('div');
    jeSuis.style.color = "white";
    jeSuis.style.fontSize = "130px";
    jeSuis.style.textAlign = "center";
    jeSuis.style.lineHeight = "108px";
    jeSuis.textContent = "Je suis"
    container.appendChild(jeSuis);

    var charlie = document.createElement('div');
    charlie.style.color = "#D4D4D4";
    charlie.style.fontSize = "130px";
    charlie.style.textAlign = "center";
    charlie.style.lineHeight = "108px";
    charlie.textContent = "Charlie"
    container.appendChild(charlie);

    header.appendChild(container);
    container.parentNode.style.transformStyle = "preserve-3d";

    setTimeout(function() {
        container.style.opacity = 1;
    }, 200);


    var input = document.getElementsByTagName("input")[0];
    input.addEventListener("input", function() {
        input.value == "" ? charlie.textContent = "Charlie" :
                            charlie.textContent = capitalizeEachWord(input.value);
    }, true);

}

function capitalizeEachWord(str) {
    return str.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

function loadFont() {
    WebFontConfig = {
        google: { families: [ 'Yanone+Kaffeesatz:700,400:latin' ] }
    };
    (function() {
        var wf = document.createElement('script');
        wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
                '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
        wf.type = 'text/javascript';
        wf.async = 'sync';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(wf, s);
    })();
}


})();
