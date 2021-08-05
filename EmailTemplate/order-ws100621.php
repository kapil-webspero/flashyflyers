	<?php
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	include("../wordpress/wp-config.php");
	
	$daterightnow=date('m/d/Y',time());
	$url=SITEURL;
	$userData=GetSglRcrdOnCndi(USERS, "USERID=".$_REQUEST['UID']);
	extract($userData);
	
	$imgtop=SITEURL.'EmailTemplate/images/emailtop.png';
	$fb=SITEURL.'EmailTemplate/images/facebook-icon.png';
	$twitter=SITEURL.'EmailTemplate/images/twitter-icon.png';
	$pinterest=SITEURL.'EmailTemplate/images/pintrest-icon.png';
	$google=SITEURL.'EmailTemplate/images/google-icon.png';
	$instagram=SITEURL.'EmailTemplate/images/instagram-icon.png';
	$imgbottom=SITEURL.'EmailTemplate/images/emailbottom.png';
	$ProductArr = getALLProductArr();
		$AddonArr = getAddonArr();
		$ProductSize = getProdSizeArr();
		$ProductType = getProdTypeArr();
		$UserArr = getUserArr();
	
	$mail='
	<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="50" bgcolor="#fff">
	
	<table width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF">
	
	<tr>
	
		<td>
	
			 <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">
	
			 <tr>  <td><img src="'.$imgtop.'"></td> </tr>
	
			 </table>
	
			   
	
			  <table width="100%" cellpadding="0" cellspacing="0" border="0" align="center" height="100" bgcolor="#FFFFFF">
	
			  <tr>
	
			  <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;"> Thanks for your order!  </td> 
	
			  </tr>
	
			  </table>
	
			   
	
			   
	
			 <!--Hello -->    
	
			<table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="15">
	
			<tr> 
	
				<td style="font-size:14px; color:#000; font-family:Arial; text-align:left; line-height:20px;"> Hello <strong>'.$FName.' '.$LName.',</strong> !<br/><br/></td> 
	
			</tr>
	
			</table>
	
		
	
		
	
		   <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">
	
		   <tr> 
	
				<td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;"> Thank you for shopping with us, we received your order on <strong> '.$daterightnow.' </strong> with the details below: </td>
	
		   </tr>
	
			
	
			<tr> 
	
			  <td> <br/>
	
				 
	
			  <table style="width: 100%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">
	
				  <tr>
	
					  <td style="width:60%"><strong>Product </strong> </td>
	
					  <td style="width:20%"><strong>Qty</strong></td>
	
					  <td style="width:20%"><strong>Price</strong></td>
	
				  </tr>';
					
					$sql="SELECT O.Id,O.customeProductsFileds,O.TotalPrice as singlePrice,O.Dimensional,O.TypeBanner,O.DefaultSize,O.TransactionId,O.parent_product_id,O.TurnAroundTime,O.OtherSize,O.ExtraAddon,O.ProductID,O.TotalPrice,P.title as productTitle,P.id as productId,T.Amount as totalAmount,COALESCE(T.DiscountAmount,0) as discountAmount,O.AssignedTo,O.psd_file,O.template_type,O.psd3dtitle FROM `tbl_orders` O,tbl_products P,tbl_transactions T where O.Id in (".$_REQUEST['orderids'].") and O.ProductID=P.id and O.TransactionId=T.id";
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
						
							$AssignTo = $OrderList['AssignedTo'];
							
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
						
						
						$mail.= '
						  <tr>
			
							  <td style="padding-top:20px;"><strong>'.$productDisplayName."</strong><br>";
								
								if($OrderList['template_type']=="psd" && $OrderList['psd_file']!="" && $AssignTo>0){
								
								
												if($OrderList['psd3dtitle']=="Yes"){
													$mail  .="<div style='margin-bottom:5px; display:block;font-size:16px;'>3D Title</div>";	
												}
			
								
								$mail .='<a download  href="'.SITEURL.'download.php?name=uploads/'.$OrderList['ProductID'].'/'.$OrderList['psd_file'].'&type=user&id='.$OrderList['Id'].'" title="Download"  style="background: #0070c0;
color: #fff;
padding: 10px;
margin-top: 6px;
display: inline-block;
border-radius: 10px;
text-decoration: navajowhite;
font-weight: bold;
" >Download PSD</a>';
								}
								
								if(empty($OrderList['customeProductsFileds']) ){
							  if($Dimensional!="" &&  $OrderList['template_type']=="customize"){
							  $mail .= "(".$Dimensional.")"; 
							  }
							  
							  $mail .= ($TypeBannerListStr!="")? $TypeBannerListStr:"";;
							  if($TurnAroundTime!="" &&  $OrderList['template_type']=="customize"){
								$mail .= ($TurnAroundTime!="" && $TurnAroundTime>0) ? "(".getTurnArroundTypeByID($TurnAroundTime).")":"";
							  }
								if(!empty(trim($OrderList['DefaultSize'])) && !empty($ProductSize[$OrderList['DefaultSize']]['name']) &&  $OrderList['template_type']=="customize") { $mail .=  "(".$ProductSize[$OrderList['DefaultSize']]['name'].")"; } if(!empty(trim($listOSStr)) && trim($listOSStr) != "()" ) { $mail .= $listOSStr; }
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
																			$mail .=  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> Yes</span></div>';
															
															}else{
															
															if(($filedsIndex=="files" || $filedsIndex =="attach_logo" || $filedsIndex=="attach_any_logos" || $filedsIndex=="attach_your_logo_design" || $filedsIndex=="vector_psd_pdf" || $filedsIndex=="attach_any_pictures"  || $filedsIndex=="attach_any_style_reference") && !empty($Filedsvalue)){
																
																
															  $filesData = array();
																
															if($filedsIndex=="attach_logo" ||  $filedsIndex=="attach_any_logos" || $filedsIndex=="attach_your_logo_design" || $filedsIndex=="vector_psd_pdf" || $filedsIndex=="attach_any_pictures"  || $filedsIndex=="attach_any_style_reference"){
																$filesData[] = $Filedsvalue;
															}else{
																$filesData = $Filedsvalue;
																	
															}
																
																	//$mail .=  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong></span></div>';		
															
																//$mail .='<div class="customFiles popup-gallery">';
															foreach($filesData as $files){
															
															$fileCustomeUrl = customProductImageURL($files);
	
															if(! empty( $files ) && $fileCustomeUrl!=""){
																
																
																$uploadedFileType = strtolower(pathinfo($files,PATHINFO_EXTENSION));
																 
																	//$mail .= '<div class="ImagesCustom">';
																if($uploadedFileType=="jpg" || $uploadedFileType=="jpeg" || $uploadedFileType=="png" || $uploadedFileType=="gif"){
																	//	$mail .= '<a href="'.$fileCustomeUrl.'" class="text-white magnificPopup WhiteText"><img src="'.$fileCustomeUrl.'" width="50" height="50"></a>';
																}else{
																	//	$mail .= '<a href="'.$fileCustomeUrl.'" download class="text-white WhiteText"><i class="fa fa-download"></i><br></a>';	
																}
																//	$mail .= '</div>';
															 } 
	
															}
															//	$mail .= '</div>';
															}
																
															//echo "filedsIndex".$filedsIndex;	
															if($Filedsvalue!=""  && $filedsIndex!="defaultSize" && $filedsIndex!="otherSize" && $filedsIndex!="attach_any_logos" && $filedsIndex!="music_file"  && $filedsIndex!="attach_your_logo_design" && $filedsIndex!="vector_psd_pdf" && $filedsIndex!="attach_any_pictures"  && $filedsIndex!="attach_any_style_reference"  && $filedsIndex!="files" && $filedsIndex!="attach_logo"){	
																
																$mail .=  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> '.$Filedsvalue.'</span></div>';
															}else if($filedsIndex=="defaultSize" && !empty($Filedsvalue)){
																
																	$mail .= ' <div class="customer_details"><strong>Default Size: </strong> '.$ProductSize[$Filedsvalue]['name'].'</span></div>';
																
																	
															}
															else if($filedsIndex=="otherSize" && !empty($Filedsvalue)){
																		$otherSize = "";
																		foreach($Filedsvalue as $singleSize){
																			$otherSize .= $ProductSize[$singleSize]['name'].", ";
																		}
																		
																	$mail .=  ' <div class="customer_details"><strong>Other Sizes: </strong> '.rtrim($otherSize,", ").'</span></div>';
															}
															}
														}
														
														}
													}
													
												}		
								} 
								
										
						 
										
							  $mail .= '</td><td>1</td><td>&nbsp;$'.$OrderList['singlePrice'].'</td></tr>';
							}
					}
			$mail.='
			  </table></td></tr></table>
	
			  <br>	
				<table cellspacing="0" style="width: 30%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">
	
				  <tr>
	
					  <td style="width:60%;"><strong>  Sub Total:</strong>  </td>
	
					  <td style="width:40%;"> $'.$totalPrice.'  </td>
	
				  </tr>
	
				  <tr>
	
					  <td style="width:60%"><strong>  - Discount:</strong>  </td>
	
					  <td style="width:40%"> $'.$tdiscount.'  </td>
	
				  </tr>
	
							  <tr>
	
					  <td style="width:60%"><strong> Total: </strong>  </td>
	
					  <td style="width:40%">  $'.($totalPrice-$tdiscount).'</td>
	
				  </tr>
	
			</table>
	
			
			  <br/><br/>
	
			  <table cellspacing="0" style="width:55%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">
	
				  <tr>
	
					  <td style="width:30%;"><strong>  Names:</strong>  </td>
	
					  <td style="width:70%;">  '.$FName.' '.$LName.' </td>
	
				  </tr>
	
				   <tr>
	
					  <td><strong>  Email:</strong>  </td>
	
					  <td>  '.$Email.'  </td>
	
				  </tr>
	
	
	
				  <tr>
	
					  <td><strong> Paid on:</strong>  </td>
	
					  <td> '.$daterightnow.'  </td>
	
				  </tr>
	
			</table>
	
			 <br/><br/>
	
			  <table cellspacing="0" style="width:95%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">
	
				  <tr>
	
					  <td>
	
					  If you have any questions or comments, please feel free to give us a shout at contact us at support@flashyflyers.com - our support team will respond to your inquiry promptly.
	
					  </td>
	
				  </tr>
	
			</table>
		</td>
	 </tr>
	
	</table>
	
		  <table width="600" cellpadding="0" cellspacing="0" border="0" align="center" height="80">
	
		<tr> <td style=" font-size:14px; text-align:left; line-height:20px; margin-top:5px;"> 
	
		<font face="Arial"  style="font-size:14px; line-height:20px; text-align:left; color:#203970; font-weight:bold">  
	
		<br><a href="'.$url.'" > https://flashyflyers.com </a> </font>
	
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
	
			  <td style="font-size:30px; color:#d33b30; font-weight:bold; font-family:Arial; text-align:center;  line-height:20px;"> Thanks for your order!  </td> 
	
			  </tr>
	
			  </table>
	
			   
	
			   
	
			 <!--Hello -->    
	
			<table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="15">
	
			<tr> 
	
				<td style="font-size:14px; color:#000; font-family:Arial; text-align:left; line-height:20px;"> Hello <strong> Admin,</strong> !<br/><br/></td> 
	
			</tr>
	
			</table>
	
		
	
		
	
		   <table width="95%" cellpadding="0" cellspacing="0" border="0" align="center" height="50">
	
		   <tr> 
	
				<td style=" font-size:14px; font-family:Arial; text-align:left; line-height:20px;"> We have receive an order by '.$Email.' on <strong> '.$daterightnow.' </strong> with the details below: </td>
	
		   </tr>
	
			
	
			<tr> 
	
			  <td> 
	
				 
	
			  <table style="width: 100%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">
	
				  <tr>
	
					  <td style="width:60%"><strong>Product </strong> </td>
	
					  <td style="width:20%"><strong>Qty</strong></td>
	
					  <td style="width:20%"><strong>Price</strong></td>
	
				  </tr>';
					
					$sql="SELECT O.Id,O.customeProductsFileds,O.TotalPrice as singlePrice,O.Dimensional,O.TypeBanner,O.DefaultSize,O.TransactionId,O.TurnAroundTime,O.parent_product_id,O.OtherSize,O.ExtraAddon,O.ProductID,O.TotalPrice,P.title as productTitle,P.id as productId,T.Amount as totalAmount,COALESCE(T.DiscountAmount,0) as discountAmount,O.AssignedTo,O.psd_file,O.template_type,O.psd3dtitle FROM `tbl_orders` O,tbl_products P,tbl_transactions T where O.Id in (".$_REQUEST['orderids'].") and O.ProductID=P.id and O.TransactionId=T.id";
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
						
						
							$AssignTo = $OrderList['AssignedTo'];
						
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
							  
							  
								if($OrderList['template_type']=="psd" && $OrderList['psd_file']!="" && $AssignTo>0){
								
								
												if($OrderList['psd3dtitle']=="Yes"){
													$mail2  .="<div style='margin-bottom:5px; display:block;font-size:16px;'>3D Title</div>";	
												}
			
								
							$mail2 .='<a class="downloadPSD btn" download  href="'.SITEURL.'download.php?name=uploads/'.$OrderList['ProductID'].'/'.$OrderList['psd_file'].'&type=admin&id='.$OrderList['Id'].'" title="Download"  style="background: #0070c0;
color: #fff;
padding: 10px;
margin-top: 6px;
display: inline-block;
border-radius: 10px;
text-decoration: navajowhite;
font-weight: bold;
" >Download PSD</a>';
								}
							  if(empty($OrderList['customeProductsFileds'])){
							  if($Dimensional!=""  &&  $OrderList['template_type']=="customize"){
							  $mail2 .= "(".$Dimensional.")"; 
							  }
							  
							  $mail2 .= ($TypeBannerListStr!=""   &&  $OrderList['template_type']=="customize")? $TypeBannerListStr:"";;
							  if($TurnAroundTime!=""   &&  $OrderList['template_type']=="customize"){
								$mail2 .= ($TurnAroundTime!="" && $TurnAroundTime>0) ? "(".getTurnArroundTypeByID($TurnAroundTime).")":"";
							  }
								if(!empty(trim($OrderList['DefaultSize'])) && !empty($ProductSize[$OrderList['DefaultSize']]['name'])   &&  $OrderList['template_type']=="customize") { $mail2 .=  "(".$ProductSize[$OrderList['DefaultSize']]['name'].")"; } if(!empty(trim($listOSStr)) && trim($listOSStr) != "()"   &&  $OrderList['template_type']=="customize") { $mail2 .= $listOSStr; } 
										
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
				<table cellspacing="0" style="width: 30%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">
	
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
	
			  <table cellspacing="0" style="width:55%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">
	
				  <tr>
	
					  <td style="width:30%;"><strong>  Names:</strong>  </td>
	
					  <td style="width:70%;">  '.$FName.' '.$LName.' </td>
	
				  </tr>
	
				   <tr>
	
					  <td><strong>  Email:</strong>  </td>
	
					  <td>  '.$Email.'  </td>
	
				  </tr>
	
	
	
				  <tr>
	
					  <td><strong> Paid on:</strong>  </td>
	
					  <td> '.$daterightnow.'  </td>
	
				  </tr>
	
			</table>
	
			 <br/><br/>
	
			  <table cellspacing="0" style="width:95%; font-size:14px; font-family:Arial; text-align:left; line-height:20px;">
	
				  <tr>
	
					  <td>
	
					  View the progress of this order on the <a href="'.ADMINURL.'order-details.php?order_id='.$productdata[0]["TransactionId"].'">order details</a> page. Thanks
	
					  </td>
	
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
	
	
	
	
	$to = $Email;
	$subject = 'Order Receipt';
	$from = 'support@flashyflyers.com';
	 
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	 
	// Create email headers
	$headers .= 'From: Flashy Flyers <'.$from.">\r\n".
		'Reply-To: '.$from."\r\n" .
		'X-Mailer: PHP/' . phpversion();
	 
	// Compose a simple HTML email message
	
	wp_mail('support@flashyflyers.com', $subject, $mail2, $headers);
	if(wp_mail($to, $subject, $mail, $headers)){
	   echo "Your mail has been sent successfully to $Email."; 
	} else{
		echo "Unable to send email. Please try again."; 
	}
	
	?>
