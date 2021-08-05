<?php
ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';
require_once 'function/cloudinary_functions.php';

$PageTitle = "Orders Details";

if(!is_login()) {
    header("location:".SITEURL."login.php")	;
    exit();
}
if($_SESSION['userType'] != "sale_rep") {
    header("location:".SITEURL."index.php")	;
    exit();
}
$AccessID = intval($_SESSION['userId']);
$AccessType = $_SESSION['userType'];
$AccessData = GetSglRcrdOnCndi(USERS, "UserID = '$AccessID'");
if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])) {
    unset($_SESSION['ORDERUID']);
    $_SESSION['ORDERUID'] = intval($_REQUEST['order_id']);
}
if(!isset($_SESSION['ORDERUID']) && empty($_SESSION['ORDERUID'])) {
    echo "<script> window.location.href = '".SITEURL."sales-rep-orders.php';</script>";
    exit();
}
$OrderUID = intval($_SESSION['ORDERUID']);
$OrderData = GetMltRcrdsOnCndiWthOdr(REPRESENTATIVE_ORDERS, "`RepOrdeID` = '$OrderUID' AND `RepUserID` = '$AccessID'", "RepUserID", "ASC");
if(count($OrderData) == 0 || empty($OrderData)) {
    echo "<script> window.location.href = '".SITEURL."sales-rep-orders.php';</script>";
    exit();
}
$CustomerData = GetSglRcrdOnCndi(USERS, "UserID = '".$OrderData[0]['RepCustomerID']."'");
$OrderDate = $OrderData[0]['RepAddedDate'];

$prodOtherSizes = getProdSizeArr();


$ProductArr = getALLProductArr();
$AddonArr = getAddonArr();
$ProductSize = getProdSizeArr();
$ProductType = getProdTypeArr();


?>

