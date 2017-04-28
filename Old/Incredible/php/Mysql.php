<?php
require_once('constants.php');
require_once('functions.php');

class Mysql {
	private $mysql;
	
	// CONSTRUCTOR
	function __construct() {
		$this->mysql = mysql_connect(DB_SERVER, DB_USER, DB_PASSWORD); 
					  if (!$this->mysql)
  					  {
  					         $error = 'Não nos conseguimos ligar à base de dados: ' . mysql_error();
  					         header("location: errors/error.php?error=$error");
  					  }
		mysql_select_db(DB_NAME, $this->mysql);
		mysql_query("set names utf8;");
		if(!isset($_SESSION)) {
			session_start();
		}
		
		if(!empty($_SESSION['status'][1])) {
			$this->verifyInfo(false);
		}
	}
	
	// REGISTER FUNCTION
	function register($nick, $password, $fname, $sname, $email, $errors) {
		// VERIFY IF ERVERYTHING IS UNIQUE
		$initIp = getIp();
				
		$lastDate = "dd/MM/yyyy hh:mm";
		$lastDate = getDateTime($lastDate);
		
		$result = mysql_query("SELECT * FROM users");	
		while($row = mysql_fetch_array($result))
  		{
  			if(strtoupper($row['nick']) == strtoupper($nick))
				$errors .= 'O nome de utilizador escolhido já está a ser utilizado;';
			if (strtoupper($row['email']) == strtoupper($email))
				$errors .= 'O email escolhido já está a ser utilizado por outro utilizador;';
 		}
		if(!empty($errors))
			return $errors;
		while(true) {
			$activcode = rand() . rand();
			$sql = "SELECT * FROM users WHERE activcode='$activcode'";
			$result = mysql_query($sql);
			if(mysql_num_rows($result)==0) {
				break;
			}
		}
			
		// INSERT USER
		$sql = "INSERT INTO users (nick, password, fname, sname, email, activcode, initIp, lastIp, lastDate) VALUES ('$nick', '$password', '$fname', '$sname', '$email', '$activcode', '$initIp', '$initIp', '$lastDate')";
		
		
		if (!mysql_query($sql)) {
			$errors .= mysql_error() . ';';
			return $errors;
		}
		
		
		// EMAIL
		$message = "
<html><body>
<p>Olá $fname $sname,</p>
<p>Para completar o registo da conta na Incredible Community, cole o código abaixo na caixa de texto do site:
<br /> <br />+-------------------------------------------------------------------------------------------
<br /> | Código de ativação: <b>$activcode</b>
<br />+-------------------------------------------------------------------------------------------</p>

<p>+------------Credenciais-------------
<br /> | Nome de Utilizador: $nick
<br /> | Password: $password
<br />+------------------------------------------</p>

<p>Esta conta será completamente apagada se não for ativada num período de 7 dias.</p>

<p>Obrigado pela sua participação nesta comunidade</p><p><br /><b>PS:</b> Se por acaso saíste da página de pôr o código, deixa o login em branco e clica para fazer login. Depois tens de clicar no link de ativar.</p></body></html>";
			
		sendMail($email, $message, 'Ativar a conta', 'activate', false);
		
		return 'none';
	}
	
	
	// ACTIVATE ACCOUNT
	function activate($activcode) {
		$result = mysql_query("SELECT activated FROM users WHERE activcode='$activcode'");
		if(!$result) {
			die(mysql_error());
		}
		
		$row = mysql_fetch_row($result);
		if(!$row) {
			return 'Este código não está ligado a nenhuma conta.';
		}
		
		if($row[0] != '1') {
			$sql = "UPDATE users SET activated = '1' WHERE activcode = '$activcode'";
			if(mysql_query($sql)) {
				$done = true;
				return $row[0];
			}
			else {
				$done = false;
				return "Erro: " . $this->mysql->error;
			}
		}
		
		else if($row[0] == 1) {
			$done = false;
			return '-1';
		}
		
		return 'Erro desconhecido, por favor avise a equipa.';
	}
	
	// VALIDATE CREDENTIALS
	function validateCredentials($nick, $password) {
		$errors = "";
		$result = mysql_query("SELECT * FROM users");
				
		while($row = mysql_fetch_array($result)) {
			if(strtoupper($row['nick']) == strtoupper($nick) && ($row['password'] == $password || $row['password'] == md5($password)) && $row['activated'] == 1) {
				$_SESSION['id'] = $row['id'];
				
				$lastIp = getIp();

				$lastDate = getDateTime("dd/MM/yyyy hh:mm");
				
				if(!mysql_query("UPDATE users SET lastDate='$lastDate', lastIp='$lastIp' WHERE id=" . $_SESSION['id'])) {
					$errors = 'couldNotValidate';
				}
				
				if($row['deactivated'] == 1) {
					$_SESSION['status'][1] = 'deactivated';
					$_SESSION['status'][2] = $row['reason'];
					header('location: deactivated.php');
				}else {
					$_SESSION['status'][1] = 'authorized';
					header('location: index.php');
				}
			} else
				$errors = 'couldNotValidate';
		}
		return $errors;
	}
	
