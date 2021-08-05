<?php
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
require("../sendgrid-php/sendgrid-php.php");
// include("../wordpress/wp-config.php");
if ( ! empty( $_REQUEST['designer_id'] ) && $_REQUEST['designer_id'] > 0 ) {

    $url = SITEURL;
    $loginurl = SITEURL . 'login.php';
    $imgtop = SITEURL . 'EmailTemplate/images/emailtop.png';
    $fb = SITEURL . 'EmailTemplate/images/facebook-icon.png';
    $twitter = SITEURL . 'EmailTemplate/images/twitter-icon.png';
    $pinterest = SITEURL . 'EmailTemplate/images/pintrest-icon.png';
    $google = SITEURL . 'EmailTemplate/images/google-icon.png';
    $instagram = SITEURL . 'EmailTemplate/images/instagram-icon.png';
    $imgbottom = SITEURL . 'EmailTemplate/images/emailbottom.png';
    $designerId = $_REQUEST['designer_id'];

    $designerData = GetSglRcrdOnCndi( USERS, "UserID=" . $designerId );
    $UserName = $designerData['FName'] . ' ' . $designerData['LName'];
    $transactionId = $_REQUEST['transaction_id'];

		$sql="SELECT O.TransactionId FROM `tbl_orders` O,tbl_products P,tbl_transactions T where O.Id in (".$_REQUEST['orderids'].") and O.ProductID=P.id and O.TransactionId=T.id";
	$productdata=ExecCustomQuery($sql);

    $orderLink = $url . "work-details.php?order_id=" . $productdata[0]["TransactionId"];
    $mail = <<<EOF
    <body marginheight="0" marginwidth="0" leftmargin="0" topmargin="50" bgcolor="#fff">

    <table width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">

<tr>

    <td>

         <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">

         <tr>  <td><img src="$imgtop"></td> </tr>

         </table>

          <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" height="100" bgcolor="#FFFFFF">

          <tr>

          <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;"> New Order Assigned </td>

          </tr>

          </table>



        <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="15">

        <tr>

         <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">Hello <strong>

          $UserName,</strong><br/><br/></td>

          </tr>

          </table>

          <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

            <tr>

          <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">

            Please <b><a href="$orderLink">click here</a></b> to check order. <br>

        <br/>
        <br/>
          If you have any questions or comments, please feel free to give us a shout at contact us at support@flashyflyers.com - our support team will respond to your inquiry promptly.
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

    $to = $designerData['Email'];
    $subject = 'New Order Assigned';
    // $from = 'support@flashyflyers.com';
    $from = 'flashy@flashyflyers.com';

    // To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

    // Create email headers
    $headers .= 'From: Flashy Flyers <' . $from . ">\r\n" . 'Reply-To: ' . $from . "\r\n" . 'CC: ' . 'support@flashyflyers.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
   // Compose a simple HTML email message
// if(wp_mail($to, $subject, $mail, $headers)){
//        echo "Your mail has been sent successfully to $to.";
//    } else {
//        echo 'Unable to send email. Please try again.';
    $headers .= 'From: Flashy Flyers <' . $from . ">\r\n" . 'Reply-To: ' . $from . "\r\n" . 'CC: ' . 'support@flashyflyers.com' . "\r\n" . 'X-Mailer: PHP/' . phpversion();
   // Compose a simple HTML email message
// if(wp_mail($to, $subject, $mail, $headers)){
//        echo "Your mail has been sent successfully to $to.";
//    } else {
//        echo 'Unable to send email. Please try again.';
//     }

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom($from);
    $email->setSubject($subject);
    $email->addTo($to);
    $email->addContent("text/html", $mail);

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
}
?>
