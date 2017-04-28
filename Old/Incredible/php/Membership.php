<?php
require_once 'Mysql.php';
require_once 'functions.php';

class Membership {
	private $mysql;
	
	// CONSTRUCTOR
	function __construct($needUser = true, $onlyNonLoggedUsers=false) {
		$this->mysql = new Mysql();
		if(!isset($_SESSION))
			session_start();
		
		// VERIFY IF USER IS LOGGED IN TO ACCESS ONLY USER PAGES
		if($needUser) {				
			if($_SESSION['status'][1] != 'authorized' && $_SESSION['status'][1] != 'deactivated') 
				header("location: /login.php");
		}
		
		// IF USER IS LOGGED IN AND TRY TO ACCESS THE LOGIN PAGE, HE IS REDIRECTED TO THE MAIN PAGE
		if(!empty($_SESSION['status'][1]) &&  $_SESSION['status'][1]== 'authorized' && $onlyNonLoggedUsers===true)
			header('location: index.php');
			
		if($_SESSION['status'][1] == 'deactivated' && $_SERVER['PHP_SELF'] != '/errors/deactivated.php') {
			header('location:/errors/deactivated.php');
		}
	}
	
	
	
	// REGISTER FUNCTION
	function register($nick, $password, $fname, $sname, $email) {
		$errors = "";
		if(strlen($nick) > 15) {
			$errors .= 'Nome de utilizador muito grande (tem de ter menos de 16 caracteres);';
		}
		if(strlen($password) < 6) {
			$errors .= 'Password muito pequena (tem de ter mais que 5 caracteres);';
		}
		if(strlen($password) > 36) {
			$errors .= 'Password muito grande (tem de ter menos de 36 caracteres);';
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    		$errors .= 'Email inválido;';
		}
		
		// Allowed email hosts
		$allowedHosts = array("gmail.com", "hotmail.com", "live.com.pt");
		$host = explode('@', $email);
		$host=$host[1];
		if(!in_array($host, $allowedHosts)) {
			$text = "
<html><body>
<p><b>Aviso de criação de conta com email desconhecido!</b></p>
<p>Hoje, $fname $sname tentou criar conta com o nome de utilizador $nick e com o email $email.</p>
<p>O host $host não foi reconhecido. Por favor, confira o email do cliente $nick</p>
</body></html>";

			sendMail("goncalossantos98@gmail.com, extremezgamer@gmail.com", $text, 'Host desconhecido!', 'verify', false);
		}
		
		// Cortar espaços a mais
		$fname = trim($fname);
		$sname = trim($sname);
		
		// Pôr a primeira letra maiúscula
		$fname = ucfirst($fname);
		$sname = ucfirst($sname);
		
		if(empty($fname) || empty($sname)) {
			$errors .= 'Alguns dos elementos deram erro. Por favor insira informação correta!;';
		}
		
		return $this->mysql->register(strip($nick), strip($password), strip($fname), strip($sname), strip($email), $errors);
	}
	
	// ACTIVATE ACCOUNT
	function activate($activcode){
		return $this->mysql->activate(strip($activcode));
	}
	
	// VALIDATE CREDENTIALS
	function validateCredentials($nick, $password) {
		$nick = strip($nick);
		$password = strip($password);
		
		return $this->mysql->validateCredentials($nick, $password);
	}
	
	// VERIFY INFO
	function verifyInfo() {
		$info = $this->mysql->verifyInfo();
		
		if($info == false)
			$this->logout();
		else
			$info = explode('-;brk;-', $info);
		
		return $info;
	}
	
	// CONFIRM THAT ADMIN IS LOGGED IN TO VIEW PAGE
	function adminPage() {
		if(!$this->mysql->isAdmin()) {
			header('location: errors/404.php');
		}
	}
	
	// UPDATE PROFILE
	function update($type, $a, $b=0, $c=0) {
		if($type == "password") {
			if($b != $c)
				return 0;
			else if(strlen($b) < 6)
				return -1;
			else if(strlen($b) > 36)
				return -2;
			else
				return $this->mysql->update($type, strip($a), strip($b));
		}
		
		else if($type == "email") {
			if (!filter_var($a, FILTER_VALIDATE_EMAIL)) {
    			return -1;
			}
			else
				return $this->mysql->update($type, strip($a));
		}
	}
	
