<?php

	ob_start();

	require_once 'function/constants.php';

	require_once 'function/configClass.php';

	require_once 'function/siteFunctions.php';

?>
<!DOCTYPE html>

<html lang="en">



<head>

    <title>Forgot Password | Flashy Flyers</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require_once 'files/headSection.php'; ?>

</head>



<body>



    <?php require_once 'files/headerSection.php'; ?>



    <div class="page-wrap bubble-bg-1 pb-5 pt-3">



        <div class="container">
        
        	<div class="notification warning">
                        <div class="d-flex"><i class="fas fa-bell"></i></div>
						<span>Warning : General message</span>
						<button class="close-ntf"><i class="fas fa-times"></i></button>
					</div>
            
            <div class="notification success" <?php if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { echo 'style="display:flex;"'; } ?>	>
                        <div class="d-flex"><i class="fas fa-check"></i></div>
						<span><?php echo $_SESSION['SUCCESS'];  unset($_SESSION['SUCCESS']);?></span>
						<button class="close-ntf"><i class="fas fa-times"></i></button>
					</div>
            
            <div class="notification error">
                        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
						<span>Error : Email not valid</span><button class="close-ntf"><i class="fas fa-times"></i></button>
					</div>



            <form id="resetForm" class="signup-form bx-shadow clearfix p-4">

                <h1 class="text-center udr-heading mb-4">Forgot Password</h1>

                <label>Email</label>

                <input type="text" name="email" class="form-control mb-3">

                <span>We will send you an email with instructions! </span>

                <button type="submit" class="btn-grad btn-lg float-right mt-4 mb-2">Send Instructions</button>

            </form>



        </div>



    </div>



    <?php require_once 'files/footerSection.php' ?>



    <!----SCRIPTS---------->

    <script src="js/jquery.js"></script>

    <script src="js/bootstrap.min.js"></script>

    <script src="js/popper.min.js"></script>
    
    <script>
	jQuery(document).ready(function() {
		jQuery("#resetForm").submit(function(e) {
            e.preventDefault();
            var email = jQuery("#resetForm input[name='email']").val();
            
            if(email == '') {
				jQuery("#resetForm input[name='email']").focus();
				$(".error span").html("Please enter email address.");
				$(".error").attr('style','display:flex;');
				/*$('html, body').animate({
					scrollTop: $(".error").offset().top
				}, 1000);*/
				setTimeout(function() {$(".error").hide(500)}, 12000);
				return false;
			}
			jQuery(".mainLoader").show();
            
            $.ajax({
                type: "POST",
                url: "<?=SITEURL;?>ajax/forgot-password.php",
                data: "email="+email,
                success: function(regResponse) {
                    regResponse = JSON.parse(regResponse);
                    jQuery(".mainLoader").hide();
                    if(regResponse.Status == 'success') {
                        $(".success span").html(regResponse.Message);
						$(".success").attr('style','display:flex;');
						/*$('html, body').animate({
							scrollTop: $(".error").offset().top
						}, 1000);*/
						setTimeout(function() {$(".success").hide(500)}, 12000);
                        return false;
                    }
                    else {
						$(".error span").html(regResponse.Message);
						$(".error").attr('style','display:flex;');
						/*$('html, body').animate({
							scrollTop: $(".error").offset().top
						}, 1000);*/
						setTimeout(function() {$(".error").hide(500)}, 12000);
                        return false;
                    }
                }
            });
		});
	});
	</script>

</body>



</html>