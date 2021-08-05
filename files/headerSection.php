<?php 
$showOffersPopup = 0;

if(!isset($_SESSION['userId']) && empty($_SESSION['userId'])){
	$showOffersPopup = 1;	
}
if(!isset($_COOKIE['FlashyOfferPopup']) &&  $_COOKIE['FlashyOfferPopup']!="Yes"){
	$showOffersPopup = 1;	
}

if(isset($_COOKIE['FlashyOfferPopup']) &&  $_COOKIE['FlashyOfferPopup']=="Yes"){
	$showOffersPopup = 0;	
}
if(isset($_SESSION['userId']) && !empty($_SESSION['userId'])){
	$showOffersPopup = 0;	
}
 if($showOffersPopup==1){
?>

<?php /*?><div class="popup_section">
<div class="container">

<div class="modal-backdrop show_popup_backdrop" style="display:none;"></div>
  <!-- The Modal -->
  <div class="modal show show_popup_model">
    <div class="modal-dialog">
      <div class="modal-content">
            <img src="<?=SITEURL;?>images/close_popup.png"  class="close_popup closeTopBtn">
        
        <!-- Modal body -->
        <div class="modal-body">
         	<div class="row">
            	<div class="col-lg-6 push_mobile">
                	<div class="popup_images">
                    	<ul>
                        	<li><img src="<?=SITEURL;?>images/Offer1_new.jpg" /></li>
                            <li><img src="<?=SITEURL;?>images/Offer2_new.jpg" /></li>
                            <li><img src="<?=SITEURL;?>images/Offer3_new.jpg" /></li>
                            <li><img src="<?=SITEURL;?>images/Offer4_new.jpg" /></li>
                            <li><img src="<?=SITEURL;?>images/Offer5_new.jpg" /></li>
                            <li><img src="<?=SITEURL;?>images/Offer5_new.jpg" /></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                	<div class="popup_right_form">
                    	<img src="<?=SITEURL;?>images/popup_logo.png" />
                        <h2 class="mt-4">join the club</h2>
                        <p class="pb-3">Welcome to Flashy Flyers, the most preferred flyer template design shop.</p>
                        <form class="PopupForm">
                        	<input type="text" name="popupname" id="popupname" placeholder="Your names" class="inputtext mb-2" />
                            <input type="email" name="popupemail" id="popupemail" placeholder="Email" class="inputtext mb-2" />
                            <button type="button" class="signbtn mb-5 PopupFormSubmit">sign up</button>
                            <p class="popup_msg close_popup">No thanks, i'll stick with the regular price.</p>
                            <div class="PopupMsg"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
  
</div>
</div><?php */?>
 <?php } ?>


<div class="mainLoader"><div class="loaderInner"><div class="loaderCenter"><div class="lds-ripple"><div></div><div></div></div></div></div></div>

<header class="header-desktop">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light main-nav">
            <a class="navbar-brand" href="<?=SITEURL;?>">
               <picture>
  <source srcset="<?=SITEURL;?>images/logo.webp" type="image/webp">
  <source srcset="<?=SITEURL;?>images/logo.png" type="image/png">