	// VERIFY INFO
	function verifyInfo($all = true) {
		$id = $_SESSION['id'];
		$result = mysql_query("SELECT * FROM users WHERE id=$id");
				
		if($all) {
			while($row = mysql_fetch_array($result)) {
				if($row['id'] == $_SESSION['id']) {
					$info = $row['nick'] . '-;brk;-';	// 0
					$info .= $row['fname'] . '-;brk;-';	// 1
					$info .= $row['sname'] . '-;brk;-';	// 2
					$info .= $row['email'] . '-;brk;-';	// 3
					$info .= $row['nsumm'] . '-;brk;-';	// 4
					$info .= $row['admin'] . '-;brk;-';	// 5
					$info .= $row['cpost'] . '-;brk;-';	// 6
					$info .= $row['myClass'].'-;brk;-'; // 7
					$info .= $row['teacher'].'-;brk;-';	// 8
					$info .= $row["subscribed"];		// 9
					$_SESSION['cpost'] = $row['cpost'];
					if($row['deactivated'] == 1) {
						$_SESSION['status'][1] = 'deactivated';
						$_SESSION['status'][2] = $row['reason'];
					} else {
						$_SESSION['status'][1] = 'authorized';
					}
					
					
					$lastIp = getIp();
				
					$lastDate = getDateTime("dd/MM/yyyy hh:mm");
					mysql_query("UPDATE users SET lastDate='$lastDate', lastIp='$lastIp' WHERE id=" . $_SESSION['id']);
					
					return $info;
				}
			}
		} else {
			while($row = mysql_fetch_array($result)) {
				if($row['id'] == $_SESSION['id']) {
					$_SESSION['cpost'] = $row['cpost'];
					if($row['deactivated'] == 1) {
						$_SESSION['status'][1] = 'deactivated';
						$_SESSION['status'][2] = $row['reason'];
					} else {
						$_SESSION['status'][1] = 'authorized';
					}
					return;
				}
			}
		}
		
		if(!isset($info))
			return false;
		
		return $info;
	}
	
	// RETURN TRUE IF ADMIN
	function isAdmin() {
		$userID = $_SESSION['id'];
		$result = mysql_query("SELECT admin FROM users WHERE id=$userID LIMIT 1");
				
		$data = mysql_fetch_row($result);
		
		if($data[0] == 1) {
			return true;
		}
		
		return false;
	}

	// RETURN TRUE IF TEACHER
	function isFromClass() {
		$userID = $_SESSION['id'];
		$result = mysql_query("SELECT myClass FROM users WHERE id=$userID LIMIT 1");
				
		$data = mysql_fetch_row($result);
		
		if($data[0] == 1) {
			return true;
		}
		
		return false;
	}
	
	// RETURN TRUE IF TEACHER
	function isTeacher() {
		$userID = $_SESSION['id'];
		$result = mysql_query("SELECT teacher FROM users WHERE id=$userID LIMIT 1");
				
		$data = mysql_fetch_row($result);
		
		if($data[0] == 1) {
			return true;
		}
		
		return false;
	}
	
