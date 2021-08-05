<?php
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
// include("../wordpress/wp-config.php");
require("../sendgrid-php/sendgrid-php.php");
$url=SITEURL;
$id = $_REQUEST['id'];
$statusMockupText = $_REQUEST['statusMockupText'];
$statusText = "Approve";
if($statusMockupText=="deletemockup"){
	$statusText = "Reject";
}
if($statusMockupText=="approved"){
	$statusText = "Approved";
}
if($statusMockupText=="rejected"){
	$statusText = "Rejected";
}
$statusText = strtolower($statusText);






$orderData=GetSglRcrdOnCndi(ORDER, "id=".$id);
extract($orderData);



$products=GetSglRcrdOnCndi(PRODUCT, "id=".$ProductID);
extract($products);

$userData=GetSglRcrdOnCndi(USERS, "`UserType` = 'user' AND `UserID`='".$CustomerID."'");
extract($userData);

if ($AssignedTo > 0 ) {
	 $designerData = GetSglRcrdOnCndi( USERS, "UserID=" . $AssignedTo );
	 $designerFirstName =$designerData['FName'];
	$designerLastName = $designerData['LName'];

}
//$attachFile = $changeReq['Attachment'];
//$fileName = $changeReq['AttechmentName'];
$imgtop=SITEURL.'EmailTemplate/images/emailtop.png';
$fb=SITEURL.'EmailTemplate/images/facebook-icon.png';
$twitter=SITEURL.'EmailTemplate/images/twitter-icon.png';
$pinterest=SITEURL.'EmailTemplate/images/pintrest-icon.png';
$google=SITEURL.'EmailTemplate/images/google-icon.png';
$instagram=SITEURL.'EmailTemplate/images/instagram-icon.png';
$imgbottom=SITEURL.'EmailTemplate/images/emailbottom.png';

$orderURL = SITEURL.'work-details.php?order_id='.$TransactionID;

$adminOrderDetailURL = ADMINURL . 'order-details.php?order_id=' . $TransactionID;



$DesingermailContent = <<<EOF
	<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="50" bgcolor="#fff">

<table width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">

<tr>

    <td>

         <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">

         <tr>  <td><img src="$imgtop"></td> </tr>

         </table>



          <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" height="100" bgcolor="#FFFFFF">

          <tr>

          <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;">Revision $statusText </td>

          </tr>

          </table>



	<table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="15">

        <tr>

         <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">Hello <strong>

		 $designerFirstName $designerLastName,</strong><br/><br/></td>

          </tr>

          </table>

          <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

            <tr>

          <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">

		Customer has $statusText revision for order #$TransactionID <br/> Please see the details below:<br><br/>

		<strong>Product Name : </strong> $Title


<br/>

<br/>Please check <span class="auto-style1"><strong>
		  <a href="$orderURL">
		  order details page</a></strong></span>
</td>

                                                         </tr>

                                                      </table>







   <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="80">

    <tr> <td style=" font-size:14px; text-align:left; line-height:20px; margin-top:5px;">

	<font face="Arial"  style="font-size:14px; line-height:20px; text-align:left; color:#203970; font-weight:bold">

	<br><a href="$url" > https://flashyflyers.com </a> </font>

    </td>

     </tr>

    <tr> <td style=" font-size:14px; color:#000000; font-face:arial; text-align:left; line-height:20px; margin-top:0px;">



    </td>

     </tr>

    </table>







      <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

      <tr>

       <td style=" font-size:12px; text-align:left; line-height:20px; margin-top:5px;">  </td>

       <td style="width:200px;">

		<a target="_blank" href="https://www.facebook.com/FlashyFlyers/"><img src="$fb" width="29" height="28"></a>

		<a target="_blank" href="https://twitter.com/FlashyFlyers"><img src="$twitter" width="29" height="28"></a>

		<a target="_blank" href="https://instagram.com/flashyflyers"> <img src="$instagram"></a>

		<a target="_blank" href="https://www.pinterest.com/flashyflyers/"><img src="$pinterest" width="29" height="28"></a

	></td>

     </tr>

     </table>





      <table  width="100%" cellpadding="0" cellspacing="0" border="0" align="center"                                                      >

                                                         <tr>



                                                            <td><img src="$imgbottom" width="600" height="auto">

                                                            </td>

                                                         </tr>





                                                      </table>

    </td>

</tr>

</table>

</body>
EOF;



$AdminmailContent = <<<EOF
	<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="50" bgcolor="#fff">

<table width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">

