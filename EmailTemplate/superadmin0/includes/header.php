<div class="mainLoader"><div class="loaderInner"><div class="loaderCenter"><div class="lds-ripple"><div></div><div></div></div></div></div></div>

<header class="header-desktop">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light main-nav">
            <a class="navbar-brand" href="<?=SITEURL;?>"><img src="images/logo.png" class="img-fluid" alt=""></a>
            <div class="ml-sm-auto cart-mobile">
                <div class="cart">
                    <a href="<?=SITEURL;?>cart.php"><i class="fas fa-shopping-cart"></i></a>
                    <?php
					if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
						$cartPro = $_SESSION['CART'];
						$totalCart = 0;
						
						foreach($_SESSION['CART'] as $key => $product ) {
						if($product['type'] != "main") { continue; }
						$totalCart += 1;
					}
						if($totalCart>0){
							echo '<span class="cart-num cart-product">'.$totalCart.'</span>';
						}
					} else {
						$totalCart = 0;
					}
					?> 
                </div>
            </div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
           <span class="navbar-toggler-icon"></span>
           </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                   <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>search.php">From Template</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>search.php?type=custom">From Scratch</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>psdshop.php">PSD Shop</a>
                    </li>
                   <?php /*?> <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>blog">Blog</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>faq">F.A.Q</a>
                    </li><?php */?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?=SITEURL;?>contact-us">Contact Us</a>
                    </li>
                    
					<?php
                     if(isset($_SESSION['userId']) && !empty($_SESSION['userId'])) {
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
								  */
                               	?>
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
                                <a class="dropdown-item" href="<?=SITEURL;?>my-work.php">My Work</a> 
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
                            <a class="nav-link" href="<?=SITEURL;?>signup.php">Sign up</a>
                        </li>
                        <?php
					}
                    ?>
                        
                </ul>
                <div class="cart-desktop">
                    <div class="cart">
                        <a href="<?=SITEURL;?>cart.php"><i class="fas fa-shopping-cart"></i></a>
                        <?php
						if($totalCart>0){
							echo '<span class="cart-num cart-product">'.$totalCart.'</span>';
						}
						?>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>
