<?php

require_once 'Mysql.php';

require_once 'functions.php';



class SummaryManager {

	private $mysql;

	

	// CONSTRUCTOR

	function __construct() {

		$this->mysql = new Mysql();

	}

	

	// ADD A NEW SUMMARY TO THE DATABASE

	function addSummary($name, $price, $expression, $ext, $subject, $classOnly, $byOthers, $littleDesc) {

		$randName = md5(rand() . rand() . rand()) . "." . $ext;

		$directory = "summaries/";

		$text ="";

		

		$subjects = array('mat', 'gf', 'his', 'cfq', 'cn', 'ing', 'fra', 'pt', 'other');

		if(!in_array($subject, $subjects)) {

			$text .= '<p>Erro ao criar</p>';

			return $text;

		}

		

		$price = comma2Point($price);

		$price = (is_numeric($price) ? $price : -1);

		if($price < 0) {

			$text .= "<p>O preço não era válido!</p>";

			return $text;

		}

		

		$littleDesc = strip($littleDesc);



		while(true)

		{

			if(file_exists($directory . $randName))

			{

				$randName = md5(rand() . rand() . rand()) . "." . $ext;

			} 

			

			else 

			{

				$location = $directory . $randName;

				break;

			}

		}

		

		$text .= "<p>Nome original: " . $_FILES["file"]["name"] . "</p>";

		$text .= "<p>Tipo: " . $_FILES["file"]["type"] . "</p>";

		$text .= "<p>Tamanho: " . ($_FILES["file"]["size"] / 1024 / 1024) . " Mb</p>";

		$text .= "<p>Ficheiro Temporário: " . $_FILES["file"]["tmp_name"] . "</p>";

		

		if($this->mysql->addSummary($location, $price, $expression, $name, $subject, $classOnly, $byOthers, $littleDesc) == 0)

		{

			$this->encryptSummary($_FILES["file"]["tmp_name"], $location, md5('iNoXRULESFUCKYEAH!sodhf9aeyfq30f'));

			$text .= "<br /><p>Guardado em: " . basename($location) . "</p>";

			$text .= "<p>Preço: " . $price . " euros</p>";

			$dom = new DOMDocument();

			$dom->load(HOME."/news.xml");

			$root=$dom->documentElement;

			$ori = $root->childNodes->item(0);

			$type = $dom->createElement("type");

			$typeText = $dom->createTextNode("Resumo no site");

			$type->appendChild($typeText);

			$id = $dom->createElement("id");

			$idText = $dom->createTextNode($this->getIdByLocation($location));

			$id->appendChild($idText);

			$date = $dom->createElement("date");

			$dateText = $dom->createTextNode(getDateTime("dd/MM/yyyy hh:mm"));

			$date->appendChild($dateText);

			

			$article = $dom->createElement('article');

			$article->appendChild($type);

			$article->appendChild($id);

			$article->appendChild($date);

			$root->insertBefore($article, $ori);

			

			$articles=$root->getElementsByTagName('article');

			foreach ($articles as $article) {

				$articleToRemove = $article;

			}

			$root->removeChild($articleToRemove);

			$dom->save(HOME."/news.xml");



		}

		else

		{

			$text .= "<p>Não conseguimos criar registo na base de dados, possivelmente por já haver um ficheiro com o nome escolhido igual.</p>";

		}

		

		return $text;

	}

	

	// GET ALL SUMMARIES TO THE UPLOAD PAGE

