/* 
	Effect: Ameno
	Made with <3 for Messer!
*/

(function() {
for(var xz = 0; xz<document.body.childNodes.length; xz++) {
	document.body.childNodes[xz].style.zIndex = 1;
	document.body.childNodes[xz].style.position = "relative";
}

var container = document.createElement('div');
container.style.cssText = "position: absolute; left: 0; right: 0; top: 0; height: "+document.body.offsetHeight+"px; overflow: hidden;";

var candleDiv1 = document.createElement('div');
candleDiv1.style.cssText = "position: absolute; width: 130px; height: 310px; left: 90px; top: 220px; overflow: hidden; z-index: 5;";
container.appendChild(candleDiv1);

var candleDiv2 = document.createElement('div');
candleDiv2.style.cssText = "position: absolute; width: 130px; height: 310px; right: 90px; top: 220px; overflow: hidden; z-index: 5;";
container.appendChild(candleDiv2);

var candleCanvas1 = document.createElement('canvas');
candleCanvas1.style.cssText = "position: absolute; left: -133px; opacity: 0; transition: opacity 8.5s;";
candleCanvas1.width = "400";
candleCanvas1.height = "300";
candleDiv1.appendChild(candleCanvas1);
drawCandle(candleCanvas1);

var candleCanvas2 = document.createElement('canvas');
candleCanvas2.style.cssText = "position: absolute; left: -133px; opacity: 0; transition: opacity 8.5s;";
candleCanvas2.width = "400";
candleCanvas2.height = "300";
candleDiv2.appendChild(candleCanvas2);
drawCandle(candleCanvas2);

var sword = document.createElement('img');
sword.style.cssText = "position: absolute; right: 205px; top: 310px; width: 500px; transform: rotate(-71deg); opacity: 0; transition: opacity 9s; z-index: 5;";
sword.src = "http://random.ihostwell.com/messer/img/sword.png";
container.appendChild(sword);

document.body.appendChild(container);

var audio = document.createElement("audio");
audio.src = "http://1in.kz/s/music/1307418408_era-ameno.mp3";
audio.loop = true;

audio.addEventListener("timeupdate", timeUpdate);
audio.addEventListener("canplaythrough", play);

document.body.appendChild(audio);

var a = 0;
var b = 0.1;

function play(){
	audio.play();
	document.body.style.transition = "background-color 9s";
	document.body.style.background = "#ACACAC";

	setTimeout(function() {
		candleCanvas1.style.opacity = 1;
		candleCanvas2.style.opacity = 1;
	}, 500);

	setInterval(tryAddSword, 1000);

	audio.removeEventListener("canplaythrough", play);
}

function timeUpdate() {
	var time = audio.currentTime;

	if(time >= 21.5) {
		setInterval(function() {
			a += b;
			if(a>=3){b=-0.1;}
			if(a<=-3){b=0.1;}
			drawing(candleCanvas1, a);
			drawing(candleCanvas2, a);
		}, 10);

		document.body.style.transition = "background-color .25s";
		document.body.style.background = "#FFF";

		setTimeout(function() {
			document.body.style.transition = "background-color .5s";
			document.body.style.transitionTimingFunction = "ease-out";
			document.body.style.background = "#000";
		}, 250);

		setTimeout(function() {
			sword.style.opacity = 1;
		}, 10000);

		audio.removeEventListener("timeupdate", timeUpdate);
	}
}

function drawCandle(canvas) {
	var context = canvas.getContext('2d');
	// top of the candle
	context.beginPath();
	context.moveTo(190,150);
	context.arc(200,150,22,0,Math.PI+0.1,false);
	context.fillStyle='lightgrey';
	context.fill();
	// wax
	context.beginPath();
	context.moveTo(180,150);
	context.rect(180,150,40,150);
	context.fillStyle = 'lightgrey';
	context.fill();
	// the wick
	context.beginPath();
	context.moveTo(197,135);
	context.fillStyle="black";
	context.fill();
	context.fillRect(197,135,5,15);
	context.closePath();
}

function drawing(canvas, a){
	var context = canvas.getContext('2d');
	// clear canvas
	context.clearRect(0,0,400,300);
	// top of the candle
	context.beginPath();
	context.moveTo(190,150);
	context.arc(200,150,22,0,Math.PI+0.1,false);
	context.fillStyle='lightgrey';
	context.fill();

	//yellow part of flame
	context.beginPath();
	context.moveTo(188,150);
	context.quadraticCurveTo(200+a,18-a,212,150);
	context.closePath();
	context.fillStyle="#FFFE00";
	context.fill();

	//white part of flame
	context.beginPath();
	context.moveTo(193,150);
	context.quadraticCurveTo(200+a,25-a,207,150);
	context.closePath();
	context.fillStyle="#E9DD00";
	context.fill();

	// blue part of flame
	context.beginPath();
	context.moveTo(194,150);
	context.quadraticCurveTo(200+a,90+a,206,150);
	context.fillStyle="rgba(0,0,255,0.8)";
	context.fill();

	// the wick
	context.beginPath();
	context.moveTo(197,135);
	context.fillStyle="black";
	context.fill();
	context.fillRect(197,135,5,15);
	context.closePath();

	// wax
	context.beginPath();
	context.moveTo(180,150);
	context.rect(180,150,40,150);
	context.fillStyle = 'lightgrey';
	context.fill();
}

var flyingCssAdded = false;

function addFlyingSword() {
	if(!flyingCssAdded) {
		var flyingCss = document.createElement('style');
		flyingCss.type = "text/css";
		flyingCss.textContent = "\
		@-webkit-keyframes flyingSword {\
		    0%   {left: -310px;}\
		    100% {left: "+(document.body.offsetWidth+310)+"px;}\
		}\
		@-webkit-keyframes rotating {\
		    0%   {transform: rotate(0deg);}\
		    100% {transform: rotate(360deg);}\
		}";

		document.head.appendChild(flyingCss);
		flyingCssAdded = true;
	}

	var flyingSword = document.createElement('img');
	flyingSword.style.cssText = "width: 300px; position: absolute; left: -310px; z-index: 100;";
	flyingSword.style.WebkitAnimation = "flyingSword 3s linear, rotating 1.5s 0s infinite linear";
	flyingSword.style.top = Math.floor(Math.random() * 601) + 100 + "px";

	flyingSword.src = "http://random.ihostwell.com/messer/img/sword.png";
	container.appendChild(flyingSword);

	setTimeout(function() {
		flyingSword.parentNode.removeChild(flyingSword);
	}, 4000);
}

function tryAddSword() {
	var randVal = Math.floor(Math.random() * 100);
	if(randVal < 10) {
		addFlyingSword();
	}
}

})();