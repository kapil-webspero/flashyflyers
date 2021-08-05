<?php

ob_start();

require_once 'function/constants.php';

require_once 'function/configClass.php';

require_once 'function/siteFunctions.php';

$PageTitle = "Orders Details";

if(!is_login()) {

    header("location:".SITEURL."login.php")	;
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

    echo "<script> window.location.href = '".SITEURL."my-work.php';</script>";

    exit();

}

$OrderUID = intval($_SESSION['ORDERUID']);


if(isset($_POST['uploadfile']) && !empty($_POST['orderIID'])) {

    $orderIID = intval($_POST['orderIID']);

    if(GetNumOfRcrdsOnCndi(ORDER, "`Id` = '$orderIID' AND `TransactionID` = '$OrderUID'")>0) {

        $dynamicPath = date("Y",$systemTime)."/".date("m",$systemTime)."/";

        $dynamic_dir = "uploads/work/";

        if(!file_exists($dynamic_dir)) {

            mkdir($dynamic_dir, 0777, true);

        }
		
		$filenameAttach = $filenameAttachThumb = array();
		foreach($_FILES["filename"]['name'] as $filePostsKey=>$filePosts){
			
		$fileType = strtolower(pathinfo(basename($_FILES["filename"]["name"][$filePostsKey]),PATHINFO_EXTENSION));
		$imageNameRand = rand(1, 10000000000);
		$file_name_1 = $imageNameRand.".".$fileType;
        $target_file = $dynamic_dir. $file_name_1;
		$filename_arr = explode('.',$file_name_1);
		
		    //thumb
			 $sourceProperties = getimagesize($_FILES["filename"]["tmp_name"][$filePostsKey]);
		 	  $FileExt = pathinfo($_FILES["filename"]["name"][$filePostsKey], PATHINFO_EXTENSION);
			  $thumbImageName = $imageNameRand;
			  
			 if(strtolower($FileExt)=="jpg" || strtolower($FileExt)=="jpeg" || strtolower($FileExt)=="png" || strtolower($FileExt)=="gif"){
			  $thumbImage = ImageThumbCreate($sourceProperties,$FileExt,$_FILES["filename"]["tmp_name"][$filePostsKey],$thumbImageName,SITE_BASE_PATH.$dynamic_dir,'work');
			}
		
		 
		 if (move_uploaded_file($_FILES["filename"]["tmp_name"][$filePostsKey], $target_file)) {
			 $file_size = $_FILES["filename"]["size"][$filePostsKey];
			digitalOceanUploadImage(SITE_BASE_PATH.$target_file,'work');
              
			 
			 
			 
				 $upload  = $file_name_1;
				 $filenameAttach[] =$file_name_1;
				 if(strtolower($FileExt)=="jpg" || strtolower($FileExt)=="webp" || strtolower($FileExt)=="jpeg" || strtolower($FileExt)=="png" || strtolower($FileExt)=="gif"){
					$filenameAttachThumb[] =array("large"=>$file_name_1,"small"=>$thumbImage);
				}
			  
			 
			 $file_name = $upload;	
           // $original_name = basename($_FILES["filename"]["name"][$filePostsKey]);

            //InsertRcrdsByData(PRODUCT_BANNER,"`prod_id` = '$ProductID', `filename` = '$file_name', `filetype` = 'image', `original_name` = '$original_name'");

          	
			
		 if($_REQUEST['TypeBanner']=="motion"){
				$TypeBanner = " and TypeBanner='motion' ";		 
		 }
		 if($_REQUEST['TypeBanner']=="static"){
				$TypeBanner = " and TypeBanner='static' and Size ='".$_REQUEST['size']."' ";		 
		 }
		 
		  if($_REQUEST['TypeBanner']==""){
				$_REQUEST['TypeBanner'] = "default";  
		  }
		  if($_REQUEST['size']==""){
		 		 $_REQUEST['size'] = 0;
		  }
		 
        } 
		
		 
			
		}
		  $OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '$OrderUID' AND `AssignedTo` = '$AccessID' and ProductID='".$_REQUEST['ProductID']."'", "Id", "ASC");
		
		 $checkFirstTimeData = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`ProductID` = '".$_REQUEST['ProductID']."' AND `Type` = 'admin' ".$TypeBanner." and FirstTime='yes'", "ID", "ASC");
		  $firstTime = "yes";
		  
		  if(!empty($checkFirstTimeData)){
			  $firstTime = "no";
		  }
		if($_REQUEST['ResponseState']==1){
		  
		   UpdateRcrdOnCndi(ORDER,"`ResponseFile` = '".$upload."', `ResponseDate` = '$systemTime'", "`Id` = '$orderIID' AND `TransactionID` = '$OrderUID'");
		 
		 InsertRcrdsByData(CHANGE_REQ,"`OrderID` = '$orderIID',`TransactionOrderID` = '$OrderUID',`ProductID` = '".$_REQUEST['ProductID']."', `UserID`='".$OrderData[0]['CustomerID']."', `DesignerID`='".$OrderData[0]['AssignedTo']."', `MessageText` ='', `Attachment`='".serialize($filenameAttach)."', `AttachmentThumb`='".serialize($filenameAttachThumb)."', `AttechmentName` = '".serialize($filenameAttach)."', `UserReadState` = '1', `CreationDate` = '$systemTime', `SystemIP` = '$systemIp', `24HoursChk` = '0', `Type` = 'admin',FirstTime='".$firstTime."',TypeBanner='".$_REQUEST['TypeBanner']."',Size='".$_REQUEST['size']."',ResponseState='1'");                 
	
		  }else{
	
		  
		  UpdateRcrdOnCndi(ORDER,"`ResponseState` = '3', `ResponseFile` = '".$upload."', `ResponseDate` = '$systemTime'", "`Id` = '$orderIID' AND `TransactionID` = '$OrderUID'");
			 InsertRcrdsByData(CHANGE_REQ,"`OrderID` = '$orderIID',`TransactionOrderID` = '$OrderUID',`ProductID` = '".$_REQUEST['ProductID']."', `UserID`='".$OrderData[0]['CustomerID']."', `DesignerID`='".$OrderData[0]['AssignedTo']."', `MessageText` ='', `Attachment`='".serialize($filenameAttach)."', `AttachmentThumb`='".serialize($filenameAttachThumb)."', `AttechmentName` = '".serialize($filenameAttach)."', `UserReadState` = '1', `CreationDate` = '$systemTime', `SystemIP` = '$systemIp', `24HoursChk` = '0', `Type` = 'admin',FirstTime='".$firstTime."',TypeBanner='".$_REQUEST['TypeBanner']."',Size='".$_REQUEST['size']."',ResponseState='2'");                 
	
		  }
		
		  
		
		 $_SESSION['SUCCESS'] = "The file has been uploaded.";
		
		
            $curl = curl_init();

            // Set some options - we are passing in a useragent too here

            curl_setopt_array($curl, array(

                CURLOPT_RETURNTRANSFER => 1,

                CURLOPT_URL => SITEURL.'EmailTemplate/orderResponse.php?OID='.$OrderUID."&reset=".$reset,

                CURLOPT_USERAGENT => 'Curl Test'

            ));
			
		
            // Send the request & save response to $resp

            $resp = curl_exec($curl);

            // Close request to clear up some resources

            curl_close($curl);


       

    } else {

        $_SESSION['ERROR'] = "Error in uploading file, please try again later.";

    }
	 echo "<script> window.location.href = '".SITEURL."work-details.php?order_id=$OrderUID';</script>";
        exit();

}



