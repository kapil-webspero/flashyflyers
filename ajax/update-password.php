<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/userFunctions.php';

	$resetcode = $_REQUEST['resetcode'];
	$uid = $_REQUEST['user'];
	$password=base64_encode($_REQUEST['password']);
	$status = 'error';
	$checkData = mysql_query("SELECT * FROM ".RESET." WHERE user_id = $uid and reset_code='$resetcode' and reset=0");
	if(mysql_num_rows($checkData) == 1) {
		$userData = mysql_fetch_assoc($checkData);
		$query = mysql_query("UPDATE ".USERS." SET password='".$password."' WHERE UserID=".$uid) or die(mysql_error());
		$query = mysql_query("UPDATE ".RESET." SET reset=1 WHERE user_id = $uid and reset_code='$resetcode' and reset=0") or die(mysql_error());
	
		$status = 'success';
		$Message = "Password updated successfully.";
		$_SESSION['SUCCESS'] = "Password updated successfully.";
		
	}
	else {
		$Message = "Oops!: Reset code is expired or invalid";
	}
	
	$myarray = array("Status" => $status, "Message" => $Message);
	echo json_encode($myarray);
?>