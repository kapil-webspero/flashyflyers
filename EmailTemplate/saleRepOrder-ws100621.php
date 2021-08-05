<?php
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
include("../wordpress/wp-config.php");
$url=SITEURL;
$userData=GetSglRcrdOnCndi(USERS, "USERID=".$_REQUEST['UID']);
$Orderkey = $_REQUEST['key'];
extract($userData);
$loginurl=SITEURL.'login.php';
$imgtop=SITEURL.'EmailTemplate/images/emailtop.png';
$fb=SITEURL.'EmailTemplate/images/facebook-icon.png';
$twitter=SITEURL.'EmailTemplate/images/twitter-icon.png';
$pinterest=SITEURL.'EmailTemplate/images/pintrest-icon.png';
$google=SITEURL.'EmailTemplate/images/google-icon.png';
$instagram=SITEURL.'EmailTemplate/images/instagram-icon.png';
$imgbottom=SITEURL.'EmailTemplate/images/emailbottom.png';
$pass=base64_decode($Password);
$OrderUrl = SITEURL."checkout.php?key=".$Orderkey;

$SalesData=GetSglRcrdOnCndi(USERS, "USERID=".$_REQUEST['SalesUID']);

$salesFName = $SalesData['FName'];
$salesLName = $SalesData['LName'];
$salesEmail = $SalesData['Email'];



$ProductArr = getALLProductArr();
	$AddonArr = getAddonArr();
	$ProductSize = getProdSizeArr();
	$ProductType = getProdTypeArr();
	$UserArr = getUserArr();

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

          <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;">Your order is ready </td> 

          </tr>

          </table>

           

	<table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="15">

        <tr> 

         <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">Hello <strong>

		  $FName,</strong><br/><br/></td>

          </tr>

          </table>

          <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

            <tr> 

          <td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;">


To get started, login with the details below<br/><br/>

<b>Login page:</b> <a href="$loginurl">Login here</a><br/>

<b>Username:</b> <a href="mailto:$Email">$Email</a><br/>

<b>Password:</b> $pass

<br>

<br/>
<b>Your order is ready Please click on below pay now Link</b><br> <a href="$OrderUrl" style="font-size: 16px;background: #d33b30;font-weight: bold;/*! font-family: Arial; */text-align: center;line-height: 20px;color: #fff;padding: 10px 20px;text-decoration: none;border-radius: 10px;text-transform: uppercase;letter-spacing: 1px;margin: 0 auto;text-align: center;display: block;margin-top: 12px;">Pay Now</a>
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


//admin

$mail2='
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="50" bgcolor="#fff">

<table width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">

