<?php

	ob_start();

	require_once '../function/constants.php';

	require_once '../function/configClass.php';

	require_once '../function/siteFunctions.php';


	$AccessType = $_SESSION['userType'];
	
	$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`ID` = '".$_REQUEST['id']."'", "Id", "Desc");
	
	if(!empty($selectChangeRequest)){
		foreach($selectChangeRequest as $singleKey=>$singleValue){
					
					if(!empty($singleValue['Attachment'])) {
									
									$attchementUnSerialize = unserialize($singleValue['Attachment']);
									
									$newImagesArray = array();
									if(is_array($attchementUnSerialize) && count($attchementUnSerialize)>0){	
										foreach($attchementUnSerialize as $imageKey=>$image){
												if($imageKey==$_REQUEST['imagekey']){
													
													if(file_exists(SITE_BASE_PATH."uploads/work/".$image)){
														unlink(SITE_BASE_PATH."uploads/work/".$image);	
													}
														$extThumb = pathinfo($image, PATHINFO_EXTENSION);
														$fileNameThumb = str_replace(".".$extThumb,"",$image);
														
													if(file_exists(SITE_BASE_PATH."uploads/work/".$fileNameThumb."_thum.".$extThumb)){
														unlink(SITE_BASE_PATH."uploads/work/".$fileNameThumb."_thum.".$extThumb);	
													}
														
													if(file_exists(SITE_BASE_PATH."uploads/work/".$fileNameThumb."_thum.webp")){
														unlink(SITE_BASE_PATH."uploads/work/".$fileNameThumb."_thum.webp");	
													}	
													
												}else{
													$newImagesArray[]  = $image;
												}
										}
										
										  UpdateRcrdOnCndi(CHANGE_REQ,"`Attachment` = '".serialize($newImagesArray)."', `AttechmentName` = '".serialize($newImagesArray)."'", "`ID` = '".$_REQUEST['id']."'");
									}
					}
		}
	}else{
		$html = "Sorry you have not any request of rivision";	
	}
	
	
	
	$myarray = array("message" =>$html);

	echo json_encode($myarray);
?>