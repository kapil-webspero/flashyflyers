<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';
checkDiscountCode();
if($_REQUEST['key']!=""){

			$getCustomer = GetSglRcrdWthSmFldsOnCndi(TRANSACTION, "RepKey='".$_REQUEST['key']."'", '*');

			if(!empty($getCustomer)){
				$_SESSION['loginType']="user";
				$_SESSION['userId'] = $getCustomer['UserID'];
				$_SESSION['userType'] = "user";
			}



	if($_SESSION['userType'] == "user"){

		$getSaleOrderByKey = getSaleOrderByKey($_REQUEST['key'],$_SESSION['userId']);

		if(!empty($getSaleOrderByKey)){
			$_SESSION['paymentRecord'] = $getSaleOrderByKey['id'];

			$_SESSION['CART'] = unserialize($getSaleOrderByKey['RepCartData']);

			$_SESSION['repOrderCart']= 	$_REQUEST['key'];
			if(!empty($getSaleOrderByKey['RepDiscountData'])){
				$_SESSION['DISCOUNT_DATA'] = unserialize($getSaleOrderByKey['RepDiscountData']);
			}

		}
		if(empty($getSaleOrderByKey)){
			$_SESSION['linkExipre'] = "Sorry your link has been expired.";
			 header("location:cart.php")	;
   			 exit();
		}
		//$_SESSION['SaleRepOrderKey'] = $_REQUEST['key'];

	}
}

if(is_cart_empty()) {
	if(isset($_REQUEST['key'])){
			$_SESSION['repOrderCartRedirect']= 	$_REQUEST['key'];
		}
	if( empty($_SESSION['userId'])){

		header("location:".SITEURL."login.php")	;
		exit;
	}else{
    	header("location:".SITEURL."cart.php")	;
    	exit();
	}
}

$IsNormalProduct =0;


foreach($_SESSION['CART'] as $singleCartKey=>$singleCart){


if(isset($singleCart['customeProductFields']) && !empty($singleCart['customeProductFields'])){

	}else{
			$IsNormalProduct =1;
	}
}
	if( empty($_SESSION['userId'])){
		if(isset($_REQUEST['key'])){
			$_SESSION['repOrderCartRedirect']= 	$_REQUEST['key'];
		}
		header("location:info.php")	;
		exit();
	}

