<?php

	ob_start();

	require_once '../function/constants.php';

	require_once '../function/configClass.php';

	require_once '../function/siteFunctions.php';


	
	$productID = $_REQUEST['product_id'];
	$type = $_REQUEST['type'];
	$ID = $_REQUEST['ID'];
	if($type=="images"){
		if(file_exists(SITE_BASE_PATH.$_SESSION['CART'][$productID]['customData']['filesImages'][$ID])){
			unlink(SITE_BASE_PATH.$_SESSION['CART'][$productID]['customData']['filesImages'][$ID]);	
		}
			unset($_SESSION['CART'][$productID]['customData']['filesImages'][$ID]);	
	}else{
		if(file_exists(SITE_BASE_PATH.$_SESSION['CART'][$productID]['customData'][$type])){
			unlink(SITE_BASE_PATH.$_SESSION['CART'][$productID]['customData'][$type]);	
		}
			unset($_SESSION['CART'][$productID]['customData'][$type]);
	}

	echo json_encode($myarray);
?>