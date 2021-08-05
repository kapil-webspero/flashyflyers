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
	$allowed =  array('gif','png' ,'jpg');
    if(GetNumOfRcrdsOnCndi(CHANGE_REQ, "`Id` = '$RequestID'")>0) {

        $dynamic_dir = "../uploads/work/";
		$dynamic_dir1 = "uploads/work/";
		

        if(!file_exists($dynamic_dir)) {

            mkdir($dynamic_dir, 0777, true);

        }
		
		  $CHANGE_REQ = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`ID` = '".$RequestID."'", "Id", "ASC");
		  $attachement = $CHANGE_REQ[0]['Attachment'];
		  $attachementArray = array();
		  if($attachement!=""){
				$attachementArray = unserialize($attachement);  
		  }
		 
		foreach($_FILES["FileUploadPopup"]['name'] as $filePostsKey=>$filePosts){
			
			
			 $fileType = strtolower(pathinfo(basename($_FILES["FileUploadPopup"]["name"][$filePostsKey]),PATHINFO_EXTENSION));

        $file_name = rand(1, 10000000000).".".$fileType;
        $target_file = $dynamic_dir ."/". $file_name;

        if (move_uploaded_file($_FILES["FileUploadPopup"]["tmp_name"][$filePostsKey], $target_file)) {

            $original_name = basename( $_FILES["FileUploadPopup"]["name"][$filePostsKey]);
			$attachementArray[] = $file_name;
		  } 
		
		
		}
		
		 UpdateRcrdOnCndi(CHANGE_REQ,"`Attachment` = '".serialize($attachementArray)."', `AttechmentName` = '".serialize(array($attachementArray))."'", "`ID` = '".$RequestID."'");
		 if(!empty($attachementArray)){
			foreach($attachementArray as $singleImage=>$image){
									
										$ext = pathinfo($image, PATHINFO_EXTENSION);
										if(!in_array($ext,$allowed)) {
													 
													 
									$html .='<span class="customer_icon  popup-gallery"><video width="100" height="100" style="vertical-align: text-top; margin-top:2px;" controls>
                                                                <source src="'.SITEURL.$dynamic_dir1.$image.'" type="video/mp4">
                                                            </video>';
									
												 }else{
									$html .='<span class="customer_icon  popup-gallery"><a href="'.SITEURL.$dynamic_dir1.$image.'" alt=""><img src="'.SITEURL.$dynamic_dir1.$image.'"></a>
									
									';
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