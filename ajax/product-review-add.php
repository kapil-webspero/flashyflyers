<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	
	parse_str($_POST['data'], $postData);
	
	if( $postData['product_rating']>0 && $postData['ReviewProductID']> 0 && $postData['product_review_comment']!=""){
		$fn = array("ReviewUserID", "ReviewProductID",  "Rating", "ReviewDescription", "ReviewStatus","ReviewDate","OrderID");
		$fv = array($_SESSION['userId'], $postData['ReviewProductID'], $postData['product_rating'],addslashes($postData['product_review_comment']),'0', date("Y-m-d H:i:s"),$postData['OrderID']);
		$userID = InsertRcrdsGetID($fn,$fv,PRODUCTS_REVIEW);
		$response['status'] = "success";
		$response['msg'] = "Thanks, your review has been submitted successfully.";
	}else{
		$response['status'] = "error";
		$response['msg'] = "Sorry enter proper review.";
					
	}
	$respo = array("Status" => $status, "Message" => $Message, "redirectUrl" => $redirectUrl);
	echo json_encode($response);
		exit;
	
	
?>