	// UPDATE PROFILE
	function update($type, $a = 0, $b = 0) {
		
		if($type == "password") {
			$result = mysql_query("SELECT * FROM users WHERE id='" . $_SESSION['id']. "' LIMIT 1");
			while($row = mysql_fetch_array($result)) {
				if($row['password'] != $a)
					return 0;
				else {
					mysql_query("UPDATE users SET password='". $b ."'
WHERE id='" . $_SESSION['id']. "'");
					return 1;
				}
			}
		}
		
		if($type == "email") {
			$result = mysql_query("SELECT * FROM users WHERE id='" . $_SESSION['id']. "' LIMIT 1");
			while($row = mysql_fetch_array($result)) {
				if($row['email'] != $a)
					return 0;
				else {
					mysql_query("UPDATE users SET tempEmail='". $b ."'
WHERE id='" . $_SESSION['id']. "'");
					return 1;
				}
			}
		}
	}
	
	// GET USERS ARRAY
	function getUsers() {
		$result = mysql_query("SELECT * FROM users ORDER BY id ASC");
		$count = 0;
		$users = NULL;
		
		while($row = mysql_fetch_array($result)) {
			$users[$count]["id"] = $row["id"];
			$users[$count]["nick"] = $row["nick"];
			$users[$count]["name"] = $row["fname"] . " " . $row["sname"];
			$users[$count]["email"] = $row["email"];
			$users[$count]["nsumm"] = $row["nsumm"];
			$users[$count]["lastDate"] = $row["lastDate"];
			$users[$count]["admin"] = $row["admin"];
			$users[$count]["activated"] = $row["activated"];
			$users[$count]["deactivated"] = $row["deactivated"];
			$users[$count]["teacher"] = $row["teacher"];
			
			$count++;
		}
		
		return $users;
	}
	
	// DELETE USER
	function deleteUser($id) {
		if(mysql_query("UPDATE FROM users SET archived=1 WHERE id=$id LIMIT 1")) {
			return 1;
		}
		
		return -1;
	}
	
	// GET USER INFO
	function getUserInfo($id){
		$result = mysql_query("SELECT * FROM users WHERE id='$id'");
		
		while($row = mysql_fetch_array($result)) {
			$user["id"] = $row["id"];
			$user["nick"] = $row["nick"];
			$user["fname"] = $row["fname"];
			$user["sname"] = $row["sname"];
			$user["email"] = $row["email"];
			$user["nsumm"] = $row["nsumm"];
			$user["admin"] = $row["admin"];
			$user["deactivated"] = $row["deactivated"];
			$user["reason"] = $row["reason"];
			$user["myClass"] = $row["myClass"];
			$user["boughtSumm"] = $row["boughtSumm"];
			$user["lastDate"] = $row["lastDate"];
			$user["lastIp"] = $row["lastIp"];
			$user["initIp"] = $row["initIp"];
			$user["teacher"] = $row["teacher"];
			$user["subscribed"] = $row["subscribed"];
			
			if(empty($user["initIp"])) {
				$user["initIp"] = "----";
			}
			if(empty($user["boughtSumm"])) {
				$user["boughtSumm"] = "----";
			}
		}
		
		if(!isset($user))
			return false;
		return $user;
	}
	
	// EDIT USER
	function editUser($id, $fname, $sname, $email, $nsumm, $admin, $deactivated, $reason, $myClass) {
		$query = "UPDATE users SET 
		fname='$fname', sname='$sname', email='$email', nsumm=$nsumm, 
		admin=$admin, deactivated=$deactivated, reason='$reason', myClass=$myClass          WHERE id=$id";
		
		if(mysql_query($query))
			return '1';
		return -1;
	}
	
	// ADD SUMMARY
	function addSummary($location, $price, $expression, $name, $subject, $classOnly, $byOthers, $littleDesc) {
		if($expression == true) {
			if(mysql_query("INSERT INTO summaries (location, name, subject, price, expression, nDownloads, classOnly, byOthers, littleDesc) VALUES ('$location', '$name', '$subject', $price, 1, 0, $classOnly, $byOthers, '$littleDesc')")) {
				return 0;
			} else {
				return -1;
			}
		}
		
		if($expression == false) {
			if(mysql_query("INSERT INTO summaries (location, name, subject, price, expression, nDownloads, classOnly, byOthers, littleDesc) VALUES ('$location', '$name', '$subject', $price, 0, 0, $classOnly, $byOthers, '$littleDesc')")) {
				return 0;
			} else {
				return -1;
			}
		}
	}
	
	// GET SUMMARIES
	function getSummaries() {
		$result = mysql_query("SELECT * FROM summaries ORDER BY nDownloads DESC");
		$i = 0;
		$summaries = NULL;
		
		while($row = mysql_fetch_array($result)) {
			$summaries[$i]['id'] = $row['id'];
			$summaries[$i]['name'] = $row['name'];
			$summaries[$i]['subject'] = $row['subject'];
			$summaries[$i]['price'] = $row['price'];
			$summaries[$i]['discount'] = $row['discount'];
			$summaries[$i]['expression'] = $row['expression'];
			$summaries[$i]['nDownloads'] = $row['nDownloads'];
			$summaries[$i]['classOnly'] = $row['classOnly'];
			$summaries[$i]['littleDesc'] = $row['littleDesc'];
			$summaries[$i]['byOthers'] = $row['byOthers'];
			
			$i++;
		}
		
		if($summaries != NULL) {
			return $summaries;
		} else
			return 0;
	}
	
	// GET SUMMARY INFO
	function getSummaryInfo($id) {
		$result = mysql_query("SELECT * FROM summaries WHERE id=$id");
		$info = NULL;
		
		while($row = mysql_fetch_array($result)) {
			$info['id'] = $id;
			$info['location'] = $row['location'];
			$info['name'] = $row['name'];
			$info['subject'] = $row['subject'];
			$info['price'] = $row['price'];
			$info['discount'] = $row['discount'];
			$info['expression'] = $row['expression'];
			$info['nDownloads'] = $row['nDownloads'];
			$info['classOnly'] = $row['classOnly'];
			$info['littleDesc'] = $row['littleDesc'];
			$info['byOthers'] = $row['byOthers'];
		}
		
		return $info;
	}
	
	// DELETE SUMMARY
	function deleteSumm($id) {
		if(mysql_query("DELETE FROM summaries WHERE id = '$id' LIMIT 1")) {
			return 0;
		}
		
		return -1;
	}
	
	// GET ALL BOUGHT SUMMARIES
	function getBoughtSumm($userID) {
		$result = mysql_query("SELECT boughtSumm FROM users WHERE id=$userID LIMIT 1");
		$row = mysql_fetch_row($result);
		if(!$row) {
			return -5;
		}
		
		$boughtSumm = explode(";", $row[0]);
		array_pop($boughtSumm);
		if(is_array($boughtSumm))
			return $boughtSumm;
		else
			return array();
	}
	
	// VERIFY IF BOUGHT
	function verifyBought($summId) {
		$userID = $_SESSION['id'];
		$boughtSumm = $this->getBoughtSumm($userID);
		
		if(in_array($summId, $boughtSumm) || $this->isAdmin() || $this->isTeacher()) {
			return 1;
		}
		return -1;
	}
	
	// SET LAST TO DOWNLOAD
	function setLastToDownload($summId) {
		$userID = $_SESSION['id'];
		
		if(!mysql_query("UPDATE users SET last=$summId WHERE id=$userID LIMIT 1")) {
			return -1;
		}
		
		return 1;
	}
	
	// DOWNLOAD STARTER
	function downloadStarter($id) {
		$userID = $_SESSION['id'];
		
		// GET SUMMARY PRICE
		$result = mysql_query("SELECT price, discount FROM summaries WHERE id=$id LIMIT 1");
		$row = mysql_fetch_row($result);
		if(!$row) {
			return -5;
		}
		$discount = $row[1];
		$price = getPercentageNumber($row[0], $row[1]);
		
		// GET NSUMM FROM USER AND VERIFY IF HE HAS AVAILABLE NSUMM FOR THE PRICE
		$result = mysql_query("SELECT nsumm, boughtSumm FROM users WHERE id=$userID LIMIT 1");
		$row = mysql_fetch_row($result);
		$nSumm = $row[0] - $price;
		if($nSumm < 0) {
			return -2;
		}		
		$boughtSumm = $row[1] . $id . ";";
		
		// UPDATE USER NSUMM, LAST AND BOUGHTSUMM
		if(!mysql_query("UPDATE users SET last=$id, nsumm=$nSumm, boughtSumm='$boughtSumm' WHERE id=$userID LIMIT 1")) {
			return -3;
		}
		
		// ADD TO NDOWNLOADS
		$result = mysql_query("SELECT nDownloads FROM summaries WHERE id=$id LIMIT 1");
		$row = mysql_fetch_row($result);
		$nDownloads = $row[0];
		$nDownloads++;
		
		if(!mysql_query("UPDATE summaries SET nDownloads=$nDownloads WHERE id=$id")) {
			return -4;
		}
		
		// RECEIPT
		$receiptTime = "dd/MM/yyyy hh:mm";
		$receiptTime = getDateTime($receiptTime);
		$receiptType = "boughtSummary";
		$boughtNumber = $this->getAllDownloads();
		$receiptInfo = array(
						"summ-id" => $id,
						"price" => $price,
						"discount" => $discount,
						"bought-number" => $boughtNumber);
		$receiptInfo = json_encode($receiptInfo);
		
		$sql = "INSERT INTO receipts (time, person_id, type, info) VALUES ('$receiptTime', '$userID', '$receiptType', '$receiptInfo')";
		
		if(!mysql_query($sql)) {
			return -5;
		}
		
		$receiptID = mysql_insert_id();
		$mailText = "
+------------- Recibo - Incredible Community -------------
| <b>ID do recibo:</b> $receiptID
| <b>Data e hora:</b> $receiptTime
| <b>Compra número:</b> $boughtNumber
+------------------------------------------------------------------------------------
<p>Este recibo é o comprovativo de que comprou um documento na Incredible.
Se tiver problemas, responda a este email enviando todos os dados possíveis (o ID do recibo, a data,
a hora e o seu problema).</p>
Obrigado por usar os nossos serviços.
<p>Este email é enviado automaticamente pelo sistema da Incredible Community. Como isto é um comprovativo,
não pode deixar de o receber.</p>";

		$userInfo = $this->getUserInfo($userID);
		$email = $userInfo["email"];
		
		sendMail($email, $mailText, "Recibo", "receipts", false, true);
		
		return 0;
	}
	
	// GET LAST BOUGHT SUMM ID
	function getLastSummId() {
		$userID = $_SESSION['id'];
		$result = mysql_query("SELECT * FROM users WHERE id=$userID LIMIT 1");
		
		while($row = mysql_fetch_array($result)) {
			return $row['last'];
		}
		
		return 0;
	}
	
	// EDIT SUMMARY
	function editSumm($id, $name, $price, $expression, $subject, $discount, $classOnly, $byOthers, $littleDesc) {
		$query = "UPDATE summaries SET name='$name', subject='$subject', price=$price, discount=$discount, classOnly=$classOnly, byOthers=$byOthers, expression=$expression, littleDesc='$littleDesc' WHERE id=$id LIMIT 1";
		
		if(!mysql_query($query)) {
			return -2;
		}
		
		else {
			return 0;
		}
	}
	
	// SET GLOBAL DISCOUNT
	function setGlobalDiscount($per) {
		$query = "UPDATE summaries SET discount=$per";
		
		if(!mysql_query($query)) {
			return false;
		}
		
		return true;
	}
	
	// SET DISCOUNT FOR SOME SUMMARIES
	function setDiscountForSomeSumm($per, $summIds) {
		foreach ($summIds as $key => $id) {
			$query = "UPDATE summaries SET discount=$per WHERE id=$id";
			if(!mysql_query($query)) {
				return false;
			}
		}
		
		return true;
	}

	// GET SUMMARY ID BY LOCATION
	function getIdByLocation($location) {
		$result = mysql_query("SELECT id FROM summaries WHERE location='$location' LIMIT 1");
		$row = mysql_fetch_row($result);
		return $row[0];
	}
	
	// ADD POST
	function addPost($content) {
		$id = $_SESSION['id'];
		$dateArr = getdate();
		$date = $dateArr['mday'] . "/" . $dateArr['mon'] . "/" . $dateArr['year'];
		if(mysql_query("INSERT INTO posts (content, poster, date) VALUES ('$content', $id, '$date')")) {
			$return[0] = $date;
			$result = mysql_query("SELECT fname, sname FROM users WHERE id=$id");
			$row = mysql_fetch_row($result);
			$return[1] = $row[0] . " " . $row[1];
			$result = mysql_query("SELECT MAX(id) FROM posts WHERE poster=$id LIMIT 1");
			$row = mysql_fetch_row($result);
			$return[2] = $row[0];
			
			return $return;
			
		} else {
			return false;
		}
	}
	
	// GET NEW POSTS
	function getNewPosts($last) {
		$result = mysql_query("SELECT * FROM posts WHERE id>$last ORDER BY id DESC");
		$new = "";
		$count = 0;
		
		while($row = mysql_fetch_array($result)) {
			$posterId = $row['poster'];
			$posterResult = mysql_query("SELECT fname, sname FROM users WHERE id=$posterId");
			$poster = mysql_fetch_row($posterResult);
			$new .= '<div class="post" id="'.$row['id'].'"><h2>'.$poster[0].' '.$poster[1].' <span class="date">'.$row['date'].'</span></h2><div class="content">'.$row['content'].'</div></div>';
			$count += 1;
		}
		
		if($new == "") {
			return "NULL";
		}
		
		else {
			$new.='|--SPLIT--|' . $count;
			return $new;
		}
	}
	
	// GET LAST POSTS
	function loadLastPosts() {
		$result = mysql_query("SELECT * FROM posts ORDER BY id DESC LIMIT 4");
		$new = "";
		
		while($row = mysql_fetch_array($result)) {
			$posterId = $row['poster'];
			$posterResult = mysql_query("SELECT fname, sname FROM users WHERE id=$posterId");
			$poster = mysql_fetch_row($posterResult);
			$name = "";
			
			if(!isset($poster[0])) {
				$name = 'Utilizador não identificado';
			} else {
				$name = $poster[0].' '.$poster[1];
			}
			
			$new .= '<div class="post" id="'.$row['id'].'"><h2>'.$name.' <span class="date">'.$row['date'].'</span></h2><div class="content">'.$row['content'].'</div></div>';
		}
		
		if($new == "") {
			return "NULL";
		}
		
		else {
			return $new;
		}
	}
	
	// LOAD MORE POSTS
	function loadMorePosts($first) {
		$result = mysql_query("SELECT * FROM posts WHERE id<$first ORDER BY id DESC LIMIT 4");
		$new = "";
		
		while($row = mysql_fetch_array($result)) {
			$posterId = $row['poster'];
			$posterResult = mysql_query("SELECT fname, sname FROM users WHERE id=$posterId");
			$poster = mysql_fetch_row($posterResult);
			$new .= '<div class="post" id="'.$row['id'].'"><h2>'.$poster[0].' '.$poster[1].' <span class="date">'.$row['date'].'</span></h2><div class="content">'.$row['content'].'</div></div>';
		}
		
		if($new == "") {
			return "NULL";
		}
		
		else {
			$new .= '<input type="submit" class="button round green image-right ic-arrow-right center" id="morePosts" value="Mais atualizações" />';
			return $new;
		}
	}
	
	// FORGOT CONFIRMATION
	function forgotConfirm($email) {
		$result = mysql_query("SELECT * FROM users WHERE email='".$email."' LIMIT 1");
		$ola = mysql_num_rows($result);
		if(mysql_num_rows($result)==0) {
			return 'Utilizador não encontrado.';
		}
		
		while(true) {
			$rand = rand(10e16, 10e20);
			$rand =  base_convert($rand, 10, 36);
			$sql = "SELECT * FROM users WHERE forgotCode='$rand'";
			$result = mysql_query($sql);
			if(mysql_num_rows($result)==0) {
				$query = "UPDATE users SET forgotCode='$rand' WHERE email='$email' LIMIT 1";
				if(!mysql_query($query)) {
					return 'Erro desconhecido.';
				}
				break;
			}
		}
		
		$emailContent = "
		<html><body>
		
			<p>Alguém tentou repor a password da conta ligada a este email.</p>
			<p>Se quer repor a password, por favor carrege no link abaixo
			<br /> <br />+-------------------------------------------------------------------------------------------
			<br /> | ".URL."register.php?forgotConfirm=".$rand." 
			<br />+-------------------------------------------------------------------------------------------</p>
		
		</body></html>";
		
		sendMail($email, $emailContent, "Recuperação de Password", "recover", false);
		return 0;
	}
	
	// RESET PASSWORD
	function forgot($code) {
		$password = rand(10e16, 10e20) . rand(10e16, 10e20);
		$password =  base_convert($password, 10, 36);
		
		$sql = "SELECT * FROM users WHERE forgotCode='$code' LIMIT 1";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) == 0) {
			return -1;
		}
		
		$sql = "UPDATE users SET forgotCode='', password='$password' WHERE forgotCode='$code' LIMIT 1";
		if(!mysql_query($sql)) {
			return -2;
		} else {
			return $password;
		}
	}
	
