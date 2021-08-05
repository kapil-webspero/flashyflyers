<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
require_once '../function/adminSession.php';


$PageTitle = "Orders Details";

if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])) {
    unset($_SESSION['ORDERUID']);
    $_SESSION['ORDERUID'] = intval($_REQUEST['order_id']);
}
if(!isset($_SESSION['ORDERUID']) && empty($_SESSION['ORDERUID'])) {
    echo "<script> window.location.href = '".ADMINURL."orders.php';</script>";
    exit();
}
$OrderUID  = intval($_SESSION['ORDERUID']);
  $price3D = $addonPrices['1'];
    $priceMotion = $addonPrices['2'];
   $priceown_music = $addonPrices['15'];

 if(!empty($_REQUEST['order_id']) && isset($_REQUEST['is_approve'])) {
        $order_id = intval($_REQUEST['order_id']);
        $is_approve = intval($_REQUEST['is_approve']);
        UpdateRcrdOnCndi(ORDER, "`is_approve` = '$is_approve'", "`TransactionID` = '$order_id'");
        if($is_approve == 1) {


			 $curl = curl_init();
        curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                                   CURLOPT_URL            => SITEURL . 'EmailTemplate/user_notify_for_review.php?order_id=' . $order_id,
                                   CURLOPT_USERAGENT      => 'Curl Test'] );
        $resp = curl_exec( $curl );
        curl_close( $curl );
            $_SESSION['SUCCESS'] = "Order approve successfully.";
        }else{
            $_SESSION['SUCCESS'] = "Order approve successfully.";
        }
        echo "<script> window.location.href = '".ADMINURL."order-details.php?order_id=".$OrderUID."';</script>";
        exit();
    }

		if(isset($_GET['paidorder']) && !empty($_GET['paidorder'])) {
		$orderid 		= $_GET['paidorder'];
		$transactionid 	= $_GET['transactionid'];
		    $query_user = "SELECT UserID FROM " . USERS . " WHERE UserType= 'designer'";
            $fetch_arr = mysql_query( $query_user );
            $usersArr = mysql_fetch_array( $fetch_arr );
        	$AssignedTo = $usersArr[0];
			$AssignedOn = $systemTime;

		 UpdateRcrdOnCndi( TRANSACTION, "`PaymentStatus` = 'success',`Status` = '1'", "`id` = '" . $transactionid."'" );
		   UpdateRcrdOnCndi(ORDER, "`AssignedTo` = '$AssignedTo', `AssignedOn` = '$AssignedOn', `OrderStatus` = '1'", "`TransactionID` = '$transactionid'");

		$ProjectIDSQuery = "SELECT Id FROM ".ORDER." where  TransactionID = '".$transactionid."'";
			$ProjectIDSQueryR = mysql_query($ProjectIDSQuery);
			while($ProjectIDSRow = mysql_fetch_array($ProjectIDSQueryR)){
				$projectOrderIdArray[] = $ProjectIDSRow['Id'];
			}


			$projectOrderId = implode(",",$projectOrderIdArray);
		/* Designer assigned mail */
		 $curl = curl_init();
        curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                                   CURLOPT_URL            => SITEURL . 'EmailTemplate/auto_assigned_order.php?designer_id=' . $AssignedTo . "&orderids=" . $projectOrderId,
                                   CURLOPT_USERAGENT      => 'Curl Test'] );
        $resp = curl_exec( $curl );
        curl_close( $curl );


		$_SESSION['SUCCESS'] = "Order Paid Successfully";
		echo "<script> window.location.href = '".ADMINURL."order-details.php?order_id=".$transactionid."';</script>";
		exit();
	}
if(!empty($_REQUEST['c_status']) && $_REQUEST['c_status'] > 0){
    $status = $_REQUEST['c_status'];
    UpdateRcrdOnCndi(PRODUCT_RATING_COMMENT,"`status` = '".$status."'", "`order_id` = '".$OrderUID."'");
    if($_REQUEST['c_status'] == 1) {
        $_SESSION['SUCCESS'] = "comment and rating approved successfully.";
    }else{
        $_SESSION['SUCCESS'] = "comment and rating reject successfully.";
    }
    echo "<script> window.location.href = '".ADMINURL."order-details.php?order_id=".$OrderUID."';</script>";
    exit();
}

$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '$OrderUID'", "Id", "ASC");
$UserID = $OrderData[0]['CustomerID'];
$OrderDate = $OrderData[0]['OrderDate'];
$AssignTo = $OrderData[0]['AssignedTo'];

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



if($AssignTo>0) {
    $AssignOn = $OrderData[0]['AssignedOn'];
    $DesignerData = GetSglRcrdOnCndi(USERS, "UserID = '$AssignTo'");
    $DesignerName = $DesignerData['FName']." ".$DesignerData['LName'];
}


if(isset($_POST['assignFBtn']) && !empty($_POST['assignFBtn'])) {
    extract($_POST);

    $result120 = mysql_query("SELECT * FROM ".USERS." where UserID='$assignedto'");
    $showData120 = mysql_fetch_array($result120);
    if($showData120['availability']=="no"){
        $_SESSION['ERROR'] = "At the moment user does not available for accepting order";
        echo "<script> window.location.href = '".ADMINURL."order-details.php';</script>";
        exit();
    }
    UpdateRcrdOnCndi(ORDER, "`AssignedTo` = '$assignedto', `AssignedOn` = '$systemTime', `OrderStatus` = '1'", "`TransactionID` = '$projectId'");
    $_SESSION['SUCCESS'] = "Project assigned to designer successfully.";
    echo "<script> window.location.href = '".ADMINURL."order-details.php';</script>";
    exit();
}
// ws #23 010521
if(isset($_POST['changeStatus']) && !empty($_POST['changeStatus'])) {
    extract($_POST);

    if(isset($_REQUEST['status']) && !empty($_REQUEST['status'])) {
      $status = $_REQUEST['status'];

      if ($status == 'revision') {
        UpdateRcrdOnCndi(ORDER, "`is_approve` = '0', `ResponseState` = '2'", "`TransactionID` = '$OrderUID'");
        $_SESSION['SUCCESS'] = "Order status successfully changed.";
      } elseif ($status =='process') {
          UpdateRcrdOnCndi(ORDER, "`is_approve` = '0', `ResponseState` = '4'", "`TransactionID` = '$OrderUID'");
          $_SESSION['SUCCESS'] = "Order status successfully changed..";
      } elseif ($status =='completed') {
          echo "<script> window.location.href = '".ADMINURL."order-details.php?order_id=".$OrderUID."&is_approve=1';</script>";
      }
      echo "<script> window.location.href = '".ADMINURL."order-details.php';</script>";
    }
    exit();
}

$UserData = GetSglRcrdOnCndi(USERS, "UserID = '$UserID'");
$ProductArr = getALLProductArr();
$AddonArr = getAddonArr();
$ProductSize = getProdSizeArr();
$ProductType = getProdTypeArr();
$UserArr = getUserArr();

