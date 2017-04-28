<?php
require_once 'php/Membership.php';
require_once 'php/document.php';

$membership = new Membership();
$info = $membership->verifyInfo();

if($_POST && $_POST['func'] && $_POST['func'] == "updateMoney" && $_POST['VCode']) {
	$returned = $membership->useVoucher($_POST['VCode']);
	
	header('location: /index.php?VCode='.$returned);
}

else if($_GET && isset($_GET['unsubEmail'])) {
	$unsub = $membership->unsubEmail();
}

else if(!$_GET || !isset($_GET['settings'])) {
	header('location: /errors/404.php');
}

$password = -999;
$email = -999;

if($_POST && isset($_POST['old-password']) && isset($_POST['new-password']) && isset($_POST['password-confirm']) && !empty($_POST['old-password']) && !empty($_POST['new-password']) && !empty($_POST['password-confirm']))
{
	$password = $membership->update("password", $_POST['old-password'], $_POST['new-password'], $_POST['password-confirm']);
} 

else if($_POST && isset($_POST['new-email']) && !empty($_POST['new-email'])) {
	$email = $membership->update("email", $_POST['new-email']);
}

showHeader($info);
if($password == 1) {
	echo '<div class="confirm-box round">Password atualizada!</div>';
} else if($password == 0) {
	echo '<div class="err-box round">Algo correu mal!</div>';
} else if($password == -1) {
	echo '<div class="err-box round">Password muito pequena! (Mínimo 6 carácteres)</div>';
}  else if($password == -2) {
	echo '<div class="err-box round">Password muito grande! (Máximo 36 carácteres)</div>';
}

if($email == 1) {
	echo '<div class="confirm-box round">Email atualizado!</div>';
} else if($email == -1) {
	echo '<div class="err-box round">Algo correu mal!</div>';
}

if(isset($unsub) && $unsub === 0) {
	echo '<div class="confirm-box round">Já não estás mais subscrito aos emails de atualizações.</div>';
} else if(isset($unsub) && $unsub === -1) {
	echo '<div class="err-box round">Algo correu mal!</div>';
}
?>
	<div class="cf">
        <!-- Half-Size -->
        <div class="half-size-column fl">
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Mudar a password</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
					<form action="" method="post">
                        <fieldset>
                        	<input type="hidden" id="password" name="password" value="sent">
                            <p>
                                <label for="old-password">Password antiga</label>
                                <input type="password" id="old-password" name="old-password" class="round default-width-input" />
                            </p>
                            
                            <p>
                                <label for="new-password">Password nova</label>
                                <input type="password" id="new-password" name="new-password" class="round default-width-input"/>
                                <em>Lembra-te que a tua password deve conter símbolos.</em>								
                            </p>
    
                            <p>
                                <label for="password-confirm">Confirmar password</label>
                                <input type="password" id="password-confirm" name="password-confirm" class="round default-width-input"/>
                                <em>Nunca contes a tua password a ninguém!</em>								
                            </p>
                            
                            <input type="submit" value="Feito" class="button round green image-right ic-arrow-right" />
                            
                        </fieldset>
                    
                    </form>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
		</div><!-- End Half-Size -->
        
		<!-- Half-Size -->
        <div class="half-size-column fr">
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Mudar o email</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
					<form action="" method="post">                        
                        <fieldset>
                        	<?php /*<input type="hidden" id="email" name="email" value="sent">
                            <p>
                                <label for="new-email">Email novo</label>
                                <input type="text" id="new-email" name="new-email" class="round default-width-input"/>						
                            </p>
                            
                            <input type="submit" value="Feito" class="button round green image-right ic-arrow-right" />
                            */?>
                            Brevemente...
                        </fieldset>
                    
                    </form>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
		</div><!-- End Half-Size -->
	</div>
        
		<?php
		if($info[9] == 0) {
			echo '<div id="subscribe-warn" class="warn-box round">Não estás subscrito da lista de emails! Se continuares assim, não receberás emails a avisar sobre promoções, revisão dos resumos que compraste e novidades. Clica <b><a id="subscribe" href="sub">aqui</a></b> para te voltares a subscrever!</div><script>
			
			$("#subscribe").click(function(e) {
				e.preventDefault();
				$.ajax({
					type: "POST",
					url: "php/ajax.php",
					data: {subscribe: true},
					success: function(data) {
						if(!data.success) {
							$("#subscribe-warn").after("<div class=\'round err-box\'>Ocorreu um erro!</div>").next().hide().slideDown().prev().slideUp();
						} else {
							$("#subscribe-warn").after("<div class=\'round confirm-box\'>Estás outra vez subscrito!</div>").next().hide().slideDown().prev().slideUp();
						}
					}
				});
			});
				
			
			</script>';
		} else {
			echo '<div id="subscribe-warn" class="info-box round">Se quiseres deixar de receber os emails que a avisar sobre promoções, revisão dos resumos que compraste e novidades, clica <b><a id="unsubscribe" href="unsub">aqui</a></b></div><script>
			
			$("#unsubscribe").click(function(e) {
				e.preventDefault();
				$.ajax({
					type: "POST",
					url: "php/ajax.php",
					data: {unsubscribe: true},
					success: function(data) {
						if(!data.success) {
							$("#subscribe-warn").after("<div class=\'round err-box\'>Ocorreu um erro!</div>").next().hide().slideDown().prev().slideUp();
						} else {
							$("#subscribe-warn").after("<div class=\'round confirm-box\'>Já não estás mais subscrito dos emails.</div>").next().hide().slideDown().prev().slideUp();
						}
					}
				});
			});
				
			
			</script>';
		}
		?>
           
<?php
showFooter();