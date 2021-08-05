<footer class="footer">

    <div class="container">

        <div class="footer-logo text-center mb-5">

            <a href="<?=SITEURL;?>">
            <picture>
  <source srcset="<?=SITEURL;?>images/footer-logo.webp" type="image/webp">
  <source srcset="<?=SITEURL;?>images/footer-logo.png" type="image/png">
<img class="img-fluid" src="<?=SITEURL;?>images/footer-logo.webp" width="197" height="123">
</picture>
            </a>

        </div>

        <div class="footer-lists">

            <ul>

                <li><a href="<?=SITEURL;?>" >Home</a></li>

                <li><a href="<?=SITEURL;?>about" >About Us</a></li>
                
				<li><a href="<?=SITEURL;?>blog" >Blog</a></li>
                
                <li><a href="<?=SITEURL;?>faq">F.A.Q</a></li>

                <li><a href="<?=SITEURL;?>contact-us">Contact Us</a></li> 
                <?php
				if(isset($_SESSION['userId']) && !empty($_SESSION['userId'])) {
				?>
                <li><a href="<?=SITEURL;?>profile.php">Profile</a></li>
                <?php } else { ?>
                <li><a href="<?=SITEURL;?>login.php">Login</a></li>

                <li><a href="<?=SITEURL;?>signup.php">Sign Up</a></li>
                <?php } ?>
                <li><a href="<?=SITEURL;?>terms">Terms & Conditions</a></li>

                <li><a href="<?=SITEURL;?>privacy-policy">Privacy Policy</a></li>
                <li><a href="<?=SITEURL;?>bug-report.php">Bug Report</a></li>

            </ul>

        </div>
        <div class="footer_telphone"><i class="fa fa-phone-square"></i><a href="tel:(919) 438-0035">(919) 438-0035</a></div>

        <div class="socket mt-4">

            <div class="social-links mb-3 fdf">

                <a target="_blank" href="https://www.facebook.com/FlashyFlyers"><i class="fab fa-facebook-f"></i></a>
                <!--<a target="_blank" href="https://twitter.com/FlashyFlyers"><i class="fab fa-twitter"></i></a>-->
                <a target="_blank" href="https://www.pinterest.com/flashyflyers/"><i class="fab fa-pinterest-p"></i></a>
<?php /*?>                <a target="_blank" href="https://www.linkedin.com/in/hansel-castillo-75462916a/"><i class="fab fa-linkedin-in"></i></a>
<?php */?>                <a target="_blank" href="https://www.instagram.com/flashyflyers/"><i class="fab fa-instagram"></i></a>

            </div>

            <p id="copyright">Copyright &copy; 2019 - 2020 All rights reserved.</p>

        </div>

    </div>

</footer>

<script>
$('.close-ntf').click(function() {
	$(this).parent().fadeOut(300, function() {
		$(this).hide();
	});
});

</script>

<style>
.textwidget.custom-html-widget {color:#787878 !important;font-size: 18px !important;}
#copyright {color:#787878 !important;font-size: 18px !important;}
</style>