$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '$OrderUID' AND `AssignedTo` = '$AccessID'", "Id", "ASC");
if(count($OrderData) == 0 || empty($OrderData)) {
    echo "<script> window.location.href = '".SITEURL."my-work.php';</script>";
    exit();
}

//$UserID = $OrderData[0]['CustomerID'];
//$OrderDate = $OrderData[0]['OrderDate'];
$AssginDate =$OrderData[0]['AssignedOn'];
//$TransactionID = $OrderData[0]['TransactionID'];
//$OrderStatus = $OrderData[0]['OrderStatus'];
//$TransactionData = GetSglRcrdOnCndi(TRANSACTION, "`id` = '$TransactionID'");
$ProductArr = getALLProductArr();
$AddonArr = getAddonArr();
$ProductSize = getProdSizeArr();
$ProductType = getProdTypeArr();



$AssignTo = $OrderData[0]['AssignedTo'];
$UserID = $OrderData[0]['CustomerID'];
$OrderDate = $OrderData[0]['OrderDate'];
$TransactionID = $OrderData[0]['TransactionID'];
$OrderStatus = $OrderData[0]['OrderStatus'];
$TransactionData = GetSglRcrdOnCndi(TRANSACTION, "`id` = '$TransactionID'");
$productID = $OrderData[0]['ProductID'];

$MainTitle = $OrderData[0]['MainTitle'];
$Subtitle = $OrderData[0]['Subtitle'];
$EventDate = $OrderData[0]['EventDate'];
$MusicBy = $OrderData[0]['MusicBy'];
$ownSong = $OrderData[0]['own_song'];
$Venue  = $OrderData[0]['Venue'];
$Address = $OrderData[0]['Address'];
$MoreInfo = $OrderData[0]['MoreInfo'];
$requirement_note = $OrderData[0]['requirement_note'];
$venue_logo = $OrderData[0]['venue_logo'];
$ArtistName = $OrderData[0]['ArtistName'];
$ProducedBy = $OrderData[0]['ProducedBy'];
$ProductTypeOrder = $OrderData[0]['ProductType'];
$PhoneNumber = $OrderData[0]['PhoneNumber'];
$VenueEmail = $OrderData[0]['VenueEmail'];
$Facebook = $OrderData[0]['Facebook'];
$Instagram = $OrderData[0]['Instagram'];
$Twitter = $OrderData[0]['Twitter'];
$Music = $OrderData[0]['Music'];


$prodOtherSizes = getProdSizeArr();


?>



<!DOCTYPE html>

<html lang="en">



