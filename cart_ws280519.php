<?php

ob_start();

require_once 'function/constants.php';

require_once 'function/configClass.php';

require_once 'function/siteFunctions.php';

//unset($_SESSION['CART']);

if($_REQUEST['RepKeyModify']!=""){
	if($_SESSION['userType'] == "sale_rep" || $_SESSION['userType'] == "admin"){

		$getSaleOrderByKey = getSaleOrderByKey($_REQUEST['RepKeyModify'],$_SESSION['userId'],'sales_rep');
		if(!empty($getSaleOrderByKey)){
			$_SESSION['paymentRecord'] = $getSaleOrderByKey['id'];
			$_SESSION['CART'] = unserialize($getSaleOrderByKey['RepCartData']);

			$_SESSION['RepKeyModify']= 	$_REQUEST['RepKeyModify'];
			if(!empty($getSaleOrderByKey['RepDiscountData'])){
				$_SESSION['DISCOUNT_DATA'] = unserialize($getSaleOrderByKey['RepDiscountData']);
			}

		}
		if(empty($getSaleOrderByKey)){
			$_SESSION['linkExipre'] = "Sorry your link has been expired.";
			 header("location:".ADMINURL."orders.php")	;
   			 exit();
		}
		//$_SESSION['SaleRepOrderKey'] = $_REQUEST['key'];

	}
}
?>

<!DOCTYPE html>

<html lang="en">

<head>

    <title>Cart | Flashy Flyers</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require_once 'files/headSection.php'; ?>

</head>