	function getSummaries() {

		$boughtSumm = $this->mysql->getBoughtSumm($_SESSION['id']);

		$summ = $this->mysql->getSummaries();

		$text = "";

		

		if($summ == 0)

		{

			$text .= "Não há documentos para mostrar.";

			return $text;

		}

		else

		{

			

			if($this->mysql->isAdmin()==1) {

				$text .= "<table class='summ' id='summaries'>

						<thead>

						<tr>

						<th class='check'></th>

						<th class='id'>ID</th>

						<th>Nome</th>

						<th>Preço</th>

						<th>Nº de Compras</th>

						<th>Ações</th>

						</tr>

						</thead>

						<tbody>";

				

					for($i=0;$i<count($summ);$i++)

					{

						if( $summ[$i]['classOnly'] == 0 || ($summ[$i]['classOnly'] == 1 && $this->mysql->isFromClass()) ) {

							if($summ[$i]['expression'] == 1) {

								$name = explode(".", $summ[$i]['name']);

								$name = ucwords($name[0]).' - '.$name[1].'º Ano, '.$name[2].'º Período';

							} else {

								$name = ucfirst($summ[$i]['name']);

							}

							$text .= '<tr';

							if(in_array($summ[$i]["id"], $boughtSumm)) {

								$text .= ' class="t-green '.$summ[$i]['subject'].'"';

							} else if($summ[$i]['discount'] > 0) {

								$text .= ' class="t-yellow '.$summ[$i]['subject'].'"';

							} else {

								$text .= ' class="'.$summ[$i]['subject'].'"';

							}

							$text .= '>';

							

							$text .= "<td class='check'><input type='checkbox' value='".$summ[$i]['id']."' class='summChecker'></td>";

							$text .= "<td class='id'>".$summ[$i]['id']."</td>";

							if($summ[$i]['littleDesc'] != "") {

								$text .= '<td><div class="arrow"></div><span class="title">'.$name.'</span><div class="desc">'.nl2br($summ[$i]['littleDesc']).'</div>';

								if($summ[$i]["byOthers"]){

									$text .= '<div class="byOthers"><div class="no-arrow"></div>Este documento não foi escrito pela Incredible</div>';

								}

								$text .= '</td>';

							}

							else {

								$text .= '<td><span class="title margined">'.$name.'</span>';



								if($summ[$i]["byOthers"]){

									$text .= '<div class="byOthers"><div class="no-arrow"></div>Este documento não foi escrito pela Incredible</div>';

								}

								

								$text .= '</td>';

							}

							if($summ[$i]['price'] != 0) {

								$text .= '<td>'.number_format(getPercentageNumber($summ[$i]['price'], $summ[$i]['discount']), 2).'€</td>';

							} else {

								$text .= '<td>Grátis!</td>';

							}

							$text .= '<td>'.$summ[$i]['nDownloads'].'</td>';

							

							$text .= "<td>	<a title='Download' href='/summaries.php?func=download&id=".$summ[$i]['id']."' class='table-actions-button ic-table-down'></a>

											<a href='/summManagement.php?func=edit&id=".$summ[$i]['id']."' class='table-actions-button ic-table-edit'></a>

											<a href='/summManagement.php?func=delete&id=".$summ[$i]['id']."' class='table-actions-button ic-table-delete'></a></td>";

							

							$text .= '</tr>';

						}

					}

			

				$text .= '</tbody></table><div class="round confirm-box">Os documentos que aparecem a verde, são os que foram comprados por si.</div>';

				return $text;

			} else {

				$text .= "<table class='notAdmin' id='summaries'>

						<thead>

						<tr>

						<th>Nome</th>

						<th>Preço</th>

						<th>Nº de Compras</th>

						<th>Ações</th>

						</tr>

						</thead>

						<tbody>";

				

					for($i=0;$i<count($summ);$i++)

					{

						if( $summ[$i]['classOnly'] == 0 || ($summ[$i]['classOnly'] == 1 && $this->mysql->isFromClass()) ) {

							if($summ[$i]['expression'] == 1) {

								$name = explode(".", $summ[$i]['name']);

								$name = ucwords($name[0]).' - '.$name[1].'º Ano, '.$name[2].'º Período';

							} else {

								$name = ucfirst($summ[$i]['name']);

							}

							$text .= '<tr';

							if(in_array($summ[$i]["id"], $boughtSumm)) {

								$text .= ' class="t-green '.$summ[$i]['subject'].'"';

							} else if($summ[$i]['discount'] > 0) {

								$text .= ' class="t-yellow '.$summ[$i]['subject'].'"';

							} else {

								$text .= ' class="'.$summ[$i]['subject'].'"';

							}

							$text .= '>';

							if($summ[$i]['littleDesc'] != "") {

								$text .= '<td><div class="arrow"></div><span class="title">'.$name.'</span><div class="desc">'.nl2br($summ[$i]['littleDesc']).'</div>';

								if($summ[$i]["byOthers"]){

									$text .= '<div class="byOthers"><div class="no-arrow"></div>Este documento não foi escrito pela Incredible</div>';

								}

								$text .= '</td>';

							}

							else {

								$text .= '<td><span class="title margined">'.$name.'</span>';



								if($summ[$i]["byOthers"]){

									$text .= '<div class="byOthers"><div class="no-arrow"></div>Este documento não foi escrito pela Incredible</div>';

								}



								$text .= '</td>';

							}

							if($summ[$i]['price'] != 0) {

								$text .= '<td>'.number_format(getPercentageNumber($summ[$i]['price'], $summ[$i]['discount']), 2).'€</td>';

							} else {

								$text .= '<td>Grátis!</td>';

							}

							$text .= '<td>'.$summ[$i]['nDownloads'].'</td>';

							$text .= "<td>	<a title='";

							if(in_array($summ[$i]["id"], $boughtSumm)) {

								$text .= "Download";

							} else {

								$text .= "Comprar";

							}

							$text .= "' href='/summaries.php?func=download&id=".$summ[$i]['id']."' class='table-actions-button ic-table-down'></a></td>";

							

							$text .= '</tr>';

						}

					}

			

				$text .= '</tbody></table><div class="round confirm-box">Os documentos que aparecem a verde, são os que foram comprados por si.</div>';

				return $text;

			}

		}

		

	}

	

