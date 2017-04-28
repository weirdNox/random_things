<?php
require_once 'php/Membership.php';
require_once 'php/document.php';

$membership = new Membership();
$info = $membership->verifyInfo();
showHeader($info);
?>
        
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Título do módulo</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
					<p>Conteúdo</p>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
           
<?php
showFooter();