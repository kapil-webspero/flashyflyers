<?php 
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';
if(isset($_REQUEST['name']) && ($_REQUEST['type']=="user" || $_REQUEST['type']=="admin" || $_REQUEST['type']=="designer" )&& $_REQUEST['id']>0){
	$AccessID = intval($_SESSION['userId']);
	if($_REQUEST['type']=="user"){
		$checkOrder = GetSglRcrdOnCndi(ORDER, "CustomerID = '$AccessID' and Id='".$_REQUEST['id']."'");
	}else{
		$checkOrder = GetSglRcrdOnCndi(ORDER, "Id='".$_REQUEST['id']."'");
	}
	if(!empty($checkOrder)){	
	$file = "https://nyc3.digitaloceanspaces.com/assets.media.flashyflyers/products/".$_REQUEST['name']; 
	
	header("Content-Description: File Transfer"); 
	header("Content-Type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=\"". basename($file) ."\""); 
	echo $file;
	readfile ($file);
	exit(); 
	}else{
		echo "Sorry, your link is expire now.";	
	}
	
}
else if(isset($_REQUEST['name']) && $_REQUEST['type']=="userThankyou"){
	$file = "https://nyc3.digitaloceanspaces.com/assets.media.flashyflyers/products/".$_REQUEST['name']; 
	
	header("Content-Description: File Transfer"); 
	header("Content-Type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=\"". basename($file) ."\""); 
	echo $file;
	readfile ($file);
	exit(); 
	
	
}
else{
		echo "Sorry, your link is expire now.";	
	}



 ?>