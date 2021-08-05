<?php

if(isset($_REQUEST['code']) || isset($_REQUEST['state'])){
				
		}
		
	else{if(!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
		echo "<script> window.location.href = '".SITEURL."'; </script>"; 
	}
	if(!isset($_SESSION['userType'])) {
		echo "<script> window.location.href = '".SITEURL."'; </script>"; 
	}
	if(basename($_SERVER['SCRIPT_FILENAME'])=="products.php" || basename($_SERVER['SCRIPT_FILENAME'])=="create-product.php" || basename($_SERVER['SCRIPT_FILENAME'])=="create-product-2.php" || basename($_SERVER['SCRIPT_FILENAME'])=="edit-product.php" || basename($_SERVER['SCRIPT_FILENAME'])=="edit-product-2.php"){
		
		if(isset($_SESSION['userType']) && ($_SESSION['userType'] != "admin" && $_SESSION['userType'] != "designer")) {
			
			echo "<script> window.location.href = '".SITEURL."'; </script>"; 
		}
		
		
		
		
	}
	else if(basename($_SERVER['SCRIPT_FILENAME'])=="orders.php" || basename($_SERVER['SCRIPT_FILENAME'])=="order-details.php" ){
		
		if(isset($_SESSION['userType']) && ($_SESSION['userType'] != "admin" && $_SESSION['userType'] != "sale_rep")) {
			
			echo "<script> window.location.href = '".SITEURL."'; </script>"; 
		}
		
		
		
		
	}
	else{
		if(isset($_SESSION['userType']) && $_SESSION['userType'] != "admin") {
			echo "<script> window.location.href = '".SITEURL."'; </script>"; 
		}
	}
	$AccessID = intval($_SESSION['userId']);
	$AccessType = $_SESSION['userType'];
	$AccessUData = GetSglRcrdOnCndi(USERS, "`UserID` = '$AccessID'");
	}
?>