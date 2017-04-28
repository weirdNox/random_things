<?php
require_once 'php/Membership.php';
require_once 'php/document.php';

$membership = new Membership();
$info = $membership->verifyInfo();
$membership->adminPage();

if($info[5] && $_GET && isset($_GET['deleted']) && $_GET['deleted'] == "1") {
	showHeader($info, 3);
	echo '<div class="confirm-box round">Utilizador apagado!</div>';
} else if($info[5] && $_GET && isset($_GET['deleted']) && $_GET['deleted'] == "-1") {
	showHeader($info, 3);
	echo '<div class="err-box round">O utilizador não pôde ser apagado!</div>';
}

else if($info[5] && $_GET && isset($_GET['deletedV']) && $_GET['deletedV'] == "1") {
	showHeader($info, 3);
	echo '<div class="confirm-box round">Voucher apagado!</div>';
} else if($info[5] && $_GET && isset($_GET['deletedV']) && $_GET['deletedV'] == "-1") {
	showHeader($info, 3);
	echo '<div class="err-box round">O voucher não pôde ser apagado!</div>';
}

else if($info[5] && $_GET && isset($_GET['edited']) && $_GET['edited'] == "1") {
	showHeader($info, 3);
	echo '<div class="confirm-box round">Utilizador editado!</div>';
} else if($info[5] && $_GET && isset($_GET['edited']) && $_GET['edited'] == "-1") {
	showHeader($info, 3);
	echo '<div class="err-box round">O utilizador não pôde ser editado!</div>';
}

else if($_GET && isset($_GET['func']) && $_GET['func'] == "delete") {
	showHeader($info, 3);
	if(isset($_GET['id']) && !empty($_GET['id'])){
		$user=$membership->getUserInfo($_GET['id']);
		if(!$user) {
			header('location:/errors/404.php');
		}
		
		echo '
				 <!-- Module -->
				<div class="content-module">
					<!-- Module Heading -->
					<div class="content-module-heading cf">
						<h3 class="fl">Apagar utilizador</h3>
						<span class="fr span-text">Clica para contrair</span>
						<span class="fr expand-span span-text">Clica para expandir</span>
					</div><!-- End Module Heading -->
					
					<!-- Module Main -->
					<div class="content-module-main">';
							
		echo'
						<form action="/manage.php" method="post">
						
							<fieldset>
								<input type="hidden" name="id" id="id" value="'.$user['id'].'" />
								<input type="hidden" name="func" id="func" value="deleteUser" />
								<label>Quer mesmo apagar o utilizador '.$user['nick'].'?</label>
								<a href="/manage.php" class="round button green">Não</a>
								<input type="submit" value="Sim" class="button round green image-right ic-arrow-right" />
							</fieldset>
						
						</form>';
				
		echo'
					</div><!-- End Module Main -->
				</div> <!-- End Module -->';
	}
}

else if($_POST && isset($_POST['func']) && $_POST['func'] == "deleteUser") {
	if(isset($_POST['id']) && !empty($_POST['id'])) {
		// Delete function
		$returned = $membership->deleteUser($_POST['id']);
		
		if($returned==1)
			header('location:/manage.php?deleted=1');
		else if($returned == -1)
			header('location:/manage.php?deleted=-1');
	}
}

