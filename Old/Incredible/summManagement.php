<?php
require_once 'php/Membership.php';
require_once 'php/document.php';
require_once 'php/SummaryManager.php';

$membership = new Membership();
$info = $membership->verifyInfo();
$membership->adminPage();
$manager = new SummaryManager();
$text = "";

// UPLOAD MANAGEMENT
if(isset($_FILES['file']) && $_POST && isset($_POST['name']) && !empty($_POST['name']) && isset($_POST['price']) && !isset($_POST['func']) && isset($_POST['subject']) && !empty($_POST['subject']))
{
	if(preg_match("/[a-zA-Zã-û]+\.[7-9]\.[1-3]/", $_POST['name'])) {
		$expression = true;
		
	} else {
		$expression = false;
	}

	if(isset($_POST['byOthers'])) {
		$byOthers = 1;
	} else {
		$byOthers = 0;
	}

	if(isset($_POST['class'])) {
		$classOnly = 1;
	} else {
		$classOnly = 0;
	}

	$littleDesc = $_POST['littleDesc'];
	
	// ALLOWED EXTENSIONS
	$allowedExts = array("pdf");
	$extension = explode(".", $_FILES["file"]["name"]);
	$extension = end($extension);
	
	// ALLOWED TYPES
	$allowedTypes = array("application/pdf");
	
	if(in_array($_FILES["file"]['type'], $allowedTypes)
	 //&& ($_FILES["file"]["size"] < 20000)
	 && in_array($extension, $allowedExts))
	{
		if($_FILES["file"]["error"] > 0)
		{
			$text .= "Código de erro: " . $_FILES["file"]["error"] . "<br />";
		}
		else
		{
			$text .= $manager->addSummary($_POST['name'], $_POST['price'], $expression, $extension, $_POST['subject'], $classOnly, $byOthers, $littleDesc);
		}
	}
	else
	{
		$text .= "<p>Ficheiro inválido</p>";
		$text .= "<br /><p>Tipos de permitidos:</p><ol class='small'>";
		for($i=0;$i<count($allowedExts);$i++)
		{
			$text .= "<li>.".$allowedExts[$i]."</li>";
		}
		$text .= "</ol>";
	}
}




showHeader($info, 2);

if(isset($text) && !empty($text)) {
	echo '<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Informações do Upload</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                <!-- Module Main -->
                <div class="content-module-main">';
	
	echo $text;
		
    echo '</div><!-- End Module Main -->
            </div><!-- End Module -->';
}

// DELETE SUMMARIES
if($_GET && !empty($_GET['func']) && !empty($_GET['id']) && $_GET['func']=="delete") {
	$info = $manager->getInfo($_GET['id']);
	
	echo '<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Apagar documento</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                <!-- Module Main -->
                <div class="content-module-main">';
				
	echo '<form action="/summManagement.php" method="post">
						
			<fieldset>
				<input type="hidden" name="id" id="id" value="'.$info['id'].'" />
				<input type="hidden" name="func" id="func" value="deleteSumm" />
				<label for="yes">Quer mesmo apagar o documento com a referência '.$info['name'].'?</label>
				<a href="/summManagement.php" class="round button green text-upper">Não</a>
				<input type="submit" value="Sim" class="button round green image-right ic-arrow-right" />
			</fieldset>
		
		</form>';
				
	echo '</div><!-- End Module Main -->
            </div><!-- End Module -->';
}

if($_POST && !empty($_POST['func']) && !empty($_POST['id']) && $_POST['func']=="deleteSumm") {
	$returned = $manager->delete($_POST['id']);
				
	if($returned == 0) {
		echo '<div class="round confirm-box">Documento apagado</div>';
	} else if($returned == -1) {
		echo '<div class="round err-box">O documento não pôde ser apagado. Nenhuma mudança feita.</div>';
	} else if($returned == -2) {
		echo '<div class="round err-box">Não conseguímos apagar o registo do documento na base-de-dados. Por favor, faz isso manualmente, ou contacta quem o consiga fazer.</div>';
	}
}

