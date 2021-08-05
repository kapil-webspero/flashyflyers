<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';
//require_once('function/userSession.php');


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
    $OrderType = "cart";
    $cart = $_SESSION['CART'];
   // unset( $_SESSION['CART'] );

    $tID = $_POST['txn_id'];
    $tAmt = $_POST['payment_gross'];
    $tDate = date( "Y-m-d h:i:s", strtotime( $_POST['payment_date'] ) );
    $tStatus = str_replace( "Completed", "success", $_POST['payment_status'] );

  
          UpdateRcrdOnCndi( TRANSACTION, "`TransactionID` = '".$tID."',`PurchaseDate` = '".$tDate."',`PaymentStatus` = '".$tStatus."',`Status` = '0',`Createdon` = '".$systemTime."'", "`id` = '" . $_SESSION['paymentRecord'] . "'" );
	
  	$AssignedTo = 0;
	$AssignedOn ="";
     $settingarr = GetSglRcrdOnCndi( SETTINGS, "id=1" );
	 if ( $settingarr['auto_assign'] == 'Yes' ) {
            $query_user = "SELECT UserID FROM " . USERS . " WHERE UserType= 'designer'";
            $fetch_arr = mysql_query( $query_user );
            $usersArr = mysql_fetch_array( $fetch_arr );
        	$AssignedTo = $usersArr[0];
			$AssignedOn = $systemTime;
			
			
				  $curl = curl_init();
    curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                               CURLOPT_URL            => SITEURL . 'EmailTemplate/auto_assigned_order.php?designer_id=' . $AssignedTo . "&orderids=" . implode( ",", $_SESSION['thankorder']),
                               CURLOPT_USERAGENT      => 'Curl Test'] );
    $resp = curl_exec( $curl );
    curl_close( $curl );
	 }
	 
	 UpdateRcrdOnCndi( ORDER, "`OrderStatus` = '1',`CustomerID` = '".$_SESSION['userId']."',`ResponseState`='0',`AssignedTo` = '".$AssignedTo."',`AssignedOn` = '".$AssignedOn."'", "`TransactionID` = '" .  $_SESSION['paymentRecord'] . "'" );
  
  
    
    $curl = curl_init();

    curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                               CURLOPT_URL            => SITEURL . 'EmailTemplate/order.php?UID=' . $_SESSION['userId'] . "&orderids=" . implode( ",", $_SESSION['thankorder'] ),
                               CURLOPT_USERAGENT      => 'Curl Test'] );

    $resp = curl_exec( $curl );

    curl_close( $curl );
	
    unset( $_SESSION['CART'] );
    unset( $_SESSION['DISCOUNT_DATA'] );
	
	    unset( $_SESSION['paymentRecord'] );
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
				
				
				if($single_value['TypeBanner']==""){
					$single_value['TypeBanner'] = "default";	
				}
				
				if($single_value['Size']==""){
					$single_value['Size'] = "0";	
				}
				InsertRcrdsByData( CHANGE_REQ, "`OrderID` = '" . $single_value['orderIID'] . "',`ProductID` = '".$single_key."',`TransactionOrderID` = '" . $single_value['TransactionOrderID'] . "', `UserID`='" . $single_value['UserID'] . "', `DesignerID`='" . $single_value['DesignerID'] . "', `MessageText` ='" . addslashes($single_value['MessageText']) . "', `Attachment`='" . $single_value['Attachment'] . "', `AttechmentName` = '" . $single_value['AttechmentName'] . "', `UserReadState` = '" . $single_value['UserReadState'] . "', `CreationDate` = '" . $systemTime . "', `SystemIP` = '" . $systemIp . "', `MediaPrice` = '" . MEDIA_CHANGE_PRICE . "', `status` = 'Paid', `transctionIDRequest` = '" . $paymentRecord . "', `24HoursChk` = '" . $HoursChk. "', `Type` = 'user',`TypeBanner` = '".$single_value['TypeBanner']."',`Size` = '".$single_value['Size']."',`ResponseState` = '3'");
				
				


           
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
            <?php
			    unset( $_SESSION['thankorder'] );
			 }
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
unset( $_SESSION['guest_checkout'] );
?>
<style>
    .cart-desktop { display: none !important;}
</style>
</body>


</html>