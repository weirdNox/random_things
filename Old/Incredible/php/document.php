<?php
require_once("SummaryManager.php");

function showHeader($info, $tab=-1) {
	echo '
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
<div id="scriptEnabled">
<!-- Messages -->
<div id="message-bg"></div>
<div id="message"><span id="message-close-button">×</span><h2 id="message-title"></h2><h3 id="message-content"></h3><div id="msg-logo"><img src="/img/logo/logo-mini.png" alt=""></div></div>
<!-- Top Bar -->
<div id="top-bar">
    <div class="container-padding cf">
    	<!-- Nav -->
        <ul id="nav" class="fl">
            <li><a class="button round image-left dark ic-menu-user">Bem-vindo '. $info[1] .'</a>
                <ul>
                    <li><a href="/settings.php?settings">Definições</a></li>
                    <li><a href="/login.php?status=loggedOut">Logout</a></li>
                </ul>
            </li>
            <li><a href="/login.php?status=loggedOut" class="button round image-left dark ic-menu-logout">Logout</a></li>
        </ul> <!-- End Nav -->
        
        <div class="fr white-text" style="margin-top: 0.3125em;">Saldo: '. number_format($info[4], 2) .'€</div>
    </div>
</div> <!-- End Top Bar -->


<!-- Tabs -->
<div id="header-with-tabs">
    <div class="container-padding cf">
        <ul id="tabs">';
            echo '<li><a href="/index.php" class="ic-tab-grid image-left"';
			if($_SERVER['REQUEST_URI']=="/index.php" || $tab==0) 
				echo ' id="active-tab"'; 
			echo '>Início</a></li>';
			
			echo '<li><a href="/summaries.php"';
			if($_SERVER['REQUEST_URI']=="/summaries.php" || $tab==1) 
				echo ' id="active-tab"'; 
			echo '>Downloads</a></li>';
       
	   		if($info[5]==1) {
				echo '<li><a href="/manage.php"';
				if($_SERVER['REQUEST_URI']=="/manage.php" || $tab==3) 
					echo ' id="active-tab"'; 
				echo '>Gestão do Site</a></li>';
				
				echo '<li><a href="/summManagement.php"';
				if($_SERVER['REQUEST_URI']=="/summManagement.php" || $tab==2) 
					echo ' id="active-tab"'; 
				echo '>Gestão de Documentos</a></li>';
			}
			
			echo '<li><a href="/about.php"';
			if($_SERVER['REQUEST_URI']=="/about.php" || $tab==4) 
				echo ' id="active-tab"'; 
			echo '>Informações</a></li>';
			
			echo '<li><a href="/feedback.php?userFeedback"';
			if($_SERVER['REQUEST_URI']=="/feedback.php?userFeedback" || $tab==5) 
				echo ' id="active-tab"'; 
			echo '>Feedback</a></li>';
	   
	   echo '</ul>
    </div>
</div> <!-- End Tabs -->


<!-- Content -->
<div id="content">
    <div class="container-padding cf"><!-- Padding -->
		<div class="fl" id="left-menus">
			<!-- Side Menu -->
			<div class="side-menu">
				<h3>Links úteis</h3>
				<ul>
					<li><a href="http://www.facebook.com/pages/Incredible-Community/328267827261868" target="_blank">Página (Facebook)</a></li>
					<li><a href="http://www.facebook.com/groups/171046736359090/" target="_blank">Grupo (Facebook)</a></li>
				</ul>
			</div> <!-- End Side Menu -->
			
			';
			$manager = new SummaryManager();
			$text = $manager->getNews();
			echo '
			<!-- Side Menu -->
			<div class="side-menu">
				<h3>Novidades</h3>
				<ul>
					'.$text.'
				</ul>
			</div> <!-- End Side Menu -->';
		echo '
			
			<!-- Side Menu -->
			<div class="side-menu">
				<h3>Publicidade</h3>
				<!-- Content -->
				<div style="background-color:white; padding: 0.6em;">
					<a href="http://internetdefenseleague.org" target="_blank" title="Envia um email para info@'.HOST.' se quiseres anunciar aqui!"><img style="margin: auto; display: block; width:96%; transition:width 0.5s;  webkit-transition:width 0.5s;" src="http://internetdefenseleague.org/images/badges/final/shield_badge.png" alt="Member of The Internet Defense League" /></a>
				</div> <!-- End Content -->
			</div> <!-- End Side Menu -->
		</div>
        
        <!-- Main Content -->
        <div id="main-content" class="fr">
';
}

function showFooter() {
	echo '
		</div> <!-- End Main Content -->
    </div><!-- End Padding -->
</div> <!-- End content -->


<!-- Footer -->
<div id="footer">
    <p class="text-upper">&copy; Copyright 2012 <strong><a href="http://www.facebook.com/pages/Incredible-Community/328267827261868" class="dark-link">Incredible Community.</a></strong></p>
    <p>Criado por <strong><a href="http://www.facebook.com/GoncaloSSantos" target="_blank" class="dark-link">Gonçalo Santos</a></strong>.</p>
</div> <!-- End Footer -->
<script>verifyTheme(true);</script>
</div>
<div id="noScript">Por favor active o javascript no seu browser.<hr class="separator">Please activate javascript in your browser.<div id="no-script-info">Para mais informações visite <a href="http://enable-javascript.com/">este site</a></div></div>
</body>
</html>';
}