<tr>

    <td>

         <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">

         <tr>  <td><img src="'.$imgtop.'"></td> </tr>

         </table>

           

          <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" height="100" bgcolor="#FFFFFF">

          <tr>

          <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;"> Customer order details!  </td> 

          </tr>

          </table>

           

           

         <!--Hello -->    

		<table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="15">

        <tr> 

        	<td style="font-size:14px; color:#000; font-family:Arial; text-align:left; line-height:20px;"> Hello <strong> '.$salesFName.' '.$salesLName.',</strong> !<br/><br/></td> 

        </tr>

   		</table>

    

    

       <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

      

 		

 		<tr> 

          <td> 

		     

		  <table style="width: 100%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">

			  <tr>

				  <td style="width:60%"><strong>Product </strong> </td>

				  <td style="width:20%"><strong>Qty</strong></td>

				  <td style="width:20%"><strong>Price</strong></td>

			  </tr>';
				
				$sql="SELECT O.customeProductsFileds,O.TotalPrice as singlePrice,O.Dimensional,O.TypeBanner,O.DefaultSize,O.TransactionId,O.TurnAroundTime,O.parent_product_id,O.OtherSize,O.ExtraAddon,O.ProductID,O.TotalPrice,P.title as productTitle,P.id as productId,T.Amount as totalAmount,COALESCE(T.DiscountAmount,0) as discountAmount FROM `tbl_orders` O,tbl_products P,tbl_transactions T where O.Id in (".$_REQUEST['orderids'].") and O.ProductID=P.id and O.TransactionId=T.id";
				$tamount=0;
				$tdiscount=0;
				$productdata=ExecCustomQuery($sql);
				if(!empty($productdata)) {
				
				  $getMotionsArray  = array();
						   foreach($productdata as $OrderList) { 
						   $TypeBanner = explode(",",$OrderList['TypeBanner']);
								if(!empty($TypeBanner)){
									$getMotionsArray[$OrderList['ProductID']]	=$TypeBanner;
								}   
							 }
							
					
				foreach($productdata as $OrderList) {
				 	$tamount= $OrderList['totalAmount'];
					$tdiscount=$OrderList['discountAmount'];
					
					$totalPrice = $totalPrice+$OrderList['TotalPrice'];
								$listOSArr = explode(",",$OrderList['OtherSize']);
								$Dimensional = $OrderList['Dimensional'];
								$TypeBanner = explode(",",$OrderList['TypeBanner']);
								$TurnAroundTime = $OrderList['TurnAroundTime'];
								
								$listOSStr = $TypeBannerListStr = "";
								
								  if($ProductArr[$OrderList['ProductID']]['Addon']==0){
								foreach($listOSArr as $osListArr) {
								if($ProductSize[$osListArr]['name']!=""){
									$listOSStr .= "(".$ProductSize[$osListArr]['name'].")";
								}
								}
								}
								
								if(!empty($getMotionsArray) && $OrderList['parent_product_id']>0){
									
									if(in_array("motion",$getMotionsArray[$OrderList['parent_product_id']]) && in_array("Use my music",$getMotionsArray[$OrderList['parent_product_id']])){
										$TypeBanner[] = "motion with music";
									}else{
										if(in_array("motion",$getMotionsArray[$OrderList['parent_product_id']])){
											$TypeBanner[] = "motion";
										}
										
									}
								}
								
								if(!empty($TypeBanner) && !empty($OrderList['TypeBanner'])){
								foreach($TypeBanner as $TypeBannerList) {
									if($TypeBannerList!=""){
										$TypeBannerListStr .= "(".ucfirst($TypeBannerList).")";
									}
								
								}
								}
								
								
								if($OrderList['parent_product_id']>0){
									$productDisplayName = $ProductArr[$OrderList['parent_product_id']]['Title']." ".$ProductArr[$OrderList['ProductID']]['Title'];
										
								}else{
									$productDisplayName = $ProductArr[$OrderList['ProductID']]['Title'];	
								}
					
					
					$mail2.= '
					  <tr>
		
						  <td style="padding-top:20px;"><strong>'.$productDisplayName."</strong><br>";
						  if(empty($OrderList['customeProductsFileds'])){
						  if($Dimensional!=""){
						  $mail2 .= "(".$Dimensional.")"; 
						  }
						  
						  $mail2 .= ($TypeBannerListStr!="")? $TypeBannerListStr:"";;
						  if($TurnAroundTime!=""){
						  	$mail2 .= ($TurnAroundTime!="" && $TurnAroundTime>0) ? "(".getTurnArroundTypeByID($TurnAroundTime).")":"";
						  }
							if(!empty(trim($OrderList['DefaultSize'])) && !empty($ProductSize[$OrderList['DefaultSize']]['name'])) { $mail2 .=  "(".$ProductSize[$OrderList['DefaultSize']]['name'].")"; } if(!empty(trim($listOSStr)) && trim($listOSStr) != "()" ) { $mail2 .= $listOSStr; } 
									
						  }
						  
						  if(!empty($OrderList['customeProductsFileds'])){
							  
						 	
								
								$customeProductsFileds = unserialize($OrderList['customeProductsFileds']);
							if(isset($customeProductsFileds) && !empty($customeProductsFileds)){
												$checkCustomProduct =1;
												foreach($customeProductsFileds as $customeProductFieldsKey=>$customeProductFieldsValue){
													
													foreach($customeProductFieldsValue as $filedsPrimaryIndex=>$filedsPrimaryIndexValue){
														
														foreach($filedsPrimaryIndexValue as $filedsIndex=>$filedsIndexValue){
												
														$FiledsLabal = $customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex];
														$Filedsvalue = $filedsIndexValue;
														if($filedsIndex=="turnaround_time"){
															$Filedsvalue = $deliArr[$Filedsvalue];
														}
														 if(($filedsIndex=="checkbox_sided" || $filedsIndex=="add_music" || $filedsIndex=="add_video" || $filedsIndex=="add_facebook_cover") && $Filedsvalue=="on"){
															 			$mail2 .=  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> Yes</span></div>';
														
														}else{
														
														if(($filedsIndex=="files" || $filedsIndex =="attach_logo" || $filedsIndex=="attach_any_logos" || $filedsIndex=="attach_your_logo_design" || $filedsIndex=="vector_psd_pdf" || $filedsIndex=="attach_any_pictures"  || $filedsIndex=="attach_any_style_reference") && !empty($Filedsvalue)){
															
															
														  $filesData = array();
															
														if($filedsIndex=="attach_logo" ||  $filedsIndex=="attach_any_logos" || $filedsIndex=="attach_your_logo_design" || $filedsIndex=="vector_psd_pdf" || $filedsIndex=="attach_any_pictures"  || $filedsIndex=="attach_any_style_reference"){
															$filesData[] = $Filedsvalue;
														}else{
															$filesData = $Filedsvalue;
																
														}
															
																//$mail2 .=  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong></span></div>';		
														
															//$mail2 .='<div class="customFiles popup-gallery">';
														foreach($filesData as $files){

														if(! empty( $files ) && file_exists( SITE_BASE_PATH."/uploads/custom_product/" . $files)){
															
															
															$uploadedFileType = strtolower(pathinfo($files,PATHINFO_EXTENSION));
															 
																//$mail2 .= '<div class="ImagesCustom">';
															if($uploadedFileType=="jpg" || $uploadedFileType=="jpeg" || $uploadedFileType=="png" || $uploadedFileType=="gif"){
																//	$mail2 .= '<a href="'.SITEURL.'/uploads/custom_product/'.$files.'" class="text-white magnificPopup WhiteText"><img src="'.SITEURL.'/uploads/custom_product/'.$files.'" width="50" height="50"></a>';
															}else{
																//	$mail2 .= '<a href="'.SITEURL.'/uploads/custom_product/'.$files.'" download class="text-white WhiteText"><i class="fa fa-download"></i><br></a>';	
															}
															//	$mail2 .= '</div>';
														 } 

														}
								    					//	$mail2 .= '</div>';
														}
															
														//echo "filedsIndex".$filedsIndex;	
														if($Filedsvalue!=""  && $filedsIndex!="defaultSize" && $filedsIndex!="otherSize" && $filedsIndex!="attach_any_logos" && $filedsIndex!="music_file" && $filedsIndex!="attach_your_logo_design" && $filedsIndex!="vector_psd_pdf" && $filedsIndex!="attach_any_pictures"  && $filedsIndex!="attach_any_style_reference"  && $filedsIndex!="files" && $filedsIndex!="attach_logo"){	
														 	
															$mail2 .=  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> '.$Filedsvalue.'</span></div>';
														}else if($filedsIndex=="defaultSize" && !empty($Filedsvalue)){
															
																$mail2 .= ' <div class="customer_details"><strong>Default Size: </strong> '.$ProductSize[$Filedsvalue]['name'].'</span></div>';
															
																
														}
														else if($filedsIndex=="otherSize" && !empty($Filedsvalue)){
																	$otherSize = "";
																	foreach($Filedsvalue as $singleSize){
																		$otherSize .= $ProductSize[$singleSize]['name'].", ";
																	}
																	
																$mail2 .=  ' <div class="customer_details"><strong>Other Sizes: </strong> '.rtrim($otherSize,", ").'</span></div>';
														}
														}
													}
													
													}
												}
												
											}		
								  
						  }

									
					 
									
						  $mail2 .= '</td>
		
						  <td>1</td>
		
						  <td>&nbsp;$'.$OrderList['singlePrice'].'</td>
		
					  </tr>';
						}
				}
		$mail2.='
          </table></td></tr></table>

		  <br>	
		    <table cellspacing="0" style="width: 30%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;padding-left: 21px;">

			  <tr>

				  <td style="width:60%;"><strong>  Sub Total:</strong>  </td>

				  <td style="width:40%;"> $'.($tamount-$tdiscount).'  </td>

			  </tr>

			  <tr>

				  <td style="width:60%"><strong>  - Discount:</strong>  </td>

				  <td style="width:40%"> $'.$tdiscount.'  </td>

			  </tr>

			  			  <tr>

				  <td style="width:60%"><strong> Total: </strong>  </td>

				  <td style="width:40%">  $'.$tamount.'</td>

			  </tr>

		</table>

		
		  <br/><br/>

		  <table cellspacing="0" style="width:80%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">

			  <tr>

				  <td style="width:30%;"><strong> Customer Name:</strong>  </td>

				  <td style="width:70%;">  '.$FName.' '.$LName.' </td>

			  </tr>

			   <tr>

				  <td><strong> Customer Email:</strong>  </td>

				  <td>  '.$Email.'  </td>

			  </tr>



			 
		</table>

	</td>
 </tr>

