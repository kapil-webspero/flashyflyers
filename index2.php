<?php
ob_start();

require_once 'function/constants.php';

require_once 'function/configClass.php';

require_once 'function/siteFunctions.php';

$flyersArr = GetMltRcrdsOnCndi(FLYERS, " is_active = '1' ORDER BY -sort_order DESC");

ExecCustomQuery("SET sql_mode = '';");

/* $seasonalsliderQuery = "SELECT p.`id`, p.`Title`, d.`filename` FROM ".PRODUCT." p
         JOIN ".PRODUCT_BANNER." d ON p.`id` = d.`prod_id`
         AND d.`filetype` = 'image' AND d.`set_default_image` = 'yes'
         WHERE p.`Status` = 1 AND p.`is_seasonal` = '1' AND p.Addon='0'
         GROUP BY p.`id`
         LIMIT 10";


 $seasonalSliderList = ExecCustomQuery($seasonalsliderQuery);*/

$seasonalsliderQuery = "`is_seasonal` = '1' AND `Status` = '1' AND `Addon` = '0' order by sort_order DESC LIMIT 0, 10";
$seasonalSliderList = GetMltRcrdsOnCndi(PRODUCT, $seasonalsliderQuery);

$saleOftheweeksliderQuery = "`is_sale_of_the_week` = '1' AND `Status` = '1' AND `Addon` = '0' order by sort_order DESC LIMIT 0, 10";
$saleOftheweekSliderList = GetMltRcrdsOnCndi(PRODUCT, $saleOftheweeksliderQuery);

if(is_login()) {
    $myBookmarks = GetMltRcrdsOnCndiWthOdr(FAVOURITE,"`UserID` = '".$_SESSION['userId']."'", "`id`", "DESC");
    $bookmarks = array();
    foreach($myBookmarks as $myBookmarkList){
        $bookmarks[] = $myBookmarkList['ProductID'];
    }
} else {
    $bookmarks = array();
}

$recentFlayerQuery = "`is_regular` = '1' AND `Status` = '1' AND `Addon` = '0'  order by ID DESC LIMIT 0, 16";
$recentFalyerList = GetMltRcrdsOnCndi(PRODUCT, $recentFlayerQuery);

?>



<!DOCTYPE html>

<html lang="en">

