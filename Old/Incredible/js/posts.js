function loadNewPosts() {
	var lastId = $('div#posts').children(':first').attr('id');
	if(isNaN(lastId)) {
		lastId = 1;
	}
	
	$.post('php/ajax.php', {lastPostId: lastId}, function(returned) {
		if(returned != 'NULL') {
			var data = new String(returned);
			data = data.split('|--SPLIT--|');
			
			$('div#posts').prepend(data[0]);
			
			$('div.post:lt('+data[1]+')').hide();
			$('div.post:lt('+data[1]+')').fadeIn(2000);
		}
	});
}

function loadLastPosts() {
	$.post('php/ajax.php', {loadLastPosts: 'yes'}, function(returned) {
		if(returned != 'NULL') {
			$('div#posts').prepend(returned);
		}
	});
}

$('input#postSubmit').on('click', function () {
	var content = $('textarea#postContent').val();
	$('textarea#postContent').val("");
	loadNewPosts();
	
	if($.trim(content) != "") {
		$.post('php/ajax.php', {content: content}, function(returned) {
			var data = new String(returned);
			data = data.split('/-split-/');
			
			if(data.length < 3) {
				$('div#postError').html('<div class="err-box round">Ocorreu um error ao escrever a atualização!</div>'); 
				$('textarea#postContent').val(content);
				return;
			}
			
			$('div#posts').prepend('<div class="post" id="'+data[2]+'"><h2>'+data[1]+' <span class="date">'+data[0]+'</span></h2><div class="content">'+content+'</div></div>');
			
			$('div#posts').children("div.post#"+data[2]).hide(1);
			$('div#posts').children("div.post#"+data[2]).fadeIn(2000);
		});
		
	} else {
		alert("Por favor, escreva o conteúdo da atualização!");
	}
});

$('#posts').delegate('input#morePosts', 'click', function () {
	$('input#morePosts').attr('id', 'morePostsClicked');
	$('input#morePostsClicked').removeClass('ic-arrow-right');
	$('input#morePostsClicked').removeClass('image-right');
	$('input#morePostsClicked').removeClass('green');
	$('input#morePostsClicked').addClass('ic-loading');
	$('input#morePostsClicked').addClass('image-center');
	$('input#morePostsClicked').addClass('greenNH');
	$('input#morePostsClicked').val('');
	
	var firstId = $('div#posts').children('.post:last').attr('id');
	if(isNaN(firstId)) {
		$('input#morePosts').remove();
		return;
	}
	
	
	$.post('php/ajax.php', {firstId: firstId}, function(returned) {
		if(returned != 'NULL') {
			var data = new String(returned);
			data = data.split('|--SPLIT--|');
			
			$('div#posts').children('.post:last').after(data[0]);
			
			//$('div.post:gt('+data[1]+')').hide();
			//$('div.post:gt('+data[1]+')').fadeIn(2000);
		} else {
			$('div#posts').children('.post:last').after("<div id='noMorePosts'>Não há mais atualizações para mostrar!</div>");
			$('input#morePosts').remove();
		}
		$('input#morePostsClicked').remove();
	});
	
});


$(document).ready(function(e) {
	loadLastPosts();
	setInterval(function (thisObj) { this.loadNewPosts(); }, 60000, this);
});