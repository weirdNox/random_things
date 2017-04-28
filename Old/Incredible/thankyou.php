<?php
require 'php/Membership.php';
$membership = new Membership(false);

if($_GET && isset($_GET['activcode']))
	$errors = $membership->activate($_GET['activcode']);
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
            <h1 class="text-upper">Obrigado</h1>
            <h5>por se registar!</h5>
        </div> <!-- End login-intro -->
        
        <img src="img/logo/logo.png" alt="Incredible Community Logo" id="logo" class="fr" />
    </div>
</div> <!-- End header -->


<!-- Content -->
<div id="content">
    <div class="container-padding cf" style="width:53em; margin:0 auto;"><!-- Padding -->
    	<?php
		if($_GET && isset($_GET['registed']))
			echo '<p class="center">Obrigado por se registar na Incredible Community. Pode começar a utilizar a sua conta quando colar o <b>código de ativação que enviámos para o seu email na caixa abaixo</b>. Se não encontrar o nosso email, espere um pouco (um bocado grande....). Se não conseguir receber o email em 5 minutos, por favor contacte-nos em info@'.HOST.'<br><b>A equipa<br><br>Atenção:<br>Este email irá conter as suas credenciais, por isso não o mostre a ninguém. Nunca revele a sua password!</b></p><br /><form id="login-form" action="/thankyou.php" method="get"><p><label for="activcode">Código que recebeste no email</label><input type="text" id="activcode" name="activcode" class="round default-width-input" /><em>Este código está no email que foi enviado para ti mal registaste a conta</em></p><input type="submit" value="Ativar" class="button round green image-right ic-arrow-right" /></form>';
			
		else if($_GET && isset($_GET['activate']))
			echo '<form id="login-form" action="/thankyou.php" method="get"><p><label for="activcode">Código que recebeste no email</label><input type="text" id="activcode" name="activcode" class="round default-width-input" /><em>Este código está no email que foi enviado para ti mal registaste a conta</em></p><input type="submit" value="Ativar" class="button round green image-right ic-arrow-right" /></form>';
		
		else if(isset($errors) && $errors == '0')
			echo '<p class="center">Pode agora aceder à sua conta. Clique <a href="login.php">aqui</a> para ir para a página inicial.</p>';
		
		else if(isset($errors) && $errors == '-1')
			echo '<p class="center">A sua conta já tinha sido ativada. Clique <a href="login.php">aqui</a> para ir para a página inicial.</p>';
		
		else if(isset($errors)) {
			echo '<div class="round err-box">' . $errors . '</div><br /><form id="login-form" action="/thankyou.php" method="get"><p><label for="activcode">Código que recebeste no email</label><input type="text" id="activcode" name="activcode" class="round default-width-input" /><em>Este código está no email que foi enviado para ti mal registaste a conta</em></p><input type="submit" value="Ativar" class="button round green image-right ic-arrow-right" /></form>';
		}
			
		else
			header('location:/errors/404.php');
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