<?php 


	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	$status = $html = "";
	$AccessID = intval($_SESSION['userId']);
	if(is_login()) {
		
if(!empty($_POST['RequestID'])) {

    $RequestID = intval($_POST['RequestID']);
	$allowed =  array('gif','png' ,'jpg','webp');
    if(GetNumOfRcrdsOnCndi(CHANGE_REQ, "`Id` = '$RequestID'")>0) {

        $dynamic_dir = "../uploads/work/";
		$dynamic_dir1 = "uploads/work/";
		

        if(!file_exists($dynamic_dir)) {

            mkdir($dynamic_dir, 0777, true);

        }
		
		  $CHANGE_REQ = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`ID` = '".$RequestID."'", "Id", "ASC");
		  $attachement = $CHANGE_REQ[0]['Attachment'];
		  $AttachmentThumb = $CHANGE_REQ[0]['AttachmentThumb'];
		  $attachementArray = $filenameAttachThumb= array();
		  if($attachement!=""){
				$attachementArray = unserialize($attachement);  
		  }
		  if($AttachmentThumb!=""){
				$filenameAttachThumb = unserialize($AttachmentThumb);  
		  }
		 
		 
		foreach($_FILES["FileUploadPopup"]['name'] as $filePostsKey=>$filePosts){
			
			
			 $fileType = strtolower(pathinfo(basename($_FILES["FileUploadPopup"]["name"][$filePostsKey]),PATHINFO_EXTENSION));

		$imageNameRand = rand(1, 10000000000);
		
        $file_name = $imageNameRand.".".$fileType;
        $target_file = $dynamic_dir ."/". $file_name;

     
	 	//thumb
				 $sourceProperties = getimagesize($_FILES["FileUploadPopup"]["tmp_name"][$filePostsKey]);
				  $FileExt = pathinfo($_FILES["FileUploadPopup"]["name"][$filePostsKey], PATHINFO_EXTENSION);
				  $thumbImageName = $imageNameRand;
			if(strtolower($FileExt)=="jpg" || strtolower($FileExt)=="jpeg" || strtolower($FileExt)=="png" || strtolower($FileExt)=="gif"){
			  $thumbImage = ImageThumbCreate($sourceProperties,$FileExt,$_FILES["FileUploadPopup"]["tmp_name"][$filePostsKey],$thumbImageName,SITE_BASE_PATH.$dynamic_dir1,'work');
			}
	
	    if (move_uploaded_file($_FILES["FileUploadPopup"]["tmp_name"][$filePostsKey], $target_file)) {
				digitalOceanUploadImage(SITE_BASE_PATH.$dynamic_dir1."/".$file_name,'work');
            $original_name = basename( $_FILES["FileUploadPopup"]["name"][$filePostsKey]);
			$file_size = $_FILES["FileUploadPopup"]["size"][$filePostsKey];
			$filename_arr = explode('.',$file_name);
			
				 $upload  = $file_name;
				 $attachementArray[] =$file_name;
				 if(strtolower($FileExt)=="jpg" || strtolower($FileExt)=="webp" || strtolower($FileExt)=="jpeg" || strtolower($FileExt)=="png" || strtolower($FileExt)=="gif"){
					$filenameAttachThumb[] =array("large"=>$file_name,"small"=>$thumbImage);
				}
			 
		  }
		
		
		}
		
		 UpdateRcrdOnCndi(CHANGE_REQ,"`Attachment` = '".serialize($attachementArray)."',`AttachmentThumb`='".serialize($filenameAttachThumb)."', `AttechmentName` = '".serialize(array($attachementArray))."'", "`ID` = '".$RequestID."'");
		 if(!empty($attachementArray)){
			foreach($attachementArray as $singleImage=>$image){
									
										$ext = pathinfo($image, PATHINFO_EXTENSION);
										$imgurl = OrderImageURL($image);
										
									if(in_array(strtoupper($ext),$allowedVideo)) {
													 
													 
									$html .='<span class="customer_icon  galleryListing popup-gallery"><video width="100" height="100" style="vertical-align: text-top; margin-top:2px;" controls>
                                                                <source src="'.$imgurl.'" type="video/mp4">
                                                            </video>';
									
												}else if(in_array($ext,$allowed)){
									
												$CheckThumbfileName = str_replace(".".$ext,"",$image);
														$imageFileSrc = $imgurl;
												 
												
											$html .='<span class="customer_icon  galleryListing popup-gallery"><a href="'.$imgurl.'" alt="">'.OrderImageSrc($image,"354").'</a>
									
									';
									
												 }
												  else{
													$html .='<span class="customer_icon  galleryListing"><a href="'.$imageSrc.'" download><img  src="'.SITEURL.'/images/download_file_White.png"></a>'; 
													}
												 
												
											$html .='<i class="fas fa-times  deletedRevisionImages" data-id="'.$RequestID.'" data-imagekey="'.$singleImage.'"></i>';
										
											
										$html .='</span>';
											
									}
			 
		$html .='<script>
								$(".popup-gallery").magnificPopup({
           							delegate: "a",
									type: "image",
									mainClass: "mfp-img-mobile",
									gallery: {
										verticalFit: true
									},
								});
								</script>';
		}
			
	  

    } 
	echo $html;
	
}
	}

?>