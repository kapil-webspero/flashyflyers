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
		$allowed =  array('gif','png' ,'jpg');
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
												 if(!in_array($ext,$allowed)) {
													 
													 
									$html .='<span class="customer_icon  popup-gallery"><video style="vertical-align: text-top; margin-top:2px;" width="100" height="100" controls>
                                                                <source src="'.SITEURL.$dynamic_dir.$image.'" type="video/mp4">
                                                            </video></span>';
									
												 }else{
                                                           
											
									$html .='<span class="customer_icon  popup-gallery"><a href="'.SITEURL.$dynamic_dir.$image.'" alt=""><img src="'.SITEURL.$dynamic_dir.$image.'"></a></span>';
												 }
									
										if($singleValue['Type']=="user" && $AccessType=="user"){
										//	$html .='<i class="fas fa-times  deletedRevisionImages" data-id="'.$singleValue['ID'].'" data-imagekey="'.$imageKey.'"></i>';
										}
										
										if($singleValue['Type']=="admin" && $AccessType=="designer"){
										//	$html .='<i class="fas fa-times  deletedRevisionImages" data-id="'.$singleValue['ID'].'" data-imagekey="'.$imageKey.'"></i>';
										}
									$html .='</span>';
									}
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