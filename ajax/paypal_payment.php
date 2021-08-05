<?php

	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';

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
$cart = $newCart = [];
if ( isset( $_SESSION['CartRequest'] ) && count( $_SESSION['CartRequest'] ) > 0 ) {
    $newCart = $_SESSION['CartRequest'];
    foreach ( $newCart as $cartProducts1 ) {
        $cartProduct = count( $cartProducts1 );

        foreach ( $cartProducts1 as $cartProductsKey => $cartProductsValue ) {
            $cartAmount = $cartAmount + $cartProductsValue['REQUEST_CHANGE_PRICE'];

        }
    }

} else {
    $newCart = $_SESSION['CART'];
    $cartProduct = count( $newCart );
    if ( $cartProduct > 0 ) {
        foreach ( $newCart as $cartProducts ) {
            $cartAmount = $cartAmount + $cartProducts['totalPrice'];
        }
    }
}

if ( ! empty( $_POST['txn_id'] ) && ( ! empty( $_SESSION['CART'] ) || ! empty( $_SESSION['CartRequest'] )) ) {




        
        if ( isset( $_SESSION['DISCOUNT_DATA'] ) ) {
            $cartAmount = $cartAmount - $_SESSION['DISCOUNT_DATA']['discountapplied'];
			$couponData=GetSglRcrdOnCndi(DISCOUNT, "Id='".$_SESSION['DISCOUNT_DATA']['discountData']['Id']."'");
			if(!empty($couponData)){
				$DicountUsed = (int)$couponData['Used'] + (int)1; 
				mysql_query("UPDATE ".DISCOUNT." SET Used='".$DicountUsed."' WHERE Id ='".$_SESSION['DISCOUNT_DATA']['discountData']['Id']."'");
				$NumberOfUses = (int)$couponData['NumberOfUses'];
				if($NumberOfUses>0 && $DicountUsed>=$NumberOfUses){
					mysql_query("UPDATE ".DISCOUNT." SET finishUse='Yes' WHERE Id ='".$_SESSION['DISCOUNT_DATA']['discountData']['Id']."'");			
				}
			}
        }
$cartAmount  = round($cartAmount,2);
        //item information
        
        $chargeJson['amount'] = $_POST['payment_gross'];
		$chargeJson['currency'] = "USD";
		$chargeJson['status'] = $_POST['payment_status'];
		$chargeJson['id'] = $_POST['payerID'];
		
        //check whether the charge is successful
      
            //order details
            $amount = $chargeJson['amount'];
            $currency = $chargeJson['currency'];
            $status = $chargeJson['status'];
            $date = date( "Y-m-d H:i:s" );

            //insert tansaction data into the database
			$TransactionData = GetSglRcrdOnCndi(TRANSACTION, "`id` = '".$_SESSION['paymentRecord']."'");
			$rep_order = "no";
			if($TransactionData['RepUserID']>0){
				$rep_order ="yes";	
			}
			
            //if order inserted successfully
            if ( $status == 'success' && isset( $_SESSION['CART'] ) ) {
				
				
                $cart = $_SESSION['CART'];

                if ( isset( $_SESSION['DISCOUNT_DATA'] ) ) {
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
                                                        "DiscountAmount",'RepCartData','RepUserID','RepKey','RepDiscountData'], [$chargeJson['id'],
                                                                            $_SESSION['userId'],
                                                                            $date,
                                                                            $amount,
                                                                            "paypal",
                                                                            "success",
                                                                            "1",
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
                                                        "Createdon",'RepCartData','RepUserID','RepKey','RepDiscountData'], [$chargeJson['id'],
                                                                       $_SESSION['userId'],
                                                                       $date,
                                                                       $amount,
                                                                       "paypal",
                                                                       "success",
                                                                       "1",
                                                                       $systemTime,$TransactionData['RepCartData'],$TransactionData['RepUserID'],$TransactionData['RepKey'],$TransactionData['RepDiscountData']], TRANSACTION );
                }

                $facebookAddonData = GetSglRcrdOnCndi( PRODUCT, "id=" . FACEBOOK_PRODUCT_ID );
                $priceFacebookCoverPrice = $facebookAddonData['Baseprice'];
				 $settingarr = GetSglRcrdOnCndi( SETTINGS, "id=1" );
                   
                foreach ( $cart as $orders ) {
                    $typeBanners = implode( ",", $orders['type_banner'] );
                    $otherSizes = implode( ",", $orders['otherSize'] );
                    $extraAddon = implode( ",", $orders['extraAddon'] );
                    $customData = $orders['customData'];
					
					$customFiledsProduct = "";
					$customFiledsProductArray = $orders['customeProductFields'];
					
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
		//	if($orders['type']=="addon"){continue;}
		//Product After Cart Update

                    $productData = GetSglRcrdOnCndi( PRODUCT, "`id` = '" . $orders['id'] . "'" );

                    if ( ! empty( $orders['deliveryTime'] ) )
                        $turnAround = $orders['deliveryTime']; else
                        $turnAround = 0;
                    if ( $settingarr['auto_assign'] == 'Yes' ) {
                        $query_user = "SELECT UserID FROM " . USERS . " WHERE UserType= 'designer'";
                        $fetch_arr = mysql_query( $query_user );
                        $usersArr = mysql_fetch_array( $fetch_arr );
                        $UserID = $usersArr[0];
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
                                                          "AssignedTo",
                                                          "AssignedOn",
                                                          "ArtistName",
                                                          "ProducedBy",
                                                          "PhoneNumber",
                                                          "VenueEmail",
                                                          "Facebook",
                                                          "Instagram",
                                                          "Twitter",
                                                          "ProductType",
                                                          "Music",
														    "OrderStatus",
                                                          "parent_product_id",
                                                          "own_song","deejay_name","ename","presenting","customeProductsFileds","rep_order","template_type",'psd_file','psd3dtitle'], [$paymentRecord,
                                                                        $orders['totalPrice'],
                                                                        $orders['dimensional'],
                                                                        $typeBanners,
                                                                        $orders['defaultSize'],
                                                                        $turnAround,
                                                                        $otherSizes,
                                                                        $extraAddon,
                                                                        $orderId,
                                                                        $_SESSION['userId'],
                                                                        $orders['added'],
                                                                       addslashes($customData['main_title']),
																		addslashes($customData['single_title']),
																		addslashes($customData['mixtape_name']),
                                                                        addslashes($customData['sub_title']),
                                                                        addslashes($customData['event_date']),
                                                                        addslashes($customData['music_by']),
                                                                        addslashes($customData['venue']),
                                                                        addslashes($customData['address']),
                                                                        $customData['more_info'],
                                                                        serialize($customData['filesImages']),
                                                                        $customData['venue_logo'],
                                                                        addslashes($customData['requirement_note']),
                                                                        $UserID,
                                                                        $systemTime,
                                                                        addslashes($customData['artist_name']),
                                                                        addslashes($customData['produced_by']),
                                                                        addslashes($customData['phone_number']),
                                                                        addslashes($customData['venue_email']),
                                                                        addslashes($customData['facebook']),
                                                                        addslashes($customData['instagram']),
                                                                        addslashes($customData['twitter']),
                                                                        $productData['parent_product_cat_id'],
                                                                        addslashes($customData['music']),
																		"1",
                                                                        $parent_product_id,
                                                                        addslashes($customData['own_song']),addslashes($customData['deejay_name']),addslashes($customData['ename']),addslashes($customData['presenting']),$customFiledsProduct,$rep_order,$orders['template_type'],$productData['psd_file'],$orders['psd3dtitle']], ORDER );
                    } else {
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
                                                          "FilesImages",
                                                          "venue_logo",
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
                                                          "own_song","deejay_name","ename","presenting","customeProductsFileds","rep_order","template_type",'psd_file','psd3dtitle'], [$paymentRecord,
                                                                        $orders['totalPrice'],
                                                                        $orders['dimensional'],
                                                                        $typeBanners,
                                                                        $orders['defaultSize'],
                                                                        $turnAround,
                                                                        $otherSizes,
                                                                        $extraAddon,
                                                                        $orderId,
                                                                        $_SESSION['userId'],
                                                                        $orders['added'],
                                                                        addslashes($customData['main_title']),
																		addslashes($customData['single_title']),
																		addslashes($customData['mixtape_name']),
                                                                        addslashes($customData['sub_title']),
                                                                        addslashes($customData['event_date']),
                                                                        addslashes($customData['music_by']),
                                                                        addslashes($customData['venue']),
                                                                        addslashes($customData['address']),
                                                                        addslashes($customData['more_info']),
                                                                       serialize($customData['FilesImages']),
                                                                       
                                                                        $customData['venue_logo'],
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
                                                                        addslashes($customData['own_song']),addslashes($customData['deejay_name']),addslashes($customData['ename']),addslashes($customData['presenting']),$customFiledsProduct,$rep_order,$orders['template_type'],$productData['psd_file'],$orders['psd3dtitle']], ORDER );
                    }
                    $myorder[] = $insertOrder;
                    $_SESSION['thankorder'][] = $insertOrder;
                }
				
				//DltSglRcrd(TRANSACTION,"id='".$_SESSION['paymentRecord']."'");
				//DltSglRcrd(ORDER,"TransactionID='".$_SESSION['paymentRecord']."'");

				
                //unset( $_SESSION['DISCOUNT_DATA'] );
                $curl = curl_init();
                curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                                           CURLOPT_URL            => SITEURL . 'EmailTemplate/order.php?UID=' . $_SESSION['userId'] . "&orderids=" . implode( ",", $_SESSION['thankorder'] ),
                                           CURLOPT_USERAGENT      => 'Curl Test'] );

                $resp = curl_exec( $curl );

                curl_close( $curl );
				
				
				if ( $settingarr['auto_assign'] == 'Yes' ) {
		
            $query_user = "SELECT UserID FROM " . USERS . " WHERE UserType= 'designer'";
            $fetch_arr = mysql_query( $query_user );
            $usersArr = mysql_fetch_array( $fetch_arr );
            $UserID = $usersArr[0];
		  $curl = curl_init();
    curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                               CURLOPT_URL            => SITEURL . 'EmailTemplate/auto_assigned_order.php?designer_id=' . $UserID . "&orderids=" . implode( ",", $_SESSION['thankorder']),
                               CURLOPT_USERAGENT      => 'Curl Test'] );
    $resp = curl_exec( $curl );
    curl_close( $curl );
	 }

                //header( "location:thankyou.php?stripepayment=success&orderids=" . implode( ",", $myorder ) );
               // exit();

            
				
  
			} else if ( $status == 'success' && isset( $_SESSION['CartRequest'] ) ) {

				if ( isset( $_SESSION['DISCOUNT_DATA'] ) ) {
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
                                                        "DiscountAmount",'RepCartData','RepUserID','RepKey','RepDiscountData'], [$chargeJson['id'],
                                                                            $_SESSION['userId'],
                                                                            $date,
                                                                            $amount,
                                                                            "paypal",
                                                                            "success",
                                                                            "1",
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
                                                        "Createdon",'RepCartData','RepUserID','RepKey','RepDiscountData'], [$chargeJson['id'],
                                                                       $_SESSION['userId'],
                                                                       $date,
                                                                       $amount,
                                                                       "paypal",
                                                                       "success",
                                                                       "1",
                                                                       $systemTime,$TransactionData['RepCartData'],$TransactionData['RepUserID'],$TransactionData['RepKey'],$TransactionData['RepDiscountData']], TRANSACTION );
                }
							

                foreach ( $_SESSION['CartRequest'] as $key => $value ) {
                    foreach ( $value as $single_key => $single_value ) {
           
                	$HoursChk =$single_value['24HoursChk']; 
                if($single_value['24HoursChk']==""){
					$HoursChk = 0;		
				}
				
				if($single_value['TypeBanner']==""){
					$single_value['TypeBanner'] = "default";	
				}
				
				if($single_value['Size']==""){
					$single_value['Size'] = "0";	
				}
				$attachementName = $filenameAttachThumb  = array();
				if($single_value['Attachment']!=""){
					  $dynamic_dir = "uploads/work/";
					$Attachment = unserialize($single_value['Attachment']);
					foreach($Attachment as $single){
						$filename_arr = explode('.',$single);
						$file_size = filesize(getcwd()."/".$dynamic_dir."/".$single);
						$upload = 	$single;
						$attachementName[]  = $upload;	
					}  	
				}
				
				if($single_value['AttachmentThumb']!=""){
					  $dynamic_dir = "uploads/work/";
					$AttachmentThumb = unserialize($single_value['AttachmentThumb']);
					foreach($AttachmentThumb as $single){
					
						$filename_arr = explode('.',$single);
						$file_size = filesize(getcwd().$dynamic_dir.$single);
						$uploadThumb = 	$single;	
						$filenameAttachThumb[] =array("large"=>$uploadThumb,"small"=>$uploadThumb);	
					}  	
				}
				
				
				InsertRcrdsByData( CHANGE_REQ, "`OrderID` = '" . $single_value['orderIID'] . "',`ProductID` = '".$single_key."',`TransactionOrderID` = '" . $single_value['TransactionOrderID'] . "', `UserID`='" . $single_value['UserID'] . "', `DesignerID`='" . $single_value['DesignerID'] . "', `MessageText` ='" . addslashes($single_value['MessageText']) . "', `Attachment`='" . serialize($attachementName). "',`AttachmentThumb` = '" . serialize($filenameAttachThumb) . "', `AttechmentName` = '" . serialize($attachementName). "', `UserReadState` = '" . $single_value['UserReadState'] . "', `CreationDate` = '" . $systemTime . "', `SystemIP` = '" . $systemIp . "', `MediaPrice` = '" . $single_value['REQUEST_CHANGE_PRICE'] . "', `status` = 'Paid', `transctionIDRequest` = '" . $paymentRecord . "', `24HoursChk` = '" .$HoursChk . "', `Type` = 'user',`TypeBanner` = '".$single_value['TypeBanner']."',`Size` = '".$single_value['Size']."',`ResponseState` = '3'");


           
            UpdateRcrdOnCndi( ORDER, "`ResponseState` = '2'", "`TransactionID` = '" . $single_value['TransactionOrderID'] . "' AND `Id` = '" . $single_value['orderIID'] . "' AND `CustomerID` = '" . $single_value['UserID'] . "'" );

            $curl = curl_init();
            // Set some options - we are passing in a useragent too here
            curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                                       CURLOPT_URL            => SITEURL . 'EmailTemplate/workResponse.php?OID=' . $single_value['orderIID'] . "&reset=" . $reset,
                                       CURLOPT_USERAGENT      => 'Curl Test'] );
            // Send the request & save response to $resp
            $resp = curl_exec( $curl );
            // Close request to clear up some resources
            curl_close( $curl );
		  }


                }
                //header( "location:thankyou.php?stripepayment=success" );
               // exit();
            } else {
                $statusMsg = "Transaction has been failed";
            }
        
     
} else {
    $statusMsg = "Form submission error.......";
}
if ( ! empty( $statusMsg ) ) {
    $_SESSION['ERROR'] = $statusMsg;
    echo $statusMsg;
}