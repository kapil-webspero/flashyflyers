<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
    $status = 'success';
	
						   $allowed =  array('gif','png' ,'jpg','webp');

	 $ID = $_POST['ID'];
	if(!empty($_POST['ID'])){
		$ProductArr = getALLProductArr();
		 $OrderList = GetSglRcrdOnCndiWthOdr(ORDER,"Id='".$ID."'","Id","asc");
		 
		 $product=GetSglRcrdOnCndi(PRODUCT, "  id=".$OrderList['ProductID']);
		$Productslug = $product['slug'];
		 
		 if($OrderList['parent_product_id']>0){
				$productDisplayName = "<div class='RightDivProductDetails'><div class='productName'>".$ProductArr[$OrderList['parent_product_id']]['Title']." ".$ProductArr[$OrderList['ProductID']]['Title']."</div>";	
		 }else{
			 if($ProductArr[$OrderList['ProductID']]['Addon']==1){
				$productDisplayName = "<div class='RightDivProductDetails'><div class='productName'>".$ProductArr[$OrderList['ProductID']]['Title']."</div>";	
			}else{
				$productDisplayName = "<div class='RightDivProductDetails'><div class='productName'><a target='_blank' href=".SITEURL."p/".$Productslug." style='color:#4f4f4f;text-decoration:none'>".$ProductArr[$OrderList['ProductID']]['Title']."</a></div>";	
				}
				
			}
			
		?>
        
        <div id="ProductInstruction<?php echo $OrderList['Id']; ?>" class="popup ProductInstruction">

    <div class="popup-box">

        <div class="popup-middle">

            <div class="popup_block">

                <h4 class="title text-center mb-4"><strong class="prodTitle"><?php echo $productDisplayName; ?></strong> <span class="close-btn float-right"><i class="fa fa-times"></i></span></h4>

                <div class="popupfull_content">

                    <div class="customer_content">
                        <?php
						$MainTitle = $OrderList['MainTitle'];
						$Subtitle = $OrderList['Subtitle'];
						$Mixtapename = $OrderList['Mixtapename'];
						$Singletitle = $OrderList['Singletitle'];
						$deejay_name = $OrderList['deejay_name'];
						$presenting = $OrderList['presenting'];
						
						$ename = $OrderList['ename'];
						$EventDate = $OrderList['EventDate'];
						$MusicBy = $OrderList['MusicBy'];
						$ownSong = $OrderList['own_song'];
						$Venue  = $OrderList['Venue'];
						$Address = $OrderList['Address'];
						$MoreInfo = $OrderList['MoreInfo'];
						$requirement_note = $OrderList['requirement_note'];
						$venue_logo = $OrderList['venue_logo'];
						$ArtistName = $OrderList['ArtistName'];
						$ProducedBy = $OrderList['ProducedBy'];
						$ProductTypeOrder = $OrderList['ProductType'];
						$PhoneNumber = $OrderList['PhoneNumber'];
						$VenueEmail = $OrderList['VenueEmail'];
						$Facebook = $OrderList['Facebook'];
						$Instagram = $OrderList['Instagram'];
						$Twitter = $OrderList['Twitter'];
						$Music = $OrderList['Music'];
					
						if(!empty($OrderList['customeProductsFileds'])){
							$customeFiledsConstant = array();
							mb_internal_encoding('utf-8');
							$customeProductsFileds = unserialize($OrderList['customeProductsFileds']);
							
							$customeFiledsConstant[key($customeProductsFileds)] = $customeProductFields[key($customeProductsFileds)]; 
							
							if(isset($customeFiledsConstant) && !empty($customeFiledsConstant)){
							?>
                             <form method="post" action="" enctype="multipart/form-data">
                           <input type="hidden"  name="FormInformation[none]">
                           
                            <div class="customer_details updateInformationMsg" >
                                <strong>Information has been successfully updated. </strong> 
                                </div>
                               
                         <div class="editInformation"><input type="button" value="Edit Information"></div>
                       
                         <?php 	
										$product=GetSglRcrdOnCndi(PRODUCT, " Addon = '0' AND id='".$OrderList['ProductID']."'");
																	
												$checkCustomProduct =1;
												foreach($customeFiledsConstant as $customeProductFieldsKey=>$customeProductFieldsValue){
													
													foreach($customeProductFieldsValue as $filedsPrimaryIndex=>$filedsPrimaryIndexValue){
														
														foreach($filedsPrimaryIndexValue as $filedsIndex=>$filedsIndexValue){
														
															
														$FiledsLabal = $customeProductsFileds[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex];
														$Filedsvalue = $customeProductsFileds[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex];
														
																
													
														
											$preSelectionCustom = 	$Filedsvalue;
											
											$FiledsKey = $customeFiledsConstant[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex];
											$filedsName = 'customeProductFields['.$customeProductFieldsKey.']['.$filedsPrimaryIndex.']['.$filedsIndex.']';
												
											?>
                                              <div class="EditInformationData">
                                              	<div class="customer_details">
                                                
                                                
                                            <?php
											
										
											if($FiledsKey['type']!="3d_or_2d" && $FiledsKey['type']!="sizes" && $FiledsKey['type']!="checkbox_sided"  && $FiledsKey['type']!="add_music" && $FiledsKey['type']!="add_video" && $FiledsKey['type']!="add_facebook_cover"){
															
											?>
											<strong><?php echo $FiledsKey['label'] ?></strong>
										   <?php } ?>
											<?php if($FiledsKey['type']=="datepicker"){ 
											
											?>
												<input value="<?php echo $preSelectionCustom;?>" type="text" autocomplete="off" name="<?php echo $filedsName ?>" >
											<?php } ?>
											
											 <?php if($FiledsKey['type']=="pick_intro"){ 
											 
												$photosCount=GetNumOfRcrdsOnCndi(PRODUCT_BANNER, "`prod_id` = '".$OrderList['ProductID']."' and  set_default_facebookimage!='yes' and (filetype='image' OR filetype='video')");
											 ?>
													<select  name="<?php echo $filedsName ?>">
														<?php for($i=1;$i<=$photosCount;$i++){ 
														?>
														<option value="<?php echo (strlen($i)==1) ?"0".$i:$i ?>" <?php $checkLogoNumber=  (strlen($i)==1) ?"0".$i:$i; 
														echo ($checkLogoNumber==$preSelectionCustom)?"selected='selected'":""; ?>> <?php echo (strlen($i)==1) ?"0".$i:$i ?></option>
														<?php } ?>
													</select>
											<?php } ?>
											
										   
										  <?php if($FiledsKey['type']=="notes_to_graphic_designer"){ ?>
											 <div class="notes_to_graphic_designer">
														<textarea  name="<?php echo $filedsName ?>"><?php echo $preSelectionCustom;?></textarea>
										</div>
											<?php } ?>
										  
											
											<?php if($FiledsKey['type']=="text"){ ?>
												<input value="<?php echo $preSelectionCustom;?>"  type="text"  name="<?php echo $filedsName ?>" >
											<?php } ?>
											
											
											<?php if($FiledsKey['type']=="textarea"){ ?>
												<textarea  name="<?php echo $filedsName ?>"  ><?php echo $preSelectionCustom;?></textarea>
											<?php } ?>
											
											<?php if($FiledsKey['type']=="multiple_file"){ ?>
												<input type="file" multiple   name="<?php echo $filedsName ?>[]">
                                                
                                                 <?php 
												 $filesData = $Filedsvalue;
												 
												if(! empty( $filesData ) ){
															foreach($filesData as $files){
															$fileCustomeUrl = customProductImageURL($files);
															if($fileCustomeUrl!=""){
															$uploadedFileType = strtolower(pathinfo($files,PATHINFO_EXTENSION));
															 
															echo '<div class="customPImages" id="customPImages'.$customeProductFieldsKey."_".$filedsIndex."_".$OrderList['Id'].'">';
															?>
																<i class="fas fa-times deleteCustomProudctImages"   data-id="<?php echo $OrderList['Id'] ?>" data-product_type="<?php echo $customeProductFieldsKey; ?>" data-fileds_type="<?php echo $filedsIndex; ?>" data-name="<?php echo $files; ?>" data-option_type="<?php echo $filedsPrimaryIndex; ?>" ></i>
															<?php 
															echo '<div class="ImagesCustom popup-gallery">';
															if($uploadedFileType=="jpg" || $uploadedFileType=="jpeg" || $uploadedFileType=="png" || $uploadedFileType=="gif"){
																echo '<a href="'.$fileCustomeUrl.'" class="text-white magnificPopup WhiteText"><img src="'.$fileCustomeUrl.'" width="50" height="50"></a>';
															}else{
																echo '<a href="'.$fileCustomeUrl.'" target="_blank" download class="text-white WhiteText"><i class="fa fa-download"></i><br></a>';	
															}
															echo '</div></div>';
															}
														 } 
												}

												?>
                                                
											<?php } ?>
											
											 <?php if($FiledsKey['type']=="single_file"){ ?>
												<input type="file"   name="<?php echo $filedsName ?>" >
                                                
                                                <?php 
												
												$fileCustomeUrl = customProductImageURL($Filedsvalue);
												if(! empty( $Filedsvalue ) && $fileCustomeUrl !=""){
															
															
															$uploadedFileType = strtolower(pathinfo($Filedsvalue,PATHINFO_EXTENSION));
															 
															echo '<div class="customPImages" id="customPImages'.$customeProductFieldsKey."_".$filedsIndex."_".$OrderList['Id'].'">';
															?>
																	<i class="fas fa-times deleteCustomProudctImages"   data-id="<?php echo $OrderList['Id'] ?>" data-product_type="<?php echo $customeProductFieldsKey; ?>" data-fileds_type="<?php echo $filedsIndex; ?>" data-name="<?php echo $Filedsvalue; ?>" data-option_type="<?php echo $filedsPrimaryIndex; ?>" ></i>															<?php 
															echo '<div class="ImagesCustom popup-gallery">';
															if($uploadedFileType=="jpg" || $uploadedFileType=="jpeg" || $uploadedFileType=="png" || $uploadedFileType=="gif"){
																echo '<a href="'.$fileCustomeUrl.'" class="text-white magnificPopup WhiteText"><img src="'.$fileCustomeUrl.'" width="50" height="50"></a>';
															}else{
																echo '<a href="'.$fileCustomeUrl.'" target="_blank" download class="text-white WhiteText"><i class="fa fa-download"></i><br></a>';	
															}
															echo '</div></div>';
														 } 

												?>
											<?php } ?>
											
											 <?php if($FiledsKey['type']=="checkbox_sided"){ ?>
												
												<label class="custom-control custom-checkbox">
													<input <?=($preSelectionCustom=="on") ? 'checked':''?> data-amount="<?php echo $checkbox_sided;?>" name="<?php echo $filedsName ?>"  type="checkbox" class="custom-control-input add_extra_items">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description"><?php echo $FiledsKey['label'] ?></strong></span>
												</label>
												
											<?php } ?>
											
											<?php 
											
											if($FiledsKey['type']=="add_music" ){ ?>
												
												<label  class="custom-control custom-checkbox">
													<input <?=($preSelectionCustom=="on") ? 'checked':''?> data-amount="<?php echo $add_music;?>" name="<?php echo $filedsName ?>"  type="checkbox" class="custom-control-input add_extra_items">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description"><?php echo $FiledsKey['label'] ?></strong></span>
												
                                                </label>
                                                
                                                <input type="file" class="AddMusicFile" style="display:<?=($preSelectionCustom=="on") ? 'block':'none'?> ;" name="<?php echo 'customeProductFields['.$product['CustomProductFiledsType'].']['.$filedsPrimaryIndex.'][music_file]'; ?>">
												
												<?php 
												
												$musicFile = $customeProductsFileds[$customeProductFieldsKey][$filedsPrimaryIndex]["music_file"];
												
												$musicFileCustomeUrl = customProductImageURL($musicFile);
												
												
												if(! empty( $musicFile ) && $musicFileCustomeUrl!=""){
															
															
															$uploadedFileType = strtolower(pathinfo($musicFile,PATHINFO_EXTENSION));
															 
															echo '<div class="customPImages" id="customPImages'.$customeProductFieldsKey."_".$filedsIndex."_".$OrderList['Id'].'">';
															?>
															<i class="fas fa-times"  data-id="<?php echo $OrderList['Id'] ?>" data-product_type="<?php echo $customeProductFieldsKey; ?>" data-fileds_type="<?php echo $filedsIndex; ?>" data-name="<?php echo $Filedsvalue; ?>" data-option_type="<?php echo $filedsPrimaryIndex; ?>" ></i>
															<?php 
															echo '<div class="ImagesCustom popup-gallery">';
															if($uploadedFileType=="jpg" || $uploadedFileType=="jpeg" || $uploadedFileType=="png" || $uploadedFileType=="gif"){
																echo '<a href="'.$musicFileCustomeUrl.'" class="text-white magnificPopup WhiteText"><img src="'.$musicFileCustomeUrl.'" width="50" height="50"></a>';
															}else{
																echo '<a href="'.$musicFileCustomeUrl.'" target="_blank" download class="text-white WhiteText"><i class="fa fa-download"></i><br></a>';	
															}
															echo '</div></div>';
														 } 

												?>
												
											<?php } ?>
											
											 <?php if($FiledsKey['type']=="add_video"){ ?>
												
												<label class="custom-control custom-checkbox">
													<input <?=($preSelectionCustom=="on") ? 'checked':''?> data-amount="<?php echo $add_video;?>" name="<?php echo $filedsName ?>"  type="checkbox" class="custom-control-input add_extra_items">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description"><?php echo $FiledsKey['label'] ?></strong></span>
												</label>
												
											<?php } ?>
											
											  <?php if($FiledsKey['type']=="add_facebook_cover"){ ?>
												
												<?php /*?><label  class="custom-control custom-checkbox">
													<input <?=($preSelectionCustom=="on") ? 'checked':''?> data-amount="<?php echo $add_facebook_cover;?>" name="<?php echo $filedsName ?>"  type="checkbox" class="custom-control-input add_extra_items">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description"><?php echo $FiledsKey['label'] ?></strong></span>
												</label><?php */?>
												
											<?php } ?>
											
											  <?php if($FiledsKey['type']=="vector_psd_pdf"){ ?>
												<input type="file"   name="<?php echo $filedsName ?>" >
                                                
                                                 
                                                <?php 
												
												
												$fileCustomeUrl = customProductImageURL($Filedsvalue);
												
												if(! empty( $Filedsvalue ) && $fileCustomeUrl!=""){
															
															
															$uploadedFileType = strtolower(pathinfo($Filedsvalue,PATHINFO_EXTENSION));
															 
															echo '<div class="customPImages" id="customPImages'.$customeProductFieldsKey."_".$filedsIndex."_".$OrderList['Id'].'">';
															?>
																	<i class="fas fa-times deleteCustomProudctImages"   data-id="<?php echo $OrderList['Id'] ?>" data-product_type="<?php echo $customeProductFieldsKey; ?>" data-fileds_type="<?php echo $filedsIndex; ?>" data-name="<?php echo $Filedsvalue; ?>" data-option_type="<?php echo $filedsPrimaryIndex; ?>" ></i>															<?php 
															echo '<div class="ImagesCustom popup-gallery">';
															if($uploadedFileType=="jpg" || $uploadedFileType=="jpeg" || $uploadedFileType=="png" || $uploadedFileType=="gif"){
																echo '<a href="'.$fileCustomeUrl.'" class="text-white magnificPopup WhiteText"><img src="'.$fileCustomeUrl.'" width="50" height="50"></a>';
															}else{
																echo '<a href="'.$fileCustomeUrl.'" target="_blank" download class="text-white WhiteText"><i class="fa fa-download"></i><br></a>';	
															}
															echo '</div></div>';
														 } 

												?>
										
                                                
											<?php } ?>
											
											  <?php 
											
											  if($FiledsKey['type']=="3d_or_2d"){ 
											  
											  
											  ?>
												  <div style="margin-top:15px;">
												<label class="custom-control custom-radio">
													<input type="radio" name="<?php echo $filedsName ?>" <?=($preSelectionCustom=="2D" || empty($preSelectionCustom)) ? 'checked':''?> class="custom-control-input dimensionalCheck" value="2D">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description">2D</span>
												</label>
												<label class="custom-control custom-radio">
													<input type="radio" name="<?php echo $filedsName ?>" <?=($preSelectionCustom=="3D") ? 'checked':''?> class="custom-control-input dimensionalCheck" value="3D">
													<span class="custom-control-indicator"></span>
													<span class="custom-control-description 3d-desc">3D <strong> (+$<?php echo(formatPrice($price3D)); ?>)</strong></span>
												</label>
											</div>
											
											<?php } ?>
											<?php 
											  if($FiledsKey['type']=="sizes"){ ?>
											
											<div class="row no-gutters customProductSizes d-flex flex-wrap">
												<?php if($product['Defaultsizes']!=""){ ?>
												<div class="col defaultSizeRadio">
													<strong>Default size</strong>
                                                    
                                                   <?php
												 
													$prodOtherSizes = getProdSizeArr();
													$i = 1;
													foreach($prodOtherSizes as $key => $vals) {
														$prodDefaSize = explode(",",$product['Defaultsizes']); ?>
														<?php if(in_array($key,$prodDefaSize)) { ?>
															<label class="custom-control custom-radio">
																<?php if(!empty($preSelectionCustom)){ ?>
																	<input data-name="<?=$vals['name'];?>" type="radio" name="<?php echo $filedsName; ?>" value="<?=$key;?>" class="custom-control-input features defaultSize" <?php if((!empty($preSelectionCustom) && $preSelectionCustom == $key )) { echo "checked"; } ?>>
																<?php }else { ?>
																	<input data-name="<?=$vals['name'];?>"  type="radio" name="<?php echo $filedsName; ?>" value="<?=$key;?>" class="custom-control-input features defaultSize" <?php if(in_array($key,$preSelectionCustom) || (empty($preSelectionCustom) || $preSelectionCustom==1  && $i == 1)) { echo "checked"; } ?>>
																<?php } ?>
																<span class="custom-control-indicator"></span>
																<span class="custom-control-description"><?=$vals['name'];?></span>
															</label>
															<?php $i++; }
													} ?>
													
													
													
													
													
		
															
													
												   
													
												</div>
												<?php }?>
											</div>
											<?php } ?>
											
											
											
											 <?php if($FiledsKey['type']=="turnaround_time"){ ?>
												<select name="<?php echo $filedsName ?>" id="turnaround_time" required>
															<option value="">Select the turn around time</option>
														   <option value="4" <?php if($preSelectionCustom=='4') { echo "selected"; } ?>>24 hours(<?php echo ($customProductDelTimePrice[4]>0) ? "+$". formatPrice($customProductDelTimePrice[4]):"FREE"; ?>)</option>
														   
														   <option value="5" <?php if($preSelectionCustom=='5') { echo "selected"; } ?>>1-2 days (<?php echo ($customProductDelTimePrice[5]>0) ? "$". formatPrice($customProductDelTimePrice[5]):"FREE"; ?>)</option>
															<option value="6" <?php if($preSelectionCustom=='6') { echo "selected"; } ?>>2-3 days (<?php echo ($customProductDelTimePrice[6]>0) ? "$". formatPrice($customProductDelTimePrice[6]):"FREE"; ?>)</option>
															
														   <?php /*?> <option value="3" <?php if($preSelection['deliveryTime']=='3') { echo "selected"; } ?>>2-3 business days (+$<?php echo formatPrice($turnAround3);?>)</option><?php */?>
															
														  <?php /*?>  <option value="4" <?php if($preSelection['deliveryTime']=='4') { echo "selected"; } ?>>3-5 business days (FREE)</option><?php */?>
		
														</select>
											<?php }  
												
												
												?>
                                                
                                                
                                                </div> </div>
                                                
                                                
                                                <?php 
                                                       
														
														
														
														if($filedsIndex=="turnaround_time"){
															$Filedsvalue = $deliArr[$Filedsvalue];
														}
														 if(($filedsIndex=="checkbox_sided" || $filedsIndex=="add_music" || $filedsIndex=="add_video" ||  $filedsIndex=="add_facebook_cover") && $Filedsvalue=="on"){
															 		echo  ' <div class="customer_details ViewInformationData"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> Yes</span></div>';
														
														}
														else if($filedsIndex!="checkbox_sided" && $filedsIndex!="add_music" && $filedsIndex!="add_video" &&  $filedsIndex!="add_facebook_cover"){
														
														if(($filedsIndex=="files" || $filedsIndex=="music_file"  || $filedsIndex =="attach_logo" || $filedsIndex=="attach_any_logos" || $filedsIndex=="attach_your_logo_design" || $filedsIndex=="vector_psd_pdf" || $filedsIndex=="attach_any_pictures"  || $filedsIndex=="attach_any_style_reference") && !empty($Filedsvalue)){
															
															
														  $filesData = array();
															
															if($customeProductFieldsKey=="flyer_design" ||  $customeProductFieldsKey=="3d_logo_conversion" ||  $customeProductFieldsKey=="laptop_skin" || $customeProductFieldsKey=="facebook_cover"  || $customeProductFieldsKey=="business_card" || $customeProductFieldsKey=="logo" || $customeProductFieldsKey=="logo_intro" || $customeProductFieldsKey=="animated_flyer"  || $customeProductFieldsKey=="mixtape_cover_design"){
																$filesData = $Filedsvalue;
															}else{
																if($filedsIndex=="attach_logo" || $filedsIndex=="music_file"  ||  $filedsIndex=="attach_any_logos" || $filedsIndex=="attach_your_logo_design" || $filedsIndex=="vector_psd_pdf" || $filedsIndex=="attach_any_pictures"  || $filedsIndex=="attach_any_style_reference"){
																	$filesData[] = $Filedsvalue;
																}else{
																	$filesData = $Filedsvalue;
																		
																}
															}
															if($filedsIndex=="music_file"){
																$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'] = "Music File";		
															}
														
															if(!empty($filesData)){
															
															echo  ' <div class="customer_details ViewInformationData"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong></span></div>';		
														
														echo '<div class="customFiles  ViewInformationData popup-gallery">';
														foreach($filesData as $files){
														$fileCustomeUrl = customProductImageURL($files);

														if(! empty( $files ) && $fileCustomeUrl!=""){
															
															
															$uploadedFileType = strtolower(pathinfo($files,PATHINFO_EXTENSION));
															 
															echo '<div class="ImagesCustom">';
															if($uploadedFileType=="jpg" || $uploadedFileType=="jpeg" || $uploadedFileType=="png" || $uploadedFileType=="gif"){
																echo '<a href="'.$fileCustomeUrl.'" class="text-white magnificPopup WhiteText"><img src="'.$fileCustomeUrl.'" width="50" height="50"></a>';
															}else{
																echo '<a href="'.$fileCustomeUrl.'" target="_blank" download class="text-white WhiteText"><i class="fa fa-download"></i><br></a>';	
															}
															echo '</div>';
														 } 

														}
								    					echo '</div>';
															}
														}
															
														//echo "filedsIndex".$filedsIndex;	
													  	//echo $filedsIndex."=>".$Filedsvalue."<br>";
														if($Filedsvalue!=""  && $filedsIndex!="music_file"  && $filedsIndex!="sizes" &&  $filedsIndex!="attach_any_logos" && $filedsIndex!="attach_your_logo_design" && $filedsIndex!="vector_psd_pdf" && $filedsIndex!="attach_any_pictures"  && $filedsIndex!="attach_any_style_reference"  && $filedsIndex!="files" && $filedsIndex!="attach_logo"){	
														 	
															echo  ' <div class="customer_details ViewInformationData"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> '.$Filedsvalue.'</span></div>';
														}else if($filedsIndex=="sizes"){
															$secondary_options_type = "primary_options";
															if($customeProductFieldsKey=="animated_flyer"){
																$secondary_options_type= "secondary_options";	
															}
															$Filedsvalue = $customeProductsFileds[$customeProductFieldsKey][$secondary_options_type]['defaultSize'];
																echo  ' <div class="customer_details ViewInformationData"><strong>Default Size: </strong> '.$prodOtherSizes[$Filedsvalue]['name'].'</span></div>';
															
																
														}
														else if($filedsIndex=="otherSize" && !empty($Filedsvalue)){
																	$otherSize = "";
																	foreach($Filedsvalue as $singleSize){
																		$otherSize .= $prodOtherSizes[$singleSize]['name'].", ";
																	}
																	
																echo  ' <div class="customer_details ViewInformationData"><strong>Other Sizes: </strong> '.rtrim($otherSize,", ").'</span></div>';
														}
														}
													}
													
													}
												}
											?>
                                            <div class="EditableInformationButton">
                                                <input type="button" data-id="<?php echo $OrderList['Id']; ?>" class="UpdateInformation" value="Submit">
                                                <input type="button" class="CancelInformation" onClick="OpenInstructionsProduct('<?php echo $ID; ?>')" value="Cancel">
                                            </div>
                                            </form>
                                            <?php 	
											}
						
							
						
							
							?>
                            
                            <?php 		
						}else{
							
							if($OrderList['parent_product_id']>0){
							
							 $productDataFacebook = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$OrderList['parent_product_id']."'");
							 $getSettingFormFileds = getSettingFormFields($productDataFacebook['parent_product_cat_id'],$productDataFacebook['child_product_cat_id']);	
								
							}else{
								$productData = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$OrderList['ProductID']."'");
								$getSettingFormFileds = getSettingFormFields($productData['parent_product_cat_id'],$productData['child_product_cat_id']);
							}
						 ?>
                          <form method="post" action="" enctype="multipart/form-data">
                           <input type="hidden"  name="FormInformation[none]">
                           
                            <div class="customer_details updateInformationMsg" >
                                <strong>Information has been successfully updated. </strong> 
                                </div>
                               
                         <div class="editInformation"><input type="button" value="Edit Information"></div>
                         
						 <?php 
						 
						 if(!empty($getSettingFormFileds)){
							if($getSettingFormFileds['presenting']==1){ ?>
                                            <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Presenting:</strong>
                                            <input type="text" value="<?php echo $presenting ?>" name="FormInformation[presenting]" class="form-control mb-3">
                                            </div>
                                            </div>
											<?php 	
											}
											if($getSettingFormFileds['main_title']==1){
												?>
                                            <div class="EditInformationData">
                           					<div class="customer_details">
                             				  <strong>Main title:</strong>
                                             	<input type="text" value="<?php echo $MainTitle ?>" name="FormInformation[MainTitle]" class="form-control mb-3">
                                             </div>
                                             </div>
                                          
                                                
                                             <?php    	
											}
											
											if($getSettingFormFileds['single_title']==1){
											?>
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                             <strong>Single title:</strong>

                                            <input type="text" value="<?php echo $Singletitle ?>" name="FormInformation[Singletitle]" class="form-control mb-3">
											</div>
                                            </div>		
                                           <?php }	if($getSettingFormFileds['sub_title']==1){ ?>
                                             <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Sub title:</strong>
                                            <input type="text" value="<?php echo $Subtitle ?>" name="FormInformation[Subtitle]" class="form-control mb-3">
                                            </div>
                                            </div>
											<?php 	
											}if($getSettingFormFileds['deejay_name']==1){ ?>
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Deejay Name:</strong>
                                            <input type="text" value="<?php echo $deejay_name ?>" name="FormInformation[deejay_name]" class="form-control mb-3">
                                            </div>
                                            </div>
											<?php 	
											}if($getSettingFormFileds['ename']==1){ ?>
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Name:</strong>
                                            <input type="text" value="<?php echo $ename ?>" name="FormInformation[ename]" class="form-control mb-3">
                                            </div>
                                            </div>
											<?php 	
											}
											
											if($getSettingFormFileds['date']==1){
											 ?>
                                               <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Date:</strong>
                                            <input type="text" value="<?php echo $EventDate ?>" name="FormInformation[EventDate]"class="form-control mb-3 datepicker" autocomplete="off">
                                             </div>
                                             </div>
                                              <?php
											}
											
											if($getSettingFormFileds['mixtape_name']==1){
											 ?>
                                               <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Mixtape name:</strong>
                                            <input type="text" value="<?php echo $Mixtapename ?>" name="FormInformation[Mixtapename]" class="form-control mb-3">
                                           </div>
                                           </div>
                                             
                                             <?php } 
											 if($getSettingFormFileds['produced_by']==1){
											 ?>
                                               <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                              <strong>Produced by:</strong>
                                            <input type="text" value="<?php echo $ProducedBy ?>" name="FormInformation[ProducedBy]" class="form-control mb-3">
                                          	</div>
                                            </div>
                                             <?php } 
											  if($getSettingFormFileds['artist_name']==1){
											 ?>
                                               <div class="EditInformationData">
                           					<div class="customer_details">
                                               <strong>Artist name:</strong>
                                            <input type="text" value="<?php echo $ArtistName ?>" name="FormInformation[ArtistName]" class="form-control mb-3">
                                            </div></div>
                                             <?php } 
											if($getSettingFormFileds['music_by']==1){
											?>
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                               <strong>Music by:</strong>
                                            <input type="text" value="<?php echo $MusicBy ?>" name="FormInformation[MusicBy]" class="form-control mb-3">
											</div>
                                            </div>		
                                            <?php 	
											}
											if($getSettingFormFileds['own_song']==1){
												?>
                                               <div class="EditInformationData">
                           						<div class="customer_details">
                            
                                                
                                                <strong>Own song:</strong>
                                                 <input type="file" name="FormInformation[own_song]" id="own_song"  data-name="own_song" />
                                                  <?php 
											  if(!empty($ownSong)) {?>
                            <div class="customer_details">
									<div class="venueLogos" id="ownSong<?php echo $OrderList['Id'] ?>">
                                    	 <div class="popup-gallery">
                                    		<?php 
											$ext = pathinfo($ownSong, PATHINFO_EXTENSION);
												if(!in_array($ext,$allowed)) {
											?>
                                            	<div class="venueLogosFilesDownload">
                                            <a href="<?=SITEURL.$ownSong;?>" target="_blank" class="text-white WhiteText">
                                   				  <i class="fas fa-file pr-2"></i> <?php echo basename($ownSong); ?> </a><i class="fas deleteFiles fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','ownSong')"></i>   	
                                            </div>
											<?php }else{ ?>
                                            	<div class="venueLogosImges">
                                          <i class="fas deleteImages fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','ownSong')"></i> 
                                   		
                                            <a href="<?=SITEURL.$ownSong;?>" class="text-white magnificPopup WhiteText">
                                   					<img src="<?=SITEURL.$ownSong;?>">
                                                </a>  
                                            </div>
                                            <?php } ?>
                                   		 </div>
                                    </div>
                                    </div>	
                              	
                                

                            
                        <?php } ?>
                                                </div></div>
                                               <?php  	
											}
											if($getSettingFormFileds['additional_info']==1){
											?>
                                               <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Additional info: (limit 500 characters)</strong>
                                            <textarea name="FormInformation[MoreInfo]" class="form-control mb-3" maxlength="500"><?=$MoreInfo;?></textarea>
                            				</div>
                                            </div>               

                                            <?php 	
											}
											 if($getSettingFormFileds['requirements_note']==1){ ?> 
                                               <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                             <strong>Notes to graphic designer:</strong>
                                            <textarea name="FormInformation[requirement_note]"  class="form-control mb-3" maxlength="500"><?php echo $requirement_note; ?></textarea>
                                            </div>
                                            </div>
											<?php } 
											if($getSettingFormFileds['venue']==1){
											?>
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                             <strong>Venue:</strong>
                                            <input type="text" name="FormInformation[Venue]" value="<?=$Venue;?>" class="form-control mb-3">
											</div>
                                            </div>
                                            	
											<?php }
											
											if($getSettingFormFileds['address']==1){
											?>
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                             <strong>Address:</strong>
                                            <input type="text" name="FormInformation[Address]" value="<?=$Address;?>" class="form-control mb-3">
											</div>
                                            </div>
                                            <?php }
											if($getSettingFormFileds['music']==1){
											
											?>
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
										  <strong>Music:</strong>
                                            <input type="text" name="FormInformation[Music]" value="<?=$Music;?>" class="form-control mb-3">
											</div>
                                            </div>
										<?php 
											}
											if($getSettingFormFileds['logo']==1){
											?>
                                            
                                            
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            
                                              <strong><?php if($ProductTypeOrder==12){echo "Logo"; }else{echo 'Venue Logo';}?>:</strong>
                                            <input type="file" name="FormInformation[venue_logo]" id="venue_logo"  data-name="venue_logo" />
                                            
                                            </div>
                                               <?php 
											  if(!empty($venue_logo)) {?>
                            <div class="customer_details">
									<div class="venueLogos" id="venue_logo<?php echo $OrderList['Id'] ?>">
                                    	 <div class="popup-gallery">
                                    		<?php 
											$ext = pathinfo($venue_logo, PATHINFO_EXTENSION);
												if(!in_array($ext,$allowed)) {
											?>
                                            	<div class="venueLogosFilesDownload">
                                            <a href="<?=SITEURL.$venue_logo;?>" target="_blank" class="text-white WhiteText">
                                   				  <i class="fas fa-file pr-2"></i> <?php echo basename($venue_logo); ?> </a><i class="fas deleteFiles fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','venue_logo')"></i>   	
                                            </div>
											<?php }else{ ?>
                                            	<div class="venueLogosImges">
                                          <i class="fas deleteImages fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','venue_logo')"></i> 
                                   		
                                            <a href="<?=SITEURL.$venue_logo;?>" class="text-white magnificPopup WhiteText">
                                   					<img src="<?=SITEURL.$venue_logo;?>">
                                                </a>  
                                            </div>
                                            <?php } ?>
                                   		 </div>
                                    </div>
                                    </div>	
                              	
                                

                            
                        <?php } ?>
                                            </div>
											<?php } if($getSettingFormFileds['phonenumber']==1){ ?>
                                            
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Phone number:</strong>
                                            <input type="text" name="FormInformation[PhoneNumber]" value="<?=$PhoneNumber;?>" class="form-control mb-3">
                                             </div>
                                             </div>
                                              <?php } if($getSettingFormFileds['email']==1){ ?>
                                           
                                             <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Email:</strong>
                                            <input type="email" required name="FormInformation[VenueEmail]" value="<?=$VenueEmail;?>" class="form-control mb-3">
                                            </div>
                                            </div>
                                              <?php } if($getSettingFormFileds['facebook']==1){ ?>
                                             <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Facebook:</strong>
                                            <input type="text" name="FormInformation[Facebook]" value="<?=$Facebook;?>" class="form-control mb-3">
                                              </div>
                                              </div>
											  <?php } if($getSettingFormFileds['instagram']==1){ ?>
											  <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Instagram:</strong>
                                            <input type="text" name="FormInformation[Instagram]" value="<?=$Instagram;?>" class="form-control mb-3">
                                              </div>
                                             </div>
											  <?php } if($getSettingFormFileds['twitter']==1){ ?>
											  <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Twitter:</strong>
                                            <input type="text" name="FormInformation[Twitter]" value="<?=$Twitter;?>" class="form-control mb-3">
                                              </div>
                                              </div>
											  <?php }  ?>
                                            <?php 
										}
                            else{ ?>

																			
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Main title:</strong>
                                            <input type="text" name="FormInformation[MainTitle]" value="<?=$MainTitle;?>" class="form-control mb-3">
                                             </div>
                                             </div>
                                               <div class="EditInformationData">
                           					<div class="customer_details">
                              
                                            <strong>Sub title:</strong>
                                            <input type="text" name="FormInformation[Subtitle]" value="<?=$Subtitle;?>" class="form-control mb-3">
                                            </div>
                                            </div>
                                              <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Date:</strong>
                                            <input type="text" name="FormInformation[EventDate]" value="<?=$EventDate;?>" class="form-control mb-3 datepicker">
                                           </div>
                                           </div>
                                             <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Music by:</strong>
                                            <input type="text" name="FormInformation[MusicBy]" value="<?=$MusicBy;?>" class="form-control mb-3">
                                                           </div>
                                           </div>
                                             <div class="EditInformationData">
                           						<div class="customer_details">
                            
                                                
                                                <strong>Own song:</strong>
                                                 <input type="file" name="FormInformation[own_song]" id="own_song"  data-name="own_song" />
                                                  <?php 
											  if(!empty($ownSong)) {?>
                            <div class="customer_details">
									<div class="venueLogos" id="ownSong<?php echo $OrderList['Id'] ?>">
                                    	 <div class="popup-gallery">
                                    		<?php 
											$ext = pathinfo($ownSong, PATHINFO_EXTENSION);
												if(!in_array($ext,$allowed)) {
											?>
                                            	<div class="venueLogosFilesDownload">
                                            <a href="<?=SITEURL.$ownSong;?>" target="_blank" class="text-white WhiteText">
                                   				  <i class="fas fa-file pr-2"></i> <?php echo basename($ownSong); ?> </a><i class="fas deleteFiles fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','ownSong')"></i>   	
                                            </div>
											<?php }else{ ?>
                                            	<div class="venueLogosImges">
                                          <i class="fas deleteImages fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','ownSong')"></i> 
                                   		
                                            <a href="<?=SITEURL.$ownSong;?>" class="text-white magnificPopup WhiteText">
                                   					<img src="<?=SITEURL.$ownSong;?>">
                                                </a>  
                                            </div>
                                            <?php } ?>
                                   		 </div>
                                    </div>
                                    </div>	
                              	
                                

                            
                        <?php } ?>
                                                </div></div>
                                             <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>More info: (limit 500 characters)</strong>
                                            <textarea name="FormInformation[MoreInfo]" class="form-control mb-3" maxlength="500"><?=$MoreInfo;?></textarea>
                                                           </div>
                                           </div>
                                             <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Notes to graphic designer:</strong>
                                            <textarea name="FormInformation[requirement_note]" value="<?=$requirement_note;?>" class="form-control mb-3" maxlength="500"></textarea>
                                                          </div>
                                           </div>
                                             <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Venue:</strong>
                                            <input type="text" name="FormInformation[Venue]" value="<?=$Venue;?>" class="form-control mb-3">
                                                           </div>
                                           </div>
                                             <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            
                                              <strong><?php if($ProductTypeOrder==12){echo "Logo"; }else{echo 'Venue Logo';}?>:</strong>
                                                <input type="file" name="FormInformation[venue_logo]" id="venue_logo"  data-name="venue_logo" />
                                               
                                            </div>
                                               <?php 
											  if(!empty($venue_logo)) {?>
                       										     <div class="customer_details">
									<div class="venueLogos" id="venue_logo<?php echo $OrderList['Id'] ?>">
                                    	 <div class="popup-gallery">
                                    		<?php 
											$ext = pathinfo($venue_logo, PATHINFO_EXTENSION);
												if(!in_array($ext,$allowed)) {
											?>
                                            	<div class="venueLogosFilesDownload">
                                            <a href="<?=SITEURL.$venue_logo;?>" target="_blank" class="text-white WhiteText">
                                   				  <i class="fas fa-file pr-2"></i> <?php echo basename($venue_logo); ?> </a><i class="fas deleteFiles fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','venue_logo')"></i>   	
                                            </div>
											<?php }else{ ?>
                                            	<div class="venueLogosImges">
                                          <i class="fas deleteImages fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','venue_logo')"></i> 
                                   		
                                            <a href="<?=SITEURL.$venue_logo;?>" class="text-white magnificPopup WhiteText">
                                   					<img src="<?=SITEURL.$venue_logo;?>">
                                                </a>  
                                            </div>
                                            <?php } ?>
                                   		 </div>
                                    </div>
                                    </div>	
                              				   <?php } ?>
                                            </div>
                                             <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Address:</strong>
                                            <input type="text" name="FormInformation[Address]" value="<?=$Address;?>" class="form-control mb-3">

											</div>
                                            </div>			
                                        <?php  }
						 ?>
                         <div class="EditInformationData">
                           					<div class="customer_details">
                            
                                            <strong>Photos and logos (Multiple):</strong>
                                                <input type="file" class="photos_and_logo" multiple name="FormInformation[photos_and_logo][]" id="photos_and_logo"  data-name="photos_and_logo" />
                                              
                                          
                           </div>
                     <?php 
					
					 $filesImages = $OrderList['filesImages'];
						$filesImagesArr = array();
						if(!empty($filesImages)){
								$filesImagesArr = unserialize($OrderList['filesImages']);
						}
					 if(!empty($filesImagesArr)) {?>

                       				<?php foreach($filesImagesArr as $singleImageKey=>$singleImageValue){
									if(file_exists( SITE_BASE_PATH . $singleImageValue )){ ?>
                                    								     
									<div class="venueLogos FilenameBlock" id="Filename<?php echo $singleImageKey; ?><?php echo $OrderList['Id'] ?>">
                                    	 <div class="popup-gallery">
                                    		<?php 
											$ext = pathinfo($singleImageValue, PATHINFO_EXTENSION);
												if(!in_array($ext,$allowed)) {
											?>
                                            	<div class="venueLogosFilesDownload">
                                            <a href="<?=SITEURL.$singleImageValue;?>" target="_blank" class="text-white WhiteText">
                                   				  <i class="fas fa-file pr-2"></i> <?php echo basename($singleImageValue); ?> </a><i class="fas deleteFiles fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','Filename<?php echo $singleImageKey; ?>')"></i>   	
                                            </div>
											<?php }else{ ?>
                                            	<div class="venueLogosImges">
                                          <i class="fas deleteImages fa-times pr-2" onClick="deleteFiles('<?php echo $OrderList['Id'] ?>','Filename<?php echo $singleImageKey?>')"></i> 
                                   		
                                            <a href="<?=SITEURL.$singleImageValue;?>" class="text-white magnificPopup WhiteText">
                                   					<img src="<?=SITEURL.$singleImageValue;?>">
                                                </a>  
                                            </div>
                                            <?php } ?>
                                   		
                                    </div>
                                    </div>	
                                    <?php }} 
                                     } ?>                                 
                                                           
                                           </div>
						 <?php 
						 
						 if(!empty($presenting)) {?>
                            <div class="customer_details ViewInformationData">
                                <strong>Presenting: </strong> <span id="presenting"><?php echo $presenting; ?></span>
                            </div>
                           
                        <?php } 
						 if(!empty($MainTitle)) {?>
                            <div class="customer_details ViewInformationData">
                                <strong>Main title: </strong> <span id="MainTitle"><?php echo $MainTitle; ?></span>
                            </div>
                            
                        <?php } ?>

                        
						<?php if(!empty($Mixtapename)) {?>
                             <div class="customer_details ViewInformationData">
                                <strong>Mixtape name: </strong> <span id="Mixtapename"><?php echo $Mixtapename; ?></span>
                            </div>
                               
                        <?php } ?>
                        
                        <?php if(!empty($Singletitle)) {?>
                              <div class="customer_details ViewInformationData">
                                <strong>Single title: </strong> <span id="Singletitle"><?php echo $Singletitle; ?></span>
                            </div>
                               
                        <?php } ?>
                        

                        <?php if(!empty($Subtitle)) {?>
                             <div class="customer_details ViewInformationData">
                                <strong>Sub title: </strong> <span id="Subtitle"><?php echo $Subtitle; ?></span>
                            </div>
                               
                        <?php } ?>
                        
                        
                          <?php if(!empty($deejay_name)) {?>
                              <div class="customer_details ViewInformationData">
                                <strong>Deejay Name: </strong> <span id="deejay_name"><?php echo $deejay_name; ?></span>
                            </div>
                              
                        <?php } ?>
                        
                        
                            <?php if(!empty($ename)) {?>
                               <div class="customer_details ViewInformationData">
                                <strong>Name: </strong> <span id="ename"><?php echo $ename; ?></span>
                            </div>
                               
                        <?php } ?>
                        
                        
                            
                        
                        


                        <?php if(!empty($EventDate)) {?>
                             <div class="customer_details ViewInformationData">
                                <strong>Date: </strong> <span id="EventDate"><?php echo $EventDate; ?></span>
                            </div>
                               
                        <?php } ?>

                        <?php if(!empty($MusicBy)) {?>
                              <div class="customer_details ViewInformationData">
                                <strong>Music by: </strong> <span id="MusicBy"><?php echo $MusicBy; ?></span>
                            </div>
                               
                        <?php } ?>

                        <?php if(! empty( $ownSong ) && file_exists( SITE_BASE_PATH . $ownSong )){ ?>
                            <div class="customer_details ViewInformationData">
                                <strong>Own song: </strong> <a style="color:#FFF;" href="<?=SITEURL.$ownSong;?>" target="_blank">Download</a>
                            </div>
                        <?php } ?>

                        <?php if(!empty($ArtistName)) {?>
                             <div class="customer_details ViewInformationData">
                                <strong>Artist name: </strong> <span id="ArtistName"><?php echo $ArtistName; ?></span>
                            </div>
                                
                        <?php } ?>

                        <?php if(!empty($ProducedBy)) {?>
                              <div class="customer_details ViewInformationData">
                                <strong>Produced by: </strong> <span id="ProducedBy"><?php echo $ProducedBy; ?></span>
                            </div>
                               
                        <?php } ?>

                        <?php if(!empty($PhoneNumber)) {?>
                             <div class="customer_details ViewInformationData">
                                <strong>Phone number: </strong> <span id="PhoneNumber"><?php echo $PhoneNumber; ?></span>
                            </div>
                             
                        <?php } ?>

                        <?php if(!empty($VenueEmail)) {?>
                            <div class="customer_details ViewInformationData">
                                <strong>Email: </strong> <span id="VenueEmail"><?php echo $VenueEmail; ?></span>
                            </div>
                              
                        <?php } ?>

                        <?php if(!empty($Facebook)) {?>
                             <div class="customer_details ViewInformationData">
                                <strong>Facebook: </strong> <span id="Facebook"><?php echo $Facebook; ?></span>
                            </div>
                               
                        <?php } ?>

                        <?php if(!empty($Instagram)) {?>
                              <div class="customer_details ViewInformationData">
                                <strong>Instagram: </strong> <span id="Instagram"><?php echo $Instagram; ?></span>
                            </div>
                               
                        <?php } ?>

                        <?php if(!empty($Twitter)) {?>
                              <div class="customer_details ViewInformationData">
                                <strong>Twitter: </strong> <span id="Twitter"><?php echo $Twitter; ?></span>
                            </div>
                               
                        <?php } ?>

                        <?php if(!empty($Music)) {?>
                              <div class="customer_details ViewInformationData">
                                <strong>Music: </strong> <span id="Music"><?php echo $Music; ?></span>
                            </div>
                               
                        <?php } ?>

                        <?php if(!empty($MoreInfo)) {?>
                              <div class="customer_details ViewInformationData">
                                <strong>More info: </strong>
                                <span class="d-block" id="MoreInfo"><?php echo $MoreInfo; ?></span>
                            </div>
                                
                        <?php } ?>

                        <?php if(!empty($requirement_note)) {?>
                            <div class="customer_details ViewInformationData">
                                <strong>Notes to graphic designer: </strong>
                                <span class="d-block" id="requirement_note"><?php echo $requirement_note; ?></span>
                            </div>
                              
                        <?php } ?>


                        <?php if(!empty($Venue)) {?>
                             <div class="customer_details ViewInformationData">
                                <strong>Venue: </strong> <span id="Venue"><?php echo $Venue; ?></span>
                            </div>
                                
                        <?php } ?>

                        <?php if(!empty($venue_logo)) {?>
                            <div class="customer_details ViewInformationData">

                                <strong><?php if($ProductTypeOrder==12){echo "Logo"; }else{echo 'Venue Logo';}?>: </strong>

                                <ul class="list-unstyled mb-0 pl-4 popup-gallery">

                                    <?php
									$ext = pathinfo($venue_logo, PATHINFO_EXTENSION);
									if(!in_array($ext,$allowed)) {
										?>
                                        <li id="eImage1"><a href="<?=SITEURL.$venue_logo;?>" target="_blank" class="text-white WhiteText">
                                   
                                    
                                    <i class="fas fa-file pr-2"></i> <?php echo basename($venue_logo); ?><?php ?></a>   <a href="<?=SITEURL.$venue_logo;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> </li>
                                                <?php 
											}else{
									 ?>
                                    
                                    <li id="eImage1"><a href="<?=SITEURL.$venue_logo;?>" class="text-white magnificPopup WhiteText">
                                   
                                    
                                    <i class="fas fa-image pr-2"></i> image.jpg<?php ?></a>   <a href="<?=SITEURL.$venue_logo;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> </li>
                                    <?php } ?>

                                </ul>

                            </div>
                        <?php } ?>

                        <?php if(!empty($Address)) {?>
                              <div class="customer_details ViewInformationData">
                                <strong>Address: </strong> <span id="Address"><?php echo $Address; ?></span>
                            </div>
                               
                        <?php } ?>

                        <?php 
						
						$filesImages = $OrderList['filesImages'];
						$filesImagesArr = array();
						if(!empty($filesImages)){
								$filesImagesArr = unserialize($OrderList['filesImages']);
						}
						
						if(!empty($filesImagesArr)) {?>
                            <div class="customer_details ViewInformationData">
                                <strong>Photos and logos: </strong>
                                <ul class="list-unstyled mb-0 pl-4 popup-gallery">

                                    <?php foreach($filesImagesArr as $singleImageKey=>$singleImageValue){
									if(file_exists( SITE_BASE_PATH . $singleImageValue )){?>
                                    
                                    		
                                        <li id="eImage1">
                                        <?php 
										$ext = pathinfo($singleImageValue, PATHINFO_EXTENSION);
										if(!in_array($ext,$allowed)) {
										?>
                                        <a href="<?=SITEURL.$singleImageValue;?>" target="_blank" class="text-white WhiteText">
                                   
                                    
                                    <i class="fas fa-file pr-2"></i> <?php echo basename($singleImageValue); ?><?php ?></a>   <a href="<?=SITEURL.$singleImageValue;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> 
                                        
                                        <?php }else{ ?>
                                        <a href="<?=SITEURL.$singleImageValue;?>" class="text-white magnificPopup WhiteText"><i class="fas fa-image pr-2"></i> image.jpg</a><a href="<?=SITEURL.$singleImageValue;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a>
                                        <?php } ?>
                                        
                                        </li>
                                    <?php } } ?>

                                   
                                </ul>
                            </div>
                        <?php } 
						?>
                        	<div class="EditableInformationButton">
                            	<input type="button" data-id="<?php echo $OrderList['Id']; ?>" class="UpdateInformation" value="Submit">
                            	<input type="button" class="CancelInformation" onClick="OpenInstructionsProduct('<?php echo $ID; ?>')" value="Cancel">
                            </div>
                            </form>
                        <?php 
						}?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
        <?php 
		
		$content =  ob_get_clean();
		
		echo json_encode(array("html"=>$content));
		exit;
		
	}
	
?>