<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <title>Order Details | Flashy Flyers</title>

    

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require_once 'files/headSection.php'; ?>

    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    <link rel="stylesheet" href="css/magnific-popup.min.css">

    <link rel="stylesheet" href="css/style-2.css">
    <style>
    .col-lg-6.pr-lg-4.pl-lg-4.div_left_part .mb-4.order-details .order-products li{margin-bottom:31px !important;}
    .custom-file-input{ min-width:auto !important;}

    .custom-file.div_pandinlabel {
        min-height: 71px;
    }

    span.name_file {
        display:inline-block;
        float: left;
        font-size: 14px;
        font-weight: normal !important;

        line-height: 13px;
        margin-bottom: 13px !important;
        padding-left: 12px;
        width: 230px !important;
        word-break: break-all !important;
        margin-top:10px !important;
    }
    .custom-file {
        margin-bottom: 23px;
    }
    .order_change::before {

        background-color: #dd2411 !important;
    }
    .order_change .name_file{ color:#FFF !important;}
    .order_complete .name_file{ color:#FFF !important;}
    .order_panding .name_file{ color:#000 !important;}
    .order_complete::before {

        background-color: #1baa32 !important;
    }
    .order_panding::before {

        background-color: #000 !important;
    }
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
.order_panding .galleryExtraIcons .fa-check,.order_panding .galleryExtraIcons .fa-times { color:#000 !important;}

#popup1 .customer_content:nth-child(odd){ background:#005489;}
#popup1 .customer_content .galleryListing{display: inline-block;
padding-top: 15px;
margin-right: 5px;

padding: 5px;
margin-top: 10px; position:relative;}
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

#popup1 .popup_block{background-color: #0072ba;}
#popup1 .popup_block .title { color:#FFF;}
#popup1 .popup_block .title { color:#FFF;}

#popup1 .popup_block .customer_content .customer_post{ color:#FFF;}
#popup1 .popup_block{ color:#FFF}
#popup1 .popup_block .customer_content .customer_title{ color:#ccce2a;}


.productName {
	width: 410px;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
}
.RightDivProductDetails span{ margin-left:0px !important;}
.div_left_part .order-details .order-products li::before{ margin-left:-35px;}
body .order-details .order_complete .approve:before{ display:none !important;}
.order_complete .approve { text-transform:capitalize; background:none !important; border:1px solid #fff; color:#FFF !important;}
.div_left_part .order-details .order-products li::before{border: 1px solid #fff;}



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
	padding: 5px 50px 5px 50px;
	margin-bottom: 9px;
	position: relative;
	border: 2px solid;}
.order_panding .customer-options{ text-align:left !important;}
.order_panding .ProductTItleRight a{color: #4f4f4f;}
ol.order-products .order_panding .customer-options .approve{ background:#00b050 !important;}
.order_panding .row_section,.order_complete .row_section{
    overflow: hidden;
}
.request_change .rq-view_rivision {
	background: #fff !important;
	color: red !important;
	display: inline-block;
	border-radius: 10px;
	padding: 5px 30px 5px 30px;
	margin-bottom: 9px;
	position: relative;
}
.request_change .pr-media-upload .custom-file{    width: 133px;}
.request_change .pr-media-upload .custom-file-control{ width:130px; font-size:16px;}
.request_change  .pr-media-upload button{ padding:5px 35px !important}
.request_change .row_section{ overflow:hidden;}
.request_change span.name_file{ color:#FFF;}
.order_panding{position:relative;}
.order_panding .pending_feedback_btn{margin-bottom:0px;}
.request_change{position:relative;}
.request_change .rq-view_rivision {margin-bottom:0px;}
.order_complete{position:relative;}
#popup1 .title {font-weight: 600;font-size: 24px;}
#popup1 .popup_block .customer_content .customer_title{color: #f4f62e;font-size: 19px;}
#popup1 .customer_content .popup-gallery{border:none;padding:0px;}
#popup1 .popup_block .customer_content .customer_post{font-size: 20px;
font-weight: 550;}
#popup1 .MessageText{font-size:17px;}


@media only screen and (max-width: 1199px) {
	.order_panding .customer-options, .request_change .customer-options{left: 39%;}
	.order_panding .custom-file-control{width: 145px !important;margin-left: 0px;}
	.order_panding .order-details span{margin-left: 0px;}
	.order_panding .pr-media-upload button.btn-blue{padding: 5px 13px !important;margin-left: 9px;}
	.request_change .custom-file-control{margin-left:0px;}
	.request_change .pr-media-upload button{padding: 5px 15px !important;margin-left: 2px;}
	.order_complete .customer-options{top: 46%;}
}
@media only screen and (max-width: 640px) {
	.request_change .customer-options, .order_panding .customer-options{width: 100%;display: inline-table;padding-top: 15px !important;position: initial;}
	.request_change .pr-media-upload .custom-file, .order_panding .custom-file.div_pandinlabel{margin-bottom:0px;}
	.order_complete .customer-options{width: 100% !important;padding-top: 15px !important;position: initial;}
}
#popup1 .customer_content .popup-gallery img{ object-fit: contain;}
.order_panding .rq-view_rivision{background: #444;padding: 3px 16px;background: none !important;border: 2px solid;text-align: center;border-radius: 10px;/*! background-color: #222; */color: #4f4f4f !important;display: inline-block;border-radius: 10px;padding: 2px 20px 2px 20px;margin-bottom: 9px;position: relative;margin-top: 10px;}
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
.ProductInstruction .customer_details ul.popup-gallery li { padding-left:0px !important; margin-bottom:5px !important;}
.ProductInstruction .customer_details ul.popup-gallery li a.WhiteText{ color:#FFF !important;}	


#popupBox .customer_content .galleryListing .deletedRevisionImages {
	text-align: center;
	display: block;
	font-size: 12px;
	color: #fff;
	margin-top: 10px;
	position: absolute;
	top: -23px;
	background: red;
	padding: 5px;
	right: -2px;
	border-radius: 50%;
	cursor: pointer;
	width: 21px;
}
#popupBox .ImageGalleryPopup .FilePopupBefore{border-radius: 50px;
background: #ededed;
color: #262626;
border: none;
width: 170px;
text-align: center;
margin-top: -28px;
padding: 5px 0px;font-size: 18px;
font-weight: bold;cursor: pointer;}
#popupBox .ImageGalleryPopup .FilePopupBefore:lang(en):empty::after{content: "Choose file...";}
#popupBox .ImageGalleryPopup .FileUploadPopup{min-width: 14rem;
max-width: 100%;
height: calc(2.25rem + 2px);
margin: 0;
opacity: 0;}
#popupBox .ImageGalleryPopup .galleryListing{vertical-align: top;}
#popupBox .ImageGalleryPopup .galleryListing img{height: 100px;margin-top:6px;}
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
.heightwidth_50 {
	width: 50px;
	object-fit: contain;
	height: 50px;
}
</style>

</head>



<body class="WorkDetaisPage">

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

            <h1 class="page-heading mb-4">Order #<?=$OrderUID;?></h1>



            <div class="row mt-5">

                <div class="col-lg-6 brd-lg-right pr-lg-4 pl-lg-4">

                    <h2 class="blue text-center mb-4">Order details</h2>

                    <div class="mb-4 order-details">

                        <p>Order#:</p>

                        <h3><?=$OrderUID;?></span></h3>

                    </div>

                    <div class="mb-4 order-details">

                        <p>Ordered on:</p>

                        <h3><?=date('D M d, Y',$OrderDate);?> <span><?=date('g:i A',$OrderDate);?></span></h3>

                    </div>

                    <div class="mb-4 order-details">

                        <p>Assigned on:</p>

                        <h3><?=date('D M d, Y',$AssginDate);?> <span><?=date('g:i A',$AssginDate);?></span></h3>


<?php
                            $getRespnseCode = array();
                            $orderStatusReview = 0;
                            foreach($OrderData as $k=>$v){
                                $getRespnseCode[$v['ResponseState']] = $v['ResponseState'];
                            }
							$startTimer = "yes";
 							if($OrderData[0]['is_approve'] == 1){
                            	 $orderStatusReview = 1;
								 $startTimer = "no";
                            }else{

                           
						  if($AssignTo==0 && $OrderStatus==4 ) {
								$startTimer ="no";
							}else if($AssignTo==0 &&  $OrderStatus==0) {
                             	$startTimer = "no";
                            }else if($AssignTo>0 && $OrderStatus==1 && in_array(3,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(2,$getRespnseCode)) {
                             }else if($AssignTo>0 && $OrderStatus==1 && in_array(1,$getRespnseCode)  && !in_array(2,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
								$startTimer = "no";
							
                             
                            }
							}


                            ?>
                   
                    </div>
                    

                    <div class="mb-4 order-details">

                        <p>Product(s) in this order:</p>

                        <ol class="order-products">

                            <?php
							$allowed =  array('gif','png' ,'jpg','webp');
							
                            $totalPrice = 0;
                            $mainProductName = "";
                           $j=0;
						   
						   $getMotionsArray  = array();
						   foreach($OrderData as $OrderList) { 
						   $TypeBanner = explode(",",$OrderList['TypeBanner']);
								if(!empty($TypeBanner)){
									$getMotionsArray[$OrderList['ProductID']]	=$TypeBanner;
								}   
							 }
						    foreach($OrderData as $OrderList) {
								
								$product=GetSglRcrdOnCndi(PRODUCT, "  id=".$OrderList['ProductID']);
								$Productslug = $product['slug'];	
                                if(!isset($ProductArr[$OrderList['ProductID']])) { continue; }


                                $listOSArr = explode(",",$OrderList['OtherSize']);
                                $Dimensional = $OrderList['Dimensional'];
                                $TypeBanner = explode(",",$OrderList['TypeBanner']);
                                $TurnAroundTime = $OrderList['TurnAroundTime'];
								

                                $listOSStr = $TypeBannerListStr = "";
								

                                if($ProductArr[$OrderList['ProductID']]['Addon']==0){
                                    if(!empty($listOSArr)) {


                                        foreach($listOSArr as $osListArr) {
                                            
											if($ProductSize[$osListArr]['name']!=""){
                                             
											    $listOSStr .= "(".$ProductSize[$osListArr]['name'].")";
                                            }

                                        }

                                    }
                                }
								if(!empty($getMotionsArray) && $OrderList['parent_product_id']>0){
									
									if(in_array("motion",$getMotionsArray[$OrderList['parent_product_id']]) && in_array("Use my music",$getMotionsArray[$OrderList['parent_product_id']])){
										$TypeBanner[] = "motion with music";
									}else{
										if(in_array("motion",$getMotionsArray[$OrderList['parent_product_id']])){
											$TypeBanner[] = "motion";
										}
										
									}
								}
                                if(!empty($TypeBanner)){
                                    foreach($TypeBanner as $TypeBannerList) {
										if($TypeBannerList!=""){
                                        $TypeBannerListStr .= "(".ucfirst($TypeBannerList).")";
										}

                                    }
                                }
								$j++;
								
								if($OrderList['parent_product_id']>0){
									 $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$OrderList['parent_product_id']."' AND `filetype` = 'image'","id","ASC");
									  $imgsrc ="";
									 
									   $imgsrc = productImageSrc($getBanners['filename'],$OrderList['parent_product_id'],'354','class="heightwidth_50"')."&nbsp;&nbsp";
									    
											$productDisplayName = "<div class='LeftDivProductDetails'>".$imgsrc."</div><div class='RightDivProductDetails'><div class='productName'>".$ProductArr[$OrderList['parent_product_id']]['Title']." ".$ProductArr[$OrderList['ProductID']]['Title']."</div>";	
										
								}else{
									
									  $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$OrderList['ProductID']."' AND `filetype` = 'image'","id","ASC");
									  $imgsrc ="";
									     $imgsrc = productImageSrc($getBanners['filename'],$OrderList['ProductID'],'354','class="heightwidth_50"')."&nbsp;&nbsp";
									
                                      
									if($ProductArr[$OrderList['ProductID']]['Addon']==1){
										
											$productDisplayName = "<div class='LeftDivProductDetails'>".$imgsrc."</div><div class='RightDivProductDetails'><div class='productName'>".$ProductArr[$OrderList['ProductID']]['Title']."</div>";	
										
									}else{
									$productDisplayName = "<div class='LeftDivProductDetails'><a target='_blank' href=".SITEURL."p/".$Productslug." style='color:#4f4f4f;text-decoration:none'>".$imgsrc."</a></div><div class='RightDivProductDetails'><div class='productName'><a target='_blank' href=".SITEURL."p/".$Productslug." style='color:#4f4f4f;text-decoration:none'>".$ProductArr[$OrderList['ProductID']]['Title']."</a></div>";	
									}
									
								}

                                ?>

                                <li><?=$productDisplayName;?>

                                    <div class="size">
									<?php 
									
									if(empty($OrderList['customeProductsFileds']) &&  ($OrderList['template_type']=="customize" || $OrderList['template_type']=="")){?>
									<?php echo ($Dimensional!="") ? "(".$Dimensional.")":"";
                                        echo ($TypeBannerListStr!="")? $TypeBannerListStr:"";;
                                        echo ($TurnAroundTime!="" && $TurnAroundTime>0) ? "(".getTurnArroundTypeByID($TurnAroundTime).")":"";
                                        ?><?php if(!empty($ProductSize[$OrderList['DefaultSize']]['name'])) { echo "(".$ProductSize[$OrderList['DefaultSize']]['name'].")"; }  
										if(!empty( $listOSStr)) { echo $listOSStr; } ?></div>
                                    <?php
                                    if(empty($mainProductName)){
                                        $mainProductName = $ProductArr[$OrderList['ProductID']]['Title'];
                                    }
                                    ?>
                                     <?php }
									  if(!empty($OrderList['customeProductsFileds'])){
										  echo ($TurnAroundTime!="" && $TurnAroundTime>0) ? "(".getTurnArroundTypeByID($TurnAroundTime).")":"";				  
									  }
									 if(($OrderList['template_type']=="customize" || $OrderList['template_type']=="")){
									  ?>
                                    
                                     <span class="type approve2 view_details" onClick="OpenInstructionsProduct('<?php echo $j; ?>')"><a>View details</a></span> 
                                     <?php } ?>
                                     
                                       <?php
										 	if($OrderList['template_type']=="psd" && $OrderList['psd_file']!="" && $AssignTo>0 &&  $OrderStatus>0 && $OrderStatus!=4){
												if($OrderList['psd3dtitle']=="Yes"){
													echo "<div style='margin-bottom:5px; display:block;font-size:16px;'>3D Title</div>";	
												}
											?>
                                            <a class="downloadPSD btn" data-type='designer' data-id='<?php echo $OrderList['Id']; ?>' href="javascript:void(0)" data-name="<?php echo "uploads/".$OrderList['ProductID']."/".$OrderList['psd_file']; ?>" title="Download" class="btn btn-primaray" style="background: #0070c0;
color: #fff;" >Download PSD</a>
                                            <?php 	
											}
										  ?>
                                    <div id="ProductInstruction<?php echo $j; ?>" class="popup ProductInstruction">

    <div class="popup-box">

        <div class="popup-middle">

            <div class="popup_block">

                <h4 class="title text-center mb-4"><strong class="prodTitle"><?php echo $productDisplayName; ?></strong> <span class="close-btn float-right"><i class="fa fa-times"></i></span></h4>

                <div class="popupfull_content">

                    <div class="customer_content">
                        <?php
						$MainTitle = $OrderList['MainTitle'];
						$Subtitle = $OrderList['Subtitle'];
						$Mixtapename = $OrderList['Mixtapename'];
						$Singletitle = $OrderList['Singletitle'];
						$deejay_name = $OrderList['deejay_name'];
						$presenting = $OrderList['presenting'];
						
						$ename = $OrderList['ename'];
						$EventDate = $OrderList['EventDate'];
						$MusicBy = $OrderList['MusicBy'];
						$ownSong = $OrderList['own_song'];
						$Venue  = $OrderList['Venue'];
						$Address = $OrderList['Address'];
						$MoreInfo = $OrderList['MoreInfo'];
						$requirement_note = $OrderList['requirement_note'];
						$venue_logo = $OrderList['venue_logo'];
						$ArtistName = $OrderList['ArtistName'];
						$ProducedBy = $OrderList['ProducedBy'];
						$ProductTypeOrder = $OrderList['ProductType'];
						$PhoneNumber = $OrderList['PhoneNumber'];
						$VenueEmail = $OrderList['VenueEmail'];
						$Facebook = $OrderList['Facebook'];
						$Instagram = $OrderList['Instagram'];
						$Twitter = $OrderList['Twitter'];
						$Music = $OrderList['Music'];
						
						if(!empty($OrderList['customeProductsFileds'])){
							
							$customeProductsFileds = unserialize($OrderList['customeProductsFileds']);
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
															
														if($customeProductFieldsKey=="flyer_design" || $customeProductFieldsKey=="3d_logo_conversion" || $customeProductFieldsKey=="laptop_skin" || $customeProductFieldsKey=="facebook_cover"  || $customeProductFieldsKey=="business_card" || $customeProductFieldsKey=="logo" || $customeProductFieldsKey=="logo_intro" || $customeProductFieldsKey=="animated_flyer"  || $customeProductFieldsKey=="mixtape_cover_design"){
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

														if(! empty( $files )){
															
															
															$uploadedFileType = strtolower(pathinfo($files,PATHINFO_EXTENSION));
															 
															echo '<div class="ImagesCustom">';
															if($uploadedFileType=="jpg" || $uploadedFileType=="jpeg" || $uploadedFileType=="png" || $uploadedFileType=="gif"){
															$fileCustomeUrl = customProductImageURL($files);
															
																echo '<a href="'.$fileCustomeUrl.'" class="text-white magnificPopup WhiteText"><img src="'.$fileCustomeUrl.'" width="50" height="50"></a>';
															}else{
																echo '<a href="'.$fileCustomeUrl.'" target="_blank" download class="text-white WhiteText"><i class="fa fa-download"></i><br></a>';	
															}
															echo '</div>';
														 } 

														}
								    					echo '</div>';
														}
															
														//echo "filedsIndex".$filedsIndex;	
														if($Filedsvalue!=""  &&  $filedsIndex!="music_file" && $filedsIndex!="defaultSize" && $filedsIndex!="otherSize"  && $filedsIndex!="attach_any_logos" && $filedsIndex!="attach_your_logo_design" && $filedsIndex!="vector_psd_pdf" && $filedsIndex!="attach_any_pictures"  && $filedsIndex!="attach_any_style_reference"  && $filedsIndex!="files" && $filedsIndex!="attach_logo"){	
													 	
														echo  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> '.$Filedsvalue.'</span></div>';
														}
														else if($filedsIndex=="defaultSize" && !empty($Filedsvalue)){
															
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
                                <strong>Own song: </strong> <a href="<?=SITEURL.$ownSong;?>" target="_blank">Download</a>
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
                                <strong>Notes to graphic designer: </strong>
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
						
						$filesImages = $OrderList['filesImages'];
						$filesImagesArr = array();
						if(!empty($filesImages)){
								$filesImagesArr = unserialize($OrderList['filesImages']);
						}
						
						if(!empty($filesImagesArr)) {?>
                            <div class="customer_details">
                                <strong>Photos and logos: </strong>
                                <ul class="list-unstyled mb-0 pl-4 popup-gallery">

                                    <?php 
										
									foreach($filesImagesArr as $singleImageKey=>$singleImageValue){
									if(file_exists( SITE_BASE_PATH . $singleImageValue )){  ?>
                                    
                                    		
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
                                    <?php } } ?>

                                    
                                </ul>
                            </div>
                        <?php
						}
						 } ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<?php if($startTimer=="yes" && ($OrderList['template_type']=="customize" || $OrderList['template_type']=="")){ ?>

 <div id="timerCountdown<?php echo $OrderList['Id']; ?>"></div>
                                    	<?php 
										
										
										 if(!empty($OrderList['customeProductsFileds'])){
											 
											$customeProductsFiledsTimer = multidimation_to_single_array(unserialize($OrderList['customeProductsFileds']));
				
											$timerDelHours=$customeProductsFiledsTimer['turnaround_time'];	
				
											 
										 }else{
												$timerDelHours = $OrderList['TurnAroundTime']; 
										  }
									
										
										
										
										?>


                                    
                                    <script>
                                        jQuery(document).ready(function(e) {
                                    jQuery('#timerCountdown<?php echo $OrderList['Id']; ?>').dateTimer(
                                              {
                                                date:'<?php echo date('d-m-Y H:i:s',$AssginDate); ?>', // day-month-year HH:MM:SS
                                                hours:<?php echo getTimerCounterHours($timerDelHours); ?>,
                                                endMessage:'<div class="time-cell timer-hour"><div class="timerDigits"><div class="timerLabel">Hours</div><div class="digitWiseCount">0</div><div class="digitWiseCount">0</div></div></div><div class="time-cell timer-minute"><div class="timerDigitsPoint">:</div><div class="timerDigits"><div class="timerLabel">Minutes</div><div class="digitWiseCount">0</div><div class="digitWiseCount">0</div></div></div><div class="time-cell timer-second"><div class="timerDigitsPoint">:</div><div class="timerDigits"><div class="timerLabel">Seconds</div><div class="digitWiseCount">0</div><div class="digitWiseCount">0</div></div></div>'
                                              }
                                            );	
											 });
										 </script>	
                                        <?php } ?>

												
                                </li>

                            <?php }
							$j=0;
                            ?>

                        </ol>

                    </div>

                </div>

                <div class="col-lg-6 pr-lg-1 pl-lg-4 div_left_part">

                    <h2 class="blue text-center mb-4">Media</h2>

                    <div class="order-ntf red">

                        <strong>Upload completed files for each product below</strong>

                    </div>

                    <div class="mb-4 order-details">

                        <ol class="order-products rightSideDesing">

                            <?php

                              $allowed =  array('gif','png' ,'jpg','webp');
                              $NewOrderArray = array();
							  
							foreach($OrderData as $OrderList) {
									if (strpos($OrderList['TypeBanner'], 'static') !== false) {
										if($OrderList['DefaultSize']!=""){
											$NewOrderArray[$OrderList['Id']][$OrderList['ProductID']][$OrderList['DefaultSize']] = $OrderList['DefaultSize'];	
										}
										if($OrderList['OtherSize']!=""){
											 $listOSArr = explode(",",$OrderList['OtherSize']);
											   if($ProductArr[$OrderList['ProductID']]['Addon']==0){
                                    if(!empty($listOSArr)) {
									 foreach($listOSArr as $osListArr) {
                                            if($ProductSize[$osListArr]['name']!=""){
												$NewOrderArray[$OrderList['Id']][$OrderList['ProductID']][$osListArr] = $osListArr;	
                                            }

                                        }

                                    }
                                }
								
								
								
											 
											
										}
									}
	
							}
							
							foreach($OrderData as $OrderList) {

                                $prod = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$OrderList['ProductID']."'");
								$Productslug = $prod['slug'];
                              
                               if($OrderList['parent_product_id']>0){
									$productDisplayName = $ProductArr[$OrderList['parent_product_id']]['Title']." ".$ProductArr[$OrderList['ProductID']]['Title'];
										
								}else{
									if($prod['Addon']==1){
										
											$productDisplayName = $ProductArr[$OrderList['ProductID']]['Title'];	
										
									}else{
									$productDisplayName = '<a href="'.SITEURL.'p/'.$Productslug.'" target="_blank">'.$ProductArr[$OrderList['ProductID']]['Title']."</a>";	
									}
								}
								
								
								$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");
								
								$imgsrc = "";
								$imgurl = "";
									if($OrderList['parent_product_id']>0){
									
										
								}else{
									 $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$OrderList['ProductID']."' AND `filetype` = 'image'","id","ASC");
									  $imgsrc ="";
									  
									   $imgsrc = productImageSrc($getBanners['filename'],$OrderList['ProductID'],'354')."&nbsp;&nbsp";
									  
									
									$imgurl = "uploads/products/".$OrderList['ProductID'].'/'.$getBanners['filename'];
                                      
								
								}
								
								$ChangeRequest = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "DESC");
								
								$getAttachment = GetSglDataOnCndiWithOrderBy(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND `ProductID` = '".$OrderList['ProductID']."' and Type='admin'", "Attachment", "ID", "DESC");
								
								
								
								if(!empty($getAttachment['Attachment'])){
									$ImageUrlData = unserialize($getAttachment['Attachment']);	
									if(!empty($ImageUrlData)){
										$imgurl = OrderImageURL($ImageUrlData[0]);
										
										$ext = pathinfo($ImageUrlData[0], PATHINFO_EXTENSION);
											
											
											if(in_array(strtoupper($ext),$allowedVideo)) {
												
												$imgsrc ='<video width="130" height="200" controls>
                                                             <source src="'.$imgurl.'" type="video/mp4">
                                                        </video>';
											}else if(in_array($ext,$allowed)){
													 
										
												$imgsrc ='<a href="'.$imgurl.'" class="magnificPopup">'.OrderImageSrc($ImageUrlData[0],"354").'</a>';
											}
											else{
													 
										
												$imgsrc ='<a href="'.$imgurl.'" download><img  src="'.SITEURL.'/images/download_file.png"></a>';
											}
									}
								}
								
							   

                                    //Submit

                                    ?>
                                    <li>


<h3 class="ProductTItleRight" style="font-size:22px;"><?= $productDisplayName;?>:</h3>
                                  
                                       
                                        <?php
										
										
										 if(!empty($NewOrderArray[$OrderList['Id']][$OrderList['ProductID']])){
											?>
                                            <span class="MultiPlayVartion">Multiple variations</span>
                                            <?php 
											foreach($NewOrderArray[$OrderList['Id']][$OrderList['ProductID']] as $singleSize){
												
												$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");
												if(!empty($selectChangeRequest)){
													
												$ChangeRequest = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."' ", "ID", "DESC");
												
												$getAttachment = GetSglDataOnCndiWithOrderBy(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."'  and Type='admin'", "Attachment" , "ID", "DESC");
								
												
												
												$CheckLastRevision = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "DESC");
								if(!empty($getAttachment['Attachment'])){
									$ImageUrlData = unserialize($getAttachment['Attachment']);	
									if(!empty($ImageUrlData)){
										$imgurl = OrderImageURL($ImageUrlData[0]);
										
										
										$ext = pathinfo($ImageUrlData[0], PATHINFO_EXTENSION);
												if(in_array(strtoupper($ext),$allowedVideo)) {
												
												$imgsrc ='<video width="130" height="200" controls>
                                                             <source src="'.$imgurl.'" type="video/mp4">
                                                        </video>';
											}else if(in_array($ext,$allowed)){
													 
										
												$imgsrc =' <div class="popup-gallery"><a href="'.$imgurl.'" class="magnificPopup">
												'.OrderImageSrc($ImageUrlData[0],"354").'</a></div>';
											}
											else{
													 
										
												$imgsrc ='<a href="'.$imgurl.'" download><img  src="'.SITEURL.'/images/download_file.png"></a>';
											}
									}
								}
													
												?>
                                                
                                                <div class="StaticType <?php if($CheckLastRevision['ResponseState']==3) {?>request_change<?php }else if($CheckLastRevision['ResponseState']==1){?>order_complete<?php }else{ ?>order_panding<?php } ?>">
     
                                               <div class="customer-media">

                                            <h3 class="SubtitleProduct">Static - <?php echo $ProductSize[$singleSize]['name']; ?></h3>
                                   

<div class="row_section">

                                                <div class="customer-image mr-3">

                                                               
                                                                   
                                                                    <?php echo $imgsrc; ?>
                                                                  
                                                             

                                                            </div>
												
                                                <div class="customer-options">
															<?php if($ChangeRequest['ResponseState']==1){ ?>	
															 <a href="javascript:void(0)" class="approve"  style="background: #1baa32;">approved</a>
                                                        <?php } ?>
                                                         <a class="rq-view_rivision changeReq" data-type='static' data-size='<?php echo $singleSize; ?>' data-id="<?=$OrderList['Id'];?>" style="background: #444;padding: 3px 16px;">View revisions(<?php echo count($selectChangeRequest) ?>)</a>
                                                               
                                                    <form method="post" class="pr-media-upload mt-4" enctype="multipart/form-data">

                                                        <label class="custom-file div_pandinlabel">

                                                               <input type="file" name="filename[]" multiple id="file" class="custom-file-input">

                                                <input type="hidden" name="orderIID" value="<?=$OrderList['Id'];?>" />
                                                <input type="hidden" name="ProductID" value="<?=$OrderList['ProductID'];?>" />
                                                 <input type="hidden" name="TypeBanner" value="static" />
                                                <input type="hidden" name="size" value="<?php echo $singleSize; ?>" >
                                                 <input type="hidden" name="ResponseState" value="<?php echo $ChangeRequest['ResponseState']; ?>" >

                                                            <span class="custom-file-control"></span><span class="name_file"></span>

                                                        </label>

                                                        <button type="submit" class="btn-blue" name="uploadfile">Upload</button>

                                                    </form>

                                                </div>
                                                
                                                

                                            </div>
                                            </div>
                                        </div>
                                                
                                                <?php 	
												}else{
													?>
                                                    
                                                   <div class="StaticType UploadDesing">
                                                    <div class="customer-media">

                                                <h3 class="SubtitleProduct">Static - <?php echo $ProductSize[$singleSize]['name']; ?></h3>
                                                <form method="post" class="pr-media-upload mt-5 ml-sm-4" enctype="multipart/form-data">

                                            <label class="custom-file">

                                                <input type="file" name="filename[]" multiple  id="file" class="custom-file-input">

                                                <input type="hidden" name="orderIID" value="<?=$OrderList['Id'];?>" />
                                                <input type="hidden" name="ProductID" value="<?=$OrderList['ProductID'];?>" />
                                                <input type="hidden" name="TypeBanner" value="static" />
                                                <input type="hidden" name="size" value="<?php echo $singleSize; ?>" />
                                                

                                                <span class="custom-file-control"></span><span class="name_file"></span>

                                            </label>

                                            <button type="submit" class="btn-blue" name="uploadfile">Upload</button>

                                        </form></div>
                                        </div>
                                                    <?php 	
												}
												?>
                                                  
                                                <?php 
													
											}
											
										}else{ 
										if(!empty($selectChangeRequest)){
											
											$CheckLastRevision = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND `ProductID` = '".$OrderList['ProductID']."' and Size='0' and TypeBanner='default'", "ID", "DESC");
											
											
											?>
                                        
                                          <div class="SimpleType <?php if($CheckLastRevision['ResponseState']==3) {?>request_change<?php }else if($CheckLastRevision['ResponseState']==1){?>order_complete<?php }else{ ?>order_panding<?php } ?>">
     
                                               <div class="customer-media">

                                        
<div class="row_section">

                                                <div class="customer-image mr-3">

                                                                <div class="popup-gallery">
                                                                   
                                                                    <?php echo $imgsrc; ?>
                                                                  
                                                                </div>

                                                            </div>

                                                <div class="customer-options">



<?php /*?> <?php if($OrderList['ResponseState']  ==  3){ ?>
                                                               <div class="pending_feedback_btn">Pending feedback</div>
                                                               <?php } ?><?php */?>
                                                                 <?php if($ChangeRequest['ResponseState']==1){ ?>	
															 <a href="javascript:void(0)" class="approve"  style="background: #1baa32;">approved</a>
                                                        <?php } ?>
                                                              
                                                                 <a class="rq-view_rivision changeReq" data-id="<?=$OrderList['Id'];?>" data-type='' data-size=''  style="background: #444;padding: 3px 16px;">View revisions(<?php echo count($selectChangeRequest) ?>)</a>
                                                               
                                                    <form method="post" class="pr-media-upload mt-4" enctype="multipart/form-data">

                                                        <label class="custom-file div_pandinlabel">

                                                               <input type="file" name="filename[]"  multiple id="file" class="custom-file-input">

                                            
                                             
                                             <input type="hidden" name="ResponseState" value="<?= $ChangeRequest['ResponseState'];?>" />
                                                <input type="hidden" name="orderIID" value="<?=$OrderList['Id'];?>" />
                                                <input type="hidden" name="ProductID" value="<?=$OrderList['ProductID'];?>" />
                                                 <input type="hidden" name="TypeBanner" value="" />
                                                <input type="hidden" name="size" value="" >

                                                            <span class="custom-file-control"></span><span class="name_file"></span>

                                                        </label>

                                                        <button type="submit" class="btn-blue" name="uploadfile">Upload</button>

                                                    </form>
                                       
                                                </div>
                                                
                                                

                                            </div>
                                            </div>
                                        </div>
                                        <?php 
                                        }else{
										?>	
                                      <div class="SimpleType UploadDesing">
                                                    <div class="customer-media">

                                        <form method="post" class="pr-media-upload mt-5 ml-sm-4" enctype="multipart/form-data">

                                            <label class="custom-file">

                                                <input type="file" name="filename[]"  multiple id="file" class="custom-file-input">

                                                <input type="hidden" name="orderIID" value="<?=$OrderList['Id'];?>" />
                                                <input type="hidden" name="ProductID" value="<?=$OrderList['ProductID'];?>" />
                                                 <input type="hidden" name="TypeBanner" value="" />
                                                <input type="hidden" name="size" value="" />
                                               
                                                

                                                <span class="custom-file-control"></span><span class="name_file"></span>

                                            </label>

                                            <button type="submit" class="btn-blue" name="uploadfile">Upload</button>

                                        </form>
                                        </div></div>
                                        <?php } } ?>
                                        <?php
										//motioon
										 if (strpos($OrderList['TypeBanner'], 'motion') !== false && $ProductArr[$OrderList['ProductID']]['Addon']==0) { ?>
                                        <?php 
										$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND TypeBanner='motion' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");
												if(!empty($selectChangeRequest)){
													
												$ChangeRequest = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='motion'  AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "DESC");
									
									$getAttachment = GetSglDataOnCndiWithOrderBy(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='motion'  AND `ProductID` = '".$OrderList['ProductID']."'  and Type='admin'", "Attachment" , "ID", "DESC");
								
									
									if(!empty($getAttachment['Attachment'])){
									$ImageUrlData = unserialize($getAttachment['Attachment']);	
									if(!empty($ImageUrlData)){
										$imgurl = OrderImageURL($ImageUrlData[0]);
										
										$ext = pathinfo($ImageUrlData[0], PATHINFO_EXTENSION);
											if(in_array(strtoupper($ext),$allowedVideo)) {
												
												$imgsrc ='<video width="130" height="200" controls>
                                                             <source src="'.$imgurl.'" type="video/mp4">
                                                        </video>';
											}else if(in_array($ext,$allowed)){
													 
										
												$imgsrc ='<a href="'.$imgurl.'" class="magnificPopup">'.OrderImageSrc($ImageUrlData[0],"354").'</a>';
											}
											else{
													 
										
												$imgsrc ='<a href="'.$imgurl.'" download><img  src="'.SITEURL.'/images/download_file.png"></a>';
											}
									}
								}
								$CheckLastRevision = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='motion'  AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "DESC");
													
												?>
                                                
                                             <div class="MotionType <?php if($CheckLastRevision['ResponseState']==3) {?>request_change<?php }else if($CheckLastRevision['ResponseState']==1){?>order_complete<?php }else{ ?>order_panding<?php } ?>">
     
                                               <div class="customer-media">

                                            <h3 class="SubtitleProduct">Motion</h3>
                                   

<div class="row_section">

                                                <div class="customer-image mr-3">

                                                                <div class="popup-gallery">
                                                                   
                                                                   <?php echo $imgsrc; ?>
                                                                  
                                                                </div>

                                                            </div>

                                                <div class="customer-options">



<?php /*?> <?php if($OrderList['ResponseState']  ==  3){ ?>
                                                               <div class="pending_feedback_btn">Pending feedback</div>
                                                               <?php } ?><?php */?>
                                                             
                                                               <?php if($ChangeRequest['ResponseState']==1){ ?>	
															 <a href="javascript:void(0)" class="approve"  style="background: #1baa32;">approved</a>
                                                        <?php } ?>
                                                             
                                                              
                                                                                                                              <a class="rq-view_rivision changeReq" data-type='motion' data-size=''  data-id="<?=$OrderList['Id'];?>" style="background: #444;padding: 3px 16px;">View revisions(<?php echo count($selectChangeRequest) ?>)</a>
                                                             
                                                    <form method="post" class="pr-media-upload mt-4" enctype="multipart/form-data">

                                                        <label class="custom-file div_pandinlabel">
                                                             <input type="hidden" name="ResponseState" value="<?php echo $ChangeRequest['ResponseState']; ?>" >

                                                               <input type="file" name="filename[]" multiple  id="file" class="custom-file-input">

                                                <input type="hidden" name="orderIID" value="<?=$OrderList['Id'];?>" />
                                                <input type="hidden" name="ProductID" value="<?=$OrderList['ProductID'];?>" />
                                                 <input type="hidden" name="TypeBanner" value="motion" />
                                                <input type="hidden" name="size" value="" />
                                                

                                                            <span class="custom-file-control"></span><span class="name_file"></span>

                                                        </label>

                                                        <button type="submit" class="btn-blue" name="uploadfile">Upload</button>

                                                    </form>
                                                
                                                </div>
                                                
                                                

                                            </div>
                                            </div>
                                        </div>
                                                
                                                <?php 	
												}else{
													?>
                                                    
                                                    <div class="MotionType UploadDesing">
                                                    <div class="customer-media">

                                                <h3 class="SubtitleProduct">Motion</h3>
                                                <form method="post" class="pr-media-upload mt-5 ml-sm-4" enctype="multipart/form-data">

                                            <label class="custom-file">

                                                <input type="file" name="filename[]" multiple   id="file" class="custom-file-input">

                                                <input type="hidden" name="orderIID" value="<?=$OrderList['Id'];?>" />
                                                <input type="hidden" name="ProductID" value="<?=$OrderList['ProductID'];?>" />
                                                <input type="hidden" name="TypeBanner" value="motion" />
                                                <input type="hidden" name="size" value="" />
                                                

                                                <span class="custom-file-control"></span><span class="name_file"></span>

                                            </label>

                                            <button type="submit" class="btn-blue" name="uploadfile">Upload</button>

                                        </form>
                                        </div>
                                        </div>
                                                    <?php 	
												}
												?>
                                        <?php } ?>

                                </li>
                                    <?php

                              

                            } ?></ol>

                    </div>

                </div>

            </div>

        </div>





    </div>

