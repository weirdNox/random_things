/*
	Effect: Message
	Made with <3 for Messer!
*/

(function() {
if(typeof effectSettings === "undefined") {
	throw "No effectSettings defined!";
}

var css = document.createElement("style");
css.type = "text/css";
css.textContent = "\
.message-box {\
    width: 97%;\
	border: 1px solid;\
	border-radius: 9px;\
	box-sizing: border-box;\
	margin: 15px auto;\
	font-size: 18px;\
	transition: all .7s;\
	overflow: hidden;\
}\
.message-box .title {\
	font-weight: bold;\
	font-size: 25px;\
	text-align: center;\
}\
.message-box.low {\
	background: #e5f5f9;\
	color: #248AA3;\
	border-color: #cae0e5;\
}\
.message-box.medium {\
	background: #E7E48F;\
	color: #816210;\
	border-color: #C2A138;\
}\
.message-box.high {\
	background: rgba(235, 104, 104, 0.75);\
	color: #6D2121;\
	border-color: #C23875;\
}\
.message-box.closed {\
	padding: 0;\
    max-height: 0;\
    border-width: 0;\
    margin-top: 0;\
    margin-bottom: 0;\
}";

document.head.appendChild(css);

var infoBox = document.createElement("div");
infoBox.classList.add("message-box");
infoBox.classList.add("closed");

switch(effectSettings.warningLevel)
{
case "low":
	infoBox.classList.add("low");
	break;
case "medium":
	infoBox.classList.add("medium");
	break;
case "high":
	infoBox.classList.add("high");
	break;
default:
	infoBox.classList.add("low");
	break;
}

var title = document.createElement("div");
title.classList.add("title");
infoBox.appendChild(title);
title.textContent = effectSettings.title;

var content = document.createElement("div");
content.classList.add("content");
infoBox.appendChild(content);
content.textContent = effectSettings.content;

document.getElementsByClassName("ss-form-heading")[0].insertBefore(infoBox, document.getElementsByClassName("ss-form-heading")[0].childNodes[1]);

setTimeout(function() {
	infoBox.style.padding = "15px";
	infoBox.style.height = title.offsetHeight + content.offsetHeight + 70 + "px";
	infoBox.style.maxHeight = title.offsetHeight + content.offsetHeight + 70 + "px";
	infoBox.classList.remove("closed");
}, 500);

})();