// EDIT SUMMARIES
if($_GET && !empty($_GET['func']) && !empty($_GET['id']) && $_GET['func']=="edit") {
	$info = $manager->getInfo($_GET['id']);
	if($info['expression']) {
		$name = explode('.', $info['name']);
		$name = ucwords($name[0].' - '.$name[1].'º Ano, '.$name[2].'º Período');
	} else {
		$name = $info['name'];
	}
	
	echo '<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Editar documento</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                <!-- Module Main -->
                <div class="content-module-main">';
				
	echo '<form action="/summManagement.php" method="post" enctype="multipart/form-data">
						
			<fieldset>
				<input type="hidden" name="id" id="id" value="'.$info['id'].'" />
				<input type="hidden" name="func" id="func" value="editSumm" />
				<label>A editar o documento '.$name.':</label>
				
				<p>
					<label for="name">Nome</label>
					<input type="text" name="name" id="name" value="'.$info['name'].'" class="round default-width-input" />
				</p>
				
				<p>
					<label for="price">Preço</label>
					<input type="text" name="price" id="price" value="'.$info['price'].'" class="round default-width-input" />
					<em style="display:none;">Este valor irá mudar devido ao desconto! O novo valor será de <span>0</span> €.</em>
				</p>

                <p>
                <label for="littleDesc">Descrição</label>
                <textarea id="littleDesc" name="littleDesc" class="round default-width-textarea" rows="6">'.$info['littleDesc'].'</textarea>
                <em>Pequena descrição do documento.</em>
                </p>
				
				<p>
				<label for="discount">Desconto</label>
				<select name="discount" id="discount">
					<option value="0"'; if($info['discount'] == "0") echo ' selected="selected"'; echo '>0%</option>
					<option value="1"'; if($info['discount'] == "25") echo ' selected="selected"'; echo '>25%</option>
					<option value="2"'; if($info['discount'] == "50") echo ' selected="selected"'; echo '>50%</option>
					<option value="3"'; if($info['discount'] == "75") echo ' selected="selected"'; echo '>75%</option>
				</select>
				<em>O desconto que o resumo terá.</em>
				</p>
				
				<p>
				<label for="subject">Disciplina</label>
				<select name="subject" id="subject">
					<option value="mat"'; if($info['subject'] == "mat") echo ' selected="selected"'; echo '>Matemática</option>
					<option value="gf"'; if($info['subject'] == "gf") echo ' selected="selected"'; echo '>Geografia</option>
					<option value="his"'; if($info['subject'] == "his") echo ' selected="selected"'; echo '>História</option>
					<option value="cfq"'; if($info['subject'] == "cfq") echo ' selected="selected"'; echo '>Físico-Química</option>
					<option value="cn"'; if($info['subject'] == "cn") echo ' selected="selected"'; echo '>Ciências Naturais</option>
					<option value="ing"'; if($info['subject'] == "ing") echo ' selected="selected"'; echo '>Inglês</option>
					<option value="fra"'; if($info['subject'] == "fra") echo ' selected="selected"'; echo '>Francês</option>
					<option value="pt"'; if($info['subject'] == "pt") echo ' selected="selected"'; echo '>Português</option>
					<option value="other"'; if($info['subject'] == "other") echo ' selected="selected"'; echo '>Outra</option>
				</select>
				<em>A disciplina do resumo.</em>
				</p>
				
				';
		if($info["classOnly"]==1)
			echo '<p><label for="class" class="alt-label">
			<input type="checkbox" id="class" name="class" checked="checked" />Só para a turma</label></p>';
			
			else echo '<p><label for="class" class="alt-label">
			<input type="checkbox" id="class" name="class" />Só para a turma</label></p>';

		if($info["byOthers"]==1)
			echo '<p><label for="byOthers" class="alt-label">
				<input type="checkbox" id="byOthers" name="byOthers" checked="checked" />Escrito por outros</label></p>';
			
			else echo '<p><label for="byOthers" class="alt-label">
				<input type="checkbox" id="byOthers" name="byOthers" />Escrito por outros</label></p>';
		echo '
				
				<p>
					<label for="change-summ" class="alt-label"><input type="checkbox" id="change-summ" name="change-summ" />Mudar o conteúdo</label>
				</p>
				
				<div id="editContents" style="display:none">
					<p>
						<label for="silent-update" class="alt-label"><input type="checkbox" id="silent-update" name="silent-update" />Silent Update (não põe o documento nas novidades)</label>
					</p>
										
					<p id="change-summ-p">
						<label for="file">Ficheiro</label>
						<input type="file" name="file" id="file" /> 
					</p>
				</div>
				
				<a href="/summManagement.php" class="round button green text-upper">Cancelar</a>
				<input type="submit" value="Editar" class="button round green image-right ic-arrow-right" />
			</fieldset>
		
		</form><script type="text/javascript">
				var percentage = $("#discount").val();
				var percentages = new Array(0, 25, 50, 75);
				percentage = (100-percentages[percentage])/100;
				$("input#price").next("em").children("span").text($("input#price").val()*percentage);
					
				$("#change-summ").click(function(){
				  $("#editContents").stop(true).slideToggle();
				});
				
				$("#editContents:checked").removeAttr("checked");
				
				function verifyDiscount() {
					if($("#discount option:selected").attr("value") != "0") {
						$("input#price").parent().addClass("form-warn");
						var percentage = $("#discount").val();
						var percentages = new Array(0, 25, 50, 75);
						percentage = (100-percentages[percentage])/100;
						$("input#price").next("em").children("span").text($("input#price").val()*percentage);
						$("input#price").next("em").slideDown();
					} else {
						$("input#price").next("em").slideUp(function() {
							$("input#price").parent().removeClass("form-warn");
						});
					}
				}
				
				verifyDiscount();
				$("#discount").change(verifyDiscount);
				
				$("#price").keyup(function() {
					var percentage = $("#discount").val();
					var percentages = new Array(0, 25, 50, 75);
					percentage = (100-percentages[percentage])/100;
					$("input#price").next("em").children("span").text($("input#price").val()*percentage);
				});
		</script>';
				
	echo '</div><!-- End Module Main -->
            </div><!-- End Module -->';
}

