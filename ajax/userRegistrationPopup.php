<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	$status = "error";
	$password = base64_encode(random_password(15));
	$email = $_REQUEST['emailAdress'];
	if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,})$/i",$email)) {
		$Message = "Invalid Email."; 
	} else {
		$checkEmail = mysql_query("SELECT * FROM ".USERS." WHERE Email = '$email'");
	
		if(mysql_num_rows($checkEmail) == 0) {
            $name = $_REQUEST['name'];
            $name = explode(' ',$name);
            $fname = $name[0];
            $lname = !empty( $name[1] ) ? $name[1] : '';
			
			$fn = array("UserType", "Email", "Password", "CreatedDate", "CreatedOn", "Status","FName","LName");
			$fv = array("user", $email, $password, time(), date("Y-m-d H:i:s"), 'active',$fname,$lname);
			$userID = InsertRcrdsGetID($fn,$fv,USERS);

			$curl = curl_init();
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => SITEURL.'EmailTemplate/welcome.php?UID='.$userID,
				CURLOPT_USERAGENT => 'Curl Test'
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);
			
			$mailchimp=syncMailchimp($email,MAILCHIMP_API_KEY,MAILCHIMP_LIST_ID,$fname,$lname);
			$status = "success";
			$_SESSION['userId'] = $userID;
			$_SESSION['userType'] = "user";
			$redirectUrl = SITEURL.'index.php';
			$Message = "Success!  Welcome on board, we have just sent you an email with more information.";    
		}
		else {
			$status = "error";
			$Message = "Oops! We already have a user with that email address. Please try another email.";
			$redirectUrl = "";
		}
		
	}
	$myarray = array("Status" => $status, "Message" => $Message, "redirectUrl" => $redirectUrl);
	echo json_encode($myarray);
?>