	// GET VOUCHERS ARRAY
	function getVouchers() {
		$result = mysql_query("SELECT * FROM vouchers ORDER BY value ASC");
		$count = 0;
		$vouchers = NULL;
		
		while($row = mysql_fetch_array($result)) {
			$vouchers[$count]["id"] = $row["id"];
			$vouchers[$count]["code"] = $row["code"];
			$vouchers[$count]["value"] = $row["value"];
			
			$count++;
		}
		
		return $vouchers;
	}
	
	// DELETE VOUCHER
	function deleteVoucher($id) {
		if(mysql_query("DELETE FROM vouchers WHERE id = '".$id."' LIMIT 1")) {
			return 1;
		}
		
		return -1;
	}
	
	// ADD VOUCHERS
	function addVouchers($quant, $value) {
		$text = "";
		for($i=0; $i<$quant; $i++) {
			// GENERATING CODE
			$count = 0;
			while(true) {
				$code = rand() . rand() . $value;
				$sql = "SELECT * FROM vouchers WHERE code='$code'";
				
				$result = mysql_query($sql);
				if(mysql_num_rows($result)==0) {
					break;
				}
				
				$count++;
				if($count == 5) {
					$text .= "<div class='round err-box'>Não estamos a conseguir gerar um código.</div>";
					return $text;
				}
			}
			
			if(!mysql_query("INSERT INTO vouchers (code, value) VALUES ('$code', $value)")) {
				$text .= "<div class='round err-box'>Não conseguimos adicionar o código $code com o valor $value</div>";
				return $text;
			}
		}
		
		return $text;
	}
	
