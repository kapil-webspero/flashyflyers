<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	$status = $html = "";

	if(is_login()) {

		$AccessID = intval($_SESSION['userId']);
		$AccessType = $_SESSION['userType'];
		$allowed =  array('gif','png' ,'jpg','webp');
		$orderIID = intval($_REQUEST['orderIID']);
		if(!empty($orderIID) && $orderIID > 0) {
			if(GetNumOfRcrdsOnCndi(ORDER, "`Id` = '$orderIID' AND `AssignedTo` = '$AccessID'")>0)
			{
				$orderData = GetSglRcrdOnCndi(ORDER, "`Id` = '$orderIID'");

				if(GetNumOfRcrdsOnCndi(CHANGE_REQ, "`OrderID` = '$orderIID' AND `DesignerID` = '$AccessID'")>0) {
					if($_REQUEST['type']=="static"){
						$getChangesArr = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '$orderIID' AND TypeBanner='static' and Size='".$_REQUEST['size']."' AND `DesignerID` = '$AccessID'", "ID", "DESC");
					}else if($_REQUEST['type']=="motion"){
						$getChangesArr = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '$orderIID' AND TypeBanner='motion' AND `DesignerID` = '$AccessID'", "ID", "DESC");
					}else{
						$getChangesArr = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '$orderIID' and TypeBanner='default' AND  `DesignerID` = '$AccessID'", "ID", "DESC");
					}
					$titleBlock ="Revisions (".count($getChangesArr).")";
					$counter=1;
					foreach($getChangesArr as $changesReq) {
						$productData = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$changesReq['ProductID']."'");
						$dynamic_dir = "uploads/work/";

						$hoursSelected = "";
						if($changesReq['24HoursChk']==1){
							//$hoursSelected = "24 hours to complete the revision";

						}
						if($changesReq['Type']=="user" ){
							$AccessData = GetSglRcrdOnCndi(USERS, "UserID = '".$changesReq['UserID']."'");
							$uplodedBy = $AccessData['FName']." ".$AccessData['LName'];
						}

						if($changesReq['Type']=="admin"){
							$AccessData = GetSglRcrdOnCndi(USERS, "UserID = '".$changesReq['DesignerID']."'");
							$uplodedBy = "Flashy Flyers";
						}

						if($changesReq['Type']=="super_admin"){
							$AccessData = GetSglRcrdOnCndi(USERS, "UserID = '".$changesReq['AdminID']."'");
							$uplodedBy =  $AccessData['FName']." ".$AccessData['LName']. " (Admin)";
						}

						$html.= '<div class="customer_content">
                            <div class="customer_post">
                               ('.$counter.') From '.$uplodedBy.' ('.date('m/d/Y g:i A',$changesReq['CreationDate']).')
								<br> '.$hoursSelected.'
                            </div>
                            <div class="customer_title">
                                '.$productData['Title'].'
                            </div>
                            <div class="customer_desc">
                               <div class="MessageText">'.$changesReq['MessageText']."</div><div class='ImageGalleryPopup'>";
								if(!empty($changesReq['Attachment'])) {

									$attchementUnSerialize = unserialize($changesReq['Attachment']);


									if(is_array($attchementUnSerialize) && count($attchementUnSerialize)>0){
										foreach($attchementUnSerialize as $imageKey=>$image){
										$ext = pathinfo($image, PATHINFO_EXTENSION);

										$imageSrc = "";
										$imageSrc = OrderImageURL($image);

									if(in_array(strtoupper($ext),$allowedVideo)) {

											$html .='<span class="customer_icon galleryListing  popup-gallery"><video width="100" height="100" style="vertical-align: text-top; margin-top:2px;" controls>
                                                                <source src="'.$imageSrc.'" type="video/mp4">
                                                            </video>';
									}else if(in_array($ext,$allowed)){
											 	$CheckThumbfileName = str_replace(".".$ext,"",$image);

											$html .='<span class="customer_icon galleryListing  popup-gallery"><a class="imageget" href="'.str_replace("https", "http", $imageSrc).'" alt="">'.OrderImageSrc($image,"354").'</a>';
									 }else{
											$html .='<span class="customer_icon  galleryListing"><a href="'.$imageSrc.'" download><img src="'.SITEURL.'/images/download_file_White.png"></a>';
										}
									 if($changesReq['Type']=="admin"   && $AccessType=="designer"){
										$html .='<i class="fas fa-times  deletedRevisionImages" data-id="'.$changesReq['ID'].'" data-imagekey="'.$imageKey.'"></i>';
									}
									$html .='</span>';

									}
								}
								}

								 if($changesReq['Type']=="admin" && $AccessType=="designer"){

								$html .='<form method="post" enctype="multipart/form-data"><input type="hidden" name="RequestID" value="'.$changesReq['ID'].'"><div class="FileBoxe"><input type="file" name="FileUploadPopup[]" class="FileUploadPopup" multiple><div class="FilePopupBefore"></div></div></form><input type="button" value="Download" class="downloadlogo" style="margin-left: 250px;
    border-radius: 5px;
    border: none;
    font-size: 22px;
    transform: translate(-50%, -100%);
    -ms-transform: translate(-50%, -50%);">';
								 }
								$html .= "</div>";
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


                                    $(".downloadlogo").unbind().click(function() {

                                var imgsrc = $(".imageget").attr("href");
                                var urls = [

								];

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
				} else {
					$html.="No Changes Available.";
				}
				$status="success";
			} else {
				$html.="Somthing went wrong with your request, please try again later";
				$status="error";
			}
		} else {
			$html.="Order ID is invalid";
			$status="error";
		}
	} else {
		$html.="Authentication denied : Login identity not valid";
		$status="error";
	}

	echo json_encode(array('message'=>$html,'status'=>$status,'titleBlock'=>$titleBlock));
