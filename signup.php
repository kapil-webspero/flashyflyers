<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	if(is_login()) {
	   header("location:index.php");
	   exit();
	}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sign up | Flashy Flyers</title>
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
            <div class="notification success">
                <div class="d-flex"><i class="fas fa-check"></i></div>
                <span>Message sent with success</span>
                <button class="close-ntf"><i class="fas fa-times"></i></button>
            </div>
            <div class="notification error">
                <div class="d-flex"><i class="fas fa-times-circle"></i></div>
                <span>Error : Email not valid</span><button class="close-ntf"><i class="fas fa-times"></i></button>
            </div>
            <form id="regForm" class="signup-form bx-shadow clearfix p-4">
                <h1 class="text-center udr-heading mb-4">Sign up</h1>
                <label>Name</label>
                <input type="text" name="name" class="form-control mb-3">
                <label>Email</label>
                <input type="text" name="emailAddress" class="form-control mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control mb-3">
                <button type="submit" class="btn-grad btn-lg float-right mb-2">Sign up</button>
            </form>
            <div class="text-center mb-5 mt-5">
                <a href="<?=SITEURL;?>social/facebook.php" class="btn-fb">Sign up with facebook</a>
            </div>
        </div>

    </div>

    <?php require_once 'files/footerSection.php'; ?>

    <!----SCRIPTS---------->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
        
    <script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#regForm").submit(function(e) {
			e.preventDefault();
			var email = jQuery("#regForm input[name='emailAddress']").val();
			var name = jQuery("#regForm input[name='name']").val();
			var password = jQuery("#regForm input[name='password']").val();

			var validationEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if(!validationEmail.test(email)) {
				jQuery("#regForm input[name='emailAddress']").focus();
				$(".error span").html("Please fill correct email.");
				$(".error").attr('style','display:flex;');
				/*$('html, body').animate({
					scrollTop: $(".error").offset().top
				}, 1000);*/
				setTimeout(function() {$(".error").hide(500)}, 12000);
				return false;
			}			
			if(name == '') {
				jQuery("#regForm input[name='name']").focus();
				$(".error span").html("Please enter name.");
				$(".error").attr('style','display:flex;');
				/*$('html, body').animate({
					scrollTop: $(".error").offset().top
				}, 1000);*/
				setTimeout(function() {$(".error").hide(500)}, 12000);
				return false;
			}

			if(password == '') {
				jQuery("#regForm input[name='password']").focus();
				$(".error span").html("Please enter password.");
				$(".error").attr('style','display:flex;');
				/*$('html, body').animate({
					scrollTop: $(".error").offset().top
				}, 1000);*/
				setTimeout(function() {$(".error").hide(500)}, 12000);
				return false;
			}

			if(password.length<7) {
				jQuery("#regForm input[name='password']").focus();
				$(".warning span").html("Password should be atleast 7 characters.");
				$(".warning").attr('style','display:flex;');
				/*$('html, body').animate({
					scrollTop: $(".warning").offset().top
				}, 1000);*/
				setTimeout(function() {$(".warning").hide(500)}, 12000);
                return false;
            }
			jQuery(".mainLoader").show();
			$.ajax({
				type: "POST",
				url: "<?=SITEURL;?>ajax/userRegistration.php",
				data: "emailAdress="+email+"&password="+password+"&name="+name,
				success: function(regResponse) {
					regResponse = JSON.parse(regResponse);
					jQuery(".mainLoader").hide();
					if(regResponse.Status == 'success') {
						jQuery('#regForm')[0].reset();
						$(".success span").html(regResponse.Message);
						$(".success").attr('style','display:flex;');
						/*$('html, body').animate({
							scrollTop: $(".success").offset().top
						}, 1000);*/
						setTimeout(function() {$(".success").hide(500);window.location.href = regResponse.redirectUrl;}, 12000);
						return false;
					}
					else {
						$(".error span").html("Oops... ! "+regResponse.Message);
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