<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	$cartAmount = $cartProduct = $response_code = 0;
	$status = $message = "";
	extract($_REQUEST); 
	//print_r($_REQUEST);   
	
	if(isset($action) && !empty($action) && $action == "update") {
		$cart = $_SESSION['CART'];
		$newCart = $cart;
		unset($_SESSION['CART']);
		
		$selAddon = $_REQUEST['addonProId'];
		$delTimes = $_REQUEST['addonProDtime'];
		
		//remove unchecked addon first
		foreach($cart as $tempKey => $tempCart) {
			if($tempCart['type'] == "addon") {
				if(!in_array($tempKey,$selAddon)) {
					unset($newCart[$tempKey]);	
				}
			}
		}
		
		$cart1 = array();
		if(count($selAddon)>0) {
			$addonArr = getAddonArr();
			
			$dimensional = "2D";
			$type_banner = array("static");
			$otherSize = array();
			
			foreach($selAddon as $myAddons) {
				$addonData = $addonArr[$myAddons];
				$deliveryTime = $totalAmount = 0;
				$totalAmount = $addonData['Baseprice'];
				$defaultSize = $addonData['Defaultsizes'];
				if(isset($delTimes[$myAddons]) && $delTimes[$myAddons]>0) {
					$deliveryTime = $delTimes[$myAddons];
					if($deliveryTime == "1") {
						$totalAmount += $turnAround1;
					} elseif($deliveryTime == "2") {
						$totalAmount += $turnAround2;
					} elseif($deliveryTime == "3") {
						$totalAmount += $turnAround3;
					} elseif($deliveryTime == "4") {
						$totalAmount += $turnAround4;
					}
				}
				
				
				/*
				
				if(count($addonProId)>0) {
		$addonAmt = 0;
		foreach($addonProId as $prodcutsAdd) {
			//Category
			$addonCate = $_POST['cate_id_'.$prodcutsAdd];			
			if($addonCate != "91") {
				if($addonCate == '93') { 
					$price3D = 10;
					$priceMotion = 10;
					$priceAnimation = 20;
				}
				//Dimension
				$addonDima = $_POST['flyer_dimension_'.$prodcutsAdd];
				if($addonDima == "3D") {
					$addonAmt += $price3D;	
				}
				//type
				$addonType = $_POST['flyerType_'.$prodcutsAdd];
				if(in_array("motion", $addonType)) {
					$addonAmt += $priceMotion;
				}
				if(in_array("animated", $addonType)) {
					$addonAmt += $priceAnimation;
				}
			}
			//DT
			$addonDTim = $_POST['addonProDtime'][$prodcutsAdd];
			if($addonDTim == "1") {
				$addonAmt += $turnAround1;
			} elseif($addonDTim == "2") {
				$addonAmt += $turnAround2;
			} elseif($addonDTim == "3") {
				$addonAmt += $turnAround3;
			} elseif($addonDTim == "4") {
				$addonAmt += $turnAround4;
			}
			//price
			$price = $addonProd[$prodcutsAdd]['Baseprice'];
			$addonAmt += $price;
		}
		$totalAmount += $addonAmt;
	}
				
				*/
				
				
				$cart1[$myAddons] = array("added" => $systemTime, "id" => $myAddons, "type" => "addon", "dimensional" => '2D', "type_banner" => $type_banner, "defaultSize" => $defaultSize, "deliveryTime" => $deliveryTime, "otherSize" => $otherSize, "totalPrice" =>$totalAmount);				
			}
		}
		foreach($cart1 as $key => $addonProduct) {
			if(isset($newCart[$key]) && !empty($newCart[$key])) {
				unset($newCart[$key]);
			}
			$newCart[$key] = $addonProduct;
		}
		$_SESSION['CART'] = $newCart;
		$status = "success";
		$message = "Addon successfully added into your cart";
		$response_code = "0001";
		$cartProduct = count($cart);
		
	} else {
		$status = "error";
		$response_code = "0101";
		$message = "Process action not defined.";
	}
	$newCart = $_SESSION['CART'];
	$cartProduct = count($newCart);
	if($cartProduct>0) {
		foreach($newCart as $cartProducts) {
			$cartAmount = $cartAmount + $cartProducts['totalPrice'];
		}
	}
	echo json_encode(array("status"=>$status, "response_code" => $response_code, "message" => $message, "cart_count" => $cartProduct, "amount_count" => $cartAmount));
?>