<tr>

    <td>

         <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">

         <tr>  <td><img src="$imgtop"></td> </tr>

         </table>



          <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" height="100" bgcolor="#FFFFFF">

          <tr>

          <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;">Revision $statusText </td>

          </tr>

          </table>



	<table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="15">

        <tr>

         <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">Hello <strong>

		 Admin,</strong><br/><br/></td>

          </tr>

          </table>

          <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

            <tr>

          <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">

		Customer has $statusText revision for order #$TransactionID <br/> Please see the details below:<br><br/>

		<strong>Product Name : </strong> $Title


<br/>

<br/>Please check <span class="auto-style1"><strong>
		  <a href="$adminOrderDetailURL">
		  order details page</a></strong></span>
</td>

                                                         </tr>

                                                      </table>







   <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="80">

    <tr> <td style=" font-size:14px; text-align:left; line-height:20px; margin-top:5px;">

	<font face="Arial"  style="font-size:14px; line-height:20px; text-align:left; color:#203970; font-weight:bold">

	<br><a href="$url" > https://flashyflyers.com </a> </font>

    </td>

     </tr>

    <tr> <td style=" font-size:14px; color:#000000; font-face:arial; text-align:left; line-height:20px; margin-top:0px;">



    </td>

     </tr>

    </table>







      <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

      <tr>

       <td style=" font-size:12px; text-align:left; line-height:20px; margin-top:5px;">  </td>

       <td style="width:200px;">

		<a target="_blank" href="https://www.facebook.com/FlashyFlyers/"><img src="$fb" width="29" height="28"></a>

		<a target="_blank" href="https://twitter.com/FlashyFlyers"><img src="$twitter" width="29" height="28"></a>

		<a target="_blank" href="https://instagram.com/flashyflyers"> <img src="$instagram"></a>

		<a target="_blank" href="https://www.pinterest.com/flashyflyers/"><img src="$pinterest" width="29" height="28"></a

	></td>

     </tr>

     </table>





      <table  width="100%" cellpadding="0" cellspacing="0" border="0" align="center"                                                      >

                                                         <tr>



                                                            <td><img src="$imgbottom" width="600" height="auto">

                                                            </td>

                                                         </tr>





                                                      </table>

    </td>

</tr>

</table>

</body>
EOF;


$subject = 'Revision '.$statusText;
// $from = 'support@flashyflyers.com';
$from = 'flashy@flashyflyers.com';

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Create email headers
$headers .= 'From: Flashy Flyers <'.$from.'>\r\n'.
    'Reply-To: '.$from."\r\n" .
    'X-Mailer: PHP/' . phpversion();

// Compose a simple HTML email message



$Adminsubject = 'Revision '.$statusText;
$Adminfrom = $userData['Email'];
$adminEmail = $userData['Email'];

// To send HTML mail, the Content-type header must be set
$Adminheaders  = 'MIME-Version: 1.0' . "\r\n";
$Adminheaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Create email headers
$Adminheaders .= 'From: Flashy Flyers <'.$Adminfrom.">\r\n".
    'Reply-To: '.$Adminfrom."\r\n" .
    'X-Mailer: PHP/' . phpversion();


if(!empty($designerData)){
	// wp_mail($designerData['Email'], $subject, $DesingermailContent, $headers);
	$email1 = new \SendGrid\Mail\Mail();
	$email1->setFrom($from);
	$email1->setSubject($subject);
	$email1->addTo($designerData['Email']);
	$email1->addContent("text/html", $DesingermailContent);

	$sendgrid = new \SendGrid(SENDGRID_API_KEY);
	try {
			$response = $sendgrid->send($email1);
			// print $response->statusCode() . "\n";
			// print_r($response->headers());
			// print $response->body() . "\n";
			echo "Your mail has been sent successfully to ".$designerData['Email'].".";
	} catch (Exception $e) {
			// echo 'Caught exception: '. $e->getMessage() ."\n";
			echo 'Unable to send email. Please try again.';
	}

}


// if(wp_mail($adminEmail, $Adminsubject, $AdminmailContent, $Adminheaders)){
//
//     echo "Your mail has been sent successfully to $adminEmail.";
// } else{
//     echo 'Unable to send email. Please try again.';
// }

$email = new \SendGrid\Mail\Mail();
$email->setFrom($from);
$email->setSubject($Adminsubject);
$email->addTo($adminEmail);
$email->addContent("text/html", $AdminmailContent);

$sendgrid = new \SendGrid(SENDGRID_API_KEY);
try {
		$response = $sendgrid->send($email);
		// print $response->statusCode() . "\n";
		// print_r($response->headers());
		// print $response->body() . "\n";
		echo "Your mail has been sent successfully to $adminEmail.";
} catch (Exception $e) {
		// echo 'Caught exception: '. $e->getMessage() ."\n";
		echo 'Unable to send email. Please try again.';
}
?>