<img class="img-fluid" src="<?=SITEURL;?>images/logo.webp" width="450" height="98">
</picture>
</a>
            <div class="ml-sm-auto cart-mobile">
                <?php
				if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
                $cartPro = $_SESSION['CART'];
                $totalCart  = 0;
					foreach($cartPro as $key => $product ) {
						if($product['type'] != "main") { continue; }
						$totalCart += 1;
					}
				?>		
				<div class="cart">
                    <a href="<?=SITEURL;?>cart.php" href='<?=SITEURL;?>cart.php'><i class="fas fa-shopping-cart"></i></a>
                    <span class="cart-num cart-product"><?=$totalCart;?></span>
                </div>
                <?php
				} 
				?> 
            </div>
            <?php 
			if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'android')) {
			?>
            <div class="MobileSearchTop">
            <?php include("search_top.php"); ?>
            </div>
            <?php } ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
           <span class="navbar-toggler-icon"></span>
           </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>search.php">From Template</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>search.php?type=custom">From Scratch Designs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>psdshop.php">PSD Shop</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>contact">Contact Us</a>
                    </li>
                    <!-- <li class="nav-item">
                       <i class="fas fa-bell"></i>
                    </li> -->
                    
					<?php
                    if(isset($_SESSION['userId']) && !empty($_SESSION['userId']) && $_SESSION['loginType']!="guest" && ($_SESSION['userType']=="admin"|| $_SESSION['userType']=="user" || $_SESSION['userType']=="designer" || $_SESSION['userType']=="sale_rep" )) {


						if($_SESSION['userType'] == "admin") {
						?>
                        <div class="dropdown account-dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="account-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Account
                            </button>
                            <div class="dropdown-menu" aria-labelledby="account-btn">
                                <a class="dropdown-item" href="<?=ADMINURL;?>">Dashboard</a>
                                <a class="dropdown-item" href="<?=ADMINURL;?>orders.php">Orders</a>
                                <a class="dropdown-item" href="<?=ADMINURL;?>products.php">View products</a>
                                <?php 
                                /*
								<a class="dropdown-item" href="<?=ADMINURL;?>product-tags.php">Product Tags</a>
                                <a class="dropdown-item" href="<?=ADMINURL;?>product-types.php">Product Types</a>
                                 <a class="dropdown-item" href="<?=ADMINURL;?>products_review.php">Product Reviews</a>
                                 */ ?>
								
                                <?php /*?>
								
								
                                <a class="dropdown-item" href="<?=ADMINURL;?>create-product.php">Create product</a><?php */?>
                                  <a class="dropdown-item" href="<?=ADMINURL;?>discount.php">Discount Codes</a>
                                <a class="dropdown-item" href="<?=ADMINURL;?>users.php">Users</a>
                                <?php 
                                /*
                                <a class="dropdown-item" href="<?=ADMINURL;?>transactions.php">Transactions</a>
                                <a class="dropdown-item" href="<?=ADMINURL;?>work-report.php">Work Report</a>
                                */
                                ?>
                                <a class="dropdown-item" href="<?=ADMINURL;?>finance-center.php">Finance Center</a>
                                <a class="dropdown-item" href="<?=ADMINURL;?>setting-variables.php">Settings</a>
                                 <?php /*?> <a class="dropdown-item" href="<?=ADMINURL;?>favorite.php">Favorite</a><?php */?>
                                <a class="dropdown-item" href="<?=ADMINURL;?>profile.php">Profile</a>
                                <a class="dropdown-item" href="<?=SITEURL;?>logout.php">Logout</a>
                            </div>
                        </div>
                        <?php
						} else if($_SESSION['userType'] == "designer") {
						?>
                        <div class="dropdown account-dropdown">
                        	<button class="btn btn-secondary dropdown-toggle" type="button" id="account-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Account
                            </button>
                            <div class="dropdown-menu" aria-labelledby="account-btn">
                                <a class="dropdown-item" href="<?=SITEURL;?>my-work.php" >My Work</a> 
                                 <a class="dropdown-item" href="<?=ADMINURL;?>products.php">View products</a>
                                <a class="dropdown-item" href="<?=SITEURL;?>profile.php">Profile</a>
                                <a class="dropdown-item" href="<?=SITEURL;?>logout.php">Logout</a>
                            </div>
                    	</div>
                        <?php
						} else {
						?>
                        <div class="dropdown account-dropdown">
                        	<button class="btn btn-secondary dropdown-toggle" type="button" id="account-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Account
                            </button>
                            <div class="dropdown-menu" aria-labelledby="account-btn">
                            		<?php if( $_SESSION['userType']!="sale_rep"){ ?>
                            
                                <a class="dropdown-item" href="<?=SITEURL;?>my-bookmarks.php">My Bookmarks</a>
                                <a class="dropdown-item" href="<?=SITEURL;?>my-orders.php">My Orders</a>
                                <?php } ?>
                                	<?php if( $_SESSION['userType']=="sale_rep"){ ?>
                                   <a class="dropdown-item" href="<?=ADMINURL;?>orders.php">Orders</a>
                                	   <a class="dropdown-item" href="<?=ADMINURL;?>discount.php">Discount Codes</a>
                                <a class="dropdown-item" href="<?=ADMINURL;?>users.php">Users</a>
                               
                                   <?php } ?>
                                   
                                <a class="dropdown-item" href="<?=SITEURL;?>profile.php">Profile</a>
                                <a class="dropdown-item" href="<?=SITEURL;?>logout.php">Logout</a>
                            </div>
                    	</div>
                        <?php
						}
					}
					else {
						?>
						                        <li class="nav-item" id="login-btn">
                        	<a class="nav-link" href="<?=SITEURL;?>login.php">Login</a>
                    	</li>
						<li class="nav-item" id="register-btn">
                            <a class="nav-link" href="<?=SITEURL;?>signup.php">Sign Up</a>
                        </li>

                        <?php
					}

                    if(isset($_SESSION['userId']) && !empty($_SESSION['userId']))
                    {
                    ?>
                        <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0);" id="flip120"><i class="nav-item fas fa-bell"></i></a>
                        <?php include('superadmin/includes/notifications.php'); ?>
                        </li>
                    <?php
                    }
                    ?> 
                        
                </ul>


                <div class="cart-desktop">
                	<?php
					if(!empty($totalCart) && $totalCart > 0) {
					?>		
					<div class="cart">
						<a href="<?=SITEURL;?>cart.php"><i class="fas fa-shopping-cart"></i></a>
						<span class="cart-num cart-product"><?=$totalCart;?></span>
					</div>
					<?php
					} 
					?>
                </div>
 
            </div>
        </nav>
    </div>
