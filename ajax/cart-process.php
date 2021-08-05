<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	$cartAmount = $cartProduct = $response_code = 0;
	$cart = $newCart = array();
	$status = $message = "";
	extract($_REQUEST);

	$IsNormalProduct = 0;
	if(isset($productID) && isset($_SESSION['CART']) && !empty($productID)) {
		if(isset($action) && $action == "add") {
			$getProduct = GetSglRcrdOnCndi(PRODUCT, "`id` = '$productID'");
			$dimensional = "2D";
			$type_banner = array("static");
			$defaultSize = $getProduct['Defaultsizes'];
			$otherSize = array();
			$deliveryTime = "0";
			$totalPrice = $getProduct['Baseprice'];
			if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
				$cart = $_SESSION['CART'];
				if(isset($cart[$productID]) && !empty($cart[$productID])) {
					$status = "error";
					$message = "Product already added into your cart";
					$response_code = "0002";
					$cartProduct = count($cart);
				} else {					
					$cart[$productID] = array("added" => $systemTime, "id" => $productID, "type" => "main", "dimensional" => $dimensional, "type_banner" => $type_banner, "defaultSize" => $defaultSize, "deliveryTime" => $deliveryTime, "otherSize" => $otherSize, "totalPrice" => $totalPrice);
					unset($_SESSION['CART']);
					$_SESSION['CART'] = $cart;
					$status = "success";
					$message = "Product successfully added into your cart";
					$response_code = "0001";
					$cartProduct = count($cart);
				}
			} else {
				$cart[$productID] = array("added" => $systemTime, "id" => $productID, "type" => "main", "dimensional" => $dimensional, "type_banner" => $type_banner, "defaultSize" => $defaultSize, "deliveryTime" => $deliveryTime, "otherSize" => $otherSize, "totalPrice" => $totalPrice);
				$_SESSION['CART'] = $cart;
				$status = "success";
				$message = "Product successfully added into your cart";
				$response_code = "0001";
				$cartProduct = count($cart);
			}
		}
		if(isset($action) && $action == "remove") {
			$cart = $_SESSION['CART'];
			if(isset($cart[$productID]) && !empty($cart[$productID])) {
				unset($cart[$productID]);
				unset($_SESSION['CART']);
				$_SESSION['CART'] = $cart;
				foreach($_SESSION['CART'] as $k=>$c){
					if(isset($c['ProductBaseID']) && $c['ProductBaseID']!="" && $productID==$c['ProductBaseID']){
						unset($cart[$k]);
						unset($_SESSION['CART']);
						$_SESSION['CART'] = $cart;
						continue;
					}
				}
				$status = "success";
				$message = "Product successfully removed from your cart";
				$response_code = "0003";
				$cartProduct = count($cart);
			} else {
				$status = "error";
				$message = "Your shopping cart is currently empty.";
				$response_code = "0005";
				$cartProduct = count($cart);
			}
		}
		 
		$newCart = $_SESSION['CART'];
		//Product After Cart Update
		$foundCart = 0;
		if(!empty($newCart)){
			foreach($newCart as $cartKey=>$cartvalue){
					if($cartvalue['type']=="main"){
						$foundCart = 1; break;		
					}	
			}	
		}
		if($foundCart==0){$newCart =array();$_SESSION['CART']=array();}
		$cartProduct = count($newCart);
		if($cartProduct>0) {
			foreach($newCart as $cartProducts) {
				//Product After Cart Update
				if($cartProducts['type'] != "main") { continue; }
				$cartAmount = $cartAmount + $cartProducts['totalPrice'];
			}
		}
	} else if(isset($productID) && isset($_SESSION['CartRequest']) && !empty($productID)) {
		if(isset($action) && $action == "remove") {
			$cart = $_SESSION['CartRequest'];
			$productID = explode("_",$productID);
				
			if(!empty($cart[$productID[0]])) {
				unset($cart[$productID[0]][$productID[1]]);
				
				if(count($cart[$productID[0]])==0){
					unset($cart[$productID[0]]);	
				}
				
				$_SESSION['CartRequest'] = $cart;
				$status = "success";
				$message = "Product successfully removed from your cart";
				$response_code = "0003";
				$cartProduct = count($cart);
				foreach($_SESSION['CartRequest'] as $key=>$value){
					$cartAmount = MEDIA_CHANGE_PRICE *	count($value);
				}	
				
				
			} else {
				$status = "error";
				$message = "Product not found into your cart";
				$response_code = "0005";
				$cartProduct = count($CartRequest);
			}
		}
		
	} else {
		
	}
	
	if(!empty($_SESSION['CART'])){
		foreach($_SESSION['CART'] as $product)
		if(isset($product['customeProductFields']) && !empty($product['customeProductFields'])){
			
			
		}else{
				$IsNormalProduct = 1;
	
		}
	}
	echo json_encode(array("status"=>$status, "response_code" => $response_code, "message" => $message, "cart_count" => $cartProduct, "amount_count" => $cartAmount, "IsNormalProduct" => $IsNormalProduct));
?>