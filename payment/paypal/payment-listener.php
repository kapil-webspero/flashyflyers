<?php
$paypalURL = "https://www.sandbox.paypal.com/";	
$paypalId="karan@evilla.com";	
$return="http://optimabranding.com/5/FlashyFlyers/Developer/thankyou.php?paypalpayment=success";
$fail="http://optimabranding.com/5/FlashyFlyers/Developer/fail.php";
$amount=10;
if(isset($_SESSION['CART'])) {
$url=$paypalURL."cgi-bin/webscr?cmd=_xclick&business=".$paypalId."&currency_code=USD&item_name=PAID-SERVICE&item_number=1&rm=2&amount=".$amount."&return=".$return."&cancel_return=".$fail;
header("location:".$url);
}
?>