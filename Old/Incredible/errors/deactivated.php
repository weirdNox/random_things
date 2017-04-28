<?php
require_once '../php/Membership.php';
require_once '../php/document.php';

$membership = new Membership();
$info = $membership->verifyInfo();
showHeader($info);
?>
        
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Conta desativada</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main center">
					<p>Ok, <b>a tua conta foi desativada</b><?php  if(!empty($_SESSION['status']['2'])) { echo ' porque '.$_SESSION['status']['2']; }  ?>.</p>
                    <br /><p>O saldo de documentos ficará intacto até os administradores decidirem o que te querem fazer. Podes vir a ser contactado por eles, e se achas que a tua conta foi desativada injustamente, envia um email para <b>info@<?=HOST?></b>.</p><br />
                    <p>Mais uma vez, ficará tudo intacto, por isso não podes criar uma conta com o teu email. E se por acaso tentares criar outra conta com outro email, os administradores irão desativar também essa.</p>
                    <br /><br /><p>Obrigado pela sua atenção,</p>
                    <p><b>Os administradores</b></p>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
           
<?php
showFooter();