if(isset($_REQUEST['submitRequest']) && !empty($_REQUEST['submitRequest'])) {
    extract($_POST);

    if(isset($_REQUEST['orderIID']) && !empty($_REQUEST['orderIID'])) {
        $orderIID = intval($_REQUEST['orderIID']);


        if(GetNumOfRcrdsOnCndi(ORDER, "`TransactionID` = '$OrderUID' AND `Id` = '$orderIID'")>0) {

            $ItemOrder = GetSglRcrdOnCndi(ORDER, "`TransactionID` = '$OrderUID' AND `Id` = '$orderIID' AND `CustomerID` = '".$OrderData[0]['CustomerID']."'", "Id");
            $original_name = "";;




                    $dynamicPath = "/";
                    $dynamic_dir = "../uploads/work/".$dynamicPath;
					$dynamic_dir_1 = "uploads/work/".$dynamicPath;
                    if(!file_exists($dynamic_dir)) {
                        mkdir($dynamic_dir, 0777, true);
                    }
					$attachementName = $filenameAttachThumb= array();
					if(!empty($_FILES['filename'])){
					foreach($_FILES['filename']['name'] as $postFileKey=>$postFileValue){

					$fileType = strtolower(pathinfo(basename($_FILES["filename"]["name"][$postFileKey]),PATHINFO_EXTENSION));
                  	$imageNameRand = rand(1, 10000000000);
				    $file_name = $imageNameRand.".".$fileType;
                    $target_file = $dynamic_dir ."/". $file_name;


					//thumb
				 $sourceProperties = getimagesize($_FILES["filename"]["tmp_name"][$postFileKey]);
				  $FileExt = pathinfo($_FILES["filename"]["name"][$postFileKey], PATHINFO_EXTENSION);
				  $thumbImageName = $imageNameRand;

				  if(strtolower($FileExt)=="jpg" || strtolower($FileExt)=="jpeg" || strtolower($FileExt)=="png" || strtolower($FileExt)=="gif"){
					  $thumbImage = ImageThumbCreate($sourceProperties,$FileExt,$_FILES["filename"]["tmp_name"][$postFileKey],$thumbImageName,SITE_BASE_PATH.$dynamic_dir_1,'work');
					}



					if (move_uploaded_file($_FILES["filename"]["tmp_name"][$postFileKey], $target_file))
					{
					digitalOceanUploadImage(SITE_BASE_PATH.$target_file,'work');

							$file_size = $_FILES["filename"]["size"][$postFileKey];

                        	$original_name = basename( $_FILES["filename"]["name"][$postFileKey]);
							$filename_arr = explode('.',$file_name);

							$upload = $imageNameRand.".".$FileExt;
							$uploadThumb =	array("large"=>$file_name,"small"=>$thumbImage);
							$attachementName[]  = $upload;
							if(strtolower($FileExt)=="jpg"  || strtolower($FileExt)=="webp" || strtolower($FileExt)=="webp"  || strtolower($FileExt)=="jpeg" || strtolower($FileExt)=="png" || strtolower($FileExt)=="gif"){
								$filenameAttachThumb[] =$uploadThumb;
							}

                    }
					}
					}

					$terms = $_REQUEST['terms'];
					if($terms==""){
						$terms = 0;
					}
					if($_REQUEST['BannerType']=="Static"){
						$ExtraInsert = " , TypeBanner='static',Size='".$_REQUEST['BannerSize']."' ";
					}
					if($_REQUEST['BannerType']=="Motion"){
						$ExtraInsert = " , TypeBanner='motion',Size='0'";
					}

					 if($_REQUEST['BannerType']==""){
							$ExtraInsert = " , TypeBanner='default',Size='0'";
					  }


		 InsertRcrdsByData(CHANGE_REQ,"`OrderID` = '$orderIID',`TransactionOrderID` = '$OrderUID',`ProductID` = '".$_REQUEST['ProductID']."',`UserID`='".$OrderData[0]['CustomerID']."', `AdminID`='$AccessID', `DesignerID`='".$ItemOrder['AssignedTo']."', `MessageText` ='".addslashes($pChanges)."', `Attachment`='".serialize($attachementName)."', `AttachmentThumb`='".serialize($filenameAttachThumb)."', `AttechmentName` = '".serialize($attachementName)."', `UserReadState` = '1', `CreationDate` = '$systemTime', `SystemIP` = '$systemIp', `24HoursChk` = '".$terms."' ,ResponseState='3' ".$ExtraInsert.", `Type` = 'super_admin'");

                UpdateRcrdOnCndi(ORDER, "`ResponseState` = '2'", "`TransactionID` = '$OrderUID' AND `Id` = '$orderIID' AND `CustomerID` = '".$OrderData[0]['CustomerID']."'");

			    $curl = curl_init();
                // Set some options - we are passing in a useragent too here
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => SITEURL.'EmailTemplate/workResponse.php?OID='.$orderIID."&reset=".$reset,
                    CURLOPT_USERAGENT => 'Curl Test'
                ));


                // Send the request & save response to $resp
                $resp = curl_exec($curl);
                // Close request to clear up some resources
                curl_close($curl);



            $_SESSION['SUCCESS'] = "Request for changes is successfully submited.";
        } else {
            $_SESSION['ERROR'] = "Order item not assigned under your account.";
        }
    } else {
        $_SESSION['ERROR'] = "Invalid Action";
    }

}


?>
<!DOCTYPE html>
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php include "includes/head.php"; ?>
    <link rel="stylesheet" href="../css/jquery.artarax.rating.star.css">
    <link rel="stylesheet" href="../css/style-2.css">
    <script src="js/jquery.js"></script>
    <style>
.editInformation{position: fixed;margin-top: -58px;}
/* Admin order details page */
.order-products .order_complete .customer-media, ol.order-products .order_complete .customer-options .approve {
    background-color: green;
}
.order-details li:nth-child(3)::before {
    background-color: #00b050;
}
.order-details li:nth-child(1)::before {
    background-color: #7030a0;
}
ol.order-products .order_panding::before, ol.order-products .order_complete::before, ol.order-products .order_change::before, ol.order-products .request_change::before {
    margin-left: 15px !important;
    top: 42px;
    display: block !important;
    position: relative;
}
.order_complete::before {
    background-color: #1baa32 !important;
}

.order_panding::before {

    background-color: #000 !important;

}

ol.order-products .request_change, ol.order-products .order_panding, ol.order-products .order_complete, ol.order-products .order_change, ol.order-products .request_change {

    padding-left: 0 !important;
    font-size: 15px;

}
.order-details li {

    font-size: 22px;
    font-weight: 700;
    margin-bottom: 14px;
    counter-increment: step-counter;
    padding-left: 35px;
    color: #4f4f4f;

}
ol.order-products .order_panding .customer-media .customer-image, ol.order-products .order_complete .customer-media .customer-image, ol.order-products .order_change .customer-media .customer-image, ol.order-products .request_change .customer-media .customer-image {

    padding: 0;
    width: 120px;
    float: left;

}
.customer-image {

    width: 35%;
    padding: 20px 10px 10px 20px;

}
.order-details li:nth-child(1)::before {
    background-color: #7030a0;
}
a.dwBtn {

    background: #0070c0;
    color: #fff;
    padding: 2px 10px;
    display: inline-block;
    margin-top: 6px;
    text-decoration: none;
    border-radius: 5px;

}
a, button {

    -webkit-transition: all 0.18s ease;
    -o-transition: all 0.18s ease;
    transition: all 0.18s ease;
    cursor: pointer;

}
a, button {

    -webkit-transition: all 0.18s ease;
    -o-transition: all 0.18s ease;
    transition: all 0.18s ease;
    cursor: pointer;

}
ol.order-products .order_complete .customer-media h3, ol.order-products .order_complete .customer-media h3 a, ol.order-products .order_change .customer-media h3, ol.order-products .order_change .customer-media h3 a {

    color: #fff !important;

}
ol.order-products .order_panding .customer-media h3, ol.order-products .order_complete .customer-media h3, ol.order-products .order_change .customer-media h3, ol.order-products .request_change .customer-media h3 {

    margin-left: 35px;
}

ol.order-products .request_change, ol.order-products .order_panding, ol.order-products .order_complete, ol.order-products .order_change, ol.order-products .request_change {
    font-size: 15px;
}

.customer-media h3 {

    font-family: 'Karla', sans-serif;
    font-weight: 700;
    color: #4f4f4f;
    font-size: 25px;

}
ol.order-products .order_panding::before, ol.order-products .order_complete::before, ol.order-products .order_change::before, ol.order-products .request_change::before {

    margin-left: 15px !important;

    display: block !important;
    position: relative;

}
.order-details li::before {
	vertical-align:top;

    content: counter(step-counter);
    font-weight: 400;
    color: #fff;
    background-color: #222;
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: inline-block;
    text-align: center;
    line-height: 25px;
    margin-left: -69px;
    font-size: 14px;
    margin-right: 6px;

}

.order_complete::before {

    background-color: #1baa32 !important;

}

.data-view .finish-btn {
    background-color: #ff0000;
    margin-bottom: 4px;
    color: #fff !Important;
}

