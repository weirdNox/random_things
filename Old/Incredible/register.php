<?php
require 'php/Membership.php';
require 'php/forms.php';
$membeship = new Membership(false, true);

// REGISTRATION
if($_POST && !empty($_POST['nick']) && !empty($_POST['password']) && !empty($_POST['fname']) && !empty($_POST['sname']) && !empty($_POST['email'])) {
	$errors = $membeship->register($_POST['nick'], $_POST['password'], $_POST['fname'], $_POST['sname'], $_POST['email']);
	
	if($errors != 'none') {
		$errors = explode(';', $errors);
		array_pop($errors);
	}
	
} else if($_POST && $_POST['submited']) {
	$errors = 'notCompleted';
}

// FORGOT PASSWORD
else if($_POST && !empty($_POST['email'])) {
	$returned = $membeship->forgotConfirm($_POST['email']);
}

else if($_GET && !empty($_GET['forgotConfirm'])) {
	$password = $membeship->forgot($_GET['forgotConfirm']);
}

?>
<!DOCTYPE html>
<html xmlns:og="http://ogp.me/ns#">
<head>
    <!--Stylesheets-->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/css/reset.css" type="text/css" />
    <link rel="stylesheet" href="/css/reset.css" id="theme-style" type="text/css" />

    <!-- Meta Info -->
    <title>Incredible Community</title>
    <meta charset="UTF-8">
    <meta name="description" content="Website que fornece resumos de todas as disciplinas escolares do 3º Ciclo.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Open Graph Protocol -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Incredible Community">
    <meta property="og:url" content="http://incredible.ihostwell.com/login.php">
    <meta property="og:image" content="http://incredible.ihostwell.com/img/logo/logo-web.png">
    <meta property="og:description" content="Website que fornece resumos de todas as disciplinas escolares do 3º Ciclo.">
    
    <!-- Javascript -->
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    <script type="text/javascript" src="/js/js.js?5"></script>
    <script>verifyTheme(false);</script>
    
    <!--Stylesheets-->
    <link rel="stylesheet" id="style" href="/css/default.css?3" type="text/css" />
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(["_setAccount", "UA-35035660-1"]);
        _gaq.push(["_trackPageview"]);
        
        (function() {
        var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
        ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>


	<noscript>
		<style type="text/css" media="screen">
			div#scriptEnabled {
				display: none;
			}

			div#noScript {
				text-align: center;
				background-color: #fde8e4;
				border: 1px solid #e6bbb3;
				color: #cf4425;
				padding: 2em;
				width: 40%;
				margin: 10em auto;
				position: relative;

				display: block;
			}

			div#no-script-info {
				position: absolute;
			    bottom: 1px;
			    right: 1px;
			    font-size: 0.7em;
			    color: gray;
			}
		</style>
	</noscript>
</head>
	
    
<body>
<!-- Top Bar -->
<div id="top-bar">
    <div class="container-padding">
        <p class="dark big white-text"><b>Bem-vindo à Incredible Community</b></p>
    </div>
</div> <!-- End top-bar -->

<div id="header">
    <div class="container-padding cf">
        <div id="login-intro" class="fl">
        <?
			if($_GET && isset($_GET['register']))
	            echo '<h1 class="text-upper">Registar</h1> <h5>Regista-te abaixo:</h5>';
			elseif($_GET && isset($_GET['forgot']))
				echo '<h1 class="text-upper">Perdi a password</h1> <h5>Coloca o teu email em baixo:</h5>';
			elseif($_GET && !empty($_GET['forgotConfirm']))
				echo '<h1 class="text-upper">Recuperação de Password</h1> <h5>A recuperação de password está a ser processada...</h5>';
			else
				header('location:/errors/404.php');
		?>
        </div> <!-- End login-intro -->
        
        <img src="img/logo/logo.png" alt="Incredible Community Logo" id="logo" class="fr" />
    </div>
</div> <!-- End header -->


<!-- Content -->
<div id="content">
    <div class="container-padding cf"><!-- Padding -->
    	<?php
		if($_GET && isset($_GET['forgot'])) {
			
			echo '	<form action="/register.php?forgot" id="login-form" method="post">';
			
			if(isset($returned) && $returned !== 0) {
				echo '<div class="round err-box">'.$returned.'</div>';
			} else if(isset($returned) && $returned === 0) {
				echo '<div class="round confirm-box">Verifique o seu email (o spam também se faz favor), pois enviámos um email com um link para confirmar que quer recuperar a password. Pode demorar um bocadinho a enviar... Pedimos desculpa pelo incómodo.</div>';
			}
			
			echo '<p>
							<label for="email">Email do Utilizador</label>
							<input type="text" id="email" name="email" class="round full-width-input" />
						</p>
						
						<a href="/login.php" class="round button green">Voltar</a>
						<input type="submit" value="Repor password" class="button round green image-right ic-arrow-right" />
					</form>';
			
			
		} else if($_GET && isset($_GET['forgotConfirm'])) {
			
			echo '	<form action="#" id="login-form" method="post">';
			
			if(isset($password) && !is_string($password)) {
				echo '<div class="round err-box">Ocorreu um erro!</div>';
			} else if(isset($password) && !empty($password)) {
				echo "<div class='round confirm-box'>Password recuperada com sucesso! A sua nova password é <b>$password</b></div>";
			} 
			
			echo '<a href="/login.php" class="round button green">Voltar</a>
					</form>';
			
			
		} else if(!isset($_POST['submited']))
			showRegisterForm();
		  else
			showRegisterForm($errors);
		?>
           
        </div> <!-- End Main Content -->
    </div><!-- End Padding -->
</div> <!-- End content -->


<!-- Footer -->
<div id="footer">
    <p class="text-upper">&copy; Copyright 2012 <strong><a href="http://www.facebook.com/pages/Incredible-Community/328267827261868" class="dark-link">Incredible Community.</a></strong></p>
    <p>Criado por <strong><a href="http://www.facebook.com/GoncaloSSantos" target="_blank" class="dark-link">Gonçalo Santos</a></strong>.</p>
</div> <!-- End Footer -->
<script>verifyTheme(true);</script>
</body>
</html>