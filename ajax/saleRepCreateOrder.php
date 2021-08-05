<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	$status = "error";
	$password = base64_encode($_REQUEST['password']);
	$email = $_REQUEST['emailAdress'];
	$randomKey = createRandomChar(50);
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
		
		if(!empty($_SESSION['RepKeyModify'])){
				$randomKey = $_SESSION['RepKeyModify'];
		}
	
	$_SESSION['thankorder'] = array();
	if($_REQUEST['customer']>0){
		if(!empty($_SESSION['RepKeyModify'])){
		
			DltSglRcrd(TRANSACTION,"id='".$_SESSION['paymentRecord']."'");
			DltSglRcrd(ORDER,"TransactionID='".$_SESSION['paymentRecord']."'");
		}
		$customerID = $_REQUEST['customer'];		
	}
	if($_REQUEST['customer']<0){
		if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,})$/i",$email)) {
			$Message = "Invalid Email."; 
		} else {
		$checkEmail = mysql_query("SELECT * FROM ".USERS." WHERE Email = '$email'");
	
		if(mysql_num_rows($checkEmail) == 0) {
            $name = $_REQUEST['name'];
            			
			 $fname =$_REQUEST['fname'];
           	 $lname =$_REQUEST['lname'];
			
			$fn = array("UserType", "Email", "Password", "CreatedDate", "CreatedOn", "Status","FName","LName");
			$fv = array("user", $email, $password, time(), date("Y-m-d H:i:s"), 'active',$fname,$lname);
			$customerID = InsertRcrdsGetID($fn,$fv,USERS);
			$mailchimp=syncMailchimp($email,MAILCHIMP_API_KEY,MAILCHIMP_LIST_ID,$fname,$lname);
			$status = "success";
			$redirectUrl = SITEURL.'index.php';
			$_SESSION['RepCreateOrder'] = "yes";
			$Message = "Success!  Welcome on board, we have just sent you an email with more information.";  
			  
		}
		else {
			$status = "error";
			$Message = "Oops! We already have a user with that email address. Please try another email.";
			$redirectUrl = "";
		}
		
	}		
	}
	if($customerID>0){
	$OrderType = "cart";
    $cart = $_SESSION['CART'];
    $facebookAddonData = GetSglRcrdOnCndi( PRODUCT, "id=" . FACEBOOK_PRODUCT_ID );
    $priceFacebookCoverPrice = $facebookAddonData['Baseprice'];
	
	 $tDate = date( "Y-m-d h:i:s");
	 $tAmt = $cartAmount;
	
	 $settingarr = GetSglRcrdOnCndi( SETTINGS, "id=1" );

  if ( isset( $_SESSION['DISCOUNT_DATA'] ) ) {
        $paymentRecord = InsertRcrdsGetID( [
                                            "UserID",
                                            "PurchaseDate",
                                            "Amount",
                                            "PaymentMethod",
                                            "PaymentStatus",
                                            "Status",
                                            "Createdon",
                                            "DiscountID",
                                            "DiscountName",
                                            "DiscountAmount",'RepCartData','RepUserID','RepKey','RepDiscountData'], [
                                                               $customerID,
                                                                $tDate,
                                                                formatPrice($tAmt),
                                                                '',
                                                                'Sales pending payment',
                                                                "3",
                                                                $systemTime,
                                                                $_SESSION['DISCOUNT_DATA']['discountData']['Id'],
                                                                $_SESSION['DISCOUNT_DATA']['discountData']['DiscountName'],
                                                                $_SESSION['DISCOUNT_DATA']['discountapplied'],serialize($_SESSION['CART']),$_SESSION['userId'],$randomKey,serialize($_SESSION['DISCOUNT_DATA'])], TRANSACTION );
    } else {
		
	
        $paymentRecord = InsertRcrdsGetID( [
                                            "UserID",
                                            "PurchaseDate",
                                            "Amount",
                                            "PaymentMethod",
                                            "PaymentStatus",
                                            "Status",
                                            "Createdon",'RepCartData','RepUserID','RepKey','RepDiscountData'], [
                                                           $customerID,
                                                           $tDate,
                                                           formatPrice($tAmt),
                                                           '',
                                                           "Sales pending payment",
                                                           "2",
                                                           $systemTime,serialize($_SESSION['CART']),$_SESSION['userId'],$randomKey,serialize($_SESSION['DISCOUNT_DATA'])], TRANSACTION );
    }
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
                                              "own_song","OrderStatus","deejay_name","ename","presenting","customeProductsFileds","rep_order"], [$paymentRecord,
                                                            $orders['totalPrice'],
                                                            $orders['dimensional'],
                                                            $typeBanners,
                                                            $orders['defaultSize'],
                                                            $deliveryTime,
                                                            $otherSizes,
                                                            $extraAddon,
                                                            $orderId,
                                                            $customerID,
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
                                                            addslashes($customData['own_song']),"4",addslashes($customData['deejay_name']),addslashes($customData['ename']),addslashes($customData['presenting']),$customFiledsProduct,"yes"], ORDER );
       
        $_SESSION['thankorder'][] = $insertOrder;
	
    }

 
			
			
	//	InsertRcrdsGetID(array('RepCartData','RepCustomerID','RepUserID','RepKey','RepAddedDate','RepDiscountData'),array(serialize($_SESSION['CART']),$_REQUEST['customer'],$_SESSION['userId'],$randomKey,date('Y-m-d'),serialize($_SESSION['DISCOUNT_DATA'])),REPRESENTATIVE_ORDERS);
			
			
			//$mailchimp=syncMailchimp($email,MAILCHIMP_API_KEY,MAILCHIMP_LIST_ID,$fname,$lname);
			$status = "success";
			$Message = "Success!  Welcome on board, we have just sent you an email with more information.";  
			$_SESSION['RepCreateOrder'] = "yes";
			
			$curl = curl_init();
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => SITEURL.'EmailTemplate/saleRepOrder.php?UID='.$customerID."&SalesUID=".$_SESSION['userId']."&mode=exits&key=".$randomKey."&orderids=" . implode( ",", $_SESSION['thankorder'] ),
				CURLOPT_USERAGENT => 'Curl Test'
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);
	}


    //====================Add order notification==========================//
                $Curr_Date=date('Y-m-d h:i:s');
                $nUserId=$customerID;
                $data = "order_id='$paymentRecord',user_id='$nUserId',notification_type='OrderCreated',read_flag='no',sale_read_flag='no',admin_read_flag='no', created_at = '$Curr_Date', description='Place order by user'";
                InsertRcrdsByData(NOTIFICATIONS2, $data);
	
	
	$myarray = array("Status" => $status, "Message" => $Message, "redirectUrl" => $redirectUrl);
	echo json_encode($myarray);
?>