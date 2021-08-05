<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
$cartAmount = $cartProduct = $response_code = 0;
$cart = $newCart = array();
$status = $message = "";

if(!empty($_SESSION['CART'])){
    $oldCart = $_SESSION['CART'];
}

extract($_REQUEST);

unset($_SESSION['CartRequest']);

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
        $newProduct['type_banner'] = $flyerType;
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
	
    if(isset($defaultSize) && !empty($defaultSize)) {
        $newProduct['defaultSize'] = $defaultSize;
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
        }
    } else {
        $newProduct['otherSize'] = array();
    }
    
	if($_REQUEST['template_type']=="psd"){
		$newProduct['totalPrice'] = $getProduct['psd_price'];
	}else{
		$newProduct['totalPrice'] = $newAmount;
	}
    $extraCart = $_SESSION['CART'];
	if(isset($action) && ($action == "add" || $action == "remove")) {
        if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
            $cart = $_SESSION['CART'];
        } else { $cart = array(); }
        unset($_SESSION['CART']);
        if(isset($cart[$productID]) && !empty($cart[$productID])) {
            unset($cart[$productID]);
        }
        $cart[$productID] = $newProduct;
        
		if(isset($oldCart[FACEBOOK_PRODUCT_ID."_".$productID])) {
            $cart[$productID]['type_banner'][] = "Facebook cover";
        }
		
		
        
        $cart[$productID]['customData'] = array("main_title" => $main_title, "sub_title" => $sub_title, "event_date" => $event_date, "music_by" => $music_by, "more_info" => $more_info,"requirement_note" => $requirement_note, "venue" => $venue, "address" => $address,'terms'=>$terms,'artist_name'=>$artist_name,'produced_by'=>$produced_by,'phone_number'=>$phone_number,'venue_email'=>$venue_email,'facebook'=>$facebook,'instagram'=>$instagram,'twitter'=>$twitter,'music'=>$music,'mixtape_name'=>$mixtape_name,'single_title'=>$single_title,'deejay_name'=>$deejay_name,'ename'=>$ename,'presenting'=>$presenting);

        $folderPath = SITE_BASE_PATH."/".'uploads/userData/'.$_SESSION['userId']."/";
		$subfolderPath = 'uploads/userData/'.$_SESSION['userId']."/";
        
		if(!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
		if(!empty($extraCart[$productID]['customData']['venue_logo'])){
				 $cart[$productID]['customData']['venue_logo']= $extraCart[$productID]['customData']['venue_logo']; 	
		}
        if(!empty($_FILES['venue_logo']['tmp_name']))
        {
            $uploadedFilename = basename($_FILES["venue_logo"]["name"]);
            $uploadedFileType = strtolower(pathinfo($uploadedFilename,PATHINFO_EXTENSION));
            $file1 = "venue_logo_".$systemTime.".".strtolower($uploadedFileType);
            $uploadedFilePath = $folderPath.$file1;

            if(move_uploaded_file($_FILES['venue_logo']['tmp_name'], $uploadedFilePath ))
            {
                $cart[$productID]['customData']['venue_logo'] = $subfolderPath.$file1;
            }
        }
		

            $ownSongFolderPath = SITE_BASE_PATH.'/uploads/own_song/'.$_SESSION['userId']."/";
            if(!file_exists($ownSongFolderPath)) {
                mkdir($ownSongFolderPath, 0777, true);
            }
			if(!empty($cart[$productID]['type_banner'] ) && in_array("Use my music",$cart[$productID]['type_banner'] )){ 
			if(!empty($extraCart[$productID]['customData']['own_song'])){
				 $cart[$productID]['customData']['own_song']= $extraCart[$productID]['customData']['own_song']; 	
		    }
			
            if(!empty($_FILES['own_song']['tmp_name']))
            {
                $ownSongFilename = basename($_FILES["own_song"]["name"]);
                $uploadedFileType = strtolower(pathinfo($ownSongFilename,PATHINFO_EXTENSION));
                $file1 = "own_song_".$systemTime.".".strtolower($uploadedFileType);
                $uploadedFilePath = $ownSongFolderPath.$file1;

                if(move_uploaded_file($_FILES['own_song']['tmp_name'], $uploadedFilePath ))
                {
                    $cart[$productID]['customData']['own_song'] = 'uploads/own_song/'.$_SESSION['userId']."/".$file1;
                }
            }
			}else{
				unset($cart[$productID]['customData']['own_song']);
			}

            $productimages = $_POST['template_files'];
           
			if(!empty($extraCart[$productID]['customData']['filesImages'])){
				$Imagesfiles = $extraCart[$productID]['customData']['filesImages']; 	
			}
			
			if(!empty($productimages)){
				foreach($productimages as $singlekey=>$singleVal){
					 $Imagesfiles[] = 'uploads/userData/'.$_SESSION['userId']."/".$singleVal;
					 $folderPath = SITE_BASE_PATH.'uploads/userData/'.$_SESSION['userId']."/";
					  copy(SITE_BASE_PATH."uploads/tmp/".$singleVal,$folderPath.$singleVal);	
					  unlink(SITE_BASE_PATH."/uploads/tmp/".$singleVal);
													  
						
				}
				
				
			}
			if(!empty($Imagesfiles)){
				$cart[$productID]['customData']['filesImages'] = $Imagesfiles;	
			}
    
		
		if(isset($_REQUEST['customeProductFields']) && !empty($_REQUEST['customeProductFields'])){
			
					$cart[$productID]['customeProductFields']= $_REQUEST['customeProductFields'];
					
				
		}

        /*if(empty($cart[$productID]['type_banner'])){
            unset($cart["11_".$productID]);
        }
        if(!empty($cart[$productID]['type_banner'])){
            if(! in_array('Facebook cover',$cart[$productID]['type_banner'])){
                unset($cart["11_".$productID]);
            }
        }*/
		$cart[$productID]['template_type'] = ($_REQUEST['template_type']!="" && isset($_REQUEST['template_type']))?$_REQUEST['template_type']:"customize";
		
		$cart[$productID]['psd3dtitle'] = ($_REQUEST['psd3dtitle']!="" && isset($_REQUEST['psd3dtitle']))?"Yes":"No";
        $_SESSION['CART'] = $cart;
        $status = "success";
        $message = "Product successfully added into your cart";
        $response_code = "0001";
        $cartProduct = count($cart);
    } else {
        $status = "error";
        $response_code = "0101";
        $message = "Process action not defined.";
    }
} else {
    $status = "error";
    $response_code = "0000";
    $message = "Product id not found.";
}
$newCart = $_SESSION['CART'];
$cartProduct = count($newCart);
if($cartProduct>0) {
	
	
//Product After Cart Update
//$j=0;
 //   foreach($newCart as $cartProducts) {
//		if($cartvalue['type']!="main" && $j==0){
//			$cartAmount  -= 10;
//		}
//		$j++;
		
//Product After Cart Update
        $cartAmount = $cartAmount + $cartProducts['totalPrice'];
    //}
}
echo json_encode(array("status"=>$status, "response_code" => $response_code, "message" => $message, "cart_count" => $cartProduct, "amount_count" => $cartAmount));
?>