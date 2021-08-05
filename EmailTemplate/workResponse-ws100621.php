<?php
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
include("../wordpress/wp-config.php");
$url=SITEURL;
$orderID = $_REQUEST['OID'];

$orderData=GetSglRcrdOnCndi(ORDER, "id=".$_REQUEST['OID']);
extract($orderData);
$userData=GetSglRcrdOnCndi(USERS, "`UserType` = 'user' AND `UserID`='".$CustomerID."'");
extract($userData);

if ($AssignedTo > 0 ) {
	 $designerData = GetSglRcrdOnCndi( USERS, "UserID=" . $AssignedTo );
	 $designerFirstName =$designerData['FName']; 
$designerLastName = $designerData['LName'];

}
$changeReq = GetSglRcrdOnCndi(CHANGE_REQ, "`OrderID` = '".$orderID."' AND `DesignerID`='".$AssignedTo."' ORDER BY ID DESC LIMIT 1");
$changeMsg = $changeReq['MessageText'];
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
$CustomerorderURL = SITEURL.'order-details.php?order_id='.$TransactionID;
$adminOrderDetailURL = ADMINURL . 'order-details.php?order_id=' . $TransactionID;

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

          <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;">  Edit Requested  </td> 

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

		Your requested changes for order #$TransactionID <br/> Please see the details below:<br><br/>

$changeMsg

 
<br/>

<br/>Please Click here for <span class="auto-style1"><strong>
		  <a href="$CustomerorderURL">
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

          <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;">  Edit Requested  </td> 

          </tr>

          </table>

           

	<table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="15">

        <tr> 

         <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">Hello <strong>

		 $designerFirstName $designerLastName ,</strong><br/><br/></td>

          </tr>

          </table>

          <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

            <tr> 

          <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">

		The client has requested changes for order #$TransactionID <br/> Please see the details below:<br><br/>

$changeMsg

 
<br/>

<br/>Please reupload the revised files in the <span class="auto-style1"><strong>
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



$adminMailContent = <<<EOF
	<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="50" bgcolor="#fff">

<table width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">

<tr>

    <td>

         <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">

         <tr>  <td><img src="$imgtop"></td> </tr>

         </table>

           

          <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" height="100" bgcolor="#FFFFFF">

          <tr>

          <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;">  Edit Requested  </td> 

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

		The client has requested changes for order #$TransactionID <br/> Please see the details below:<br><br/>

$changeMsg

 
<br/>

<br/>Please Click here for <span class="auto-style1"><strong>
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

$to = $Email;
$subject = 'Edit Requested';
$from = 'support@flashyflyers.com';
 
// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 
// Create email headers
$headers .= 'From: Flashy Flyers <'.$from.">\r\n".
    'Reply-To: '.$from."\r\n" .
    'X-Mailer: PHP/' . phpversion();
 
// Compose a simple HTML email message



$Adminsubject = 'Edit Requested';
$Adminfrom = $to;
 
// To send HTML mail, the Content-type header must be set
$Adminheaders  = 'MIME-Version: 1.0' . "\r\n";
$Adminheaders .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 
// Create email headers
$Adminheaders .= 'From: Flashy Flyers <'.$from.">\r\n".
    'Reply-To: '.$Adminfrom."\r\n" .
    'X-Mailer: PHP/' . phpversion();
 

if(!empty($designerData)){
	wp_mail($designerData['Email'], $subject, $DesingermailContent, $headers);
		
}

wp_mail($adminEmail, $Adminsubject, $adminMailContent, $Adminheaders);
if(wp_mail($to, $subject, $customerMailContent, $headers)){
    echo "Your mail has been sent successfully to team.";
} else{
    echo 'Unable to send email. Please try again.';
}
?>