	// USE VOUCHER
	function useVoucher($VCode) {
		$sql = "SELECT * FROM vouchers WHERE code='$VCode' LIMIT 1";
		$result = mysql_query($sql);
		if(mysql_num_rows($result) == 0) {
			return -1;
		}
		
		while($row = mysql_fetch_array($result)) {
			$userId = $_SESSION['id'];
			$value = $row['value'];
			
			$sql = "SELECT nsumm, email FROM users WHERE id=$userId LIMIT 1";
			$uResult = mysql_query($sql);
			$nsummRow = mysql_fetch_row($uResult);
			$nsumm = $nsummRow[0];
			$nsumm += $value;
			$email = $nsummRow[1];
			$sql = "DELETE FROM vouchers WHERE code='$VCode' LIMIT 1";
			if(!mysql_query($sql)) {
				return -2;
			}
			
			$sql = "UPDATE users SET nsumm=$nsumm WHERE id=$userId";
			if(!mysql_query($sql)) {
				return -2;
			} 
			
			// RECEIPT
			$format = "dd/MM/yyyy hh:mm";
			$receiptTime = getDateTime($format);
			$receiptType = "usedVoucher";
			$receiptInfo = array(
							"voucher-value" => $value,
							"voucher-code" => $VCode,
							"voucher-id" => $row['id']);
			$receiptInfo = json_encode($receiptInfo);
			
			$sql = "INSERT INTO receipts (time, person_id, type, info) VALUES ('$receiptTime', '$userId', '$receiptType', '$receiptInfo')";
			
			if(!mysql_query($sql)) {
				return -2;
			}
			
			$receiptID = mysql_insert_id();
			$mailText = "
+------------- Recibo - Incredible Community -------------<br />
| <b>ID do recibo:</b> $receiptID<br />
| <b>Data e hora:</b> $receiptTime<br />
| <b>Código do voucher:</b> $VCode<br />
| <b>Valor do voucher:</b> $value euros<br />
+---------------------------------------------------------------
<p>Este recibo é o comprovativo de que carregou o seu saldo com $value euros na Incredible.<br />
Se tiver problemas, responda a este email enviando todos os dados possíveis (o ID do recibo, a data<br />
e a hora e o seu problema).</p><br />
Obrigado por usar os nossos serviços.<br />
<p>Este email é enviado automaticamente pelo sistema da Incredible Community. Como isto é um comprovativo,
não pode deixar de o receber.</p>";
			
			sendMail($email, $mailText, "Recibo", "receipts", false, false);
			
			return $value;
		}
		
	}
	
