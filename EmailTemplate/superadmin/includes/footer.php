<footer class="footer">

        <div class="container">

            <div class="footer-logo text-center mb-5">
                <a href="<?=SITEURL;?>"><img src="<?=SITEURL;?>images/footer-logo.png" class="img-fluid" alt=""></a>
            </div>

            <div class="row">

                <?php /*?><div class="col-md-3 footer-lists mb-4">
                    <h4>More information</h4>
                    <ul>
                        <li><a href="<?=SITEURL;?>about">About Us</a></li>
                        <li><a href="<?=SITEURL;?>blog">Freelancing Blog</a></li>
                        <li><a href="#">Free Flyer Templates</a></li>
                        <li><a href="#">Sitemap</a></li>
                    </ul>
                </div><?php */?>

                <?php /*?><div class="col-md-6 footer-lists categories mb-4">
                    <h4>Categories</h4>
                    <ul>
                        <li><a href="#">All Flyer Templates</a></li>
                        <li><a href="#">Bundles</a></li>
                        <li><a href="#">Business Flyers</a></li>
                        <li><a href="#">Church Flyers</a></li>
                        <li><a href="#">Club Flyers</a></li>
                        <li><a href="#">Community Flyers</a></li>
                        <li><a href="#">Free Flyers</a></li>
                        <li><a href="#">Seasonal Flyers</a></li>
                        <li><a href="#">Special Event Flyers</a></li>
                        <li><a href="#">Sports Flyers</a></li>
                        <li><a href="#">Vintage Flyer Templates</a></li>
                    </ul>
                </div><?php */?>

                <?php /*?><div class="col-md-3 footer-lists mb-4">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">F.A.Qs</a></li>
                        <li><a href="#">Contact Support</a></li>
                        <li><a href="#">File Requirements</a></li>
                        <li><a href="#">File Licenses</a></li>
                        <li><a href="<?=SITEURL;?>privacy-policy">Privacy Policy</a></li>
                        <li><a href="<?=SITEURL;?>terms">Terms of Service</a></li>
                    </ul>

                </div><?php */?>

            </div>
<div class="footer-lists">

            <ul>

                <li><a href="<?=SITEURL;?>">Home</a></li>

                <li><a href="<?=SITEURL;?>about">About Us</a></li>
                
				<li><a href="<?=SITEURL;?>blog">Blog</a></li>
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

            </ul>

        </div>
         <div class="footer_telphone"><i class="fa fa-phone-square"></i><a href="tel:(919) 438-0035">(919) 438-0035</a></div>

            <div class="socket mt-4">

                <div class="social-links mb-3 fdf">

                <a target="_blank" href="https://www.facebook.com/FlashyFlyers"><i class="fab fa-facebook-f"></i></a>
                <?php /*?><a target="_blank" href="https://twitter.com/FlashyFlyers"><i class="fab fa-twitter"></i></a><?php */?>
                <a target="_blank" href="https://www.pinterest.com/flashyflyers/"><i class="fab fa-pinterest-p"></i></a>
<?php /*?>                <a target="_blank" href="https://www.linkedin.com/in/hansel-castillo-75462916a/"><i class="fab fa-linkedin-in"></i></a>
<?php */?>                <a target="_blank" href="https://www.instagram.com/flashyflyers/"><i class="fab fa-instagram"></i></a>

            </div>

                <p id="copyright">Copyright &copy; <?=date('Y');?> All rights reserved.</p>
            </div>

        </div>

    </footer>
    
    <style>
.textwidget.custom-html-widget {color:#787878 !important;font-size: 18px !important;}
#copyright {color:#787878 !important;font-size: 18px !important;}
.social-links a {
    background-color: #ffffff;
    border-radius: 50%;
    display: inline-block;
    font-size: 18px;
    height: 40px;
    line-height: 40px;
    margin-right: 20px;
    text-align: center;
    width: 40px;
}
.social-links a:nth-child(1) {
    border: 2px solid #4260a0;
    color: #3e5b98;
}
.social-links a:nth-child(2) {
    border: 2px solid #4da7de;
    color: #4da7de;
}
.social-links a:nth-child(3) {
    border: 2px solid #ca2027;
    color: #ca2027;
}
.social-links a:nth-child(4) {
    border: 2px solid #0286ba;
    color: #0286ba;
}
.social-links a:nth-child(5) {
    border: 2px solid #da2f65;
    color: #da2f65;
}
.footer-lists ul li {

    list-style-type: none;
    display: inline-block;
    margin-right: 30px;

}
.footer-lists ul li a {

    font-size: 16px;
    color: #787878;
    display: block;

}
.footer-lists ul {

    margin: 0;
    padding: 0;
    display: block;
    text-align: center;

}
#copyright{ margin-bottom:40px;}
</style>