	// ECHO USERS
	function echoUsers() {
		
		$users = $this->mysql->getUsers();
		
				echo <<<EOM
			<div class="content-module">
				<!-- Module Heading -->
				<div class="content-module-heading cf">
					<h3 class="fl">Utilizadores</h3>
					<span class="fr span-text">Clica para contrair</span>
					<span class="fr expand-span span-text">Clica para expandir</span>
				</div><!-- End Module Heading -->
				
				<!-- Module Main -->
				<div class="content-module-main">
					<table class="accounts">
					
						<thead>
					
							<tr>
								<th class="small-col">Nº</th>
								<th>Nome de Utilizador</th>
								<th>Nome</th>
								<th>Email</th>
								<th class="small-col">Saldo</th>
								<th class="small-col">Última vez online</th>
								<th>Ações</th>
							</tr>
						
						</thead>
						<tbody>
EOM;
				
				for($i=0; $i<count($users); $i++) {
					$count = $i + 1;
					echo "<tr";
					if($users[$i]["deactivated"] == 1)
						echo ' class="t-red"';
					else if($users[$i]["activated"] != 1)
						echo ' class="t-yellow"';
					else if($users[$i]["admin"] == 1)
						echo ' class="t-green"';
					echo ">";
					echo "<td class='small-col'>". $count ."</td>"; // Count
					echo "<td>". $users[$i]["nick"] ."</td>"; // Nome de Utilizador
					echo "<td>". $users[$i]["name"] ."</td>"; // Nome
					echo '<td><a href="/mail.php?to='.$users[$i]["email"].'">'. $users[$i]["email"] ."</a></td>"; // Email
					echo "<td class='small-col'>". number_format($users[$i]["nsumm"], 2) ."€</td>"; // Slado
					echo "<td class='small-col'>". $users[$i]["lastDate"] ."</td>"; // Última data online
					echo "<td><a href='/manage.php?func=edit&id=". $users[$i]["id"]."' class='table-actions-button ic-table-edit'></a>
                              <a href='/manage.php?func=delete&id=". $users[$i]["id"]."' class='table-actions-button ic-table-delete'></a></td></tr>"; // Ações
				}
				
				echo '</tbody></table></div><!-- End Module Main --> </div><!-- End Module -->';
	}
	
	// DELETE USER
	function deleteUser($id) {
		return $this->mysql->deleteUser(strip($id));
	}
	
	// GET USER INFO
	function getUserInfo($id) {
		return $this->mysql->getUserInfo(strip($id));
	}
	
	// EDIT USER
	function editUser($id, $fname, $sname, $email, $nsumm, $admin, $deactivated, $reason, $myClass) {
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    		return '-1';
		}

		$nsumm = comma2Point($nsumm);
		$nsumm = (is_numeric($nsumm) ? $nsumm : -1);
		if($nsumm < 0) {
			return -1;
		}
		
