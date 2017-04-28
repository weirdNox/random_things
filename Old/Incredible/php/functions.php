<?php

// STRIPS EVERYTHING
function strip($str) {
	$str = trim($str);
	//$str = strip_tags($str);
	$str = mysql_real_escape_string($str);
	
	return $str;
}

// UNSTRIPS
function unstrip($str) {
	$str = stripslashes($str);
	
	return $str;
}

// COMMA TO POINT - 0,1 to 0.1
function comma2Point($val)
{
	return str_replace(",", ".", $val);
}


function sendMail($to, $message, $subject = "", $from = '', $noreply = false, $nl2br = false) {
	// TO
	$to = strip($to);
	
	// SUBJECT
	if(empty($subject))
		$subject = 'Incredible Community';
	else
		$subject = unstrip($subject);
		
	// FROM
	if($noreply == true) {
		$from = 'NoReply';
	} else if(empty($from)) {
		$from = 'info';
	}
	
	// Headers
	$headers = "";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	
	$headers .= 'From: Incredible Community ';
	if($noreply)
		$headers .= 'No-Reply ';
	$headers .= '<';
	$headers .= $from;
	$headers .= '@'.HOST.'>' . "\r\n";
	if(!$noreply)
		$headers .= "Reply-To: info@".HOST;
	
	if($nl2br)
		$message = nl2br($message);
		
	// SEND MAIL
	if(mail($to, $subject, $message, $headers))
		return true;
	else
		return false;
}

function getRandom($length, $withNumbers, $withLetters) {
	$characters = "";
	if($withNumbers)
		$characters .= "0123456789";
	if($withLetters)
    	$characters .= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	
    $string = "";    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}

function getDateTime($format) {
	date_default_timezone_set('Europe/Lisbon');
	$dateTime = str_replace("dd", date("d"), $format);
	$dateTime = str_replace("MM", date("m"), $dateTime);
	$dateTime = str_replace("yyyy", date("o"), $dateTime);
	$dateTime = str_replace("hh", date("H"), $dateTime);
	$dateTime = str_replace("mm", date("i"), $dateTime);
	
	return $dateTime;
}
	
// GET PERCENTAGE OF A NUMBER
function getPercentageNumber($number, $per) {
	$per = ((int)$per);
	if($per == 0) {
		return $number;
	}
	
	$per = (100 - $per)/100;
	return $number*$per;
}

// PROTOTYPE OF FUNCTION - IGNORE
function isInt($num, $convert=true) {
	$num2 = is_numeric($num)?((int)$num):false;
	if($num2 === false) {
		return false;
	} else if($convert===false && $num2 != $num) {
		return false;
	}
	
	return true;
}

// TO UPPER WITH SPECIAL CHARACTERS
function fullUpper($string){ 
  return strtr(strtoupper($string), array( 
      "à" => "À", 
      "è" => "È", 
      "ì" => "Ì", 
      "ò" => "Ò", 
      "ù" => "Ù", 
      "á" => "Á", 
      "é" => "É", 
      "í" => "Í", 
      "ó" => "Ó", 
      "ú" => "Ú", 
	  "â" => "Â", 
      "ê" => "Ê", 
      "î" => "Î", 
      "ô" => "Ô", 
      "û" => "Û", 
	  "ç" => "Ç", 
    )); 
}

// GET IP
function getIp() {
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		return $_SERVER['HTTP_CLIENT_IP'];
	}
	
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	}

	else {
		return $_SERVER['REMOTE_ADDR'];
	}
}