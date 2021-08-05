<?php 

$targetFolderPath = 'uploads/tmp/';
if($_REQUEST['action']=="addFile"){
	if(!file_exists($targetFolderPath)) {
		mkdir($targetFolderPath, 0777, true);
	}
	
if(isset($_FILES['template_files']) && $_FILES['template_files']['name'][0]!=""){

foreach($_FILES['template_files']['name'] as $singleOptionKey=>$singleOptionValue){
			$singleFileUploadName = rand().time().rand().$singleOptionValue;
					$uploadedFilePath = $targetFolderPath.$singleFileUploadName;
			
										if(move_uploaded_file($_FILES['template_files']['tmp_name'][$singleOptionKey], $uploadedFilePath ))
										{
											$fileResponse[] = $singleFileUploadName; 
										}
										

}
}else{
foreach($_FILES['customeProductFields']['name'] as $singleOptionKey=>$singleOptionValue){
						
						foreach($singleOptionValue as $singleOptionKey1=>$singleOptionValue1){
							foreach($singleOptionValue1 as $singleOptionKey2=>$singleOptionValue2){
											
									if($singleOptionKey=="logo_design" || $singleOptionKey=="3d_logo_conversion"  || $singleOptionKey =="mixtape_cover_design" || $singleOptionKey=="flyer_design" || $singleOptionKey=="facebook_cover" || $singleOptionKey=="laptop_skin"|| $singleOptionKey=="business_card" || $singleOptionKey=="animated_flyer" || $singleOptionKey=="logo_intro"){
										
										if($singleOptionKey2=="files" || $singleOptionKey2=="attach_any_pictures" || $singleOptionKey2=="music_file"  || $singleOptionKey2=="attach_any_logos"  || $singleOptionKey2=="attach_your_logo_design"  || $singleOptionKey2=="attach_any_style_reference" || $singleOptionKey2=="vector_psd_pdf" || $singleOptionKey2=="attach_logo"){
											foreach($singleOptionValue2 as $singleFileUploadKey=>$singleFileUpload){
												
												$singleFileUploadName = rand().time().rand().$singleFileUpload;
											 $uploadedFilePath = $targetFolderPath.$singleFileUploadName;
			
										if(move_uploaded_file($_FILES['customeProductFields']['tmp_name'][$singleOptionKey][$singleOptionKey1][$singleOptionKey2][$singleFileUploadKey], $uploadedFilePath ))
										{
											$response['customeProductFields'][$singleOptionKey][$singleOptionKey1][$singleOptionKey2][] =$singleFileUploadName;											
											$fileResponse[] = $singleFileUploadName; 
										}
											
											}
										}
									}
									
								
							}
						}
						
						
						
					}
				
				}
					echo json_encode($fileResponse);
					exit;
					

}
if($_REQUEST['action']=="fileRemove"){
	unlink($targetFolderPath.$_REQUEST['filename']);
}

?>