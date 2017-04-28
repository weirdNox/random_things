imgs = [];
setTheme("xmas", "12/31/2012");	// MONTH/DAY/YEAR

$(document).ready(function(e) {
	//Sliders
	$(".expand-span").hide();
	$('.initial-contract').parent().next("div.content-module-main").hide(0, function () {
		$(this).parent().children('div.content-module-heading').children('.span-text').toggle();
	});
	
	$("div.content-module-heading").click(function(e) {
		e.preventDefault();
		$('.span-text').css('color', '#969DAC');	
		$(this).next("div.content-module-main").stop(true).slideToggle(750);
		$(this).children('.span-text').toggle();
	});
	
	//Todo List Management
	$('.todo').children().children('.done').append('<em style="text-decoration:none; color: #9498a1; display:inline-block; padding-left:0.5em;">Feito.</em>');
	
	$('.todo').children().children('.not-confirmed').append('<em style="text-decoration:none; color: #ca614a; display:inline-block; padding-left:0.5em;">Não confirmado!!</em>');
	
	$('.todo').children().children('.confirmed').append('<em style="text-decoration:none; color: #52964f; display:inline-block; padding-left:0.5em;">Confirmado.</em>');
	
	// Mail page
	$('#no-reply').click(function() {
		if($('#from').attr('disabled') == 'disabled') {
			$('#from').removeAttr('disabled');
		}
		else {
			$('#from').attr('disabled', 'disabled');
			$('#from').val('');
		}
	});
	
	var img = new Image(150,20);
	img.src = "/img/ic/ic-menu-logout-active.png";
	var img = new Image(150,20);
	img.src = "/img/ic/ic-loading.gif";
	var img = new Image(150,20);
	img.src = "/img/themes/hw/ic-menu-logout-active.png";
	var img = new Image(150,20);
	img.src = "/img/themes/hw/ic-loading.gif";
	var img = new Image(150,20);
	img.src = "/img/ic/ic-tb-arrow-down.png";
	var img = new Image(150,20);
	img.src = "/img/ic/ic-tb-arrow-right.png";



	$("div#message span#message-close-button").on("click", function()
	{
		$("div#message h2#message-title").text("");
		$("div#message h3#message-content").text("");

		$("div#message-bg").removeClass("visible")
		$("div#message").hide();
	});
});

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function setTheme(theme, date)
{
	localStorage['theme'] = theme;
	var dateUntil = new Date(date).getTime();
	var today = new Date().getTime();
	if(dateUntil-today <= 0)
	{
		localStorage['theme'] = null;
	}
}

function verifyTheme(everything) {
	if(localStorage["theme"] == null)
	{
		return;
	}
	// HALLOWEEN THEME
	else if(localStorage['theme'] == 'hw') {
		var replace = [	"logo.png",
						"ic-lock.png",
						"ic-loading.gif"];

		var folder = "themes/hw/";
		var css = "/css/halloween.css";

		for(var i=0; i<replace.length; i++) {
			imgs[i] = new Image();
			imgs[i].src = "/img/"+folder+replace[i];
		}

		if(everything == false) {
			$('link#theme-style').attr('href', css);
		}
			
		if(everything == true) {
			$("img").each(function() {
				for(var i=0; i<replace.length; i++) {
					if($(this).attr("src").indexOf(replace[i]) != -1) {
						$(this).attr("src", imgs[i].src);
					}
				}
			});

			$("div, a, input").each(function() {
				for(var i=0; i<replace.length; i++) {
					if($(this).css("background-image").indexOf(replace[i]) != -1) {
						$(this).css("background-image", "url("+imgs[i].src+")");
					}
				}
			});
		}
	}

	// CHRISTMAS THEME
	else if(localStorage['theme'] == 'xmas') {
		var replace = [	"logo.png",
						"ic-lock.png",
						"ic-loading.gif"];

		var folder = "themes/xmas/";
		var css = "/css/xmas.css";

		for(var i=0; i<replace.length; i++) {
			imgs[i] = new Image();
			imgs[i].src = "/img/"+folder+replace[i];
		}

		if(everything == false) {
			$('link#theme-style').attr('href', css);
			// Add snow
			$('link#theme-style').after("<script type='text/javascript' src='/js/snowstorm-min.js'></script>");
			snowStorm.snowColor = '#FF5157';
			snowStorm.useMeltEffect = false;
			snowStorm.useTwinkleEffect = false;
		}
			
		if(everything == true) {
			$("img").each(function() {
				for(var i=0; i<replace.length; i++) {
					if($(this).attr("src").indexOf(replace[i]) != -1) {
						$(this).attr("src", imgs[i].src);
					}
				}
			});

			$("div, a, input").each(function() {
				for(var i=0; i<replace.length; i++) {
					if($(this).css("background-image").indexOf(replace[i]) != -1) {
						$(this).css("background-image", "url("+imgs[i].src+")");
					}
				}
			});
		}
	}
}

function showMessage(title, message)
{
	$("div#message h2#message-title").html(title);
	$("div#message h3#message-content").html(message);

	
	$("div#message-bg").addClass("visible")
	$("div#message").show();
}