	// GET TOTAL DOWNLOADS
	function getAllDownloads() {
		$sql = "SELECT nDownloads FROM summaries";
		$result = mysql_query($sql);
		$downloads = 0;
		
		while($row = mysql_fetch_array($result)) {
			$downloads += $row['nDownloads'];
		}
		
		return $downloads;
	}
	
	// GET ALL USERS THAT BOUGHT ONE SUMMARY
	function getAllUsersWhoBought($summId) {
		$sql = "SELECT id, email, fname, sname FROM users";
		$result = mysql_query($sql);
		$users = array();
		$count = 0;
		
		while($row = mysql_fetch_array($result)) {
			$boughtSumm = $this->getBoughtSumm($row['id']);
			
			if(in_array($summId, $boughtSumm)) {
				$users[$count]['fname'] = $row['fname'];
				$users[$count]['sname'] = $row['sname'];
				$users[$count]['email'] = $row['email'];
				$users[$count]['subscribed'] = $row['subscribed'];
				$count++;
			}
		}
		
		return $users;
	}
	
	// SEARCH PERSONS BY NAME
	function searchPersonsName($q) {
		$q = fullUpper($q);
		$sql = "SELECT * FROM users WHERE upper(fname)='$q' OR upper(sname)='$q' OR upper(nick)='$q' LIMIT 5";
		
		if(!$result=mysql_query($sql)) {
			return false;
		}
		
		$returnArray = array();
		$count = 0;
		while($row = mysql_fetch_array($result)) {
			$returnArray[$count]['id'] = $row['id'];
			$count++;
		}
		
		return $returnArray;
	}
	