<body>

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

                <?php
					if(isset($_SESSION['linkExipre'])){
						?>
                        <div class="notification error" style="display:flex;">
                    <div class="d-flex"><i class="fas fa-times-circle"></i></div>
                    <span><?php echo $_SESSION['linkExipre']; ?></span><button class="close-ntf"><i class="fas fa-times"></i></button>
                </div>
                    <?php
						unset($_SESSION['linkExipre']);
					  }
				?>

                <div class="wizard">

                    <div class="wizard-inner">

                        <ul class="wizard-steps">
                            <?php
							 $isNormalProductWizard =0;
							if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
                            $cart = $_SESSION['CART'];
						    foreach($cart as $key => $product ) {
								if(isset($product['customeProductFields']) && !empty($product['customeProductFields'])){
									$isNormalProductWizard = 1;

								}else if($product['type']=="main"){
									$isNormalProductWizard =0;
									break;
								}

							}
							}
							if($isNormalProductWizard==0){

							 ?>
                             <?php if(!isset($_SESSION['userId'])){?>
                             <li class="active"><a><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
                           <?php /*?> <li><a><span class="step-num">2</span>Login<span class="step-arrow">&raquo;</span></a></li><?php */?>
                            <li><a><span class="step-num">2</span>Add-ons<span class="step-arrow">&raquo;</span></a></li>

                            <li><a><span class="step-num">3</span>Checkout</a></li>


                             <?php }else{ ?>
                            <li class="active"><a><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
                            <li><a><span class="step-num">2</span>Add-ons<span class="step-arrow">&raquo;</span></a></li>

                            <li><a><span class="step-num">3</span>Checkout</a></li>
                            <?php } ?>
							<?php }else{ ?>
                             <li class="active"><a><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
                           <?php if(!isset($_SESSION['userId'])){?>
                        <?php /*?>   <li><a><span class="step-num">2</span>Login</a></li><?php */?>
                           <li><a><span class="step-num">2</span>Checkout</a></li>
						   <?php }else{ ?>
                            <li><a><span class="step-num">2</span>Checkout</a></li>
                            <?php } ?>
							<?php } ?>
                        </ul>

                    </div>

                    <div class="wizard-content bx-shadow pl-sm-5 pr-sm-5 pb-sm-5 showProductList">

                        <?php

                        $prodOtherSizes = getProdSizeArr();
                        $totalAmount = 0;
                      	 $checkCustomProduct =0;
						 $isNormalProduct =0;
						if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
                            $cart = $_SESSION['CART'];
                            foreach($cart as $key => $product ) {

                                if($product['type'] != "main") { continue; }

                                $productAndAddonId = $key;
                                $key = explode("_",$key);
                                $key = $key[0];
                                $baseProductId = !empty($key[1]) ? $key[1] : $key[0];
                                $getProduct = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$key."'");
                                $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$key."' AND `filetype` = 'image'","id","ASC");

                                ?>
                                <div class="cart-row pt-4 pb-4 base-prod-<?=$baseProductId;?>" id="cart_id_<?=$productAndAddonId;?>">
                                    <div class="product-img">
                                    <?php
                                        echo productImageSrc($getBanners['filename'],$getProduct['id'],'354');
                                    ?>
                                    </div>
                                    <div class="cart-pr-info">
                                        <h3><?=$getProduct['Title'];?></h3>
                                        <ul>
                                            <?php

											 if($product['template_type']=="psd" && $product['psd3dtitle']=="Yes"){
												echo "<li>3D Title</li>";
											 }

										    if($product['template_type']!="psd"){
											if(isset($product['dimensional']) )
											if(!isset($product['customeProductFields']) && empty($product['customeProductFields'])){
											echo '<li>'.$product['dimensional'].'</li>';
											}
											if(isset($product['type_banner']))
                                            {
                                                $ord = array_map('ucfirst', $product['type_banner']);
                                                echo '<li>'.implode(", ",$ord).'</li>';
                                            }
											}
                                            if(isset($product['defaultSize']) && $product['type'] == "main")
                                                echo '<li>Default Size: '.$prodOtherSizes[$product['defaultSize']]['name'].'</li>';
                                            if(!empty($product['otherSize']) && $product['type'] == "main") {
                                                $otherSized = $product['otherSize'];
                                                $sn =1;
                                                foreach($otherSized as $extraSize)
                                                    echo '<li>Other Sizes: '.$prodOtherSizes[$extraSize]['name'].'</li>';
                                            }
                                            if(isset($product['deliveryTime']) && $product['deliveryTime']>0 && $product['type'] == "main"){
                                                echo '<li>Delivery: '.$deliArr[$product['deliveryTime']].'</li>';
											}
											if(isset($product['customeProductFields']) && !empty($product['customeProductFields'])){
												$checkCustomProduct =1;
												foreach($product['customeProductFields'] as $customeProductFieldsKey=>$customeProductFieldsValue){

													foreach($customeProductFieldsValue as $filedsPrimaryIndex=>$filedsPrimaryIndexValue){

														foreach($filedsPrimaryIndexValue as $filedsIndex=>$filedsIndexValue){

														$FiledsLabal = $customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex];
														$Filedsvalue = $filedsIndexValue;
														if($filedsIndex=="turnaround_time"){
															$Filedsvalue = $deliArr[$Filedsvalue];
														}
														if($filedsIndex=="3d_or_2d"){
															echo "<li>".$Filedsvalue."</li>";
														}else if(($filedsIndex=="checkbox_sided" || $filedsIndex=="add_music" ||    $filedsIndex=="add_video") && $Filedsvalue=="on"){
															 		echo '<li>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].': Yes</li>';

														}else{
														if($Filedsvalue!="" && $filedsIndex!="music_file" && $filedsIndex!="defaultSize" && $filedsIndex!="add_facebook_cover" && $filedsIndex!="otherSize" && $filedsIndex!="attach_any_logos" && $filedsIndex!="attach_your_logo_design" && $filedsIndex!="vector_psd_pdf" && $filedsIndex!="attach_any_pictures"  && $filedsIndex!="attach_any_style_reference"  && $filedsIndex!="files" && $filedsIndex!="attach_logo"){
													 		echo '<li>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].': '.$Filedsvalue.'</li>';
														}
														}
													}

													}
												}

											}else{
												$isNormalProduct =1;}

                                            ?>

                                        </ul>
                                        <?php
                                        $currentCartAddon = array_filter($cart, function ($var) use ($key) {
                                            return ($var['ProductBaseID'] == $key && $var['type'] == 'addon');
                                        });

                                        if(!empty($currentCartAddon) && count($currentCartAddon) > 0){
                                            ?>
                                            <div>
                                                <h3>Addons</h3>
                                                <ul>

                                                    <?php

                                                    foreach ($currentCartAddon as $addonKey => $addonValue){
                                                        if($addonValue['ProductBaseID'] ==  $key && $addonValue['type'] == 'addon' ){
                                                            $addonKey = explode("_",$addonKey);
                                                            $addonKey = $addonKey[0];

                                                            $getAddonData = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$addonKey."'");
                                                            echo "<li>".$getAddonData['Title'];
                                                            /*if(!empty($addonValue['type_banner'])){
                                                                echo " - ".implode($addonValue['type_banner'],', ');
                                                            }*/
                                                            if(!empty($addonValue['dimensional'])){
                                                                echo " - ".$addonValue['dimensional'];
                                                            }
                                                            if(!empty($addonValue['totalPrice']) && $addonValue['totalPrice'] > 0 && $addonKey != FACEBOOK_PRODUCT_ID) {
                                                                echo " ( $" . formatPrice($addonValue['totalPrice']) . " )";
                                                            }
                                                            if($addonKey == FACEBOOK_PRODUCT_ID){
                                                                $getFacebookAddonsData = GetSglRcrdOnCndiWthOdr(PRODUCT,"`id` = '".FACEBOOK_PRODUCT_ID."'","`id`", "ASC");
                                                                $priceFacebookcover = $getFacebookAddonsData['Baseprice'];

                                                                if(!empty($addonValue['totalPrice']) && $addonValue['totalPrice'] > 0){
                                                                    $priceFacebookcover = $addonValue['totalPrice'];
                                                                }
                                                                echo " ( $" . formatPrice($priceFacebookcover) . " )";
                                                            }
                                                            echo "</li>";
                                                            $product['totalPrice'] = $product['totalPrice'] + $addonValue['totalPrice'];
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                        <a onClick="action_remove('<?=$productAndAddonId;?>');" class="btn-rmv">Remove</a>
                                        <?php if($product['type'] == "main"){ ?>
                                            <a href="<?php echo SITEURL.'p/'.$getProduct['slug']; ?>"  href="<?php echo SITEURL.'p/'.$getProduct['slug']; ?>" class="btn-edit-cart">Edit</a>
                                        <?php } ?>
                                    </div>
                                    <div class="cart-price text-right">
                                        $<?php

										echo formatPrice($product['totalPrice']); $totalAmount += $product['totalPrice'];?>
                                    </div>
                                </div>
                                <?php
                            }
                        } else if(isset($_SESSION['CartRequest']) && !empty($_SESSION['CartRequest'])) {
                            $cart1 = $_SESSION['CartRequest'];
                            foreach($cart1 as $key2=>$cart){
                                foreach($cart as $key => $product ) {
                                    $getProduct = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$key."'");
                                    $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$key."' AND `filetype` = 'image'","id","ASC");
                                    ?>
                                    <div class="cart-row pt-4 pb-4" id="cart_id_<?= $key2."_".$getProduct['id'];?>">
                                        <div class="product-img">
                                        <?php
											 echo productImageSrc($getBanners['filename'],$getProduct['id'],'354');
											 ?>


                                        </div>
                                        <div class="cart-pr-info">
                                            <h3>Change request for  <?=$getProduct['Title'];?> <?php echo ($product['TypeBanner']!="") ? " - ".ucfirst($product['TypeBanner']):""; echo ($product['SizeLabel']!="") ? " - ".ucfirst($product['SizeLabel']):""; ?>  for order #<?php echo $key2; ?></h3>

                                            <a onClick="action_remove('<?= $key2."_".$getProduct['id'];?>');" class="btn-rmv">Remove</a>
                                        </div>
                                        <div class="cart-price text-right">
                                            $<?php echo formatPrice($product['REQUEST_CHANGE_PRICE']); $totalAmount += $product['REQUEST_CHANGE_PRICE'];?>
                                        </div>
                                    </div>
                                    <?php
                                }}
                        }  else {
                            ?>
                            <div class="cart-row pt-4 pb-4">
                                <div class="product-img"></div>
                                <div class="cart-pr-info">
                                    <h3>Your shopping cart is currently  empty.</h3>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="total-price text-right">

                            Total <strong class="totalAmt">$<?=formatPrice($totalAmount);?></strong>

                        </div>

                        <div class="wizard-foot clearfix mt-4">

                            <a  href="<?=SITEURL;?>#flyer-templates" class="btn-grey btn-lg float-md-left">CONTINUE SHOPPING</a>
                            <?php if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) { ?>
                                <a   href="<?php if(!isset($_SESSION['userId'])){?>info.php<?php }else{ ?>addons.php<?php } ?>" class="btn-lg btn-grad float-md-right proceedBtn checkoutbtn">$<?=formatPrice($totalAmount);?> - Proceed</a>
                            <?php } ?>

                            <?php if(isset($_SESSION['CartRequest']) && !empty($_SESSION['CartRequest'])) { ?>
                                <a    href="checkout.php" class="btn-lg btn-grad float-md-right proceedBtn">$<?=formatPrice($totalAmount);?> - Proceed</a>
                            <?php } ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php require_once 'files/footerSection.php' ?>

