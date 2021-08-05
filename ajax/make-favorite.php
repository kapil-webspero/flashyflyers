<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	
	$favoriteTo = $_REQUEST['favoriteTo'];
	$_SESSION['favoriteTo'] =  $favoriteTo;
	

	if(!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
		$status = "login";
		$Message = "Please login in order to place bookmarks.";
	}else if($_SESSION['userId']=="guest"){
		$status = "login";
		$Message = "Please login in order to place bookmarks.";
	}
	else{
		if(GetNumOfRcrdsOnCndi(FAVOURITE,"`UserID` = '".$_SESSION['userId']."' AND `ProductID` = '".$favoriteTo."'")>0) {
			DltSglRcrd(FAVOURITE,"`UserID` = '".$_SESSION['userId']."' AND `ProductID` = '".$favoriteTo."'");
			unset($_SESSION['SUCCESS']); 
			$status = "error";
			$Message = "Bookmark removed successfully.";
		}
		else {
			$fn = array("UserID", "ProductID", "Createdon");
			$fv = array($_SESSION['userId'], $favoriteTo, $systemTime);
			InsertRcrds($fn,$fv,FAVOURITE);
			$status = "success";
			$Message = "Bookmarked successfully.";
		}
	} 
	$myarray = array("status" => $status, "message" => $Message);
	echo json_encode($myarray);
?>