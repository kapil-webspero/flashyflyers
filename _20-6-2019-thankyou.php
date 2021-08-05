<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';
//require_once('function/userSession.php');

$_SESSION['thankorder'] = [];
$OrderType = "cart";

if ( ! empty( $_SESSION['CartRequest'] ) ) {
    $jm = 0;
    $OrderType = "cartRequest";
    $orderIDS = "";
    $noOfOrders = count( $_SESSION['CartRequest'] );
    foreach ( $_SESSION['CartRequest'] as $key => $value ) {
        if ( $jm == 0 ) {
            $orderIDS = $key;
        }
        $jm++;

    }
}

if ( isset( $_SESSION['CART'] ) && $_REQUEST['paypalpayment'] == 'success' && isset( $_POST['txn_id'] ) && ! empty( $_POST['txn_id'] ) ) {
    $OrderType = "cart";
    $cart = $_SESSION['CART'];
    unset( $_SESSION['CART'] );

    $tID = $_POST['txn_id'];
    $tAmt = $_POST['payment_gross'];
    $tDate = date( "Y-m-d h:i:s", strtotime( $_POST['payment_date'] ) );
    $tStatus = str_replace( "Completed", "success", $_POST['payment_status'] );

    //"DiscountID", "DiscountName", "DiscountAmount, "
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
                                            "DiscountAmount"], [$tID,
                                                                $_SESSION['userId'],
                                                                $tDate,
                                                                $tAmt,
                                                                'paypal',
                                                                $tStatus,
                                                                "0",
                                                                $systemTime,
                                                                $_SESSION['DISCOUNT_DATA']['discountData']['Id'],
                                                                $_SESSION['DISCOUNT_DATA']['discountData']['DiscountName'],
                                                                $_SESSION['DISCOUNT_DATA']['discountapplied']], TRANSACTION );
    } else {
        $paymentRecord = InsertRcrdsGetID( ["TransactionID",
                                            "UserID",
                                            "PurchaseDate",
                                            "Amount",
                                            "PaymentMethod",
                                            "PaymentStatus",
                                            "Status",
                                            "Createdon"], [$tID,
                                                           $_SESSION['userId'],
                                                           $tDate,
                                                           $tAmt,
                                                           'paypal',
                                                           $tStatus,
                                                           "0",
                                                           $systemTime], TRANSACTION );
    }

    $facebookAddonData = GetSglRcrdOnCndi( PRODUCT, "id=" . FACEBOOK_PRODUCT_ID );
    $priceFacebookCoverPrice = $facebookAddonData['Baseprice'];
	 $settingarr = GetSglRcrdOnCndi( SETTINGS, "id=1" );
    foreach ( $cart as $orders ) {
		

        $typeBanners = implode( ",", $orders['type_banner'] );
        $otherSizes = implode( ",", $orders['otherSize'] );
        $extraAddon = implode( ",", $orders['extraAddon'] );
        $customData = $orders['customData'];

       

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
                                              "Subtitle",
                                              "EventDate",
                                              "MusicBy",
                                              "Venue",
                                              "Address",
                                              "MoreInfo",
                                              "Filename1",
                                              "Filename2",
                                              "Filename3",
                                              "Filename4",
                                              "Filename5",
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
											
                                              "own_song"], [$paymentRecord,
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
                                                            $customData['main_title'],
                                                            $customData['sub_title'],
                                                            $customData['event_date'],
                                                            $customData['music_by'],
                                                            $customData['venue'],
                                                            $customData['address'],
                                                            $customData['more_info'],
                                                            $customData['file1'],
                                                            $customData['file2'],
                                                            $customData['file3'],
                                                            $customData['file4'],
                                                            $customData['file5'],
                                                            $customData['venue_logo'],
                                                            $customData['requirement_note'],
                                                            $UserID,
                                                            $systemTime,
                                                            $customData['artist_name'],
                                                            $customData['produced_by'],
                                                            $customData['phone_number'],
                                                            $customData['venue_email'],
                                                            $customData['facebook'],
                                                            $customData['instagram'],
                                                            $customData['twitter'],
                                                            $productData['ProductType'],
                                                            $customData['music'],
															"1",
                                                            $parent_product_id,$customData['own_song']], ORDER );
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
                                              "Subtitle",
                                              "EventDate",
                                              "MusicBy",
                                              "Venue",
                                              "Address",
                                              "MoreInfo",
                                              "Filename1",
                                              "Filename2",
                                              "Filename3",
                                              "Filename4",
                                              "Filename5",
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
                                              "own_song"], [$paymentRecord,
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
                                                            $customData['main_title'],
                                                            $customData['sub_title'],
                                                            $customData['event_date'],
                                                            $customData['music_by'],
                                                            $customData['venue'],
                                                            $customData['address'],
                                                            $customData['more_info'],
                                                            $customData['file1'],
                                                            $customData['file2'],
                                                            $customData['file3'],
                                                            $customData['file4'],
                                                            $customData['file5'],
                                                            $customData['venue_logo'],
                                                            $customData['requirement_note'],
                                                            $customData['artist_name'],
                                                            $customData['produced_by'],
                                                            $customData['phone_number'],
                                                            $customData['venue_email'],
                                                            $customData['facebook'],
                                                            $customData['instagram'],
                                                            $customData['twitter'],
                                                            $productData['ProductType'],
                                                            $customData['music'],
                                                            $parent_product_id,
                                                            $customData['own_song']], ORDER );
        }
        $_SESSION['thankorder'][] = $insertOrder;
	
    }

    $curl = curl_init();

    curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                               CURLOPT_URL            => SITEURL . 'EmailTemplate/order.php?UID=' . $_SESSION['userId'] . "&orderids=" . implode( ",", $_SESSION['thankorder'] ),
                               CURLOPT_USERAGENT      => 'Curl Test'] );

    $resp = curl_exec( $curl );

    curl_close( $curl );
	//auto assing mail
	
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
	
	
    unset( $_SESSION['CART'] );
    unset( $_SESSION['DISCOUNT_DATA'] );
}

