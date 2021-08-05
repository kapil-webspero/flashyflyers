<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/userFunctions.php';
	
	$status = 'error';

	$email = $_REQUEST['email'];
	if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,})$/i",$email)) {
		$Message = "Invalid Email."; 
	} else {
		$checkUsername = mysql_query("SELECT * FROM ".USERS." WHERE Email = '$email' and (oauth_provider='' OR ISNULL(oauth_provider))");
		if(mysql_num_rows($checkUsername) == 1) {
			$userData = mysql_fetch_assoc($checkUsername);
			$reset=mt_rand();
			$fn = array("user_id", "reset_code");
			$fv = array($userData['UserID'], $reset);
			$resetted = InsertRcrdsGetID($fn,$fv,RESET);
			
			$curl = curl_init();
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => SITEURL.'EmailTemplate/passwordreset.php?UID='.$userData['UserID']."&reset=".$reset,
				CURLOPT_USERAGENT => 'Curl Test'
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);
		
			$status = 'success';
			$Message = "We have sent password reset instructions to your email.";
			
		}
		else {
			$Message = "We don't have that email address in our system";
		}
	}
	$myarray = array("Status" => $status, "Message" => $Message);
	echo json_encode($myarray);
?>