<head>    <link rel="canonical" href="https://www.flashyflyers.com/"/>    <title>FlashyFlyers.com - The Most Preferred Flyer Template Design Shop - Online Flyer Templates PSD | Event, party Flyer Template</title>	<meta name="description" content="FlashyFlyers, animated flyer template design shop, offers excellence in design & service. Make your first impression to last longer! Get hands on 3D animated flyers." />    <meta charset="utf-8">    <meta name="viewport" content="width=device-width, initial-scale=1">    <meta name="google-site-verification" content="Pxj1hSIfIVBjqZf2zbYdrFIN1g1lmDbYYOKEox4T5_Q" />    <?php require_once 'files/headSection.php'; ?>    <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script></head><body class="home"><div id="fb-root"></div><script>(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src='https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.1';fjs.parentNode.insertBefore(js,fjs)}(document,'script','facebook-jssdk'))</script><?php require_once 'files/headerSection.php'; ?><section class="mt-5 pt-5 main-banner">
    <div class="container">
        <div class="newcarousel-wrap">
            <div id="newcarousel">
                <?php
                 foreach($flyersArr as $key => $flyerArr) {
                    if (strpos($flyerArr['image_path'],'res.cloudinary.com') !== false)
                    {
                        $flyerImagePath =  httpToSecure($flyerArr['image_path']);
                    }
                    else
                    {
                        $flyerImagePath = SITEURL . "uploads/flyer/" . $flyerArr['image_path'];
                    }

                    if ( ! empty( $flyerArr['image_path'] )) {
                        ?>
                        <div class="item">
                            <img class="img-fluid"     src="<?php echo $flyerImagePath; ?>">
                        </div>
                        
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="main-banner-wrap bx-shadow">
            <div class="main-banner-content">
                <p class="ml-lg-5">Welcome to Flashy Flyers, the most preferred flyer template design shop.</p>
                <h1 class="udr-heading mt-4">You only have one chance to make <br>a great impression!</h1>
                <div class="shop-btn btn-lg btn-grad mt-4 ml-lg-5"><a   href="search.php">Start Shopping</a></div>
            </div>
        </div>
        <div class="layer-img-2">
            <img src="images/layer-2.png">
        </div>
    </div>
</section>

<section class="hm-section-1 HomeDesktopView">
    <div class="container">
        <div class="hm-col-row d-lg-flex">
            <div class="hm-col-1">
                <div class="hm-1-inner">
                    <p><h4 style="text-transform:uppercase">3D, motion &amp; animated templates now available.</h4></p>
                </div>
            </div>
            <div class="hm-col-2">
                <ul class="tick-list">
                    <li>The best club flyer designs.</li>
                    <li>An easy ordering process.</li>
                    <li>Competitively low prices.</li>
                    <li>We edit the templates for you, free!.</li>
                </ul>
            </div>
            <div class="hm-col-3">

                <h2 class="udr-heading text-lg-right h2" style="font-weight: 700;">Our core philosophy is excellence in design &amp; service.</h2>

            </div>
        </div>
    </div>
</section>
<section class="hm-section-2 HomeDesktopView">
    <div class="container">
        <div class="d-flex align-items-center justify-content-around flex-wrap">
            <div class="col mt-2 mb-2">
                <h2 class="h2" style="font-weight: 700;">2D </h2>
                <p>Our 2D products feature graphics made in flat/single dimension style.</p>
            </div>
            <div class="col mt-2 mb-2">
                <h2 class="h2" style="font-weight: 700;">3D </h2>
                <p>Our 3D products feature graphics containing three-dimensional effects.</p>
            </div>
            <div class="col mt-2 mb-2">
                <h2 class="h2" style="font-weight: 700;">Static</h2>
                <p>Static refers to products produced as images as opposed to video.</p>
            </div>
            <div class="col mt-2 mb-2">
                <h2 class="h2" style="font-weight: 700;">Motion</h2>
                <p>Motion refers to products produced as videos with custom animations.</p>
            </div>
            <?php /*?><div class="col mt-2 mb-2">
                <h1>Animated</h1>
                <p>Animated products are produced as videos with custom animation and sound.</p>
            </div><?php */?>


        </div>



    </div>



</section>



<section class="hm-section-4 HomeDesktopView">



    <div class="container">

        <div class="liner"></div>

        <div class="row">



            <div class="col-md-4 process-box mb-3 text-center">

                <img src="images/grad-check.png" alt="free flyer templates">

                <h2 class="h2" style="font-weight: 700;">1</h2>

                <h3>Search</h3>

                <p>Take time to look through our product gallery; use the provided filters to sort through the collection. Find the product template that best fits your event or brand.</p>

            </div>



            <div class="col-md-4 process-box mb-3 text-center">

                <img src="images/grad-check.png" alt="flyer design templates">

                <h2 class="h2" style="font-weight: 700;">2</h2>

                <h3>Buy</h3>

                <p>Add the template to the shopping cart and proceed to checkout, during checkout you will be directed to fill out some information & upload the photos that will go into your final flyer.</p>

            </div>



            <div class="col-md-4 process-box mb-3 text-center">

                <img src="images/grad-check.png" alt="free event flyer templates">

                <h2 class="h2" style="font-weight: 700;">3</h2>

                <h3>Personalize</h3>

                <p>After receiving your order, we will customize the template you selected by adding your information & photos and send it to you. We offer one round of revision for free.</p>



            </div>

        </div>

    </div>



</section>

<?php if(!empty($seasonalSliderList) && count($seasonalSliderList) > 0){ ?>

    <section class="hm-section-5 pb-5">
        <div class="text-center hm-5-content mb-5 season_block">

            <h2 class="h2" style="font-weight: 700;">Seasonal</h2>

        </div>
        <div class="container">
            <div class="seasonal_slider row" id="seasonal_slider">

                <?php

                foreach($seasonalSliderList as $products) {

                    $getBanners = GetMltRcrdsOnCndi(PRODUCT_BANNER, "`prod_id` = '".$products['id']."' AND `filetype`= 'image' and set_default_facebookimage = 'no'");
                    
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

                    
                    if(count($getBanners)>0) {
                        $galleryImages = "";
                        $j=0;
                        
                        foreach($getBanners as $banners) {
                            
                           // if($j==2){continue;}
                            $j++;
                            
                            if (strpos($banners['filename'],'res.cloudinary.com') !== false)
                            {
                                $galleryImages .= '<li><a onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img data-src="'.httpToSecure($banners['filename']).'" alt="'.$products['Title'].'" class="hompageimage" style="width:100%"></a></li>';
                            }
                            else
                            {
                                $galleryImages .= '<li><a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img data-src="../uploads/products/'.$products['id'].'/'.$banners['filename'].'" class="hompageimage" alt="'.$products['Title'].'" style="width:100%"></a></li>';
                            }
                        }
                    } else {
                        $galleryImages = '<li><a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img src="images/flyer-6.jpg" alt="" style="width:100%"></a></div><div class="item"><a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img src="images/flyer-5.jpg" alt="" style="width:100%"></a></div><div class="item"><a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img src="images/flyer-4.jpg" alt="" style="width:100%"></a></li>';
                    }
                    
                    $mygallery="";
                    if(count($getBanners)>0) {
                        $mygallery=$galleryImages;
                    } 
                    
                    $bookmarkCls = "bookmark-btn";
                    if(in_array($products['id'],$bookmarks)) {
                        $bookmarkCls = "my-bookmark-btn";
                    }
                    
                    $html .= '<div class="seasonal_slider_main_block">

                            <div class="flyer-product mb-3">

                                <div class="flyer-img-wrap">
                                    <div class="flyer-img">';
									
									  $html .= ' <div class="flexslider"><ul class="slides">'.$mygallery.'</ul></div><div class="ProductDetailsInfo">  <h2>'.$products['Title'].'</h2>	
                                    </div>
                                    <div class="flyer-content">

                                        <div class="flyer-product-info">
                                            <h2>'.$products['Title'].'</h2>';
                    if(!empty($products['Description']))
                    {

                      //  $html .= '<p>'.substr($products['Description'],0,100).'</p>';
                    }

                    $html .= '<div class="buy-btns text-center">
                                                <a   onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')"href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Customize</span></a>
                                            </div>
                                            <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>
                                        </div>
									

                                    </div>
                                </div>

                                <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>

                                <div class="buy-btns text-center">
                                    <a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Customize</span></a>
                                </div>
							
</div>

                            </div>
	
                        </div>';


                }
                echo $html;


                ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if(!empty($saleOftheweekSliderList) && count($saleOftheweekSliderList) > 0) { 
	$html = ''; 
	?>

    <section class="hm-section-5 pb-5">
        <div class="text-center hm-5-content mb-5 season_block">

            <h2 class="h2" style="font-weight: 700;">Sale of the week</h2>


        </div>
        <div class="container">
            <div class="sale_week_slider row" id="sale_week_slider">


                <?php
$html = "";
                foreach($saleOftheweekSliderList as $products) {

                    $getBanners = GetMltRcrdsOnCndi(PRODUCT_BANNER, "`prod_id` = '".$products['id']."' AND `filetype`= 'image' and set_default_facebookimage = 'no'");
                   
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

                    
                    if(count($getBanners)>0) {
                        $galleryImages = "";
                        $j=0;
                        
                        foreach($getBanners as $banners) {
                            
                          //  if($j==2){continue;}
                            $j++;
                            
                            if (strpos($banners['filename'],'res.cloudinary.com') !== false)
                            {
                                $galleryImages .= '<li><a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img  alt="'.$products['Title'].'" class="hompageimage" data-src="'.httpToSecure($banners['filename']).'" style="width:100%"></a></li>';
                            }
                            else
                            {
                                $galleryImages .= '<li><a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img  alt="'.$products['Title'].'" class="hompageimage" data-src="../uploads/products/'.$products['id'].'/'.$banners['filename'].'"  alt="" style="width:100%"></a></li>';
                            }
                        }
                    } else {
                        $galleryImages = '<li><a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img src="images/flyer-6.jpg" alt="" style="width:100%"></a></div><div class="item"><a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img src="images/flyer-5.jpg" alt="" style="width:100%"></a></div><div class="item"><a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img src="images/flyer-4.jpg" alt="" style="width:100%"></a></li>';
                    }
                    
                    $mygallery="";
                    if(count($getBanners)>0) {
                        $mygallery=$galleryImages;
                    } 
                    
                    $bookmarkCls = "bookmark-btn";
                    if(in_array($products['id'],$bookmarks)) {
                        $bookmarkCls = "my-bookmark-btn";
                    }
                    
                    $html .= '<div class="seasonal_slider_main_block">

                            <div class="flyer-product mb-3">

                                <div class="flyer-img-wrap">
                                    <div class="flyer-img">';
									
									   $html .= ' <div class="flexslider"><ul class="slides">'.$mygallery.'</ul></div><div class="ProductDetailsInfo">  <h2>'.$products['Title'].'</h2>	
                                    </div>
                                    <div class="flyer-content">

                                        <div class="flyer-product-info">
                                            <h2>'.$products['Title'].'</h2>';
                    if(!empty($products['Description']))
                    {

                       // $html .= '<p>'.substr($products['Description'],0,100).'</p>';
                    }

                    $html .= '<div class="buy-btns text-center">
                                                <a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Customize</span></a>
                                            </div>
                                            <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>
                                        </div>
									

                                    </div>
                                </div>

                                <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>

                                <div class="buy-btns text-center">
                                    <a  onclick="return gtag_report_conversion(\'flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'\')" href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Customize</span></a>
                                </div>
							
</div>

                            </div>

                        </div>';


                }
                echo $html;


                ?>
            </div>
        </div>
    </section>
<?php } ?>
<section class="hm-section-3" id="flyer-templates">

    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="notification" id="warning">
                    <div class="d-flex"><i class="fas fa-bell"></i></div> <span class="msg">Warning : General message</span><button class="close-ntf"><i class="fas fa-times"></i></button></div>

                <div class="notification success" id="success">
                    <div class="d-flex"><i class="fas fa-check"></i></div> <span class="msg">Message sent with success</span><button class="close-ntf"><i class="fas fa-times"></i></button></div>

            </div>
        </div>
        <div class="product-listing-wrap" id="product-listing">


            <?php /*?><form id="filter_form" method="post">

                <div class="row filters" id='filters'>

                    <div class="col-lg-3  d-flex align-items-center justify-content-center">

                        <select name="category" id="productCategory">

                            <option value="" selected>Select product types</option>

                        <?php 
						$cat_assoc_arr = getProductTypesDetails();
$par_cat_array = getParentProductTypesList(0, $old_cat="", $menu_id, 1, 1);
						?>
						<?php
                        for($i=0 , $n=count($par_cat_array) ; $i<$n ; $i++)
                        {									
                            if($par_cat_array[$i]['ID'] == $parent_id)
                                $selected = "selected";
                            else
                                $selected = "";
                            echo "<option value='". $par_cat_array[$i]['ID'] ."' $selected>". $par_cat_array[$i]['path'] ."</option>";
                        }
                        ?>

                        </select>

                    </div>

                    <div class="col-lg-3">

                        <div class="p-lg-0 p-2" style="position:relative;">
                            <input type="text" name="hint_text" class="form-control" id="textfield" placeholder="What are you looking for?">
                            <span id="searchBtn"><i class="fa fa-search"></i></span>
                            <input type="hidden" name="page_id" id="page_id" value="1">
                        </div>

                    </div>

                    <div class="col-lg-2 col-md-4 d-flex align-items-center justify-content-center">

                        <label class="custom-control custom-radio">

                            <input type="radio" name='dimensional' id='2D' value='2D' class="custom-control-input features">

                            <span class="custom-control-indicator"></span>

                            <span class="custom-control-description">2D</span>

                        </label>

                        <label class="custom-control custom-radio">

                            <input type="radio" name='dimensional' id='3D' value='3D' class="custom-control-input features">

                            <span class="custom-control-indicator"></span>

                            <span class="custom-control-description">3D</span>

                        </label>

                    </div>

                    <div class="col-lg-4 col-md-8 d-flex align-items-center justify-content-around">

                        <label class="custom-control custom-checkbox">

                            <input type="checkbox" name='static' id='static' value='static' class="custom-control-input features">

                            <span class="custom-control-indicator"></span>

                            <span class="custom-control-description">Static</span>

                        </label>

                        <label class="custom-control custom-checkbox">

                            <input type="checkbox" name='motion' id='motion' value='motion' class="custom-control-input features">

                            <span class="custom-control-indicator"></span>

                            <span class="custom-control-description">Motion</span>

                        </label>

                    </div>
                </div>
            </form><?php */?>

            <div class="product-listing-inner">
                <div class="row" id="productList">
                	
                <?php
				$html = "";
               
			 foreach($recentFalyerList as $products) {
			$getBanners = GetMltRcrdsOnCndi(PRODUCT_BANNER, "`prod_id` = '".$products['id']."' AND `filetype`= 'image' and set_default_facebookimage = 'no'");
			
			if(count($getBanners)>0) {

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

				$galleryImages = "";
				$j=0;
				foreach($getBanners as $banners) {
					//if($j==2){continue;}
					$j++;
					if (strpos($banners['filename'],'res.cloudinary.com') !== false)
					{
						$galleryImages .= '<li><a   href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img class="hompageimage1" src="'.httpToSecure($banners['filename']).'" alt="'.$products['Title'].'" style="width:100%"></a></li>'; 
					}
					else
					{
					$galleryImages .= '<li><a   href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img class="hompageimage1" src="../uploads/products/'.$products['id'].'/'.$banners['filename'].'" alt="'.$products['Title'].'" style="width:100%"></a></li>'; 
					}
				}
			} else {
				$galleryImages = '<li><a   href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img src="images/flyer-6.jpg" alt="" style="width:100%"></a></div><div class="item"><a href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img src="images/flyer-5.jpg" alt="" style="width:100%"></a></div><div class="item"><a href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'"><img src="images/flyer-4.jpg" alt="" style="width:100%"></a></li>';
			}
			 $mygallery="";
                    if(count($getBanners)>0) {
                        $mygallery=$galleryImages;
                    } 
                    
					
			$bookmarkCls = "bookmark-btn";
			if(in_array($products['id'],$bookmarks)) {
				$bookmarkCls = "my-bookmark-btn";
			}
			
			$html .= '<div class="col-lg-3 col-sm-6">

                            <div class="flyer-product mb-3">

                                <div class="flyer-img-wrap">
                                    <div class="flyer-img">';
									  $html .= ' <div class="flexslider"><ul class="slides">'.$mygallery.'</ul></div><div class="ProductDetailsInfo">  <h2>'.$products['Title'].'</h2>	
                                    </div>
                                    <div class="flyer-content">

                                        <div class="flyer-product-info">
                                            <h2>'.$products['Title'].'</h2>';
											if(!empty($products['Description']))
											{
											
											//	$html .= '<p>'.substr(strip_tags($products['Description']),0,100).'</p>';
											}

                                            $html .= '<div class="buy-btns text-center">
                                                <a   href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Customize</span></a>
                                            </div>
                                            <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>
                                        </div>
										<div style="display:block; width:100%; text-align:center"><div class="fb-share-button" data-href="'.SITEURL.'flyer-details.php?productId='.$products['id'].'" data-layout="button" data-size="large" data-mobile-iframe="true"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fflashyflyers.com%2Fdeveloper%2Fflyer-details.php%3FproductId%3D'.$products['id'].'&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div></div>


                                    </div>
                                </div>

                                <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>

                                <div class="buy-btns text-center">
                                    <a  href="flyer-details.php?productId='.$products['id'].'&title='.urlencode($products['Title']).'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Customize</span></a>
                                </div>

                            </div>

                        </div>
                        </div>';	
			 }
		
                echo $html;


                ?>
                
                </div>
            </div>

            
            <div class="ViewAllTemplates">
            	<a href="search.php">VIEW ALL TEMPLATES</a>
            </div>
        </div>



    </div>



</section>


<section class="hm-section-1 HomeMobileView">
    <div class="container">
        <div class="hm-col-row d-lg-flex">
            <div class="hm-col-1">
                <div class="hm-1-inner">
                    <p><h4 style="text-transform:uppercase">3D, motion &amp; animated templates now available.</h4></p>
                </div>
            </div>
            <div class="hm-col-2">
                <ul class="tick-list">
                    <li>The best club flyer designs.</li>
                    <li>An easy ordering process.</li>
                    <li>Competitively low prices.</li>
                    <li>We edit the templates for you, free!.</li>
                </ul>
            </div>
            <div class="hm-col-3">

                <h2 class="udr-heading text-lg-right h2" style="font-weight: 700;">Our core philosophy is excellence in design &amp; service.</h2>

            </div>
        </div>
    </div>
</section>
<section class="hm-section-2 HomeMobileView">
    <div class="container">
        <div class="d-flex align-items-center justify-content-around flex-wrap">
            <div class="col mt-2 mb-2">
                <h2 class="h2" style="font-weight: 700;">2D </h2>
                <p>Our 2D products feature graphics made in flat/single dimension style.</p>
            </div>
            <div class="col mt-2 mb-2">
                <h2 class="h2" style="font-weight: 700;">3D </h2>
                <p>Our 3D products feature graphics containing three-dimensional effects.</p>
            </div>
            <div class="col mt-2 mb-2">
                <h2 class="h2" style="font-weight: 700;">Static</h2>
                <p>Static refers to products produced as images as opposed to video.</p>
            </div>
            <div class="col mt-2 mb-2">
                <h2 class="h2" style="font-weight: 700;">Motion</h2>
                <p>Motion refers to products produced as videos with custom animations.</p>
            </div>
            <?php /*?><div class="col mt-2 mb-2">
                <h1>Animated</h1>
                <p>Animated products are produced as videos with custom animation and sound.</p>
            </div><?php */?>


        </div>



    </div>



</section>



<section class="hm-section-4 HomeMobileView">



    <div class="container">

        <div class="liner"></div>

        <div class="row">



            <div class="col-md-4 process-box mb-3 text-center">

                <img src="images/grad-check.png" alt="free flyer templates">

                <h2 class="h2" style="font-weight: 700;">1</h2>

                <h3>Search</h3>

                <p>Take time to look through our product gallery; use the provided filters to sort through the collection. Find the product template that best fits your event or brand.</p>

            </div>



            <div class="col-md-4 process-box mb-3 text-center">

                <img src="images/grad-check.png" alt="flyer design templates">

                <h2 class="h2" style="font-weight: 700;">2</h2>

                <h3>Buy</h3>

                <p>Add the template to the shopping cart and proceed to checkout, during checkout you will be directed to fill out some information & upload the photos that will go into your final flyer.</p>

            </div>



            <div class="col-md-4 process-box mb-3 text-center">

                <img src="images/grad-check.png" alt="free event flyer templates">

                <h2 class="h2" style="font-weight: 700;">3</h2>

                <h3>Personalize</h3>

                <p>After receiving your order, we will customize the template you selected by adding your information & photos and send it to you. We offer one round of revision for free.</p>



            </div>

        </div>

    </div>



</section>
<section class="hm-section-5 mt-5">

    <div class="container">

        <div class="text-center hm-5-content mb-5">

            <h2 class="h2" style="font-weight: 700;">About us</h2>

            <p>Flashy flyers is a creative agency that creates powerful and compelling design to help our clients market with outstanding boldness and clarity. We offer templates for flyers and other products and customize them after purchase at lightning speed! <a onclick="return gtag_report_conversion('<?=SITEURL;?>about')"  href="about">read more</a>                    </p>
			<h2>Buy Flyer Templates PSD Online</h2><p>Do you want to buy flyer templates? As the most sought after flyer template design shop, Flashy Flyers offers finest quality templates for our customers. <span id="dots">...</span><span id="more">You can find highly appealing 3D flyer design templates with excellent design and effects. We are deeply passionate about design and you can contact us to inimitable event flyer PSD online. <br/><br/>Our track record as an online flyer templates PSD service provider is unbeatable. We have delivered numerous high impact flyer template PSD solutions for our customers over the years. No matter whether you need club flyer PSD online, personalized 3D flyers for your event or any other types of design solutions, you can seek our expertise.<br/><br/>Our flyer design templates PSD stand tall in terms of quality and appeal. In addition to offering flyer templates, Flashy Flyers provide animated flyers, Facebook covers, Instagram flyers, motion flyers, mixtape covers, Snapchat tickets, event badges, single covers and many more.<br/><br/></span><button style="border: none; background-color: transparent;" onclick="myFunction()" id="myBtn">Read more</button></p>

        </div>

    </div>

</section>

<?php require_once 'files/footerSection.php' ?>

<style>
#more {display: none;}
</style>
<script>
function myFunction() {
  var dots = document.getElementById("dots");
  var moreText = document.getElementById("more");
  var btnText = document.getElementById("myBtn");

  if (dots.style.display === "none") {
    dots.style.display = "inline";
    btnText.innerHTML = "Read more"; 
    moreText.style.display = "none";
  } else {
    dots.style.display = "none";
    btnText.innerHTML = "Read less"; 
    moreText.style.display = "inline";
  }
}
</script>

<?php /*?><!-- Modal -->

<div class="modal fade" id="AnimatedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <video controls id="AnimatedModal-player" width="466">

                    <!-- Video files -->

                    <source src="uploads/products/5/1537645861.mp4" type="video/mp4" size="466">



                    <!-- Fallback for browsers that don't support the <video> element -->

                    <a href="uploads/products/5/1537645861.mp4" download>Download</a>

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="MotionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">

                <video controls id="category-player" width="300">

                    <!-- Video files -->

                    <source src="uploads/products/2/1535976142.mp4" type="video/mp4" size="300">



                    <!-- Fallback for browsers that don't support the <video> element -->

                    <a href="uploads/products/2/1535976142.mp4" download>Download</a>

            </div>

        </div>

    </div>

</div>
<?php */?>


<div id="showprop" class="bx-shadow"></div>

<div id="overlay"></div>



<!----SCRIPTS---------->


<script src="js/bootstrap.min.js" defer></script>



<script src="js/popper.min.js" defer></script>

<script src="js/slick.min.js"></script>

<script src="js/jquery.fCarousel.min.js" defer></script>

<script src="js/script.js" defer></script>

<script src="js/filter.js" defer></script>


<script>

var OpenSite = 0;
function LoadImages(){

	
  var lazyloadImages = document.querySelectorAll("img.hompageimage");    
  var lazyloadThrottleTimeout;
  
  function lazyload () {
    if(lazyloadThrottleTimeout) {
      clearTimeout(lazyloadThrottleTimeout);
    }    
    
    lazyloadThrottleTimeout = setTimeout(function() {
        var scrollTop = window.pageYOffset;
        lazyloadImages.forEach(function(img) {
            if(img.offsetTop < (window.innerHeight + scrollTop)) {
              img.src = img.dataset.src;
              img.classList.remove('hompageimage');
            }
        });
      //  if(lazyloadImages.length == 0) { 
         // document.removeEventListener("scroll", lazyload);
         // window.removeEventListener("resize", lazyload);
         // window.removeEventListener("orientationChange", lazyload);
        //}
    }, 20);
  }
  
  document.addEventListener("scroll", lazyload);
  window.addEventListener("resize", lazyload);
  window.addEventListener("orientationChange", lazyload);
}

  function recro(){if($(window).width()>768){$("#showprop").mouseleave(function(){var content="";$(this).hide(0).html(content)})}
$('#newcarousel').fCarousel({'distance':550,'perspective':0,'centerItem':0,'sepration':550,autoplay:6000,'responsive':{1200:{'separation':350},1024:{'separation':250},960:{'separation':350},600:{'separation':300},320:{'separation':150},0:{'sepration':150}}})}
function startSlickFunction(){var $slideshow=$('#showprop > .flyer-gallery');var ImagePauses=[60,4000,4000,4000];$slideshow.slick({slidesToShow:1,slidesToScroll:1,arrows:!0,autoplay:!0,prevArrow:'<i class="icon-angle-left"></i>',nextArrow:'<i class="icon-angle-right"></i>',initialSlide:2,autoplaySpeed:1,pauseOnHover:!1,pauseOnFocus:!1});$slideshow.on('afterChange',function(event,slick,currentSlide){$slideshow.slick('slickSetOption','autoplaySpeed',4000,!0)})}
function action_bookmark(productid){$.ajax({type:"POST",url:"<?=SITEURL;?>ajax/make-favorite.php",data:"favoriteTo="+productid,success:function(regResponse){regResponse=JSON.parse(regResponse);if(regResponse.status=="error"){$(".Pbook_"+productid).removeClass("my-bookmark-btn");$(".Pbook_"+productid).addClass("bookmark-btn");$("#success").attr('style','display:flex;');$("#success").find("span.msg").html(regResponse.message);$('html, body').animate({scrollTop:$("#success").offset().top},1000);setTimeout(function(){$("#success").hide(500)},12000)}else if(regResponse.status=="success"){$(".Pbook_"+productid).removeClass("bookmark-btn");$(".Pbook_"+productid).addClass("my-bookmark-btn");$("#success").attr('style','display:flex;');$("#success").find("span.msg").html(regResponse.message);$('html, body').animate({scrollTop:$("#success").offset().top},1000);setTimeout(function(){$("#success").hide(500)},12000)}}})}
    <?php /*?>function action_buy(productid) {
           	 $.ajax({
				type: "POST",
				url: "<?=SITEURL;?>ajax/cart-process.php",
				data: "action=add&productID="+productid,
				success: function(regResponse) {
					regResponse = JSON.parse(regResponse);
                    $(".cart-product").html(regResponse.cart_count);
					setTimeout(function(){ window.location.href = "<?=SITEURL;?>cart.php"; }, 3000);
				}
			});
		}<?php */?>
		$(document).ready(function(){
recro()
$(".shop-btn").click(function(){if($(window).width()>600){$("html,body").animate({scrollTop:$("#product-listing").offset().top-20},2000)}else{$("html,body").scrollTop($("#product-listing").offset().top-20)}});/*const category_player=new Plyr('#category-player');*/$('#exampleModal').on('hidden.bs.modal',function(e){category_player.pause()});});
$(document).ready(function(){$("#AnimatedModal").on("hidden.bs.modal",function(){var vid=document.getElementById("AnimatedModal-player");vid.pause()})});jQuery(".seasonal_slider,.sale_week_slider").slick({autoplay:!1,dots:!1,slidesToShow:4,slidesToScroll:1,prevArrow:'<i class="icon-angle-left"></i>',nextArrow:'<i class="icon-angle-right"></i>',centerMode:!1,responsive:[{breakpoint:1024,settings:{slidesToShow:1,slidesToScroll:1,infinite:!1,dots:!1}},{breakpoint:767,settings:{slidesToShow:1,slidesToScroll:1,infinite:!0,dots:!1}},{breakpoint:480,settings:{slidesToShow:1,slidesToScroll:1,dots:!1,}}]})

</script>
	<link rel="stylesheet" href="flexslider/css/flexslider.css" type="text/css" media="screen" />

 <!-- jQuery -->
<script defer src="flexslider/js/jquery.flexslider.js" defer="defer"></script>

  <script type="text/javascript">
  
   jQuery(document).ready(function(){var $slider=jQuery('.flexslider');$slider.flexslider({controlNav:!1,animation:"slide",slideshow:!1,start:function(slider){slider.mouseover(function(){slider.flexslider("next");slider.manualPause=!1});slider.mouseout(function(){slider.manualPause=!0;slider.pause()})}})})
   
document.addEventListener("DOMContentLoaded", LoadImages());

</script>


<style>#productList .col-lg-3{ margin-bottom:30px;}
.product-listing-inner{ border:0px !important;}
</style>


</body>
</html>