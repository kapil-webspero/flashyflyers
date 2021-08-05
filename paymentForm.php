<?php 
if($_SESSION['loginType']=="guest" && !empty($_SESSION['guest_checkout'])){
	$strip_email_address = $_SESSION['guest_checkout']['email'];
	$strip_first_name = $_SESSION['guest_checkout']['first_name'];
	$strip_last_name = $_SESSION['guest_checkout']['last_name'];
}else{
		$userData=GetSglRcrdOnCndi(USERS, "USERID=".$_SESSION['userId']);
extract($userData);

	$strip_email_address = $Email;
	$strip_first_name = $FName;
	$strip_last_name = $LName;
}
?>

<div class="row pt-4">
                                <div class="col-lg-5">
                                    <div class="paypal-checkout">
                                        <a href="<?=SITEURL?>paybypaypal.php"  
                                        target="_blank" class="btn-pay">Pay with paypal</a> 
                                       <!-- <div id="paypal-button" style="display:inline-block;"></div>-->
                                        <span>or</span>
                                    </div>
                                    
                                    
                                    <div class="payment_icon">
                                    <h3>You can pay by</h3>
                                        <img src="images/payment_icon.png" />
                                                        
                                    </div>
                                    
                                </div>
                                <div class="col-lg-7">

                                    <form action="paybystripe.php" method="post" class="checkout-form" id="paymentFrm">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>First name</label>
                                                <input type="text" name="fname" value="<?php echo $strip_first_name; ?>"  class="form-control mb-3" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <label>Last name</label>
                                                <input type="text" name="lname" value="<?php echo $strip_last_name; ?>"  class="form-control mb-3" required>
                                            </div>
                                        </div>

                                        <label>Email</label>
                                        <input type="text" value="<?php echo $strip_email_address; ?>" name="email" class="form-control mb-3" required>

                                        <label>Card number</label>
                                        <input type="text" min="16" maxlength="16" name="card_num" class="form-control mb-3 card-number" required>

																				<div class="row">
																					<div class="col-sm-6">
																						<label>CVV</label>
																						<input type="password" name="cvc" maxlength="3" class="form-control mb-3 card-cvc"  required>
																					</div>
																					<div class="col-sm-6">
																						<label>Zip code</label>
																						<input  name="postal_code" class="postal-code form-control mb-3" maxlength="4" required>
																					</div>
																				</div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <label>Expiration month</label>
                                                <select class="form-control mb-3 card-expiry-month" name="exp_month" required>
                                                    <option value="">mm</option>
                                                    <option value="01">Jan (1)</option>
                                                    <option value="02">Feb (2)</option>
                                                    <option value="03">Mar (3)</option>
                                                    <option value="04">Apr (4)</option>
                                                    <option value="05">May (5)</option>
                                                    <option value="06">Jun (6)</option>
                                                    <option value="07">Jul (7)</option>
                                                    <option value="08">Aug (8)</option>
                                                    <option value="09">Sep (9)</option>
                                                    <option value="10">Oct (10)</option>
                                                    <option value="11">Nov (11)</option>
                                                    <option value="12">Dec (12)</option>
                                                </select>
                                            </div>

                                            <div class="col-sm-6">
                                                <label>Expiration year</label>
                                                <select class="form-control mb-3 card-expiry-year" name="exp_year" required>
                                                    <option value="">yyyy</option>
                                                    <?php
                                                    for($yr = date('Y'); $yr<=2030; $yr++) {
                                                        echo '<option value="'.$yr.'">'.$yr.'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            <div class="wizard-foot clearfix mt-4">
                                <?php if(isset($_SESSION['CartRequest']) && count($_SESSION['CartRequest'])>0){ ?>
                                    <a href="cart.php" class="btn-grey btn-lg float-sm-left" >Back</a>
                                <?php }else{ ?>
                                    <a href="addons.php" class="btn-grey btn-lg float-sm-left">Back</a>
                                <?php } ?>
                                <button type="submit" class="btn-lg btn-grad float-sm-right" id="payBtn">Pay Now</button>
                            </div>
                            <?php 
	
	$paypalAmount = $cartAmount;
	
	if(isset($_SESSION['DISCOUNT_DATA'])) {
       $paypalAmount=$paypalAmount-$_SESSION['DISCOUNT_DATA']['discountapplied'];
	}
							?>
                            <script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
    paypal.Button.render({
	locale: 'en_US',
style: {
 size: 'medium',
 color: 'blue',
 shape: 'rect',
 label: 'pay',
 tagline: false
},
        env: 'sandbox',
        /*env: 'production',*/
        client: {
            //web-spero
            // sandbox: 'ARHUUeNT_WdabZnL3AH6e5WY5sEDlj_wJawH1a7c7PkATfN3ZwyDTo0xOmAVUyDpLtO6skYM3Ooikl71'
            //client-side
            sandbox: 'AcJDXEHMnrJ2XLVP9LEUVexx57Udm_bhMJo82rkK6CHbFT-CjI_91JbMxH8Y_cNjIECV9DeX3DWaYWW2'
            /*production: 'PayPalClientId'*/

        },
        payment: function (data, actions) {
			 
            return actions.payment.create({
                transactions: [{
                    amount: {
                        total: <?php echo round($paypalAmount,2); ?>,
                        currency: 'USD'
                    }
                }]
            });
        },
        onAuthorize: function (data, actions) {
		
            return actions.payment.execute()
                .then(function () {
					jQuery(".mainLoader").show();
					 var datavariables= {
             		   paypalpayment: "success",
             		   payerID: data.payerID,
					   txn_id: data.orderID,
					   payment_gross:<?php echo round($paypalAmount,2); ?>,
					
					   
             		   payment_status: "success",
            		};
					 jQuery.ajax({
                url:'<?=SITEURL?>ajax/paypal_payment.php',
                type:'POST',
                data:datavariables,
                success:function(regResponse) {
					jQuery(".mainLoader").hide();
					window.location = "<?php echo SITEURL ?>/thankyou.php?paymentID=" + data.paymentID + "&payerID=" + data.payerID + "&token=" + data.paymentToken;
				}
            	
			});
                   
                });
        }
    }, '#paypal-button');
</script>