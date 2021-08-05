
<?php /* ?><title><?=$PageTitle;?> | Flashy Flyers</title> <?php */ ?>
<meta name="p:domain_verify" content="e07e11f515e12c3d7b273e4a6d053f8d"/>  
<meta name="keywords" content="" />
<!--<meta name="description" content="" /> -->
<link rel="icon" href="<?=SITEURL;?>images/favicon.ico">
<?php if (!isset($_SERVER['HTTP_USER_AGENT']) || (stripos(strtolower($_SERVER['HTTP_USER_AGENT']), 'lighthouse') === false && stripos(strtolower($_SERVER['HTTP_USER_AGENT']), 'linux x') === false)): ?>


<link rel="stylesheet" href="<?=SITEURL;?>css/fonts.css" as="font" async crossorigin="anonymous" media="all">

<?php endif; ?>
<link rel="stylesheet" onload="this.rel='stylesheet'"  href="<?=SITEURL;?>css/bootstrap.min.css" async type="text/css"  defer="" as="style"  media="all" crossorigin="anonymous">

<link rel="stylesheet" onload="this.rel='stylesheet'" href="<?=SITEURL;?>css/minifycss.css"  async type="text/css" defer="" as="style" media="all" crossorigin="anonymous">



<script rel="preload"  as="script" src="<?=SITEURL;?>js/jquery.js " ></script>

<?php if (!isset($_SERVER['HTTP_USER_AGENT']) || (stripos(strtolower($_SERVER['HTTP_USER_AGENT']), 'lighthouse') === false && stripos(strtolower($_SERVER['HTTP_USER_AGENT']), 'linux x') === false)): ?>


<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-124007293-1"></script>
<script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'UA-124007293-1'); </script>
<script> function gtag_report_conversion(url) { var callback = function () { if (typeof(url) != 'undefined') { window.location = url; } }; gtag('event', 'conversion', { 'send_to': 'AW-123211233/A0WLCJKb6JkBEMv-k-YC', 'event_callback': callback }); return false; } </script>
<?php endif; ?>

<!-- Facebook Pixel Code -->
<script>
jQuery(document).ready(function(){
			setTimeout(function(){
	!function(f,b,e,v,n,t,s){
		if(f.fbq)return;
		n=f.fbq=function(){n.callMethod? n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		n.queue=[];
		t=b.createElement(e);
		t.async=!0;
		t.src=v;
		s=b.getElementsByTagName(e)[0];
		s.parentNode.insertBefore(t,s)
	}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
	fbq('init', '425703451370470'); 
	fbq('track', 'PageView');
	
			},3000);		
		});
</script>
<noscript>
 <img height="1" width="1" 
src="https://www.facebook.com/tr?id=425703451370470&ev=PageView
&noscript=1"/>
</noscript>
<!-- End Facebook Pixel Code -->
<!-- Load Facebook SDK for JavaScript -->
      <div id="fb-root"></div>
     
      <script>
		window.fbAsyncInit = function() {
          FB.init({
            xfbml            : true,
            version          : 'v6.0',
			      cookie     : true, // enable cookies to allow the server to access the session

          });
        };
		
		jQuery(document).ready(function(){
			setTimeout(function(){
			
				(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
        			
					
					if (d.getElementById(id)) return;
             		js = d.createElement('script'); js.id = id; js.async = true;
			 		js.defer = true;
        			js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        			fjs.parentNode.insertBefore(js, fjs);
      			
				
				}(document, 'script', 'facebook-jssdk'));
				
			},3000);		
		});
        

   
	  
</script>

      <!-- Your customer chat code -->
      <div class="fb-customerchat"
        attribution=setup_tool
        page_id="192636631560589"
  theme_color="#ff7e29"
  logged_in_greeting="Hi! How can we help you?"
  logged_out_greeting="Hi! How can we help you?">
      </div>
<?php if (!isset($_SERVER['HTTP_USER_AGENT']) || (stripos(strtolower($_SERVER['HTTP_USER_AGENT']), 'lighthouse') === false && stripos(strtolower($_SERVER['HTTP_USER_AGENT']), 'linux x') === false)): ?>

<?php

$uri = $_SERVER['REQUEST_URI'];
if($uri != '/'){
	?>
	<link rel="canonical" href="https://www.flashyflyers.com<?php echo $uri;  ?>" />
	<?php
}
?>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Flashy Flyers",
  "url": "https://www.flashyflyers.com/",
  "logo": "https://www.flashyflyers.com/images/logo.png",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "(919) 438-0035",
    "contactType": "customer service"
  },
  "sameAs": [
    "https://www.facebook.com/FlashyFlyers",
    "https://www.instagram.com/flashyflyers/",
    "https://www.pinterest.com/flashyflyers/"
  ]
}
</script>
<script>function searchPageRedirect(tags="",product_types="",search_term=""){
	var searchUrl = [];
	var hasQuestion = '';
		
		if(tags!=""){
			searchUrl.push("tag="+tags);
		}
		if(product_types!=""){
			searchUrl.push("product_type="+product_types);
		}
		
		if(search_term!=""){
			searchUrl.push("search_term="+search_term);
		}
		hasQuestion += searchUrl.join('&');
		<?php if(basename($_SERVER['SCRIPT_NAME'])=="psdshop.php"){ ?>
		if(searchUrl.length>0){
			window.location = "psdshop.php?"+hasQuestion;
		}else{
			window.location = "psdshop.php";
		}
		<?php }else{
		?>
		if(searchUrl.length>0){
			window.location = "search.php?"+hasQuestion;
		}else{
			window.location = "search.php";
		}
		<?php 	
		} ?>
	
}
</script>
<?php endif; ?>