else if($_GET && isset($_GET['func']) && $_GET['func'] == "edit") {
	showHeader($info, 3);
	if(isset($_GET['id']) && !empty($_GET['id'])) {
		$user=$membership->getUserInfo($_GET['id']);
		
		echo '
				 <!-- Module -->
				<div class="content-module">
					<!-- Module Heading -->
					<div class="content-module-heading cf">
						<h3 class="fl">Editar utilizador</h3>
						<span class="fr span-text">Clica para contrair</span>
						<span class="fr expand-span span-text">Clica para expandir</span>
					</div><!-- End Module Heading -->
					
					<!-- Module Main -->
					<div class="content-module-main">';
							
		echo'
						<form action="/manage.php" method="post">
						
							<fieldset>
								<input type="hidden" name="func" id="func" value="editUser" />
								<input type="hidden" name="id" id="id" value="'.$user['id'].'" />
								<label>A editar o utilizador '.$user["nick"].':</label>
								
								<p>
								<label for="fname">Primeiro Nome</label>
								<input type="text" name="fname" id="fname" value="'.$user['fname'].'" class="round default-width-input" />
								</p>
								
								<p>
								<label for="sname">Último Nome</label>
								<input type="text" name="sname" id="sname" value="'.$user['sname'].'" class="round default-width-input" />
								</p>
								
								<p>
								<label for="email">Email</label>
								<input type="text" name="email" id="email" value="'.$user['email'].'" class="round full-width-input" />
								</p>
								
								<p>
								<label for="nsumm">Saldo</label>
								<input type="text" name="nsumm" id="nsumm" value="'.$user['nsumm'].'" class="round default-width-input" />
								</p>
								
								<p>';
								
								// Admin
								if($user["admin"]==1)
								echo '<label for="admin" class="alt-label">
								<input type="checkbox" id="admin" name="admin" checked="checked" />Admin</label></p>';
								
								else echo '<label for="admin" class="alt-label">
								<input type="checkbox" id="admin" name="admin" />Admin</label></p>';
								
								if($user["myClass"]==1)
								echo '<p><label for="class" class="alt-label">
								<input type="checkbox" id="class" name="class" checked="checked" />Da tua turma</label></p>';
								
								else echo '<p><label for="class" class="alt-label">
								<input type="checkbox" id="class" name="class" />Da tua turma</label></p>';
								
								// Deactivated
								if($user["deactivated"]==1)
								echo '<p><label for="deactivated" class="alt-label">
								<input type="checkbox" id="deactivated" name="deactivated" 
								checked="checked" />Desativado</label></p>';
								
								else echo '<p><label for="deactivated" class="alt-label">
								<input type="checkbox" id="deactivated" name="deactivated" />Desativado</label></p>';
								
								echo '<p><label for="reason">Razão: Estás desativado porque... (Continuar a frase)</label>
								<textarea id="reason" name="reason" 
								class="round full-width-textarea" rows="10">'.$user['reason'].'</textarea></p>';
							
							echo '
								<a href="/manage.php" class="round button green">Cancelar</a>
								<input type="submit" value="Editar" class="button round green image-right ic-arrow-right" /></fieldset>
								<div class="stripe-separator"></div>
								<p><b>Informações sobre o utilizador</b> (algumas informações não foram tratadas)</p>
								<ul class="small marged">
                    
									<li><b>Comprou:</b>  '.$user['boughtSumm'].'</li>
									<li><b>Última vez online:</b>  '.$user['lastDate'].'</li>
									<li><b>Último IP:</b>  '.$user['lastIp'].'</li>
									<li><b>Primeiro IP (IP do registo):</b>  '.$user['initIp'].'</li>
								
								</ul>
						
						</form>';
				
		echo'
					</div><!-- End Module Main -->
				</div> <!-- End Module -->';
	}
}

else if($_POST && isset($_POST['func']) && $_POST['func'] == "editUser") 
{
	if(isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['fname']) && !empty($_POST['fname']) && isset($_POST['sname']) && !empty($_POST['sname']) && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['nsumm']) && isset($_POST['reason'])) {
		
		$admin = 0;
		$nsumm = 0;
		$myClass = 0;
		$deactivated = 0;
		
		if(isset($_POST['admin'])) {
			$admin = 1;
		}
		if(isset($_POST['deactivated'])) {
			$deactivated = 1;
		}
		if(isset($_POST['class'])) {
			$myClass = 1;
		}
		
		if(empty($_POST['nsumm'])) {
			$nsumm = 0;
		}
		else {
			$nsumm = $_POST['nsumm'];
		}
		
		$returned = $membership->editUser($_POST['id'], $_POST['fname'], $_POST['sname'], $_POST['email'], $nsumm, $admin, $deactivated, $_POST['reason'], $myClass);
		
		header('location:/manage.php?edited='.$returned);
	}
}

else if($_GET && isset($_GET['func']) && $_GET['func'] == "deleteVoucher") {
	if(isset($_GET['id']) && !empty($_GET['id'])) {
		// Delete function
		$returned = $membership->deleteVoucher($_GET['id']);
		
		if($returned==1)
			header('location:/manage.php?deletedV=1');
		else
			header('location:/manage.php?deletedV=-1');
	}
}

else if($_POST && isset($_POST['func']) && $_POST['func'] == "addVoucher") {
	showHeader($info, 3);
	if(isset($_POST['quantV']) && !empty($_POST['quantV']) && isset($_POST['valueV']) && !empty($_POST['valueV'])) {
		// Add function
		$addedV = $membership->addVouchers($_POST['quantV'], $_POST['valueV']);
	
	}
}

