<?php
require_once 'php/Membership.php';
require_once 'php/functions.php';
require_once 'php/document.php';

$membership = new Membership();
$info = $membership->verifyInfo();
$membership->adminPage();

if($_POST && !empty($_POST["to"]) && !empty($_POST["message"]) && isset($_POST['subject'])) {
	// OBRIGATÓRIO
	$to = $_POST["to"];
	
	// OBRIGATÓRIO
	$message = $_POST["message"];
	
	// NÃO OBRIGATÓRIO
	if(isset($_POST["no-reply"])) {
		$noReply = true;
		$from = '';
	} else if(isset($_POST["from"]) && !empty($_POST["from"])) {
		$noReply = false;
		$from  = $_POST["from"];
	} else {
		$mailed = false;
	}
	
	// NÃO OBRIGATÓRIO
	$subject = $_POST['subject'];

	if(!isset($mailed)) {
		$mailed = sendMail($to, $message, $subject, $from, $noReply, false, false);
	}
	
}
showHeader($info);
?>        
        	<!-- Module -->
            <div class="content-module">
                <!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Enviar email</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
                    <form method="post" action="mail.php" name="email-me">
                        <fieldset>
                            <p>
                                <label for="from">De</label>
                                <input type="text" id="from" name="from" class="round default-width-input"/>
                                <label for="no-reply" class="alt-label" style="display:inline-block; padding-left:1em;"><input type="checkbox" id="no-reply" name="no-reply" />No-reply?</label>
                                <em>Escreve aqui o que substitui os pontos de interrogação: ?????@<?=HOST?></em>
                            </p>
                            
                            <p>
                                <label for="to">Para</label>
                                <input type="text" id="to" name="to" class="round full-width-input" <?php if($_GET && isset($_GET['to'])) echo 'value="'. $_GET['to'] .'"' ?>/>
                            </p>
                            
                            <p>
                                <label for="subject">Assunto</label>
                                <input type="text" id="subject" name="subject" class="round full-width-input"/>
                            </p>
                            
                            <p>
                                <label for="message">Mensagem</label>
                                <textarea id="message" name="message" class="round full-width-textarea" rows="10"></textarea>
                                <em>Podes redimensionar-me!</em>
                            </p>
                            
                            <input type="submit" class="button round green image-right ic-arrow-right" value="Enviar"/>
                            <?php 
                            if(isset($mailed)) {
                                if($mailed == true) 
                                    echo '<em style="padding-left: 5px;">Mail enviado!</em>'; 
                                else
                                    echo '<em style="padding-left: 5px;">Não conseguimos enviar o mail!</em>';
                            }
                            ?>
                        </fieldset>
                    </form>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
        
<?php
showFooter();