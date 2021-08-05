<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';

if(is_cart_empty()) {
    header("location:".SITEURL)	;
    exit();
}
if($_SESSION['loginType'] != "guest") {
    //$_SESSION['ERROR'] = "It&rsquo;s seems like youa'e not logged in with user account. please login again.";
    header("location:".SITEURL."cart.php")	;
    exit();
}

if($_SESSION['userType'] != "user") {
    //$_SESSION['ERROR'] = "It&rsquo;s seems like youa'e not logged in with user account. please login again.";
    header("location:".SITEURL."cart.php")	;
    exit();
}
if(isset($_SESSION['CartRequest']) && count($_SESSION['CartRequest'])>0){
    
	foreach($_SESSION['CartRequest']  as $cartProducts1) {
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

if($_POST['guest_email_address']==""){
 $_SESSION['ERROR']  ="Please enter email address.";
  header("location:".SITEURL."checkout.php");
 
    exit();	
}
$_POST['guest_create_account'] =1;
if($_POST['guest_create_account']=="1"){
	$checkUsername = mysql_query("SELECT * FROM ".USERS." WHERE Email = '".$_POST['guest_email_address']."'");

		if(mysql_num_rows($checkUsername) > 0) {
			$_SESSION['ERROR']  ="Sorry email address already exist. Please choose another email address.";
			header("location:".SITEURL."checkout.php");
   	 		exit();	
		}
		
}
unset($_SESSION['guest_checkout']);
$_SESSION['guest_checkout']['email'] = $_POST['guest_email_address'];
$_SESSION['guest_checkout']['first_name'] = $_POST['guest_first_name'];
$_SESSION['guest_checkout']['last_name'] = $_POST['guest_last_name'];
if($_POST['guest_create_account']=="1"){
$_SESSION['guest_checkout']['create_account'] = $_POST['guest_create_account'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Checkout | Flashy Flyers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once 'files/headSection.php'; ?>
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
                    <div class="notification error">
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
                                <li><a><span class="step-num">1</span>Your cart</a></li>
                                <li><a><span class="step-num">2</span>Add-ons</a></li>
                                <li class="active"><a><span class="step-num">3</span>Checkout</a></li>
                            <?php }else{ ?> 
                              <li><a><span class="step-num">1</span>Your cart</a></li>
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
                                             <?php 
											 echo productImageSrc($getBanners['filename'],$getProduct['id'],'354');
											 ?>
                                              
                                            
                                        </div>
                                        <div class="cart-pr-info">
                                            <h3><?=$getProduct['Title'];?></h3>
                                            <?php
                                            $extraDetails ="";
												
											if($product['template_type']=="psd" && $product['psd3dtitle']=="Yes"){
												  $extraDetails .= '3D Title, ';
											 }
											 if($product['template_type']!="psd"){
                                               if(!isset($product['customeProductFields']) && empty($product['customeProductFields'])){

											if(isset($product['dimensional']))
												 $extraDetails .= $product['dimensional'].', ';
											  
                                            if(isset($product['type_banner']))
                                                $extraDetails .= implode(",",$product['type_banner']).', ';
												
											   }
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
                                            }
											 echo rtrim($extraDetails,", ");
											
											if(isset($product['customeProductFields']) && !empty($product['customeProductFields'])){
												$checkCustomProduct =1;
												echo "<ul>";
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
                                                <?php if($product['ResponseFile']!=""){ ?>
                                                    <img src="<?=$product['ResponseFile'];?>" alt="<?=$getProduct['Title'];?>">
                                                <?php }else{

                                                        ?>
                                               <?php 
											 echo productImageSrc($getBanners['filename'],$getProduct['id'],'354');
											 ?>
                                                    <?php 
                                                }
                                                ?>

                                            </div>
                                            <div class="cart-pr-info">
                                                <h3>Change request for  <?=$getProduct['Title'];?>  for order #<?php echo $key2; ?></h3>

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



                            <div class="row pt-4">
                                <?php /*?><span class="btn-lg">Total: $<?=formatPrice($cartAmount);?></span><?php */?>

                                <!-- <div class="col-lg-12">
                                    <div class="discount-code mb-5 clearfix">
                                        <label class="goodpad">Discount code</label>
                                        <input type="text" class="form-control mb-3 wid70" id="coupon" value="<?php //echo$_SESSION['DISCOUNT_DATA']['discountData']['DiscountName']?>">

                                        <a class="btn-lg btn-grad" onClick="applyCoupon()">APPLY DISCOUNT</a>
                                    </div>
                                </div> -->
                               
                               </div>
                               
                               <?php include("paymentForm.php"); ?>
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
			if(jQuery("#coupon").val()!="" &&  (typeof jQuery("#coupon").val()  !== "undefined")){
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
                exp_year: $('.card-expiry-year').val()
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
                exp_year: $('.card-expiry-year').val()
            }, stripeResponseHandler);
			//create single-use token to charge the user
          }  
            //submit from callback
            return false;
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
    .product-img img{height: 70px !important;
        width: 70px;}
    .cart-row { padding-bottom:10px !important; padding-top:10px !important;}
    .goodpad{ padding:0px !important;}
    .discount-code{ margin-bottom:10px !important;}
</style>
</body>

</html>
