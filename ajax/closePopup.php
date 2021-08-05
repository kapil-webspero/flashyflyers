<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	$status = "error";
	setcookie("FlashyOfferPopup", "Yes", time() + 86400, '/');
	
	echo json_encode($myarray);
?>