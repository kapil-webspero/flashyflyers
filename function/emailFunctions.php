<?php
function welcomeMail() {
	$message = file_get_contents("files/email-template/welcome.html");
	//SendEmail("Abhishek", "abhishekjaingodha@gmail.com", "testing mail", $message);
	return $message;
}
?>