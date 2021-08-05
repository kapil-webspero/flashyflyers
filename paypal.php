<?php

	ob_start();

	require_once 'function/constants.php';

	require_once 'function/configClass.php';

	require_once 'function/siteFunctions.php';

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <title>Contact us | Flashy Flyers</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require_once 'files/headSection.php'; ?>

</head>



<body>

    <?php require_once 'files/headerSection.php'; ?>

    <div class="page-wrap bubble-bg-1 pb-5 pt-3">

        <div class="container">

            <div class="row justify-content-center">

                <div class="col-md-11 mb-5 mt-5">

                    <div class="row">

                        <div class="col-lg-9">

                            <div class="contact-wrap bx-shadow pl-sm-5 pr-sm-5 pt-sm-4 pb-sm-5">

                                <h1 class="udr-heading mb-4">Pay Now</h1>

                                
<form action="https://sandbox.paypal.com/cgi-bin/webscr" method="post" class="contact-form clearfix mt-sm-5">

  <!-- Identify your business so that you can collect the payments. -->
  <input type="hidden" name="business" value="herschelgomez@xyzzyu.com">

  <!-- Specify a Buy Now button. -->
  <input type="hidden" name="cmd" value="_xclick">

  <!-- Specify details about the item that buyers will purchase. -->
  <input type="hidden" name="item_name" value="Hot Sauce-12oz. Bottle">
  <input type="hidden" name="amount" value="5.95">
  <input type="hidden" name="currency_code" value="USD">

  <!-- Display the payment button. -->
  <input type="image" name="submit" border="0"
  src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif"
  alt="Buy Now">
  <img alt="" border="0" width="1" height="1"
  src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" >

</form>

                                

                            </div>

                        </div>

                        <div class="col-lg-3">

                            <div class="ct-info mt-5">

                                <i class="fas fa-phone"></i>

                                <p><strong>Phone</strong><a href="tel:+61383766284"> +61 3 8376 6284</a>

                                </p>

                            </div>

                            <!--<div class="ct-info mt-5">

                                <i class="fas fa-map-marker-alt"></i>

                                <p><strong>Address</strong> 5201 Eden Avenue <br> Suite 300 <br> Edina, MN 55436

                                </p>

                            </div>-->

                            <div class="ct-info mt-5">

                                <i class="fas fa-envelope"></i>

                                <p><strong>Email</strong> contact@flashyflyers.com

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <?php require_once 'files/footerSection.php' ?>

    <!----SCRIPTS---------->

    <script src="js/jquery.js"></script>

    <script src="js/bootstrap.min.js"></script>

    <script src="js/popper.min.js"></script>

</body>



</html>