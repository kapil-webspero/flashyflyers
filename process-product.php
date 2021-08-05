<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';

$main_title = $sub_title = $event_date = $music_by = $own_song = $more_info = $requirement_note = $venue = $address = $terms =  "";

extract($_REQUEST);
unset($_SESSION['CartRequest']);


$customeProductCount = 0;
foreach($_SESSION['CART'] as $singleCart){
	
if(!empty($singleCart['customeProductFields'])) { 
	$customeProductCount++; 										
}

if(!empty($singleCart['type']) && $singleCart['type']=="addon") { 
	$customeProductCount++; 										
}
}

if(isset($_SESSION['userId']) || !empty($_SESSION['userId'])){
    $cart = $_SESSION['CART'];
    if(!isset($_REQUEST['main_title'])) {
        $main_title = "";
    }
    if(isset($_REQUEST['product_id']) && !empty($_REQUEST['product_id'])) {
        $productKey = $_REQUEST['product_id'];
    }
    if(!isset($cart[$productKey]) && empty($cart[$productKey])) {
        $message = "product id not found."	;
    } else {
        $product = $cart[$productKey];

        $product['customData'] = array("main_title" => $main_title, "sub_title" => $sub_title, "event_date" => $event_date, "music_by" => $music_by, "more_info" => $more_info,"requirement_note" => $requirement_note, "venue" => $venue, "address" => $address,'terms'=>$terms,'artist_name'=>$artist_name,'produced_by'=>$produced_by,'phone_number'=>$phone_number,'venue_email'=>$venue_email,'facebook'=>$facebook,'instagram'=>$instagram,'twitter'=>$twitter,'music'=>$music,'mixtape_name'=>$mixtape_name,'single_title'=>$single_title,'deejay_name'=>$deejay_name,'ename'=>$ename,'presenting'=>$presenting);

        $folderPath = 'uploads/userData/'.$_SESSION['userId']."/";
        if(!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
		
		if(!empty($_SESSION['CART'][$productKey]['customData']['venue_logo'])){
				 $product['customData']['venue_logo']= $_SESSION['CART'][$productKey]['customData']['venue_logo']; 	
		}
        if(!empty($_FILES['venue_logo']['tmp_name']))
        {
            $uploadedFilename = basename($_FILES["venue_logo"]["name"]);
            $uploadedFileType = strtolower(pathinfo($uploadedFilename,PATHINFO_EXTENSION));
            $file1 = "venue_logo_".$systemTime.".".strtolower($uploadedFileType);
            $uploadedFilePath = $folderPath.$file1;

            if(move_uploaded_file($_FILES['venue_logo']['tmp_name'], $uploadedFilePath ))
            {
                $product['customData']['venue_logo'] = $uploadedFilePath;
            }
        }

            $ownSongFolderPath = 'uploads/own_song/'.$_SESSION['userId']."/";
            if(!file_exists($ownSongFolderPath)) {
                mkdir($ownSongFolderPath, 0777, true);
            }
			
			if(!empty($_SESSION['CART'][$productKey]['customData']['own_song'])){
				 $product['customData']['own_song']= $_SESSION['CART'][$productKey]['customData']['own_song']; 	
		}
			
            if(!empty($_FILES['own_song']['tmp_name']))
            {
                $ownSongFilename = basename($_FILES["own_song"]["name"]);
                $uploadedFileType = strtolower(pathinfo($ownSongFilename,PATHINFO_EXTENSION));
                $file1 = "own_song_".$systemTime.".".strtolower($uploadedFileType);
                $uploadedFilePath = $ownSongFolderPath.$file1;

                if(move_uploaded_file($_FILES['own_song']['tmp_name'], $uploadedFilePath ))
                {
                    $product['customData']['own_song'] = $uploadedFilePath;
                }
            }

            $productimages = $_POST['product_gallery_image_hidden'];
           
			if(!empty($_SESSION['CART'][$productKey]['customData']['filesImages'])){
				$Imagesfiles = $_SESSION['CART'][$productKey]['customData']['filesImages']; 	
			}
			
			if(!empty($productimages)){
				foreach($productimages as $singlekey=>$singleVal){
					  $base64Data = $singleVal;
					$imageName=rand().time().rand().'.png';
					$image_parts = explode(";base64,",$base64Data);
					$image_type_aux = explode("image/", $image_parts[0]);
					$image_type = $image_type_aux[1];
					$image_base64 = base64_decode($image_parts[1]);
					$file = $folderPath.$imageName;
					file_put_contents($file, $image_base64);
					$Imagesfiles[] = $file; 
						
				}
				
				
			}
			if(!empty($Imagesfiles)){
				$product['customData']['filesImages'] = $Imagesfiles;	
			}
    }
	
    $cart[$productKey] = $product;

    unset($_SESSION['CartRequest']);
    unset($_SESSION['CART']);
    $_SESSION['CART'] = $cart;

    $message = "record successfully updated";


    if((count($cart)-$customeProductCount)>($product_key+1)) {
        header("location:info.php?product_setup_id=".($product_key+1));
        exit();
    } else {
		
        header("location:addons.php");
        exit();
    }
} else {
    $message = "User is not logged in."	;
}
?>