.data-view a.view {
    margin-bottom: 4px;
}
a.order-view-btn {
    color: #6d6d6d;
    font-size: 25px;
    display: inline-block;
    padding: 0px;
    margin-right: 9px;
}
a.order-delete-btn{
    color: #ff0000;
    font-size: 25px;
    display: inline-block;
    padding: 0px;
}
.data-view ul{
    padding: 0;
    margin: 3px;
}
.data-view ul li{
    list-style: none;
    display: inline-block;
}
.order-products .order_change .customer-media, ol.order-products .order_change .customer-options .approve {
	background-color: red;
	color: #fff;
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

.order_change .galleryExtraIcons a.delete,.order_change .galleryExtraIcons a.dwBtnIcon,.order_complete .galleryExtraIcons a.delete
{ color:#fff;}
.order_change .galleryExtraIcons a.delete{ color:#fff;}
.galleryExtraIcons .fa-check{ font-size:15px; color:#FFF; margin-left:10px;}
.galleryExtraIcons .fa-times{ font-size:15px; color:#FFF;margin-left:10px;}

.order_complete .galleryExtraIcons .fa-check{ font-size:15px; color:#FFF; margin-left:10px;}
.order_complete .galleryExtraIcons .fa-times{ font-size:15px; color:#FFF;margin-left:10px;}
.order_panding .galleryExtraIcons .dwBtnIcon,.order_panding .galleryExtraIcons .fa-check,.order_panding .galleryExtraIcons .fa-times { color:#000 !important;}
.popup.active {
    display: block !important;
}
.popup-box {
    max-width: 600px;
    display: table;
    width: 100%;
    height: 100%;
    margin: 0 auto;
}
.popup-middle {
    display: table-cell;
    vertical-align: middle;
}
.popup_block {
    background-color: #ddd;
    color: #000;
    padding: 20px 15px;
}


.popup {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 999;
    display: none;
    /* overflow: hidden; */
    outline: 0;
    display: none;
}
.order_change .rq-view_rivision,.order_panding .rq-view_rivision{ background:#066 !important;color: #fff;
display: inline-block;
border-radius: 10px;
padding: 2px 15px 2px 9px;

position: relative;}
.order_complete .rq-view_rivision{ background:#000 !important;color: #fff;
display: inline-block;
border-radius: 10px;
padding: 2px 15px 2px 9px;
margin-bottom: 9px;
position: relative;}
#popupBoxRivision .customer_content:nth-child(odd){ background:#005489;}
#popupBoxRivision .customer_content .popup-gallery img{ height:100px;object-fit: contain;}
#popupBoxRivision .customer_content .galleryListing   img{ height:100px;object-fit: contain;}

#popupBoxRivision .customer_content .popup-gallery,#popupBoxRivision .customer_content .galleryListing {display: inline-block;
padding-top: 15px;
margin-right: 5px;

padding: 5px;
margin-top: 10px; vertical-align:top;}
.mainLoader {
    width: 100%;
    height: 100%;
    position: fixed;
    display: none;
    text-align: center;
    top: 0px;
    left: 0px;
    background: rgba(0, 0, 0, 0.25);
    z-index: 99999;
}
.loaderInner {
    display: table;
    width: 100%;
    height: 100%;

}
.loaderCenter {
    display: table-cell;
    vertical-align: middle;
}
.lds-ripple {
    display: inline-block;
    position: relative;
    width: 64px;
    height: 64px;
	display:none;
}
.lds-ripple div {
    position: absolute;
    border: 4px solid #fff;
    opacity: 1;
  /*  border-radius: 50%;
    animation: lds-ripple 1s cubic-bezier(0, 0.2, 0.8, 1) infinite;*/
}
.lds-ripple div:nth-child(2) {
    animation-delay: -0.5s;
}
.popup_block .customer_content .customer_post, .popup_block .customer_content .customer_title {
    font-size: 18px;
    font-weight: 600;
}
.popup_block .customer_content .customer_title {
    color: #ccce2a;
}
.order-details .order-products li{ margin-bottom:40px;}
.order-details .order-products{ margin-top:15px;}
.LeftDivProductDetails {width: 50px;display: inline-block;vertical-align: top;margin-right:5px;}
.RightDivProductDetails{display: inline-block;width: 90%;}
.view_details a{ font-size: 15px;
    text-decoration: underline !important;

}
#popup1RevisionBox .popup_block{background-color: #0072ba;}
#popup1RevisionBox .popup_block .title { color:#FFF;}
#popup1RevisionBox .popup_block .title { color:#FFF;}

#popup1RevisionBox .popup_block .customer_content .customer_post{ color:#FFF;}
#popup1RevisionBox .popup_block{ color:#FFF}
#popup1RevisionBox .popup_block .customer_content .customer_title{ color:#ccce2a;}
.productName {
	width: 410px;
	text-overflow: ellipsis;
	overflow: hidden;
	white-space: nowrap;
}
.RightDivProductDetails span{ margin-left:0px !important;}
.order_complete .customer-options{ text-align:center}
.order_complete .dwBtnIcon{ color:#FFF;}
.desgin_part_block.order-details .order-products li::before{border: 1px solid #fff;}
.order_change .customer-options{ text-align:center;}


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
.pending_feedback_btn{background: none;border: 2px solid;text-align: center;border-radius: 10px;/*! background-color: #222; */color: #00;display: inline-block;border-radius: 10px;padding: 2px 20px 2px 20px;margin-bottom: 9px;position: relative; }
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
.order_panding .rq-view_rivision {background: none;border: 2px solid;text-align: center;border-radius: 10px;/*! background-color: #222; */color: #4f4f4f !important;display: inline-block;border-radius: 10px;padding: 2px 10px 2px 12px;position: relative;}
.order_complete .row_section { overflow:hidden;}
.order_panding .customer-options,.request_change .customer-options{ top:33% !important;}
.order-details .customer-options{ clear:none !important;}

.desgin_part_block .customer-media{ margin-top:0px !important;}
.request_change .viewRevisions{ display:inline-block !important;}
.request_change .pending_feedback_btn,.request_change .rq-view_rivision {

    background: none;
    border: 2px solid;
    text-align: center;
    border-radius: 10px;
    color: #fff;
    display: inline-block;
    border-radius: 10px;
    padding: 2px 20px 2px 20px;
    margin-bottom: 9px;
    position: relative;
   }
.order_panding .rq-view_rivision{ background:none !important;}
.desgin_part_block .customer-media{overflow: auto;}
.order_panding, .request_change{position:relative;}
.order_complete{position:relative;}

#popupBoxRivision .revision_title {font-weight: 600;font-size: 24px;}
#popup1RevisionBox .popup_block .customer_content .customer_title{color: #f4f62e;font-size: 19px;}
#popupBoxRivision .customer_content .popup-gallery{border:none;padding:0px;}
#popup1RevisionBox .popup_block .customer_content .customer_post{font-size: 20px;
font-weight: 550;}
#popupBoxRivision .MessageText{font-size:17px;}


@media only screen and (max-width: 1199px) {
.order_panding .customer-options{left: 36%;}
.request_change .customer-options{left: 36%;}
.order_complete .customer-options{left: 36%;}
}
@media only screen and (max-width: 640px) {
	.order_panding .customer-options{position: initial;width:100%;clear:both !important;}
	.request_change .customer-options, .order_complete .customer-options{position: initial;width:100%;clear:both !important;}
}

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
.Download{ text-align:center;}
.request_change .Download a{ color:#FFF; }
.order_complete .Download{ display:inline-block;margin-left:10px; }
.ProductInstruction .customer_details ul.popup-gallery li { padding-left:0px !important; margin-bottom:5px !important;}
.ProductInstruction .customer_details ul.popup-gallery li a.WhiteText{ color:#FFF !important;}
.customFiles { margin:0px; padding:0px;}
.customFiles li {
    display: inline-block;
    margin-right: 5px;
    margin-bottom: 5px !important;
}
.customFiles li img{ width:50px; height:50px;}
.editInformation input{background: #000 !important;color: #fff;border: 0px;font-size: 14px;font-weight: bold;padding: 5px 15px;border-radius: 10px;margin-bottom: 10px;cursor: pointer;}
.EditableInformationButton{ display:none;}
.EditInformationData{ display:none; margin-bottom:15px;}
.EditInformationData .customer_details strong{ display:block;}
.EditInformationData .customer_details input {width: 100%;border: 1px solid #000;padding: 12px;}
.EditInformationData .customer_details .form-file-upload span{ color:#000 !important;}

.EditInformationData .customer_details select{width: 100%;border: 1px solid #000;padding: 12px; background:#FFF;}
.EditInformationData .customer_details textarea{width: 100%; height:250px;border: 1px solid #000;padding: 12px;resize:none;}
.EditableInformationButton{ text-align:center;}
.EditableInformationButton .UpdateInformation{ font-size:14px; cursor:pointer;background: #000 !important;color: #fff;border: 1px solid #000;padding: 7px 18px;font-weight: bold;}
.EditableInformationButton .CancelInformation{font-size:14px;cursor:pointer;background: #ccc !important;/*! color: #fff; */border: 0px;padding: 7px 18px;font-weight: bold;}
.updateInformationMsg{ display:none; text-align:center;}
.EditInformationData .customer_details .custom-control-description strong{ display:inline-block;}
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

.venueLogosImges {
	width:50px;
	position: relative;
	margin-top: 12px;
}
.customPImages{	width:50px;
	position: relative;
	margin-top: 15px; margin-right:15px; display:inline-block;}
.customPImages .fa-times{position: absolute;background: red;right: -10px;top: -10px;text-align: center;padding: 3px !important;font-size: 9px;font-weight: bold;cursor: pointer;}

.customPImages img {
	width: 50px;
	border: 1px solid #ccce2a !important;
	padding: 4px;
}
.customPImages .fa-download{border: 1px solid #ccce2a;padding: 16px;}
.venueLogos .deleteImages{position: absolute;background: red;right: -10px;top: -10px;text-align: center;padding: 3px !important;font-size: 9px;font-weight: bold;cursor: pointer;}

.venueLogos .deleteFiles {
	/* position: absolute; */
	background: red;
	/* right: -10px; */
	/* top: -10px; */
	text-align: center;
	padding: 3px !important;
	font-size: 15px;
	font-weight: bold;
	cursor: pointer;
	border-radius: 50%;
	width: 20px;
	height: 20px;
}
.venueLogos img {
	width: 50px;
	border: 1px solid #ccce2a !important;
	padding: 4px;
}
.venueLogos{ display:inline-block; margin-right:15px;}
.timerLabel{ font-size:15px;}
#rq-popup .popup_block {
    box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.30);
    -webkit-box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.30);
    -moz-box-shadow: 0px 0px 20px 0px rgba(0,0,0,0.30);
}
.close-btn{ cursor:pointer;}
.customer-options .rq-change::before {
	content: "";
	display:none;


}
.outputFilename{display: block;clear: both;width: 100%;margin-left: 10px; margin-top:5px;font-weight: bold;}
.outputFilename label::after {
	content: ", ";
	width: 10px;
	display: inline-block;
}
.outputFilename label:last-child::after{ display:none;}
.main-nav .nav-link{ font-size:13px !important}
.heightwidth_50 {
	width: 50px;
	object-fit: contain;
	height: 50px;
}

</style>


</head>

<body>
<?php include "includes/header.php"; ?>
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
        <div class="main-content bx-shadow">
            <h1 class="page-heading mb-4">Order #<?=$OrderUID;?> - <?=$UserData['FName'].' '.$UserData['LName'];?> - <?=date('M d, Y',$OrderDate);?>


            </h1>

            <div class="row mt-5">
                <div class="col-lg-6 brd-lg-right pr-lg-4 pl-lg-5">
                    <h2 class="blue text-center mb-4">Order details    </h2>

                    <div class="mb-4 order-details">
                        <?php



						if($OrderData[0]['is_approve'] == 1) { ?>
                                        <a class="finish-btn" style="background: #0b76c2;padding: 5px 18px;display: inline-block;color: #fff;border-radius: 10px;text-decoration: none;" href="?order_id=<?=$OrderData[0]['TransactionID'];?>&is_approve=0" onClick="return confirm('Are you sure you want to approved order?')">Approved</a>
                                    <?php }else{ ?>
                                        <a class="finish-btn" style="background: #0b76c2;padding: 5px 18px;display: inline-block;color: #fff;border-radius: 10px;text-decoration: none;" href="?order_id=<?=$OrderData[0]['TransactionID'];?>&is_approve=1" onClick="return confirm('Are you sure you want to approve order?')">Approve</a>
                                    <?php }  ?>
                                     <?php
									if($OrderData[0]['AssignTo']==0 && $OrderData[0]['OrderStatus']==4 && $TransactionData['PaymentStatus']!="Sales pending payment") {
									if($_SESSION['userType']!="sale_rep"){
								?>

                                <a style="background: green ; margin-top:5px;padding: 5px 18px;color: #fff; display:inline-block;border-radius: 10px;text-decoration: none;" href="?paidorder=<?=$OrderData[0]['Id'];?>&transactionid=<?=$OrderData[0]['TransactionID'];?>" onClick="return confirm('Are you sure you want to mark this order make as paid?')">Make as paid</a>
                                <?php } } ?>

                    </div>
                    <div class="mb-4 order-details">
                        <p>Ordered on:</p>
                        <h3><?=date('D M d, Y',$OrderDate);?> <span><?=date('g:i A',$OrderDate);?></span></h3>
                    </div>
                    <div class="mb-4 order-details">
                        <p>Ordered by:</p>
                        <h3><?=$UserData['FName'].' '.$UserData['LName'];?> <br><span style="margin-left:0px;"><?=$UserData['Email'];?></span></h3>
                    </div>
                    <?php
                    $userData = $AccessUData;

                    if($userData['UserType']!='user')
                    {
                        ?>
                        <div class="mb-4 order-details">
                            <p>Assigned to:</p>
                            <h3><?php if($AssignTo>0) { echo $DesignerName.'<span>'.date('M d, Y',$AssignOn).'</span> <span>'.date('g:i A',$AssignOn).'</span>'; } else { echo "Not Assigned"; } ?></h3>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="mb-4 order-details order-progress">
                        <p>Order status:</p>
                        <h3><?php
                            $getRespnseCode = array();
                            $orderStatusReview = 0;
                            foreach($OrderData as $k=>$v){
                                $getRespnseCode[$v['ResponseState']] = $v['ResponseState'];
                            }
							$startTimer = "yes";
 							if($OrderData[0]['is_approve'] == 1){
                                echo "Approved";
								 $orderStatusReview = 1;
								 $startTimer = "no";
                            }else{


						  if($AssignTo==0 && $OrderStatus==4 ) {
								echo "Pending Payment";
								$startTimer ="no";
							}else if($AssignTo==0 &&  $OrderStatus==0) {
                                echo "New";
								$startTimer = "no";
                            }else if($AssignTo>0 && $OrderStatus==1 && in_array(3,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(2,$getRespnseCode)) {
                                echo "Submitted to customer";
                            }else if($AssignTo>0 && $OrderStatus==1 && in_array(1,$getRespnseCode)  && !in_array(2,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
								$startTimer = "no";

                                echo "Approved & done";
                                $orderStatusReview = 1;
                            }else if($AssignTo>0 && $OrderStatus==1 && in_array(2,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
                                echo "Revisions";
                            }else if($AssignTo>0 && $OrderStatus==1 ) {
                                echo "In progress";
                            }
							}


                            ?></h3>


                    </div>
                    <!-- ws #23 010521 -->
                    <div class="mb-4 order-details">
                      <form method="post">
                          <p>Change status:</p>
                          <div class="row">
                            <div class="col-sm-8">
                              <select name="status" id="statusId" class="form-control">
                                <option value="" disabled selected> Select status </option>
                                <!-- Approved -->
                                <?php if ($OrderData[0]['is_approve'] == 1): ?>
                                  <option value="revision"> Under Revision </option>
                                  <option value="process"> In Progress </option>

                                  <!-- Revisions -->
                                <?php elseif (($OrderData[0]['is_approve'] != 1) && ($OrderData[0]['OrderStatus'] == 1) && ($OrderData[0]['ResponseState'] == 2)): ?>
                                  <option value="process"> In Progress </option>
                                  <option value="completed"> Completed </option>

                                  <!-- In progress -->
                                <?php elseif ((($OrderData[0]['is_approve'] != 1) || ($OrderData[0]['ResponseState'] == 4)) && ($OrderData[0]['OrderStatus'] == 1)): ?>
                                  <option value="revision"> Under Revision </option>
                                  <option value="completed"> completed </option>
                                <?php endif; ?>
                              </select>
                            </div>
                            <div class="col-sm-4">
                                  <input type="submit" name="changeStatus" class="btn-blue btn-lg" value="Update" onClick="return iconfirm()" >
                            </div>
                          </div>
                      </form>
                    </div>
                    <div class="mb-4 order-details">
                        <p>Order details:</p>
                        <ol class="order-products">
                            <?php
                             $totalPrice = 0;
                            $mainProductName = "";
                           $j=0;
						   $allowed =  array('gif','png' ,'jpg','webp');

						   $getMotionsArray  = array();
						   foreach($OrderData as $OrderList) {
						   $TypeBanner = explode(",",$OrderList['TypeBanner']);
								if(!empty($TypeBanner)){
									$getMotionsArray[$OrderList['ProductID']]	=$TypeBanner;
								}
							 }

						   foreach($OrderData as $OrderList) {

                                if(!isset($ProductArr[$OrderList['ProductID']])) { continue; }

								$product=GetSglRcrdOnCndi(PRODUCT, "  id=".$OrderList['ProductID']);
								$Productslug = $product['slug'];


                                $listOSStr = $TypeBannerListStr = "";

								$listOSArr = explode(",",$OrderList['OtherSize']);
                                $Dimensional = $OrderList['Dimensional'];
                                $TypeBanner = explode(",",$OrderList['TypeBanner']);
                                $TurnAroundTime = $OrderList['TurnAroundTime'];
									$listOSStr ="";

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

									  	$imgsrc = productImageSrc($getBanners['filename'],$OrderList['parent_product_id'],'354','class="heightwidth_50"').'&nbsp;&nbsp;';

											$productDisplayName = "<div class='LeftDivProductDetails'>".$imgsrc."</div><div class='RightDivProductDetails'><div class='productName'>".$ProductArr[$OrderList['parent_product_id']]['Title']." ".$ProductArr[$OrderList['ProductID']]['Title']."</div>";

								}else{

									  $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$OrderList['ProductID']."' AND `filetype` = 'image'","id","ASC");
									  $imgsrc ="";
									  $imgsrc = productImageSrc($getBanners['filename'],$OrderList['ProductID'],'354','class="heightwidth_50"').'&nbsp;&nbsp;';



									if($ProductArr[$OrderList['ProductID']]['Addon']==1){

											$productDisplayName = "<div class='LeftDivProductDetails'>".$imgsrc."</div><div class='RightDivProductDetails'><div class='productName'>".$ProductArr[$OrderList['ProductID']]['Title']."</div>";

									}else{
									$productDisplayName = "<div class='LeftDivProductDetails'><a target='_blank' href=".SITEURL."p/".$Productslug." style='color:#4f4f4f;text-decoration:none'>".$imgsrc."</a></div><div class='RightDivProductDetails'><div class='productName'><a target='_blank' href=".SITEURL."p/".$Productslug." style='color:#4f4f4f;text-decoration:none'>".$ProductArr[$OrderList['ProductID']]['Title']."</a></div>";
									}

								}

                                ?>

                                   <li><?=$productDisplayName;?> <span class="type"><?=$ProductType[$ProductArr[$OrderList['ProductID']]['parent_product_cat_id']]['name'];?></span> <span class="price">($<?=$OrderList['TotalPrice'];?>)</span>
                                 <?php if(empty($OrderList['customeProductsFileds']) &&  $OrderList['template_type']=="customize"){?>
                                    <div class="size"><?php echo ($Dimensional!="") ? "(".$Dimensional.")":"";
                                        echo ($TypeBannerListStr!="")? $TypeBannerListStr:"";;
                                        echo ($TurnAroundTime!="" && $TurnAroundTime>0) ? "(".getTurnArroundTypeByID($TurnAroundTime).")":"";
                                        ?><?php if(!empty($ProductSize[$OrderList['DefaultSize']]['name'])) { echo "(".$ProductSize[$OrderList['DefaultSize']]['name'].")"; }  if(!empty( $listOSStr)) { echo $listOSStr; } ?></div>
                                        <?php }

										 if(!empty($OrderList['customeProductsFileds'])){
										  echo ($TurnAroundTime!="" && $TurnAroundTime>0) ? "<br>(".getTurnArroundTypeByID($TurnAroundTime).")":"";
									  }
										?>
                                      <?php if($ProductArr[$OrderList['ProductID']]['Addon']!=1  && $OrderList['template_type']=="customize"){ ?>
                                         <span style="margin-left:0px;" class="approve2 view_details" onClick="OpenInstructionsProduct('<?php echo $OrderList['Id']; ?>')"><a>View Details</a></span>
                                    <?php } ?>

                                    <?php
										 	if($OrderList['template_type']=="psd" && $OrderList['psd_file']!="" && $AssignTo>0 &&  $OrderStatus>0 && $OrderStatus!=4){
												if($OrderList['psd3dtitle']=="Yes"){
													echo "<div style='margin-bottom:5px; display:block;font-size:16px;'>3D Title</div>";
												}
											?>
                                            <a class="downloadPSD btn" data-type='admin' data-id='<?php echo $OrderList['Id']; ?>' href="javascript:void(0)" data-name="<?php echo "uploads/".$OrderList['ProductID']."/".$OrderList['psd_file']; ?>" title="Download" class="btn btn-primaray" style="background: #0070c0;
color: #fff;
display: block;
clear: both;
max-width: 140px;
" >Download PSD</a>
                                            <?php
											}

										  ?>
                                    <?php

									if($startTimer=="yes" && $OrderList['template_type']=="customize"){


									?>

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
                                                date:'<?php echo date('d-m-Y H:i:s',$AssignOn); ?>', // day-month-year HH:MM:SS
                                                hours:<?php echo getTimerCounterHours($timerDelHours); ?>,
                                                endMessage:'<div class="time-cell timer-hour"><div class="timerDigits"><div class="timerLabel">Hours</div><div class="digitWiseCount">0</div><div class="digitWiseCount">0</div></div></div><div class="time-cell timer-minute"><div class="timerDigitsPoint">:</div><div class="timerDigits"><div class="timerLabel">Minutes</div><div class="digitWiseCount">0</div><div class="digitWiseCount">0</div></div></div><div class="time-cell timer-second"><div class="timerDigitsPoint">:</div><div class="timerDigits"><div class="timerLabel">Seconds</div><div class="digitWiseCount">0</div><div class="digitWiseCount">0</div></div></div>'
                                              }
                                            );
											 });
										 </script>
                                        <?php } ?>

                                </li>
                            <?php }
                            ?>
                        </ol>

                    </div>
                    <?php if($TransactionData['DiscountName']!=""){ ?>
                    <div class="mb-4 order-details">
                        <p>Discount code:</p>
                        <h3><?php echo $TransactionData['DiscountName']; ?>  (-$<?php echo $TransactionData['DiscountAmount'];?>) <span>USD</span></h3>
                    </div>
                    <?php } ?>


                    <div class="mb-4 order-details">
                        <p>Total:</p>
                        <h3>$<?=$TransactionData['Amount'];?> <span>USD</span></h3>
                    </div>
                    <div class="mb-4 order-details">
                        <p>Transaction:</p>
                        <h3>Order# <span><?=$TransactionID;?></span></h3>
                        <h3><strong style="font-size: 20px;"><?=$TransactionData['TransactionID'];?></strong> <span><?=$TransactionData['PaymentMethod'];?> checkout</span></h3>
                    </div>
                </div>
                <div class="col-lg-6 pr-lg-1 pl-lg-4">
                    <h2 class="blue text-center mb-4">Media</h2>
                    <?php /*?><div class="order-ntf green mb-5">
                        <strong>Hello <?=$AccessUData['FName']." ".$AccessUData['LName'];?></strong><br> We are busy working on your order, when the files are ready, they will be uploaded and will be visible for you to look at below
                    </div><?php */?>

                    <div class="mb-4 order-details">

                        <ol class="order-products rightSideDesing">

                            <?php

                              $allowed =  array('gif','png' ,'jpg','webp');
                              $NewOrderArray =$finalCountProductArray = array();

							foreach($OrderData as $OrderList) {
									if (strpos($OrderList['TypeBanner'], 'static') !== false) {
										if($OrderList['DefaultSize']!=""){
											$NewOrderArray[$OrderList['Id']][$OrderList['ProductID']][$OrderList['DefaultSize']] = $OrderList['DefaultSize'];
											$finalCountProductArray[$OrderList['Id']][$OrderList['ProductID']][$OrderList['DefaultSize']] = $OrderList['DefaultSize'];
										}
										 if (strpos($OrderList['TypeBanner'], 'motion') !== false && $ProductArr[$OrderList['ProductID']]['Addon']==0) {
											 $finalCountProductArray[$OrderList['Id']][$OrderList['ProductID']]['motion'] = "motion";
										 }

										if($OrderList['OtherSize']!=""){
											 $listOSArr = explode(",",$OrderList['OtherSize']);
											   if($ProductArr[$OrderList['ProductID']]['Addon']==0){
                                    				if(!empty($listOSArr)) {
														 foreach($listOSArr as $osListArr) {
                                            				if($ProductSize[$osListArr]['name']!=""){
																$NewOrderArray[$OrderList['Id']][$OrderList['ProductID']][$osListArr] = $osListArr;
																$finalCountProductArray[$OrderList['Id']][$OrderList['ProductID']][$osListArr] = $osListArr;
                                            				}
														}
											}
                                }





										}
									}

							}


							foreach($OrderData as $OrderList) {
									$product=GetSglRcrdOnCndi(PRODUCT, "  id=".$OrderList['ProductID']);
								$Productslug = $product['slug'];

                                $prod = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$OrderList['ProductID']."'");


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

									   $imgsrc = productImageSrc($getBanners['filename'],$OrderList['ProductID'],'354','class="heightwidth_50"').'&nbsp;&nbsp;';



											$imgurl = SITEURL."uploads/products/".$OrderList['ProductID'].'/'.$getBanners['filename'];


								}


								$ChangeRequest = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND `ProductID` = '".$OrderList['ProductID']."'  and type='Admin'", "ID", "DESC");
								if(!empty($ChangeRequest['Attachment'])){
									$ImageUrlData = unserialize($ChangeRequest['Attachment']);
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

							  if(!empty($ChangeRequest)){

                                    //Submit

                                    ?>
                                    <li>


<h3 class="ProductTItleRight" style="font-size:22px;"><?= $productDisplayName;?>:</h3>


                                        <?php if(!empty($NewOrderArray[$OrderList['Id']][$OrderList['ProductID']])){
											$r = 0;
											?>
                                            <span class="MultiPlayVartion" style="    margin-bottom: -22px;">Multiple variations</span>
                                            <?php
											foreach($NewOrderArray[$OrderList['Id']][$OrderList['ProductID']] as $singleSize){

												$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");

													$selectChangeRequestCount = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND  TypeBanner='static' AND Size='".$singleSize."' and `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");

												if(!empty($selectChangeRequest)){

												$ChangeRequest = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."' and type='Admin'", "ID", "DESC");

												$CheckLastRevision = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "DESC");


								if(!empty($ChangeRequest['Attachment'])){
									$ImageUrlData = unserialize($ChangeRequest['Attachment']);
									if(!empty($ImageUrlData)){
										$imgurl = OrderImageURL($ImageUrlData[0]);

										$ext = pathinfo($ImageUrlData[0], PATHINFO_EXTENSION);
											if(in_array(strtoupper($ext),$allowedVideo)) {

												$imgsrc ='<div class="popup-gallery"><video width="130" height="200" controls>
                                                             <source src="'.$imgurl.'" type="video/mp4">
                                                        </video></div>';
											}else if(in_array($ext,$allowed)){


												$imgsrc ='<div class="popup-gallery"><a href="'.$imgurl.'" class="magnificPopup">'.OrderImageSrc($ImageUrlData[0],"354").'</a></div>';
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

                                                <a  class="downloadBtn" target="_blank" download href="<?php echo $imgurl ?>">Download</a>
															<?php if($ChangeRequest['ResponseState']==1){ ?>
															 <a href="javascript:void(0)" class="approve"  style="background: #1baa32;">approved</a>
                                                        <?php } ?>

                                                                <a class="rq-view_rivision text-white" data-type='static' data-size='<?php echo $singleSize; ?>'  data-orderid="<?= $OrderList['Id'];?>" data-productid="<?=$OrderList['ProductID'];?>" data-userid='<?php echo $AccessID; ?>'>View revisions (<?php echo count($selectChangeRequestCount); ?>)</a>

                                                              <br><a class="rq-change text-white" data-title="<?php echo  $prod['Title'];?> - Static - <?php echo $ProductSize[$singleSize]['name']; ?>" data-id="<?=$OrderList['Id'];?>" data-image='<?php echo $filePath; ?>' data-productid="<?php echo $prod['id']; ?>" bannertype="Static" bannersize='<?php echo $singleSize; ?>' bannersizelabel='<?php echo $ProductSize[$singleSize]['name']; ?>'>Request revision</a>






                                                </div>



                                            </div>


                                            </div>
                                        </div>

                                                <?php
												}
												?>

                                                <?php

											}

										}else{
										if(!empty($selectChangeRequest)){
											$CheckLastRevision = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND `ProductID` = '".$OrderList['ProductID']."' and Size='0' and TypeBanner='default'", "ID", "DESC");

													$selectChangeRequestCount = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND  TypeBanner='default' and `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");

											?>

                                          <div class="SimpleType <?php if($CheckLastRevision['ResponseState']==3) {?>request_change<?php }else if($CheckLastRevision['ResponseState']==1){?>order_complete<?php }else{ ?>order_panding<?php } ?>">

                                               <div class="customer-media">


<div class="row_section">

                                                <div class="customer-image mr-3">

                                                                <div class="popup-gallery">

                                                                    <a href="<?=$imgurl;?>" class="magnificPopup"> <?php echo $imgsrc; ?></a>
                                                                </div>

                                                            </div>

                                                <div class="customer-options">
															<a  target="_blank" class="downloadBtn" download href="<?php echo $imgurl ?>">Download</a>
															<?php if($ChangeRequest['ResponseState']==1){ ?>
															 <a href="javascript:void(0)" class="approve"  style="background: #1baa32;">approved</a>
                                                        <?php } ?>
                                                         <?php if(count($selectChangeRequestCount)>0 ){ ?>
                                                                <a class="rq-view_rivision text-white" data-type='' data-size=''  data-orderid="<?= $OrderList['Id'];?>" data-productid="<?=$OrderList['ProductID'];?>" data-userid='<?php echo $AccessID; ?>'>View revisions (<?php echo count($selectChangeRequestCount); ?>)</a>
                                                                <?php } ?>

                                                          <br>
                                                           <a class="rq-change text-white" data-title="<?= $prod['Title'];?>" bannertype="" bannersize='' bannersizelabel='' data-id="<?=$OrderList['Id'];?>" data-image='<?php echo $filePath; ?>' data-productid="<?php echo $prod['id']; ?>">Request revision</a>






                                                </div>



                                            </div>
                                            </div>
                                        </div>
                                        <?php
                                        }
										 } ?>
                                        <?php
										//motioon
										 if (strpos($OrderList['TypeBanner'], 'motion') !== false && $ProductArr[$OrderList['ProductID']]['Addon']==0) { ?>
                                        <?php
										$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND TypeBanner='motion' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");
												if(!empty($selectChangeRequest)){

												$ChangeRequest = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='motion'  AND `ProductID` = '".$OrderList['ProductID']."'  and type='Admin'", "ID", "DESC");
								if(!empty($ChangeRequest['Attachment'])){
									$ImageUrlData = unserialize($ChangeRequest['Attachment']);
									if(!empty($ImageUrlData)){
										$imgurl = OrderImageURL($ImageUrlData[0]);

										$ext = pathinfo($ImageUrlData[0], PATHINFO_EXTENSION);
											if(in_array(strtoupper($ext),$allowedVideo)) {

												$imgsrc =' <div class="popup-gallery"><video width="130" height="200" controls>
                                                             <source src="'.$imgurl.'" type="video/mp4">
                                                        </video></div>';
											}else if(in_array($ext,$allowed)){


												$imgsrc =' <div class="popup-gallery"><a href="'.$imgurl.'" class="magnificPopup">'.OrderImageSrc($ImageUrlData[0],"354").'</a></div>';
											}
											else{


												$imgsrc ='<a href="'.$imgurl.'" download><img  src="'.SITEURL.'/images/download_file.png"></a>';
											}
									}
								}
								$CheckLastRevision = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='motion'  AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "DESC");
									$selectChangeRequestCount = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND  TypeBanner='motion' and `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");

												?>

                                             <div class="MotionType <?php if($CheckLastRevision['ResponseState']==3) {?>request_change<?php }else if($CheckLastRevision['ResponseState']==1){?>order_complete<?php }else{ ?>order_panding<?php } ?>">

                                               <div class="customer-media">

                                            <h3 class="SubtitleProduct">Motion</h3>


<div class="row_section">

                                                <div class="customer-image mr-3">



                                                                   <?php echo $imgsrc; ?>




                                                            </div>

                                                <div class="customer-options">
                                                <a   target="_blank" class="downloadBtn" download href="<?php echo $imgurl ?>">Download</a>
															<?php if($ChangeRequest['ResponseState']==1){ ?>
															 <a href="javascript:void(0)" class="approve"  style="background: #1baa32;">approved</a>
                                                        <?php } ?>

                                                         <?php if(count($selectChangeRequestCount)>0){ ?>
                                                                <a class="rq-view_rivision text-white" data-type='motion' data-orderid="<?= $OrderList['Id'];?>" data-productid="<?=$OrderList['ProductID'];?>" data-userid='<?php echo $AccessID; ?>'>View revisions (<?php echo count($selectChangeRequestCount); ?>)</a>
                                                                <?php } ?>
                                                        <br>

                                                         <a class="rq-change text-white" data-title="<?= $prod['Title'];?> - Motion" bannertype="Motion" bannersize='' bannersizelabel='' data-id="<?=$OrderList['Id'];?>" data-image='<?php echo $filePath; ?>' data-productid="<?php echo $prod['id']; ?>">Request revision</a>




                                                </div>



                                            </div>
                                            </div>
                                        </div>

                                                <?php
												}
												?>
                                        <?php } ?>

                                </li>
                                    <?php

							  }

                            } ?></ol>

                    </div>
                    <?php //if($OrderStatus == 0) { ?>
                        <form method="post" class="form-row assign-desg">
                            <div class="col-12">
                                <label>Assign to designer</label>
                            </div>
                            <div class="col-sm-8">
                                <select name="assignedto" class="form-control">
                                    <option value="">Select designer</option>
                                    <?php foreach($UserArr as $designers) {
                                        if($designers['UserType'] != "designer") { continue; }

                                        $selected='';

                                        if($designers['UserID']==$AssignTo){
                                            $selected='selected="selected"';
                                        }

                                        echo '<option '.$selected.' value="'.$designers['UserID'].'">'.$designers['FName'].' '.$designers['LName'].'</option>';
                                    } ?>
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <input type="hidden" id="projectId" value="<?=$TransactionID;?>" name="projectId" />
                                <input type="submit" name="assignFBtn" class="btn-blue btn-lg" value="Assign" />
                            </div>
                        </form>
                    <?php
                    //}

					?>
                </div>
            </div>
            <?php include('../orderDiscussion.php'); ?>
        </div>
    </div>


    </div>
</main>

<div id="popup1RevisionBox" class="popup">

    <div class="popup-box">

        <div class="popup-middle">

            <div class="popup_block">

                <h4 class="title text-center mb-4"><font class="revision_title">Revision Requested</font><span class="close-btn float-right"><i class="fa fa-times" <="" span=""></i></span></h4>

                <div class="popupfull_content" id="popupBoxRivision">




                </div>

            </div>

        </div>

    </div>

</div>
<div class="ViewInformationBox"></div>
<div class="mainLoader" style="display: none;"><div class="loaderInner"><div class="loaderCenter"><div class="lds-ripple"><div></div><div></div></div></div></div></div>

<div id="rq-popup" class="popup">

    <div class="popup-box">

        <div class="popup-middle">

            <div class="popup_block bg-white">

                <h4 class="title text-center mb-4 text-dark"><span class="close-btn float-right"><i class="fa fa-times"></i></span></h4>

                <div class="popupfull_content" id="popupBox">

                    <div class="customer_content py-0">

                        <div class="request-form">

                            <form method="post" enctype="multipart/form-data">

                                <div class="form-group">

                                    <input type="text" name="pTitle" id="pTitle" disabled class="form-control" placeholder="Name">


                                </div>

                                <div class="form-group">

                                    <textarea name="pChanges" type="text" class="form-control" placeholder="Required Changes"></textarea>

                                </div>



                                <div class="form-group pr-media-upload">

                                    <label class="custom-file">

                                        <input type="file" accept="image/*,video/mp4,video/x-m4v,video/*" name="filename[]" multiple  id="file" class="custom-file-input">

                                        <input type="hidden" name="orderIID" id="orderIID" value="" />
                                        <input type="hidden" name="orderType" id="orderType" value="" />
                                        <input type="hidden" name="ProductID" id="ProductID" value="" />
                                        <input type="hidden" name="BannerType" id="BannerType" value="" />
                                        <input type="hidden" name="BannerSize" id="BannerSize" value="" />
                                        <input type="hidden" name="BannerSizeLabel" id="BannerSizeLabel" value="" />
                                        <input type="hidden" name="ResponseFile" id="ResponseFile" value="" />

                                        <span class="custom-file-control"></span>


                                    </label>

                                      <div class="outputFilename"></div>


                                </div>


<label class="custom-control custom-checkbox" style="margin-top:15px;margin-left:5px;">

                                            <input type="checkbox" oninvalid="if(this.checked==false) {this.setCustomValidity('Please give us at least 24 hours to complete the revision')}if(this.checked==true) {this.setCustomValidity('');}"
                                                   oninput="this.setCustomValidity('')" onBlur="this.setCustomValidity('')" onChange="this.setCustomValidity('')" required class="custom-control-input" value="1" name="terms" >
                                            <span class="custom-control-indicator"></span>
                                            <span class="custom-control-description">
                                            Please give us at least 24 hours to complete the revision
                                 </span></label>
                                 <div class="popup-footer" style="text-align:right;">
                                    <input type="submit" class="btn-blue ml-auto" name="submitRequest" value="Submit" />
                                    </div>
                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<?php include "includes/footer.php"; ?>

<script src="js/bootstrap.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/datepicker.min.js"></script>
<script src="js/jquery.tablesorter.min.js"></script>
<script src="js/script.js"></script>
<script src="../js/jquery.magnific-popup.min.js"></script>
<script src="../js/timer.js"></script>
 <link rel="stylesheet" href="../css/magnific-popup.min.css">
<script>
// ws #23 020521
function iconfirm() {
      var status = document.getElementById("statusId").value;
      if (status) {
         // status = document.getElementsByName("status");
         var c = confirm(`Are you sure you want to change order status to `+status+`? Press ok to confirm.`);
         if (c==true) {
           return true;
         } else {
           return false
         }
      }
      alert("Please select status to update.");
      return false;
 }

function OpenInstructionsProduct(id){
	jQuery(".mainLoader").show();
	var $ = jQuery;
	  jQuery.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/view-order-information.php",
            data: "ID="+id,
            success: function(regResponse) {
   jQuery(".mainLoader").hide();
                console.log(regResponse);

                regResponse = JSON.parse(regResponse);

                jQuery(".ViewInformationBox").html(regResponse.html);
				// jQuery(".ViewInformationBox #ProductInstruction"+id).show();
				 $("#ProductInstruction"+id).addClass("active");
	 jQuery(".EditInformationData,.EditableInformationButton").hide();
	 jQuery(".ViewInformationData,.editInformation input").show();

	  $('.popup-gallery').magnificPopup({

        delegate: '.magnificPopup',

        type: 'image',

        mainClass: 'mfp-img-mobile',

        gallery: {

            verticalFit: true

        },

    });


				// $(".revision_title").html(regResponse.titleBlock);

              //  $("#popup1RevisionBox").addClass("active");


            }
        });

}

    $(document).on("click",".close-btn",function(){


        $("#popup1,#rq-popup").removeClass("active");
		 $("#popup1RevisionBox").removeClass("active");

     jQuery(".AddonsInstruction,.ProductInstruction").removeClass("active");
	 jQuery(".EditInformationData,.EditableInformationButton").hide();
	 jQuery(".ViewInformationData,.editInformation input").show();


    });

	  $('.popup-gallery').magnificPopup({

        delegate: '.magnificPopup',

        type: 'image',

        mainClass: 'mfp-img-mobile',

        gallery: {

            verticalFit: true

        },

    });

	   jQuery(".rq-view_rivision").click(function(){
        var  $ = jQuery;
		jQuery(".mainLoader").show();
        var orderIID = $(this).data("orderid")

        var ProductID = $(this).data("productid");
		 var type = $(this).data('type');
		  var size = $(this).data('size');

        jQuery.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/view-rivision.php",
            data: "id="+orderIID+'&ProductID='+ProductID+"&type="+type+"&size="+size+"&DisplayPage=admin",
            success: function(regResponse) {
   jQuery(".mainLoader").hide();
                /*console.log(regResponse);*/

                regResponse = JSON.parse(regResponse);

                $("#popupBoxRivision").html(regResponse.message);
				 $(".revision_title").html(regResponse.titleBlock);

                $("#popup1RevisionBox").addClass("active");


            }
        });


    });


jQuery(document).ready(function(e) {
    jQuery(document).on("click",".editInformation input",function(e) {
        jQuery(this).hide();
		jQuery(".ViewInformationData").hide();
		jQuery(".EditInformationData,.EditableInformationButton").show();
    });
	/*
    jQuery(document).on("click",".CancelInformation",function(e) {

	 jQuery(".editInformation input").show();
		jQuery(".ViewInformationData").show();
		jQuery(".EditInformationData,.EditableInformationButton").hide();
    });*/

	 jQuery(document).on("click",".UpdateInformation",function(){
        var  $ = jQuery;
		jQuery(".mainLoader").show();
        var ID = $(this).data("id");
		var form_data= $( this ).closest("form")[0];
		var PostFoamData = new FormData(form_data);
		PostFoamData.append("ID",ID)

        jQuery.ajax({

            type: "POST",
            url: "<?=SITEURL;?>ajax/updateOrderInformation.php",
            data: PostFoamData,
			processData: false,
			contentType: false,
			cache:false,
			async:false,
			success: function(regResponse) {
   				jQuery(".mainLoader").hide();
				jQuery(".updateInformationMsg").show();
				jQuery(".ViewInformationData").hide();
				jQuery(".EditInformationData,.EditableInformationButton").hide();
                jQuery(".popup_block .title").hide();
				jQuery(".popupfull_content").css("height","auto");
				setTimeout(function(){ window.location.reload(); }, 2000);



            }
        });


    });


});

jQuery(document).on("click",".deleteCustomProudctImages",function(){
var confirm_Alert = confirm("Are you sure you want to delete?");
var id= jQuery(this).attr("data-id");
var product_type= jQuery(this).attr("data-product_type");
var fileds_type= jQuery(this).attr("data-fileds_type");
var name= jQuery(this).attr("data-name");
var option_type= jQuery(this).attr("data-option_type");

if(confirm_Alert){
	deleteCustomProudctImages(id,product_type,fileds_type,name,option_type);
	jQuery(this).parent().remove();
}
});
function deleteCustomProudctImages(id,product_type,fileds_type,name,option_type){


		jQuery(".mainLoader").show();
		 jQuery.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/updateOrderInformation.php",
            data: "ID="+id+"&mode=customdeleteFile&product_type="+product_type+"&fileds_type="+fileds_type+"&name="+name+"&option_type="+option_type,
            success: function(regResponse) {
   				jQuery(".mainLoader").hide();
				//alert("#"+fileds_type+""+id);

			//	jQuery("#customPImages"+product_type+"_"+fileds_type+"_"+id).remove();


            }
        });

}

function deleteFiles(id,fileds_type){
	var confirm_Alert = confirm("Are you sure you want to delete?");

	if(confirm_Alert){
		jQuery(".mainLoader").show();
		 jQuery.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/updateOrderInformation.php",
            data: "ID="+id+"&mode=deleteFile&fileds_type="+fileds_type,
            success: function(regResponse) {
   				jQuery(".mainLoader").hide();
				//alert("#"+fileds_type+""+id);
				jQuery("#"+fileds_type+""+id).remove();


            }
        });
	}
}
jQuery(document).on("change",".photos_and_logo",function(event){
	var files = event.target.files; //FileList object
	  var numFilescnt = files.length;

	  var FilenameBlock = jQuery(this).parent().parent().parent().find(".FilenameBlock").length;
	var totalFiles = parseInt(numFilescnt) + parseFloat(FilenameBlock);
	  if(totalFiles > 5){
                    alert('Maximum upload 5 file');
                   jQuery(this).val("");
                    return false;
                }
});


$(".add_extra_items").on("click", function(){

        if ($(this).is(':checked')) {
			jQuery(this).parent().parent().find(".AddMusicFile").show();
				jQuery(this).parent().parent().find(".customPImages").show();

        } else {
            jQuery(this).parent().parent().find(".AddMusicFile").hide();
			jQuery(this).parent().parent().find(".customPImages").hide();
			jQuery(this).parent().parent().find(".AddMusicFile").val("");

        }

    });
	   jQuery(document).on("click",".rq-change",function(){
        var  $ = jQuery;
		jQuery(".mainLoader").show();
        var orderIID = $(this).data("id")

        var ProductID = $(this).data("productid");
        var title = $(this).data("title");
        var ResponseFile = $(this).data("image");

        var BannerType = $(this).attr("bannertype");
        var BannerSize = $(this).attr("bannersize");
        var BannerSizeLabel = $(this).attr("bannersizelabel");


        jQuery.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/check-change-request.php",
            data: "id="+$(this).data("id")+'&TransactionOrderID=<?php echo $OrderUID;?>',
            success: function(regResponse) {
                regResponse = JSON.parse(regResponse);
                jQuery(".mainLoader").hide();
                if(regResponse.Status==0){
                  //  jQuery("#rq-popup .prodTitle").html("Second Revision  = $<?php echo MEDIA_CHANGE_PRICE; ?>");
                  //  jQuery(".request-form .ml-auto").val("Proceed to pay $<?php echo MEDIA_CHANGE_PRICE; ?>");
                }else{
                    jQuery("#rq-popup .prodTitle").html("Form");
                    jQuery(".request-form .ml-auto").val("Submit");
                }
                $("#BannerType").val(BannerType);
				$("#BannerSize").val(BannerSize);
				$("#BannerSizeLabel").val(BannerSizeLabel);

				$("#orderIID").val(orderIID);
                $("#ProductID").val(ProductID);
                $("#pTitle").val(title);

                $("#orderType").val(regResponse.Status);
                $("#ResponseFile").val(ResponseFile);

                $("#rq-popup").addClass("active");


     jQuery(".AddonsInstruction,.ProductInstruction").removeClass("active");



            }
        });


    });
	jQuery(document).on("change",".custom-file input[type='file']",function(){
		 var ele = document.getElementById(jQuery(this).attr('id'));
    	 var result = ele.files;
    	 var arrayListing = "";
		 for(var x = 0;x< result.length;x++){
    		 var fle = result[x];
        	arrayListing += "<label>"+fle.name+"</label>";

    	}
		jQuery(".outputFilename").html(arrayListing);
	});

	jQuery(document).on("click",".downloadPSD",function(){
		var name =  jQuery(this).attr("data-name");
		var type =  jQuery(this).attr("data-type");
		var id =  jQuery(this).attr("data-id");

		window.location = "<?php echo SITEURL; ?>download.php?name="+name+"&type="+type+"&id="+id;
	});

</script>
</body>

</html>
