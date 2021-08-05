<?php

	ob_start();

	require_once 'function/constants.php';

	require_once 'function/configClass.php';

	require_once 'function/siteFunctions.php';
	$record=array();
	if(is_login()) {
	   header("location:index.php");
	   exit();
	}
	if(!isset($_GET['reset'])) { die(); }
	else {
		$record=GetSglRcrdOnCndi(RESET, "reset_code='".$_GET['reset']."' and reset=0");
	}
	

?>
<!DOCTYPE html>

<html lang="en">



<head>

    <title>Reset Password | Flashy Flyers</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require_once 'files/headSection.php'; ?>

</head>



<body>



    <?php require_once 'files/headerSection.php'; ?>



    <div class="page-wrap bubble-bg-1 pb-5 pt-3">



        <div class="container">
        
        	<?php if(empty($record)): ?>
            <div class="notification warning" style="display:flex;">
                        <div class="d-flex"><i class="fas fa-bell"></i></div>
						<span>Oops!: Reset code is expired or invalid.</span>
						<button class="close-ntf"><i class="fas fa-times"></i></button>
			</div>
            <?php else: ?>
            <div class="notification warning">
                        <div class="d-flex"><i class="fas fa-bell"></i></div>
						<span>Warning: General message</span>
						<button class="close-ntf"><i class="fas fa-times"></i></button>
			</div>
            <?php endif; ?>
            
            <div class="notification success" <?php if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { echo 'style="display:flex;"'; } ?>	>
                        <div class="d-flex"><i class="fas fa-check"></i></div>
						<span><?php echo $_SESSION['SUCCESS'];  unset($_SESSION['SUCCESS']);?></span>
						<button class="close-ntf"><i class="fas fa-times"></i></button>
					</div>
            
            <div class="notification error">
                        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
						<span>Error: Email not valid</span><button class="close-ntf"><i class="fas fa-times"></i></button>
					</div>


			<?php if(!empty($record)): ?>
            	<form id="resetForm" class="signup-form bx-shadow clearfix p-4">

                <h1 class="text-center udr-heading mb-4">Reset Password</h1>

                <label>New Password</label>

                <input type="password" name="password" class="form-control mb-3">
                
                <label>Confirm Password</label>

                <input type="password" name="confirmPassword" class="form-control mb-3">

                <span></span>

                <button type="submit" class="btn-grad btn-lg float-right mt-4 mb-2">Update</button>

            </form>
            <?php endif; ?>



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
            
			var password = jQuery("#resetForm input[name='password']").val();
			var confirmPassword = jQuery("#resetForm input[name='confirmPassword']").val();
            
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
			if(confirmPassword == '') {
				jQuery("#regForm input[name='confirmPassword']").focus();
				$(".error span").html("Please enter confirm password.");
				$(".error").attr('style','display:flex;');
				/*$('html, body').animate({
					scrollTop: $(".error").offset().top
				}, 1000);*/
				setTimeout(function() {$(".error").hide(500)}, 12000);
				return false;
			}
			
			if(password != confirmPassword) {
				jQuery("#regForm input[name='password']").focus();
				$(".error span").html("Password and confirm password does not match.");
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
            var user="<?=$record['user_id']?>";
			var resetcode="<?=$record['reset_code']?>";
            $.ajax({
                type: "POST",
                url: "<?=SITEURL;?>ajax/update-password.php",
                data: "resetcode="+resetcode+"&user="+user+"&password="+password,
                success: function(regResponse) {
                    regResponse = JSON.parse(regResponse);
                    jQuery(".mainLoader").hide();
                    if(regResponse.Status == 'success') {
						window.location.href='<?=SITEURL?>login.php'; 
                        /*$(".success span").html(regResponse.Message);
						$(".success").attr('style','display:flex;');*/
						/*$('html, body').animate({
							scrollTop: $(".error").offset().top
						}, 1000);*/
						/*setTimeout(function() {
							$(".success").hide(500);
							window.location.href='';  
						}, 12000);*/
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