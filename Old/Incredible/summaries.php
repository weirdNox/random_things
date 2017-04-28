<?php
require_once 'php/Membership.php';
require_once 'php/document.php';
require_once 'php/SummaryManager.php';
require_once 'php/constants.php';

$membership = new Membership();
$userInfo = $membership->verifyInfo();
$manager = new SummaryManager();

// DOWNLOAD SUMMARIES 
if($_GET && isset($_GET['downloadLast'])) {
	$bool = true;
	while($bool) {
		$location = HOME . "downloads/".rand().rand().".pdf";
		if(!file_exists($location))
			$bool = false;
	}
	$manager->downloadLast($location);
	unlink($location);
}
else if($_POST && !empty($_POST['func']) && !empty($_POST['id']) && $_POST['func']=="downloadSumm") {
	$returned = $manager->downloadStarter($_POST['id']);

	if($returned == 0) {
		header('location:/summaries.php?downloaded=1');
	} else {
		header('location:/summaries.php?downloaded='.$returned);
	}
	
} else if($_GET && isset($_GET['downloaded']) && !empty($_GET['downloaded'])) {
	
	showHeader($userInfo,1);
	echo '<!-- Module -->
		<div class="content-module">
			<!-- Module Heading -->
			<div class="content-module-heading cf">
				<h3 class="fl">Fazer o download de um documento</h3>
				<span class="fr span-text">Clica para contrair</span>
				<span class="fr expand-span span-text">Clica para expandir</span>
			</div><!-- End Module Heading -->
			<!-- Module Main -->
			<div class="content-module-main">';
			
	$errorCode = $_GET['downloaded'];
	if($errorCode == '1') {
		echo '<p>Carregue <a href="/summaries.php?downloadLast"><b>aqui</b></a> para começar o download.</p>';
		echo '<div class="warn-box round">Se não conseguir receber o documento ou se ele estiver corrompido, tente outra vez, e se mesmo assim não conseguir, por favor, <b>NÃO FAÇA</b> mais nenhum download e envie um email com o seu problema para: <b>info@'.HOST.'</b>.<br /><br /><b>Ao fazer o download do documento, está a aceitar os <a href="/about.php#TOS">Termos e Condições de Serviço</a></b></div>';
		
	} 
	
	else {
		echo '<div class="err-box round">Não conseguimos fazer o download desse documento. Por favor tente outra vez e verifique se tem saldo disponível. Se tiver saldo e não conseguir fazer o download, por favor avise a equipa (email: info@'.HOST.')<br /><br /><b>';
		
		if($errorCode == -2) {
			echo 'Problema: Não tem saldo para o documento!';
		} else if($errorCode == -1) {
			echo 'Problema: O documento não existe... por favor avise a equipa. (Não foi debitado nenhum documento ao seu saldo)';
		} else {
			echo 'Código do erro: '.$errorCode;
		}
		echo '</b></div>';
	}
				
	echo '</div><!-- End Module Main -->
			</div><!-- End Module -->';
	
} 
else if($_GET && !empty($_GET['func']) && !empty($_GET['id']) && $_GET['func']=="download") {
	// VERIFY IF BOUGHT
	$returnCode = $manager->verifyBought($_GET['id']);
	if($returnCode == 1) {
		$manager->setLastToDownload($_GET['id']);
		header("location:/summaries.php?downloaded=1");
	} else if($returnCode != -1) {
		echo '<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Comprar um documento</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                <!-- Module Main -->
                <div class="content-module-main">
				<div class="roun err-box">Ocorreu um erro! (Error code: '.$returnCode.')</div>
				</div><!-- End Module Main -->
            </div><!-- End Module -->';
	}
	$info = $manager->getInfo($_GET['id']);
	if($info['expression']) {
		$name = explode('.', $info['name']);
		$name = $name[0].' - '.$name[1].'º Ano, '.$name[2].'º Período';
	} else {
		$name = $info['name'];
	}
	showHeader($userInfo,1);
	
	echo '<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Comprar um documento</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                <!-- Module Main -->
                <div class="content-module-main">';
				
	if(is_array($info)) {
		echo '<form action="summaries.php" method="post">
							
				<fieldset>
					<input type="hidden" name="id" id="id" value="'.$info['id'].'" />
					<input type="hidden" name="func" id="func" value="downloadSumm" />
					<label for="yes">Quer mesmo comprar '.$name.'? <p>Custa '.number_format($info['price'],2).'€</p></label>
					<a href="/summaries.php" class="round button green text-upper">Não</a>
					<input type="submit" value="Sim" class="button round green image-right ic-arrow-right" />
				</fieldset>
			
			</form>';
	} else {
		echo '<div class="roun err-box">Ocorreu um erro!</div>';
	}
				
	echo '</div><!-- End Module Main -->
            </div><!-- End Module -->';
}else
	showHeader($userInfo,1);
?>
        
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Documentos disponíveis</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
                <div class="cf">
                <?php
					$downloads = $manager->getAllDownloads();
					echo "<div class='round info-box fl' style='width:75%; margin-top:0;'>Já foram feitas $downloads compras no total.</div>";
				?>
                <div class="fr">
                	<p><label for="subjectSelect">Filtrar por disciplina:</label></p>
                	<select name="subjectSelect" id="subjectSelect">
                   		<option value="all">Todos</option>
                        <option value="mat">Matemática</option>
                        <option value="gf">Geografia</option>
                        <option value="his">História</option>
                        <option value="cfq">Físico-Química</option>
                        <option value="cn">Ciências Naturais</option>
                        <option value="ing">Inglês</option>
                        <option value="fra">Francês</option>
                        <option value="pt">Português</option>
                        <option value="other">Outras</option>
                    </select>
             	</div>
               	</div>
					<?php
						echo $manager->getSummaries();
					?>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
            
            <script>
            $("table#summaries td div.arrow").on("click", function(e) {
            	var $this = $(this);
            	if($this.css("background-image").indexOf("/img/ic/ic-tb-arrow-right.png") !== -1)
            		$this.css("background-image", "url(/img/ic/ic-tb-arrow-down.png)");
            	else
            		$this.css("background-image", "url(/img/ic/ic-tb-arrow-right.png)");

            	$this.nextAll("div.desc").stop().slideToggle();
            });

            $('#subjectSelect').change(function() {
				var subject = $('#subjectSelect').val();
				if(subject == 'all') {
					$('table#summaries > tbody >  tr:even:not([class~="downloaded"])').css('background-color', 'white');
					$('table#summaries > tbody >  tr:odd:not([class~="downloaded"])').css('background-color', '#f8f9fa');
					$('table#summaries > tbody >  tr.t-green').css('background-color', '');
					$('table#summaries > tbody > tr').fadeIn(200);
				} 
				
				else {
					$('table#summaries > tbody >  tr.' + subject + ':even:not([class~="downloaded"])').css('background-color', 'white');
					$('table#summaries > tbody >  tr.' + subject + ':odd:not([class~="downloaded"])').css('background-color', '#f8f9fa');
					$('table#summaries > tbody >  tr.t-green').css('background-color', '');
					$('table#summaries > tbody >  tr.t-yellow').css('background-color', '');
					$('table#summaries > tbody >  tr.' + subject).fadeIn(200);
					$('table#summaries > tbody >  tr:not([class~="'+subject+'"])').fadeOut(350);
				}
				
				
			});
            </script>
           
<?php
showFooter();