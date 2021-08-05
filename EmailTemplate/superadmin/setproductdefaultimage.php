<?php
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	if(isset($_POST['photoimage_id']))
	{ 
	$radiobtnvalue = $_POST['radiobtnvalue'];
	$photoimage_id = $_POST['photoimage_id'];
	$productid_value = $_POST['productid_value'];
	$data = "set_default_image = '$radiobtnvalue'";
	$data1 = "set_default_image = 'no'";
		UpdateRcrdOnCndi(PRODUCT_BANNER, $data1, "prod_id = '$productid_value'");
		UpdateRcrdOnCndi(PRODUCT_BANNER, $data, "id = '$photoimage_id'");
		$response['msg']= "This Image Is Set Default Image";
		echo json_encode($response);
		exit;
	}
	
?>