if($_POST && !empty($_POST['func']) && !empty($_POST['id']) && $_POST['func']=="editSumm" && !empty($_POST['name']) && isset($_POST['price']) && isset($_POST['subject']) && !empty($_POST['subject']) && isset($_POST['discount'])) {
	if(isset($_POST['change-summ']) && !empty($_FILES['file']['tmp_name'])) {
		$editedContents = true;
	} else {
		$editedContents = false;
	}

	if(isset($_POST['class'])) {
		$classOnly = 1;
	} else {
		$classOnly = 0;
	}

	if(isset($_POST['byOthers'])) {
		$byOthers = 1;
	} else {
		$byOthers = 0;
	}

	$littleDesc = $_POST['littleDesc'];
	
	if(isset($_POST['silent-update'])) {
		$silent = true;
	} else {
		$silent = false;
	}
	$returned = $manager->edit($_POST['id'], $_POST['name'], $_POST['price'], $editedContents, $_POST['subject'], $_POST['discount'], $classOnly, $littleDesc, $byOthers, $silent);
				
	if($returned == 0) {
		echo '<div class="round confirm-box">Documento editado!</div>';
	} else {
		echo '<div class="round err-box">Erro ao editar o resumo... (Erro: '.$returned.')</div>';
	}
}
?>
        	<!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Upload</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                <!-- Module Main -->
                <div class="content-module-main">
					<form action="/summManagement.php" method="post" enctype="multipart/form-data">
                        <p>
                        <label for="file">Ficheiro</label>
                        <input type="file" name="file" id="file" /> 
                        </p>
                        
                        <p>
                        <label for="name">Referência</label>
                        <input type="text" name="name" id="name"  class="round default-width-input" /> 
                        <em>A referência pode ser de dois tipos:<br/>- Tipo padrão que é no formato Disciplina.Ano.Periodo e serve para resumos (ex: Ciências.8.3)<br/>- Tipo livre (ex: Exercícios de Francês - 1)</em>
                        </p>
                        
                        <p>
                        <label for="price">Preço</label>
                        <input type="text" name="price" id="price"  class="round default-width-input" /> 
                        <em>Se quiseres pôr de graça põe igual a 0.</em>
                        </p>

                        <p>
                        <label for="littleDesc">Descrição</label>
                        <textarea id="littleDesc" name="littleDesc" class="round default-width-textarea" rows="6"></textarea>
                        <em>Pequena descrição do documento.</em>
                        </p>
                        
                        <p>
                        <label for="subject">Disciplina</label>
                        <select name="subject" id="subject">
                            <option value="mat">Matemática</option>
                            <option value="gf">Geografia</option>
                            <option value="his">História</option>
                            <option value="cfq">Físico-Química</option>
                            <option value="cn">Ciências Naturais</option>
                            <option value="ing">Inglês</option>
                            <option value="fra">Francês</option>
                            <option value="pt">Português</option>
                            <option value="other">Outra</option>
                        </select>
                        <em>A disciplina do resumo.</em>
                        </p>

                        <p><label for="class" class="alt-label">
						<input type="checkbox" id="class" name="class" />Só para a turma</label></p>

						<p><label for="byOthers" class="alt-label">
						<input type="checkbox" id="byOthers" name="byOthers" />Escrito por outros</label></p>
                        
                        <input type="submit" value="Upload" class="button round green image-right ic-arrow-right" />
                        <?php 
						if(isset($error)) {
							echo '<em style="padding-left: 5px;">'.$error.'</em>';
						}
						?>
                    </form>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
            
            <!-- Module -->
            <div class="content-module" id="confirm-module" style="display:none">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Confirmar desconto global</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                <!-- Module Main -->
                <div class="content-module-main">
                	<div id="confirm">
                        <p><b style="display:block;margin-bottom:0.5em;" id="confirm-text"></b></p>
                        <input type="hidden" value="global" id="discount-type" />
                        <a class="round button green text-upper" id="confirm-cancel">Cancelar</a>
                        <a class="button round green image-right ic-arrow-right text-upper" id="confirm-confirm" style="margin-left:0.3em;">Sim</a>
                 	</div>
                    <div id="after">
                        
                 	</div>
                </div><!-- End Module Main -->
            </div><!-- End Module -->
            
            <!-- Module -->
            <div class="content-module">
				<!-- Module Heading -->
                <div class="content-module-heading cf">
                    <h3 class="fl">Documentos no site</h3>
                    <span class="fr span-text">Clica para contrair</span>
                    <span class="fr expand-span span-text">Clica para expandir</span>
                </div><!-- End Module Heading -->
                
                <!-- Module Main -->
                <div class="content-module-main">
                <div class="cf">
                <?php
					$downloads = $manager->getAllDownloads();
					echo "<div class='round info-box fl' style='width:55%; margin-top:0;'>Já foram feitas $downloads compras no total.</div>";
				?>
                <div class="fr" style="max-width:40%;">
               		<div style="display:inline-block;margin-right:1em">
                        <p><label for="global-discount">Aplicar desconto global:</label></p>
                        <select name="global-discount" id="global-discount" style="width:100%">
                            <option value="-1" selected="selected">Escolher...</option>
                            <option value="0">0%</option>
                            <option value="1">25%</option>
                            <option value="2">50%</option>
                            <option value="3">75%</option>
                        </select>
                    </div>
               		<div style="display:inline-block">
                        <p><label for="subjectSelect">Filtrar por disciplina:</label></p>
                        <select name="subjectSelect" id="subjectSelect">
                            <option value="all" selected="selected">Todos</option>
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
               	</div>
					<?php
						echo $manager->getSummaries();
					?>
					<div>
                        <p><label for="selected-discount">Aplicar desconto aos documentos selecionados:</label></p>
                        <select name="selected-discount" id="selected-discount" style="width:100%">
                            <option value="-1" selected="selected">Escolher...</option>
                            <option value="0">0%</option>
                            <option value="1">25%</option>
                            <option value="2">50%</option>
                            <option value="3">75%</option>
                        </select>
                    </div>
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
					$('table#summaries > tbody > tr').fadeIn(350);
				} 
				
				else {
					$('table#summaries > tbody >  tr.' + subject + ':even:not([class~="downloaded"])').css('background-color', 'white');
					$('table#summaries > tbody >  tr.' + subject + ':odd:not([class~="downloaded"])').css('background-color', '#f8f9fa');
					$('table#summaries > tbody >  tr.' + subject).fadeIn(350);
					$('table#summaries > tbody >  tr:not([class~="'+subject+'"])').fadeOut(350);
				}
				
				
			});
			
			$('#global-discount').change(function() {
				var percentage = $('#global-discount').val();
				var percentages = new Array(0, 25, 50, 75);
				
				if(percentage!="-1" && percentage!="0") {
					$("#confirm-module b").text("Tens mesmo a certeza que queres aplicar um desconto global de "+percentages[percentage]+"%?");
					$("#confirm").show();
					$("#after").hide();
					$("#discount-type").val("global");
					$("#confirm-module").slideDown();
				} else if(percentage=="0") {
					$("#confirm-module b").text("Tens mesmo a certeza que queres remover todos os descontos?");
					$("#confirm").show();
					$("#after").hide();
					$("#discount-type").val("global");
					$("#confirm-module").slideDown();
				} else {
					$("#confirm-module").slideUp();
				}
			});

			$('#selected-discount').change(function() {
				var percentage = $('#selected-discount').val();
				var percentages = new Array(0, 25, 50, 75);
				
				if(percentage!="-1" && percentage!="0") {
					$("#confirm-module b").text("Tens mesmo a certeza que queres aplicar um desconto aos documentos selecionados de "+percentages[percentage]+"%?");
					$("#confirm").show();
					$("#after").hide();
					$("#discount-type").val("selected");
					$("#confirm-module").slideDown();
				} else if(percentage=="0") {
					$("#confirm-module b").text("Tens mesmo a certeza que queres remover todos os descontos?");
					$("#confirm").show();
					$("#after").hide();
					$("#discount-type").val("selected");
					$("#confirm-module").slideDown();
				} else {
					$("#confirm-module").slideUp();
				}
			});
			
			$('#confirm-confirm').click(function(e) {
				e.preventDefault();
				var type = $("#discount-type").val();
				
				if(type=="global") {
					var percentage = $('#global-discount').val();
					var percentages = new Array(0, 25, 50, 75);
					percentage = percentages[percentage];
					$.ajax({
						type: "POST",
						url: "php/ajax.php",
						data: {per: percentage},
						success: function(data) {
							if(!data.success) {
								$("#after").html("<div class='round err-box'>Ocorreu um erro!</div>");
							} else {
								$("#after").html("<div class='round confirm-box'>Desconto global aplicado!</div>");
							}
							
							$("#confirm").slideUp();
							$("#after").slideDown();
						}});
				} else if(type=="selected") {
					var percentage = $('#selected-discount').val();
					var percentages = new Array(0, 25, 50, 75);
					percentage = percentages[percentage];
					var summIds = [];
					$("input.summChecker:checked").each(function() {
						summIds.push($(this).val());
					});

					$.ajax({
						type: "POST",
						url: "php/ajax.php",
						data: {func: "discountSomeSumm", percentage: percentage, summIds: summIds},
						success: function(data) {
							if(!data.success) {
								$("#after").html("<div class='round err-box'>Ocorreu um erro!</div>");
							} else {
								$("#after").html("<div class='round confirm-box'>Desconto aplicado!</div>");
							}
							
							$("#confirm").slideUp();
							$("#after").slideDown();
						}
					});
				}
			});
			
			$('#confirm-cancel').click(function(e) {
				$("#confirm-module").slideUp();
				$('#global-discount').val("-1");
			});
            </script>
           
<?php
showFooter();