</main>



<div id="popup1" class="popup">

    <div class="popup-box">

        <div class="popup-middle">

            <div class="popup_block">

                <h4 class="title text-center mb-4"><font class="revision_title">Customer request </font><span class="close-btn float-right"><i class="fa fa-times" <="" span=""></i></span></h4>

                <div class="popupfull_content" id="popupBox">

                    <div class="customer_content">

                        <div class="customer_post">

                            Posted: Today 9/23/2018 <span>11:18am</span>

                        </div>

                        <div class="customer_title">

                            For Night Club

                        </div>

                        <div class="customer_desc">

                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit.

                            <span class="customer_icon"><i class="fas fa-image pr-2"></i> image.jpg</span>

                        </div>

                    </div>

                    <div class="customer_content active">

                        <div class="customer_post">

                            Posted: Today 9/23/2018 <span>11:18am</span>

                        </div>

                        <div class="customer_title">

                            For Night Club

                        </div>

                        <div class="customer_desc">

                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit.

                            <span class="customer_icon"><i class="fas fa-image pr-2"></i> image.jpg</span>

                        </div>

                    </div>

                    <div class="customer_content">

                        <div class="customer_post">

                            Posted: Today 9/23/2018 <span>11:18am</span>

                        </div>

                        <div class="customer_title">

                            For Night Club

                        </div>

                        <div class="customer_desc">

                            Lorem ipsum dolor sit amet, consectetuer adipiscing elit.

                            <span class="customer_icon"><i class="fas fa-image pr-2"></i> image.jpg</span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>





