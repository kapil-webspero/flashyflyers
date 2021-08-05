<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';
$QUERY_STRING = $_SERVER['QUERY_STRING'];
$QUERY_STRING =explode("productId=",$QUERY_STRING);

$productID = $QUERY_STRING[1];

if(is_numeric($_REQUEST['productId'])){

$product=GetSglRcrdOnCndi(PRODUCT, " Addon = '0' AND id='".$_REQUEST['productId']."'");
	header("location:".SITEURL.'p/'.$product['slug']);
	exit;		
}


$title = $_REQUEST['title'];
$checkProduct = GetNumOfRcrdsOnCndi(PRODUCT, "slug='".$productID."' AND Addon = '0'  AND `Status` = '1'");
if($checkProduct == 0) {
    header("location:".SITEURL);
    exit();
}

$product=GetSglRcrdOnCndi(PRODUCT, " Addon = '0' AND slug='".$productID."'");
$productID = $product['id'];

if(is_login()) {
    $myBookmarks = GetMltRcrdsOnCndiWthOdr(FAVOURITE,"`UserID` = '".$_SESSION['userId']."'", "`id`", "DESC");
    $bookmarks = array();
    foreach($myBookmarks as $myBookmarkList){
        $bookmarks[] = $myBookmarkList['ProductID'];
    }
} else {
    $bookmarks = array();
}

    $price3D = $addonPrices['1'];
    $priceMotion = $addonPrices['2'];
   $priceown_music = $addonPrices['15'];
//$priceFacebookcover=$addonPrices['11'];

$product_addon_id = $product['product_addon_id'];
$isFacebookAddon = 0;
$productAddonList = array();
if(!empty($product_addon_id)) {
    $productAddonList = explode( ",", $product_addon_id );
    if(in_array(FACEBOOK_PRODUCT_ID,$productAddonList)){
        $isFacebookAddon = 1;
    }
}

//if(!empty())
$getAddonsData = GetMltRcrdsOnCndiWthOdr(PRODUCT,"`id` = '11'","`id`", "ASC");
$priceFacebookcover=$getAddonsData[0]['Baseprice'];

$photos=GetMltRcrdsOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$productID."'","filetype","ASC");

