<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
$cartAmount = $cartProduct = $response_code = 0;
$cart = $newCart = array();
$status = $message = "";
extract($_REQUEST);

unset($_SESSION['CartRequest']);

$getAddonsData = GetMltRcrdsOnCndiWthOdr(PRODUCT,"`id` = '".FACEBOOK_PRODUCT_ID."'","`id`", "ASC");
$priceFacebookcover=$getAddonsData[0]['Baseprice'];


if(isset($action) && !empty($action) && $action == "update") {
    $cart = $_SESSION['CART'];
    $newCart = $cart;
    //unset($_SESSION['CART']);

    $selAddon = $_REQUEST['addonProId'];
    $delTimes = $_REQUEST['addonProDtime'];

    //remove unchecked addon first
    foreach($cart as $tempKey => $tempCart) {
		
			
        if($tempCart['type'] == "addon") {
         $expolodeID = explode("_",$tempCart['id']);
		 	if(isset($cart[$expolodeID[1]]['customeProductFields']) && !empty($cart[$expolodeID[1]]['customeProductFields'])){}else{
		    	unset($newCart[$tempKey]);
			}
        }
    }
	

    if(count($addonProId)>0) {
        $addonArr = getAddonArr();
        $dimensional = "2D";
        $type_banner = array("static");
        $otherSize = array();

        foreach($addonProId as $prodcutsAdd) {

            $prodcutAddonFullId = $prodcutsAdd;
            $prodcutAddonBaseId = $prodcutsAdd;
            $prodcutsAddonArray = explode('_',$prodcutsAdd);
            $prodcutsAdd = $prodcutsAddonArray[0];
            $prodcutsBase = $prodcutsAddonArray[1];

            $productType = 'addon';
            $productId = 0;
            if($prodcutsAddonArray[0] == 11){
                $prodcutAddonBaseId = $prodcutsAddonArray[1];
                $productType = 'main';
            }

            //Category
            $addonAmt = 0;
            $addonAmtFacebook = 0;
            /*if($productType == 'main') {
                $addonAmt = $newCart[$prodcutAddonBaseId]['totalPrice'];
            }*/
            $addonCate = $_POST['cate_id_'.$prodcutsAdd];
            if($addonCate != "91") {
                if($addonCate == '93') {
                    $price3D = 10;
                    $priceMotion = 10;
                    $priceAnimation = 20;
                }

                //Dimension
                if(!empty($_POST['flyer_dimension_'.$prodcutAddonFullId])) {
                    $dimensional = $_POST['flyer_dimension_' . $prodcutAddonFullId];
                }

                if($productType == 'main') {
                    $type_banner = $newCart[$prodcutAddonBaseId]['type_banner'];
                    $dimensional = $newCart[$prodcutAddonBaseId]['dimensional'];
                    $type_banner_for_addon = $_POST['flyerType_' . $prodcutAddonFullId];
                }else{
                    $type_banner = $_POST['flyerType_' . $prodcutAddonFullId];
                }

                if($dimensional == "3D") {
                    $addonAmt += $price3D;
                }

                if(in_array("motion", $type_banner)) {
                    $addonAmt += $priceMotion;
                }
                if(in_array("animated", $type_banner)) {
                    $addonAmt += $priceAnimation;
                }

                if(!empty($type_banner_for_addon)){

                    if(in_array("motion", $type_banner_for_addon)) {

                        $addonAmtFacebook += $priceMotion;
                    }

                    if(in_array("animated", $type_banner_for_addon)) {

                        $addonAmtFacebook += $priceAnimation;
                    }
                }else{
                    $type_banner_for_addon = array("static");
                }
            }
			
            //DT

            if($productType == 'addon') {
                // $deliveryTime = $_POST['addonProDtime'][$prodcutAddonFullId];
                $deliveryTime = $newCart[$prodcutsBase]['deliveryTime'];
            }else{
                //$deliveryTime = $_POST['addonProDtime'][$prodcutAddonFullId];
                //$deliveryTime = $newCart[$prodcutAddonFullId]['deliveryTime'];
                $deliveryTime = $newCart[$prodcutsBase]['deliveryTime'];

            }
            if($deliveryTime == "1") {
                //	$addonAmt += $turnAround1;
            } elseif($deliveryTime == "2") {
                //	$addonAmt += $turnAround2;
            } elseif($deliveryTime == "3") {
                //	$addonAmt += $turnAround3;
            } elseif($deliveryTime == "4") {
                //	$addonAmt += $turnAround4;
            }
            //price
            $price = 0;
            //	if($prodcutsAdd!=11){
            $price = $addonArr[$prodcutsAdd]['Baseprice'];
            //	}

            if($productType == 'addon') {
                $defaultSize = $addonArr[$prodcutsAdd]['Defaultsizes'];
            }else{
                $defaultSize = $newCart[$prodcutAddonBaseId]['defaultSize'];
            }

            /* otherSize */
            if($productType == 'main') {
                $otherSize = $newCart[$prodcutAddonBaseId]['otherSize'];
            }

            $addonAmt += $price;
            if($productType == 'main') {
                $addonAmt = $newCart[$prodcutAddonBaseId]['totalPrice'];
            }
            $cart1[$prodcutAddonBaseId] = array("customData"=>$newCart[$prodcutAddonBaseId]['customData'],"added" => $systemTime, "id" => $prodcutAddonBaseId, "type" => $productType, "dimensional" => $dimensional, "type_banner" => $type_banner, "defaultSize" => $defaultSize, "deliveryTime" => $deliveryTime, "otherSize" => $otherSize, "totalPrice" =>$addonAmt, "ProductBaseID" =>$prodcutsBase,"template_type"=>$newCart[$prodcutsBase]['template_type'],'psd3dtitle'=>$newCart[$prodcutsBase]['psd3dtitle']);
            if($productType == 'main') {
				
                $addonAmtFacebook = $priceFacebookcover;
                $cart1[$prodcutAddonFullId] = array("added" => $systemTime, "id" => $prodcutAddonFullId, "type" => 'addon', "dimensional" => '', "type_banner" => $type_banner_for_addon, "defaultSize" => '', "deliveryTime" => $deliveryTime, "otherSize" => '', "totalPrice" =>$addonAmtFacebook, "ProductBaseID" =>$prodcutsBase,"template_type"=>$newCart[$prodcutsBase]['template_type'],'psd3dtitle'=>$newCart[$prodcutsBase]['psd3dtitle']);
            }
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