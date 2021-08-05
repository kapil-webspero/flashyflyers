<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';
include(SITE_BASE_PATH."wordpress/wp-config.php");
if($_POST['loginType']=="guest"){
	$_SESSION['loginType'] = "guest";
	$_SESSION['userId'] = "guest";	
	$_SESSION['userType'] ="user";
    header("location:addons.php")	;
    exit();
}


if(is_cart_empty()) {
    header("location:".SITEURL)	;
    exit();
}
if(isset($_SESSION['CartRequest']) && count($_SESSION['CartRequest'])>0){
    header("location:cart.php")	;
    exit();
}


$customeProductCount = 0;
$IsNormalProduct =0;  
$cartArray = array();

foreach($_SESSION['CART'] as $singleCartKey=>$singleCart){
	
if(!empty($singleCart['customeProductFields'])) { 
	$customeProductCount++; 										
}

if(isset($singleCart['customeProductFields']) && !empty($singleCart['customeProductFields'])){
						
	}else if($singleCart['type']!="addon"){
	$cartArray[$singleCartKey] = $singleCart;
	$IsNormalProduct =1;	
	}
}



$cartVals = array_values($cartArray);

if(isset($_REQUEST['product_setup_id']) && !empty($_REQUEST['product_setup_id'])) {
    $productKeys = intval($_REQUEST['product_setup_id']);
} else {
    $productKeys = 0;
}
$productVals = $cartVals[$productKeys];

if(empty($productVals) && isset($_REQUEST['product_setup_id'])) {
    header("location:info.php");
    exit();
}


if($IsNormalProduct==0 && !empty($_SESSION['userId'])){
		header("location:addons.php")	;
		exit(); 
}	

