<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
    $status = 'success';
	
	 $ID = $_POST['ID'];
	if(!empty($_POST['ID'])){
		 $getFiledsData = GetSglRcrdOnCndiWthOdr(ORDER,"Id='".$ID."'","Id","asc");
		
		
     
		
		if($_POST['mode']=="customdeleteFile"){
			
		
		
			
			if($getFiledsData['customeProductsFileds']!=""){
				$customeProductsFileds = unserialize($getFiledsData['customeProductsFileds']);
				$value = $customeProductsFileds[$_POST['product_type']][$_POST['option_type']][$_POST['fileds_type']];
				
					$key = array_search($_POST['name'],$value);
					digitalOceanDeleteImage("custom_product/".$value);
					unset($value[$key]);
					if(!empty($value)){
						$value = array_values($value);
						
					}
					$customeProductsFileds[$_POST['product_type']][$_POST['option_type']][$_POST['fileds_type']] = $value;
					
					mysql_query( "UPDATE ".ORDER." SET customeProductsFileds='".serialize($customeProductsFileds)."' WHERE Id=".$ID);
				
			}	
				
		}else if($_POST['mode']=="deleteFile"){
			
			if (strpos($_POST['fileds_type'], 'Filename') !== false) {
				
				
				$filesImagesArr = array();
				if(!empty($getFiledsData['filesImages'])){
					$filesImagesArr = unserialize($getFiledsData['filesImages']);
				}
				
			$indexKey = str_replace("Filename","",$_POST['fileds_type']);
			
			if($filesImagesArr[$indexKey]!=""){
				if(file_exists(SITE_BASE_PATH.$filesImagesArr[$indexKey])){
					unlink(SITE_BASE_PATH.$filesImagesArr[$indexKey]);
					unset($filesImagesArr[$indexKey]);
					$updateFiles = "";
					if(!empty($filesImagesArr)){
						$updateFiles = serialize($filesImagesArr);
					}
					
					mysql_query( "UPDATE ".ORDER." SET filesImages ='".$updateFiles."' WHERE Id=".$ID);
		   	
				}	
			}	
				
			}else{
			if($getFiledsData[$_POST['fileds_type']]!=""){
				if(file_exists(SITE_BASE_PATH.$getFiledsData[$_POST['fileds_type']])){
					unlink(SITE_BASE_PATH.$getFiledsData[$_POST['fileds_type']]);
					mysql_query( "UPDATE ".ORDER." SET ".$_POST['fileds_type']." ='' WHERE Id=".$ID);
		   	
				}	
			}	
			}
				
		}else{
		$updatedFileds = "";
		
		$customeProductsFiledsCheck = GetSglDataOnCndi(ORDER,"Id='".$ID."'","customeProductsFileds");
		
		
		
		mkdir(SITE_BASE_PATH."uploads/userData/".$getFiledsData['CustomerID']);
		$userDir = "uploads/userData/".$getFiledsData['CustomerID'];
			
			
	if(!empty($customeProductsFiledsCheck) && !empty($_POST['customeProductFields'])){
		//$ownSongFolderPath = 'uploads/custom_product/';
		$explodeCustom = unserialize($customeProductsFiledsCheck);
		
	
		if(key($_POST['customeProductFields'])=="flyer_design"){
		
			if(!isset($_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['add_facebook_cover'])){
				$_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['add_facebook_cover'] ="off";	
			}
		}
		
		if(key($_POST['customeProductFields'])=="animated_flyer"){
		
			if(!isset($_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['add_music'])){
				$_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['add_music'] ="off";	
			}
			
			$_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['defaultSize'] =$_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['sizes'];
			unset($_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['sizes']);
		}
		
		if(key($_POST['customeProductFields'])=="business_card"){
		
			if(!isset($_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['checkbox_sided'])){
				$_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['checkbox_sided'] ="off";	
			}
		}
		
		if(key($_POST['customeProductFields'])=="video_flyer"){
		
			if(!isset($_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['add_video'])){
				$_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['add_video'] ="off";	
			}
		}
		if(key($_POST['customeProductFields'])=="flyer_design"){
			$_POST['customeProductFields'][key($_POST['customeProductFields'])]['primary_options']['defaultSize'] =$_POST['customeProductFields'][key($_POST['customeProductFields'])]['secondary_options']['sizes'];
		}
		
		
		
		
			$ownSongFolderPath = 'uploads/custom_product/';
            if(!file_exists($ownSongFolderPath)) {
                mkdir($ownSongFolderPath, 0777, true);
            }
			
			
            if(!empty($_FILES['customeProductFields']))
            {
				
				foreach($_FILES['customeProductFields']['name'] as $sinlgekey=>$single){
				if($sinlgekey=="logo_design" || $sinlgekey=="3d_logo_conversion"|| $sinlgekey =="mixtape_cover_design" || $sinlgekey=="flyer_design" || $sinlgekey=="facebook_cover" || $sinlgekey=="laptop_skin"|| $sinlgekey=="business_card" || $sinlgekey=="animated_flyer" || $sinlgekey=="logo_intro"){
						
						foreach($single as $singleFilesKey1=>$singleFileValue1){
							
							
						foreach($singleFileValue1 as $singleFilesIndexLoop=>$singleFileValueLoop){
								
									
									if(!empty($explodeCustom[$sinlgekey][$singleFilesKey1][$singleFilesIndexLoop])){
										$_POST['customeProductFields'][$sinlgekey][$singleFilesKey1][$singleFilesIndexLoop]= $explodeCustom[$sinlgekey][$singleFilesKey1][$singleFilesIndexLoop];
									}
									
																	
									foreach($singleFileValueLoop as $singleFilesKey2=>$singleFileValue2){
									
									 if($singleFileValue2!=""){	
									 $ownSongFilename = basename($singleFileValue2);
									 $uploadedFileType = strtolower(pathinfo($ownSongFilename,PATHINFO_EXTENSION));
									 $file1 = "custom_".time().rand().".".strtolower($uploadedFileType);
									 $uploadedFilePath = $ownSongFolderPath.$file1;
									 
									
										if(move_uploaded_file($_FILES['customeProductFields']['tmp_name'][$sinlgekey][$singleFilesKey1][$singleFilesIndexLoop][$singleFilesKey2], SITE_BASE_PATH."/".$uploadedFilePath ))
										{
											  digitalOceanUploadImage(SITE_BASE_PATH."/".$uploadedFilePath ,'custom_product');
								
											
											  $_POST['customeProductFields'][$sinlgekey][$singleFilesKey1][$singleFilesIndexLoop][] = $file1;
										}
									
									}
									}
						}
					}
				
						
					}else{
					
					
					foreach($single as $singleFilesKey=>$singleFileValue){
			
					
						foreach($singleFileValue as $singleFilesKey2=>$singleFileValue2){
						 if($singleFilesKey2=="music_file"){
							if(!isset($_POST['customeProductFields'][$sinlgekey][$singleFilesKey]['add_music']) && $_POST['customeProductFields'][$sinlgekey][$singleFilesKey]['add_music']!="on"){
									continue;
							}	 
						}
						 if($singleFileValue2!=""){
						 $ownSongFilename = basename($singleFileValue2);
						 $uploadedFileType = strtolower(pathinfo($ownSongFilename,PATHINFO_EXTENSION));
           				 $file1 = "custom_".time().rand().".".strtolower($uploadedFileType);
						 $uploadedFilePath = $ownSongFolderPath.$file1;
						
							if(move_uploaded_file($_FILES['customeProductFields']['tmp_name'][$sinlgekey][$singleFilesKey][$singleFilesKey2], SITE_BASE_PATH."/".$uploadedFilePath ))
							{
							 digitalOceanUploadImage(SITE_BASE_PATH."/".$uploadedFilePath ,'custom_product');
								
								
								  $_POST['customeProductFields'][$sinlgekey][$singleFilesKey][$singleFilesKey2] = $file1;
							}
						 }else{
							 $_POST['customeProductFields'][$sinlgekey][$singleFilesKey][$singleFilesKey2] = $explodeCustom[$sinlgekey][$singleFilesKey][$singleFilesKey2];
								 
						  }
						
						}
					}
					}
				}
            }
			
			
	
		$query = mysql_query("UPDATE ".ORDER." SET customeProductsFileds='".serialize($_POST['customeProductFields'])."' WHERE Id=".$ID);
			
			
	}else{	
	
	//for flayer
     
	   if(!empty($_POST['FormInformation'])){
		   foreach($_POST['FormInformation'] as $single=>$v){
			   	if($single!="none"){
					$updatedFileds .= $single."='".$v."',";   
				}
	   		}
	   }
	    if(!empty($_FILES)){
			
				$multiPlyFileUploads = $FilesUploadFiels = array();
			
			
			$filesImagesArr = array();
				if(!empty($getFiledsData['filesImages'])){
					$filesImagesArr = unserialize($getFiledsData['filesImages']);
				}
		
			
			
			
		 		foreach($_FILES['FormInformation']['name'] as $singleKey=>$singleValue){
					
						if($singleKey=="photos_and_logo"){
							$k=1;
					
							foreach($singleValue as $singlePhotosKey=>$singlePhotos){
								
									 $Filename = basename($singlePhotos);
									 $uploadedFileType = strtolower(pathinfo($Filename,PATHINFO_EXTENSION));
									 $file1 = "custom_".time().rand().".".strtolower($uploadedFileType);
									 $uploadedFilePath = $userDir."/".$file1;
											
									
										if(move_uploaded_file($_FILES['FormInformation']['tmp_name'][$singleKey][$singlePhotosKey], SITE_BASE_PATH."/".$uploadedFilePath ))
										{
											$filesImagesArr[] = $uploadedFilePath;
											
										}
								
								   
								$k++;	
							}	
						}else{
								
								
									 $Filename = basename($singleValue);
									 $uploadedFileType = strtolower(pathinfo($Filename,PATHINFO_EXTENSION));
									 $file1 = "custom_".time().rand().".".strtolower($uploadedFileType);
									 $uploadedFilePath = $userDir."/".$file1;
									
										if(move_uploaded_file($_FILES['FormInformation']['tmp_name'][$singleKey], SITE_BASE_PATH."/".$uploadedFilePath ))
										{
											
											  $updatedFileds .= $singleKey."='".$uploadedFilePath."',";
										}
								
								   
								$k++;	
								
						}	
				}	 
				
		}
		
	
		 
	   if($updatedFileds!=""){
		  
			$query = mysql_query( "UPDATE ".ORDER." SET ".rtrim($updatedFileds,",")." WHERE Id=".$ID) or die(mysql_error());
		   
	   	}
			if(!empty($filesImagesArr)){
				$query = mysql_query( "UPDATE ".ORDER." SET filesImages='".serialize($filesImagesArr)."' WHERE Id=".$ID) or die(mysql_error());
			}
			
		}
    }
	}
	
?>