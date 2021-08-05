<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
require_once '../function/adminSession.php';


$PageTitle = "Bug Details";

if(isset($_REQUEST['viewID']) && !empty($_REQUEST['viewID'])) {
    unset($_SESSION['viewBugID']);
    $_SESSION['viewBugID'] = intval($_REQUEST['viewID']);
}

$bugID=$_SESSION['viewBugID'];
$result = mysql_query("SELECT * FROM ".BUG_REPORT." where TicketNo='$bugID'");
$showData = mysql_fetch_array($result);

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
<main class="main-content-wrap">
    <div class="container">
        <div class="main-content bx-shadow">
           
            
          
            </h1>

            <div class="row mt-5">
                <div class="col-lg-6 brd-lg-right pr-lg-4 pl-lg-5">
                    <h2 class="blue text-center mb-4">Bug Details</h2>
                    
                    <div class="mb-4 order-details">
                        <p>Reported by:</p>
                        <h3>
                        <?=$showData['name'];?> 
                        <br><span style="margin-left:0px;"><?=$showData['email'];?></span>
                        <br><span style="margin-left:0px;"><?=$showData['phone'];?></span>
                    
                    </h3>
                    </div>
                    
                    
                    <div class="mb-4 order-details">
                        <p>Bug Title:</p>
                        <h3><?=$showData['title'];?> </h3>
                    </div>

                    <div class="mb-4 order-details">
                        <p>Bug Description:</p>
                        <h3><?=$showData['description'];?> </h3>
                    </div>

                    <div class="mb-4 order-details">
                        <p>Bug Status:</p>
                        <h3><?php if($showData['bug_status']==0) { echo 'Pending';}else{ echo 'Completed'; } ?> </h3>
                    </div>

                    <div class="mb-4 order-details">
                        <p>Reported on:</p>
                        <h3><?=date('D M d, Y',$showData['CreatedOn']);?></h3>
                    </div>



                </div>
                <div class="col-lg-6 pr-lg-1 pl-lg-4">
                    <h2 class="blue text-center mb-4">Bug Media</h2>
               
                    
                    <div class="mb-4 order-details">

                        <ol class="order-products rightSideDesing">
                        <?php
                        $result2 = mysql_query("SELECT * FROM ".BUG_IMAGES." where bug_id='$bugID'");
                        
                        while($showData2 = mysql_fetch_array($result2)){

                            ?>
                            <li><a target="_blank" href="<?php echo SITEURL."images/bugs/".$showData2['file_path']; ?>" ><img style="width: 50%;" src="<?php echo SITEURL."images/bugs/".$showData2['file_path']; ?>"></a></li>
                            <?php
                        }
                        ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>
</main>


</div></div></div></div>


<?php include "includes/footer.php"; ?>

<script src="js/bootstrap.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/datepicker.min.js"></script>
<script src="js/jquery.tablesorter.min.js"></script>
<script src="js/script.js"></script>
<script src="../js/jquery.magnific-popup.min.js"></script>
<script src="../js/timer.js"></script>
 <link rel="stylesheet" href="../css/magnific-popup.min.css">

</body>
</html>