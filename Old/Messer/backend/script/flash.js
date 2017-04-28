/* 
	Effect: Flashing background
	Made with <3 for Messer!
*/

(function() {

function rand(min, max) {
    return parseInt(Math.random() * (max-min+1), 10) + min;
}

function getRandomColor() {
    var h = rand(1, 360);
    var s = rand(30, 100);
    var l = rand(30, 70);
    return 'hsl(' + h + ',' + s + '%,' + l + '%)';
}


setInterval(function() {
	document.body.style.cssText = "background: " + getRandomColor();
}, 100);

})();
