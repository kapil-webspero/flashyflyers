<?php
	if(!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
		echo "<script> window.location.href = '".SITEURL."'; </script>"; 
	}
	if(isset($_SESSION['userType']) && $_SESSION['userType'] != "user") {
		echo "<script> window.location.href = '".SITEURL."'; </script>"; 
	}
	$AccessID = intval($_SESSION['userId']);
	$AccessType = $_SESSION['userType'];
	$AccessUData = GetSglRcrdOnCndi(USERS, "`UserID` = '$AccessID'");
?>