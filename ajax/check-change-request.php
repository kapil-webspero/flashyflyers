<?php

	ob_start();

	require_once '../function/constants.php';

	require_once '../function/configClass.php';

	require_once '../function/siteFunctions.php';


	$id = $_REQUEST['id'];
	$whereClasue = "";
	if($_REQUEST['BannerType']!=""){
		$whereClasue .= " and TypeBanner='".strtolower($_REQUEST['BannerType'])."'";
	}
	if($_REQUEST['BannerSize']!=""){
		$whereClasue .= " and Size='".$_REQUEST['BannerSize']."'";
	}
	$TransactionOrderID = $_REQUEST['TransactionOrderID'];
	$GetSumOnCndi = GetNumOfRcrdsOnCndi(CHANGE_REQ,'TransactionOrderID = '.$TransactionOrderID." and status='Free' and type='user' and   OrderID=".$id." and ProductID='".$_REQUEST['ProductID']."' ".$whereClasue);
	$GetSumOnCndi_2 = GetNumOfRcrdsOnCndi(CHANGE_REQ,'TransactionOrderID = '.$TransactionOrderID." and status='Paid' and type='user' and   OrderID=".$id." and ProductID='".$_REQUEST['ProductID']."' ".$whereClasue);
	$status = 1; 
		if($GetSumOnCndi>0){
			$status = 0;
	}
	
		$GetSumOnCndi_3 = GetNumOfRcrdsOnCndi(CHANGE_REQ,'TransactionOrderID = '.$TransactionOrderID." and  ResponseState = '1' and  OrderID=".$id." and ProductID='".$_REQUEST['ProductID']."' ".$whereClasue);	
		if($GetSumOnCndi_3>0){
			$status = 0;
			$GetSumOnCndi_2 = $GetSumOnCndi_3;
		}


	
	$myarray = array("Status" => $status,"GetSumOnCndi" => $GetSumOnCndi,"GetSumOnCndi_2" => $GetSumOnCndi_2);

	echo json_encode($myarray);
?>