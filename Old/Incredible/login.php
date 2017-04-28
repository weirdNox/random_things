<?php
require_once('php/Membership.php');
$membership = new Membership(false, true);

if(isset($_POST) && !empty($_POST['nick']) && !empty($_POST['password'])){
	$returned = $membership->validateCredentials($_POST['nick'], $_POST['password']);
	
	if($returned == 'couldNotValidate')
		$passed = false;
} else if(isset($_GET) && !empty($_GET['status'])) {
	$membership->logout();
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
    <!-- Messages -->
    <div id="message-bg"></div>
    <div id="message"><span id="message-close-button">×</span><h2 id="message-title"></h2><h3 id="message-content"></h3><div id="msg-logo"><img src="/img/logo/logo-mini.png" alt=""></div></div>
    <div id="top-bar">
        <div class="container-padding">
            <p class="dark big white-text"><b>Bem-vindo à Incredible Community</b></p>
        </div>
    </div> <!-- End top-bar -->
    
    <div id="header">
        <div class="container-padding cf">
            <div id="login-intro" class="fl">
                <h1 class="text-upper">Login</h1>
                <h5>Insere as tuas credenciais abaixo:</h5>
            </div> <!-- End login-intro -->
            
            <img src="img/logo/logo.png" alt="Incredible Community Logo" id="logo" style="width:415px;" class="fr" />
        </div>
    </div> <!-- End header -->
    
    <!--<h1 class="bold" style="text-align:center; margin-bottom:2em;"></h1>-->
    
    
    <div id="content">
        
        <form method="post" action="#" id="login-form">
            <fieldset>
            	<input type="hidden" name="submited" />
                <p>
                    <label for="login-username" class="bold">Nome de Utilizador</label>
                    <input type="text" id="login-username" autofocus="autofocus" class="round full-width-input" name="nick" />
                </p>
                
                <p>
                    <label for="login-password" class="bold">Password</label>
                    <input type="password" id="login-password" class="round full-width-input" name="password" />
                </p>
                
                <p class="cf" style="display:block;" id="forgotten"><a href="/register.php?forgot" target="_blank" class="dark-link fl">Esqueci-me da password.</a><a href="thankyou.php?activate" target="_blank" class="dark-link fr">Tens o código de ativação?</a></p>
                
                <input type="submit" name="login" value="Login" class="button ic-arrow-right image-right round green text-upper white-text bold fr" />
                
                <a href="/register.php?register" class="button ic-edit image-right round green text-upper white-text bold fl">Registar</a>
                
            </fieldset> <!-- End fieldset-->
            <?php
			if(isset($passed) || isset($_POST['submited']))
            	echo '<div class="round err-box">O nome de utilizador ou a password estavam mal ou ainda não ativou a sua conta. <a href="thankyou.php?activate" target="_blank">Tens o código de ativação?</a></div>';
            ?>
        </form>
        
    </div> <!-- End content -->
    
    
    
    <div id="footer">
        <p class="text-upper">&copy; Copyright 2012 <strong><a href="http://www.facebook.com/pages/Incredible-Community/328267827261868" class="dark-link">Incredible Community</a></strong>.</p>
    <p>Criado por <strong><a href="http://www.facebook.com/GoncaloSSantos" target="_blank" class="dark-link">Gonçalo Santos</a></strong>.</p>
    <a href="http://internetdefenseleague.org"><img style="height:45px;" src="http://internetdefenseleague.org/images/badges/final/shield_badge.png" alt="Member of The Internet Defense League" /></a>
    </div> <!-- End footer -->
<script>

if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) { //test for MSIE x.x;
    showMessage("Aviso", "Não recomendamos o uso do Internet Explorer na Incredible! Faça o download de um browser mais moderno <a href='https://www.google.com/intl/en/chrome/browser/'>aqui.</a>");
}

verifyTheme(true);

</script>
</body>
</html>