<?php require_once 'files/footerSection.php' ?>

<!----SCRIPTS---------->



<script src="js/popper.min.js"></script>

<script src="js/bootstrap.min.js"></script>

<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/timer.js"></script>

<script>
function OpenInstructions(id){
	jQuery(".AddonsInstruction,.ProductInstruction").removeClass("active");
	$("#AddonsInstruction"+id).addClass("active");
}

function OpenInstructionsProduct(id){
	jQuery(".AddonsInstruction,.ProductInstruction").removeClass("active");
	$("#ProductInstruction"+id).addClass("active");
}


 
    $(".close-btn").click(function(){

        $("#popup1").removeClass("active");

     jQuery(".AddonsInstruction,.ProductInstruction").removeClass("active");

    });



    $(".changeReq").on("click", function(){

        var orderID = $(this).data('id');
		   var type = $(this).data('type');
		   var size = $(this).data('size');

        $.ajax({

            type: "POST",

            url: "<?=SITEURL;?>ajax/change-request.php",

            data: "action=add&orderIID="+orderID+"&type="+type+"&size="+size,

            success: function(regResponse) {

                /*console.log(regResponse);*/

                regResponse = JSON.parse(regResponse);

                $("#popupBox").html(regResponse.message);
				$(".revision_title").html(regResponse.titleBlock);

                $("#popup1").addClass("active");

            }

        });

    });



    $('.popup-gallery').magnificPopup({

        delegate: '.magnificPopup',

        type: 'image',

        mainClass: 'mfp-img-mobile',

        gallery: {

            verticalFit: true

        },

    });