		return $this->mysql->editUser(strip($id), strip($fname), strip($sname), strip($email), strip($nsumm), strip($admin), strip($deactivated), strip($reason), strip($myClass));
	}
	
	// LOGS OUT
	function logout() {
		if(!isset($_SESSION))
			session_start();
		session_destroy();
		session_unset();
		header('location: login.php');
	}
	
	// FORGOT PASSWORD
	function forgotConfirm($email) {
		$email = strip($email);
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    		return 'Email inválido';
		}
		
		return $this->mysql->forgotConfirm($email);
	}
	
	function forgot($code) {
		$code = strip($code);
		
		return $this->mysql->forgot($code);
	}
	
	// ECHO VOUCHERS
	function echoVouchers() {
		$vouchers = $this->mysql->getVouchers();
		
		?>
        <div class="content-module">
            <!-- Module Heading -->
            <div class="content-module-heading cf">
                <h3 class="fl">Vouchers ativos</h3>
                <span class="fr span-text">Clica para contrair</span>
                <span class="fr expand-span span-text">Clica para expandir</span>
            </div><!-- End Module Heading -->
            
            <!-- Module Main -->
            <div class="content-module-main">
            
 <?php 
 		if($vouchers != NULL) {
			echo'
			<table class="accounts" id="vouchers">
				<thead>
					<tr>
						<th>Código</th>
						<th>Valor</th>
						<th>Ações</th>
					</tr>
				</thead>
				<tbody>';
			for($i=0; $i<count($vouchers); $i++) {
				?>	
				<tr>
                    <td class="code"><?=$vouchers[$i]["code"]?></td>
                    <td class="value"><?=$vouchers[$i]["value"]?></td>
                    <td><a href='/manage.php?func=deleteVoucher&id=<?=$vouchers[$i]["id"]?>' class='table-actions-button ic-table-delete'></a></td>
                </tr>
				<?php
			}
			echo '</tbody></table>';	
			
		} 
			
		else {
			echo '<div class="round warn-box">Não há vouchers para mostrar.</div>';
		}
		
		?>
        
        <div class="stripe-separator"></div>
        <form action="/manage.php" method="post" id="addVouchers">	
            <fieldset>
            	<input type="hidden" value="addVoucher" name="func" id="func" />
                <p>
                    <label for="quantV">Quantidade de vouchers para criar</label>
                    <input type="text" id="quantV" name="quantV" class="round default-width-input" autocomplete="off" />
                    <em>Máximo 10 vouchers</em>
                </p>
                
                <p>
                    <label for="valueV">Valor dos vouchers</label>
                    <input type="text" id="valueV" name="valueV" class="round default-width-input" autocomplete="off" />		
                    <em>Máximo 10 resumos por voucher</em>					
                </p>
                
                <input type="submit" value="Adicionar" class="button round green image-right ic-arrow-right" /></fieldset>
                
        	</fieldset>
		</form>
        <p style="margin-top:1em;"><a id="showVouchersText" href="">Copy and Paste Vouchers!</a></p>
        <script>
        $('#showVouchersText').click(function () {
			$('#showVouchersText').slideUp(250, function () {			
				var vouchersText = "<textarea class='round full-width-textarea' rows='10' style='margin-top:1em; display:none;' id='copyAndPasteVouchers'>";
				
				$('table#vouchers tbody tr').each(function () {
					vouchersText += $(this).children('td.code').text() + '  ';
					var length = 25 - $(this).children('td.code').text().length;
					for(var i=0; i<length; i++) {
						vouchersText += '-';
					}
					vouchersText += '>  ' + $(this).children('td.value').text() + '\n';
				});
				vouchersText += "</textarea>";
				$('#addVouchers').after(vouchersText);
				$('#copyAndPasteVouchers').slideDown(750);
			});
			return false;
		});
        </script>
        
        <?php
		
		echo '</div><!-- End Module Main --> </div><!-- End Module -->';
	}
	
	// DELETE VOUCHER
	function deleteVoucher($id) {
		return $this->mysql->deleteVoucher($id);
	}
	
	// ADD VOUCHERS
	function addVouchers($quant, $value) {
		$text = "";
		
		$quant = (is_numeric($quant) ? (int)$quant : -1);
		if($quant < 0 || $quant > 10) {
			$text .= "<div class='round err-box'>A quantidade não era válida!</div>";
		}
		
		$value = (is_numeric($value) ? (int)$value : -1);
		if($value < 0 || $value > 10) {
			$text .= "<div class='round err-box'>O valor não era válido!</div>";
		}
		
		if($text == "") {
			$text .= $this->mysql->addVouchers($quant, $value);
		}
		
		return $text;
	}
	
	// USE VOUCHER
	function useVoucher($VCode) {
		$VCode = strip($VCode);
		
		return $this->mysql->useVoucher($VCode);
	}
	
	// ECHO RECEIPTS
	function echoReceipts($num) {
		$num = strip($num);
		$num = (is_numeric($num) ? (int)$num : -1);
		if($quant < 0) {
			echo "<div class='round err-box'>Erro!</div>";
			return false;
		}
		$lastReceipts = $this->mysql->getLastReceipts($num);
		
		$html = "";
		for($i=0;$i<count($lastReceipts);$i++) {
			$receipt = $lastReceipts[$i];
			if($i>0) {
				$html .= "<div class='stripe-separator'></div>";
			}
			if($receipt["type"] == "usedVoucher") {
				$html .= "<h4><b>Id:</b> ".$receipt["id"]."</h4>
						<p><b>Tipo:</b> Usou um voucher<br/>
						<p><b>Nome da pessoa:</b> ".$receipt["who"]."<br/>
						<p><b>Data e Hora:</b> ".$receipt["time"]."<br/>
						<p><b>Valor do voucher:</b> ".$receipt["info"]["voucher-value"]."<br/>
						<p><b>Código do voucher:</b> ".$receipt["info"]["voucher-code"]."<br/>
						<p><b>Id do voucher:</b> ".$receipt["info"]["voucher-id"]."</p>";
			} else {
				$sName = $this->mysql->getSummaryInfo($receipt["info"]["summ-id"]);
				if($sName['expression'] == 1) {
					$sName = explode(".", $sName['name']);
					$sName = ucwords($sName[0]).' - '.$sName[1].'º Ano, '.$sName[2].'º Período';
				} else {
					$sName = ucfirst($sName['name']);
				}
				$html .= "<h4><b>Id:</b> ".$receipt["id"]."</h4>
						<p><b>Tipo:</b> Comprou um documento<br/>
						<p><b>Nome da pessoa:</b> ".$receipt["who"]."<br/>
						<p><b>Data e Hora:</b> ".$receipt["time"]."<br/>
						<p><b>Nome do documento:</b> <a href='/summaries.php?func=download&id=".$receipt["info"]["summ-id"]."'>".$sName."</a><br/>
						<p><b>Preço:</b> ".$receipt["info"]["price"]."<br/>
						<p><b>Desconto:</b> ".$receipt["info"]["discount"]."%<br/>
						<p><b>Compra número:</b> ".$receipt["info"]["bought-number"]."</p>";
			}
		}
		
		echo $html;
		return true;
	}
	
	// SUBSCRIBE TO THE EMAIL LIST
	function subEmail() {
		return $this->mysql->subEmail();
	}
	
	// UNSUBSCRIBE FROM THE EMAIL LIST
	function unsubEmail() {
		return $this->mysql->unsubEmail();
	}
	
}