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

                                <h1 class="udr-heading mb-4">Contact us</h1>

                                <p>Don't hesitate to reach out to us with any questions, requests, or comments. </br> We are more than happy to assist and will respond shortly </p>

                                <form action="" class="contact-form clearfix mt-sm-5">

                                    <label>Your names</label>

                                    <input type="text" class="form-control mb-3">

                                    <label>Your email</label>

                                    <input type="email" class="form-control mb-3">

                                    <label>Your phone</label>

                                    <input type="text" class="form-control mb-3">

                                    <label>Message</label>

                                    <textarea name="" class="form-control mb-3"></textarea>

                                    <button type="submit" class="btn-grad float-right">Send message</button>

                                </form>

                            </div>

                        </div>

                        <!--div class="col-lg-3">

                            <div class="ct-info mt-5">

                                <i class="fas fa-phone"></i>

                                <p><strong>Phone</strong><a href="tel:+61383766284"> +61 3 8376 6284</a>

                                </p>

                            </div-->

                            <!--<div class="ct-info mt-5">

                                <i class="fas fa-map-marker-alt"></i>

                                <p><strong>Address</strong> 5201 Eden Avenue <br> Suite 300 <br> Edina, MN 55436

                                </p>

                            </div>-->

                            <div class="ct-info mt-5">

                                <i class="fas fa-envelope"></i>

                                <p><strong>Email</strong> support@flashyflyers.com

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
<style>
.wpcf7-response-output.wpcf7-mail-sent-ok {top: 39pt !important;}
.wpcf7-response-output.wpcf7-validation-errors {top: 39pt !important;}
</style>
    <?php require_once 'files/footerSection.php' ?>

    <!----SCRIPTS---------->

    <script src="js/jquery.js"></script>

    <script src="js/bootstrap.min.js"></script>

    <script src="js/popper.min.js"></script>

</body>



</html>