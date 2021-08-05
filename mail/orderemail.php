<?php
require '../function/constants.php';
$url=SITEURL;
$logo=$url.'images/logo.png';
$abouturl=$url.'about';
$contacturl=$url.'contact';
$termsurl=$url.'terms.php';
$ord="#".$_REQUEST['order_id'];
$price="$".$_REQUEST['order_amount'];
$mail = <<<EOF

<body class="perks " style="color:#2a2a2a;font-family:Arial, sans-serif;font-size:18px;width:100% !important;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;line-height:18px;margin-top:0;margin-bottom:20px;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">
  <div style="background-color:#eae9ea;">
    <!--[if mso]>
    <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
        <v:fill type="tile" color="#eae9ea"></v:fill>
    </v:background><![endif]-->
  </div>
  <table height="100%" width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#eae9ea" style="border-collapse:collapse;">
    <tbody><tr>
      <td valign="top" align="center" style="border-collapse:collapse;">
        <table width="600" cellspacing="0" cellpadding="0" border="0" class="main" style="border-collapse:collapse;margin-left:30px;margin-right:30px;">
          <tbody><tr>
            <td align="left" class="min_width" style="border-collapse:collapse;width:600px;">

              <!-- BODY SECTION -->

              <table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" class="" style="border-collapse:collapse;margin-top:10px;">
                <tbody><tr>
                  <td align="left" style="border-collapse:collapse;">

                    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;">
                      <tbody><tr style="margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">
                        <td align="center" style="border-collapse:collapse;border-spacing:0;padding-top:50px;padding-bottom:50px;padding-right:50px;padding-left:50px;">


                          <div align="left" style="margin-bottom:30px;text-align:left;">
                            <a href="SITEURL" utm_content="igg-header-logo" utm_medium="email" style="color:#eb1478;text-decoration:none;">
                              <img alt="" class="logo" src="$logo" width="125" style="display:block;border-style:none;width:100%;">
                            </a>
                          </div>

                          <p style="font-size:22px;line-height:22px;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">Your order placed successfully.</p>
                           <h2>Order Details:</h2>
                           <p>Product Name: Test Product Name</p>     
                           <p>Product Price: $price</p>
                           <p>Order ID: <a href="">$ord</a></p> 

                          <div align="left" style="text-align:left;">
                            <p style="font-size:20px;line-height:28px;margin-top:20px;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">
                              Thanks for shopping at Optimabranding. Have a great day!
                            </p>
                          </div>

                          <p style="font-size:20px;line-height:22px;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">
                            <a href="#">Payment was successful</a>
                          </p>

                          <div style="width:100px;margin-top:35px;margin-bottom:35px;margin-right:0px;margin-left:0px;border-width:1px;border-style:solid;border-color:#e9e9e9;"></div>

                          <p style="font-size:20px;line-height:22px;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">
                            Sincerely,
                          </p>

                          <p style="font-size:22px;font-weight:bold;line-height:22px;margin-top:10px;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">
                            The Optimabranding Team
                          </p>


                        </td>
                      </tr>
                    </tbody></table>

                  </td>
                </tr>
              </tbody></table>


              <table width="100%" cellspacing="0" cellpadding="0" border="0" class="footer" style="border-collapse:collapse;">
                <tbody><tr>
                  <td align="center" class="footer" style="border-collapse:collapse;padding-top:30px;padding-bottom:30px;padding-right:30px;padding-left:30px;">
                    <p style="font-size:13px;font-family:Arial, sans-serif;color:#000001;line-height:18px;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;">
                      <a class="footer-link" target="_blank" href="$url" style="color:#2a2a2a;text-decoration:none;">Home</a> |
                      <a class="footer-link" target="_blank" href="$abouturl" style="color:#2a2a2a;text-decoration:none;">About</a> |
                      <a class="footer-link" target="_blank" href="$contacturl" style="color:#2a2a2a;text-decoration:none;">Contact us</a> |
                      <a class="footer-link" target="_blank" href="$termsurl" style="color:#2a2a2a;text-decoration:none;">Terms & Conditions</a> | 
                    </p>
                   
                  </td>
                </tr>
              </tbody></table>
            </td>
          </tr>
        </tbody></table>
      </td>
    </tr>
  </tbody></table>


</body>
EOF;

$to = (isset($_REQUEST['to'])?$_REQUEST['to']:'');
$subject = 'Order Placed';
$from = 'info@optimabranding.com';
 
// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 
// Create email headers
$headers .= 'From: '.$from."\r\n".
    'Reply-To: '.$from."\r\n" .
	'CC: '.'stsveamillion@gmail.com'."\r\n".
    'X-Mailer: PHP/' . phpversion();
 
// Compose a simple HTML email message

 

if(mail($to, $subject, $mail, $headers)){
    echo "Your mail has been sent successfully .";
} else{
    echo 'Unable to send email. Please try again.';
}
?>