if($_SESSION['userType'] == "") {
	header("location:".SITEURL."login.php")	;
   		 exit();
}

	if($_SESSION['userType']!="sale_rep"){
    //$_SESSION['ERROR'] = "It&rsquo;s seems like youa'e not logged in with user account. please login again.";
	if(isset($_REQUEST['key'])){
		$_SESSION['repOrderCartRedirect']= 	$_REQUEST['key'];
	}

}
if(isset($_SESSION['CartRequest']) && count($_SESSION['CartRequest'])>0){

	foreach($_SESSION['CartRequest'] as $cartProducts1) {
			$cartProduct = count($cartProducts1);

			foreach($cartProducts1 as $cartProductsKey=>$cartProductsValue) {
				$cartAmount = $cartAmount +$cartProductsValue['REQUEST_CHANGE_PRICE'];
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
/*if(isset($_SESSION['DISCOUNT_DATA'])) {
    $cartAmount=$cartAmount-$_SESSION['DISCOUNT_DATA']['discountapplied'];
}*/
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Checkout | Flashy Flyers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once 'files/headSection.php'; ?>
		<link rel="stylesheet"   href="<?=SITEURL;?>css/style-3.css" async type="text/css"  defer="" as="style"  media="all" crossorigin="anonymous">
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
</head>

<body class="Checkoupage">
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
                <?php if(isset($_SESSION['ERROR']) && !empty($_SESSION['ERROR'])) { ?>
                    <div class="notification error"  style="display:flex">
                        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
                        <span>Error: <?=$_SESSION['ERROR'];?></span><button class="close-ntf"><i class="fas fa-times"></i></button>
                    </div>
                    <?php unset($_SESSION['ERROR']); } ?>
                <div class="row justify-content-center">
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
									$isNormalProductWizard = 0;
									break;
								}

							}
							}
							if($isNormalProductWizard==0){
							 ?>
                              <?php if(!isset($_SESSION['userId'])){?>
                                <li><a><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
                                <li><a><span class="step-num">2</span>Info<span class="step-arrow">&raquo;</span></a></li>
                                <li><a><span class="step-num">3</span>Add-ons<span class="step-arrow">&raquo;</span></a></li>

                                <li class="active"><a><span class="step-num">4</span>Checkout</a></li>
                                <?php }else{ ?>
                                    <!--ws bredcrumb 31-may-2021-->
                                    <li><a href="<?=SITEURL;?>cart.php" ><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
                                    <li><a href="<?=SITEURL;?>addons.php"><span class="step-num">2</span>Add-ons<span class="step-arrow">&raquo;</span></a></li>

                                <li class="active"><a><span class="step-num">3</span>Checkout</a></li>

                                <?php } ?>
								<?php }else{ ?>

                                <li><a><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
                                <li class="active"><a><span class="step-num">2</span>Checkout</a></li>

                                <?php } ?>
                            </ul>
                        </div>
                        <div class="wizard-content bx-shadow pl-sm-5 pr-sm-5 pb-sm-5 showProductList">

                            <?php

                            $prodOtherSizes = getProdSizeArr();
                            $totalAmount = 0;
                            if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
                                $cart = $_SESSION['CART'];
                                foreach($cart as $key => $product ) {
                                    if($product['type'] == "main") {
                                    $getProduct = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$key."'");
                                    $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$key."' AND `filetype` = 'image'","id","ASC");
                                    ?>
                                    <div class="cart-row pt-4 pb-4" id="cart_id_<?=$getProduct['id'];?>">
                                        <div class="product-img">

                                         <?php  echo  productImageSrc($getBanners['filename'],$getProduct['id'],'354'); ?>
                                        </div>
                                        <div class="cart-pr-info">
                                            <h3><?=$getProduct['Title'];?></h3>
                                            <?php
                                            $extraDetails ="";

											if($product['template_type']=="psd" && $product['psd3dtitle']=="Yes"){
												  $extraDetails .= '3D Title, ';
											 }
											 if($product['template_type']!="psd"){
											if(isset($product['dimensional']))
											if(!isset($product['customeProductFields']) && empty($product['customeProductFields'])){
                                                $extraDetails .= $product['dimensional'].', ';
											}

                                            if(isset($product['type_banner']))
                                                $extraDetails .= implode(",",$product['type_banner']).', ';
											 }
										   if(!isset($product['customeProductFields']) && empty($product['customeProductFields'])){
										    if(isset($product['defaultSize'])  && $product['type'] == "main")
                                                $extraDetails .= $prodOtherSizes[$product['defaultSize']]['name'].', ';
                                            if(!empty($product['otherSize'])  && $product['type'] == "main") {
                                                $otherSized = $product['otherSize'];
                                                $sn =1;
                                                foreach($otherSized as $extraSize)
                                                    $extraDetails .= $sn++.'.: '.$prodOtherSizes[$extraSize]['name'].', ';
                                            }

                                            if(isset($product['deliveryTime'])  && $product['type'] == "main"){
                                                $extraDetails .= $deliArr[$product['deliveryTime']].', ';
                                            }
                                            echo rtrim($extraDetails,", ");
										   }
											$customeProduct = "";


											if(isset($product['customeProductFields']) && !empty($product['customeProductFields'])){
												echo "<ul>";
												$checkCustomProduct =1;

												if(isset($product['defaultSize'])  && $product['type'] == "main")
                                               		echo "<li>Default Size: ".$prodOtherSizes[$product['defaultSize']]['name'].'</li>';
                                            if(!empty($product['otherSize'])  && $product['type'] == "main") {
                                                $otherSized = $product['otherSize'];
                                                $sn =1;
                                                foreach($otherSized as $extraSize)
                                                    echo "<li>Other Sizes: ".$prodOtherSizes[$extraSize]['name'].'</li>';
                                            }
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
												echo "</ul>";

											}


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
                                        </div>

                                        <div class="cart-price text-right">
                                            $<?php echo formatPrice($product['totalPrice']); $totalAmount += $product['totalPrice'];?>
                                        </div>
                                    </div>
                                    <?php
									}else{

										$AddonsKey  = explode("_",$key);
										if($AddonsKey[1]>0){continue;}
											$addonsCartProduct[] = $product;
										?>

                                        <?php
									}

                                }
								if(!empty($addonsCartProduct)){

										?>
                                        <div class="AddonsCheckOut" style="margin-top:10px;">

                                                    <h3 style="font-size:18px;">Addons</h3>
                                                <div class="row">
                                                   <div class="col-lg-10">
                                                    <ul>
                                                        <?php
                                                        foreach ($addonsCartProduct as $addonKey => $addonValue){
                                                            if($addonValue['type'] == 'addon' ){
                                                                $addonKey = explode("_",$addonValue['id']);
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

                                                                echo "</li>";
                                                                $product['totalPrice'] = $product['totalPrice'] + $addonValue['totalPrice'];
																$totalAmount +=$addonValue['totalPrice'];
																$totalAddonsPrice +=$addonValue['totalPrice'];
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                    </div>
                                                    <div class="col-lg-2">
                                                    <div class="TotalAddonsPrice"><?php  echo " $" . formatPrice($totalAddonsPrice); ?></div>
                                                    </div>
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
                                              <?php  echo productImageSrc($getBanners['filename'],$getProduct['id'],'354'); ?>

                                            </div>
                                            <div class="cart-pr-info">
                                                <h3>Change request for  <?=$getProduct['Title'];?>  <?php echo ($product['TypeBanner']!="") ? " - ".ucfirst($product['TypeBanner']):""; echo ($product['SizeLabel']!="") ? " - ".ucfirst($product['SizeLabel']):""; ?>  for order #<?php echo $key2; ?></h3>

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
                            <div class="total-price text-right" style="width:95%">

                                Total :  <strong class="totalAmt">$<?=formatPrice($totalAmount);?></strong>

                            </div>


                             <?php
                                $newCartAmount = '&nbsp;';
                                if(isset($_SESSION['DISCOUNT_DATA'])) {
                                    $newCartAmount=$cartAmount-$_SESSION['DISCOUNT_DATA']['discountapplied'];
                                }
                                ?>

                                <div class="text-right discount_price_total" <?php if(!isset($_SESSION['DISCOUNT_DATA'])) { echo 'style="display:none"'; } ?>>Discount <?php echo ($_SESSION['DISCOUNT_DATA']['discountper']>0) ? " (".$_SESSION['DISCOUNT_DATA']['discountper']."%) ":"" ?>: <strong> - <?="$".formatPrice($_SESSION['DISCOUNT_DATA']['discountapplied']);?></strong></div>
                                <div class="text-right discount_price_total_amount" <?php if(!isset($_SESSION['DISCOUNT_DATA'])) { echo 'style="display:none"'; } ?>>New Total : <strong class="cartTotal"><?="$".formatPrice($newCartAmount);?></strong></div>



                                <div class="col-lg-12">
                                    <div class="discount-code mb-5 clearfix">
                                        <a class="goodpad" id="applyDiscount">Apply coupon</a>
                                        <div class="discount-block">
                                          <input type="text" class="form-control mb-3 wid70" id="coupon" value="<?=$_SESSION['DISCOUNT_DATA']['discountData']['DiscountName']?>">

                                          <a class="btn-lg btn-grad" onClick="applyCoupon()">APPLY DISCOUNT</a>
                                        </div>
                                    </div>
                                </div>

                                 <?php if(isset($_SESSION['loginType']) && $_SESSION['loginType']=="guest"){ ?>
                                	<div class="GuestCheckOutPage">
                                    <h3>Where should your order be delivered?</h3>
                                    <form method="post" action="payment.php">

                                     <div class="row">
                                     <div class="col-lg-4"><input required name="guest_email_address"  value="<?php echo (isset($_SESSION['guest_checkout']) && $_SESSION['guest_checkout']['email']!="") ?$_SESSION['guest_checkout']['email']:""; ?>" placeholder="Email" type="email" class="form-control"></div>
                                        <div class="col-lg-4"><input required name="guest_first_name" value="<?php echo (isset($_SESSION['guest_checkout']) && $_SESSION['guest_checkout']['first_name']!="") ?$_SESSION['guest_checkout']['first_name']:""; ?>" placeholder="First Name" type="text" class="form-control"></div>
                                        <div class="col-lg-4"><input required name="guest_last_name" value="<?php echo (isset($_SESSION['guest_checkout']) && $_SESSION['guest_checkout']['last_name']!="") ?$_SESSION['guest_checkout']['last_name']:""; ?>"  placeholder="Last Name" type="text" class="form-control"></div>

                                     </div>
                                      <div class="row create_account">
                                     <?php /*?><div class="col-lg-12"><label class="custom-control custom-checkbox" style="margin-top:15px;margin-left:5px;">

                                            <input type="checkbox" class="custom-control-input" value="1" <?php echo ($_SESSION['guest_checkout']['create_account']==1)? "checked='checked'":""; ?> name="guest_create_account">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">
                                           Create a account?
                                 </span></label></div><?php */?>

                                     </div>
                                        <div class="row">
                                        <div class="col-lg-12 guestCheckOutBtn">
                                          <a href="addons.php" class="btn-grey btn-lg float-sm-left" >Back</a>
                                     <button type="submit" class="btn-lg btn-grad float-sm-right" id="PayGuestCheckOut">Proceed</button>
                                     </div>
                                     </div>

                                     </form>
                                     </div>
								<?php }else{ ?>
                               <?php
							   	if($_SESSION['userType']=="sale_rep" || $_SESSION['userType']=="admin"){

									$allCustomers = getAllCustomer();
									$getSaleOrderByKey = getSaleOrderByKey($_SESSION['RepKeyModify'],$_SESSION['userId'],'sales_rep');
									?>

                                    <form action="" id="SaleRepForm">
                                    <div class="row">
                                        	<div class="col-lg-6  mb-3">
                                                <label>Customer</label>
                                                <select name="customers" id="customers" class="form-control">

                                                    <option value="-1" <?php if($getSaleOrderByKey['UserID']==""){echo "selected";} ?>>Please select customer</option>
                                                    <?php foreach($allCustomers as $single){
														$customerName  = "";
															if($single['FName']!=""){
																$customerName = $single['FName'];
															}
															if($single['LName']!=""){
																$customerName .= " ".$single['LName'];
															}
															if($single['Email']!=""){
																$customerName .= " (".$single['Email']." )";
															}


														 ?>
                                                    		<option value="<?php echo $single['UserID']; ?>" <?php  echo ($single['UserID']==$getSaleOrderByKey['UserID'])?"selected":"";?>><?php echo $customerName ; ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>

                                            <div class="col-lg-6  mb-3 create_customer_btn">
                                            <br>OR&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn-grad mb-3 btn-lg create_customer">Create Customer</button>
                                            </div>
                                        </div>
                                        <div class="errorSaleRep">
                                            	<div class="notification " style="display:flex;">
                    <div class="d-flex"><i class="fas fa-times-circle"></i></div>
                    <span></span><button class="close-ntf"><i class="fas fa-times"></i></button>
                </div>
                                            </div>
                                        <div class="row hideCreateCustomer">

                                            <div class="col-lg-6  mb-3">
                                                <label>First Name</label>
                                                <input type="text" name="first_name" class="form-control">
                                            </div>

                                            <div class="col-lg-6  mb-3">
                                                <label>Last Name</label>
                                                <input type="text" name="last_name" class="form-control mb-3">
                                            </div>

                                            <div class="col-lg-6  mb-3">
                                                <label>Email</label>
                                                <input type="text" name="emailAddress" class="form-control">
                                            </div>

                                            <div class="col-lg-6  mb-3">
                                                <label>Password</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>

                                            <div class="col-lg-6  mb-3">
                                                <label>Repeat Password</label>
                                                <input type="password" name="confirmPassword" class="form-control">
                                            </div>


                                        </div>
                                        <div class="offset-lg-9">
                                         <div class="col-lg-12 text-center">

                                                <button type="submit" class="btn-grad mb-3 btn-lg"><?php echo (!empty($getSaleOrderByKey))?"Update Order":"Create Order" ;?></button>
                                            </div>
                                        <div></div></div>
                                    </form>


                                    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
			<script>
            		jQuery(document).ready(function(e) {

					 	jQuery(".create_customer").click(function(e) {
                            jQuery(".hideCreateCustomer").css("display","inline-flex");
							jQuery('#customers').prop('selectedIndex', 0).change();
						//	jQuery(".create_customer_btn").hide();
                        });
					    jQuery('#customers').select2({
					 		 placeholder: 'Please select customer'
					});
					jQuery(document).on("change","#customers",function(){
							var customer = jQuery(this).val();

								jQuery(".hideCreateCustomer").hide();
							if(customer=="-1"){
								jQuery(".hideCreateCustomer").css("display","inline-flex");
							//	jQuery(".create_customer_btn").hide();
							}else{
								jQuery(".create_customer_btn").show();
							}

					});
			jQuery("#SaleRepForm").submit(function(e) {
            e.preventDefault();
			var customer = jQuery("#SaleRepForm select[name='customers']").val();

			if(customer==''){

				 jQuery("#SaleRepForm select[name='customers']").focus();
                $(".errorSaleRep span").html("Please select customer.");
                $(".errorSaleRep").attr('style','display:flex;');
				return false;

			}
				if(customer=='-1'){

            var email = jQuery("#SaleRepForm input[name='emailAddress']").val();
            var fname = jQuery("#SaleRepForm input[name='first_name']").val();
            var lname = jQuery("#SaleRepForm input[name='last_name']").val();
            var password = jQuery("#SaleRepForm input[name='password']").val();
            var confirmPassword = jQuery("#SaleRepForm input[name='confirmPassword']").val();

            var validationEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            if(fname == '') {
                jQuery("#SaleRepForm input[name='first_name']").focus();
                $(".errorSaleRep span").html("Please enter first name.");
                $(".errorSaleRep").attr('style','display:flex;');


                return false;
            }

            if(lname == '') {
                jQuery("#SaleRepForm input[name='last_name']").focus();
                $(".errorSaleRep span").html("Please enter last name.");
                $(".errorSaleRep").attr('style','display:flex;');


                return false;
            }



			if(!validationEmail.test(email)) {
                jQuery("#SaleRepForm input[name='emailAddress']").focus();
                $(".errorSaleRep span").html("Please fill correct email.");
                $(".errorSaleRep").attr('style','display:flex;');


                return false;
            }
            if(password == '') {
                jQuery("#SaleRepForm input[name='password']").focus();
                $(".errorSaleRep span").html("Please enter password.");
                $(".errorSaleRep").attr('style','display:flex;');

                return false;
            }
            if(confirmPassword == '') {
                jQuery("#SaleRepForm input[name='confirmPassword']").focus();
                $(".errorSaleRep span").html("Please enter confirm password.");
                $(".errorSaleRep").attr('style','display:flex;');

                return false;
            }

            if(password != confirmPassword) {
                jQuery("#SaleRepForm input[name='password']").focus();
                $(".errorSaleRep span").html("Password and confirm password does not match.");
                $(".errorSaleRep").attr('style','display:flex;');

                return false;
            }
            if(password.length<7) {
                jQuery("#SaleRepForm input[name='password']").focus();
                $(".errorSaleRep span").html("Password should be atleast 7 characters.");
                $(".errorSaleRep").attr('style','display:flex;');

                return false;
            }
				}

            jQuery(".mainLoader").show();
            $.ajax({
                type: "POST",
                url: "<?=SITEURL;?>ajax/saleRepCreateOrder.php",
               data: "emailAdress="+email+"&customer="+customer+"&password="+password+"&fname="+fname+"&lname="+lname+"&regiser=info",
                success: function(regResponse) {
                    regResponse = JSON.parse(regResponse);
                    jQuery(".mainLoader").hide();
                    if(regResponse.Status == 'success') {

					  	window.location='thankyou.php';
                        return false;
                    }
                    else {
                        $(".errorSaleRep span").html(regResponse.Message);
                        $(".errorSaleRep").attr('style','display:flex;');
                        /*$('html, body').animate({
                            scrollTop: $(".error").offset().top
                        }, 1000);*/
                        setTimeout(function() {$(".errorSaleRep").hide(500)}, 12000);
                        return false;
                    }
                }
            });
        });
                    });
            </script>

                                <!--profile-tab-->

                                    <?php
									}else{
							    	include("paymentForm.php");
								}
								} ?>
                            <!---wizard-foot-->
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
<script>
    $(document).ready(function() {
        $('.close-ntf').click(function() {
            $(this).parent().fadeOut(300, function() {
                $(this).remove();
            });
        });
    });
</script>

<script type="text/javascript">
    //set your publishable key
    Stripe.setPublishableKey('<?=PUBLISHABLE_KEY?>');

    //callback to handle the response from stripe
    function stripeResponseHandler(status, response) {
        if (response.error) {
            //enable the submit button
            $('#payBtn').removeAttr("disabled");
            //display the errors on the form
            $(".error span").html(response.error.message);
            $(".error").attr('style','display:flex;');
            $('html, body').animate({
                scrollTop: $(".error").offset().top
            }, 1000);
            setTimeout(function() {$(".error").hide(500)}, 12000);
        } else {
            var form$ = $("#paymentFrm");
            //get token id
            var token = response['id'];
            //insert the token into the form
            form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
            //submit form to the server
            form$.get(0).submit();
        }
    }
    $(document).ready(function() {
        //on form submit
        $("#paymentFrm").submit(function(event) {
            //disable the submit button to prevent repeated clicks
            $('#payBtn').attr("disabled", "disabled");

            var datavariables= {
                coupon: '<?php echo $_SESSION['DISCOUNT_DATA']['discountData']['DiscountName'] ?>'
            }
				if(jQuery("#coupon").val()!=""){
				  $.ajax({
                url:'<?=SITEURL?>ajax/applydiscount.php',
                type:'POST',
                data:datavariables,
                success:function(regResponse) {
                    regResponse=JSON.parse(regResponse);
                    if(regResponse.status=="error") {
                        jQuery(".discount_price_total,.discount_price_total_amount").hide();
						$(".error span").html(regResponse.message);
                        $(".error").attr('style','display:flex;');
                        $('html, body').animate({
                            scrollTop: $(".error").offset().top
                        }, 1000);


                        setTimeout(function() {$(".error").hide(500)}, 12000);
                    } else if(regResponse.status=="success") {

            //create single-use token to charge the user
            Stripe.createToken({
                number: $('.card-number').val(),
                cvc: $('.card-cvc').val(),
                exp_month: $('.card-expiry-month').val(),
                exp_year: $('.card-expiry-year').val(),
                address_zip: $('.postal-code').val()
            }, stripeResponseHandler);

					}

                }
            });
				}else{

            //create single-use token to charge the user
            Stripe.createToken({
                number: $('.card-number').val(),
                cvc: $('.card-cvc').val(),
                exp_month: $('.card-expiry-month').val(),
                exp_year: $('.card-expiry-year').val(),
                address_zip: $('.postal-code').val()
            }, stripeResponseHandler);
				}


            //submit from callback
            return false;
        });
				$(".discount-block").hide();
				$("#applyDiscount").on("click", function(){
						$(".discount-block").toggle();
			});
    });

    function applyCoupon() {
        $(".cartTotal").html('');
		jQuery(".error,.success").hide();
        $(".cartTotal").hide();
		  jQuery(".discount_price_total,.discount_price_total_amount").hide();

        if(jQuery("#coupon").val()=="") {

            $(".error span").html("Discount code is empty");
            $(".error").attr('style','display:flex;');
            $('html, body').animate({
                scrollTop: $(".error").offset().top
            }, 1000);
            setTimeout(function() {
                $(".error").hide(500);
            }, 12000);
        }
        else {
            var datavariables= {
                coupon: jQuery("#coupon").val()
            }
            $.ajax({
                url:'<?=SITEURL?>ajax/applydiscount.php',
                type:'POST',
                data:datavariables,
                success:function(regResponse) {
                    regResponse=JSON.parse(regResponse);
                    if(regResponse.status=="error") {
                        jQuery(".discount_price_total,.discount_price_total_amount").hide();
						$(".error span").html(regResponse.message);
                        $(".error").attr('style','display:flex;');
                        $('html, body').animate({
                            scrollTop: $(".error").offset().top
                        }, 1000);


                        setTimeout(function() {$(".error").hide(500)}, 12000);
                    } else if(regResponse.status=="success") {
						window.location.reload();
                        jQuery(".discount_price_total,.discount_price_total_amount").show();

						  $(".cartTotal").html(" $"+(parseFloat(regResponse.valsav)).toFixed(2));
						  if(regResponse.discountper>0){
							  $(".discount_price_total").html(" Discount ("+regResponse.discountper+"%) :  <strong> - $"+(parseFloat(regResponse.discount)).toFixed(2)+"</strong>");
						  }else{
						   $(".discount_price_total").html(" Discount :  <strong> - $"+(parseFloat(regResponse.discount)).toFixed(2)+"</strong>");
						  }

                    $(".cartTotal").show();
                        $(".success span").html(regResponse.message);
                        $(".success").attr('style','display:flex;');
                        $('html, body').animate({
                            scrollTop: $(".success").offset().top
                        }, 1000);
                        setTimeout(function() {$(".success").hide(500)}, 12000);
                    }

                }
            });
        }
    }

</script>
<style>
    .product-img{max-width: 10%;}
    .product-img img{        width: 70px;}
    .cart-row { padding-bottom:10px !important; padding-top:10px !important;}
    .goodpad{ padding:0px !important;}
    .discount-code{ margin-bottom:10px !important;}
	#paymentFrm label{ font-size:18px;}
</style>
</body>

</html>