	// GET SUMMARY INFO

	function getInfo($id) {

		$id = strip($id);

		$id = (is_numeric($id) ? (int)$id : -1);

		if($id < 0) {

			return -1;

		}

		

		return $this->mysql->getSummaryInfo($id);

	}

	

	// DELETE SUMMARY

	function delete($id) {

		$id = strip($id);

		$id = (is_numeric($id) ? (int)$id : -1);

		if($id < 0) {

			return -1;

		}

		

		$summ = $this->getInfo($id);

		$location = $summ['location'];

		

		if(!unlink($location)) {

			//return -1;

		}

		

		if($this->mysql->deleteSumm($id) == -1) {

			return -2;

		}

		

		return 0;

	}

	

	// VERIFY IF BOUGHT

	function verifyBought($summId) {

		$summId = strip($summId);

		$summId = (is_numeric($summId) ? (int)$summId : -1);

		if($summId < 0) {

			return -2;

		}

		

		$info = $this->getInfo($summId);

		$location = HOME.$info['location'];

		

		if(is_file($location)) {

			return $this->mysql->verifyBought($summId);

			

		} else {

			return -3;

		}

	}

	

	// SET LAST TO DOWNLOAD

	function setLastToDownload($summId) {

		$summId = strip($summId);

		$summId = (is_numeric($summId) ? (int)$summId : -1);

		if($summId < 0) {

			return -2;

		}

		

		return $this->mysql->setLastToDownload($summId);

	}

	

	// DOWNLOAD STARTER

	function downloadStarter($id) {

		$id = strip($id);

		$id = (is_numeric($id) ? (int)$id : -1);

		if($id < 0) {

			return -1;

		}

		

		$info = $this->getInfo($id);

		$location = HOME.$info['location'];

		

		if (is_file($location)) {

			return $this->mysql->downloadStarter($id);

			

		} else {

			return -1;

		}

	}

	

	// DOWNLOAD LAST

	function downloadLast($location) {

		$id = $this->mysql->getLastSummId();

		$info = $this->getInfo($id);

		if(!$this->decryptSummary($info['location'],$location, md5('iNoXRULESFUCKYEAH!sodhf9aeyfq30f'))) {

			return -3;

		}

		if($info['expression'] == 1) {

			$name = explode('.', $info['name']);

			$name = ucwords($name[0]). " - " . $name[1] . "º Ano, " . $name[2] . "º Período";

		} else {

			$name = ucfirst($info['name']);

		}

		

		if ($fd = fopen ($location, "r")) {

			$fsize = filesize($location);

			

			$pathParts = pathinfo($location);

   			$ext = strtolower($pathParts["extension"]);

			

			switch ($ext) {

				case "pdf":

					header("Content-type: application/pdf");

					header("Content-Disposition: attachment; filename=\"$name.$ext\"");

					break;

				default;

					header("Content-type: application/octet-stream");

					header("Content-Disposition: filename=\"$name.$ext\"");

			}

			

			header("Content-length: $fsize");

			header("Cache-control: private");

			while(!feof($fd)) {

				$buffer = fread($fd, 2048);

				echo $buffer;

			}

			fclose($fd);

			return 0;

			

		} else {

			fclose($fd);

			$text = -1;

		}

		

		return $text;

	}

	

