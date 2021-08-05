<?php 

// the message
$msg = "First line of text\nSecond line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
mail("karanvij210@gmail.com","My subject",$msg);

//phpinfo();


//     ini_set( 'display_errors', 1 );
//     error_reporting( E_ALL );
//     $from = "admin@flashyflyers.com";
//     $to = "karanvij210@gmail.com";
//     $subject = "PHP Mail Test script";
//     $message = "This is a test to check the PHP Mail functionality";
//     $headers = "From:" . $from;
//   if (mail($to,$subject,$message, $headers)) {
//       echo "Test email sent";
//   }

?>