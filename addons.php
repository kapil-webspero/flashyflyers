<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';

	if(is_cart_empty()) {
		header("location:".SITEURL)	;
		exit();
	}
if(empty($_SESSION['userId'])){
	  header("location:info.php");
    exit();
}
	 if(isset($_SESSION['CartRequest']) && count($_SESSION['CartRequest'])>0){
			header("location:cart.php")	;
		exit();
	 }
$IsNormalProduct =0;
if(!empty($_SESSION['CART'])){
	foreach($_SESSION['CART'] as $product){
		if(isset($product['customeProductFields']) && !empty($product['customeProductFields'])){

		}else{
			$IsNormalProduct =1;
		}
	}
	if($IsNormalProduct==0){
		header("location:checkout.php")	;
		exit();
	}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Add ons | Flashy Flyers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once 'files/headSection.php'; ?>
    <style>
	.addon-info .custom-control {
		display: inline;
	}
	.addon-info .custom-control-description {
    	top: 4px;
	}
	.addon-info .custom-control-description strong {
		 margin-left: 5px;
	}
	</style>
</head>

<body class="AddonsPage">
    <?php require_once 'files/headerSection.php'; ?>
    <div class="page-wrap pb-5 pt-3 bubble-bg-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                	<div class="notification warning">
                        <div class="d-flex"><i class="fas fa-bell"></i></div>
                        <span>Warning: General message</span>
                        <button class="close-ntf"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="notification success">
                        <div class="d-flex"><i class="fas fa-check"></i></div>
                        <span>Message sent with success</span>
                        <button class="close-ntf"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="notification error">
                        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
                        <span>Error: Email not valid</span><button class="close-ntf"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="wizard">
                        <div class="wizard-inner">
                            <ul class="wizard-steps">
                            <?php if(!isset($_SESSION['userId'])){?>

                                <li><a><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
                                <li><a><span class="step-num">2</span>Info<span class="step-arrow">&raquo;</span></a></li>
                                <li class="active"><a><span class="step-num">3</span>Add-ons<span class="step-arrow">&raquo;</span></a></li>

                                <li><a><span class="step-num">4</span>Checkout</a></li>
                            <?php }else{ ?>
                              <li><a  href="<?=SITEURL;?>cart.php"><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
                                <li class="active"><a><span class="step-num">2</span>Add-ons<span class="step-arrow">&raquo;</span></a></li>

                                <li><a  href="<?=SITEURL;?>checkout.php"><span class="step-num">3</span>Checkout</a></li>
                            <?php } ?>
                            </ul>
                        </div>
                        <?php
						$newCart = $_SESSION['CART'];

						$totalAmount = $cartProductAmt = $cartAddonAmt = 0;
						$i=0;
						if(count($newCart)>0) {
							foreach($newCart as $cartProducts) {
								if($cartProducts['type'] == "main") {
									$cartProductAmt = $cartProductAmt + $cartProducts['totalPrice'];
								}
								if($i==0)
								{
									$deliveryTime=$cartProducts['deliveryTime'];
								}
								$i++;
							}
						}

						?>
                        <form method="post" name="addon_form" id="addon_form">
                            <div class="wizard-content bx-shadow pl-sm-5 pr-sm-5 pb-sm-5">

								<?php
								//facebookcover start

								if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
									if(count($newCart)>0) {

                                $getAddons = GetMltRcrdsOnCndiWthOdr(PRODUCT,"`id` = '".FACEBOOK_PRODUCT_ID."'","`id`", "ASC");

								foreach($newCart as $cartProductKey =>$cartProducts) {
										if($cartProducts['type'] != "main") { continue; }
										if(!empty($cartProducts['customeProductFields'])) { continue; }

                                        $currentProduct = GetSglRcrdOnCndi(PRODUCT, "id=".$cartProductKey);
										if($currentProduct['ProductType']==PRODUCT_TYPE_FACEBOOK_COVERS){continue;}
										/*
                                        $isFacebookAddon = 0;
                                        $product_addon_id = $currentProduct['product_addon_id'];
                                        $productAddonList = array();

                                        if(!empty($product_addon_id)) {
                                            $productAddonList = explode( ",", $product_addon_id );
                                            if(in_array(FACEBOOK_PRODUCT_ID,$productAddonList)){
                                                $isFacebookAddon = 1;
                                            }
                                        }

                                        if($isFacebookAddon != 1){ continue; }
										*/
                                        $addonProd =$getAddons[0];
										 $price3D = $addonPrices['1'];
											$priceMotion = $addonPrices['2'];
										   $priceown_music = $addonPrices['15'];
										//$priceFacebookcover=$addonPrices['16'];
                                        $priceFacebookcover=$getAddons[0]['Baseprice'];

										$getBanners = GetSglDataOnCndi(PRODUCT_BANNER,"`prod_id` = '".$cartProducts['id']."' AND `filetype` = 'image' order by id asc", "filename");
										$thisAvail = false;
										$thisDeliv = $thisAmt = 0;

										if(isset($newCart[$addonProd['id']]) && !empty($newCart[$addonProd['id']])) {

											$thisAvail = true;
											$thisDeliv = $newCart[$addonProd['id']]['deliveryTime'];
											$thisAmt = 	$newCart[$addonProd['id']]['totalPrice'];
										}
										if($thisDeliv==0)
										{
											if($deliveryTime!='')
											{
												$thisDeliv=$deliveryTime;
											}
											else
											{
												$thisDeliv=1;
											}

										}
										$cartAddonAmt += $thisAmt;

										?>
                                        <input type="hidden" value="<?=$addonProd['Category'];?>" name="cate_id_<?=$addonProd['id'];?>" id="cate_id_<?=$addonProd['id'];?>" data-name="cate_id_<?=$addonProd['id'];?>" />

                                        <input type="hidden" value="<?=$cartProducts['id'];?>" name="ProductBaseID<?=$addonProd['id']."_".$cartProductKey;?>" id="ProductBaseID<?=$addonProd['id']."_".$cartProductKey;?>" data-name="ProductBaseID<?=$addonProd['id']."_".$cartProductKey;?>" />

										<div class="cart-row pt-4 pb-4 block_addons<?php echo $addonProd['id']."_".$cartProductKey; ?>">
										<div class="product-img">
										</div>
										<div class="addon-info cart-pr-info" style="margin-bottom:10px;">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input addonProd addonId_<?=$addonProd['id']."_".$cartProductKey;?>" value="<?=$addonProd['id']."_".$cartProductKey;?>" data-amount="0.00" name="addonProId[<?=$addonProd['id']."_".$cartProductKey;?>]" <?php if(in_array('Facebook cover',$cartProducts['type_banner']) || in_array('motion',$cartProducts['type_banner'])) { echo "checked"; } ?> data-value="<?=$addonProd['id']."_".$cartProductKey;?>">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description"><?='Matching Facebook Cover'?>&emsp;<strong>(+$ <?php echo formatPrice($addonProd['Baseprice']);?>)</strong></span>
                                            </label>
                                            <?php if(in_array('motion',$cartProducts['type_banner'])) {?>
                                              <?php /*?><label class="custom-control custom-checkbox motionLabel_<?=$addonProd['id']."_".$cartProductKey;?>" value="<?=$addonProd['id']."_".$cartProductKey;?>">
                                              <input type="checkbox" class="custom-control-input" data-amount="0.00" checked disabled>
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description">
												<?php if(in_array('motion',$cartProducts['type_banner']) && in_array('Use my music',$cartProducts['type_banner'])) { ?>
												<?='Motion with music';?>
                                                <?php }else{echo "Motion";} ?>
                                                &emsp;</span>
                                            </label><?php */?>
                                            <?php  } ?>
									</div>
                                    <div class="product-img">


                                    <?php
											echo  productImageSrc($getBanners,$cartProducts['id'],'354');
											 ?>


                                    </div>

									<div class="addon-info cart-pr-info">
									<?php
									$getBanners = GetMltRcrdsOnCndi(PRODUCT_BANNER,"`prod_id` = '".$cartProducts['id']."' AND `filetype` = 'image'");
                                        $foundCover = 0;
										foreach($getBanners as $getBanners)
										{
                                            if ($getBanners['set_default_facebookimage']=="yes")
											{
                                                $foundCover = 1;

												?>

                                                    <?php
											echo  productImageSrc($getBanners['filename'],$cartProducts['id'],'355');
											 ?>


													<?php

											}
										}
                                        if($foundCover==0){
                                            echo '<img src="images/facebook_cover.jpg" width="375" alt="">';
                                        }
										?>

                                        <!--
                                         <div class="d-block mb-3">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input typeCheck type_static static_<?/*=$addonProd['id']."_".$cartProductKey;*/?>" value="static" data-amount="0" data-prodid="<?/*=$addonProd['id'];*/?>" name="flyerType_<?/*=$addonProd['id']."_".$cartProductKey;*/?>[]" data-value="static">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Static</span>
                                        </label>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input typeCheck type_motion motion_<?=$addonProd['id']."_".$cartProductKey;?>" value="motion" data-amount="<?=$priceMotion;?>" data-prodid="<?=$addonProd['id'];?>" name="flyerType_<?=$addonProd['id']."_".$cartProductKey;?>[]" data-value="motion" >
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Motion <strong>(+$<?=$priceMotion;?>)</strong></span>
                                        </label>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input typeCheck type_animated animated_<?=$addonProd['id']."_".$cartProductKey;?>" value="animated" data-amount="<?=$priceAnimation;?>" data-prodid="<?=$addonProd['id'];?>" name="flyerType_<?=$addonProd['id']."_".$cartProductKey;?>[]" data-value="animated">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Animated <strong>(+$<?=$priceAnimation;?>)</strong></span>
                                        </label>

                                    </div>-->

                                        <div class="d-flex1 flex-sm-wrap1 align-items-center1 turn-around-opt2" style="display:none !important;">
                                                <label>Turn around time</label>





                                                <select name="addonProDtime[<?=$addonProd['id'];?>]" class="deliveryTimes addonDtime_<?=$addonProd['id'];?> form-control" data-prodid="<?=$addonProd['id'];?>">
                                                    <option value="">Select the turn around time</option>
                                                    <option value="3" <?php if($thisDeliv=='3') { echo "selected"; } ?>>2-3 business days(<?php echo ($turnAround3>0) ? "+$". formatPrice($turnAround3):"FREE"; ?>)</option>

                                                   <option value="2" <?php if($thisDeliv=='2') { echo "selected"; } ?>>24 hours (<?php echo ($turnAround2>0) ? "$". formatPrice($turnAround2):"FREE"; ?>)</option>
                                                    <option value="1" <?php if($thisDeliv=='1') { echo "selected"; } ?>>12 Hours Same-Day (<?php echo ($turnAround1>0) ? "$". formatPrice($turnAround1):"FREE"; ?>)</option>
                                                </select>
                                                <input type="hidden" class="deliveryTimesAmt" value="<?php if($thisDeliv =='1') { echo $turnAround1; } elseif($thisDeliv =='2') { echo $turnAround2; } elseif($thisDeliv =='3') { echo $turnAround3; } elseif($thisDeliv =='4') { echo $turnAround3; } else {echo $turnAround4; } ?>" />
                                            </div>
                                        </div>
                                    </div>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    }
								}
                                //facebookcover end



					$getAddons = GetMltRcrdsOnCndiWthOdr(PRODUCT,"`Addon` = '1' AND `Status` = '1'","`id`", "ASC");

                                        if ( count( $getAddons ) > 0 ) {

									//cwiser Cart Update
											$cartProductId = 0;

                                        $prodOtherSizes = getProdSizeArr();

									   foreach ( $getAddons as $addonProd ) {
										   if($addonProd['id'] == FACEBOOK_PRODUCT_ID){ continue;}

                                        $getBanners = GetSglRcrdOnCndiWthOdr( PRODUCT_BANNER, "`prod_id` = '" . $addonProd['id'] . "' AND `filetype` = 'image'", "id", "ASC" );
                                        $thisAvail = false;
                                        $thisDeliv = $thisAmt = 0;

                                        if ( isset( $newCart[$addonProd['id']] ) && ! empty( $newCart[$addonProd['id']] ) ) {

                                            $thisAvail = true;
                                            $thisDeliv = $newCart[$addonProd['id']]['deliveryTime'];
                                            $thisAmt = $newCart[$addonProd['id']]['totalPrice'];
                                        }
                                        if ( $thisDeliv == 0 ) {
                                            if ( $deliveryTime != '' ) {
                                                $thisDeliv = $deliveryTime;
                                            } else {
                                                $thisDeliv = 1;
                                            }

                                        }
                                        $cartAddonAmt += $thisAmt;
                                        ?>

                                        <div class="cart-row pt-4 pb-4 <?php echo ($addonProd['id']!=11)?"AddonsDataBlock":""; ?> block_addons<?php echo $addonProd['id']."_".$cartProductId; ?>">
                                            <div class="product-img">
                                                <?php
                                                if (count($getBanners)>0 ) {
                                                    ?>
                                                    <?php echo SITEURL; ?>uploads/products/<?=$addonProd['id'];?>/<?=$getBanners['filename'];?>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <img src="<?= SITEURL . "images/not_found.jpg";?>">

                                                    <?php
                                                }
                                                ?>
                                                <input type="hidden" value="<?= $addonProd['Category']; ?>"
                                                       name="cate_id_<?= $addonProd['id']; ?>"
                                                       id="cate_id_<?= $addonProd['id']; ?>"
                                                       data-name="cate_id_<?= $addonProd['id']; ?>"/>
                                            </div>
                                            <div class="addon-info cart-pr-info">

                                                <label class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                           class="custom-control-input addonProd addonId_<?= $addonProd['id']."_".$cartProductId; ?>"
                                                           value="<?= $addonProd['id']."_".$cartProductId; ?>"
                                                           data-amount="<?= $addonProd['Baseprice']; ?>"
                                                           name="addonProId[<?= $addonProd['id']."_".$cartProductId; ?>]"
                                                            <?php
                                                            $currentCartThisProAdd = array();
                                                            if(!empty($newCart[$addonProd['id']."_".$cartProductId])) {
                                                                $currentCartThisProAdd = $newCart[$addonProd['id'] . "_" . $cartProductId];
                                                                echo "checked";
                                                            }

                                                            ?>
                                                           data-value="<?= $addonProd['id']."_".$cartProductId; ?>" >
                                                    <span class="custom-control-indicator"></span>
                                                    <span class="custom-control-description">
													<?php if($addonProd['Defaultsizes']!=""){?>

                                                    <?= $addonProd['Title'] . '&emsp;(' . $prodOtherSizes[$addonProd['Defaultsizes']]['name'] . ')'; ?>&emsp;<strong>(+$<?php echo formatPrice($addonProd['Baseprice']); ?>)</strong>
                                                    <?php
                                                    }else{
													?>

                                                    <?= $addonProd['Title'];  ?>&emsp;<strong>(+$<?php echo formatPrice($addonProd['Baseprice']); ?>)</strong>
                                                    <?php
													} ?>
													</span>
                                                </label>

                                                <?php


                                                    $price3D = $addonPrices['1'];
    $priceMotion = $addonPrices['2'];
   $priceown_music = $addonPrices['15'];?>

                                                    <div class="d-block mb-3">
                                                        <label class="custom-control custom-radio">
                                                            <input type="radio"
                                                                   name="flyer_dimension_<?= $addonProd['id']."_".$cartProductId; ?>"
                                                                   data-prodid="<?= $addonProd['id']."_".$cartProductId; ?>"
                                                                   class="custom-control-input dimensionalCheck"
                                                                   value="2D"
                                                                    <?php

                                                                    if(!empty($currentCartThisProAdd['dimensional'])) {
                                                                        if ( isset( $currentCartThisProAdd['dimensional'] ) && $currentCartThisProAdd['dimensional'] == '2D' ) {
                                                                            echo "checked";
                                                                        }
                                                                    }else{
                                                                        echo "checked";
                                                                    }
                                                                    ?>
                                                            >
                                                            <span class="custom-control-indicator"></span>
                                                            <span class="custom-control-description">2D</span>
                                                        </label>
                                                        <label class="custom-control custom-radio">
                                                            <input type="radio"
                                                                   name="flyer_dimension_<?= $addonProd['id']."_".$cartProductId; ?>"
                                                                   data-prodid="<?= $addonProd['id']."_".$cartProductId; ?>"
                                                                   class="custom-control-input dimensionalCheck"
                                                                   value="3D"
                                                                   <?php
                                                                   if(!empty($currentCartThisProAdd['dimensional']) && $currentCartThisProAdd['dimensional'] == '3D'){echo "checked";}
                                                                   ?>
                                                            >
                                                            <span class="custom-control-indicator"></span>
                                                            <span class="custom-control-description 3d-desc">3D <strong> (+$<?php echo formatPrice( $price3D ); ?>)</strong></span>
                                                        </label>
                                                    </div>

                                                    <?php
                                                    if ( $addonProd['Motion'] == '1' ) {
                                                        ?>
                                                        <?php /*?><div class="d-block mb-3">
                                                            <label class="custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                       class="custom-control-input typeCheck type_static"
                                                                       value="static" data-amount="0"
                                                                       data-prodid="<?= $addonProd['id']."_".$cartProductId; ?>"
                                                                       name="flyerType_<?= $addonProd['id']."_".$cartProductId; ?>[]"
                                                                       data-value="static" checked>
                                                                <span class="custom-control-indicator"></span>
                                                                <span class="custom-control-description">Static</span>
                                                            </label>
                                                            <label class="custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                       class="custom-control-input typeCheck type_motion"
                                                                       value="motion"
                                                                       data-amount="<?= $priceMotion; ?>"
                                                                       data-prodid="<?= $addonProd['id']."_".$cartProductId; ?>"
                                                                       name="flyerType_<?= $addonProd['id']."_".$cartProductId; ?>[]"
                                                                       data-value="motion">
                                                                <span class="custom-control-indicator"></span>
                                                                <span class="custom-control-description">Motion <strong>(+$<?= $priceMotion; ?>)</strong></span>
                                                            </label>

                                                        </div><?php */?>
                                                    <?php } ?>
                                                    <div class="row no-gutters d-flex justify-content-around flex-wrap">
                                                    </div>

                                                <div class="d-flex1 flex-sm-wrap1 align-items-center1 turn-around-opt2"
                                                     style="display:none !important;">
                                                    <label>Turn around time</label>
                                                    <select name="addonProDtime[<?= $addonProd['id']; ?>]"
                                                            class="deliveryTimes addonDtime_<?= $addonProd['id']; ?> form-control"
                                                            data-prodid="<?= $addonProd['id']; ?>">
                                                        <option value="">Select the turn around time</option>


                                                        <option value="3" <?php if($thisDeliv=='3') { echo "selected"; } ?>>2-3 business days(<?php echo ($turnAround3>0) ? "+$". formatPrice($turnAround3):"FREE"; ?>)</option>

                                                   <option value="2" <?php if($thisDeliv=='2') { echo "selected"; } ?>>24 hours (<?php echo ($turnAround2>0) ? "$". formatPrice($turnAround2):"FREE"; ?>)</option>
                                                    <option value="1" <?php if($thisDeliv=='1') { echo "selected"; } ?>>12 Hours Same-Day (<?php echo ($turnAround1>0) ? "$". formatPrice($turnAround1):"FREE"; ?>)</option>



                                                    </select>
                                                    <input type="hidden" class="deliveryTimesAmt"
                                                           value="<?php if ( $thisDeliv == '1' ) {
                                                               echo formatPrice($turnAround1);
                                                           } elseif ( $thisDeliv == '2' ) {
                                                               echo formatPrice($turnAround2);
                                                           } elseif ( $thisDeliv == '3' ) {
                                                               echo formatPrice($turnAround3);
                                                           } elseif ( $thisDeliv == '4' ) {
                                                               echo formatPrice($turnAround3);
                                                           } else {
                                                               echo formatPrice($turnAround4);
                                                           } ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php }
                                                        }


                                    $totalAmount = $cartProductAmt + $cartAddonAmt;


                                /* Product Addon end */
                                ?>
                                <div class="total-price text-right">
                                <input type="hidden" id="totalAmount" value="<?=formatPrice($totalAmount);?>" />
                                    Total <strong id="totalCartAmount">$<?php echo formatPrice($totalAmount);?></strong>
                                </div>
                                <div class="wizard-foot clearfix mt-4">
                                    <a  href="<?=SITEURL;?>cart.php" href="cart.php" class="btn-grey btn-lg float-md-left">Back</a>
                                    <a class="btn-lg btn-grad float-md-right addon_update" id="totalCartAmountBtn">$<?=formatPrice($totalAmount);?> - Proceed</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'files/footerSection.php' ?>
    <!----SCRIPTS---------->
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
		var addonArr = Array();
		$(".dimensionalCheck").on("change", function(){
			var addonid = $(this).data('prodid');
			updateAddonArray(addonid);
		});
		$(".typeCheck").on("change", function(){
			var addonid = $(this).data('prodid');
            updateAddonArray(addonid);
		});
		$(".deliveryTimes").on("change",function() {
			var addonid = $(this).data('prodid');
            updateAddonArray(addonid);
		});

		$(".addonProd").on("change", function(){
			var addonid = $(this).data("value");

			jQuery(".block_addons"+ addonid +" .typeCheck").attr("disabled", true);
			jQuery(".block_addons"+ addonid +" .dimensionalCheck").attr("disabled", true);
			jQuery(".block_addons"+addonid+" .typeCheck").prop('checked',false);
			if(addonid=="11" && jQuery(this).prop('checked')==true){
				jQuery(".block_addons"+ addonid +" .typeCheck:eq(0)").prop('checked',true);


			}
			if(jQuery(this).prop('checked')==true){
				jQuery(".block_addons"+addonid+" .typeCheck").removeAttr("disabled");
				jQuery(".block_addons"+ addonid +" .dimensionalCheck").removeAttr("disabled");
				jQuery(".motionLabel_"+addonid).show();
			}else{

				jQuery(".motionLabel_"+addonid).hide();
			}


			updateAddonArray(addonid);

		});
		function updateAddonArray(addonID){

			var targetForm = $("#addon_form");
			var formDetail = new FormData(targetForm[0]);
			formDetail.append('action' , "add");
			$.ajax({
				method : 'post',
				url : 'ajax/addonOperation.php',
				data:formDetail,
				cache:false,
				contentType: false,
				processData: false
			}).done(function(regResponse){
				/*console.log(regResponse);*/
				$("#totalCartAmount").html("$"+$.trim(regResponse));
				$("#totalAmount").html($.trim(regResponse));
				$("#totalCartAmountBtn").html("$"+$.trim(regResponse)+" - Proceed");
			});
		}
		$(".addon_update").on("click", function(event) {
			event.preventDefault();
			var _this = $(this);
			var targetForm = _this.closest('form');
			var checkTaT = true;
			$.each($("input.addonProd:checked"), function(){
               	var addonid = $(this).data("value");

				var checkDelivery = $('.addonDtime_'+addonid).find(":selected").val();
				if(checkDelivery == "") {
					$(".warning span").html("Please select the turn around time.");
					$(".warning").attr('style','display:flex;');
					$('html, body').animate({
						scrollTop: $(".warning").offset().top
					}, 1000);
					setTimeout(function() {$(".warning").hide(500)}, 12000);
					$('.addonDtime_'+addonid).focus();
					checkTaT = false;
					return false;
				}
            });

			if(checkTaT) {
				var formDetail = new FormData(targetForm[0]);
				formDetail.append('action' , "update");
				$.ajax({
					method : 'post',
					url : 'ajax/cart-update.php',
					data:formDetail,
					cache:false,
					contentType: false,
					processData: false
				}).done(function(regResponse){
					/*console.log(regResponse);*/
					regResponse = JSON.parse(regResponse);
					$(".cart-product").html(regResponse.cart_count);
					if(regResponse.status == "success") {

						window.location.href = "checkout.php";
					}
				});
			}
		});

        jQuery(document).ready(function(e) {
            jQuery(".addonProd").trigger("change");

            <?php
            foreach ($newCart as $cartKey => $cartValue){
                if($cartValue['type'] == 'main'){continue;}

                $cartAddonProductIdArray = explode('_',$cartKey);
                $cartAddonId = $cartAddonProductIdArray[0];
                $cartAddonProductId = $cartAddonProductIdArray[1];

                if($cartAddonId == 11){

                    if(!empty($cartValue['type_banner'])){

                            if(in_array( 'static', $cartValue['type_banner'] )) { ?>
                                e = jQuery("input.static_<?=$cartKey;?>");
                                e.attr("disabled", false).prop('checked', true);

                            <?php } if(in_array( 'motion', $cartValue['type_banner'] )) { ?>
                                e = jQuery("input.motion_<?=$cartKey;?>");
                                e.attr("disabled", false).prop('checked', true);

                            <?php } if(in_array( 'animated', $cartValue['type_banner'] )) { ?>
                                e = jQuery("input.animated_<?=$cartKey;?>");
                                e.attr("disabled", false).prop('checked', true);
                            <?php
                            }
                            ?>e.change();<?php
                     }
                }
            }
            ?>
            /*jQuery(".typeCheck").change();*/
        });
	</script>
</body>

</html>