	// EDIT SUMMARY

	function edit($id, $name, $price, $editedCont, $subject, $discount, $classOnly, $littleDesc, $byOthers, $silent) {

		// VERIFY NAME

		if(preg_match("/[a-zA-Zã-û]+\.[7-9]\.[1-3]/", $name)) {

			$expression = 1;

		} else {

			$expression = 0;

		}





		$price = comma2Point($price);		

		$price = (is_numeric($price) ? $price : -1);

		if($price < 0) {

			return -1;

		}

		

		$name = strip($name);

		$littleDesc = strip($littleDesc);

		

		$discountList = Array(0, 25, 50, 75);

		$discount = $discountList[$discount];

		

		$returned = $this->mysql->editSumm($id, $name, $price, $expression, $subject, $discount, $classOnly, $byOthers, $littleDesc);



		if($returned != 0) {

			return $returned;

		}



		if($editedCont && isset($_FILES['file'])) {

			$summinfo = $this->getInfo($id);

			$this->encryptSummary($_FILES["file"]["tmp_name"], $summinfo['location'], md5('iNoXRULESFUCKYEAH!sodhf9aeyfq30f'));

			$usersWhoBought = $this->mysql->getAllUsersWhoBought($id);

			

			if(!empty($usersWhoBought) && !$silent) {

				$to = "";

				for($i = 0; $i<count($usersWhoBought); $i++) {

					if($usersWhoBought[$i]['subscribed'] == 1)

						$to .= $usersWhoBought[$i]['email'].", ";

				}

				

				$summInfo = $this->getInfo($id);

				if($summInfo['expression'] == 1) {

					$summName = explode(".", $summInfo['name']);

				} else {

					$summName = $summInfo['name'];

				}

				

				if(is_array($summName)) {

					$summName = ucwords($name[0]).' - '.$name[1].'º Ano, '.$name[2].'º Período';

				} else {

					$summName = ucfirst($name);

				}

				

				$msg = "

		<html><body>

		

			<p>Um documento que comprou na Incredible Commmunity foi revisto.</p>

			<p>O documento chama-se $summName.

			<br />Por favor, vá à Incredible e volte a fazer o download do documento para ter a nova versão.</p>

			<br />

			<p>Este email é enviado automaticamente pelo sistema da Incredible Community. Se quiser deixar de receber estes emails, vá às definições da sua conta e carregue no link em baixo de tudo.</p>

		

		</body></html>";

				

				

				sendMail($to, $msg, 'Edição de Documento', 'documentos', false);

				

			}

			

			if(!$silent && !empty($_FILES['file']['tmp_name'])) {

				$dom = new DOMDocument();

				$dom->load(HOME."/news.xml");

				$root=$dom->documentElement;

				$ori = $root->childNodes->item(0);

				$type = $dom->createElement("type");

				$typeText = $dom->createTextNode("Resumo revisto");

				$type->appendChild($typeText);

				$idDom = $dom->createElement("id");

				$idDomText = $dom->createTextNode($id);

				$idDom->appendChild($idDomText);

				$date = $dom->createElement("date");

				$dateText = $dom->createTextNode(getDateTime("dd/MM/yyyy hh:mm"));

				$date->appendChild($dateText);

				

				$article = $dom->createElement('article');

				$article->appendChild($type);

				$article->appendChild($idDom);

				$article->appendChild($date);

				$root->insertBefore($article, $ori);

				

				$articles=$root->getElementsByTagName('article');

				foreach ($articles as $article) {

					$articleToRemove = $article;

				}

				$root->removeChild($articleToRemove);

				$dom->save(HOME."/news.xml");

			}

		}

		

		return $returned;

	}

	

	// GET TOTAL DOWNLOADS