if(!empty($_SESSION['userId'])){
	  header("location:addons.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Info | Flashy Flyers</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once 'files/headSection.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.css" />
    <style>
    .box__input { margin: 0 auto; width: 100%; display: table; text-align: center; position: relative; }
.dropzone {
    background: rgba(0, 0, 0, 0.03) none repeat scroll 0 0;
    border: 1px solid rgba(0, 0, 0, 0.03);
    border-radius: 3px;
    display: table-cell;
    height: 30px;
    padding: 6px;
    position: relative;
    text-align: center;
    vertical-align: middle;
    color: #333;
    cursor: pointer;
}

    #product_gallery { height: 100%; left: 0; opacity: 0; position: absolute; top: 0; width: 100%; z-index: 10000; cursor: pointer; }
   .BarSingle {
    width: 1%;
    height: 10px;
    background-color: #377FEA;
    display: none;
    margin-top: 0px;
}
.dropzone span {
    font-size: 14px;
    color: #333;
    font-weight: 700;
}
    .cmb2-upload-button.button-secondary { display: none; }
    .cwi_image_outer { border: 1px solid #ededed; padding: 5px; width: 14%; position: relative; cursor: pointer;  margin-right: 25px; float: left; box-sizing: border-box; }
    .cwi_image_outer .remove-this,.cwi_image_outer .remove-this-exist { position: absolute;
    right: -12px;
    top: -8px;
    background-color: #00AE9E;
    color: #fff;
    padding: 3px 6px 1px 6px;
    border-radius: 100%;
    font-size: 11px;
    text-align: center;
    width: 22px;
    height: 22px; }
    .cwi_image_outer .thumbnail { height: 70px !important; width: 70px !important; }
    .cwi_image_outer img { margin: 0 auto !important; display: block; }
	
	.DownloadFile {
       border: 1px solid #ededed;
    padding: 5px;
    width: 14%;
    position: relative;
    cursor: pointer;
    margin-top: 10px;
    text-align: center;
    margin-right: 25px;
    /* float: left; */
    box-sizing: border-box;
}
	.DownloadFile img{  height: 70px !important;
    width: 70px !important;     object-fit: contain;}
	.DownloadFile a,.DownloadFile a:hover{
    font-weight: bold;
	text-decoration:none;
   
    display: block;
    font-size: 14px;
}
.DownloadFile .remove-this-exist{
	position: absolute;
    right: -12px;
    top: -8px;
    background-color: #00AE9E;
    color: #fff;
    padding: 3px 6px 1px 6px;
    border-radius: 100%;
    font-size: 11px;
    text-align: center;
    width: 22px;
    height: 22px;	
}
</style>
</head>

<body>
<?php require_once 'files/headerSection.php'; ?>
<div class="page-wrap pb-5 pt-3 bubble-bg-3">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="notification warning">
                    <div class="d-flex"><i class="fas fa-bell"></i></div>
                    <span>Warning: General message</span>
                    <button class="close-ntf"><i class="fas fa-times"></i></button>
                </div>
                <div class="notification success" <?php if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { echo 'style="display:flex;"'; } ?>	>
                    <div class="d-flex"><i class="fas fa-check"></i></div>
                    <span><?php echo $_SESSION['SUCCESS'];  unset($_SESSION['SUCCESS']);?></span>
                    <button class="close-ntf"><i class="fas fa-times"></i></button>
                </div>
                <div class="notification error">
                    <div class="d-flex"><i class="fas fa-times-circle"></i></div>
                    <span>Error: Email not valid</span><button class="close-ntf"><i class="fas fa-times"></i></button>
                </div>
                <div class="wizard">
                    <div class="wizard-inner">
                        <ul class="wizard-steps">
                          <?php 
							 $isNormalProductWizard =0;
							if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
                            $cart = $_SESSION['CART'];
                           	
						    foreach($cart as $key => $product ) {
								if(isset($product['customeProductFields']) && !empty($product['customeProductFields'])){
									$isNormalProductWizard = 1;
											
								}else{
									$isNormalProductWizard = 0;
									break;	
								}
								
							}
							}
							if($isNormalProductWizard==0){
							 ?>
                        
                            <li><a><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
<?php /*?>                            <li class="active"><a><span class="step-num">2</span>Login<span class="step-arrow">&raquo;</span></a></li>
<?php */?>                            <li><a><span class="step-num">2</span>Add-ons<span class="step-arrow">&raquo;</span></a></li>
                          
                            <li><a><span class="step-num">3</span>Checkout</a></li>
                            
                            <?php }else{ ?>
                            
                            <li><a><span class="step-num">1</span>Your cart<span class="step-arrow">&raquo;</span></a></li>
                            <?php /*?><li class="active"><a><span class="step-num">2</span>Login<span class="step-arrow">&raquo;</span></a></li><?php */?>
                            
                            <li><a><span class="step-num">2</span>Checkout<span class="step-arrow">&raquo;</span></a></li>
                           
							
							<?php } ?>
                        </ul>
                    </div>
                    <div class="wizard-content bx-shadow pl-sm-5 pr-sm-5 pb-sm-5">
                      <?php 
					  
					  if(!isset($_SESSION['userId']) || empty($_SESSION['userId']) || !isset($_SESSION['loginType']) ){ ?>
                          <div class="row guest_checkout">
                                <div class="col-md-6 guest_checkout_left text-center">
                                   <form method="post">
                                   <input type="hidden" name="loginType" value="guest">
                                   <button type="submit" class="btn-grad mb-3 btn-lg">Checkout as a guest</button>
                                   </form>
                                </div>
                                
                                 <div class="col-md-6 text-center">
                                 
                                            <a  href="<?=SITEURL;?>social/facebook.php?request=checkout" class="btn-fb_proceed">Signup/Login with facebook</a>
                                </div>
                            </div>
                               <div class="row ">
                                <div class="col-md-12 text-center">
                                		OR
                                </div>
                                
                                </div>
                            <ul class="nav nav-tabs nav-fill pt-4" id="tabs4" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="false">Sign up</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Login</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="tabs4-content">
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <form action="" id="regForm">
                                        <div class="row">
                                            <div class="col-lg-6  mb-3">
                                                <label>First Name</label>
                                                <input type="text" name="first_name" class="form-control">
                                            </div>

                                            <div class="col-lg-6  mb-3">
                                                <label>Last Name</label>
                                                <input type="text" name="last_name" class="form-control mb-3">
                                            </div>

                                            <div class="col-lg-6  mb-3">
                                                <label>Email</label>
                                                <input type="text" name="emailAddress" class="form-control">
                                            </div>

                                            <div class="col-lg-6  mb-3">
                                                <label>Password</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>

                                            <div class="col-lg-6  mb-3">
                                                <label>Repeat Password</label>
                                                <input type="password" name="confirmPassword" class="form-control">
                                            </div>

                                            <div class="col-lg-6 offset-lg-6 text-center">
                                                <button type="submit" class="btn-grad mb-3 btn-lg">Sign up</button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php /*?><div class="row">
                                        <div class="col-lg-6 offset-lg-6 text-center">
                                            <a   href="<?=SITEURL;?>social/facebook.php" class="btn-fb">Sign up with facebook</a>
                                        </div>
                                    </div><?php */?>
                                </div>
                                <!----home-tab---->
                                <div class="tab-pane fade " id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <form action="" id="loginForm">
                                        <div class="row">
                                            <div class="col-lg-6  mb-3">
                                                <label>Email</label>
                                                <input type="text" name="emailAddress" class="form-control">
                                            </div>
                                            <div class="col-lg-6  mb-3">
                                                <label>Password</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>
                                            <div class="col-lg-6 offset-lg-6 text-center">
                                                <button type="submit" class="btn-grad mb-3 btn-lg">Login</button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                </div>
                                <!--profile-tab-->
                            </div>
                            <div class="wizard-foot clearfix mt-4" style="width:100%;">
                                <a   href="addons.php" class="btn-grey btn-lg float-sm-left">Back</a>
                            </div>
                        <?php } else if($_SESSION['userType'] != "user" && !isset($_SESSION['loginType'])) {
                            ?>
                            <div class="row">
                                <div class="col-md-12">
                                    You need to login as a customer to proceed with your order. Please log out &amp; log in as a customer.
                                </div>
                            </div>
                            <?php
                        } else { ?>
                            <!---mytabcontent-->
                            <form action="process-product.php" name="product_form" enctype="multipart/form-data" method="post" class="flyer-form">
                                <div class="row pb-4">
                                    <?php
                                    $has_custom_data = false;

                                    if(isset($productVals['customData']) && !empty($productVals['customData'])) {
                                        $has_custom_data = true;
                                    }

                                    $orderIdFull = $productVals['id'];
                                    $orderIdArray = explode("_",$orderIdFull);
                                    $orderId = $orderIdArray[0];
                                    $parent_product_id = (!empty($orderIdArray[1]) && $orderIdArray[1] > 0) ? $orderIdArray[1] : 0;

                                    if($orderId==11){
                                      //  $orderId = 	 $parent_product_id;
                                    }
									
									
                                    $productData = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$orderId."'");
                                    $productBanner = GetSglDataOnCndi(PRODUCT_BANNER,"`prod_id` = '".$orderId."' AND `filetype` = 'image' order by id asc", "filename");
									if($parent_product_id>0){
										$getBanners = GetMltRcrdsOnCndi(PRODUCT_BANNER,"`prod_id` = '".$parent_product_id."' AND `filetype` = 'image'");
									}

                                    ?>
                                    
                                    <?php 
									if($orderId==11 && $parent_product_id>0 && !empty($getBanners)){
										echo '    <div class="col-lg-2 col-md-3">';
										$foundCover = 0;
										foreach($getBanners as $getBanners)
										{
                                            if ($getBanners['set_default_facebookimage']=="yes")
											{
                                                $foundCover = 1;
													?>
													<img src="../uploads/products/<?=$cartProducts['id'];?>/<?=$getBanners['filename']?>" alt="">
													<?php
											
											}
										}
                                        if($foundCover==0){
                                            echo '<img src="images/facebook_cover.jpg" style="width:100%;" alt="">';
                                        }
										echo "</div>";
										}else{
									?>
                                    <div class="col-lg-2 col-md-3">
                                            <img src="<?=SITEURL."uploads/products/".$productVals['id']."/".$productBanner;?>" class="img-fluid" alt="">
                                            
                                    </div>
                                    <?php } ?>
                                    <div class="col-lg-10 col-md-9">
                                    <?php $ToolTipTemplateValue = get_option('ToolTipTemplateFileds'); ?>
                                        <div style="color:#F00; padding-bottom:10px">
                                            <strong>Note : </strong>Since we will only be editing the text &amp; photos in this template in its current structure, please provide text &amp; photos sufficient to match those seen in the template.
                                        </div>
                                        <?php
										  if($orderId==11){
                                    
									
                                   			 $productDataFacebook = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$parent_product_id."'");
											 $getSettingFormFileds = getSettingFormFields($productDataFacebook['parent_product_cat_id'],$productDataFacebook['child_product_cat_id']);
										
										
										?>
                                        
                                        <h1><?=$productDataFacebook['Title'];?>  Facebook Cover </h1>
                                        <?php 	  
										  }else{
											  $getSettingFormFileds = getSettingFormFields($productData['parent_product_cat_id'],$productData['child_product_cat_id']);
										
										 ?>
                                        <h1><?=$productData['Title'];?>  <?php if(PRODUCT_TYPE_FLYERS==$productData['ProductType']){ ?> -  4x6 Flyer <?php } ?></h1>
										<?php } ?>
                                        <?php 
										
										if(!empty($getSettingFormFileds)){
										
											if($getSettingFormFileds['presenting']==1){ ?>
                                            <label>Presenting:
                                            <?php if(isset($ToolTipTemplateValue['presenting']) && $ToolTipTemplateValue['presenting']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['presenting']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="presenting" value="<?=$productVals['customData']['presenting'];?>" class="form-control mb-3">
                                            <?php 	
											}
											if($getSettingFormFileds['main_title']==1){
												?>
                                                 <label>Main title:
                                                 <?php if(isset($ToolTipTemplateValue['main_title']) && $ToolTipTemplateValue['main_title']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['main_title']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                                 </label>
                                            <?php
                                            if($productVals['dimensional']=='2D')
                                            {
                                                ?>
                                                <input type="text" name="main_title" value="<?=$productVals['customData']['main_title'];?>" class="form-control mb-3">
                                                <?php

                                            }else{
                                                if($productVals['dimensional']=='3D')
                                                {

                                                    if($productData['3dTextEditable'] == 1) { ?>
                                                        <input type="text" name="main_title" value="<?=$productVals['customData']['main_title'];?>" class="form-control mb-3">
                                                    <?php } else {

                                                        ?>
                                                        <input type="text" value="The 3D title for this flyer cannot be edited" disabled class="form-control mb-3">

                                                    <?php }
                                                }else{
                                                    ?>
                                                    <input type="text" name="main_title" value="<?=$productVals['customData']['main_title'];?>" class="form-control mb-3">
                                                    <?php
                                                }
                                            } ?>
                                                
                                             <?php    	
											}
											
											if($getSettingFormFileds['single_title']==1){
											?>
                                             <label>Single title:
                                             <?php if(isset($ToolTipTemplateValue['single_title']) && $ToolTipTemplateValue['single_title']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['single_title']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                             </label>

                                            <input type="text" name="single_title" value="<?=$productVals['customData']['single_title'];?>" class="form-control mb-3">

                                           <?php }	if($getSettingFormFileds['sub_title']==1){ ?>
                                            <label>Sub title:
                                            <?php if(isset($ToolTipTemplateValue['sub_title']) && $ToolTipTemplateValue['sub_title']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['sub_title']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="sub_title" value="<?=$productVals['customData']['sub_title'];?>" class="form-control mb-3">
                                            <?php 	
											}if($getSettingFormFileds['deejay_name']==1){ ?>
                                            <label>Deejay Name:
                                            <?php if(isset($ToolTipTemplateValue['deejay_name']) && $ToolTipTemplateValue['deejay_name']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['deejay_name']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="deejay_name" value="<?=$productVals['customData']['deejay_name'];?>" class="form-control mb-3">
                                            <?php 	
											}if($getSettingFormFileds['ename']==1){ ?>
                                            <label>Name:
                                            
                                            <?php if(isset($ToolTipTemplateValue['ename']) && $ToolTipTemplateValue['ename']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['ename']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="ename" value="<?=$productVals['customData']['ename'];?>" class="form-control mb-3">
                                            <?php 	
											}
											
											if($getSettingFormFileds['date']==1){
											 ?>
                                            <label>Date:
                                            <?php if(isset($ToolTipTemplateValue['date']) && $ToolTipTemplateValue['date']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['date']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="event_date" value="<?=$productVals['customData']['event_date'];?>" class="form-control mb-3 datepicker" autocomplete="off">
                                              <?php
											}
											
											if($getSettingFormFileds['mixtape_name']==1){
											 ?>
                                            <label>Mixtape name:
                                            
                                            <?php if(isset($ToolTipTemplateValue['mixtape_name']) && $ToolTipTemplateValue['mixtape_name']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['mixtape_name']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="mixtape_name" value="<?=$productVals['customData']['mixtape_name'];?>" class="form-control mb-3">
                                           
                                             
                                             <?php } 
											 if($getSettingFormFileds['produced_by']==1){
											 ?>
                                              <label>Produced by:
                                              <?php if(isset($ToolTipTemplateValue['produced_by']) && $ToolTipTemplateValue['produced_by']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['produced_by']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                              
                                              </label>
                                            <input type="text" name="produced_by" value="<?=$productVals['customData']['produced_by'];?>" class="form-control mb-3">
                                             <?php } 
											  if($getSettingFormFileds['artist_name']==1){
											 ?>
                                               <label>Artist name:
                                               <?php if(isset($ToolTipTemplateValue['artist_name']) && $ToolTipTemplateValue['artist_name']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['artist_name']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                               </label>
                                            <input type="text" name="artist_name" value="<?=$productVals['customData']['artist_name'];?>" class="form-control mb-3">
                                             <?php } 
											if($getSettingFormFileds['music_by']==1){
											?>
                                               <label>Music by:
                                               <?php if(isset($ToolTipTemplateValue['music_by']) && $ToolTipTemplateValue['music_by']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['music_by']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                               </label>
                                            <input type="text" name="music_by" value="<?=$productVals['customData']['music_by'];?>" class="form-control mb-3">

                                            <?php 	
											}
											if($getSettingFormFileds['own_song']==1){
												?>
                                                
                                                <?php if(!empty($productVals['type_banner'] ) && in_array("Use my music",$productVals['type_banner'] )){ ?>
                                                <label>Own song:
                                                
                                                <?php if(isset($ToolTipTemplateValue['own_song']) && $ToolTipTemplateValue['own_song']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['own_song']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                                </label>
                                                <div class="form-file-upload mb-3">
                                                    <input type="file" name="own_song" id="own_song" class="inputfile" data-name="own_song" />
                                                    <label for="own_song"><span class="own_song">Select Song</span></label>
                                                </div>
                                                
                                                  <?php 
												if(!empty($productVals['customData']['own_song'])){
												?>
                                                <div class="DownloadFile">
                                                <span class="remove-this-exist" data-product-id="<?php echo $productVals['id'];   ?>" data-id="0" data-type='own_song'>X</span>
                                                <a class="" download href="<?php echo SITEURL.$productVals['customData']['own_song'] ?>"><img src="<?php echo SITEURL; ?>/images/file_icon.png">
                                                <br>Download
                                                </a>
                                                </div>
                                                <?php 	
												} ?>
                                            <?php } ?>
                                               <?php  	
											}
											if($getSettingFormFileds['additional_info']==1){
											?>
                                             
                                            <label>Additional info: (limit 500 characters)
                                            
                                            <?php if(isset($ToolTipTemplateValue['additional_info']) && $ToolTipTemplateValue['additional_info']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['additional_info']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?></label>
                                            <textarea name="more_info" class="form-control mb-3" maxlength="500"><?=$productVals['customData']['more_info'];?></textarea>
                                           

                                            <?php 	
											}
											 if($getSettingFormFileds['requirements_note']==1){ ?> 
                                             <label>Requirements note: (limit 500 characters)
                                             
                                             
                                             
                                             <?php if(isset($ToolTipTemplateValue['requirements_note']) && $ToolTipTemplateValue['requirements_note']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['requirements_note']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                             </label>
                                            <textarea name="requirement_note" value="<?=$productVals['customData']['requirement_note'];?>" class="form-control mb-3" maxlength="500"></textarea>
                                            <?php } 
											if($getSettingFormFileds['venue']==1){
											?>
                                             <label>Venue:
                                             
                                             
                                             
                                             <?php if(isset($ToolTipTemplateValue['venue']) && $ToolTipTemplateValue['venue']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['venue']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                             </label>
                                            <input type="text" name="venue" value="<?=$productVals['customData']['venue'];?>" class="form-control mb-3">

                                            	
											<?php }
											
											if($getSettingFormFileds['address']==1){
											?>
                                             <label>Address:
                                             
                                             
                                             
                                             <?php if(isset($ToolTipTemplateValue['address']) && $ToolTipTemplateValue['address']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['address']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                             </label>
                                            <input type="text" name="address" value="<?=$productVals['customData']['address'];?>" class="form-control mb-3">

                                            <?php }
											if($getSettingFormFileds['music']==1){
											
											?>
										  <label>Music:
                                          
                                          
                                          
                                          <?php if(isset($ToolTipTemplateValue['music']) && $ToolTipTemplateValue['music']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['music']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                          </label>
                                            <input type="text" name="music" value="<?=$productVals['customData']['music'];?>" class="form-control mb-3">
										<?php 
											}
											if($getSettingFormFileds['logo']==1){
											?>
                                            
                                              <label>Logo:
                                              
                                              
                                              <?php if(isset($ToolTipTemplateValue['logo']) && $ToolTipTemplateValue['logo']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['logo']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                              </label>
                                            <div class="form-file-upload">
                                            <?php 
											
											?>
                                                <input type="file" name="venue_logo" id="venue_logo" class="inputfile" data-name="venue_logo" />
                                                <label for="venue_logo"><span class="venue_logo">Select file</span></label>
                                                <?php 
												if(!empty($productVals['customData']['venue_logo'])){
												?>
                                                <div class="DownloadFile">
                                                <span class="remove-this-exist" data-product-id="<?php echo $productVals['id'];   ?>" data-id="0" data-type='venue_logo'>X</span>
                                                <a class="" download href="<?php echo SITEURL.$productVals['customData']['venue_logo'] ?>"><img src="<?php echo SITEURL; ?>/images/file_icon.png">
                                                <br>Download
                                                </a>
                                                </a>
                                                <?php 	
												} ?>
                                            </div>
                                            <?php } if($getSettingFormFileds['phonenumber']==1){ ?>
                                            <label>Phone number:
                                            
                                            
											<?php if(isset($ToolTipTemplateValue['phonenumber']) && $ToolTipTemplateValue['phonenumber']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['phonenumber']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="phone_number" value="<?=$productVals['customData']['phone_number'];?>" class="form-control mb-3">
                                              <?php } if($getSettingFormFileds['email']==1){ ?>
                                            <label>Email:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['email']) && $ToolTipTemplateValue['email']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['email']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="email" required name="venue_email" value="<?=$productVals['customData']['venue_email'];?>" class="form-control mb-3">
                                              <?php } if($getSettingFormFileds['facebook']==1){ ?>
                                            <label>Facebook:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['facebook']) && $ToolTipTemplateValue['facebook']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['facebook']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="facebook" value="<?=$productVals['customData']['facebook'];?>" class="form-control mb-3">
                                              <?php } if($getSettingFormFileds['instagram']==1){ ?>

                                            <label>Instagram:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['instagram']) && $ToolTipTemplateValue['instagram']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['instagram']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="instagram" value="<?=$productVals['customData']['instagram'];?>" class="form-control mb-3">
                                              <?php } if($getSettingFormFileds['twitter']==1){ ?>

                                            <label>Twitter:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['twitter']) && $ToolTipTemplateValue['twitter']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['twitter']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="twitter" value="<?=$productVals['customData']['twitter'];?>" class="form-control mb-3">
                                              <?php }  ?>
                                            <?php 
										}
                                        else{ ?>


                                            <label>Main title:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['main_title']) && $ToolTipTemplateValue['main_title']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['main_title']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <?php
                                            if($productVals['dimensional']=='2D')
                                            {
                                                ?>
                                                <input type="text" name="main_title" value="<?=$productVals['customData']['main_title'];?>" class="form-control mb-3">
                                                <?php

                                            }else{
                                                if($productVals['dimensional']=='3D')
                                                {

                                                    if($productData['3dTextEditable'] == 1) { ?>
                                                        <input type="text" name="main_title" value="<?=$productVals['customData']['main_title'];?>" class="form-control mb-3">
                                                    <?php } else {

                                                        ?>
                                                        <input type="text" value="The 3D title for this flyer cannot be edited" disabled class="form-control mb-3">

                                                    <?php }
                                                }else{
                                                    ?>
                                                    <input type="text" name="main_title" value="<?=$productVals['customData']['main_title'];?>" class="form-control mb-3">

                                                    <?php
                                                }
                                            } ?>
                                            <label>Sub title:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['sub_title']) && $ToolTipTemplateValue['sub_title']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['sub_title']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="sub_title" value="<?=$productVals['customData']['sub_title'];?>" class="form-control mb-3">
                                            <label>Date:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['date']) && $ToolTipTemplateValue['date']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['date']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="event_date" value="<?=$productVals['customData']['event_date'];?>" class="form-control mb-3 datepicker">
                                            <label>Music by:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['music_by']) && $ToolTipTemplateValue['music_by']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['music_by']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="music_by" value="<?=$productVals['customData']['music_by'];?>" class="form-control mb-3">
                                            <?php if(!empty($productVals['type_banner'] ) && in_array("Use my music",$productVals['type_banner'] )){ ?>
                                                <label>Own song:
                                                
                                                
                                                <?php if(isset($ToolTipTemplateValue['own_song']) && $ToolTipTemplateValue['own_song']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['own_song']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                                </label>
                                                <div class="form-file-upload mb-3">
                                                    <input type="file" name="own_song" id="own_song" class="inputfile" data-name="own_song" />
                                                    <label for="own_song"><span class="own_song">Select Song</span></label>
                                                </div>
                                                 <?php 
												if(!empty($productVals['customData']['own_song'])){
												?>
                                                <div class="DownloadFile">
                                                <span class="remove-this-exist" data-product-id="<?php echo $productVals['id'];   ?>" data-id="0" data-type='own_song'>X</span>
                                                <a class="" download href="<?php echo SITEURL.$productVals['customData']['own_song'] ?>"><img src="<?php echo SITEURL; ?>/images/file_icon.png">
                                                <br>Download
                                                </a>
                                                </div>
                                                <?php 	
												} ?>
                                            <?php } ?>
                                            <label>Additional Info: (limit 500 characters)
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['additional_info']) && $ToolTipTemplateValue['additional_info']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['additional_info']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <textarea name="more_info" class="form-control mb-3" maxlength="500"><?=$productVals['customData']['more_info'];?></textarea>
                                            <label>Requirements note: (limit 500 characters)
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['requirement_note']) && $ToolTipTemplateValue['requirement_note']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['requirement_note']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>                                            
                                            </label>
                                            <textarea name="requirement_note" value="<?=$productVals['customData']['requirement_note'];?>" class="form-control mb-3" maxlength="500"></textarea>
                                            <label>Venue:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['venue']) && $ToolTipTemplateValue['venue']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['venue']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="venue" value="<?=$productVals['customData']['venue'];?>" class="form-control mb-3">
                                            <label>Venue Logo (optional):
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['venue_logo']) && $ToolTipTemplateValue['venue_logo']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['venue_logo']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <div class="form-file-upload mb-3">
                                                <input type="file" name="venue_logo" id="venue_logo" class="inputfile" data-name="venue_logo" />
                                                <label for="venue_logo"><span class="venue_logo">Select file</span></label>
                                                 <?php 
												if(!empty($productVals['customData']['venue_logo'])){
												?>
                                                <div class="DownloadFile">
                                                <span class="remove-this-exist" data-product-id="<?php echo $productVals['id'];   ?>" data-id="0" data-type='venue_logo'>X</span>
                                                <a class="" download href="<?php echo SITEURL.$productVals['customData']['venue_logo'] ?>"><img src="<?php echo SITEURL; ?>/images/file_icon.png">
                                                <br>Download
                                                </a>
                                                </a>
                                                <?php 	
												} ?>
                                            </div>
                                            <label>Address:
                                            
                                            
                                            <?php if(isset($ToolTipTemplateValue['address']) && $ToolTipTemplateValue['address']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['address']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?>
                                            </label>
                                            <input type="text" name="address" value="<?=$productVals['customData']['address'];?>" class="form-control mb-3">


                                        <?php  }
                                        ?>


                                        <label>Photos and logos: <?php if(isset($ToolTipTemplateValue['photos_and_logos']) && $ToolTipTemplateValue['photos_and_logos']!=""){ ?>
                                             <i  title="<?php echo $ToolTipTemplateValue['photos_and_logos']; ?>" class="fa fa-question-circle customProductToolTip" aria-hidden="true"  data-toggle="tooltip"></i>
                             				<?php } ?></label>
                                        <div class="img-upload">
                                            <div class="box__input">
                                                <div class="dropzone dz-clickable" id="my-awesome-dropzone-gallery"><span>+ &nbsp;Click or drag your photo here to upload</span>
                                                    <div class="BarSingle"></div>
                                                    <input type="file" class="cmb2-upload-file regular-text" name="product_gallery" id="product_gallery" value="" size="45" multiple="multiple">
                                                    <input class="cmb2-upload-button button-secondary" type="button" value="Add or Upload File">


                                                </div>
                                            </div>
                                        </div>
                                        <div style="color:#F00; padding-bottom:10px">
                                            <strong>Note : </strong>please upload max 5MB image.
                                        </div>
                                        <div id="cwi_upload_image_preview_gallery">
                                        		<?php 
												if(!empty($productVals['customData']['filesImages'])){ 
													foreach($productVals['customData']['filesImages'] as $singlekey=>$singlevalue){
															?>
                                                            <div class="cwi_image_outer" >
                                                            <span data-type="images" class="remove-this-exist" data-product-id="<?php echo $productVals['id'];   ?>" data-id="<?php echo $singlekey; ?>">X</span>
                                                            <img src="<?php echo SITEURL."/".$singlevalue; ?>" class="thumbnail" >
                                                            </div>
                                                           <?php  
																
															
													}
												?>
                                                
												<?php } ?>
                                        </div>
                                        <label class="custom-control custom-checkbox" style="margin-top:15px;margin-left:5px;">

                                            <input type="checkbox" class="custom-control-input addonProd " value="1" <?php echo ($cartVals[0]['customData']['terms']==1)? "checked='checked'":""; ?> name="terms"  oninvalid="if(this.checked==false) {this.setCustomValidity('You must agree to the terms & conditions and privacy policy in order to proceed')}if(this.checked==true) {this.setCustomValidity('');}"
                                                   oninput="this.setCustomValidity('')" onBlur="this.setCustomValidity('')" onChange="this.setCustomValidity('')" required>
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">
                                            You must agree to the <a href="<?php echo SITEURL; ?>terms" target="_blank">terms & conditions</a> and <a href="<?php echo SITEURL; ?>privacy-policy" target="_blank">privacy policy</a> in order to proceed
                                 </span></label>

                                    </div>
                                </div>
                                <div class="wizard-foot clearfix mt-4" style="width:100%;">
                                    <a href="addons.php"  href="addons.php" class="btn-grey btn-lg float-sm-left">Back</a>
                                    <input type="hidden" name="product_id" value="<?=$productVals['id'];?>" />
                                    <input type="hidden" name="product_key" value="<?=$productKeys;?>" />
                                    <input type="submit" class="btn-lg btn-grad float-sm-right" id="proceed-btn" value="Save & Proceed <?php if(($productKeys+1) != (count($cartVals))){ echo 'to next product'; } ?>" />
                                </div>
                            </form>
                            <!---wizard-foot-->
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'files/footerSection.php' ?>
<!----SCRIPTS---------->
<script src="js/jquery.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        function readURL(input) {
            if (input.files && input.files[0]) {
                var fileName = input.files[0].name;
                var bname = $(input).data('name');
                $("."+bname).text(fileName);
            }
        }
        $(".inputfile").on('change', function(){
            var bname = $(this).data('name');
            $("."+bname).text("Select file");
            readURL(this);
        });
        var date = new Date();
        date.setDate(date.getDate());
        $('.datepicker').datepicker({
            startDate: date
        });

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
                data: "emailAddress="+email+"&password="+password+"&login=info",
                success: function(regResponse) {
                    regResponse = JSON.parse(regResponse);
                    jQuery(".mainLoader").hide();
                    if(regResponse.Status == 'success') {
                        window.location.href = '<?=SITEURL?>addons.php';
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

        jQuery("#regForm").submit(function(e) {
            e.preventDefault();
            var email = jQuery("#regForm input[name='emailAddress']").val();
            var fname = jQuery("#regForm input[name='first_name']").val();
            var lname = jQuery("#regForm input[name='last_name']").val();
            var password = jQuery("#regForm input[name='password']").val();
            var confirmPassword = jQuery("#regForm input[name='confirmPassword']").val();

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
            if(fname == '') {
                jQuery("#regForm input[name='first_name']").focus();
                $(".error span").html("Please enter first name.");
                $(".error").attr('style','display:flex;');
                /*$('html, body').animate({
                    scrollTop: $(".error").offset().top
                }, 1000);*/
                setTimeout(function() {$(".error").hide(500)}, 12000);
                return false;
            }

            if(lname == '') {
                jQuery("#regForm input[name='last_name']").focus();
                $(".error span").html("Please enter last name.");
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
            $.ajax({
                type: "POST",
                url: "<?=SITEURL;?>ajax/userRegistration.php",
                data: "emailAdress="+email+"&password="+password+"&fname="+fname+"&lname="+lname+"&regiser=info",
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
                        setTimeout(function() {$(".success").hide(500); location.reload();}, 2000);
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
        //Upload file
        jQuery('.dropzone').on('dragover dragenter', function() {
            jQuery(this).addClass('is-dragover');
        })
        jQuery('.dropzone').on('dragleave dragend drop', function() {
            jQuery(this).removeClass('is-dragover');
        })

        if( jQuery('#product_gallery').length > 0) {




            jQuery('#product_gallery').attr('type','file');
            jQuery('#product_gallery').attr('multiple','multiple');
            var filesInput2 = document.getElementById("product_gallery");
            filesInput2.addEventListener("change", function(event){
                if (jQuery("#cwi_upload_image_preview_gallery .cwi_image_outer").length >= 5 ){
                    alert('Maximum upload 5 file');
                    return false;
                }
                var bar = jQuery('#product_gallery').prev();
                var files = event.target.files; //FileList object
                var numFilescnt = files.length;

                if(numFilescnt > 5){
                    alert('Maximum upload 5 file');
                    jQuery("#cwi_upload_image_preview_gallery").html('');
                    jQuery('#product_gallery').val('');
                    return false;
                }
                var outputGallery = document.getElementById("cwi_upload_image_preview_gallery");
                for(var i = 0; i< files.length; i++){
                    var file = files[i];

                    var fileSize = files[i].size;
                    //if(parseFloat(fileSize)>5000000){
                    var fileSizeMB = ((files[i].size / 1024) / 1024);

                    if(parseFloat(fileSizeMB) > 5){
                        alert('Image Upload Max size is 5MB');
                        jQuery("#cwi_upload_image_preview_gallery").html('');
                        jQuery('#product_gallery').val('');
                        return false;
                    }
                    //Only pics
                    if(!file.type.match('image')) {
                        alert('Only Images Allowd');
                        return false;
                    }
                }
                var interval = 0;
                if(files.length <= 2){
                    interval = 2500;
                }else {
                    interval = (parseInt(files.length)*1000);
                }

                jQuery(bar).animate({ width: "1%"}, 10 );
                jQuery(bar).show();
                jQuery(bar).animate({ width: "100%"}, interval);
                for(var i = 0; i< files.length; i++){
                    var file = files[i];
                    var fileSize = files[i].size;
                    var picReader = new FileReader();
                    picReader.addEventListener("load",function(event){
                        var picFile = event.target;
                        var div = document.createElement("div");
                        div.className = "cwi_image_outer";
                        var cwi_images_input1 = document.createElement("input");
                        cwi_images_input1.setAttribute("type", "hidden");
                        cwi_images_input1.setAttribute("name", "product_gallery_image_hidden[]");
                        cwi_images_input1.setAttribute("value",picFile.result);

                        div.innerHTML = "<span class='remove-this'>X</span><img class='thumbnail' src='" + picFile.result + "'" +"title='" + picFile.name + "'/>";
                        setTimeout(function(){
                            outputGallery.insertBefore(div,null);
                            div.appendChild(cwi_images_input1);
                        },2000);


                    });
                    //Read the image
                    picReader.readAsDataURL(file);
                }
                setTimeout(function(){jQuery(bar).hide();},interval);

            });
        }

        jQuery('body').on('click','.remove-this',function(e){
            jQuery(this).parent().remove();
        });
		
        jQuery('body').on('click','.remove-this-exist',function(e){
            jQuery(this).parent().remove();
			var ID =   jQuery(this).attr("data-id");
			var product_id =   jQuery(this).attr("data-product-id");
			var type =   jQuery(this).attr("data-type");
			
			 $.ajax({
                type: "POST",
                url: "<?=SITEURL;?>ajax/removeInfoImages.php",
                data: "ID="+ID+"&product_id="+product_id+"&type="+type,
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
                        setTimeout(function() {$(".success").hide(500); location.reload();}, 2000);
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
	
jQuery(document).ready(function(e) {
  jQuery('[data-toggle="tooltip"]').tooltip();
});	
</script>

</body>
</html>>>>>