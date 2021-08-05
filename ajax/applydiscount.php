<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	$status = $html = "";
	$valsav=  $discountValue  = $discountper= 0;
	if(isset($_SESSION['CartRequest'])){
	$newCart = $_SESSION['CartRequest'];
		if($newCart>0) {
			foreach($newCart as $cartProducts1) {
			$cartProduct = count($cartProducts1);
		
			foreach($cartProducts1 as $cartProductsKey=>$cartProductsValue) {
				$cartAmount = $cartAmount +$cartProductsValue['REQUEST_CHANGE_PRICE'];
			}
			}
		}
	}else{
	
	$newCart = $_SESSION['CART'];
	$cartProduct = count($newCart);
	if($cartProduct>0) {
		foreach($newCart as $cartProducts) {
			$cartAmount = $cartAmount + $cartProducts['totalPrice'];
		}
	}
	}
	if(isset($_POST['coupon']) ) {
		$code=$_POST['coupon'];
		$coupon=GetSglRcrdOnCndi(DISCOUNT, "finishUse ='NO' AND DiscountName='".$code."' and EndDate>=".time());
		if($coupon['NumberOfUses']>0){
		if($coupon['NumberOfUses']<= $coupon['Used']){
				unset($_SESSION['DISCOUNT_DATA']);
				$html.="Discount code is invalid";	
				$status="error";
				
			echo json_encode(array('message'=>$html,'status'=>$status,'valsav'=>$valsav,"discount"=>$discountValue,'discountper'=>$discountper));
			exit;

		}
		}
		if($coupon['CustomerID'] >0 ){
			if($_SESSION['userId']!=$coupon['CustomerID'] ){
				unset($_SESSION['DISCOUNT_DATA']);
				$html.="Discount code is invalid";	
				$status="error";
				
			echo json_encode(array('message'=>$html,'status'=>$status,'valsav'=>$valsav,"discount"=>$discountValue,'discountper'=>$discountper));
			exit;
			
			}
			
			
		}
		
		if(!empty($coupon)) {
			
			$amount=0;
			if($coupon['Type']==1) {
				if($coupon['Value']>=$cartAmount) {
				$amount=$cartAmount;
				} else {
				$amount=$coupon['Value'];
				}
					
				
			} else if($coupon['Type']==2) {
				$amount=$cartAmount*($coupon['Value']/100);
				$discountper = $coupon['Value'];
			}
			$valsav=$cartAmount-$amount;
			$discountValue = $amount;
			$_SESSION['DISCOUNT_DATA']=array(
				'discountData'=>$coupon,
				'discountapplied'=>$amount,
				'discountper'=>$discountper,
			);
			$html.="Great! You have received a $".number_format((float)$amount, 2, '.', '')." discount.";
			$status="success";
			
		} else {
			unset($_SESSION['DISCOUNT_DATA']);
			$html.="Discount code is invalid";	
			$status="error";
			$valsav=$cartAmount;
		}
	} else {
		
			unset($_SESSION['DISCOUNT_DATA']);
		$html.="Invalid request";	
		$status="error";
		$valsav=$cartAmount;
	}
	echo json_encode(array('message'=>$html,'status'=>$status,'valsav'=>$valsav,"discount"=>$discountValue,'discountper'=>$discountper));