if ( isset( $_SESSION['CartRequest'] ) && $_REQUEST['paypalpayment'] == 'success' && isset( $_POST['txn_id'] ) && ! empty( $_POST['txn_id'] ) ) {
    $tID = $_POST['txn_id'];
    $tAmt = $_POST['payment_gross'];
    $tDate = date( "Y-m-d h:i:s", strtotime( $_POST['payment_date'] ) );


    $paymentRecord = InsertRcrdsGetID( ["TransactionID",
                                        "UserID",
                                        "PurchaseDate",
                                        "Amount",
                                        "PaymentMethod",
                                        "PaymentStatus",
                                        "Status",
                                        "Createdon"], [$tID,
                                                       $_SESSION['userId'],
                                                       $tDate,
                                                       $tAmt,
                                                       'paypal',
                                                       $tStatus,
                                                       "0",
                                                       $systemTime], TRANSACTION );


    $OrderType = "cartRequest";
    $jm = 0;
    $orderIDS = "";
    $noOfOrders = count( $_SESSION['CartRequest'] );
    foreach ( $_SESSION['CartRequest'] as $key => $value ) {
        if ( $jm == 0 ) {
            $orderIDS = $key;
        }
        $jm++;
        foreach ( $value as $single_key => $single_value ) {
           
		   		$HoursChk  =  $single_value['24HoursChk']; 
                if($single_value['24HoursChk']==""){
					$HoursChk = 0;		
				}
				
				InsertRcrdsByData( CHANGE_REQ, "`OrderID` = '" . $single_value['orderIID'] . "',`ProductID` = '".$single_key."',`TransactionOrderID` = '" . $single_value['TransactionOrderID'] . "', `UserID`='" . $single_value['UserID'] . "', `DesignerID`='" . $single_value['DesignerID'] . "', `MessageText` ='" . $single_value['MessageText'] . "', `Attachment`='" . $single_value['Attachment'] . "', `AttechmentName` = '" . $single_value['AttechmentName'] . "', `UserReadState` = '" . $single_value['UserReadState'] . "', `CreationDate` = '" . $systemTime . "', `SystemIP` = '" . $systemIp . "', `MediaPrice` = '" . MEDIA_CHANGE_PRICE . "', `status` = 'Paid', `transctionIDRequest` = '" . $paymentRecord . "', `24HoursChk` = '" . $HoursChk. "', `Type` = 'user'");


           
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


    unset( $_SESSION['CART'] );
    unset( $_SESSION['DISCOUNT_DATA'] );
    unset( $_SESSION['CartRequest'] );
}

if ( $_GET['stripepayment'] == 'success' && isset( $_GET['orderids'] ) ) {
    $_SESSION['thankorder'] = explode( ",", $_GET['orderids'] );
}

?>
<!DOCTYPE html>

<html lang="en">


<head>

    <title>Thank you | Flashy Flyers</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require_once 'files/headSection.php'; ?>

</head>


<body>


<?php require_once 'files/headerSection.php'; ?>


<div class="page-wrap bubble-bg-1">
    <div class="container">
        <div class="thankyou-msg bx-shadow">
            <h1 class="udr-heading">Thank you <br> for your order!</h1>
            <?php if ( ! empty( $_SESSION['thankorder'] ) && $OrderType != "cartRequest" ) {
                $orderID = $_SESSION['thankorder'][0];
                $getTransID = GetSglDataOnCndi( ORDER, "`Id` = '" . $orderID . "'", "TransactionID" );
                ?>
                <a href="<?= SITEURL . 'my-orders.php?orderUID=' . $getTransID ?>" class="btn-grad btn-lg"
                   style="margin-bottom:15px;">View my order #<?= $getTransID ?></a>
            <?php }
            if ( $OrderType == "cartRequest" ) {
                if ( $noOfOrders > 1 ) {

                    ?>
                    <a href="<?= SITEURL . 'my-orders.php' ?>" class="btn-grad btn-lg" style="margin-bottom:15px;">View
                        my orders</a>
                    <?php
                } else {

                    ?>
                    <a href="<?= SITEURL . 'my-orders.php?orderUID=' . $orderIDS ?>" class="btn-grad btn-lg"
                       style="margin-bottom:15px;">View my order #<?= $orderIDS ?></a>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>


<?php require_once 'files/footerSection.php' ?>


<!----SCRIPTS---------->

<script src="js/jquery.js"></script>

<script src="js/bootstrap.min.js"></script>

<script src="js/popper.min.js"></script>
<?php
unset( $_SESSION['CART'] );
unset( $_SESSION['DISCOUNT_DATA'] );
unset( $_SESSION['CartRequest'] );
?>
<style>
    .cart-desktop { display: none !important;}
</style>
</body>


</html>