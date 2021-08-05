<?php

	ob_start();

	require_once '../function/constants.php';

	require_once '../function/configClass.php';

	require_once '../function/siteFunctions.php';


	$AccessType = $_SESSION['userType'];
	$firstTime = " and FirstTime='no'";
	if(isset($_REQUEST['DisplayPage']) && $_REQUEST['DisplayPage']=="admin"){
		$firstTime = "";
	}

	if($_REQUEST['type']=="static"){
						$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$_REQUEST['id']."'  and `ProductID` = '".$_REQUEST['ProductID']."'   AND TypeBanner='static' ".$firstTime."  and Size='".$_REQUEST['size']."'", "ID", "DESC");
					}else if($_REQUEST['type']=="motion"){
						$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$_REQUEST['id']."'  and `ProductID` = '".$_REQUEST['ProductID']."' ".$firstTime."  AND TypeBanner='motion'", "ID", "DESC");
					}else{
						$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$_REQUEST['id']."' and TypeBanner='default' ".$firstTime." AND `ProductID` = '".$_REQUEST['ProductID']."'", "ID", "Desc");
					}


	$titleBlock ="Revisions (".count($selectChangeRequest).")";

	if(!empty($selectChangeRequest)){
		$counter =1;
		$allowed =  array('gif','png' ,'jpg','webp');
		foreach($selectChangeRequest as $singleKey=>$singleValue){
			$productData = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$singleValue['ProductID']."'");
					$dynamic_dir = "uploads/work/";
						$hoursSelected = "";
						if($singleValue['24HoursChk']==1){
							//$hoursSelected = "24 hours to complete the revision";

						}
						if($singleValue['Type']=="user" ){
							$AccessData = GetSglRcrdOnCndi(USERS, "UserID = '".$singleValue['UserID']."'");
							$uplodedBy = $AccessData['FName']." ".$AccessData['LName'];
						}

						if($singleValue['Type']=="admin"){
							$AccessData = GetSglRcrdOnCndi(USERS, "UserID = '".$singleValue['DesignerID']."'");
							$uplodedBy = "Flashy Flyers";
						}
						if($singleValue['Type']=="super_admin"){
							$AccessData = GetSglRcrdOnCndi(USERS, "UserID = '".$singleValue['AdminID']."'");
							$uplodedBy =  $AccessData['FName']." ".$AccessData['LName']. " (Admin)";
						}
						$html.= '<div class="customer_content">
                            <div class="customer_post">
                               ('.$counter.') From '.$uplodedBy.' ('.date('m/d/Y g:i A',$singleValue['CreationDate']).')

                            </div>
                            <div class="customer_title">
                                '.$productData['Title'].'
                            </div>
                            <div class="customer_desc">
                                <div class="MessageText">'.$singleValue['MessageText']."</div>";
								if(!empty($singleValue['Attachment'])) {

									$attchementUnSerialize = unserialize($singleValue['Attachment']);


									if(is_array($attchementUnSerialize) && count($attchementUnSerialize)>0){
										foreach($attchementUnSerialize as $imageKey=>$image){
												$ext = pathinfo($image, PATHINFO_EXTENSION);

												 $imageSrc = "";
													 $imageSrc = OrderImageURL($image);

													if(in_array(strtoupper($ext),$allowedVideo)) {
													$html .='<span class="customer_icon galleryListing  popup-gallery"><video style="vertical-align: text-top; margin-top:2px;" width="100" height="100" controls>
                                                                <source src="'.$imageSrc.'" type="video/mp4">
                                                            </video></span>';

												}else if(in_array($ext,$allowed)){
                                                        $CheckThumbfileName = str_replace(".".$ext,"",$image);
														$imageFileSrc = $imageSrc;
														$html .='<span  class="customer_icon  galleryListing  popup-gallery"><a class="imageget" href="'.$imageSrc.'" alt="">'.OrderImageSrc($image,"354").'</a></span>';
												 }
												 else{
													$html .='<span class="customer_icon galleryListing"><a href="'.$imageSrc.'" download><img  src="'.SITEURL.'/images/download_file_White.png"></a></span>';
													}

										if($singleValue['Type']=="user" && $AccessType=="user"){
										//	$html .='<i class="fas fa-times  deletedRevisionImages" data-id="'.$singleValue['ID'].'" data-imagekey="'.$imageKey.'"></i>';
										}

										if($singleValue['Type']=="admin" && $AccessType=="designer"){
										//	$html .='<i class="fas fa-times  deletedRevisionImages" data-id="'.$singleValue['ID'].'" data-imagekey="'.$imageKey.'"></i>';
										}
									$html .='</span>';
									}
									$html .='<div><input type="button" value="Download" class="downloadAssets"
									style="border-radius: 5px;
											border: none;
											font-size: 22px;">
											</div>';
								}
								}
								$html .= '
								<script>
								$(".popup-gallery").magnificPopup({
           							delegate: "a",
									type: "image",
									mainClass: "mfp-img-mobile",
									gallery: {
										verticalFit: true
									},
								});

								$(".downloadAssets").unbind().click(function() {
									var imgsrc = $(".imageget").attr("href");
									var urls = [];
									urls=[];

									$(".imageget").each(function(){
											var href = $(this).attr("href");
											urls.push(href)
									});
									for (var i in urls) {
											download(urls[i]);
									}
								});
								function download(item) {
									var fileName = item.split(/(\\|\/)/g).pop();

									var image = new Image();
									image.crossOrigin = "Anonymous";
									image.src = item;
									image.onload = function() {

										// use canvas to load image
										var canvas = document.createElement("canvas");
										canvas.width = this.naturalWidth;
										canvas.height = this.naturalHeight;
										canvas.getContext("2d").drawImage(this, 0, 0);

										// grab the blob url
										var blob;
										if (image.src.indexOf(".jpg") > -1) {
											blob = canvas.toDataURL("image/jpeg");
										} else if (image.src.indexOf(".png") > -1) {
											blob = canvas.toDataURL("image/png");
										} else if (image.src.indexOf(".gif") > -1) {
											blob = canvas.toDataURL("image/gif");
										} else {
											blob = canvas.toDataURL("image/png");
										}

										// create link, set href to blob
										var a = document.createElement("a");
										a.title = fileName;
										a.href = blob;
										a.style.display ="none";
										a.setAttribute("download", fileName);
										a.setAttribute("target", "_blank");
										document.body.appendChild(a);

										// click item
										a.click();
									}
								}
								</script>
                            </div>
                        </div>';
						$counter++;


		}
	}else{
		$html = "Sorry you have not any request of rivision";
	}



	$myarray = array("message" =>$html,"titleBlock"=>$titleBlock);

	echo json_encode($myarray);
?>