</script>
<script type="text/javascript">
    jQuery(document).ready(function(e) {

	
        jQuery(".custom-file-input").change(function () {

            jQuery('.pr-media-upload .custom-file').removeClass('activebox');
            jQuery(this).closest('.custom-file').addClass('activebox');

            jQuery('.activebox .name_file').html(this.files[0].name);
        });

    });
	
	 jQuery(document).on("click",".deletedRevisionImages",function(){
		 var thisBlock =   this;
        var  $ = jQuery;
		var id = $(this).data("id");
		var imagekey = $(this).data("imagekey");
		var confirmBox = confirm("Are you sure you want to delete this image?")
 		if(confirmBox==true){
        jQuery.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/delete-rivision-image.php",
            data: "id="+id+'&imagekey='+imagekey,
            success: function(regResponse) {
			 
			 jQuery(thisBlock).parent().remove();
                

            }
        });
		}


    });
	
	jQuery('body').on('change', '.FileUploadPopup', function(e){
		jQuery(".mainLoader").show();
        e.preventDefault();
        var formData = new FormData($(this).parents('form')[0]);
		var parentData = $(this).parents('form');
			var ImageGalleryPopup = $(this).parents('form').parents(".ImageGalleryPopup").children(".popup-gallery");

        jQuery.ajax({
            url: "<?=SITEURL;?>ajax/rivision-upload.php",
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            success: function (data) {
              //  alert("Data Uploaded: "+data);
			  jQuery(".mainLoader").hide();
			  jQuery(ImageGalleryPopup).empty();
			  
			  jQuery(data).insertBefore(parentData);
            },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
});
	
	jQuery(document).on("click",".downloadPSD",function(){
		var name =  jQuery(this).attr("data-name");
		var type =  jQuery(this).attr("data-type");
		var id =  jQuery(this).attr("data-id");
		
		window.location = "download.php?name="+name+"&type="+type+"&id="+id;
	});
	
</script>

</body>



</html>