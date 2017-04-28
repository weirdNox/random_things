/* 
	Effect: Rolar no chão
	Made with <3 for Messer!
*/


(function() {

	var video = document.createElement('iframe');
	video.width = '420';
	video.height = '315';
	video.src = "//www.youtube.com/embed/zQ1atOsx7PU?rel=0&amp;controls=0&amp;showinfo=0&autoplay=1&loop=1";
	video.frameborder = "0";
	video.style.cssText = "display: none;";
	document.body.appendChild(video);

	setInterval(tryRolar, 1000);

	var rolarCssAdded = false;
	var rolando = false;

	function rolar() {
		if(!rolarCssAdded) {
			document.body.childNodes[0].style.overflow = "hidden";
			var rolarCss = document.createElement('style');
			rolarCss.type = "text/css";
			rolarCss.textContent = "\
			@-webkit-keyframes rolar {\
			    0%   {transform: rotate(0deg);}\
			    100% {transform: rotate(360deg);}\
			}";

			document.head.appendChild(rolarCss);
			rolarCssAdded = true;
		}

		if(!rolando) {
			document.body.childNodes[0].style.WebkitAnimation = "rolar 3s ease-in-out 0s 1";
			document.body.childNodes[0].addEventListener("webkitAnimationEnd", rolarEnded);

			rolando = true;
		}
	}

	function tryRolar() {
		var randVal = Math.floor(Math.random() * 100);
		if(randVal < 15) {
			rolar();
		}
	}

	function rolarEnded() {
		document.body.childNodes[0].style.WebkitAnimation = "";
		document.body.childNodes[0].removeEventListener("webkitAnimationEnd", rolarEnded);
		rolando = false;
	}

})();