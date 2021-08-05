<?php

	ob_start();

	require_once '../function/constants.php';

	require_once '../function/configClass.php';

	require_once '../function/siteFunctions.php';

	

	$email = $_REQUEST['emailAddress'];

	$password = base64_encode($_REQUEST['password']);

	$status = "error";

	$redirectUrl = '';

	$crntTime = time();

	if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,})$/i",$email)) {
		$Message = "Invalid Email."; 
	} else {

		$checkUsername = mysql_query("SELECT * FROM ".USERS." WHERE Email = '$email'");

		if(mysql_num_rows($checkUsername) == 1) {
	
			$userData = mysql_fetch_assoc($checkUsername);
			
			if($userData['Password'] == $password) {
				if($userData['Status'] == "active") {
					
					/*$flashyflyersUsersArray=array(
  					  'userId'=>$userData['UserID'],
 					   'userType'=>$userData['UserType']
					);*/
					//$json = json_encode($flashyflyersUsersArray);
					//setcookie('flashyflyersUsers', $json, time() + (86400 * 30), "/","flashyflyers.com", 1);
					$_SESSION['userId'] = $userData['UserID'];
					$_SESSION['userType'] = $userData['UserType'];
					
					UpdateRcrdOnCndi(USERS,"`LastLogin` = '".$systemTime."'", "`UserID` = '".$userData['UserID']."'");
					
					$favoriteTo = $_SESSION['favoriteTo'];
					if(!empty($favoriteTo)){			
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
						unset($_SESSION['favoriteTo']);
					}
						unset($_SESSION['SUCCESS']);
					$status = "success";
					if($_SESSION['userType']=="admin"){
						$redirectUrl = SITEURL.'superadmin/index.php';	
							$_SESSION['loginType']="admin";
					}else{
						$_SESSION['loginType']="user";
						if(isset($_SESSION['repOrderCartRedirect'])){
							$redirectUrl = SITEURL.'checkout.php?key='.$_SESSION['repOrderCartRedirect'];
						}else{
							$redirectUrl = SITEURL.'index.php';
						}
					}
				} else {
					$Message = "Your account is banned by admin, please contact to our support team to unbanned your account.";
				}
			}
			else
				$Message = "Your login details are incorrect, please try again";
		} else
		$Message = "We do not have that email address in our records.";

			
		if(isset($_REQUEST['login']) && !empty($_REQUEST['login']) && $_REQUEST['login'] == "info") {
			$_SESSION['SUCCESS'] = "Below, please provide the information you need for your flyer";
			
		}
	}
	$myarray = array("Status" => $status, "Message" => $Message, "redirectUrl" => $redirectUrl);

	echo json_encode($myarray);
?>