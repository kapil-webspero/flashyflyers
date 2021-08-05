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
    <title>Login | Flashy Flyers</title>
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
            
            <div class="notification success" <?php if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { echo 'style="display:flex;"'; } ?>	>
                <div class="d-flex"><i class="fas fa-check"></i></div>
                <span><?php echo $_SESSION['SUCCESS'];  unset($_SESSION['SUCCESS']);?></span>
                <button class="close-ntf"><i class="fas fa-times"></i></button>
            </div>
                    
            <form id="loginForm" class="login-form bx-shadow clearfix p-4">
                <h1 class="text-center udr-heading mb-4">Login</h1>
                <label>Email</label>
                <input type="text" name="emailAddress" class="form-control mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <a href="<?=SITEURL;?>forgot-password.php" id="forgot">Forgot Password?</a>
                <button type="submit" class="btn-grad btn-lg float-right mt-5 mb-2">Login</button>
            </form>
            <div class="text-center mb-5 mt-5">
                <a href="<?=SITEURL;?>social/facebook.php" class="btn-fb">Login with facebook</a>
            </div>
        </div>
    </div>
    <?php require_once 'files/footerSection.php'; ?>
    
    <!----SCRIPTS---------->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    
    <script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#loginForm").submit(function(e) {
			e.preventDefault();
			var email = jQuery("#loginForm input[name='emailAddress']").val();
			var password = jQuery("#loginForm input[name='password']").val();
			
			if(email == '') {
				jQuery("#loginForm input[name='emailAddress']").focus();
				$(".error span").html("Please enter email address.");
				$(".error").attr('style','display:flex;');
				/*$('html, body').animate({
					scrollTop: $(".error").offset().top
				}, 1000);*/
				setTimeout(function() {$(".error").hide(500)}, 12000);
				return false;
			}
			
			if(password == '') {
				jQuery("#loginForm input[name='password']").focus();
				$(".error span").html("Please enter password.");
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
				url: "<?=SITEURL;?>ajax/login.php",
				dataType: 'json',
				 'data': jQuery("#loginForm").serialize(),
				success: function(regResponse) {
					
					jQuery(".mainLoader").hide();
					if(regResponse.Status == 'success') {
						window.location.href = regResponse.redirectUrl;
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