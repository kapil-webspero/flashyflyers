<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';

if($_SESSION['loginType']=="guest" && !empty($_SESSION['guest_checkout'])){
if(isset($_SESSION['guest_checkout']['create_account'])){
	
	$password = base64_encode(random_password(15));
	$fn = array("UserType", "Email", "Password", "CreatedDate", "CreatedOn", "Status","FName","LName");
			$fv = array("user", $_SESSION['guest_checkout']['email'], $password, time(), date("Y-m-d H:i:s"), 'active',$_SESSION['guest_checkout']['first_name'],$_SESSION['guest_checkout']['last_name']);
			$userID = InsertRcrdsGetID($fn,$fv,USERS);

			$curl = curl_init();
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => SITEURL.'EmailTemplate/welcome.php?UID='.$userID,
				CURLOPT_USERAGENT => 'Curl Test'
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);
			
			$mailchimp=syncMailchimp($email,MAILCHIMP_API_KEY,MAILCHIMP_LIST_ID,$fname,$lname);
			$status = "success";
			$_SESSION['userId'] = $userID;
			$_SESSION['userType'] = "user";
			$_SESSION['loginType'] = "user";
			
			$UserOrderType = 'user';
	
}
}
if(isset($_SESSION['CART']) || isset($_SESSION['CartRequest']) ) {
	if(isset($_SESSION['CART'])){
		$newCart = $_SESSION['CART'];
		$cartProduct = count($newCart);
		$names=array();
		if($cartProduct>0) {
			$j=1;
            foreach($newCart as $cartProducts) {
				$cartAmount = $cartAmount + $cartProducts['totalPrice'];
                $cartProductsId = explode("_",$cartProducts['id']);
                $cartProductsId = $cartProductsId[0];
                $product=GetSglRcrdOnCndi(PRODUCT, "id=".$cartProductsId);
				$names[]=$product['Title'];
				$j++;	
			}
		}
		if(isset($_SESSION['DISCOUNT_DATA'])) {
			$cartAmount=$cartAmount-$_SESSION['DISCOUNT_DATA']['discountapplied'];
		}
	}else{
		
		
		$newCart = $_SESSION['CartRequest'];
		$names=array();
		if($newCart>0) {
			$j=1;
			foreach($newCart as $cartProducts1) {
			$cartProduct = count($cartProducts1);
		
			foreach($cartProducts1 as $cartProductsKey=>$cartProductsValue) {
				$cartAmount = $cartAmount + $cartProductsValue['REQUEST_CHANGE_PRICE'];
                $cartProductsId = explode("_",$cartProductsKey);
                $cartProductsKey = $cartProductsId[0];
				$product=GetSglRcrdOnCndi(PRODUCT, "id=".$cartProductsKey);
				$names[]=$product['Title'];
				$j++;	
			}
			}
		}
		if(isset($_SESSION['DISCOUNT_DATA'])) {
			$cartAmount=$cartAmount-$_SESSION['DISCOUNT_DATA']['discountapplied'];
		}

					
	}
    $namesstr=implode(" and ",$names);   
	$return= SITEURL."thankyou.php?paypalpayment=success";
	$fail= SITEURL."fail.php";
	
	//order crate
	
	
	
	
if ( isset( $_SESSION['CART'] ) ) {
	$_SESSION['thankorder'] = [];
	
		$TransactionData = GetSglRcrdOnCndi(TRANSACTION, "`id` = '".$_SESSION['paymentRecord']."'");
		$rep_order = "no";
		if($TransactionData['RepUserID']>0){
			$rep_order ="yes";	
		}
	
	$OrderType = "cart";
    $cart = $_SESSION['CART'];
    $facebookAddonData = GetSglRcrdOnCndi( PRODUCT, "id=" . FACEBOOK_PRODUCT_ID );
    $priceFacebookCoverPrice = $facebookAddonData['Baseprice'];
	
	 $tDate = date( "Y-m-d h:i:s");
	 $tAmt = $cartAmount;
	
	 $settingarr = GetSglRcrdOnCndi( SETTINGS, "id=1" );

  if ( isset( $_SESSION['DISCOUNT_DATA'] ) ) {
  			$couponData=GetSglRcrdOnCndi(DISCOUNT, "Id='".$_SESSION['DISCOUNT_DATA']['discountData']['Id']."'");
			if(!empty($couponData)){
				$DicountUsed = (int)$couponData['Used'] + (int)1; 
				mysql_query("UPDATE ".DISCOUNT." SET Used='".$DicountUsed."' WHERE Id ='".$_SESSION['DISCOUNT_DATA']['discountData']['Id']."'");
				$NumberOfUses = (int)$couponData['NumberOfUses'];
				if($NumberOfUses>0 && $DicountUsed>=$NumberOfUses){
					mysql_query("UPDATE ".DISCOUNT." SET finishUse='Yes' WHERE Id ='".$_SESSION['DISCOUNT_DATA']['discountData']['Id']."'");			
				}
			}
  
        $paymentRecord = InsertRcrdsGetID( ["TransactionID",
                                            "UserID",
                                            "PurchaseDate",
                                            "Amount",
                                            "PaymentMethod",
                                            "PaymentStatus",
                                            "Status",
                                            "Createdon",
                                            "DiscountID",
                                            "DiscountName",
                                            "DiscountAmount",'RepCartData','RepUserID','RepKey','RepDiscountData'], [$tID,
                                                                $_SESSION['userId'],
                                                                $tDate,
                                                                formatPrice($tAmt),
                                                                'paypal',
                                                                'Pending payment',
                                                                "3",
                                                                $systemTime,
                                                                $_SESSION['DISCOUNT_DATA']['discountData']['Id'],
                                                                $_SESSION['DISCOUNT_DATA']['discountData']['DiscountName'],
                                                                $_SESSION['DISCOUNT_DATA']['discountapplied'],$TransactionData['RepCartData'],$TransactionData['RepUserID'],$TransactionData['RepKey'],$TransactionData['RepDiscountData']], TRANSACTION );
    } else {
        $paymentRecord = InsertRcrdsGetID( ["TransactionID",
                                            "UserID",
                                            "PurchaseDate",
                                            "Amount",
                                            "PaymentMethod",
                                            "PaymentStatus",
                                            "Status",
                                            "Createdon",'RepCartData','RepUserID','RepKey','RepDiscountData'], [$tID,
                                                           $_SESSION['userId'],
                                                           $tDate,
                                                           formatPrice($tAmt),
                                                           'paypal',
                                                           "Pending payment",
                                                           "2",
                                                           $systemTime,$TransactionData['RepCartData'],$TransactionData['RepUserID'],$TransactionData['RepKey'],$TransactionData['RepDiscountData']], TRANSACTION );
    }
	DltSglRcrd(TRANSACTION,"id='".$_SESSION['paymentRecord']."'");
	DltSglRcrd(ORDER,"TransactionID='".$_SESSION['paymentRecord']."'");

	$_SESSION['paymentRecord'] = $paymentRecord;
    foreach ( $cart as $orders ) {
		

        $typeBanners = implode( ",", $orders['type_banner'] );
        $otherSizes = implode( ",", $orders['otherSize'] );
        $extraAddon = implode( ",", $orders['extraAddon'] );
        $customData = $orders['customData'];
		$customFiledsProductArray = $orders['customeProductFields'];
		$customFiledsProduct="";
		if(!empty($customFiledsProductArray)){
			foreach($customFiledsProductArray as $singleKey=>$singleValue){
				if($singleValue!=""){
					$customFiledsProduct[$singleKey] = $singleValue;			
				}	
			}
			$customFiledsProduct = serialize($customFiledsProduct);
		}
		

       

        $orderIdFull = $orders['id'];
        $orderIdArray = explode( "_", $orderIdFull );
        $orderId = $orderIdArray[0];
       
        $parent_product_id = ( ! empty( $orderIdArray[1] ) && $orderIdArray[1] > 0) ? $orderIdArray[1] : 0;
		

        /*if($orderId != FACEBOOK_PRODUCT_ID && !empty($orders['type_banner']) && (in_array('Facebook cover',$orders['type_banner']) )){
            $orders['totalPrice'] = $orders['totalPrice'] - $priceFacebookCoverPrice;
        }

        if($orderId == FACEBOOK_PRODUCT_ID){
            $orders['totalPrice'] = $orders['totalPrice'] + $priceFacebookCoverPrice;
        }*/
		//Product After Cart Update
			//if($orders['type']=="addon"){continue;}
		//Product After Cart Update
        $productData = GetSglRcrdOnCndi( PRODUCT, "`id` = '" . $orderId . "'" );
		
		$deliveryTime =  ($orders['deliveryTime']>0 && $orders['deliveryTime']!="") ? $orders['deliveryTime']:0;
        $insertOrder = InsertRcrdsGetID( ["TransactionID",
                                              "TotalPrice",
                                              "Dimensional",
                                              "TypeBanner",
                                              "DefaultSize",
                                              "TurnAroundTime",
                                              "OtherSize",
                                              "ExtraAddon",
                                              "ProductID",
                                              "CustomerID",
                                              "OrderDate",
                                              "MainTitle",
											  "Singletitle",
											  "Mixtapename",
                                              "Subtitle",
                                              "EventDate",
                                              "MusicBy",
                                              "Venue",
                                              "Address",
                                              "MoreInfo",
                                              "filesImages",
                                              "venue_logo",
                                              "requirement_note",
                                              "ArtistName",
                                              "ProducedBy",
                                              "PhoneNumber",
                                              "VenueEmail",
                                              "Facebook",
                                              "Instagram",
                                              "Twitter",
                                              "ProductType",
                                              "Music",
                                              "parent_product_id",
                                              "own_song","OrderStatus","deejay_name","ename","presenting","customeProductsFileds","rep_order","template_type",'psd_file','psd3dtitle'], [$paymentRecord,
                                                            $orders['totalPrice'],
                                                            $orders['dimensional'],
                                                            $typeBanners,
                                                            $orders['defaultSize'],
                                                            $deliveryTime,
                                                            $otherSizes,
                                                            $extraAddon,
                                                            $orderId,
                                                            $_SESSION['userId'],
                                                            $orders['added'],
                                                            addslashes($customData['main_title']),
                                                            addslashes($customData['single_title']),
                                                            addslashes($customData['mixtape_name']),
                                                            addslashes($customData['sub_title']),
                                                            $customData['event_date'],
                                                            addslashes($customData['music_by']),
                                                            addslashes($customData['venue']),
                                                            addslashes($customData['address']),
                                                            addslashes($customData['more_info']),
                                                            serialize($customData['filesImages']),
                                                            $customData['venue_logo'],
                                                            addslashes($customData['requirement_note']),
                                                            addslashes($customData['artist_name']),
                                                            addslashes($customData['produced_by']),
                                                            addslashes($customData['phone_number']),
                                                            addslashes($customData['venue_email']),
                                                            addslashes($customData['facebook']),
                                                            addslashes($customData['instagram']),
                                                            addslashes($customData['twitter']),
                                                            $productData['parent_product_cat_id'],
                                                            addslashes($customData['music']),
                                                            $parent_product_id,
                                                            addslashes($customData['own_song']),"4",addslashes($customData['deejay_name']),addslashes($customData['ename']),addslashes($customData['presenting']),$customFiledsProduct,$rep_order,$orders['template_type'],$productData['psd_file'],$orders['psd3dtitle']], ORDER );
       
        $_SESSION['thankorder'][] = $insertOrder;
	
    }
    

 
}

	
    //====================Add order notification==========================//
    $Curr_Date=date('Y-m-d h:i:s');
    $nUserId=$_SESSION['userId'];
    $data = "order_id='$paymentRecord',user_id='$nUserId',notification_type='OrderCreated',read_flag='no',sale_read_flag='no',admin_read_flag='no', created_at = '$Curr_Date', description='Place order by user'";
    InsertRcrdsByData(NOTIFICATIONS2, $data);
	
	$url=$paypalURL."cgi-bin/webscr?cmd=_xclick&business=".$paypalId."&currency_code=USD&rm=2&item_name=".$namesstr."&item_number=1&return=".$return."&amount=".$cartAmount."&cancel_return=".$fail;
	
	header("location:".$url);
	
}
?>