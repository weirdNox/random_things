// ==UserScript==
// @name            Ah...
// @description     Ah...
// @author          Ah...
// @version         4.4.0.0.0
// @namespace       http://Ah...
// @grant           GM_log
//
// @include         https://www.facebook.com/
// ==/UserScript==

(function() {
    function parseQuery(qstr) {
        var query = {};
        var a = qstr.split('&');
        for (var i = 0; i < a.length; i++) {
            var b = a[i].split('=');
            query[decodeURIComponent(b[0])] = decodeURIComponent(b[1] || '');
        }
        return query;
    }

    function encodeQuery(data) {
        var ret = [];
        for (var d in data) {
            if(data[d])
                ret.push(encodeURIComponent(d) + "=" + encodeURIComponent(data[d]));
            else
                ret.push(encodeURIComponent(d));
        }
        return ret.join("&");
    }

    var tourettesList = ["banana", "gelado", "arroz", "eu sou lindo"];

    (function(send) {
        XMLHttpRequest.prototype.send = function(data) {
            if(typeof data !== "string") {
                send.call(this, data);
                return;
            }

            data = parseQuery(data);
            if("message_batch[0][body]" in data) {
                // NOTE(nox): Tourette's syndrome
                // wordArray = data["message_batch[0][body]"].split(' ');
                // for(var i = 0; i <= wordArray.length; i++) {
                //     if(Math.random() < 0.15) {
                //         var word = tourettesList[Math.floor(Math.random()*tourettesList.length)];

                //         if(Math.random() < 0.3) {
                //             word = word.toUpperCase();
                //             if(Math.random() < 0.1) {
                //                 word += "!!!!";
                //             }
                //         }

                //         wordArray.splice(i, 0, word);
                //         i += 1;
                //     }
                // }
                // data["message_batch[0][body]"] = wordArray.join(' ');

                // NOTE(nox): Ah... replacement
                // data["message_batch[0][body]"] = data["message_batch[0][body]"].replace(/a/gi, 'Ah...');

                // GM_log(JSON.stringify(data));
                // data["message_batch[0][sticker_id]"] = "685209894921647";
                data["message_batch[0][tags][0]"] = "hot_emoji_size:large";
                data["message_batch[0][body]"] = "👻👽🤖😺";
            }

            send.call(this, encodeQuery(data));
        };
    })(XMLHttpRequest.prototype.send);
})();
