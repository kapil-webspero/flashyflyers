<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';
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
				$cartAmount = $cartAmount + MEDIA_CHANGE_PRICE;
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
	
	$url=$paypalURL."cgi-bin/webscr?cmd=_xclick&business=".$paypalId."&currency_code=USD&rm=2&item_name=".$namesstr."&item_number=1&return=".$return."&amount=".$cartAmount."&cancel_return=".$fail;
	
	header("location:".$url);
	
}
?>