else {
	showHeader($info, 3);
}
if(isset($addedV) && !empty($addedV)) {
	echo $addedV;
}
$membership->echoUsers();

$membership->echoVouchers();
?>
<!-- Module -->
<div class="content-module">
    <!-- Module Heading -->
    <div class="content-module-heading cf">
        <h3 class="fl">Recibos</h3>
        <span class="fr span-text">Clica para contrair</span>
        <span class="fr expand-span span-text">Clica para expandir</span>
    </div><!-- End Module Heading -->
    
    <!-- Module Main -->
    <div class="content-module-main">
    	<div class="cf">
            <!-- Form part of the search -->
            <div class="fl" style="max-width:30%;">
                <form action="php/ajax.php" id="receipts-search">
                    <fieldset>
                        <p>
                            <label for="receipts-search-inp">Procurar por ID ou nome/nick da pessoa</label>
                            <input type="text" placeholder="Procurar..." id="receipts-search-inp" name="receipts-search-inp" class="round full-width-input" />
                        </p>
                        
                        <p class="cf" style="display:block;" id="forgotten"><a href="clean" target="_blank" class="dark-link fr" id="clean">Limpar resultados</a></p>
                    </fieldset>
                </form>
            </div>
            
            <!-- Results -->
            <div class="fr" style="max-width:65%;min-width:45%;margin-top:0.7em;display:none;" id="search-receipts-results">
            </div>
        </div>
        <div class='stripe-separator'></div>
        <div style="padding:1em;border: thin dashed gray;padding:1em;">
        	<h1><b>Últimos 20 recibos:</b></h1>
            <?php
				$membership->echoReceipts(20);
			?>
        </div>
    </div><!-- End Module Main -->
</div><!-- End Module -->
<script>
$("#receipts-search").submit(function(e) {
    e.preventDefault();
	
	$.ajax({
		type: "POST",
		url: $("#receipts-search").attr("action"),
		data: {func: "search-receipts", q: $("#receipts-search-inp").val()},
		success: function (data) {
			if(!data.success) {
				$("#search-receipts-results").html("<div class='round err-box'>Ocorreu um erro!</div>").slideDown();
				return;
			}
			var html = "<div style='border: thin dashed gray;padding:1em;'>";
			
			
			if(data.results.length == 0) {
				html += "<h3 style='margin:0;'><b>Não encontramos resultados para esse termo de pesquisa.</b></h3>";
			} else {
				for(var i=0; i<data.results.length; i++) {
					var receipt = data.results[i];
					if(i > 0) {
						html += "<div class='stripe-separator'></div>";
					}
					if(receipt.type == "usedVoucher") {
						html += "<h4><b>Id:</b> "+receipt.id+"</h4>";
						html += "<p><b>Tipo:</b> Usou um voucher<br/>";
						html += "<p><b>Nome da pessoa:</b> "+receipt.who+"<br/>";
						html += "<p><b>Data e Hora:</b> "+receipt.time+"<br/>";
						html += "<p><b>Valor do voucher:</b> "+receipt.info["voucher-value"]+"<br/>";
						html += "<p><b>Código do voucher:</b> "+receipt.info["voucher-code"]+"<br/>";
						html += "<p><b>Id do voucher:</b> "+receipt.info["voucher-id"]+"</p>";
					} else {
						html += "<h4><b>Id:</b> "+receipt.id+"</h4>";
						html += "<p><b>Tipo:</b> Comprou um documento<br/>";
						html += "<p><b>Nome da pessoa:</b> "+receipt.who+"<br/>";
						html += "<p><b>Data e Hora:</b> "+receipt.time+"<br/>";
						html += "<p><b>Id do documento:</b> "+receipt.info["summ-id"]+"<br/>";
						html += "<p><b>Preço:</b> "+receipt.info["price"]+"<br/>";
						html += "<p><b>Desconto:</b> "+receipt.info["discount"]+"%<br/>";
						html += "<p><b>Compra número:</b> "+receipt.info["bought-number"]+"</p>";
					}
				}
			}
			
			html += "</div>";
			$("#search-receipts-results").html(html).slideDown();
		}
	}).fail(function(jqXHR, textStatus) {
  		$("#search-receipts-results").html("<div class='round err-box'>Ocorreu um erro!</div>").slideDown();
});

$("#clean").click(function (e) {
	e.preventDefault();
	$("#search-receipts-results").slideUp(function(){html("")});
});
});
</script>
<?php
showFooter();