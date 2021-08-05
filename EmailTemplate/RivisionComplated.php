<?php
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
// include("../wordpress/wp-config.php");
require("../sendgrid-php/sendgrid-php.php");
$url=SITEURL;
$orderID = $_REQUEST['OID'];
$orderData=GetSglRcrdOnCndi(ORDER, "id=".$_REQUEST['OID']);
extract($orderData);
$userData=GetSglRcrdOnCndi(USERS, "`UserType` = 'user' AND `UserID`='".$CustomerID."'");
extract($userData);
$imgtop=SITEURL.'EmailTemplate/images/emailtop.png';
$fb=SITEURL.'EmailTemplate/images/facebook-icon.png';
$twitter=SITEURL.'EmailTemplate/images/twitter-icon.png';
$pinterest=SITEURL.'EmailTemplate/images/pintrest-icon.png';
$google=SITEURL.'EmailTemplate/images/google-icon.png';
$instagram=SITEURL.'EmailTemplate/images/instagram-icon.png';
$imgbottom=SITEURL.'EmailTemplate/images/emailbottom.png';

$CustomerorderURL = SITEURL.'order-details.php?order_id='.$TransactionID;

$customerMailContent = <<<EOF
	<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="50" bgcolor="#fff">

<table width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">

<tr>

    <td>

         <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">

         <tr>  <td><img src="$imgtop"></td> </tr>

         </table>



          <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" height="100" bgcolor="#FFFFFF">

          <tr>

          <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;">We Complate You

</td>

          </tr>

          </table>



	<table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="15">

        <tr>

         <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">Hello <strong>

		 $FName $LName,</strong><br/><br/></td>

          </tr>

          </table>

          <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

            <tr>

          <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">


This email is to let you know we went ahead and marked your order as complete.
<br><br>
If no revisions are requested, orders are automatically completed three days after we initially deliver them.
<br><br>

If you have any issues with this order, please contact <a href='mailto:flashy@flashyflyers.com'>flashy@flashyflyers.com</a> or use the Resolution Center for further assistance.

<br><br>
<a href='https://www.flashyflyers.com/contact-us/'>We appreciate your feedback! Please take a few minutes to leave your thoughts about our service</a>


<br/>

<br/><span class="auto-style1" style="width: 100%;text-align: center;margin: 0 auto;display: block;margin-top: 17px;"><strong>
		  <a href="$CustomerorderURL" style="text-align: center;/*! display: inline; */margin-top: 10px;background: #d33b30;color: #fff;padding: 10px 21px;/*! width: 100%; */margin: 0 auto;width: 100%;text-decoration: none;border-radius: 7px;text-transform: capitalize;">
		  Review your order
</a></strong></span>
</td>

                                                         </tr>

                                                      </table>







   <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="80">

    <tr> <td style=" font-size:14px; text-align:left; line-height:20px; margin-top:5px;">

	<font face="Arial"  style="font-size:14px; line-height:20px; text-align:left; color:#203970; font-weight:bold">

	<br>Thanks, <br>
www.flashyflyers.com</font>

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

$to = $Email;
$subject = 'Your order was automatically complated';
// $from = 'support@flashyflyers.com';
$from = 'flashy@flashyflyers.com';

// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Create email headers
$headers .= 'From: Flashy Flyers <'.$from.">\r\n".
    'Reply-To: '.$from."\r\n" .
    'X-Mailer: PHP/' . phpversion();

// Compose a simple HTML email message

// if(wp_mail($to, $subject, $customerMailContent, $headers)){
//     echo "Your mail has been sent successfully to team.";
// } else{
//     echo 'Unable to send email. Please try again.';
// }

$email = new \SendGrid\Mail\Mail();
$email->setFrom($from);
$email->setSubject($subject);
$email->addTo($to);
$email->addContent("text/html", $customerMailContent);

$sendgrid = new \SendGrid(SENDGRID_API_KEY);
try {
		$response = $sendgrid->send($email);
		// print $response->statusCode() . "\n";
		// print_r($response->headers());
		// print $response->body() . "\n";
		echo "Your mail has been sent successfully to $to.";
} catch (Exception $e) {
		// echo 'Caught exception: '. $e->getMessage() ."\n";
		echo 'Unable to send email. Please try again.';
}
?>
