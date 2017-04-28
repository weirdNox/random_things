/*
	Effect: Nyan Cats
	Made with <3 for Messer!
*/

(function() {

var duration = 5;

function outerWidth(el) {
  var width = el.offsetWidth;
  var style = getComputedStyle(el);

  width += parseInt(style.marginLeft) + parseInt(style.marginRight);
  return width;
}

var flyRight = function(cat) {
	var leftPos = -cat.offsetWidth;
	var leftEnd = outerWidth(document.body);
	var topPos = Math.random() * (document.body.offsetHeight - cat.offsetHeight);

	cat.style.left = leftPos + "px";
	cat.style.top = topPos + "px";

	setTimeout(function() {
		cat.style.transition = "left " + duration + "s linear";
		setTimeout(function(){
			cat.style.left = leftEnd + "px";
			setTimeout(function() {
				if(document.contains(cat)) {
					moar(cat);
					(flyNext())(cat);
				}
			}, duration * 1000);

		}, 100);
	}, Math.random() * 3000);
};

var flyLeft = function(cat) {
	var leftPos = outerWidth(document.body);
	var leftEnd = -cat.offsetWidth;
	var topPos = Math.random() * (document.body.offsetHeight - cat.offsetHeight);

	cat.style.left = leftPos + "px";
	cat.style.top = topPos + "px";

	setTimeout(function() {
		cat.style.transition = "left " + duration + "s linear";
		setTimeout(function(){
			cat.style.left = leftEnd + "px";
			setTimeout(function() {
				if(document.contains(cat)) {
					moar(cat);
					(flyNext())(cat);
				}
			}, duration * 1000);

		}, 100);
	}, Math.random() * 3000);
};

var rotateRight = function(cat) {
	var leftPos = -cat.offsetWidth;
	var leftEnd = document.body.offsetWidth;
	var topPos = Math.random() * (document.body.offsetHeight - cat.offsetHeight);

	cat.style.left = leftPos + "px";
	cat.style.top = topPos + "px";

	setTimeout(function() {
		cat.style.transition = "left "+duration+"s linear, transform "+duration+"s linear";
		setTimeout(function(){
			cat.style.left = leftEnd + "px";
			cat.style.transform = "rotate(-700deg)";
			setTimeout(function() {
				if(document.contains(cat)) {
					moar(cat);
					(flyNext())(cat);
				}
			}, duration * 1000);

		}, 100);
	}, Math.random() * 3000);
};

var rotateLeft = function(cat) {
	var leftPos = document.body.offsetWidth;
	var leftEnd = -cat.offsetWidth;
	var topPos = Math.random() * (document.body.offsetHeight - cat.offsetHeight);

	cat.style.left = leftPos + "px";
	cat.style.top = topPos + "px";

	setTimeout(function() {
		cat.style.transition = "left "+duration+"s linear, transform "+duration+"s linear";
		setTimeout(function(){
			cat.style.left = leftEnd + "px";
			cat.style.transform = "rotate(700deg)";
			setTimeout(function() {
				if(document.contains(cat)) {
					moar(cat);
					(flyNext())(cat);
				}
			}, duration * 1000);

		}, 100);
	}, Math.random() * 3000);
};

var flyFunctions = [flyRight, flyLeft, rotateRight, rotateLeft];

function moar(cat) {
	cat.style.transition = "";
	cat.style.transform = "";
	var width = parseInt(cat.style.width);
	var height = parseInt(cat.style.height);

	if(width > 900) {
		return;
	}

	if(width < 250) {
		cat.style.width = width * 1.6 + "px";
		cat.style.height = height * 1.6 + "px";
	}
	else if(width < 500) {
		cat.style.width = width * 1.4 + "px";
		cat.style.height = height * 1.4 + "px";
	}
	else {
		cat.style.width = width * 1.2 + "px";
		cat.style.height = height * 1.2 + "px";
	}
}

var cats = 0;
function flyflyfly() {
	if(cats < 50) {
		cats++;
		var newCatBox = document.createElement("div");

		var newCat = document.createElement("img");
		newCat.src = "http://random.ihostwell.com/messer/img/nyan.gif";
		newCat.addEventListener("click", function() {
				newCat.parentNode.parentNode.removeChild(newCat.parentNode);
				cats--;
				flyflyfly();
				flyflyfly();
				if ((Math.floor(Math.random() * 3)) < 1) {flyflyfly();}
		});

		newCat.classList.add("nyancat");
		newCatBox.appendChild(newCat);
		document.getElementById("nyanbox").appendChild(newCatBox);

		newCat.style.width = 301 / 5 + "px";
		newCat.style.height = 119 / 5 + "px";
		newCat.style.top = document.body.offsetHeight + "px";

		setTimeout(function() {
			(flyNext())(newCat);
		}, Math.random() * 2000);
	}
}

function flyNext() {
	var x = Math.floor(Math.random() * flyFunctions.length);
	if ((Math.floor(Math.random() * 3 * cats)) < 1) {flyflyfly();}
	return flyFunctions[x];
}

function iterateAndSpan(node) {
	var childNodes = node.childNodes;
	var nodeText = "";

	if(typeof childNodes[0] !== "undefined" && childNodes[0].nodeType == 3) {
		nodeText = childNodes[0].textContent;
	}

	for(var i = 0; i<childNodes.length; i++) {
		if(childNodes[i].nodeType == 3) {
		}

		else {
			iterateAndSpan(childNodes[i]);
		}
	}

	var nodeHTML = node.innerHTML.replace(nodeText, "");
	nodeText = nodeText.replace(/([a-zA-ZÁÉÍÓÚáéíóúãç\-:\(\)])/g, "<span class='nyanText'>$&</span>");
	node.innerHTML = nodeText + nodeHTML;
}

iterateAndSpan(document.body);
var spanNodes = document.getElementsByClassName("nyanText");

var availableColors = ["#FF0000", "#FF9900", "#FFFF00", "#33FF00", "#0099FF", "#6633FF"];

setInterval(function() {
	for(var i = 0; i<spanNodes.length; i++) {
		spanNodes[i].style.color = availableColors[Math.floor(Math.random() * 6)];
	}
}, 300);

var css = document.createElement('style');
css.type = "text/css";
css.textContent = "\
#nyanbox div{position:absolute;}\
.nyancat{position:absolute; z-index: 2;}\
@-webkit-keyframes backgroundAnim {\
	0%   {background-position: 0% 0%;}\
	100% {background-position: -65.5% 0%;}\
}\
";

document.head.appendChild(css);

for(var xz = 0; xz<document.body.childNodes.length; xz++) {
	document.body.childNodes[xz].style.zIndex = 1;
	document.body.childNodes[xz].style.position = "relative";
}

var container = document.createElement('div');
container.id = "nyanbox";
container.style.cssText = "position: absolute; left: 0; right: 0; top: 0; height: "+document.body.offsetHeight+"px; overflow: hidden;";
document.body.appendChild(container);

var background = document.createElement("div");
background.style.width = document.body.offsetWidth + "px";
background.style.height = document.body.offsetHeight + "px";
background.style.backgroundImage = "url(http://random.ihostwell.com/messer/img/nyanback.jpg)";
background.style.backgroundSize = "500px auto";
background.style.opacity = 0;
background.style.webkitAnimation = "backgroundAnim 1.5s infinite linear";
container.appendChild(background);

background.style.transition = "opacity 7s";
document.getElementsByClassName("ss-form-container")[0].style.transition = "all 7s";
document.getElementsByClassName("ss-form-title")[0].style.transition = "all 7s";

setTimeout(function() {
	background.style.opacity = 1;
	document.getElementsByClassName("ss-form-container")[0].style.background = "none";
	document.getElementsByClassName("ss-form-container")[0].style.border = "none";
	document.getElementsByClassName("ss-form-title")[0].style.backgroundPosition = "center -500px";
	document.getElementsByClassName("ss-form-title")[0].style.backgroundColor = "transparent";
	document.getElementsByClassName("ss-form-title")[0].style.border = "none";
	setTimeout(function() {
		document.getElementsByClassName("ss-form-title")[0].style.background = "none";
	}, 7000);
}, 100);

var video = document.createElement('iframe');
video.width = '420';
video.height = '315';
video.src = "//www.youtube.com/embed/QH2-TGUlwu4?rel=0&amp;controls=0&amp;showinfo=0&autoplay=1&loop=1";
video.frameborder = "0";
video.style.cssText = "display: none;";
document.body.appendChild(video);

flyflyfly();
flyflyfly();
if ((Math.floor(Math.random() * 5)) < 1) {flyflyfly();}

})();
