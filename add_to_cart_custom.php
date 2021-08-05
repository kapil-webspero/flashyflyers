<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';
$cartAmount = $cartProduct = $response_code = 0;
$cart = $newCart = array();
$status = $message = "";


if(!empty($_SESSION['CART'])){
    $oldCart = $_SESSION['CART'];
}
extract($_REQUEST);

unset($_SESSION['CartRequest']);

$ownSongFolderPath = 'uploads/custom_product/';
            if(!file_exists($ownSongFolderPath)) {
                mkdir($ownSongFolderPath, 0777, true);
            }
			

$productID = $product_id;
if(isset($productID) && !empty($productID)) {

    $newProduct = array("added" => $systemTime, "id" => $productID, "type" => "main");
    $getProduct = GetSglRcrdOnCndi(PRODUCT, "`id` = '$productID'");

    
        $price3D = $addonPrices['1'];
        $priceMotion = $addonPrices['2'];
    $priceown_music = $addonPrices['15'];


    $getAddonsData = GetMltRcrdsOnCndiWthOdr(PRODUCT,"`id` = '11'","`id`", "ASC");
    $priceFacebookcover=$getAddonsData[0]['Baseprice'];


    //$priceFacebookcover=$getProduct['Baseprice'];
    $newAmount = $getProduct['Baseprice'];
    //array("added" => $systemTime, "id" => $productID, "type" => "main", "dimensional" => $dimensional, "type_banner" => $type_banner, "defaultSize" => $defaultSize, "otherSize" => $otherSize, "totalPrice" => $totalPrice);
    if(isset($flyer_dimension) && !empty($flyer_dimension)) {
        $newProduct['dimensional'] = $flyer_dimension;
        if($flyer_dimension == "3D") {
            $newAmount = $newAmount+$price3D;
        }
    } else {
        $newProduct['dimensional'] = "2D";
    }
    if(isset($_REQUEST['flyerType']) && !empty($_REQUEST['flyerType'])) {
        $flyerType = $_REQUEST['flyerType'];
       // $newProduct['type_banner'] = $flyerType;
        if(in_array("motion", $flyerType)) {
            $newAmount += $priceMotion;
		   // $newProduct['type_banner'][] = "Facebook cover";
        }
        if(in_array("animated", $flyerType)) {
            $newAmount += $priceAnimation;
        }
        if(in_array("Use my music", $flyerType)) {
            $newAmount += $priceown_music;
        }
        /*if(in_array("Facebook cover", $flyerType)) {
            $newAmount += $priceFacebookcover;
        }*/
    }
	$newProduct['customeProductFields']= $_REQUEST['customeProductFields'];
	
    if(isset($defaultSize) && !empty($defaultSize)) {
        $newProduct['defaultSize'] = $defaultSize;
		$newProduct['customeProductFields'][$customeProductFieldsType]['primary_options']['defaultSize'] = $defaultSize;
    }
    if(isset($deliveryTime) && !empty($deliveryTime)) {
        $newProduct['deliveryTime'] = $deliveryTime;
        if($deliveryTime == "1") {
            $newAmount += $turnAround1;
        } elseif($deliveryTime == "2") {
            $newAmount += $turnAround2;
        } elseif($deliveryTime == "3") {
            $newAmount += $turnAround3;
        } elseif($deliveryTime == "4") {
            $newAmount += $turnAround4;
        }
    } else {
        $newProduct['deliveryTime'] = "0";
    }
    if(isset($_REQUEST['otherSize']) && !empty($_REQUEST['otherSize'])) {
        $getOtherSizes = getProdSizeArr();
        $otherSizes = $_REQUEST['otherSize'];
        $newProduct['otherSize'] = $otherSizes;
        foreach($otherSizes as $selectedSize){
            $newAmount += $getOtherSizes[$selectedSize]['price'];
			$newProduct['customeProductFields'][$customeProductFieldsType]['primary_options']['otherSize'][] = $selectedSize;
		
		   $newProduct['otherSizePrice'] +=$getOtherSizes[$selectedSize]['price'];
        }
    } else {
        $newProduct['otherSize'] = array();
    }
  
	 $newProduct['totalPrice'] = $newAmount;
     
        if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
            $cart = $_SESSION['CART'];
        } else { $cart = array(); }
        unset($_SESSION['CART']);
        if(isset($cart[$productID]) && !empty($cart[$productID])) {
            unset($cart[$productID]);
        }
         $cart[$productID] = $newProduct;
        if(isset($oldCart[FACEBOOK_PRODUCT_ID."_".$productID])) {
        	//    $cart[$productID]['type_banner'][] = "Facebook cover";
        }
		
	
		if(isset($_REQUEST['customeProductFields']) && !empty($_REQUEST['customeProductFields'])){
				$cart[$productID]['turnaround_time_price'] =0;
				$cart[$productID]['checkbox_sided_price'] =0;
				$cart[$productID]['add_music_price'] =0;
				$cart[$productID]['3d_or_2d_price'] =0;
				$cart[$productID]['add_video_price'] =0;
				$cart[$productID]['add_facebook_cover_price'] =0;
				
				
				
				
			
					foreach($cart[$productID]['customeProductFields'] as $singleOptionKey=>$singleOptionValue){
						
						foreach($singleOptionValue as $singleOptionKey1=>$singleOptionValue1){
							foreach($singleOptionValue1 as $singleOptionKey2=>$singleOptionValue2){
								
								
											
									if($singleOptionKey=="logo_design" || $singleOptionKey=="3d_logo_conversion"  || $singleOptionKey =="mixtape_cover_design" || $singleOptionKey=="flyer_design" || $singleOptionKey=="facebook_cover" || $singleOptionKey=="laptop_skin"|| $singleOptionKey=="business_card" || $singleOptionKey=="animated_flyer" || $singleOptionKey=="logo_intro"){
										
										if($singleOptionKey2=="files" || $singleOptionKey2=="attach_any_pictures"  || $singleOptionKey2=="attach_any_logos"  || $singleOptionKey2=="attach_your_logo_design"  || $singleOptionKey2=="attach_any_style_reference" || $singleOptionKey2=="vector_psd_pdf" || $singleOptionKey2=="attach_logo"){
										//	unset($cart[$productID]['customeProductFields'][$singleOptionKey][$singleOptionKey1][$singleOptionKey2]);
											if(!empty($singleOptionValue2)){
												foreach($singleOptionValue2 as $singleFileUpload){
													
													if(file_exists(SITE_BASE_PATH."uploads/tmp/".$singleFileUpload)){
													 digitalOceanUploadImage(SITE_BASE_PATH."uploads/tmp/".$singleFileUpload,'custom_product');
													 }
													 //copy("uploads/tmp/".$singleFileUpload,"uploads/custom_product/".$singleFileUpload);	
													// unlink("uploads/tmp/".$singleFileUpload);
												}
											}
										}
										if($singleOptionKey2=="music_file"){
												if(!empty($singleOptionValue2)){
														foreach($singleOptionValue2 as $singleFileUpload){	
															
															if(file_exists(SITE_BASE_PATH."uploads/tmp/".$singleFileUpload)){
															 digitalOceanUploadImage(SITE_BASE_PATH."uploads/tmp/".$singleFileUpload,'custom_product');
															 }
															/// copy("uploads/tmp/".$singleFileUpload,"uploads/custom_product/".$singleFileUpload);	
															//unlink("uploads/tmp/".$singleFileUpload);	
														}
												}
										
										}
									}
									
									/*if($singleOptionKey=="flyer_design" || $singleOptionKey=="facebook_cover" || $singleOptionKey=="laptop_skin"|| $singleOptionKey=="business_card" || $singleOptionKey=="animated_flyer" || $singleFilesKey=="logo_intro"){
										if(($singleOptionKey2=="attach_any_pictures"  || $singleOptionKey2=="attach_any_logos"  || $singleOptionKey2=="attach_any_style_reference") && $singleOptionValue2!=""){
										
											unset($cart[$productID]['customeProductFields'][$singleOptionKey][$singleOptionKey1][$singleOptionKey2]);
											
											 $base64Data = $singleOptionValue2;
												$imageName="custom_".rand().time().rand().'.png';
												$image_parts = explode(";base64,",$base64Data);
												$image_type_aux = explode("image/", $image_parts[0]);
												$image_type = $image_type_aux[1];
												$image_base64 = base64_decode($image_parts[1]);
												$file = $ownSongFolderPath.$imageName;
												file_put_contents($file, $image_base64);
												$cart[$productID]['customeProductFields'][$singleOptionKey][$singleOptionKey1][$singleOptionKey2] =$imageName;											}
										
									}*/
									
					
					
								
								if($singleOptionKey2=="3d_or_2d" && $singleOptionValue2=="3D"){
										 $cart[$productID]['totalPrice'] += $addonPrices['1'];
										 $cart[$productID]['3d_or_2d_price'] =$addonPrices['1'];
								}
								if($singleOptionKey2=="turnaround_time" && $singleOptionValue2!="6"){
										$cart[$productID]['totalPrice'] += $customProductDelTimePrice[$singleOptionValue2];
										 $cart[$productID]['turnaround_time_price'] =$customProductDelTimePrice[$singleOptionValue2];
								}
								
								if($singleOptionKey2=="checkbox_sided" && $singleOptionValue2=="on"){
									$cart[$productID]['totalPrice'] += $checkbox_sided;
									$cart[$productID]['checkbox_sided_price'] =$checkbox_sided;	
								}
								if($singleOptionKey2=="add_music" && $singleOptionValue2=="on"){
										$cart[$productID]['totalPrice'] += $add_music;
										$cart[$productID]['add_music_price'] =$add_music;
								}
								
								if($singleOptionKey2=="add_video" && $singleOptionValue2=="on"){
										$cart[$productID]['totalPrice'] += $add_video;
										$cart[$productID]['add_video_price'] =$add_video;
								}
								
								if($singleOptionKey2=="add_facebook_cover" && $singleOptionValue2=="on"){
										//$cart[$productID]['totalPrice'] += $add_facebook_cover;
										$cart[$productID]['add_facebook_cover_price'] =$add_facebook_cover;
								}
								
								//echo $singleOptionKey2."\>".$singleOptionValue2."<br>";
							}
						}
						
						
						
					}
					//die;
					
		}
		
			
        	if(isset($_REQUEST['customeProductFields']['flyer_design']['secondary_options']['add_facebook_cover']) && $_REQUEST['customeProductFields']['flyer_design']['secondary_options']['add_facebook_cover']=="on"){
				$cart['11_'.$productID] = array("added" => $systemTime, "id" => '11_'.$productID, "type" => "addon", "deliveryTime" =>$_REQUEST['customeProductFields']['flyer_design']['secondary_options']['turnaround_time'], "totalPrice" =>$add_facebook_cover, "ProductBaseID" => $productID);			
			}else{
				if(isset($_REQUEST['customeProductFields']['flyer_design'])){
					unset($cart['11_'.$productID]);	
				}
			}
 	 
		
		$_SESSION['CART'] = $cart;
        
		
		$status = "success";
        $message = "Product successfully added into your cart";
        $response_code = "0001";
        $cartProduct = count($cart);
    
} 
$newCart = $_SESSION['CART'];
$cartProduct = count($newCart);

header("location:cart.php");
exit;
?>