</table>

      <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" height="80">

    <tr> <td style=" font-size:14px; text-align:left; line-height:20px; margin-top:5px;"> 

	<font face="Arial"  style="font-size:14px; line-height:20px; text-align:left; color:#203970; font-weight:bold">  

	<br><a href="'.SITEURL.'" > '.SITEURL_BASE.' </a> </font>

    </td>
 
     </tr>

    <tr> <td style=" font-size:14px; color:#000000; font-face:arial; text-align:left; line-height:20px; margin-top:0px;"> 	

	 

    </td>

     </tr>

    </table>





                                                      

      <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" height="50">

      <tr> 

       <td style=" font-size:12px; text-align:left; line-height:20px; margin-top:5px;">  </td>

       <td style="width:200px;">

		<a target="_blank" href="https://www.facebook.com/FlashyFlyers/"><img src="'.$fb.'" width="29" height="28"></a>

		<a target="_blank" href="https://twitter.com/FlashyFlyers"><img src="'.$twitter.'" width="29" height="28"></a>
		
		<a target="_blank" href="https://instagram.com/flashyflyers"> <img src="'.$instagram.'"></a>

		<a target="_blank" href="https://www.pinterest.com/flashyflyers/"><img src="'.$pinterest.'" width="29" height="28"></a

	></td>

     </tr>

     </table>

                                                      

                                                      

      <table  width="600" cellpadding="0" cellspacing="0" border="0" align="center"                                                      >

                                                         <tr>

                                                         

                                                            <td><img src="'.$imgbottom.'" width="600" height="auto">

                                                            </td>

                                                         </tr>

                                                            

                                                           

                                                      </table>

    </td>

</tr>    

</table>

</body>';





 $from = 'support@flashyflyers.com';
// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 
// Create email headers
$headers .= 'From: Flashy Flyers <'.$from.">\r\n".
    'Reply-To: '.$from."\r\n" .
    'X-Mailer: PHP/' . phpversion();
 
// Compose a simple HTML email message

wp_mail($salesEmail, 'Customer Order Created', $mail2, $headers);

//admin



$to = $Email;
$subject = 'Welcome to flashyflyers';

 
// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 
// Create email headers
$headers .= 'From: Flashy Flyers <'.$from.">\r\n".
    'Reply-To: '.$from."\r\n" .
    'X-Mailer: PHP/' . phpversion();
 
// Compose a simple HTML email message

if(wp_mail($to, $subject, $mail, $headers)){
    echo "Your mail has been sent successfully to $Email.";
} else{
    echo 'Unable to send email. Please try again.';
}
?>


