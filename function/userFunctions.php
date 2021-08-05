<?php

	ob_start();	

	function userLogin($email, $password) {

		$password = md5($password);

		$query = mysql_query("SELECT * FROM ".USER." WHERE email = '$email' AND  password='$password' AND accountVerify=1 AND STATUS=1") or die(mysql_error());

		if(mysql_num_rows($query) > 0)

		 	{		

				$row = mysql_fetch_assoc($query);

				$_SESSION['ACCESSID']=$row['ID'];	

				$_SESSION['SUCCESS']="Succesfully Login";	

				echo "<script> window.location.href = '".SITEURL."index.php'; </script>"; 			}

			else

			{
				

				$_SESSION['ERROR'] = 'Username and Password does not match.. Please Login With Correct Username and Password..';

			}

		

	}



	function UserForgotPassword($email) {

		$query = mysql_query("SELECT * FROM ".SELLER." WHERE email = '$email' AND  oauth_provider = 'direct'");

		if(mysql_num_rows($query) > 0) {

			$row = mysql_fetch_assoc($query);

			$password = base64_decode($row['password']);



			$mail_body = '<table width="750" border="0" cellspacing="0" cellpadding="0" style="border:4px solid #bcd9e9">';

			$mail_body .= '<tbody><tr><td width="12" align="left" valign="top" height="15"></td>';

			$mail_body .= '<td width="448" align="left" valign="top" height="15"></td>';

			$mail_body .= '<td width="21" align="left" valign="top" height="15"></td>';

			$mail_body .= '<td width="247" align="left" valign="top" height="15"></td>';

			$mail_body .= '<td width="14" align="right" valign="top" height="15"></td>';

			$mail_body .= '</tr>';



			$mail_body .= '<tr>';

			$mail_body .= '<td align="left" valign="top"></td>';

			$mail_body .= '<td align="left" valign="top">';

			$mail_body .= '<div style="font:normal 12px arial;color:#252c86"> Hello '.$row['name'].', <p>Greetings from Crowdads team.</p>';

			$mail_body .= '<p>It is our pleasure to fulfill your request for new password.<br >';

			$mail_body .= 'Email is '.$row['email'].' <br >';

			$mail_body .= 'Password is '.$password.' </p>';

			$mail_body .= '<p> Thank you for being with us.</p>Crowdads Team.</div>';

			$mail_body .= '</td>';

			$mail_body .= '<td></td>';

			$mail_body .= '<td></td>';

			$mail_body .= '<td></td>';

			$mail_body .= '</tr>';

			$mail_body .= '</tbody></table>';



			$subject = "Crowdads Forget Password Reminder";

			$headers  = "From: Crowdads"."\r\n";

			$headers .= "Content-type: text/html\r\n";

			$to = $email;

			$mail_result = mail($to, $subject, $mail_body, $headers);

			$_SESSION['SUCCESS'] = 'Password success send on your email address. please check your inbox or spam box.';

		}

		else

			$_SESSION['ERROR'] = 'Email does not exits';

	}

	function UpdateUserPassword($id, $currentpass, $newpass, $confirmpass) {

		$UserData = GetSglRcrd(SELLER, $id);

		$currentpass = base64_encode($currentpass);

		if($currentpass == $UserData['password']) {

			if(!empty($newpass) && !empty($confirmpass)) {

				if($newpass == $confirmpass) {

					$newpassword = md5($newpass);

					$query = mysql_query("UPDATE ".SELLER." SET password = '$newpassword' WHERE id = '$id' AND oauth_provider = 'direct'") or die(mysql_error());

					if($query)

						$_SESSION['SUCCESS'] = 'Password Update Successfully..';

					else

						$_SESSION['ERROR'] = 'Opps Something was wrong. try again';

				}

				else {

					$_SESSION['ERROR'] = 'New Password and Confirm Password does not match';

				}

			} else {

				$_SESSION['ERROR'] = 'Do not empty Password Field';

			}

		} else {

			$_SESSION['ERROR'] = 'Current Password does not match';

		}

	}
	
	
	
	
	
?>