	// SEARCH RECEIPTS BY ID OR PERSON
	function searchReceipts($q) {
		//RETURNING FOR IDS
		$sql = "SELECT * FROM receipts WHERE id='$q'";
		
		if(!$result=mysql_query($sql)) {
			return false;
		}
		
		$returnArray = array();
		$count = 0;
		while($row = mysql_fetch_array($result)) {
			$pName = $this->getUserInfo($row['person_id']);
			$pName = $pName["fname"]." ".$pName["sname"];
			$returnArray[$count] = array(
					'id' => $row['id'],
					'time' => $row['time'],
					'who' => "<a href='/manage.php?func=edit&id=".$row["person_id"]."'>".$pName."</a>",
					'type' => $row['type'],
					'info' => json_decode($row['info'], true));
					
			if($returnArray[$count]["type"] == "boughtSummary") {
				$sName = $this->getSummaryInfo($returnArray[$count]["info"]["summ-id"]);
				if($sName['expression'] == 1) {
					$sName = explode(".", $sName['name']);
					$sName = ucwords($sName[0]).' - '.$sName[1].'º Ano, '.$sName[2].'º Período';
				} else {
					$sName = ucfirst($sName['name']);
				}
				$returnArray[$count]["info"]["summ-id"] = "<a href='/summaries.php?func=download&id=".$returnArray[$count]['info']['summ-id']."'>".$sName."</a>";
			}
			$count++;
		}
		
		// RETURNING FOR PERSONS
		$persons = $this->searchPersonsName($q);
		if(!empty($persons) && $persons != false) {
			for($i=0; $i<count($persons); $i++) {
				$personId = $persons[$i]['id'];
				$sql = "SELECT * FROM receipts WHERE person_id=$personId ORDER BY id DESC LIMIT 5";
				
				if(!$result=mysql_query($sql)) {
					echo mysql_error();
					return false;
				}
				
				while($row = mysql_fetch_array($result)) {
					$pName = $this->getUserInfo($row['person_id']);
					$pName = $pName["fname"]." ".$pName["sname"];
					$returnArray[$count] = array(
							'id' => $row['id'],
							'time' => $row['time'],
							'who' => "<a href='/manage.php?func=edit&id=".$row["person_id"]."'>".$pName."</a>",
							'type' => $row['type'],
							'info' => json_decode($row['info'], true));
							
					if($returnArray[$count]["type"] == "boughtSummary") {
						$sName = $this->getSummaryInfo($returnArray[$count]["info"]["summ-id"]);
						if($sName['expression'] == 1) {
							$sName = explode(".", $sName['name']);
							$sName = ucwords($sName[0]).' - '.$sName[1].'º Ano, '.$sName[2].'º Período';
						} else {
							$sName = ucfirst($sName['name']);
						}
						$returnArray[$count]["info"]["summ-id"] = "<a href='/summaries.php?func=download&id=".$returnArray[$count]['info']['summ-id']."'>".$sName."</a>";
					}
					$count++;
				}
			}
		}
		return $returnArray;
	}
	