	function getAllDownloads() {

		$downloads = $this->mysql->getAllDownloads();

		

		return $downloads;

	}

	

	// GET NEWS

	function getNews() {

		$dom=new DOMDocument();

		$dom->load(HOME."/news.xml");

		$root=$dom->documentElement;

		$articles=$root->getElementsByTagName('article');

		

		$text = "";

		$count = 0;

		

		// Loop trough childNodes

		foreach ($articles as $article) {

			$name = "";

			

			$id = $article->getElementsByTagName('id')->item(0)->textContent;

			$summInfo = $this->getInfo($id);

			

			if(is_array($summInfo) && ($summInfo['classOnly'] == 0 || ($summInfo['classOnly'] == 1 && $this->mysql->isFromClass()) )) {

				if($summInfo['expression'] == 1) {

					$name = explode(".", $summInfo['name']);

					$name = ucfirst($name[0]).' - '.$name[1].'º Ano, '.$name[2].'º Período';

				} else {

					$name = ucfirst($summInfo['name']);

				}

				$type = $article->getElementsByTagName('type')->item(0)->textContent;

				$date = $article->getElementsByTagName('date')->item(0)->textContent;

				$bought = $this->verifyBought($id);

				

				$text .= '	<li><a class="newsSummType" href="/summaries.php?func=download&id='.$id.'">

							<b>'.$type.' <span style="color: #757474; font-size: 0.75em;">'.$date.'</span></b><br />

							<span class="newsSummName">'.$name.'</span>';

							

				if($bought == 1 || $summInfo['price'] == 0) {

					$text .= '<br /><span style="color: #4d7515;">Faz o download de graça!</span>';

				} else {

					$text .= '<br /><span style="color: #b28a0b;">Custa '.$summInfo['price'].'€</span>';

				}

				

				$text .= '</a></li>';

			}

		}

		

		if($text == "") {

			return '<li><p style="padding: 1em; background-color: white;">Não há novidades para mostrar</p></li>';

		}

		

		return $text;

	}

	

	// GET SUMMARY ID BY LOCATION

	function getIdByLocation($location) {

		$location = strip($location);

		return $this->mysql->getIdByLocation($location);

	}

	

	//---------* ENCRYPTION THINGS *--------------------------------------------------------------

	function encryptSummary($original, $detination, $password) {

		if(file_exists($detination)) {

			unlink($detination);

		}

		

		//check the file if exists

		if (file_exists($original)){

	 

			//get file content as string

			$InFile = file_get_contents($original);

	 

				// get string length

				$StrLen = strlen($InFile);

	 

				// get string char by char

				for ($i = 0; $i < $StrLen ; $i++){

					//current char

					$chr = substr($InFile,$i,1);

	 

					//get password char by char

					$modulus = $i % strlen($password);

					$passwordchr = substr($password,$modulus, 1);

	 

					//encryption algorithm

					$OutFile .= chr(ord($chr)+ord($passwordchr));

				}

	 

			$OutFile = base64_encode($OutFile);

	 

			//write to a new file

			if($newfile = fopen($detination, "c")){

				file_put_contents($detination,$OutFile);

				fclose($newfile);

				return true;

			}else{

				return false;

			}

		}else{

			return false;

		}

	}

	

	function decryptSummary($InFileName,$OutFileName,$password){

		//check the file if exists

		if (file_exists($InFileName)){

	 

			//get file content as string

			$InFile = file_get_contents($InFileName);

			$InFile = base64_decode($InFile);

				// get string length

				$StrLen = strlen($InFile);

	 

				// get string char by char

				for ($i = 0; $i < $StrLen ; $i++){

					//current char

					$chr = substr($InFile,$i,1);

	 

					//get password char by char

					$modulus = $i % strlen($password);

					$passwordchr = substr($password,$modulus, 1);

	 

					//encryption algorithm

					$OutFile .= chr(ord($chr)-ord($passwordchr));

				}

	 

			//write to a new file

			if($newfile = fopen($OutFileName, "c")){

				file_put_contents($OutFileName,$OutFile);

				fclose($newfile);

				return true;

			}else{

				return false;

			}

		}else{

			return false;

		}

	}

	

}