foreach($photos as $pd) {
    if($pd['filetype'] == 'image') {
        $shPic = httpToSecure($pd['filename']);
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>	
<?php 	
$uri = $_SERVER['REQUEST_URI'];
	if($productID=='32')
	{
		$page_title="HipHop Mixtape Cover Template | Mixtape Cover Templates | Flashy Flyers";
		$page_meta_description="Buy mixtape cover templates from our online store at a very reasonable cost. Customize templates according to you and get it delivered at your door step.";
	}
	else if($productID=='31')
	{
		$page_title=$product['Title']." | Flashy Flyers";
		$page_meta_description="Are you looking for DJ booking flyer? Find wide selection of flyer at our online store where you can customize them accordingly.";
	}
	else if($productID=='97')
	{
		$page_title="Exquisite Animate 3D Flyer Designs for Church Events";
		$page_meta_description="Get your colorful motion 3D flyer designs from FlashyFlyers today for any Church event or occasion to achieve your mission of calling people for the blessings of God";
	}
	else if($productID=='96')
	{
		$page_title="Add Life to Your Church Events with 3D Flyer Templates";
		$page_meta_description="Customize your motion flyer with FlashyFlyers in the least possible time with easy to use animate 3D flyer templates for upcoming events in the Church";
	}
	else if($productID=='93')
	{
		$page_title="Get Partying or Die Trying with These Mixtape Covers Design!";
		$page_meta_description="With the ultimate dance event look on these mixtape covers design and the stunning women, your friends will have no choice but to agree to come to your party";
	}
	else if($productID=='94')
	{
		$page_title="Get Ready for Some Fun in the Sun with This Mixtape Cover Template!";
		$page_meta_description="Use this amazing mixtape cover template to throw the ultimate party you have been thinking about. Dancing on the beach with your friends never sounded so fun";
	}
	else if($productID=='29')
	{
		$page_title="Get Your Spooky On with These Motion Graphic Flyers!";
		$page_meta_description="With a creepy music in the background and scary visuals, everyone will be intrigued when you invite them to your Halloween party with these Motion Graphic Flyers";
	}
	else if($productID=='91')
	{
		$page_title="Dance the Night Away with Animated Mixtape Covers!";
		$page_meta_description="If you want to see the excitement on people's faces who attend your dance party, then choose these animated mixtape covers and become the talk of the town";
	}
	else if($productID=='84')
	{
		$page_title="DJ Booking Flyer Template | Flashy Flyers | 2D and 3D Booking Flyers";
		$page_meta_description="DJ Booking Flyer Template is ideal to convey a brief message, a limited list of products, the opening of a place or business, service or product, an offer or discount.";
	}
	else if($productID=='82')
	{
		$page_title="DJ Motion Booking Flyer | Flashy Flyers | DJ Motion Booking Flyer";
		$page_meta_description="Make a DJ Motion Booking Flyer that looks stunning and impresses your clients without a designer. We can also deliver it to you same day in just 12 hours.";
	}
	else if($productID=='83')
	{
		$page_title="DJ Booking Animated Flyer | Flashy Flyers | 2D or 3D DJ Booking Flyer";
		$page_meta_description="DJ Booking Animated Flyer: Now impresses your customers with this 2D or 3D DJ Booking Flyer. Now impress your clients or customers with this 2D or 3D DJ Booking Flyer.";
	}
	else if($productID=='81')
	{
		$page_title="DJ Booking Template | Flashy Flyers | Booking Flashy Flyers Templates";
		$page_meta_description="DJ Booking Template comes in many sizes from 8x10in to 12x24in. Select the one that suits you the best. We have all the templates ready.";
	}
	else if($productID=='7')
	{
		$page_title="Entice Everyone with This Labor Day Flyer!";
		$page_meta_description="If you want everyone around to know about how fun and enthralling your Labor Day Party is going to be then choosing this Labor Day Flyer is your best shot";
	}
	else if($productID=='20')
	{
		$page_title="Animated Flyer Template - Spread the Word about Your Halloween Themed Party";
		$page_meta_description="Go ahead and prepare your Halloween party with animated flyer template in vivacious colors by FlashyFlyers ensuring a great event sure to be remembered by one and all";
	}
	else if($productID=='95')
	{
		$page_title="Call Upon God with These Motion Graphic Flyers!";
		$page_meta_description="The perfect multi-colored and customizable Motion Graphic Flyers to be used as Church Flyer to call people towards God so that you can be loved by Him";
	}
	else if($productID=='9')
	{
		$page_title="Get Your Party Going with This Labor Day Flyer!";
		$page_meta_description="Want to throw a memorable Labor Day Party to enjoy the amazing holiday? Make use of Labor Day flyer and let the people talk about you and your party for months";
	}
	else if($productID=='69')
	{
		$page_title="White Party Flyer Template | Flashy Flyers | Fancy Graphic Flyers";
		$page_meta_description="White Party Flyer Template are amazingly designed poster having all the required fancy graphics will let your guest know how thematic the party has been arranged.";
	}
	else if($productID=='72')
	{
		$page_title="Hookah Night Template| Flashy Flyers | 2D and 3D Hookah Night Flyer";
		$page_meta_description="Make hookah party more exciting by adding our Hookah night template. This flyer comes in fancy colors, contemporary and modern graphics work.";
	}
	else if($productID=='10')
	{
		$page_title="Single v/s Taken Flyer | Flashy Flyers | 3D Party Flyers";
		$page_meta_description="Single v/s Taken Flyer by Flash flyer is specially designed for the theme parties having both couples and groups of single.";
	}
	else if($productID=='70')
	{
		$page_title="Hookah Flyer Template | Night Party Flyers | Flashy Flyers";
		$page_meta_description="Hookah Flyer Template by flash flyer has some interesting deal for you showing fancy graphics and details designed creatively.";
	}
	else if($productID=='18')
	{
		$page_title="Birthday Bash Flyer | Animated Flashy Flyers | Flashy Flyers";
		$page_meta_description="Birthday Bash Flashy flyer is the ultimate charmer of your birthday party. It comes in fancy colors and graphics which are designed for birthday theme.";
	}
	else if($productID=='2')
	{
		$page_title="Birthday Bash Template | Birthday Theme Flyer | Flashy Flyers";
		$page_meta_description="Birthday Bash Template by Flashy Flyers comes in different sizes and funky colors which creates a whole different ambiance when reflected in the light.";
	}
	else if($productID=='42')
	{
		$page_title="Tropical Saturday Template | Motion Theme Flyer | Flashy Flyers";
		$page_meta_description="Tropical Saturday Flyer : When you are already in a mood of throwing a party for Saturday you can add a charm to your party by having a Tropical Saturday Flyer.";
	}
	else if($productID=='73')
	{
		$page_title="Glow in the dark Party Flyer | Party Motion Flashy Flyer | Flashy Flyers";
		$page_meta_description="The Glow in the dark party flyer is majorly used for the theme based party that has radium colors and decorations which glitters and radiates at night.";
	}
	else if($productID=='38')
	{
		$page_title="Ladies Night Out Flyer Template | Ladies Party Flyer | Flashy Flyers";
		$page_meta_description="Ladies Night Out Flyer Template is an ideal wall decor which can make your slumber party more happening and amazing. The flyer is available in different size.";
	}
	else if($productID=='68')
	{
		$page_title="Foam Party Template | Water Theme Flyer | Flashy Flyers";
		$page_meta_description="When you are up to adding some extra punch in your party by adding foam and water theme you need to have a Foam Party Flyer.";
	}
	else if($productID=='74')
	{
		$page_title="Customizable Retro Party Template Online | Flashy Flyers";
		$page_meta_description="Buy Retro Party online at promotional prices. Flashy flyers is the one stop solution for quality templates for flyers and other products.";
	}
	else if($productID=='75')
	{
		$page_title="Customizable Spring Break Flyer Template Online | Flashy Flyers";
		$page_meta_description="Buy Spring Break Flyer Template online at promotional prices. Flashy flyers is the one stop solution for quality templates for flyers and other products.";
	}
	else if($productID=='79')
	{
		$page_title="Customizable 5 De Mayo Flyer Template Online | Flashy Flyers";
		$page_meta_description="Buy 5 De Mayo Flyer online at promotional prices. Flashy flyers is the one stop solution for quality templates for flyers and other products.";
	}
	else if($productID=='8')
	{
		$page_title="Customizable Labor Day Flyer Template Online | Flashy Flyers";
		$page_meta_description="Buy Labor Day Flyer online at promotional prices. Flashy flyers is the one stop solution for quality templates for flyers and other products.";
	}
	else if($productID=='80')
	{
		$page_title="Cinco De Mayo Template for Sale | Flashy Flyers";
		$page_meta_description="Cinco De Mayo Flyers available online at fair prices. Flashy flyers offer templates for flyers and other products and modify them after purchase in no time!";
	}
	else if($productID=='85')
	{
		$page_title="Buy Memorial Day Weekend Template Online | Flashy Flyers";
		$page_meta_description="Memorial Day Weekend Flyer available online at fair prices. Flashy flyers offer templates for flyers and other products and modify them after purchase in no time!";
	}
	else if($productID=='86')
	{
		$page_title="Buy 4th of July Party Template Online | Flashy Flyers";
		$page_meta_description="4th of July Party Flyers available online at fair prices. Flashy flyers offer templates for flyers and other products and modify them after purchase in no time!";
	}
	else if($productID=='87')
	{
		$page_title="Buy 4th of July Party v2 Flyer Online | Flashy Flyers";
		$page_meta_description="4th of July Party v2 Flyers available online at fair prices. Flashy flyers offer templates for flyers and other products and modify them after purchase in no time!";
	}
	else if($productID=='88')
	{
		$page_title="Magnificent 3d Flyer Design to Awe-Inspire Karaoke Enthusiasts";
		$page_meta_description="FlashyFlyers is the name of inspiring 3d flyer design templates to throw fun filled parties, be it Karaoke Night or any other sort of event at your club";
	}
	else if($productID=='89')
	{
		$page_title="Exquisite Karaoke Night Animated Flyer Template to Get Indulged in Music";
		$page_meta_description="Dial FlashyFlyers customer care number and get your completely personalized Karaoke Night Animated Flyer Template today to get indulged in music to have real fun";
	}
	else if($productID=='90')
	{
		$page_title="Buy Karaoke Nights Flyer Templates for Sale | Flashy Flyers";
		$page_meta_description="Karaoke Night Flyer Template available online at fair prices. Flashy flyers offer templates for flyers and other products and modify them after purchase in no time!";
	}
	else if($productID=='104')
	{
		$page_title="Exceptional Motion Flyer Templates for DJ Booking";
		$page_meta_description="Look no further than FlashyFlyers whenever you need any sort of motion flyer templates for DJ Booking. Grab one of our best DJ Flyers to inspire your audience";
	}
	else if($productID=='106')
	{
		$page_title="The Perfect Motion Graphic Flyers to Throw Your Next Gay Party";
		$page_meta_description="FlashyFlyers is the right place to get motion graphic flyers for your imminent gay party. We’ve a team of pros ready to assist you attract more members of your community";
	}
	else if($productID=='103')
	{
		$page_title="An Inciting Animated Flyer Template for Your Cocktail Party";
		$page_meta_description="FlashyFlyers bring amazing collection of animated flyer templates and 3D animated flyers for summer cocktail parties and summer camps to promote the events";
	}
	else if($productID=='102')
	{
		$page_title="Extraordinary 3d Flyer Design for Your Super Bowl Party";
		$page_meta_description="Time to start promoting the menu of Super Bowl Sunday Party with completely customizable 3d flyer design prepared by the expert designers of FlashyFlyers";
	}
	else if($productID=='101')
	{
		$page_title="Tempt the Taco Lovers with 3d Flyer Templates";
		$page_meta_description="When it is Taco Time, entice the Taco lovers in Mexicano style while making use of trendy animated 3d flyer templates created by the designers of FlashyFlyers";
	}
	else if($productID=='99')
	{
		$page_title="Enthuse the Super Bowl Lovers with Free Club Flyer Design";
		$page_meta_description="FlashyFlyers brings free club flyer design to make your Super Bowl party the most celebrated and your club as one of the best clubs to offer such incredible parties";
	}
	else if($productID=='89')
	{
		$page_title="Exquisite Karaoke Night Animated Flyer Template to Get Indulged in Music";
		$page_meta_description="Dial FlashyFlyers customer care number and get your completely personalized Karaoke Night Animated Flyer Template today to get indulged in music to have real fun";
	}
	else if($productID=='88')
	{
		$page_title="Magnificent 3d Flyer Design to Awe-Inspire Karaoke Enthusiasts";
		$page_meta_description="FlashyFlyers is the name of inspiring 3d flyer design templates to throw fun filled parties, be it Karaoke Night or any other sort of event at your club";
	}
	else if($productID=='105')
	{
		$page_title="Stir up Souls to Get Indulged in the Gay Pride Party with Motion Graphic Flyers";
		$page_meta_description="When it’s time to express your affection for your much-loved ones and make them join you at gay party, get hands on motion graphic flyers designed by FlashyFlyers";
	}
	else if($productID=='98')
	{
		$page_title="The Most Distinct Free Club Flyer Design for Baseball Game Template";
		$page_meta_description="Invite the baseball fans to enjoy the lifetime thrill of the game day while making use of free club flyer design template and be their generous and hospitable host";
	} 
	else
	{
		$page_title=$product['Title']." | Flashy Flyers";
		$page_meta_description="We are creative agency that offer templates for flyers and other products. Visit our website and choose templates from our wide selection.";
	}
?>

    <title><?php echo $page_title; ?></title>
	<meta name="description" content="<?php echo $page_meta_description; ?>" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require_once 'files/headSection.php'; ?>
    <!-- You can use Open Graph tags to customize link previews.
    Learn more: https://developers.facebook.com/docs/sharing/webmasters -->
      <meta property="og:url"           content="https://www.flashyflyers.com<?php echo $uri; ?>" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="<?=$product['Title'];?> | Flashy Flyers" />
    <meta property="og:description"   content="<?=$product['Title'];?>" />
    <meta property="og:image"         content="<?=$shPic;?>" />
    <link rel="stylesheet" href="<?=SITEURL;?>css/jquery.artarax.rating.star.css">
</head>

<body>

<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.1';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<?php require_once 'files/headerSection.php';
$product=array();


if(isset($_REQUEST['productId'])) {
    $product=GetSglRcrdOnCndi(PRODUCT, "id=".$productID);
	$photos=GetMltRcrdsOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$productID."'","filetype","ASC");
    
	/* Array Sorting */    
	foreach($photos as $photoKey => $photo){

		if($photo['filetype'] == 'image'){
	
			//list($width, $height) = getimagesize($photo['filename']);
			
			$raw = rangerServerUrlGetImage($photo['filename']);
			$im = imagecreatefromstring($raw);
			
			$width = imagesx($im);
			$height = imagesy($im);

			
			if($width < $height){
				$photos[$photoKey]['sort_order'] = 1;
			}
			if($width == $height){
				$photos[$photoKey]['sort_order'] = 2;
			}

			if($width > $height){
				$photos[$photoKey]['sort_order'] = 3;
			}
		}else{
			$photos[$photoKey]['sort_order'] = 4;
		}
	}
	
	
	usort($photos,"sortArrayByColumnValue");		

	/* Array Sorting */    
	
	if(isset($_SESSION['CART'][$_REQUEST['productId']]) && !empty($_SESSION['CART'][$_REQUEST['productId']])) {
        $preSelection = $_SESSION['CART'][$productID];
    }

    ?>
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
    
    <main class="bubble-bg-2">
        <div class="container">
            <div class="row justify-content-center mt-5 mb-5">
                <?php if($checkProduct>0) { ?>
                <h1 class="udr-heading mb-4 mobile_product_title">
                                <?php
                                echo $product['Title'];
                                //echo $_GET['title'];
                                ?>
                            </h1>
                    <div class="col-lg-5 col-md-7 pl-lg-4 pr-lg-4">
                        <?php if(!empty($photos)) { ?>

                            <div class="product-slider flyer-details-slider product-lider-wrap">
                                <?php foreach($photos as $p):
                                    if ($p['type']!="animated" && $p['set_default_facebookimage']!="yes" && ($p['filetype'] == 'image' || $p['filetype'] == 'video'))
                                    {
                                        ?>
                                        <div class="item">
                                            <?php
                                            $bookmarkCls = "bookmark-btn";
                                            if(in_array($productID,$bookmarks)) {
                                                $bookmarkCls = "my-bookmark-btn";
                                            }
                                            ?>
                                            <div class="flyer-product">
                                                <div class="flyer-img">
                                                    <a class="Pbook_<?=$productID;?> <?=$bookmarkCls?> pr-det-book" onClick="return action_bookmark(<?=$productID;?>);"><i class="far fa-heart"></i></a>
                                                    <?php if($p['filetype'] == 'image') {


                                                        if (strpos($p['filename'],'res.cloudinary.com') !== false){
                                                            ?>
                                                            <img src="<?=httpToSecure($p['filename'])?>" alt="">
                                                            <?php
                                                        }
                                                        else{
                                                            ?>
                                                            <img src="<?php echo SITEURL; ?>uploads/products/<?=$productID;?>/<?=$p['filename']?>" alt="">
                                                        <?php } }  else if($p['filetype'] == 'video' && $p['type']=="motion") {
                                                     	  $filename = $p['filename'];
															$filenameExt = pathinfo($p['original_name'], PATHINFO_EXTENSION);
                                                            $file_arr = explode('v=',$filename);
                                                           if (strpos($p['original_name'], 'v=') !== false) {
															   $youtubeUrl = str_replace("https://www.youtube.com/watch?v=","",$p['original_name']);
														   }else{
															  	$youtubeUrl = $p['original_name']; 
															  }
                                                            
														    if(empty($filenameExt)){
													   // if (strpos($p['filename'],'youtube') !== false)
                                                       // {
														  
															?>
                                                            <div class="videoYoutube">
															<iframe allowfullscreen="true"
                                                                    allowscriptaccess="always"
                                                                    frameborder="0"
                                                                    height="<?=PRODUCT_VIDEO_HEIGHT;?>"
                                                                    width="<?=PRODUCT_VIDEO_WIDTH;?>"
                                                                    scrolling="no"
                                                                    src="https://www.youtube.com/embed/<?php echo $youtubeUrl; ?>?controls=1">
                                                            </iframe>
                                                            </div>
                                                            <!--<div class="player" height="<?=PRODUCT_VIDEO_HEIGHT;?>"
                                                                 width="<?=PRODUCT_VIDEO_WIDTH;?>" data-plyr-provider="youtube" data-plyr-embed-id="<?php echo $p['filename']; ?>" id="player"></div> -->
                                                           <!-- <iframe allowfullscreen="true"
                                                                    allowscriptaccess="always"
                                                                    frameborder="0"
                                                                    height="<?/*=PRODUCT_VIDEO_HEIGHT;*/?>"
                                                                    width="<?/*=PRODUCT_VIDEO_WIDTH;*/?>"
                                                                    scrolling="no"
                                                                    src="https://www.youtube.com/embed/<?php /*echo $file_arr[1]; */?>?controls=1">
                                                            </iframe>
															-->
                                                            <?php
                                                        }else {
                                                            ?>
                                                            <video controls poster="<?php echo SITEURL; ?>uploads/products/<?=$productID;?>/<?=$p['filename']?>" class="player" id="player" >
                                                                <source src="<?php echo SITEURL; ?>uploads/products/<?php  echo $productID;?>/<?php echo $p['filename']?>" type="video/mp4" size="576">
                                                                <a href="<?php echo SITEURL; ?>uploads/products/<?php echo $productID;?>/<?php echo $p['filename']?>" download>Download</a>
                                                            </video>
                                                            <?php
                                                        }
                                                        ?>
                                                        <!--<div class="playpause"></div>-->


                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } endforeach; ?>
                            </div>

                        <?php } ?>

                        <?php if(!empty($photos)) { ?>
                            <div class="product-slider-thumb">
                                <?php 
								foreach($photos as $p):
                                    if ($p['type']!="animated"  && $p['set_default_facebookimage']!="yes")
                                    {
                                        ?>
                                        <div class="itel video_icon">

                                            <?php if($p['filetype'] == 'image') {

                                                if (strpos($p['filename'],'res.cloudinary.com') !== false)
                                                {
                                                    ?>
                                                    <img src="<?=httpToSecure($p['filename'])?>" alt="" class="wid100">
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <img src="<?php echo SITEURL; ?>uploads/products/<?=$productID;?>/<?=$p['filename']?>" alt="" class="wid100">
                                                    <?php
                                                }
                                            }   else if($p['filetype'] == 'video' && $p['type']=="motion") { 
											
											
														$filenameExt = pathinfo($p['original_name'], PATHINFO_EXTENSION);
                                                            $file_arr = explode('v=',$filename);
                                                           if (strpos($p['original_name'], 'v=') !== false) {
															   $youtubeUrl = str_replace("v=","",$p['original_name']);
														   }else{
															  	$youtubeUrl = $p['original_name']; 
															  }
															  
											
                                                            if(empty($filenameExt)){
													?>
                                                    <div class="youtubeIcon">
                                                   <img src="<?=str_replace("http://","https://",$photos[0]['filename']);?>" alt="" class='video-thumb-detail'>
                                                    </div>
                                                    <?php 				
															}else{
											?>
                                                <!-- <img src="images/thumb-4.jpg" alt=""> -->
                                                <img src="<?=str_replace("http://","https://",$photos[0]['filename']);?>" alt="" class='video-thumb-detail'>
                                                <!--<i class="far fa-play-circle"></i> -->
                                                <img src="<?=SITEURL;?>/images/video_ico.png" class="video-icon">
                                            <?php } } ?>
                                        </div>
                                    <?php } endforeach; ?>
                            </div>
                        <?php } ?>
							<?php if($product['Tags']!=""){
								$tagsLists =   getProductTagsByTags($product['Tags']);
								if(!empty($tagsLists )){
									
									
								 ?>
						<div class="productDetailsTags productDetailsTagsDesktop">
                        <h3>Tags</h3>
                        	<ul>
                            	<?php foreach($tagsLists  as $tag){ ?>
                            	<li><a href="<?php echo SITEURL ?>search.php?tag=<?php echo $tag['Id']; ?>"><?php echo $tag['TagName']; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>	
                        <?php } } ?>
                    </div>
                    <div class="col-lg-7 col-md-12">
                        <div class="product-summary bx-shadow pl-5 pr-5 pt-4 pb-5">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a   href="<?=SITEURL?>">Home</a></li>
                                <li class="breadcrumb-item active"><?=$product['Title']?></li>
                            </ol>
                            <h1 class="udr-heading mb-4 desktop_product_title">
                                <?php
                                echo $product['Title'];
                                //echo $_GET['title'];
                                ?>
                            </h1>
                            
                            
                            <?php 
							if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'android')) {
						?>
							
                            <form style="margin-bottom:30px;" method="post" name="addon_form" style="margin-top:20px">
                                <div class="product-options p-4">
                                    <div class="d-block mb-3">
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="flyer_dimension" <?=($preSelection['dimensional']=="2D" || empty($preSelection['dimensional'])) ? 'checked':''?> class="custom-control-input dimensionalCheck" value="2D">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">2D</span>
                                        </label>
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="flyer_dimension" <?=($preSelection['dimensional']=="3D") ? 'checked':''?> class="custom-control-input dimensionalCheck" value="3D">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description 3d-desc">3D <strong> (+$<?php echo(formatPrice($price3D)); ?>)</strong></span>
                                        </label>
                                    </div>
                                    <div class="d-block mb-3">
                                    
                                    
                                        <label data-toggle="tooltip" title="Hooray!" class="custom-control custom-checkbox">
                                           
                                           
<div class="tooltipStatic">Static
  <span class="tooltiptextStatic tooltipStaticTop">The static version is the default product</span>
</div>
                                            <input type="checkbox" disabled class="custom-control-input typeCheck type_static" <?=(in_array("static",$preSelection['type_banner']) || empty($preSelection['type_banner'])) ? 'checked':''?> value="static" data-amount="0"  name="flyerType[]" checked  data-value="static">
                                            <input type="hidden" name="flyerType[]" value="static"  data-value="static">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description"></span>
                                        </label>
                                        <?php if($product['Motion']==1){ ?>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input typeCheck type_motion" <?=(in_array("motion",$preSelection['type_banner'])) ? 'checked':''?> value="motion" data-amount="<?php echo formatPrice($priceMotion); ?>" name="flyerType[]" data-value="motion">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Motion <strong>(+$<?php echo formatPrice($priceMotion);?>)</strong></span>
                                        </label>
                                        <?php /*?><label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input typeCheck type_animated" <?=(in_array("animated",$preSelection['type_banner'])) ? 'checked':''?> value="animated" data-amount="<?php echo formatPrice($priceAnimation);?>" name="flyerType[]" data-value="animated">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Animated <strong>(+$<?php echo formatPrice($priceAnimation); ?>)</strong></span>
                                        </label><?php */?>

                                        <label class="custom-control custom-checkbox" id="own_music" style="display: <?=(in_array("motion",$preSelection['type_banner'])) ? 'block':'none'?>;">
                                            <input type="checkbox" class="custom-control-input typeCheck TypeOwnMusic" <?=(in_array("Use my music",$preSelection['type_banner'])) ? 'checked':''?> value="Use my music" data-amount="<?=$priceown_music;?>" name="flyerType[]" data-value="own_music">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description" style="color:#00a892">Add music and convert this into an animated flyer? <strong>(+$<?php echo formatPrice($priceown_music);?>)</strong></span>
                                        </label>
                                        <?php } ?>
                                        <?php if($isFacebookAddon == 1 && false){ ?>
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input typeCheck type_fbcover" <?=(in_array("Facebook cover",$preSelection['type_banner'])) ? 'checked':''?> value="Facebook cover" data-amount="<?=$priceFacebookcover;?>" name="flyerType[]" data-value="Facebook-cover">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description">Add matching Facebook cover <strong>(+$<?php echo formatPrice($priceFacebookcover);?>)</strong></span>
                                            </label>
                                        <?php } ?>
                                    </div>
                                    <div class="row no-gutters d-flex justify-content-around flex-wrap">
                                        <div class="col defaultSizeRadio">
                                            <h4>Default size</h4>
                                            <div class="multiselectAddons"> <div class="selectBoxAddons" id="DefaultSizeDP"> <select class="form-control"> <option>Select default size</option> </select> <div class="overSelectAddons"></div></div><div id="checkboxesAddons2" class="checkboxesAddons"><?php
                                            $prodOtherSizes = getProdSizeArr();
                                            $i = 1;
                                            foreach($prodOtherSizes as $key => $vals) {
                                                $prodDefaSize = explode(",",$product['Defaultsizes']); ?>
                                                <?php if(in_array($key,$prodDefaSize)) { ?>
                                                    <label class="custom-control custom-radio">
                                                        <?php if(!empty($preSelection['defaultSize'])){ ?>
                                                            <input data-name="<?=$vals['name'];?>" type="radio" name="defaultSize" value="<?=$key;?>" class="custom-control-input features defaultSize" <?php if((!empty($preSelection['defaultSize']) && $preSelection['defaultSize'] == $key )) { echo "checked"; } ?>>
                                                        <?php }else { ?>
                                                            <input data-name="<?=$vals['name'];?>"  type="radio" name="defaultSize" value="<?=$key;?>" class="custom-control-input features defaultSize" <?php if(in_array($key,$preSelection['defaultSize']) || (empty($preSelection['defaultSize']) || $preSelection['defaultSize']==1  && $i == 1)) { echo "checked"; } ?>>
                                                        <?php } ?>
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-description"><?=$vals['name'];?></span>
                                                    </label>
                                                    <?php $i++; }
                                            } ?> </div></div>
                                            

													 <h4 style="margin-top:20px;">Other sizes</h4>
                                            <div class="multiselectAddons"> <div class="selectBoxAddons" id="otherSizeDP"> <select class="form-control"> <option>Select other sizes</option> </select> <div class="overSelectAddons"></div></div><div id="checkboxesAddons2" class="checkboxesAddons"> <?php
                                            foreach($prodOtherSizes as $key => $vals) {
                                                $prodOtherSize = explode(",",$product['OtherSizes']); ?>
                                                <?php if(in_array($key,$prodOtherSize)) { ?>
                                                    <label class="custom-control custom-checkbox d-block">
                                                        <input type="checkbox" class="custom-control-input otherSizeCheck otherSizes other_<?=$vals['name'];?>" data-name="<?=$vals['name'];?>" <?php if(in_array($key,$preSelection['otherSize'])) { echo "checked"; } ?> value="<?=$key;?>" data-amount="<?=$vals['price'];?>" name="otherSize[]" data-value="<?=$key;?>">
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-description"><?=$vals['name'];?> <strong> (+$<?=formatPrice($vals['price']);?>)</strong></span>
                                                    </label>
                                                <?php }
                                            } ?> </div></div>
                                           
                                            
                                        </div>
                                        <div class="col">
                                           <div id="turnaround-opt">
                                                <h4>Turn around time</h4>
                                                <select name="deliveryTime" onChange="return updateCart();" class="form-control" required>
                                                    <option value="">Select the turn around time</option>
                                                   <option value="3" <?php if($preSelection['deliveryTime']=='3') { echo "selected"; } ?>>2-3 business days(<?php echo ($turnAround3>0) ? "+$". formatPrice($turnAround3):"FREE"; ?>)</option>
                                                   
                                                   <option value="2" <?php if($preSelection['deliveryTime']=='2') { echo "selected"; } ?>>24 hours (<?php echo ($turnAround2>0) ? "$". formatPrice($turnAround2):"FREE"; ?>)</option>
                                                    <option value="1" <?php if($preSelection['deliveryTime']=='1') { echo "selected"; } ?>>12 Hours Same-Day (<?php echo ($turnAround1>0) ? "$". formatPrice($turnAround1):"FREE"; ?>)</option>
                                                    
                                                   <?php /*?> <option value="3" <?php if($preSelection['deliveryTime']=='3') { echo "selected"; } ?>>2-3 business days (+$<?php echo formatPrice($turnAround3);?>)</option><?php */?>
                                                    
                                                  <?php /*?>  <option value="4" <?php if($preSelection['deliveryTime']=='4') { echo "selected"; } ?>>3-5 business days (FREE)</option><?php */?>

                                                </select>
                                            </div>

                                            <?php /*?><label class="custom-control custom-checkbox d-block">
                                        <input type="checkbox" class="custom-control-input otherSizeCheck" <?php if(in_array('8x10in',$preSelection['otherSize'])) { echo "checked"; } ?> value="8x10in" data-amount="20" name="otherSize[]" data-value="8x10in">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">8x10in <strong> ($20)</strong></span>
                                    </label><?php */?>

                                            <input type="hidden" name="product_id" value="<?=$product['id'];?>" />
                                            <input type="hidden" id="totalAmount" value="<?php if(!empty($preSelection['totalPrice'])) { echo $preSelection['totalPrice']; } else { echo $product['Baseprice']; }?>" />
                                            <input type="hidden" id="deliAmount" value="<?php if($preSelection['deliveryTime']=='1') { echo $turnAround1; } elseif($preSelection['deliveryTime']=='2') { echo $turnAround2; } elseif($preSelection['deliveryTime']=='3') { echo $turnAround3; } else {echo "0"; } ?>" />
                                            <input type="hidden" id="dimAmount" value="<?=($preSelection['dimensional']=="3D") ? $price3D:'0'?>" />
                                            <div class="buy-btns mt-4">
                                                <a class="buy-btn addon_buy" ><span class="price" id="totalCartAmount">$<?php if(!empty($preSelection['totalPrice'])) { echo formatPrice($preSelection['totalPrice']); } else { echo formatPrice($product['Baseprice']); }?></span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Add to cart</span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                <?php }
							$charcterDesc = strip_html_tags($product['Description']);
							
							if(str_word_count($charcterDesc)>100){
								echo "<div class='colspanDesc'>".limit_text($charcterDesc,100)."&nbsp;&nbsp;.&nbsp;.&nbsp;.&nbsp;<a class='readMoreDesc'>Read more</a></div>";
								echo "<div class='ReadMoreDesc' style='display:none;'>".$product['Description']."&nbsp;<a style='font-size:19px;' class='readLessDesc'>Read less</a></div>";	
								
							}else{
								echo $product['Description'];	
							}?>
                           
                            <!-- Your share button code -->
                            
                            <div class="ProductDetailsShareButton ProductDetailsShareButtonDesktop">

                                <div class="fb-like" data-href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" data-layout="button" data-action="like" data-size="large" data-show-faces="true" data-share="true"></div>
                            </div>

							<?php 
							
						if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'android')) {}
						else { ?>
                            


                            <form method="post" name="addon_form" style="margin-top:20px">
                                <div class="product-options p-4">
                                    <div class="d-block mb-3">
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="flyer_dimension" <?=($preSelection['dimensional']=="2D" || empty($preSelection['dimensional'])) ? 'checked':''?> class="custom-control-input dimensionalCheck" value="2D">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">2D</span>
                                        </label>
                                        <label class="custom-control custom-radio">
                                            <input type="radio" name="flyer_dimension" <?=($preSelection['dimensional']=="3D") ? 'checked':''?> class="custom-control-input dimensionalCheck" value="3D">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description 3d-desc">3D <strong> (+$<?php echo(formatPrice($price3D)); ?>)</strong></span>
                                        </label>
                                    </div>
                                    <div class="d-block mb-3">
                                    
                                    
                                        <label data-toggle="tooltip" title="Hooray!" class="custom-control custom-checkbox">
                                           
                                           
<div class="tooltipStatic">Static
  <span class="tooltiptextStatic tooltipStaticTop">The static version is the default product</span>
</div>
                                            <input type="checkbox" disabled class="custom-control-input typeCheck type_static" <?=(in_array("static",$preSelection['type_banner']) || empty($preSelection['type_banner'])) ? 'checked':''?> value="static" data-amount="0"  name="flyerType[]" checked  data-value="static">
                                            <input type="hidden" name="flyerType[]" value="static"  data-value="static">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description"></span>
                                        </label>
                                        <?php if($product['Motion']==1){ ?>
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input typeCheck type_motion" <?=(in_array("motion",$preSelection['type_banner'])) ? 'checked':''?> value="motion" data-amount="<?php echo formatPrice($priceMotion); ?>" name="flyerType[]" data-value="motion">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Motion <strong>(+$<?php echo formatPrice($priceMotion);?>)</strong></span>
                                        </label>
                                        <?php /*?><label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input typeCheck type_animated" <?=(in_array("animated",$preSelection['type_banner'])) ? 'checked':''?> value="animated" data-amount="<?php echo formatPrice($priceAnimation);?>" name="flyerType[]" data-value="animated">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">Animated <strong>(+$<?php echo formatPrice($priceAnimation); ?>)</strong></span>
                                        </label><?php */?>
                                        <label class="custom-control custom-checkbox" id="own_music" style="display: <?=(in_array("motion",$preSelection['type_banner'])) ? 'block':'none'?>;">
                                            <input type="checkbox" class="custom-control-input typeCheck TypeOwnMusic" <?=(in_array("Use my music",$preSelection['type_banner'])) ? 'checked':''?> value="Use my music" data-amount="<?=$priceown_music;?>" name="flyerType[]" data-value="own_music">
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description" style="color:#00a892">Add music and convert this into an animated flyer? <strong>(+$<?php echo formatPrice($priceown_music);?>)</strong></span>
                                        </label>
                                        <?php } ?>
                                        <?php if($isFacebookAddon == 1 && false){ ?>
                                            <label class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input typeCheck type_fbcover" <?=(in_array("Facebook cover",$preSelection['type_banner'])) ? 'checked':''?> value="Facebook cover" data-amount="<?=$priceFacebookcover;?>" name="flyerType[]" data-value="Facebook-cover">
                                                <span class="custom-control-indicator"></span>
                                                <span class="custom-control-description">Add matching Facebook cover <strong>(+$<?php echo formatPrice($priceFacebookcover);?>)</strong></span>
                                            </label>
                                        <?php } ?>
                                    </div>
                                    <div class="row no-gutters d-flex justify-content-around flex-wrap">
                                        <div class="col defaultSizeRadio">
                                            <h4>Default size</h4>
                                            <?php
                                            $prodOtherSizes = getProdSizeArr();
                                            $i = 1;
                                            foreach($prodOtherSizes as $key => $vals) {
                                                $prodDefaSize = explode(",",$product['Defaultsizes']); ?>
                                                <?php if(in_array($key,$prodDefaSize)) { ?>
                                                    <label class="custom-control custom-radio">
                                                        <?php if(!empty($preSelection['defaultSize'])){ ?>
                                                            <input type="radio" name="defaultSize" value="<?=$key;?>" class="custom-control-input features defaultSize" <?php if((!empty($preSelection['defaultSize']) && $preSelection['defaultSize'] == $key )) { echo "checked"; } ?>>
                                                        <?php }else { ?>
                                                            <input type="radio" name="defaultSize" value="<?=$key;?>" class="custom-control-input features defaultSize" <?php if(in_array($key,$preSelection['defaultSize']) || (empty($preSelection['defaultSize']) || $preSelection['defaultSize']==1  && $i == 1)) { echo "checked"; } ?>>
                                                        <?php } ?>
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-description"><?=$vals['name'];?></span>
                                                    </label>
                                                    <?php $i++; }
                                            } ?>


                                            <div id="turnaround-opt">
                                                <h4>Turn around time</h4>
                                                <select name="deliveryTime" onChange="return updateCart();" class="form-control" required>
                                                    <option value="">Select the turn around time</option>
                                                   <option value="3" <?php if($preSelection['deliveryTime']=='3') { echo "selected"; } ?>>2-3 business days(<?php echo ($turnAround3>0) ? "+$". formatPrice($turnAround3):"FREE"; ?>)</option>
                                                   
                                                   <option value="2" <?php if($preSelection['deliveryTime']=='2') { echo "selected"; } ?>>24 hours (<?php echo ($turnAround2>0) ? "$". formatPrice($turnAround2):"FREE"; ?>)</option>
                                                    <option value="1" <?php if($preSelection['deliveryTime']=='1') { echo "selected"; } ?>>12 Hours Same-Day (<?php echo ($turnAround1>0) ? "$". formatPrice($turnAround1):"FREE"; ?>)</option>
                                                    
                                                   <?php /*?> <option value="3" <?php if($preSelection['deliveryTime']=='3') { echo "selected"; } ?>>2-3 business days (+$<?php echo formatPrice($turnAround3);?>)</option><?php */?>
                                                    
                                                  <?php /*?>  <option value="4" <?php if($preSelection['deliveryTime']=='4') { echo "selected"; } ?>>3-5 business days (FREE)</option><?php */?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h4>Other sizes</h4>
                                            <?php
                                            foreach($prodOtherSizes as $key => $vals) {
                                                $prodOtherSize = explode(",",$product['OtherSizes']); ?>
                                                <?php if(in_array($key,$prodOtherSize)) { ?>
                                                    <label class="custom-control custom-checkbox d-block">
                                                        <input type="checkbox" class="custom-control-input otherSizeCheck otherSizes other_<?=$vals['name'];?>" <?php if(in_array($key,$preSelection['otherSize'])) { echo "checked"; } ?> value="<?=$key;?>" data-amount="<?=$vals['price'];?>" name="otherSize[]" data-value="<?=$key;?>">
                                                        <span class="custom-control-indicator"></span>
                                                        <span class="custom-control-description"><?=$vals['name'];?> <strong> (+$<?=formatPrice($vals['price']);?>)</strong></span>
                                                    </label>
                                                <?php }
                                            } ?>

                                            <?php /*?><label class="custom-control custom-checkbox d-block">
                                        <input type="checkbox" class="custom-control-input otherSizeCheck" <?php if(in_array('8x10in',$preSelection['otherSize'])) { echo "checked"; } ?> value="8x10in" data-amount="20" name="otherSize[]" data-value="8x10in">
                                        <span class="custom-control-indicator"></span>
                                        <span class="custom-control-description">8x10in <strong> ($20)</strong></span>
                                    </label><?php */?>

                                            <input type="hidden" name="product_id" value="<?=$product['id'];?>" />
                                            <input type="hidden" id="totalAmount" value="<?php if(!empty($preSelection['totalPrice'])) { echo $preSelection['totalPrice']; } else { echo $product['Baseprice']; }?>" />
                                            <input type="hidden" id="deliAmount" value="<?php if($preSelection['deliveryTime']=='1') { echo $turnAround1; } elseif($preSelection['deliveryTime']=='2') { echo $turnAround2; } elseif($preSelection['deliveryTime']=='3') { echo $turnAround3; } else {echo "0"; } ?>" />
                                            <input type="hidden" id="dimAmount" value="<?=($preSelection['dimensional']=="3D") ? $price3D:'0'?>" />
                                            <div class="buy-btns mt-4">
                                                <a class="buy-btn addon_buy" ><span class="price" id="totalCartAmount">$<?php if(!empty($preSelection['totalPrice'])) { echo formatPrice($preSelection['totalPrice']); } else { echo formatPrice($product['Baseprice']); }?></span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Add to cart</span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </form>
                                <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="container_inner default_template_holder" style="text-align:center">
                        <div class="page_not_found">
                            <h2> The product you are looking for is not found </h2>
                            <p> The product you are looking for does not exist. It may have been inactive or removed altogether. Perhaps you can return back to the site's homepage and see if you can find what you are looking for. </p>
                            <div class="separator  transparent center  " style="margin-top:35px;"></div>
                            <p><a  itemprop="url" class="qbutton with-shadow" href="<?=SITEURL;?>"> Back to homepage </a></p>
                            <div class="separator  transparent center  " style="margin-top:35px;"></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php if($product['FullDescription']!=""){  ?>
			<div class="row justify-content-center">
				<div class="col-md-12 fullDescription">
					<?php echo $product['FullDescription']; ?>
				</div>
			</div>
            <?php } ?>
            
            <?php 	if(!empty($tagsLists )){ ?>
            <div class="productDetailsTags productDetailsTagsMobile">
                        <h3>Tags</h3>
                        	<ul>
                            	<?php foreach($tagsLists  as $tag){ ?>
                            	<li><a href="<?php echo SITTE_URL; ?>search.php?tag=<?php echo $tag['Id']; ?>"><?php echo $tag['TagName']; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
            <?php } ?>
            
            <div class="ProductDetailsShareButton ProductDetailsShareButtonMobile">

                                <div class="fb-like" data-href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" data-layout="button" data-action="like" data-size="large" data-show-faces="true" data-share="true"></div>
                            </div>
                            <div class="ProductReview col-lg-12 col-md-12">
            	<input type="hidden" name="page_id_review" id="page_id_review" value="1">
                
            	<input type="hidden" name="LimitProductReviewPerPage" id="LimitProductReviewPerPage" value="10">
              
            	 <?php  
				if($_SESSION['userId']>0 && $_SESSION['userType']=="user"){
				?>
            	<?php /*?><div class="ReviewAddForm col-lg-7 col-md-12">
                
                			<form method="post" class="profile-form pl-md-1 pr-md-1 pb-5" enctype="multipart/form-data" name="form_comment_rating" id="form_comment_rating">
						
                                    <h5>  <label>Add Your Review</label></h5>
                                    <div class="row">
                                    <div class=" col-md-12 ">
                                    	<div class="form-group">
                                    
                                        <div class="rating-star">
                                            <span data-id="100" data-val="1"></span>
                                            <span data-id="100" data-val="2"></span>
                                            <span data-id="100" data-val="3"></span>
                                            <span data-id="100" data-val="4"></span>
                                            <span data-id="100" data-val="5"></span>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                    
                                    <div class="row">
                                    <div class="col-md-12">
                                    	<div class="form-group">
                                        	<label>Write Your  Review<span class="RequiredFiled">*</span></label>
                                             <textarea id="product_review_comment" name="product_review_comment" placeholder="Please write your review..." class="form-control mb-4"></textarea>
                                        </div>
                                        </div>
                                    </div>
                                   
                                   

                                    <input type="hidden" name="product_rating" id="product_rating">

                                    <input type="hidden" name="ReviewProductID" id="ReviewProductID" value="<?php echo $productID; ?>">

                                   

                                    
                                  
                                    <div class="notification error rating-require" style="display: none;">
                                        <div class="d-flex"><i class="fas fa-times-circle"></i><span>Error: Please select rating</span></div>
                                        
                                    </div>
                                    
                                    <div class="notification error rating-require-title" style="display: none;">
                                        <div class="d-flex"><i class="fas fa-times-circle"></i><span>Error: Please enter review title</span></div>
                                        
                                    </div>
                                    
                                    <div class="notification error rating-require-review" style="display: none;">
                                        <div class="d-flex"><i class="fas fa-times-circle"></i><span>Error: Please enter review</span></div>
                                        
                                    </div>
                                    
                                    
                                    <div class="notification error rating-message" style="display: none;">
                                        <div class="d-flex"><i class="fas fa-times-circle"></i><span></span></div>
                                        
                                    </div>
                                    <div class="text-center review_submit">
                                        <button type="submit" name="add_p_comment_rating">Submit</button>
                                    </div>

                                </form>
                  </div><?php */?>
                  <?php } ?>
                
                <div id="customerReviewListing"></div>
                <div id="customerReviewListingPagination"></div>
              
               
            </div>
            
            <?php 
			//related Images
			$getRelatedApproveImages = getRelatedApproveImages($product['id']);
			if(!empty($getRelatedApproveImages)){
			?>
            <div class="getRelatedImages col-lg-12 col-md-12">
              
            		<div class="RelatedImagesSlider" id="RelatedImagesSlider">
                    	<?php foreach($getRelatedApproveImages as $single){ 
								
						?>
                    	<div class="RelatedImagesSliderBlock">
                        
                        <img src="<?=SITEURL;?>timthumb.php?src=<?php echo  SITEURL."uploads/work/".$single; ?>&w=120&h=150&zc=1"></div>
                        <?php } ?>
                                            	
                    </div>	
            </div>
            <?php
			}

            //$related = GetMltRcrdsOnCndi(PRODUCT, "id !=".$product['id']." and Category LIKE '%".$product['Category']."%' ORDER BY id desc LIMIT 4");
            //echo $product['ProductType'];
            $related = GetMltRcrdsOnCndi(PRODUCT, "id !=".$product['id']." and Addon='0' and parent_product_cat_id  ='".$product['parent_product_cat_id']."' ORDER BY id desc LIMIT 4");
			
           if(!empty($related)):
                ?>
                <div class="related-products pt-3 pb-5">
                    <h3>Related products</h3>
                    <div class="rlt-slider">
                        <?php foreach($related as $r) {

                               $getBanners = GetMltRcrdsOnCndi(PRODUCT_BANNER, "`prod_id` = '".$r['id']."' AND `filetype`= 'image' and set_default_facebookimage = 'no'");
                    
                    /* Array Sorting */ 
                   foreach($getBanners as $photoKey => $photo){
                    
					//list($width, $height) = getimagesize($photo['filename']);
					
					$raw = rangerServerUrlGetImage($photo['filename']);
					$im = imagecreatefromstring($raw);
					$width = imagesx($im);
					$height = imagesy($im);
					
					if(!empty($width) && !empty($height)){
						if($width < $height &&  $photo['set_default_image']=="yes"){
							$getBanners[$photoKey]['sort_order'] = 1;
						}else{
							$getBanners[$photoKey]['sort_order'] = 2;
						}
						if($width == $height){
							$getBanners[$photoKey]['sort_order'] = 2;
						}

						if($width > $height){
							$getBanners[$photoKey]['sort_order'] = 3;
						}
					}else{
						if( $photo['set_default_image']=="yes"){
							$getBanners[$photoKey]['sort_order'] = 1;
						}else{
							$getBanners[$photoKey]['sort_order'] = 4;
						}
					}
				}
                    
                    usort($getBanners,"sortArrayByColumnValue");	
						
						
						/* Array Sorting */    
						$getMainBanner = (!empty($getBanners[0]['filename'])) ? $getBanners[0]['filename'] : '';	
                            ?>
                            <div class="item">
                                <div class="flyer-product">
                                    <div class="flyer-img">
                                        <a href="<?=SITEURL.'p/'.$r['slug'];?>">
                                            <?php if($getMainBanner2 != '')
                                            {

                                                if (strpos($getMainBanner2,'res.cloudinary.com') !== false)
                                                {
                                                    ?>
                                                    <img src="<?= httpToSecure($getMainBanner2); ?>" alt="">
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <img src="<?= SITEURL."uploads/products/".$r['id']."/".$getMainBanner; ?>" alt="">

                                                <?php }

                                            }
                                            else{

                                                if (strpos($getMainBanner,'res.cloudinary.com') !== false)
                                                {
                                                    ?>
                                                    <img src="<?= httpToSecure($getMainBanner); ?>" alt="">
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <img src="<?= SITEURL."uploads/products/".$r['id']."/".$getMainBanner; ?>" alt="">
                                                <?php }

                                            }
                                            ?>
                                        </a>
                                    </div>
                                    <?php
                                    $bookmarkCls = "bookmark-btn";
                                    if(in_array($r['id'],$bookmarks)) {
                                        $bookmarkCls = "my-bookmark-btn";
                                    }
                                    ?>
                                    <a class="Pbook_<?=$r['id'];?> <?=$bookmarkCls?>" onClick="return action_bookmark(<?=$r['id'];?>);"><i class="far fa-heart"></i></a>
                                    <div class="buy-btns text-center">
                                        <a class="buy-btn" href="<?=SITEURL;?>p/<?=$r['slug'];?>"><span class="price">$<?=formatPrice($r['Baseprice'])?></span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Customize</span></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                    </div>

                </div>
            <?php endif; ?>
        </div>
    </main>

<?php } ?>

<?php require_once 'files/footerSection.php' ?>
<!----SCRIPTS---------->

<script defer src="<?=SITEURL;?>js/bootstrap.min.js"></script>
<script defer src="<?=SITEURL;?>js/popper.min.js"></script>
<script defer src="<?=SITEURL;?>js/slick.min.js"></script>
<script src="<?=SITEURL;?>js/plyr.min.js"></script>
<script defer src="<?=SITEURL;?>js/script.js"></script>

<script src="<?=SITEURL;?>js/jquery.artarax.rating.star.js"></script>



<script>
otherSizeOptions();
DefaultSizeOptions();
function otherSizeOptions(){
	var OtherSizeText = [];
	 $.each($(".otherSizeCheck:checked"), function(){            

                OtherSizeText.push($(this).attr("data-name"));

            });
			
			if(OtherSizeText.length=="0"){
				
				jQuery("#otherSizeDP select option").html("Select other sizes");	
			}
			if(OtherSizeText.length<=2 && OtherSizeText.length>0){
				jQuery("#otherSizeDP select option").html(OtherSizeText.join(", "));	
			}
			if(OtherSizeText.length>=3){
				jQuery("#otherSizeDP select option").html("selected("+OtherSizeText.length+")");	
			}	
}

function DefaultSizeOptions(){
	var DefaultSizeText = [];
	 $.each($(".defaultSize:checked"), function(){            
				
                DefaultSizeText.push($(this).attr("data-name"));

            });
			
			if(DefaultSizeText.length=="0"){
				
				jQuery("#DefaultSizeDP select option").html("Select default size");	
			}
			if(DefaultSizeText.length<=2 && DefaultSizeText.length>0){
				jQuery("#DefaultSizeDP select option").html(DefaultSizeText.join(", "));	
			}
			if(DefaultSizeText.length>=3){
				jQuery("#DefaultSizeDP select option").html("selected("+DefaultSizeText.length+")");	
			}	
}
function paggedReview(page_id_review){$("#page_id_review").val(page_id_review);customerReviewListingGet()}
jQuery(document).ready(function(e) {
	
  customerReviewListingGet();  
});
function customerReviewListingGet(){
	$(".mainLoader").show();
	var page_id_review = jQuery("#page_id_review").val();
	
	$.ajax({type:"POST",url:"<?=SITEURL;?>ajax/product_review_listing.php",dataType: 'json',   data: "page_id_review="+page_id_review+"&product_id="+'<?php echo $productID; ?>',success:function(regResponse){
		$(".mainLoader").hide();
		
			jQuery("#customerReviewListing").html(regResponse.html);
			jQuery("#customerReviewListingPagination").html(regResponse.pagination);
	}
	
});	
}
    $(function () {
		
        var artaraxRatingStar = $.artaraxRatingStar({
            onClickCallBack: onRatingStar,
        });
        function onRatingStar(rate, id) {
            jQuery("#product_rating").val(rate);
            $(".rating-require").hide();
        }
	
	  jQuery(document).on("submit","#form_comment_rating",function( event ) {
		  event.preventDefault();
		  $(".rating-require,.rating-require-title,.rating-require-review,.rating-message").hide();
		  var ratingdata =true;	
		  if( $("#product_rating").val() == undefined || $("#product_rating").val() == '') {
                event.preventDefault();
                $(".rating-require").show();
				ratingdata = false;
          }
		 
		  
		  if( $("#product_review_comment").val() == undefined || $("#product_review_comment").val() == '') {
                event.preventDefault();
                $(".rating-require-review").show();
				ratingdata = false;
          }
		  if(ratingdata){
			  $(".mainLoader").show();
		  	jQuery.ajax({
			type: 'POST',
			dataType: 'json',
		   url: "<?=SITEURL;?>ajax/product-review-add.php",
			data:{
			
				 'data': jQuery("#form_comment_rating").serialize()
				},
				error: function (jqXHR, exception) { 
				
				},success: function(data){
					
					if(data.status=="success"){
						jQuery(".rating-message").removeClass("error");
						jQuery(".rating-message").addClass("success");
						jQuery(".rating-message").show();
						jQuery(".rating-message .d-flex").html('<i class="fas fa-times-circle"></i><span>'+data.msg+'</span>');	
					}
					if(data.status=="error"){
						jQuery(".rating-message").removeClass("success");
						jQuery(".rating-message").addClass("error");
						jQuery(".rating-message").show();
						jQuery(".rating-message .d-flex").html('<i class="fas fa-times-circle"></i><span>'+data.msg+'</span>');	
					}
					$(".mainLoader").hide();
					
				}
		});	
		  }
			
        });
	
	jQuery(document).on("click",".ReviewListingOverAllWriteReview"	,function(){
	jQuery('html, body').animate({
        scrollTop: jQuery('#form_comment_rating').offset().top - 20 //#DIV_ID is an example. Use the id of your destination on the page
    }, 'slow');
});	
    });

    const players = Plyr.setup('.player');
    $('.product-slider').on('afterChange', function(slick, currentSlide) {
        try {
            players.forEach(function(instance1){
                instance1.pause();
            });
        }catch(e){}

    });

    var total_amount = 0;

    $(".dimensionalCheck").on("change", function(){
        var totalAmount = parseFloat($("#totalAmount").val());
        var dimOldAmt = parseFloat($("#dimAmount").val());
        var dimNewAmt = 0;
        totalAmount = totalAmount - dimOldAmt;
        if($(this).val() == "3D") {
            //$(this).siblings(".custom-control-description").append('<strong> ($<?=($product['Baseprice']+$price3D);?>) </strong>');
            dimNewAmt = <?=$price3D;?>;
        } else {
            //$(".3d-desc").find('strong').remove();
        }
        totalAmount = totalAmount + dimNewAmt;
        $("#dimAmount").val(dimNewAmt);
        $("#totalAmount").val(totalAmount.toFixed(2));
        $("#totalCartAmount").html("$"+totalAmount.toFixed(2));
    });

    $(".typeCheck").on("click", function(){
        var cart_price = $(this).data('amount');
        var totalAmount = parseFloat($("#totalAmount").val());
        var cart_action = "";
        if ($(this).is(':checked')) {
            if($(this).data("value") == "motion") {
                totalAmount = totalAmount + <?=$priceMotion;?>;
            } 
            else if($(this).data("value") == "own_music" && jQuery(".type_motion").is(":checked")) {
                totalAmount = totalAmount + <?=$priceown_music;?>;
            }
           

        } else {
            if($(this).data("value") == "motion") {
				
               
			    totalAmount =  totalAmount - <?=$priceMotion;?>;
				//totalAmount = totalAmount - <?=$priceown_music;?>;
				if(jQuery(".TypeOwnMusic").is(":checked")) {
					 $('.TypeOwnMusic').prop("checked", false);
                	totalAmount = totalAmount - <?=$priceown_music;?>;
            	}
				
				
            } 
            else if($(this).data("value") == "own_music") {
                totalAmount = totalAmount - <?=$priceown_music;?>;
            }
           
        }
		//alert(totalAmount);

        $("#totalAmount").val(totalAmount);
        $("#totalCartAmount").html("$"+totalAmount.toFixed(2));
    });

    $(".otherSizes").on("click", function(){
     	
	    var cart_price = $(this).data('amount');
        var totalAmount = parseFloat($("#totalAmount").val());
        var cart_action = "";
        if ($(this).is(':checked')) {
            totalAmount = totalAmount + cart_price;
        } else {
            totalAmount = totalAmount - cart_price;
        }
		    
        $("#totalAmount").val(totalAmount);
        $("#totalCartAmount").html("$"+totalAmount.toFixed(2));
		otherSizeOptions();
    });



    function updateCart() {
        var totalAmount = parseFloat($("#totalAmount").val());
        var checkDelivery = $('select[name="deliveryTime"]').find(":selected").val();
        var oldAmt = $("#deliAmount").val();
        var newAmt = 0;
        totalAmount = totalAmount - oldAmt;
        if(checkDelivery == "1") {
            newAmt = <?=$turnAround1;?>;
        } else if(checkDelivery == "2") {
            newAmt = <?=$turnAround2;?>;
        } else if(checkDelivery == "3") {
            newAmt = <?=$turnAround3;?>;
        }
        totalAmount = totalAmount + newAmt;
        $("#deliAmount").val(newAmt);
        $("#totalAmount").val(totalAmount);
        $("#totalCartAmount").html("$"+totalAmount.toFixed(2));
    }

    $(".addon_buy").on("click", function() {
        var _this = $(this);
        var targetForm = _this.closest('form');
        var checkDelivery = $('select[name="deliveryTime"]').find(":selected").val();
        if(checkDelivery == "") {
            $(".warning span").html("Please select the turn around time.");
            $(".warning").attr('style','display:flex;');
            $('html, body').animate({
                scrollTop: $(".warning").offset().top
            }, 1000);
            setTimeout(function() {$(".warning").hide(500)}, 12000);
            $('select[name="deliveryTime"]').focus();
            return false
        }
        var formDetail = new FormData(targetForm[0]);
        formDetail.append('action' , "add");
        $.ajax({
            method : 'post',
            url : '<?=SITEURL;?>ajax/cart-upgrade.php',
            data:formDetail,
            cache:false,
            contentType: false,
            processData: false
        }).done(function(regResponse){
            /*console.log(regResponse);*/
            regResponse = JSON.parse(regResponse);
            $(".cart-product").html(regResponse.cart_count);
			
            setTimeout(function(){ window.location.href = "<?=SITEURL;?>cart.php"; }, 1000);
        });
    });

    function action_buy(productid) {
        event.preventDefault();
        var checkDelivery = $('select[name="deliveryTime"]').find(":selected").val();
        if(checkDelivery == "") {
            $(".warning span").html("Please select the turn around time.");
            $(".warning").attr('style','display:flex;');
            $('html, body').animate({
                scrollTop: $(".warning").offset().top
            }, 1000);
            setTimeout(function() {$(".warning").hide(500)}, 12000);
            $('select[name="deliveryTime"]').focus();
            return false
        }
        $.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/cart-process.php",
            data: "action=add&productID="+productid,
            success: function(regResponse) {
                /*console.log(regResponse);*/
                regResponse = JSON.parse(regResponse);
                $(".cart-product").html(regResponse.cart_count);

            }
        });
    }

    function action_bookmark(productid) {
        $.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/make-favorite.php",
            data: "favoriteTo="+productid,
            success: function(regResponse) {
                regResponse = JSON.parse(regResponse);
                if(regResponse.status=="error") {
                    $(".Pbook_"+productid).removeClass("my-bookmark-btn");
                    $(".Pbook_"+productid).addClass("bookmark-btn");
                    $("#success").attr('style','display:flex;');
                    $("#success").find("span.msg").html(regResponse.message);
                    $('html, body').animate({
                        scrollTop: $("#success").offset().top
                    }, 1000);
                    setTimeout(function() {$("#success").hide(500)}, 12000);
                } else if(regResponse.status=="success") {
                    $(".Pbook_"+productid).removeClass("bookmark-btn");
                    $(".Pbook_"+productid).addClass("my-bookmark-btn");
                    $("#success").attr('style','display:flex;');
                    $("#success").find("span.msg").html(regResponse.message);
                    $('html, body').animate({
                        scrollTop: $("#success").offset().top
                    }, 1000);
                    setTimeout(function() {$("#success").hide(500)}, 12000);
                }
            }
        });
    }
    $(".defaultSize").on("click",function(){
        var $thisVal = $(this).val();
        $(".otherSizes").attr("disabled", false);
        if($('.other_'+$thisVal).is(":checked")) {
            $('.other_'+$thisVal).click();
        }
        $('.other_'+$thisVal).attr("disabled", true);
		DefaultSizeOptions();
    });
    $('.type_motion').click(function() {
	
        if ($(this).is(':checked')) {
            $('#own_music').css("display", "block");
            // Do stuff
        }else
        {
            $('#own_music').css("display", "none");
			   $('.TypeOwnMusic').prop("checked", false);
        }

    });
   
   
