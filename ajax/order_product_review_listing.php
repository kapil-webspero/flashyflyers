<?php
    ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';


	extract($_REQUEST);
	
		global $reviewStatus;
	
		$html = getReviewHtmlByProductIDOrderID($_REQUEST['order_id'],$_REQUEST['productID']);
	   
	echo json_encode(array( "html"=>$html));
?>