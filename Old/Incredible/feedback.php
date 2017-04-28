<?php
require_once 'php/Membership.php';
require_once 'php/document.php';
require_once 'php/functions.php';

$membership = new Membership();
$info = $membership->verifyInfo();
showHeader($info, 5);

if($_POST && !empty($_POST["message"])) {
	$to = "info@".HOST;
	$from = "user-feedback";
	$message = "";
	
	// NÃO OBRIGATÓRIO
	if(isset($_POST["anonymous"])) {
		$anon = true;
	}
		
	$message .= "+---------------------------\n"
	. "| <b>Nome de utilizador</b>: " . $info[0]. "\n"
	. "| <b>Nome</b>: " . $info[1]. " " . $info[2] . "\n"
	. "| <b>Email</b>: " . $info[3] . "\n"
	. "+---------------------------\n\n";
	$message .= $_POST["message"];
	$mailed = sendMail($to, $message, "Feedback de Utilizadores", $from, false, true);
	
}

if($_GET && isset($_GET['adversiteHere']) && $info[5]) {
?>
        
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Como fazer publicidade na Incredible</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
					<div class="round info-box">A sua publicidade deve cumprir as seguintes regras:
                    <ul class="marged li-marged">
                    	<li>A publicidade tem de ser uma imagem não animada, por isso não pode ser uma animação flash nem uma imagem .gif. Terá de ser, por isso, uma imagem .jpeg, .png, etc...</li>
                        <li>O seu tamanho tem de estar entre 185x50 pixeis a 185x200 pixeis.</li>
                        <li>A publicidade poderá ser sobre qualquer tema desde que não contenha calão.</li>
                    </ul><br />
                    O email que nos enviar deverá conter:
                    <ul class="marged li-marged">
                    	<li>A imagem da publicidade anexada</li>
                        <li>Um método de pagamento</li>
                        <li>O link que abre quando se carrega na imagem (opcional)</li>
                    </ul>
                    
                    <br />
                    Enviar email para <b>info@<?=HOST?></b>
                    </div>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
           
<?php
}

else if($_GET && isset($_GET['userFeedback'])) {
?>
        
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Feedback do utilizador</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
					<div class="round info-box"><b>Este feedback pode ter diferentes finalidades:</b><ul class="marged">
                    <li><b>Ajuda</b> sobre alguma funcionalidade do site</li>
                    <li>Falar sobre a <b>qualidade dos resumos</b> aqui presentes</li>
                    <li><b>Dar ideias</b> para novas funcionalidades da Incredible</li>
                    <li><b>Comunicar</b> um <b>erro</b></li>
                    <li>Comunicar sobre um <b>resumo corrompido ou com um título que não seja o real</b></li>
                    <li>Etc...</li>
                    </ul></div>
                    
                    <form method="post" action="feedback.php?userFeedback" name="email">
                        <fieldset>
                            <p>
                                <label for="message">O seu feedback</label>
                                <textarea id="message" name="message" class="round full-width-textarea" rows="10"></textarea>
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
                    <p style="margin-top:1em;" id="feddbackStatus"></p>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
           
<?php
}

else {
	header('location: index.php');
}
showFooter();