</header>

<?php 
if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'android')) {
}else{include("search_top.php");} ?>


<script>
 function validateEmail($email) {
  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  return emailReg.test( $email );
}
jQuery(document).ready(function(e) {
    jQuery(document).on("click",".PopupFormSubmit",function(){
		var popupname = jQuery("#popupname").val();
		var popupemail = jQuery("#popupemail").val();
		jQuery(".PopupMsg").hide();
		
		if(popupname==""){
			alert("Please enter name");
			jQuery("#popupname").focus();
			return false;	
		}
		
		if(popupemail==""){
			alert("Please enter email");
			
			jQuery("#popupemail").focus();
			return false;	
		}
		if( !validateEmail(popupemail)) { 
			alert("Please enter valid email address");
			
			jQuery("#popupemail").focus();
			return false;	
			
		
		}
		
		jQuery(".signbtn").attr("disabled","disabled");
		jQuery(".signbtn").html("Please wait...");
		
		
		$.ajax({
				type: "POST",
				url: "<?=SITEURL;?>ajax/userRegistrationPopup.php",
				data: "name="+popupname+"&emailAdress="+popupemail,
				success: function(regResponse) {
					jQuery(".signbtn").removeAttr("disabled");
					jQuery(".signbtn").html("Sign up");
					
					regResponse = JSON.parse(regResponse);
					jQuery(".PopupMsg").show();
					if(regResponse.Status == 'success') {
						
						$(".PopupMsg").html(regResponse.Message);
						setTimeout(function() {$(".success").hide(500);window.location.href = regResponse.redirectUrl;}, 300);
						return false;
					}
					else {
						$(".PopupMsg").html(regResponse.Message);
						/*$('html, body').animate({
							scrollTop: $(".error").offset().top
						}, 1000);*/
						setTimeout(function() {$(".PopupMsg").hide(500)}, 12000);
						return false;
					}
				}
			});
			
	});
	
	jQuery(document).on("click",".close_popup",function(){
		jQuery(".popup_section").hide();		
jQuery.ajax({
				type: "POST",
				url: "<?=SITEURL;?>ajax/closePopup.php",
				data: "close='yes'",
				success: function(regResponse) {
						jQuery(".popup_section").hide();		
				}
			});	
	});
	
setTimeout(function(){
 
 //jQuery(".show_popup_backdrop").show();
 //jQuery(".show_popup_model").show();
 
 
}, 5000);	
});
				

</script>