jQuery(document).on("click",".readMoreDesc",function(e) {
    jQuery(".ReadMoreDesc").show();
	jQuery(".colspanDesc").hide();
	
});	
jQuery(document).on("click",".readLessDesc",function(e) {
    jQuery(".ReadMoreDesc").hide();
	jQuery(".colspanDesc").show();
	
});	

jQuery(document).ready(function(){
  jQuery('[data-toggle="tooltip"]').tooltip();
});
</script>
<style>
.readMoreDesc,.readLessDesc{ color:#007bff !important;    text-decoration: underline !important}
.colspanDesc, .colspanDesc p, .colspanDesc p span, .ReadMoreDesc, .ReadMoreDesc p, .ReadMoreDesc p span {
	font-size: 18px !important;
	font-family: 'Karla', sans-serif !important;
	line-height: 1.7 !important;
	color: rgba(32, 32, 32, 0.8) !important;
}
.fullDescription,.fullDescription p,.fullDescription span{font-size: 18px !important;
	font-family: 'Karla', sans-serif !important;
	line-height: 1.7 !important;
	color: rgba(32, 32, 32, 0.8) !important;}
.tooltipStatic {
  position: relative;
  display: inline-block;
}

.tooltipStatic .tooltiptextStatic {
	visibility: hidden;
	width: 320px;
	background-color: black;
	color: #fff;
	text-align: center;
	border-radius: 6px;
	padding: 5px 0;
	position: absolute;
	z-index: 1;
	left: -41px;
	top: -42px;
}

.tooltipStaticTop::after {
	content: "";
	position: absolute;
	top: 100%;
	left: 20%;
	margin-left: -5px;
	border-width: 5px;
	border-style: solid;
	border-color: #555 transparent transparent transparent;
}

.tooltipStatic:hover .tooltiptextStatic {
  visibility: visible;
}
.related-products .slick-track{ margin-left:0px !important;}
.RelatedImagesSlider .slick-slide img{ width:100%;}

.RelatedImagesSlider .icon-angle-right{
    right: -70px;
}
.RelatedImagesSlider .icon-angle-left{
    left: -70px;
}
.RelatedImagesSlider{ margin-bottom:50px;}
.RelatedImagesSlider .slick-arrow{
    position: absolute;
    top: 50%;
    transform: translate(0px, -50%);
    line-height: 0;
    cursor: pointer;
    z-index: 109;
    font-size: 30px;
    transition: opacity 0.2s ease 0s;
}

.RelatedImagesSlider .slick-arrow::before{
    border-radius: 50%;
    width: 60px;
    height: 60px;
    line-height: 60px;
    background: rgba(0, 255, 242, 1);
    background: -webkit-gradient(left top, right bottom, color-stop(0%, rgba(0, 255, 242, 1)), color-stop(100%, rgba(148, 255, 162, 1)));
    background: -webkit-linear-gradient(-45deg, rgba(0, 255, 242, 1) 0%, rgba(148, 255, 162, 1) 100%);
    background: -o-linear-gradient(-45deg, rgba(0, 255, 242, 1) 0%, rgba(148, 255, 162, 1) 100%);
    background: -webkit-linear-gradient(315deg, rgba(0, 255, 242, 1) 0%, rgba(148, 255, 162, 1) 100%);
    background: -o-linear-gradient(315deg, rgba(0, 255, 242, 1) 0%, rgba(148, 255, 162, 1) 100%);
    background: linear-gradient(135deg, rgba(0, 255, 242, 1) 0%, rgba(148, 255, 162, 1) 100%);
    filter: progid: DXImageTransform.Microsoft.gradient( startColorstr='#00fff2', endColorstr='#94ffa2', GradientType=1);
}
.RelatedImagesSlider .slick-track{ margin:0px !important;}
.getRelatedImages h3{font-size: 1.25rem;
    margin-bottom: 10px;}
@media only screen and (max-width: 640px) {	
	.RelatedImagesSlider .icon-angle-right{    right: -16px;}
	.RelatedImagesSlider .icon-angle-left {
    left: -22px;
}.RelatedImagesSlider .slick-arrow::before {
    width: 40px;
    height: 40px;
    line-height: 38px;
}
	}

</style>
<script>
jQuery(document).ready(function(e) {
    jQuery(".RelatedImagesSlider").slick({autoplay:!1,dots:!1,slidesToShow:8,slidesToScroll:1,prevArrow:'<i class="icon-angle-left"></i>',nextArrow:'<i class="icon-angle-right"></i>',centerMode:!1,responsive:[{breakpoint:1024,settings:{slidesToShow:1,slidesToScroll:1,infinite:!1,dots:!1}},{breakpoint:767,settings:{slidesToShow:1,slidesToScroll:1,infinite:!0,dots:!1}},{breakpoint:480,settings:{slidesToShow:1,slidesToScroll:1,dots:!1,}}]})
});
</script>
</body>
</html>