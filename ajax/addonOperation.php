<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
$cartAmount = $cartProduct = $response_code = 0;

$newCart = $_SESSION['CART'];
$totalAmount = $cartProductAmt = $cartAddonAmt = 0;
if(count($newCart)>0) {
    foreach($newCart as $cartProducts) {
        if($cartProducts['type'] == "main") {

            $productId = $cartProducts['id'];

            $productIdFull = "11_".$cartProducts['id'];

            if(!empty($_POST['addonProId'][$productIdFull])) {

                if(! in_array("Facebook cover",$newCart[$productId]['type_banner'])){

                    $newCart[$productId]['type_banner'][] = 'Facebook cover';

                    // $addonsData = GetSglRcrdOnCndiWthOdr(PRODUCT,"`id` = ".$productId,"`id`", "ASC");
                    // $priceFacebookcover=$addonsData['Baseprice'];

                    $getAddonsData = GetMltRcrdsOnCndiWthOdr(PRODUCT,"`id` = '11'","`id`", "ASC");
                    $priceFacebookcover=$getAddonsData[0]['Baseprice'];



                    /*$newCart[$productId]['totalPrice'] = $newCart[$productId]['totalPrice'] + $priceFacebookcover;
                    $cartProducts['totalPrice'] = $cartProducts['totalPrice'] + $priceFacebookcover;*/
                }

            }else{

                if(in_array("Facebook cover",$newCart[$productId]['type_banner'])){
                    $key =array_search ('Facebook cover', $newCart[$productId]['type_banner']);
                    unset($newCart[$productId]['type_banner'][$key]);
                    //  $addonsData = GetSglRcrdOnCndiWthOdr(PRODUCT,"`id` = ".$productId,"`id`", "ASC");
                    // $priceFacebookcover=$addonsData['Baseprice'];

                    $getAddonsData = GetMltRcrdsOnCndiWthOdr(PRODUCT,"`id` = '11'","`id`", "ASC");
                    $priceFacebookcover=$getAddonsData[0]['Baseprice'];

                    /*$newCart[$productId]['totalPrice'] = $newCart[$productId]['totalPrice'] - $priceFacebookcover;
                    $cartProducts['totalPrice'] = $cartProducts['totalPrice'] - $priceFacebookcover;*/
                }
            }
			//Product After Cart Update

            $cartProductAmt = $cartProductAmt + $cartProducts['totalPrice'];
			
			
        }
    }
}

$getAddonsData = GetMltRcrdsOnCndiWthOdr(PRODUCT,"`id` = '".FACEBOOK_PRODUCT_ID."'","`id`", "ASC");
$priceFacebookcover=$getAddonsData[0]['Baseprice'];

$totalAmount = $cartProductAmt;
extract($_POST);
$addonProd = getAddonArr();

 $price3D = $addonPrices['1'];
    $priceMotion = $addonPrices['2'];
   $priceown_music = $addonPrices['15'];
if(count($addonProId)>0) {
    $addonAmt = 0;
	
	
    foreach($addonProId as $prodcutsAdd) {
        $addonProIdFull = $prodcutsAdd;
        $prodcutsAddonArray = explode('_',$prodcutsAdd);
        $prodcutsAdd = $prodcutsAddonArray[0];
        $prodcutsBase = $prodcutsAddonArray[1];

        $addonCate = $_POST['cate_id_'.$prodcutsAdd];
       
         
            //Dimension
            $addonDima = $_POST['flyer_dimension_'.$prodcutsAdd."_".$prodcutsBase];


            //echo $addonDima."-";
            if($addonDima == "3D") {
                $addonAmt += $price3D;
            }

            //type
            $addonType = $_POST['flyerType_'.$prodcutsAdd."_".$prodcutsBase];
            if(in_array("motion", $addonType)) {
                $addonAmt += $priceMotion;
            }
            if(in_array("animated", $addonType)) {
                $addonAmt += $priceAnimation;
            }
        
        $ProductBaseID = $_POST['ProductBaseID'.$prodcutsAdd."_".$prodcutsBase];

        //DT
        $addonDTim = $_POST['addonProDtime'][$prodcutsAdd];
        if($addonDTim == "1") {
            //	$addonAmt += $turnAround1;
        } elseif($addonDTim == "2") {
            //	$addonAmt += $turnAround2;
        } elseif($addonDTim == "3") {
            //	$addonAmt += $turnAround3;
        } elseif($addonDTim == "4") {
            //		$addonAmt += $turnAround4;
        }

        //price
        $price = 0;

        if($prodcutsAdd != 11){
            $price = $addonProd[$prodcutsAdd]['Baseprice'];
        }else{
            $price = $priceFacebookcover;
        }
        $addonAmt += $price;
    }

    $totalAmount += $addonAmt;
}
$_SESSION['CART'] = $newCart;

echo formatPrice($totalAmount);
?>