	// GET ALL RECEIPTS
	function getLastReceipts($numberOf) {
		$sql = "SELECT * FROM receipts ORDER BY id DESC LIMIT $numberOf";
		if(!$result=mysql_query($sql)) {
			return false;
		}
		
		$returnArray = array();
		$count = 0;
		while($row = mysql_fetch_array($result)) {
			$pName = $this->getUserInfo($row['person_id']);
			$pName = $pName["fname"]." ".$pName["sname"];
			$returnArray[$count] = array(
					'id' => $row['id'],
					'time' => $row['time'],
					'who' => "<a href='/manage.php?func=edit&id=".$row["person_id"]."'>".$pName."</a>",
					'type' => $row['type'],
					'info' => json_decode($row['info'], true));
			$count++;
		}
		
		return $returnArray;
	}
	
	// SUBSCRIBE TO THE EMAIL LIST
	function subEmail() {
		$userID = $_SESSION['id'];
		$sql = "UPDATE users SET subscribed = 1 WHERE id = $userID";
		
		if(mysql_query($sql)) {
			return 0;
		}
		
		return -1;
	}
	
	// UNSUBSCRIBE FROM THE EMAIL LIST
	function unsubEmail() {
		$userID = $_SESSION['id'];
		$sql = "UPDATE users SET subscribed = 0 WHERE id = $userID";
		
		if(mysql_query($sql)) {
			return 0;
		}
		
		return -1;
	}

	// Verify non-activated accounts
	function verifyNonActivated() {
		$nowDate = getDateTime("dd-MM-yyyy");
		$sql = "SELECT * FROM users WHERE activated=0";

		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)) {
			$registerDate = str_replace("/", "-", $row['lastDate']);
			$diff = abs(strtotime($nowDate) - strtotime($registerDate));

			if(floor($diff/(24*60*60))>=7) {
				$id=$row['id'];
				echo "Deleted user with id $id.<br/>";
				$sql = "DELETE FROM users WHERE id=$id LIMIT 1";
				if(!mysql_query($sql)) {
					Echo "Error deleteing user with id $id!<br/>";
					return;
				}
			}
		}
	}
}