<!----SCRIPTS---------->

<script src="js/popper.min.js"></script>

<script src="js/bootstrap.min.js"></script>

<script src="js/script.js"></script>
<script>
    function action_remove(productid) {
        $.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/cart-process.php",
            data: "action=remove&productID="+productid,
            success: function(regResponse) {
				window.location.reload();
                regResponse = JSON.parse(regResponse);
                $("#cart_id_"+productid).remove();
                $(".base-prod-"+productid).remove();
                if(parseInt(regResponse.cart_count) == 0) {
                    $(".showProductList").html('<div class="cart-row pt-4 pb-4"><div class="product-img"></div><div class="cart-pr-info"><h3>Your shopping cart is currently empty.</h3></div></div><div class="total-price text-right">Total <strong>$0.00</strong></div><div class="wizard-foot clearfix mt-4"><a   href="<?=SITEURL;?>" class="btn-grey btn-lg float-md-left">Continue Shopping</a></div></div>');
                    $(".totalAmt").attr('disabled', true);
                    $(".cart-num").remove();
                } else {
                    $(".cart-product").html(regResponse.cart_count);
                    $(".totalAmt").html('$'+regResponse.amount_count);
                    $(".proceedBtn").html('$'+regResponse.amount_count+' - Proceed');
					if(regResponse.IsNormalProduct==1){
						jQuery(".checkoutbtn").attr("href","addons.php");
					}else{
						jQuery(".checkoutbtn").attr("href","checkout.php");
					}
                }
            }
        });
    }
</script>
</body>



</html>
