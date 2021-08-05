<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	require_once('function/userSession.php');

if(isset($_SESSION['CART'])) {
	
	$cart = $_SESSION['CART'];
	$cartProduct = count($cart);
	if($cartProduct>0) {
		foreach($cart as $cartProducts) {
			$cartAmount = $cartAmount + $cartProducts['totalPrice'];
		}
	}
	unset($_SESSION['CART']);
	
	$tID = mt_rand(1000000,9999999);
	$tAmt = $cartAmount;
	$tDate = date("Y-m-d h:i:s",time());
	$tStatus = "failed"; 
	
   //"DiscountID", "DiscountName", "DiscountAmount, "
//   $paymentRecord = InsertRcrdsGetID(array("TransactionID", "UserID", "PurchaseDate", "Amount", "PaymentMethod", "PaymentStatus", "Status", "Createdon"), array($tID, $_SESSION['userId'], $tDate, $tAmt, 'paypal', $tStatus, "0", $systemTime), TRANSACTION);
   
	

   
   //file_get_contents(SITEURL.'mail/orderemail.php?to='.$_POST['payer_email'].'&order_id='.$tID.'&order_amount='.$tAmt);
   unset($_SESSION['CART']);
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
                <h1 class="udr-heading">Sorry <br> Payment failed</h1>
                
            </div>
        </div>
    </div>



    <?php require_once 'files/footerSection.php' ?>



    <!----SCRIPTS---------->

    <script src="js/jquery.js"></script>

    <script src="js/bootstrap.min.js"></script>

    <script src="js/popper.min.js"></script>

</body>



</html>