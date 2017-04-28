<?php
require_once 'Mysql.php';
require_once 'functions.php';
$mysql = new Mysql();

if(isset($_POST['content']) && !empty($_POST['content']) && $_SESSION['cpost'] == 1) {
	$content = strip(nl2br($_POST['content']));
	$content = ucfirst($content);

	$returned = $mysql->addPost($content);
	
	if(is_array($returned)) {
		echo $returned[0].'/-split-/'.$returned[1].'/-split-/'.$returned[2];
	} else {
		echo '-1';
	}
}

else if(isset($_POST['lastPostId']) && !empty($_POST['lastPostId'])) {
	$last = strip($_POST['lastPostId']);
	
	$returned = $mysql->getNewPosts($last);
	echo unstrip($returned);
}

else if(isset($_POST['loadLastPosts']) && $_POST['loadLastPosts']==='yes') {
	$returned = $mysql->loadLastPosts();
	echo unstrip($returned);
}

else if(isset($_POST['firstId']) && !empty($_POST['firstId'])) {
	$id = strip($_POST['firstId']);
	
	$returned = $mysql->loadMorePosts($id);
	echo unstrip($returned);
}

else if(isset($_POST['per'])) {
	header('Content-Type: application/json');
	$per = strip($_POST['per']);
	//if(isInt($per, false) === false) {
	//	echo '{success: false}';
	//	return;
	//}
	
	if(!$mysql->setGlobalDiscount($per)) {
		echo '{"success": false}';
		return;
	}
	
	echo '{"success": true}';
	return;
}

else if(isset($_POST['func']) && $_POST['func']=='discountSomeSumm' && isset($_POST['summIds']) && isset($_POST['percentage'])) {
	header('Content-Type: application/json');
	$per = strip($_POST['percentage']);
	$summIds = $_POST['summIds'];
	foreach ($summIds as $key => $value) {
		$summIds[$key] = strip($value);
	}

	if(!$mysql->setDiscountForSomeSumm($per, $summIds)) {
		echo '{"success": false}';
		return;
	}
	
	echo '{"success": true}';
	return;
}

else if(isset($_POST['func']) && $_POST['func'] == "search-receipts" && isset($_POST['q']) && !empty($_POST['q'])) {
	header('Content-Type: application/json');
	$q = strip($_POST['q']);
	
	$results = $mysql->searchReceipts($q);
	if($results === false) {
		echo '{"success": false}';
		return;
	}
	
	echo '{"success": true, "results": '.json_encode($results).'}';
	return;
}

else if(isset($_POST['subscribe']) && $_POST['subscribe'] == true) {
	header('Content-Type: application/json');
	
	if($mysql->subEmail() == 0) {
		echo '{"success": true}';
		return;
	}
	echo '{"success": false}';
	return;
}

else if(isset($_POST['unsubscribe']) && $_POST['unsubscribe'] == true) {
	header('Content-Type: application/json');
	
	if($mysql->unsubEmail() == 0) {
		echo '{"success": true}';
		return;
	}
	echo '{"success": false}';
	return;
}

else {
	header('location:../errors/404.php');
}