<!DOCTYPE html>
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Order Details | Flashy Flyers</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php require_once 'files/headSection.php'; ?>
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
    <link href="css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/jquery.artarax.rating.star.css">
    <link rel="stylesheet" href="css/style-2.css">
    <style>

    .order_change::before {

        background-color: #dd2411 !important;
    }
    .order_complete::before {

        background-color: #1baa32 !important;
    }
    .order_panding::before,.request_change::before{

        background-color: #000 !important;
    }
    .row_section{ clear:both; overflow:hidden; margin-bottom:20px;}
    .request_change .row_section .deleteExtraMockup{ background:#FF0000 !important;}
    .order_complete .row_section .status0,.request_change .row_section .status0{ background:#000 !important;}
    .order_complete .row_section .status1,.request_change .row_section .status1{ background:#1baa32 !important;}
    .order_complete .row_section .status2,.request_change .row_section .status2{ background:#FF0000 !important;}

    .order_complete .row_section .approve.deleteExtraMockup{ background:#FF0000 !important;}
	
	.popup-gallery .ListGallery {
	width: 100px;
	display: inline-block;
	height: 100px;
	margin-bottom: 50px;
	margin-left: 40px;
	background: #000;
	padding: 2px;
	vertical-align:top;
}
.popup-gallery .ListGallery img,.popup-gallery .ListGallery video {
	width: 100%;
	height: 100%;
}
.galleryExtraIcons a.delete{ color:#F00; }
.galleryExtraIcons{ text-align:center; vertical-align:top; margin-top:10px;}
.galleryExtraIcons a{ margin-left:10px; display:inline-block;}

.order_change .galleryExtraIcons a.delete,.order_change .galleryExtraIcons a.dwBtnIcon,.order_complete .galleryExtraIcons a.delete,.order_complete .galleryExtraIcons a.dwBtnIcon 
{ color:#fff;}
.order_change .galleryExtraIcons a.delete{ color:#fff;}
.galleryExtraIcons .fa-check{ font-size:15px; color:#FFF; margin-left:10px;}
.galleryExtraIcons .fa-times{ font-size:15px; color:#FFF;margin-left:10px;}

.order_complete .galleryExtraIcons .fa-check{ font-size:15px; color:#FFF; margin-left:10px;}
.order_complete .galleryExtraIcons .fa-times{ font-size:15px; color:#FFF;margin-left:10px;}
.customer-options a{ display:inline-block !important;}
.customer-options{ width:100% ;}
.request_change .rq-view_rivision {
	background: #fff !important;
	color: red !important;
	display: inline-block;
	border-radius: 10px;
	padding: 5px 30px 5px 30px;
	margin-bottom: 9px;
	position: relative;
}

#popupBoxRivision .customer_content .popup-gallery img{height:100px;}
#popupBoxRivision .customer_content .popup-gallery{display: inline-block;
padding-top: 15px;
margin-right: 5px;
padding: 5px;
margin-top: 10px; position:relative;}
#popupBoxRivision .customer_content .popup-gallery .deletedRevisionImages {
    text-align: center;
    display: block;
    font-size: 12px;
    color: #fff;
    margin-top: 10px;
    position: absolute;
    top: -22px;
    background: red;
    padding: 5px;
    right: -2px;
    border-radius: 23px;
    cursor: pointer;
}
.order_complated_btn_top .rq-view_rivision{ background:#066 !important;color: #fff;
display: inline-block;
border-radius: 10px;
padding: 2px 15px 2px 9px;
margin-bottom: 9px;
position: relative;}


#popup1 .customer_content:nth-child(odd){ background:#005489;}

#popup1 .customer_content .galleryListing {
    display: inline-block;
    /* padding-top: 15px; */
    margin-right: 5px;
    vertical-align: text-bottom;
    padding: 0px;
    margin-top: 0px !important;
    margin-top: 10px;
}

.order_panding .changeReq{ color:#FFF !important;}

.order-details .order-products li{ margin-bottom:40px;}
.order-details .order-products{ margin-top:15px;}
.LeftDivProductDetails {

    width: 50px;
  display: inline-block;

vertical-align: top;
margin-right:5px;

}
.RightDivProductDetails{display: inline-block;width: 90%;}
.view_details a{ font-size: 15px;
    text-decoration: underline !important;

}
.order-details  .order-products li::before { margin-left:-69px; vertical-align:top;}
.order-details  .order-products.rightSideDesing li::before {margin-left:-35px !important; }

#popup1 .popup_block{background-color: #0072ba;}
#popup1 .popup_block .title { color:#FFF;}
#popup1 .popup_block .title { color:#FFF;}

#popup1 .popup_block .customer_content .customer_post{ color:#FFF;}
#popup1 .popup_block{ color:#FFF}
#popup1 .popup_block .customer_content .customer_title{ color:#ccce2a;}


.view_details a{ font-size: 15px !important;
    text-decoration: underline !important; font-weight:bold;

}
.productName {
	width: 410px;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
}
.RightDivProductDetails span{ margin-left:0px !important;}
ol.order-products .order_complete .approve::before{ display:none !important;}
.order_complete .row_section .status1{ border:1px solid #FFF; background:none !important; padding:5px 34px !important;}
.order_complete .order_complated_btn_top{ text-align:center; width:100% !important;}
.order_complete .order_complated_btn_top .customer-options{ text-align:center; width:100% !important;}
.order-details li::before{ border:1px solid #fff;}
.request_change .request_change_top_btn{text-align:center; width:100% !important;}
.pending_feedback_btn{background: none;border: 2px solid;text-align: center;border-radius: 10px;/*! background-color: #222; */color: #00;display: inline-block;border-radius: 10px;padding: 2px 20px 2px 20px;margin-bottom: 9px;position: relative; margin-bottom:30px;}
.ProductTItleRight{ margin-bottom:20px;}
.request_change .customer-media{ background:#F00;}
.request_change .ProductTItleRight a,.request_change .ProductTItleRight,.request_change .ProductTItleRight a:hover{ color:#FFF; text-decoration:none;}

ol.order-products .request_change::before { background:none !important;}
.request_change .customer-options .approve{border: 2px solid; padding:2px 20px;}
.request_change .customer-options .rq-change {
	background-color: none;
	color: #fff;
	display: inline-block;
	border-radius: 10px;
	padding: 2px 20px 2px 20px;
	margin-bottom: 9px;
	position: relative;
	border: 2px solid;
}
.order_complete .approved{background-color: none;
	color: #fff;
	display: inline-block;
	border-radius: 10px;
	padding: 5px 25px;
	margin-bottom: 9px;
	position: relative;
	border: 2px solid;}
.order_panding .customer-options{ text-align:left !important;}
.order_panding .ProductTItleRight a{color: #4f4f4f;}
ol.order-products .order_panding .customer-options .approve{ background:#00b050 !important;}
.order_panding .rq-view_rivision {background: none;border: 2px solid;text-align: center;border-radius: 10px;/*! background-color: #222; */color: #4f4f4f !important;display: inline-block;border-radius: 10px;padding: 2px 10px 2px 12px;margin-bottom: 9px;position: relative;margin-bottom: 30px;}

.order_panding .rq-view_rivision{margin-bottom:0px;}
.order_panding .pending_feedback_btn{margin-bottom: 26px;}
.order_panding{position:relative;}
.order_panding .customer-options{position: absolute;top: 42%;left: 34%;}
.request_change, .order_complete{position:relative;}

.order_complete .customer-options{position:unset !important;margin-top: 30px !important;left: 34%;}

@media only screen and (max-width: 1199px) {
	.order_panding .customer-options{left:39%;}
	.request_change .customer-options{left:39%;position: initial;}
	.order_complete .customer-options{left: 39%;}
}
@media only screen and (max-width: 640px) {
	.order_panding .customer-media .customer-options{width: 100% !Important;padding-top: 17px !Important;position: initial;}
	.request_change .customer-options{width: 100%;display: inline-block;padding-top: 17px !Important;}
	.order_complete .customer-options{width: 100% !Important;position: initial;padding-top: 17px !important;}
}
ol.order-products .order_panding .customer-options .approve,ol.order-products .order_panding .customer-options .rq-change{ padding:2px 15px 2px 15px !important;}
#popup1 .customer_content .galleryListing img{object-fit: contain;}
.ProductInstruction .popup_block {
    background-color: #0072ba;
    color: #fff;
    padding: 20px 15px;
}
.ProductInstruction .productName a{ color:#FFF !important;}
.ProductInstruction .popupfull_content p, .ProductInstruction  .popupfull_content span, .ProductInstruction  .close-btn{ color:#FFF !important;}
.ProductInstruction .popupfull_content  strong{ color:#ccce2a !important;}
.ProductInstruction  .productName{ width:100%;}
.ProductInstruction  .LeftDivProductDetails{ display:none;}
.request_change .Download a{ color:#FFF;}
.order_complete .Download{ display:inline-block;margin-left:10px;}
.order_complete .Download a {
	background-color: none;
	color: #fff;
	display: inline-block;
	border-radius: 10px;
	padding: 2px 15px 2px 15px;
	margin-bottom: 9px;
	position: relative;
	border: 2px solid;
	margin-top: 9px;
}
.Download{ text-align:center;}	
.ProductInstruction .customer_details ul.popup-gallery li { padding-left:0px !important; margin-bottom:5px !important;}
.ProductInstruction .customer_details ul.popup-gallery li a.WhiteText{ color:#FFF !important;}	
.ReviewListingBlock{ background:#FFF !important; border-color:#FFF !important;}
.ReviewForm .notification.error{background: #ed2d01;}
.ReviewForm .notification.error .d-flex,.ReviewForm .notification.success .d-flex{padding: 0px 15px !important;color: #fff !important;}
.ReviewForm .notification.error .d-flex span,.ReviewForm .notification.success .d-flex span{
    color: #fff !important;
}
.customFiles { margin:0px; padding:0px;}
.customFiles li {
    display: inline-block;
    margin-right: 5px;
    margin-bottom: 5px !important;
}
.customFiles li img{ width:50px; height:50px;}

#timerCountdown {
	margin-left: -7px;
}
 #timerCountdown > div {
	text-align: center;
	font-weight: bold;
	font-size: 40px;
}
.time-cell.timer-hour,.time-cell.timer-minute,.time-cell.timer-second{display: inline-block;vertical-align: top;}
.timerDigitsPoint{display: inline-block;}
.timerDigits {
	display: inline-block;

	padding: 5px;
	font-size: 30px;
	margin-left: 5px;

}

.digitWiseCount {
    color: #dbdbdb;
    background: #373737;
    display: inline-block;
    margin-right: 7px;
    padding: 10px 10px;
    min-width: 38px;
    text-align: center;
    border-radius: 5px;
}

.timerLabel{ font-size:15px;}
.outputFilename{display: block;clear: both;width: 100%;margin-left: 10px; margin-top:5px;font-weight: bold;}
.outputFilename label::after {
	content: ", ";
	width: 10px;
	display: inline-block;
}
.outputFilename label:last-child::after{ display:none;}
#popup1 .customer_content .galleryListing img{ width:76px !important;}
</style>
</head>

<body class="OrderDetails">
<?php require_once 'files/headerSection.php'; ?>
<?php if(isset($_SESSION['ERROR']) && !empty($_SESSION['ERROR'])) { ?>
    <div class="notification error">
        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
        <span>Error: <?=$_SESSION['ERROR'];?></span><button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['ERROR']); } if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { ?>
    <div class="notification success div_sucess">
        <div class="d-flex"><i class="fas fa-check"></i></div>
        <span><?=$_SESSION['SUCCESS'];?></span>
        <button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['SUCCESS']); } ?>


<main class="main-content-wrap">
    <div class="container">
        <div class="main-content pl-60 pr-60 bx-shadow">
            <h1 class="page-heading mb-4"><a href="<?php echo SITEURL."sales-rep-orders.php"; ?>" class="btn btn-blue">Back</a> Order #<?=$OrderUID;?> - <?=$CustomerData['FName'].' '.$CustomerData['LName'];?> - <?=date('M d, Y',strtotime($OrderDate));?></h1>

            <div class="row mt-5">
                <div class="col-lg-6 brd-lg-right pr-lg-4 pl-lg-4">
                    <h2 class="blue text-center mb-4">Order details</h2>
                    <div class="mb-4 order-details">
                        <p>Ordered on:</p>
                        <h3><?=date('D M d, Y',strtotime($OrderDate));?> <span><?=date('g:i A',strtotime($OrderDate));?></span></h3>
                    </div>
                   
                    <div class="mb-4 order-details">
                        <p>Order details:</p>
                        <ol class="order-products">
                            <?php 
                            
							 $getMotionsArray  = array();
							 $j=0;
							 $totalPrice = 0;
                            $mainProductName = "";
							$allowed =  array('gif','png' ,'jpg');
                           $j=0;
						   	$OrderData1 = unserialize($OrderData[0]['RepCartData']);
						  foreach($OrderData1 as $OrderList) {
							     $j++;
								
								$OrderList['ProductID'] = $OrderList['id'];
								if(strpos($OrderList['id'], "_") !== false){
									$explodeParentID = explode("_",$OrderList['id']);
									$OrderList['id'] = $explodeParentID[0];
									
									$OrderList['ProductID'] = $OrderList['id'];
									$OrderList['parent_product_id'] = $explodeParentID[1];
								}
								
									
                                if(!isset($ProductArr[$OrderList['id']])) { continue; }
								
								$product=GetSglRcrdOnCndi(PRODUCT, "  id=".$OrderList['id']);
								$productSlug = $product['slug'];	
								
									if($OrderList['parent_product_id']>0){
									 $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$OrderList['parent_product_id']."' AND `filetype` = 'image'","id","ASC");
									  $imgsrc ="";
									  if (strpos($getBanners['filename'],'res.cloudinary.com') !== false)
                                        {
											$imgsrc = '<img width="50" height="50" style="width:50px;object-fit: contain;"  src="'.httpToSecure($getBanners['filename']).'">&nbsp;&nbsp;';
                                        }
                                        else
                                        {
											$imgsrc ='<img width="50" height="50" style="width:50px;object-fit: contain;" src="uploads/products/'.$OrderList['ProductID'].'/'.$getBanners['filename'].'">&nbsp;&nbsp;';
                                           
                                        }
										
											$productDisplayName = "<div class='LeftDivProductDetails'>".$imgsrc."</div><div class='RightDivProductDetails'><div class='productName'>".$ProductArr[$OrderList['parent_product_id']]['Title']." ".$ProductArr[$OrderList['ProductID']]['Title']."</div>";	
										
								}else{
									
									  $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$OrderList['ProductID']."' AND `filetype` = 'image'","id","ASC");
									  $imgsrc ="";
									  if (strpos($getBanners['filename'],'res.cloudinary.com') !== false)
                                        {
											$imgsrc = '<img width="50" height="50" style="width:50px;object-fit: contain;"  src="'.httpToSecure($getBanners['filename']).'">&nbsp;&nbsp;';
                                        }
                                        else
                                        {
											$imgsrc ='<img width="50" height="50" style="width:50px;object-fit: contain;" src="uploads/products/'.$OrderList['ProductID'].'/'.$getBanners['filename'].'">&nbsp;&nbsp;';
                                           
                                        }
									
									
									if($ProductArr[$OrderList['ProductID']]['Addon']==1){
										
											$productDisplayName = "<div class='LeftDivProductDetails'>".$imgsrc."</div><div class='RightDivProductDetails'><div class='productName'>".$ProductArr[$OrderList['ProductID']]['Title']."</div>";	
										
									}else{
									$productDisplayName = "<div class='LeftDivProductDetails'><a target='_blank' href=".SITEURL."p/".$productSlug." style='color:#4f4f4f;text-decoration:none'>".$imgsrc."</a></div><div class='RightDivProductDetails'><div class='productName'><a target='_blank' href=".SITEURL."p/".$productSlug." style='color:#4f4f4f;text-decoration:none'>".$ProductArr[$OrderList['ProductID']]['Title']."</a></div>";	
									}
								
								
									
								}
								$Dimensional = $OrderList['dimensional'];
								$type_banner = $OrderList['type_banner'];
								$TypeBannerListStr = "";
								if(!empty($type_banner)){
									foreach($type_banner as $banner){
										$TypeBannerListStr  .="(".ucfirst($banner).")";
										}
								}
								 $TurnAroundTime = $OrderList['deliveryTime'];
								 	$listOSArr = $OrderList['otherSize'];
									
									 if($ProductArr[$OrderList['ProductID']]['Addon']==0){
                                    if(!empty($listOSArr)) {

                                        foreach($listOSArr as $osListArr) {
                                            if($ProductSize[$osListArr]['name']!=""){
                                                $listOSStr .= "(".$ProductSize[$osListArr]['name'].")";
                                            }

                                        }

                                    }
                                }
								
								$totalPrice += $OrderList['totalPrice'];
                                ?>

                                   <li><?=$productDisplayName;?> <span class="type"><?=$ProductType[$ProductArr[$OrderList['ProductID']]['parent_product_cat_id']]['name'];?></span> <span class="price">($<?=$OrderList['totalPrice'];?>)</span>
                                     
                                    <div class="size">
										<?php if(empty($OrderList['customeProductFields'])){?>
									<?php echo ($Dimensional!="") ? "(".$Dimensional.")":"";
                                        echo ($TypeBannerListStr!="")? $TypeBannerListStr:"";;
                                        
										echo ($TurnAroundTime!="" && $TurnAroundTime>0) ? "(".getTurnArroundTypeByID($TurnAroundTime).")":"";
                                        ?><?php if(!empty($ProductSize[$OrderList['defaultSize']]['name'])) { echo "(".$ProductSize[$OrderList['defaultSize']]['name'].")"; } 
										
										 if(!empty( $listOSStr)) { echo $listOSStr; } ?>
                                         <?php }
										
										  ?>
                                          <?php if($ProductArr[$OrderList['ProductID']]['Addon']!=1){ ?>
                                        
                                          <span style="margin-left:0px;" class="approve2 view_details" onClick="OpenInstructionsProduct('<?php echo $j; ?>')"><a>View Details</a></span>
											<?php } ?>
                                         </div>
                                         
                                         <div id="ProductInstruction<?php echo $j; ?>" class="popup ProductInstruction">

    <div class="popup-box">

        <div class="popup-middle">

            <div class="popup_block">

                <h4 class="title text-center mb-4"><strong class="prodTitle"><?php echo $productDisplayName; ?></strong> <span class="close-btn float-right"><i class="fa fa-times"></i></span></h4>

                <div class="popupfull_content">

                    <div class="customer_content">
                        <?php
						$MainTitle = $OrderList['customData']['main_title'];
						$Subtitle = $OrderList['customData']['sub_title'];
						$Mixtapename = $OrderList['customData']['mixtape_name'];
						$Singletitle = $OrderList['customData']['single_title'];
						$deejay_name = $OrderList['customData']['deejay_name'];
						$presenting = $OrderList['customData']['presenting'];
						$ename = $OrderList['customData']['ename'];
						$EventDate = $OrderList['customData']['date'];
						$MusicBy = $OrderList['customData']['music_by'];
						$ownSong = $OrderList['customData']['own_song'];
						$Venue  = $OrderList['customData']['venue'];
						$Address = $OrderList['customData']['address'];
						$MoreInfo = $OrderList['customData']['more_info'];
						$requirement_note = $OrderList['customData']['requirement_note'];
						$venue_logo = $OrderList['customData']['venue_logo'];
						$ArtistName = $OrderList['customData']['artist_name'];
						$ProducedBy = $OrderList['customData']['produced_by'];
						$ProductTypeOrder = $OrderList['customData']['ProductType'];
						$PhoneNumber = $OrderList['customData']['phonenumber'];
						$VenueEmail = $OrderList['customData']['venue_email'];
						$Facebook = $OrderList['customData']['facebook'];
						$Instagram = $OrderList['customData']['instagram'];
						$Twitter = $OrderList['customData']['twitter'];
						$Music = $OrderList['customData']['music'];
					
						$filesImages = $OrderList['customData']['filesImages'];
						$filesImagesArr = array();
						if(!empty($filesImages)){
								$filesImagesArr = $OrderList['customData']['filesImages'];
						}
						if(!empty($OrderList['customeProductFields'])){
							
							$customeProductsFileds = $OrderList['customeProductFields'];
							
							if(isset($customeProductsFileds) && !empty($customeProductsFileds)){
												$checkCustomProduct =1;
												foreach($customeProductsFileds as $customeProductFieldsKey=>$customeProductFieldsValue){
													
													foreach($customeProductFieldsValue as $filedsPrimaryIndex=>$filedsPrimaryIndexValue){
														
														foreach($filedsPrimaryIndexValue as $filedsIndex=>$filedsIndexValue){
															if($filedsIndex=="sizes"){continue;}
												
														$FiledsLabal = $customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex];
														$Filedsvalue = $filedsIndexValue;
														if($filedsIndex=="turnaround_time"){
															$Filedsvalue = $deliArr[$Filedsvalue];
														}
														 if(($filedsIndex=="checkbox_sided" || $filedsIndex=="add_music" || $filedsIndex=="add_video" || $filedsIndex=="add_facebook_cover") && $Filedsvalue=="on"){
															 		echo  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> Yes</span></div>';
														
														}
														else if($filedsIndex!="checkbox_sided" && $filedsIndex!="add_music" && $filedsIndex!="add_video" &&  $filedsIndex!="add_facebook_cover"){
														
														if(($filedsIndex=="files" || $filedsIndex=="music_file" || $filedsIndex =="attach_logo" || $filedsIndex=="attach_any_logos" || $filedsIndex=="attach_your_logo_design" || $filedsIndex=="vector_psd_pdf" || $filedsIndex=="attach_any_pictures"  || $filedsIndex=="attach_any_style_reference") && !empty($Filedsvalue)){
															
															
														  $filesData = array();
														if($customeProductFieldsKey=="flyer_design" || $customeProductFieldsKey=="3d_logo_conversion" ||  $customeProductFieldsKey=="laptop_skin" || $customeProductFieldsKey=="facebook_cover"  || $customeProductFieldsKey=="business_card" || $customeProductFieldsKey=="logo" || $customeProductFieldsKey=="logo_intro" || $customeProductFieldsKey=="animated_flyer"  || $customeProductFieldsKey=="mixtape_cover_design"){
																$filesData = $Filedsvalue;
														}else{
																if($filedsIndex=="attach_logo"  || $filedsIndex=="music_file" ||  $filedsIndex=="attach_any_logos" || $filedsIndex=="attach_your_logo_design" || $filedsIndex=="vector_psd_pdf" || $filedsIndex=="attach_any_pictures"  || $filedsIndex=="attach_any_style_reference"){
																	$filesData[] = $Filedsvalue;
																}else{
																	$filesData = $Filedsvalue;
																		
																}
															}
															
															if($filedsIndex=="music_file"){
																$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'] = "Music File";		
															}
															echo  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong></span></div>';		
														
														echo '<div class="customFiles popup-gallery">';
														foreach($filesData as $files){

														if(! empty( $files ) && file_exists( SITE_BASE_PATH."/uploads/custom_product/" . $files)){
															
															
															$uploadedFileType = strtolower(pathinfo($files,PATHINFO_EXTENSION));
															 
															echo '<div class="ImagesCustom">';
															if($uploadedFileType=="jpg" || $uploadedFileType=="jpeg" || $uploadedFileType=="png" || $uploadedFileType=="gif"){
																echo '<a href="'.SITEURL.'/uploads/custom_product/'.$files.'" class="text-white magnificPopup WhiteText"><img src="'.SITEURL.'/uploads/custom_product/'.$files.'" width="50" height="50"></a>';
															}else{
																echo '<a href="'.SITEURL.'/uploads/custom_product/'.$files.'" download class="text-white WhiteText"><i class="fa fa-download"></i><br></a>';	
															}
															echo '</div>';
														 } 

														}
								    					echo '</div>';
														}
															
														//echo "filedsIndex".$filedsIndex;	
														if($Filedsvalue!="" && $filedsIndex!="music_file"  && $filedsIndex!="defaultSize" && $filedsIndex!="otherSize" && $filedsIndex!="attach_any_logos" && $filedsIndex!="attach_your_logo_design" && $filedsIndex!="vector_psd_pdf" && $filedsIndex!="attach_any_pictures"  && $filedsIndex!="attach_any_style_reference"  && $filedsIndex!="files" && $filedsIndex!="attach_logo"){	
													 	
																echo  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> '.$Filedsvalue.'</span></div>';
														}else if($filedsIndex=="defaultSize" && !empty($Filedsvalue)){
															
																echo  ' <div class="customer_details"><strong>Default Size: </strong> '.$prodOtherSizes[$Filedsvalue]['name'].'</span></div>';
															
																
														}
														else if($filedsIndex=="otherSize" && !empty($Filedsvalue)){
																	$otherSize = "";
																	foreach($Filedsvalue as $singleSize){
																		$otherSize .= $prodOtherSizes[$singleSize]['name'].", ";
																	}
																	
																echo  ' <div class="customer_details"><strong>Other Sizes: </strong> '.rtrim($otherSize,", ").'</span></div>';
														}
														
														}
													}
													
													}
												}
												
											}	
						}else{
						
						  if(!empty($presenting)) {?>
                            <div class="customer_details">
                                <strong>Presenting: </strong> <span id="sTitle"><?php echo $presenting; ?></span>
                            </div>
                        <?php } 
						 if(!empty($MainTitle)) {?>
                            <div class="customer_details">
                                <strong>Main title: </strong> <span id="mTitle"><?php echo $MainTitle; ?></span>
                            </div>
                        <?php } ?>

                        
						<?php if(!empty($Mixtapename)) {?>
                            <div class="customer_details">
                                <strong>Mixtape name: </strong> <span id="sTitle"><?php echo $Mixtapename; ?></span>
                            </div>
                        <?php } ?>
                        
                        <?php if(!empty($Singletitle)) {?>
                            <div class="customer_details">
                                <strong>Single title: </strong> <span id="sTitle"><?php echo $Singletitle; ?></span>
                            </div>
                        <?php } ?>
                        

                        <?php if(!empty($Subtitle)) {?>
                            <div class="customer_details">
                                <strong>Sub title: </strong> <span id="sTitle"><?php echo $Subtitle; ?></span>
                            </div>
                        <?php } ?>
                        
                      
                        
                        
                            <?php if(!empty($deejay_name)) {?>
                            <div class="customer_details">
                                <strong>Deejay Name: </strong> <span id="sTitle"><?php echo $deejay_name; ?></span>
                            </div>
                        <?php } ?>
                        
                        
                        
                        
                            <?php if(!empty($ename)) {?>
                            <div class="customer_details">
                                <strong>Name: </strong> <span id="sTitle"><?php echo $ename; ?></span>
                            </div>
                        <?php } ?>
                        
                        
                        

                        <?php if(!empty($EventDate)) {?>
                            <div class="customer_details">
                                <strong>Date: </strong> <span id="sTitle"><?php echo $EventDate; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($MusicBy)) {?>
                            <div class="customer_details">
                                <strong>Music by: </strong> <span id="sTitle"><?php echo $MusicBy; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(! empty( $ownSong ) && file_exists( SITE_BASE_PATH . $ownSong )){ ?>
                            <div class="customer_details">
                                <strong>Own song: </strong> <a href="<?=SITEURL.$ownSong;?>" target="_blank" style="color:#FFF;">Download</a>
                            </div>
                        <?php } ?>

                        <?php if(!empty($ArtistName)) {?>
                            <div class="customer_details">
                                <strong>Artist name: </strong> <span id="sTitle"><?php echo $ArtistName; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($ProducedBy)) {?>
                            <div class="customer_details">
                                <strong>Produced by: </strong> <span id="sTitle"><?php echo $ProducedBy; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($PhoneNumber)) {?>
                            <div class="customer_details">
                                <strong>Phone number: </strong> <span id="sTitle"><?php echo $PhoneNumber; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($VenueEmail)) {?>
                            <div class="customer_details">
                                <strong>Email: </strong> <span id="sTitle"><?php echo $VenueEmail; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($Facebook)) {?>
                            <div class="customer_details">
                                <strong>Facebook: </strong> <span id="sTitle"><?php echo $Facebook; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($Instagram)) {?>
                            <div class="customer_details">
                                <strong>Instagram: </strong> <span id="sTitle"><?php echo $Instagram; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($Twitter)) {?>
                            <div class="customer_details">
                                <strong>Twitter: </strong> <span id="sTitle"><?php echo $Twitter; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($Music)) {?>
                            <div class="customer_details">
                                <strong>Music: </strong> <span id="sTitle"><?php echo $Music; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($MoreInfo)) {?>
                            <div class="customer_details">
                                <strong>More info: </strong>
                                <span class="d-block" id="mInfo"><?php echo $MoreInfo; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($requirement_note)) {?>
                            <div class="customer_details">
                                <strong>Requirements note: </strong>
                                <span class="d-block" id="requmNote"><?php echo $requirement_note; ?></span>
                            </div>
                        <?php } ?>


                        <?php if(!empty($Venue)) {?>
                            <div class="customer_details">
                                <strong>Venue: </strong> <span id="sTitle"><?php echo $Venue; ?></span>
                            </div>
                        <?php } ?>

                        <?php if(!empty($venue_logo)) {?>
                            <div class="customer_details">

                                <strong><?php if($ProductTypeOrder==12){echo "Logo"; }else{echo 'Venue Logo';}?>: </strong>

                                <ul class="list-unstyled mb-0 pl-4 popup-gallery">

                                    <?php
									$ext = pathinfo($venue_logo, PATHINFO_EXTENSION);
									if(!in_array($ext,$allowed)) {
										?>
                                        <li id="eImage1"><a href="<?=SITEURL.$venue_logo;?>" target="_blank" class="text-white WhiteText">
                                   
                                    
                                    <i class="fas fa-file pr-2"></i> <?php echo basename($venue_logo); ?><?php ?></a>   <a href="<?=SITEURL.$venue_logo;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> </li>
                                                <?php 
											}else{
									 ?>
                                    
                                    <li id="eImage1"><a href="<?=SITEURL.$venue_logo;?>" class="text-white magnificPopup WhiteText">
                                   
                                    
                                    <i class="fas fa-image pr-2"></i> image.jpg<?php ?></a>   <a href="<?=SITEURL.$venue_logo;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> </li>
                                    <?php } ?>

                                </ul>

                            </div>
                        <?php } ?>

                        <?php if(!empty($Address)) {?>
                            <div class="customer_details">
                                <strong>Address: </strong> <span id="sTitle"><?php echo $Address; ?></span>
                            </div>
                        <?php } ?>

                        <?php 
						
						if(!empty($filesImagesArr) ) {?>
                            <div class="customer_details">
                                <strong>Photos and logos: </strong>
                                <ul class="list-unstyled mb-0 pl-4 popup-gallery">
									
                                    
                                    <?php 
									foreach($filesImagesArr as $singleImageKey=>$singleImageValue){
									if(file_exists( SITE_BASE_PATH . $singleImageValue )){ ?>
                                    
                                    		
                                        <li>
                                        <?php 
										$ext = pathinfo($singleImageValue, PATHINFO_EXTENSION);
										if(!in_array($ext,$allowed)) {
										?>
                                        <a href="<?=SITEURL.$singleImageValue;?>" target="_blank" class="text-white WhiteText">
                                   
                                    
                                    <i class="fas fa-file pr-2"></i> <?php echo basename($singleImageValue); ?><?php ?></a>   <a href="<?=SITEURL.$singleImageValue;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> 
                                        
                                        <?php }else{ ?>
                                        <a href="<?=SITEURL.$singleImageValue;?>" class="text-white magnificPopup WhiteText"><i class="fas fa-image pr-2"></i> image.jpg</a><a href="<?=SITEURL.$singleImageValue;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a>
                                        <?php } ?>
                                        
                                        </li>
                                    <?php } 
									}
									?>

                                  
                                </ul>
                            </div>
                        <?php }} ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
									<?php if($startTimer=="yes"){ ?>
                                     
                                    <div id="timerCountdown<?php echo $OrderList['Id']; ?>"></div>
                                    	<?php 
										
										
										 if(!empty($OrderList['customeProductsFileds'])){
											 
											$customeProductsFiledsTimer = multidimation_to_single_array(unserialize($OrderList['customeProductsFileds']));
				
											$timerDelHours=$customeProductsFiledsTimer['turnaround_time'];	
				
											 
										 }else{
												$timerDelHours = $OrderList['TurnAroundTime']; 
										  }
									
										
										
										if($timerDelHours==""){$timerDelHours=0;}
										?>
                                    	
									<?php } ?>
		
                                </li>
                            <?php }
                            ?>
                        </ol>
                    </div>
                     <?php
					 $RepDiscountData = unserialize($OrderData[0]['RepDiscountData']);
					  if($RepDiscountData['discountData']['DiscountName']!=""){ 
					  $discountTotal = $RepDiscountData['discountData']['Value'];
					  if($RepDiscountData['discountData']['Type']==2){
					  	$discountTotal = ($totalPrice*$RepDiscountData['discountData']['Value'])/100;
					  }
					  ?>
                    <div class="mb-4 order-details">
                        <p>Discount code:</p>
                        <h3><?php echo $RepDiscountData['discountData']['DiscountName']; ?>  (-$<?php echo number_format($discountTotal,2);?>) <span>USD</span></h3>
                    </div>
                    <?php } ?>
                    
                    <div class="mb-4 order-details">
                        <p>Total:</p>
                        <h3>$<?=number_format($totalPrice-$discountTotal,2);?> <span>USD</span></h3>
                    </div>
                    
                    
                    
                </div>
                
                
            </div>
        </div>
        


    </div>
</main>



<?php 

require_once 'files/footerSection.php' ?>
<!----SCRIPTS---------->
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>


<script src="js/jquery.magnific-popup.min.js"></script>
 <link rel="stylesheet" href="css/magnific-popup.min.css">
<script>


function OpenInstructionsProduct(id){
	jQuery(".AddonsInstruction,.ProductInstruction").removeClass("active");
	$("#ProductInstruction"+id).addClass("active");
}

    
	  $('.popup-gallery').magnificPopup({

        delegate: '.magnificPopup',

        type: 'image',

        mainClass: 'mfp-img-mobile',

        gallery: {

            verticalFit: true

        },

    });

</script>



<script>

   
 
    $(".close-btn").click(function(){
        $("#rq-popup").removeClass("active");
		 $("#popup1").removeClass("active");
		
     jQuery(".AddonsInstruction,.ProductInstruction").removeClass("active");

    });
	
	
</script>


</body>
</html>