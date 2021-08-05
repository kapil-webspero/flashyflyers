<?php

	ob_start();

	require_once 'function/constants.php';

	require_once 'function/configClass.php';

	require_once 'function/siteFunctions.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Summary | Flashy Flyers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once 'files/headSection.php'; ?>
</head>

<body>
    <?php require_once 'files/headerSection.php'; ?>
    <div class="page-wrap pb-5 pt-3 bubble-bg-3 mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="wizard">
                        <div class="wizard-inner">
                            <ul class="wizard-steps">
                                <li><a href="cart.html"><span class="step-num">1</span>Your cart</a></li>
                                <li><a href="addons.html"><span class="step-num">2</span>Add-ons</a></li>
                                <li class="active"><a href="info.html"><span class="step-num">3</span>Info</a></li>
                                <li><a href="checkout.html"><span class="step-num">4</span>Checkout</a></li>
                            </ul>
                        </div>
                        <div class="wizard-content bx-shadow pl-sm-5 pr-sm-5 pb-sm-5">
                            <div class="row pb-4">
                                <div class="col-lg-auto col-md-3 product-summary-img">
                                    <img src="images/cart-1.jpg" class="img-fluid" alt="">
                                    <a href="info.html">edit</a>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <div class="product-summary-block">
                                        <h1>Flyer template Lorem Ipsum</h1>
                                        <label>Main title:</label>
                                        <p>Flyer Product title</p>
                                        <label>Sub title:</label>
                                        <p>Flyer Product Subtitle </p>
                                        <label>Event date:</label>
                                        <p>22/09/2019</p>
                                        <label>Music by:</label>
                                        <p>J K Donald</p>
                                        <label>More info:</label>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
                                            commodo consequat. </p>
                                        <label>Venue:</label>
                                        <p>MSG, New York</p>
                                        <label>Address:</label>
                                        <p>10th St, London New York</p>
                                        <label>Size :</label>
                                        <p>4x6in</p>
                                        <label>Files uploaded:</label>
                                        <div class="flyer-uploaded-files">
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row pb-4">
                                <div class="col-lg-auto col-md-3 product-summary-img">
                                    <img src="images/cart-1.jpg" class="img-fluid" alt="">
                                    <a href="info.html">edit</a>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <div class="product-summary-block">
                                        <h1>Flyer template Lorem Ipsum</h1>
                                        <label>Main title:</label>
                                        <p>Flyer Product title</p>
                                        <label>Sub title:</label>
                                        <p>Flyer Product Subtitle </p>
                                        <label>Event date:</label>
                                        <p>22/09/2019</p>
                                        <label>Music by:</label>
                                        <p>J K Donald</p>
                                        <label>More info:</label>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
                                            commodo consequat. </p>
                                        <label>Venue:</label>
                                        <p>MSG, New York</p>
                                        <label>Address:</label>
                                        <p>10th St, London New York</p>
                                        <label>Size :</label>
                                        <p>4x6in</p>
                                        <label>Files uploaded:</label>
                                        <div class="flyer-uploaded-files">
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row pb-4">
                                <div class="col-lg-auto col-md-3 product-summary-img">
                                    <img src="images/cart-1.jpg" class="img-fluid" alt="">
                                    <a href="info.html">edit</a>
                                </div>
                                <div class="col-lg-10 col-md-9">
                                    <div class="product-summary-block">
                                        <h1>Flyer template Lorem Ipsum</h1>
                                        <label>Main title:</label>
                                        <p>Flyer Product title</p>
                                        <label>Sub title:</label>
                                        <p>Flyer Product Subtitle </p>
                                        <label>Event date:</label>
                                        <p>22/09/2019</p>
                                        <label>Music by:</label>
                                        <p>J K Donald</p>
                                        <label>More info:</label>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea
                                            commodo consequat. </p>
                                        <label>Venue:</label>
                                        <p>MSG, New York</p>
                                        <label>Address:</label>
                                        <p>10th St, London New York</p>
                                        <label>Size :</label>
                                        <p>4x6in</p>
                                        <label>Files uploaded:</label>
                                        <div class="flyer-uploaded-files">
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                            <figure>
                                                <img src="images/cart-1.jpg" alt="">
                                                <figcaption>File-1.jpg</figcaption>
                                            </figure>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="wizard-foot clearfix mt-4">
                                <a href="addons.html" class="btn-grey btn-lg float-sm-left">Back</a>
                                <a href="checkout.html" class="btn-lg btn-grad float-sm-right">$72 - Procced</a>
                            </div>
                            <!---wizard-foot-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'files/footerSection.php' ?>
    <!----SCRIPTS---------->
    <script src="js/jquery.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>>