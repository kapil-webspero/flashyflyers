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
if($_SESSION['userType'] != "user") {
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
    echo "<script> window.location.href = '".SITEURL."my-orders.php';</script>";
    exit();
}
$OrderUID = intval($_SESSION['ORDERUID']);
$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '$OrderUID' AND `CustomerID` = '$AccessID'", "Id", "ASC");
if(count($OrderData) == 0 || empty($OrderData)) {
    echo "<script> window.location.href = '".SITEURL."my-orders.php';</script>";
    exit();
}


if(isset($_REQUEST['mode']) && ($_REQUEST['mode']=="deletemockup" || $_REQUEST['mode']=="approvemockup" || $_REQUEST['mode']=="unapprovemockup" || $_REQUEST['mode']=="unapproverejected") && isset($_REQUEST['id'])) {
    $RequestID = intval($_REQUEST['id']);
    $statusMockup = "1";
    $statusMockupText = "approve";

    if($_REQUEST['mode']=="deletemockup"){
        $statusMockup = "2";
        $statusMockupText = "rejected";
    }
	
	if($_REQUEST['mode']=="unapprovemockup"){
        $statusMockup = "0";
        $statusMockupText = "approved";
    }
	
	if($_REQUEST['mode']=="unapproverejected"){
        $statusMockup = "0";
        $statusMockupText = "rejected";
    }
	if($statusMockup==1){
		UpdateRcrdOnCndi(ORDER,"`ResponseState` = '1'", "`id` = '".$RequestID."'");
		UpdateRcrdOnCndi(ORDER,"`ResponseState` = '1'", "`id` = '".$RequestID."'");
	}else{
		UpdateRcrdOnCndi(ORDER,"`ResponseState` = '2'", "`id` = '".$RequestID."'");
		
	
	}
		$RequestGet = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`ID` = '".$_REQUEST['RequestId']."'", "ID", "DESC");
		UpdateRcrdOnCndi(CHANGE_REQ,"`ResponseState` = '1'", "`OrderID` = '".$RequestGet['OrderID']."' and ProductID='".$RequestGet['ProductID']."' and Size='".$RequestGet['Size']."' and TypeBanner='".$RequestGet['TypeBanner']."'");
	
	  $curl = curl_init();
                // Set some options - we are passing in a useragent too here
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => SITEURL.'EmailTemplate/revision-change.php?statusMockupText='.$statusMockupText."&id=".$RequestID,
                    CURLOPT_USERAGENT => 'Curl Test'
                ));
				
			
                // Send the request & save response to $resp
                $resp = curl_exec($curl);
                // Close request to clear up some resources
                curl_close($curl);

    $_SESSION['SUCCESS'] = "Record has been successfully ".$statusMockupText.".";
    echo "<script> window.location.href = '".SITEURL."order-details.php?order_id=".$_REQUEST['order_id']."';</script>";
	
	
    exit();
}



if(isset($_REQUEST['actionTaken']) && $_REQUEST['actionTaken'] == "approved") {
    if(isset($_REQUEST['orderItem']) && !empty($_REQUEST['orderItem'])) {
        $orderItem = intval($_REQUEST['orderItem']);

        if(GetNumOfRcrdsOnCndi(ORDER, "`TransactionID` = '$OrderUID' AND `Id` = '$orderItem' AND `CustomerID` = '$AccessID'")>0) {
            $CurrentTime = TIME();
            UpdateRcrdOnCndi(ORDER, "`ResponseState` = '1',`OrderStatus`='1',`finishDate` = '$CurrentTime'", "`TransactionID` = '$OrderUID' AND `Id` = '$orderItem' AND `CustomerID` = '$AccessID'");
            $_SESSION['SUCCESS'] = "Order item is marked as complete.";
        } else {
            $_SESSION['ERROR'] = "Order item not assigned under your account.";
        }
    } else {
        $_SESSION['ERROR'] = "Invalid Action";
    }
    echo "<script> window.location.href = '".SITEURL."order-details.php?order_id=".$OrderUID."';</script>";
    exit();
}
if(isset($_REQUEST['submitRequest']) && !empty($_REQUEST['submitRequest'])) {
    extract($_POST);
    if(isset($_REQUEST['orderIID']) && !empty($_REQUEST['orderIID'])) {
        $orderIID = intval($_REQUEST['orderIID']);

        if(GetNumOfRcrdsOnCndi(ORDER, "`TransactionID` = '$OrderUID' AND `Id` = '$orderIID' AND `CustomerID` = '$AccessID'")>0) {

            $ItemOrder = GetSglRcrdOnCndi(ORDER, "`TransactionID` = '$OrderUID' AND `Id` = '$orderIID' AND `CustomerID` = '$AccessID'", "Id");
            $original_name = "";;
            if(isset($_REQUEST['orderType']) && $_REQUEST['orderType']==0){

                
				$dynamicPath = "/";
                    $dynamic_dir = "uploads/work/".$dynamicPath;
                    if(!file_exists($dynamic_dir)) {
                        mkdir($dynamic_dir, 0777, true);
                    }
					$attachementName = array();
					if(!empty($_FILES['filename'])){ 
					foreach($_FILES['filename']['name'] as $postFileKey=>$postFileValue){
                 	 
					 $fileType = strtolower(pathinfo(basename($_FILES["filename"]["name"][$postFileKey]),PATHINFO_EXTENSION));
                    $file_name = rand(1, 10000000000).".".$fileType;
                    $target_file = $dynamic_dir ."/". $file_name;
                    if (move_uploaded_file($_FILES["filename"]["tmp_name"][$postFileKey], $target_file)) {
                        $original_name = basename( $_FILES["filename"]["name"][$postFileKey]);
                    	$attachementName[]  = $file_name;   
                    }
					}
					}
					
					$terms = $_REQUEST['terms'];
					if($terms==""){
						$terms = 0;	
					}
					$TypeBanner = "";
					$TypeSize = 0;
					
					if($_REQUEST['BannerType']=="Static"){
						$TypeBanner= "static";	
						$TypeSize= $_REQUEST['BannerSize'];	
					}
					if($_REQUEST['BannerType']=="Motion"){
						$TypeBanner= "motion";	
					}
					 if($_REQUEST['BannerType']==""){
							$_REQUEST['BannerType'] = "default";  
						  }
					
			   $_SESSION['CartRequest'][$_REQUEST['order_id']][$_REQUEST['ProductID']] = array('orderIID'=>$orderIID,'TransactionOrderID'=>$OrderUID,'UserID'=>$AccessID,'DesignerID'=>$ItemOrder['AssignedTo'],'MessageText'=>$pChanges,'UserReadState'=>1,'CreationDate'=>$systemTime,'SystemIP'=>$systemIp,'ProductID'=>$_REQUEST['ProductID'],'Attachment'=>serialize($attachementName),'AttechmentName'=>serialize($attachementName),'ResponseFile'=>$_REQUEST['ResponseFile'],'24HoursChk'=>$terms,'TypeBanner'=>$TypeBanner,'Size'=>$TypeSize,'SizeLabel'=>$_REQUEST['BannerSizeLabel']);


                unset($_SESSION['CART']);
                header("location:cart.php");
                exit();
            }else{
			
			
			
                    $dynamicPath = "/";
                    $dynamic_dir = "uploads/work/".$dynamicPath;
                    if(!file_exists($dynamic_dir)) {
                        mkdir($dynamic_dir, 0777, true);
                    }
					$attachementName = array();
					if(!empty($_FILES['filename'])){ 
					foreach($_FILES['filename']['name'] as $postFileKey=>$postFileValue){
                 	 
					 $fileType = strtolower(pathinfo(basename($_FILES["filename"]["name"][$postFileKey]),PATHINFO_EXTENSION));
                    $file_name = rand(1, 10000000000).".".$fileType;
                    $target_file = $dynamic_dir ."/". $file_name;
                    if (move_uploaded_file($_FILES["filename"]["tmp_name"][$postFileKey], $target_file)) {
                        $original_name = basename( $_FILES["filename"]["name"][$postFileKey]);
                    	$attachementName[]  = $file_name;   
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
						  
						  
 InsertRcrdsByData(CHANGE_REQ,"`OrderID` = '$orderIID',`TransactionOrderID` = '$OrderUID',`ProductID` = '".$_REQUEST['ProductID']."', `UserID`='$AccessID', `DesignerID`='".$ItemOrder['AssignedTo']."', `MessageText` ='".addslashes($pChanges)."', `Attachment`='".serialize($attachementName)."', `AttechmentName` = '".serialize($attachementName)."', `UserReadState` = '1', `CreationDate` = '$systemTime', `SystemIP` = '$systemIp', `24HoursChk` = '".$terms."' ,ResponseState='3' ".$ExtraInsert.", `Type` = 'user'");                 

                UpdateRcrdOnCndi(ORDER, "`ResponseState` = '2'", "`TransactionID` = '$OrderUID' AND `Id` = '$orderIID' AND `CustomerID` = '$AccessID'");
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
            }
			

            $_SESSION['SUCCESS'] = "Request for changes is successfully submited.";
        } else {
            $_SESSION['ERROR'] = "Order item not assigned under your account.";
        }
    } else {
        $_SESSION['ERROR'] = "Invalid Action";
    }
    echo "<script> window.location.href = '".SITEURL."order-details.php?order_id=".$OrderUID."';</script>";
    exit();
}

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

$Filename1 = $OrderData[0]['Filename1'];
$Filename2 = $OrderData[0]['Filename2'];
$Filename3 = $OrderData[0]['Filename3'];
$Filename4 = $OrderData[0]['Filename4'];
$Filename5 = $OrderData[0]['Filename5'];
$prodOtherSizes = getProdSizeArr();

if(isset($_REQUEST['add_p_comment_rating'])){

    $product_rating_comment_id = $_REQUEST['product_rating_comment_id'];
    $product_comment = !empty($_REQUEST['product_comment']) ? $_REQUEST['product_comment'] : "";
    $product_rating = !empty($_REQUEST['product_rating']) ? $_REQUEST['product_rating'] : NULL;

    if($product_rating_comment_id > 0) {
        UpdateRcrdOnCndi( PRODUCT_RATING_COMMENT, "`comment` = '" . $product_comment . "', `rating` = '" . $product_rating . "', `status` = '0'", "id = '$product_rating_comment_id'" );
        $orderId = $product_rating_comment_id;
    }else {
        $fn = ["product_id",
               "order_id",
               "user_id",
               "comment",
               "rating",
               "status"];
        $fv = [$productID,
               $OrderUID,
               $UserID,
               $product_comment,
               $product_rating,
               0];
        $orderId = InsertRcrdsGetID( $fn, $fv, PRODUCT_RATING_COMMENT );
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => SITEURL.'EmailTemplate/admin_notify_for_comment_rating.php.php?order_id='.$OrderUID,
        CURLOPT_USERAGENT => 'Curl Test'
    ));
    $resp = curl_exec($curl);
    curl_close($curl);

    $_SESSION['SUCCESS'] = "Comment & rating is added successfully submited.";
    echo "<script> window.location.href = '".SITEURL."order-details.php?order_id=".$OrderUID."';</script>";
    exit();
}

$ProductArr = getALLProductArr();
$AddonArr = getAddonArr();
$ProductSize = getProdSizeArr();
$ProductType = getProdTypeArr();
$ProductCommentAndReview = GetSglRcrdOnCndi(PRODUCT_RATING_COMMENT, "`order_id` = '$OrderUID' AND `product_id` = '$productID' AND `user_id` = '$UserID'");


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
            <h1 class="page-heading mb-4">Order #<?=$OrderUID;?> - <?=$AccessData['FName'].' '.$AccessData['LName'];?> - <?=date('M d, Y',$OrderDate);?></h1>

            <div class="row mt-5">
                <div class="col-lg-6 brd-lg-right pr-lg-4 pl-lg-4">
                    <h2 class="blue text-center mb-4">Order details</h2>
                    <div class="mb-4 order-details">
                        <p>Ordered on:</p>
                        <h3><?=date('D M d, Y',$OrderDate);?> <span><?=date('g:i A',$OrderDate);?></span></h3>
                    </div>
                   <?php /*?> <div class="mb-4 order-details">
                        <p>Ordered by:</p>
                        <h3><?=$AccessData['FName'].' '.$AccessData['LName'];?><br /> <span style="margin-left:0px;"><?=$AccessData['Email'];?></span></h3>
                    </div><?php */?>
                    <div class="mb-4 order-details order-progress">
                        <p>Order status:</p>
                        <h3><?php
                            $getRespnseCode = array();
							$orderStatusReview = 0;
                            foreach($OrderData as $k=>$v){
                                $getRespnseCode[$v['ResponseState']] = $v['ResponseState'];
                            }
							
							 if($OrderData[0]['is_approve'] == 1){
								 $orderStatusReview = 1;
                                echo "Approved";
                            }else{

                           
						  if($AssignTo==0 && $OrderStatus==4 ) {
								echo "Pending Payment";
							}else if($AssignTo==0 &&  $OrderStatus==0) {
                                echo "New";
                            }else if($AssignTo>0 && $OrderStatus==1 && in_array(3,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(2,$getRespnseCode)) {
                                echo "Submitted to customer";
                            }else if($AssignTo>0 && $OrderStatus==1 && in_array(1,$getRespnseCode)  && !in_array(2,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
                                echo "Approved & done";
								//$orderStatusReview = 1;
                            }else if($AssignTo>0 && $OrderStatus==1 && in_array(2,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
                                echo "Revisions";
                            }else if($AssignTo>0 && $OrderStatus==1 ) {
                                echo "In progress";
                            }
							}


                            ?></h3>
                    </div>
                    <div class="mb-4 order-details">
                        <p>Order details:</p>
                        <ol class="order-products">
                            <?php 
                            
							 $getMotionsArray  = array();
						   foreach($OrderData as $OrderList) { 
						   $TypeBanner = explode(",",$OrderList['TypeBanner']);
								if(!empty($TypeBanner)){
									$getMotionsArray[$OrderList['ProductID']]	=$TypeBanner;
								}   
							 }
							 $totalPrice = 0;
                            $mainProductName = "";
							$allowed =  array('gif','png' ,'jpg');
                           $j=0;
						    foreach($OrderData as $OrderList) {

                                if(!isset($ProductArr[$OrderList['ProductID']])) { continue; }
								
								$product=GetSglRcrdOnCndi(PRODUCT, "  id=".$OrderList['ProductID']);
								$productSlug = $product['slug'];	
								
								


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

                                ?>

                                   <li><?=$productDisplayName;?> <span class="type"><?=$ProductType[$ProductArr[$OrderList['ProductID']]['parent_product_cat_id']]['name'];?></span> <span class="price">($<?=$OrderList['TotalPrice'];?>)</span>
                                     
                                    <div class="size">
										<?php if(empty($OrderList['customeProductsFileds'])){?>
									<?php echo ($Dimensional!="") ? "(".$Dimensional.")":"";
                                        echo ($TypeBannerListStr!="")? $TypeBannerListStr:"";;
                                        echo ($TurnAroundTime!="" && $TurnAroundTime>0) ? "(".getTurnArroundTypeByID($TurnAroundTime).")":"";
                                        ?><?php if(!empty($ProductSize[$OrderList['DefaultSize']]['name'])) { echo "(".$ProductSize[$OrderList['DefaultSize']]['name'].")"; } 
										
										 if(!empty( $listOSStr)) { echo $listOSStr; } ?>
                                         <?php }
										 if(!empty($OrderList['customeProductsFileds'])){
										  echo ($TurnAroundTime!="" && $TurnAroundTime>0) ? "(".getTurnArroundTypeByID($TurnAroundTime).")":"";}
										  ?>
                                        
                                          <span style="margin-left:0px;" class="approve2 view_details" onClick="OpenInstructionsProduct('<?php echo $j; ?>')"><a>View Details</a></span>

                                         </div>
                                         
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
						
						$Filename1 = $OrderList['Filename1'];
						$Filename2 = $OrderList['Filename2'];
						$Filename3 = $OrderList['Filename3'];
						$Filename4 = $OrderList['Filename4'];
						$Filename5 = $OrderList['Filename5'];
						
						if(!empty($OrderList['customeProductsFileds'])){
							
							$customeProductsFileds = unserialize($OrderList['customeProductsFileds']);
							if(isset($customeProductsFileds) && !empty($customeProductsFileds)){
												$checkCustomProduct =1;
												foreach($customeProductsFileds as $customeProductFieldsKey=>$customeProductFieldsValue){
													
													foreach($customeProductFieldsValue as $filedsPrimaryIndex=>$filedsPrimaryIndexValue){
														
														foreach($filedsPrimaryIndexValue as $filedsIndex=>$filedsIndexValue){
												
														$FiledsLabal = $customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex];
														$Filedsvalue = $filedsIndexValue;
														if($filedsIndex=="turnaround_time"){
															$Filedsvalue = $deliArr[$Filedsvalue];
														}
														 if(($filedsIndex=="checkbox_sided" || $filedsIndex=="add_music" || $filedsIndex=="add_video" || $filedsIndex=="add_facebook_cover") && $Filedsvalue=="on"){
															 		echo  ' <div class="customer_details"><strong>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].':</strong> Yes</span></div>';
														
														}else{
														
														if(($filedsIndex=="files" || $filedsIndex=="music_file" || $filedsIndex =="attach_logo" || $filedsIndex=="attach_any_logos" || $filedsIndex=="attach_your_logo_design" || $filedsIndex=="vector_psd_pdf" || $filedsIndex=="attach_any_pictures"  || $filedsIndex=="attach_any_style_reference") && !empty($Filedsvalue)){
															
															
														  $filesData = array();
															
														if($customeProductFieldsKey=="mixtape_cover_design"){
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

                        <?php if(!empty($Filename1) || !empty($Filename2) || !empty($Filename3) || !empty($Filename4) || !empty($Filename5)) {?>
                            <div class="customer_details">
                                <strong>Photos and logos: </strong>
                                <ul class="list-unstyled mb-0 pl-4 popup-gallery">

                                    <?php if(! empty( $Filename1 ) && file_exists( SITE_BASE_PATH . $Filename1 )){ ?>
                                    
                                    		
                                        <li id="eImage1">
                                        <?php 
										$ext = pathinfo($Filename1, PATHINFO_EXTENSION);
										if(!in_array($ext,$allowed)) {
										?>
                                        <a href="<?=SITEURL.$Filename1;?>" target="_blank" class="text-white WhiteText">
                                   
                                    
                                    <i class="fas fa-file pr-2"></i> <?php echo basename($Filename1); ?><?php ?></a>   <a href="<?=SITEURL.$Filename1;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> 
                                        
                                        <?php }else{ ?>
                                        <a href="<?=SITEURL.$Filename1;?>" class="text-white magnificPopup WhiteText"><i class="fas fa-image pr-2"></i> image.jpg</a><a href="<?=SITEURL.$Filename1;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a>
                                        <?php } ?>
                                        
                                        </li>
                                    <?php } ?>

                                    <?php if(! empty( $Filename2 ) && file_exists( SITE_BASE_PATH . $Filename2 )){ ?>
                                        <li id="eImage1">
                                        
                                        <?php 
										$ext = pathinfo($Filename2, PATHINFO_EXTENSION);
										if(!in_array($ext,$allowed)) {
										?>
                                        <a href="<?=SITEURL.$Filename2;?>" target="_blank" class="text-white WhiteText">
                                   
                                    
                                    <i class="fas fa-file pr-2"></i> <?php echo basename($Filename2); ?></a>   <a href="<?=SITEURL.$Filename2;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> 
                                        
                                        <?php }else{ ?>
                                        <a href="<?=SITEURL.$Filename2;?>" class="text-white magnificPopup WhiteText"><i class="fas fa-image pr-2"></i> image.jpg</a><a href="<?=SITEURL.$Filename2;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a>
                                        <?php } ?>
                                        
                                        </li>
                                    <?php } ?>

                                    <?php if(! empty( $Filename3 ) && file_exists( SITE_BASE_PATH . $Filename3 )){ ?>
                                        <li id="eImage1">
                                        <?php 
										$ext = pathinfo($Filename3, PATHINFO_EXTENSION);
										if(!in_array($ext,$allowed)) {
										?>
                                        <a href="<?=SITEURL.$Filename3;?>" target="_blank" class="text-white WhiteText">
                                   
                                    
                                    <i class="fas fa-file pr-2"></i> <?php echo basename($Filename3); ?></a>   <a href="<?=SITEURL.$Filename3;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> 
                                        
                                        <?php }else{ ?>
                                        
                                        <a href="<?=SITEURL.$Filename3;?>" class="text-white WhiteText magnificPopup"><i class="fas fa-image pr-2"></i> image.jpg</a><a href="<?=SITEURL.$Filename3;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a>
                                        
                                        <?php } ?>
                                        </li>
                                    <?php } ?>

                                    <?php if(! empty( $Filename4 ) && file_exists( SITE_BASE_PATH . $Filename4 )){ ?>
                                        <li id="eImage1">
                                        <?php 
										$ext = pathinfo($Filename4, PATHINFO_EXTENSION);
										if(!in_array($ext,$allowed)) {
										?>
                                        <a href="<?=SITEURL.$Filename4;?>" target="_blank" class="text-white WhiteText">
                                   
                                    
                                    <i class="fas fa-file pr-2"></i> <?php echo basename($Filename4); ?></a>   <a href="<?=SITEURL.$Filename4;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> 
                                        
                                        <?php }else{ ?>
                                        
                                        <a href="<?=SITEURL.$Filename4;?>" class="text-white WhiteText magnificPopup"><i class="fas fa-image pr-2"></i> image.jpg</a><a href="<?=SITEURL.$Filename4;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a>
                                        <?php } ?>
                                        </li>
                                    <?php } ?>

                                    <?php if(! empty( $Filename5 ) && file_exists( SITE_BASE_PATH . $Filename5 )){ ?>
                                        <li id="eImage1">
                                         <?php 
										$ext = pathinfo($Filename5, PATHINFO_EXTENSION);
										if(!in_array($ext,$allowed)) {
										?>
                                        <a href="<?=SITEURL.$Filename5;?>" target="_blank" class="text-white WhiteText">
                                   
                                    
                                    <i class="fas fa-file pr-2"></i> <?php echo basename($Filename5); ?></a>   <a href="<?=SITEURL.$Filename5;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a> 
                                        
                                        <?php }else{ ?>
                                        
                                        
                                        <a href="<?=SITEURL.$Filename5;?>" class="text-white magnificPopup WhiteText"><i class="fas fa-image pr-2"></i> image.jpg</a><a href="<?=SITEURL.$Filename5;?>" download class="DowloadIcon"><i class="fas fa-download pr-2"></i></a>
                                        <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php }} ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
                                </li>
                            <?php }
                            ?>
                        </ol>
                    </div>
                    
                     <?php if($TransactionData['DiscountName']!=""){ ?>
                    <div class="mb-4 order-details">
                        <p>Discount code:</p>
                        <h3><?php echo $TransactionData['DiscountName']; ?>  (-$<?php echo formatPrice($TransactionData['DiscountAmount']);?>) <span>USD</span></h3>
                    </div>
                    <?php } ?>
                    <div class="mb-4 order-details">
                        <p>Total:</p>
                        <h3>$<?=formatPrice($TransactionData['Amount']);?> <span>USD</span></h3>
                    </div>
                    <div class="mb-4 order-details">
                        <p>Transaction:</p>
                        <h3>Order# <span><?=$TransactionID;?></span></h3>
                        <h3><strong style="font-size: 20px;"><?=$TransactionData['TransactionID'];?></strong> <span><?=$TransactionData['PaymentMethod'];?> checkout</span></h3>
                    </div>
                    
                </div>
                <?php 
				 $allowed =  array('gif','png' ,'jpg');
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
						
				?>
                
                <div class="col-lg-6 pr-lg1 pl-lg-4">
                <?php 
				$reviewTitle  = 0;
			
					foreach($OrderData as $OrderList) {
						$checkTotalAccepted = GetNumOfRcrdsOnCndi(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND `ProductID` = '".$OrderList['ProductID']."' and ResponseState='1'", "ID", "ASC");
						$mainProductReview = 0;
						    $prod = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$OrderList['ProductID']."'");
						 if($OrderList['parent_product_id']>0){}else{if($prod['Addon']==1){}else{
									$mainProductReview = 1;	
									}
								}
								
								if($mainProductReview ==1 && (count($finalCountProductArray[$OrderList['Id']][$OrderList['ProductID']])==$checkTotalAccepted || count($finalCountProductArray[$OrderList['Id']][$OrderList['ProductID']])<$checkTotalAccepted) && $checkTotalAccepted>0){
									$applyForReview = 1;	
									
									
									$productDisplayName = '<a style="color:#fff;text-decoration:none;font-weight:500;" href="'.SITEURL.'p/'.$productSlug.'" target="_blank">'.$ProductArr[$OrderList['ProductID']]['Title']."</a>";	
									
									
									if($reviewTitle ==0){
										
										
											
									
									?>
                                    <h2  class="blue text-center mb-4">Review</h2>
                                    <?php 		
									}
									$reviewTitle++;
								?>
                                
                                             <?php 
										
												?>
                                                <div class="ReviewSectionBlock">
                                                <form method="post" class="ReviewForm ReviewForm<?php echo $OrderList['ProductID'];?> pl-md-1 pr-md-1 pb-5" enctype="multipart/form-data" name="form_comment_rating" id="form_comment_rating<?php echo $OrderList['ProductID']; ?>">
                                    				<h5><?PHP echo $productDisplayName; ?></h5>
												<?php 
													$htmlReview = getReviewHtmlByProductIDOrderID($_REQUEST['order_id'],$OrderList['ProductID']);
												if($htmlReview==""){
											?>
                                                <div class="reviewFormAdd">
                            
                                    <div class="row">
                                    <div class=" col-md-12 ">
                                    	<div class="PleaseRate">Please rate it</div>
                                    	<div class="form-group">
                                    
                                        <div class="rating-star" id="rating-start-product<?php echo $OrderList['ProductID']; ?>">
                                            <span data-id="100" data-val="1" data-order="<?php echo $OrderList['ProductID']; ?>"></span>
                                            <span data-id="100" data-val="2" data-order="<?php echo $OrderList['ProductID']; ?>"></span>
                                            <span data-id="100" data-val="3" data-order="<?php echo $OrderList['ProductID']; ?>"></span>
                                            <span data-id="100" data-val="4" data-order="<?php echo $OrderList['ProductID']; ?>"></span>
                                            <span data-id="100" data-val="5" data-order="<?php echo $OrderList['ProductID']; ?>"></span>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                    
                                    <div class="row">
                                    <div class="col-md-12">
                                    	<div class="form-group">
                                        	
                                             <textarea style="margin-bottom:0px !important;" id="product_review_comment<?php echo $OrderList['ProductID']; ?>" name="product_review_comment" placeholder="Tell us how you liked the product?" class="form-control mb-4"></textarea>
                                        </div>
                                        </div>
                                    </div>
                                    </div>
                                   
                                   

                                    <input type="hidden" name="product_rating" id="product_rating<?php echo $OrderList['ProductID']; ?>">

 <input type="hidden" name="OrderID" id="OrderID<?php echo $OrderList['ProductID']; ?>" value="<?php echo $_REQUEST['order_id']; ?>">

                                    <input type="hidden" name="ReviewProductID" id="ReviewProductID<?php echo $OrderList['ProductID']; ?>" value="<?php echo $OrderList['ProductID']; ?>">

                                   

                                    
                                  
                                    <div class="notification error rating-require<?php echo $OrderList['ProductID']; ?>" style="display: none;">
                                        <div class="d-flex"><i class="fas fa-times-circle"></i><span>Error: Please select rating</span></div>
                                        
                                    </div>
                                    
                                    <div class="notification error rating-require-review<?php echo $OrderList['ProductID']; ?>" style="display: none;">
                                        <div class="d-flex"><i class="fas fa-times-circle"></i><span>Error: Please enter review</span></div>
                                        
                                    </div>
                                    
                                    
                                    <div class="notification error rating-message<?php echo $OrderList['ProductID']; ?>" style="display: none;">
                                        <div class="d-flex"><i class="fas fa-times-circle"></i><span></span></div>
                                        
                                    </div>
                                    <div class="review_submit text-right">
                                      
                                    
                                        <button type="submit" name="add_p_comment_rating" class="add_p_comment_rating">Post</button>
                                       
					
                                    </div>
                                    <?php } ?>
                                    <div  id="customerReviewListing<?php echo $OrderList['ProductID']; ?>"><?php echo $htmlReview; ?></div>
                                     

<script>
   
    $(function () {
        var artaraxRatingStar = $.artaraxRatingStar({
            onClickCallBack: onRatingStar,
        });
        function onRatingStar(rate, id,hoverOrder) {
            jQuery("#product_rating"+hoverOrder).val(rate);
            $(".rating-require").hide();
        }
		
    });
    
   jQuery(document).on("submit","#form_comment_rating<?php echo $OrderList['ProductID']; ?>",function( event ) {
		  event.preventDefault();
		  $(".rating-require<?php echo $OrderList['ProductID']; ?>,.rating-require-title<?php echo $OrderList['ProductID']; ?>,.rating-require-review<?php echo $OrderList['ProductID']; ?>,.rating-message<?php echo $OrderList['ProductID']; ?>").hide();
		  var ratingdata =true;	
		  if( $("#product_rating<?php echo $OrderList['ProductID']; ?>").val() == undefined || $("#product_rating<?php echo $OrderList['ProductID']; ?>").val() == '') {
                event.preventDefault();
                $(".rating-require<?php echo $OrderList['ProductID']; ?>").show();
				ratingdata = false;
          }
		 
		  
		  if( $("#product_review_comment<?php echo $OrderList['ProductID']; ?>").val() == undefined || $("#product_review_comment<?php echo $OrderList['ProductID']; ?>").val() == '') {
                event.preventDefault();
                $(".rating-require-review<?php echo $OrderList['ProductID']; ?>").show();
				ratingdata = false;
          }
		  if(ratingdata){
			  $(".mainLoader").show();
		  	jQuery.ajax({
			type: 'POST',
			dataType: 'json',
		   url: "<?=SITEURL;?>ajax/product-review-add.php",
			data:{
			
				 'data': jQuery("#form_comment_rating<?php echo $OrderList['ProductID']; ?>").serialize()
				},
				error: function (jqXHR, exception) { 
				
				},success: function(data){
					
					if(data.status=="success"){
						
						jQuery(".rating-message<?php echo $OrderList['ProductID']; ?>").removeClass("error");
						jQuery(".rating-message<?php echo $OrderList['ProductID']; ?>").addClass("success");
						jQuery(".rating-message<?php echo $OrderList['ProductID']; ?>").show();
						jQuery(".rating-message<?php echo $OrderList['ProductID']; ?> .d-flex").html('<i class="fas fa-times-circle"></i><span>'+data.msg+'</span>');	
						$.ajax({type:"POST",url:"<?=SITEURL;?>ajax/order_product_review_listing.php",dataType: 'json',   data: "order_id=<?php  echo $_REQUEST['order_id']?>&productID=<?php echo $OrderList['ProductID']; ?>",success:function(regResponse){
						$(".mainLoader").hide();
							jQuery("#customerReviewListing<?php echo $OrderList['ProductID']; ?>").html(regResponse.html);
							jQuery(".ReviewForm<?php echo $OrderList['ProductID']; ?> .reviewFormAdd,.ReviewForm<?php echo $OrderList['ProductID']; ?> .review_submit").remove();
								
								 setTimeout(function(){ 
								jQuery(".rating-message<?php echo $OrderList['ProductID']; ?>").remove();}, 2000);
								 
								
	
						}
					});
					}
					if(data.status=="error"){
						jQuery(".rating-message<?php echo $OrderList['ProductID']; ?>").removeClass("success");
						jQuery(".rating-message<?php echo $OrderList['ProductID']; ?>").addClass("error");
						jQuery(".rating-message<?php echo $OrderList['ProductID']; ?>").show();
						jQuery(".rating-message<?php echo $OrderList['ProductID']; ?> .d-flex").html('<i class="fas fa-times-circle"></i><span>'+data.msg+'</span>');	
					}
				
				}
		});	
		  }
			
        });

</script>
                           
                            </form>
                           					</div>
                                                <?php 
												$r++;	
											 	
								}
								
					}
				?>
                

                    <h2 class="blue text-center mb-4">Media</h2>

                    <?php if(GetNumOfRcrdsOnCndi(ORDER,"`ResponseState` <> 0 AND `TransactionID` = '$OrderUID' AND `CustomerID` = '$AccessID'") == 0) { ?>
                        <div class="order-ntf green">
                            <strong>Hello <?=$AccessData['FName']." ".$AccessData['LName'];?></strong><br> We are busy working on your order, when the files are ready, they will be uploaded and will be visible for you to look at below
                            <figure class="text-center mb-5 mt-4">
                                <img src="images/customer.png" class="img-fluid" alt="">
                            </figure>

                        </div>
                    <?php } ?>
                    <div class="mb-4 order-details">

                        <ol class="order-products rightSideDesing">

                            <?php

                             
							foreach($OrderData as $OrderList) {
								
								

                                $prod = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$OrderList['ProductID']."'");
								$productSlug = $prod['slug'];
								

                              
                              if($OrderList['parent_product_id']>0){
									$productDisplayName = $ProductArr[$OrderList['parent_product_id']]['Title']." ".$ProductArr[$OrderList['ProductID']]['Title'];
										
								}else{
									if($prod['Addon']==1){
										
											$productDisplayName = $ProductArr[$OrderList['ProductID']]['Title'];	
										
									}else{
									$productDisplayName = '<a href="'.SITEURL.'p/'.$productSlug.'" target="_blank">'.$ProductArr[$OrderList['ProductID']]['Title']."</a>";	
									}
								}
								
								$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");
								
								
								
								$imgsrc = "";
								$imgurl = "";
									if($OrderList['parent_product_id']>0){
									
										
								}else{
									 $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$OrderList['ProductID']."' AND `filetype` = 'image'","id","ASC");
									  $imgsrc ="";
									  if (strpos($getBanners['filename'],'res.cloudinary.com') !== false)
                                        {
											$imgsrc = '<img  src="'.httpToSecure($getBanners['filename']).'">';
											$imgurl = httpToSecure($getBanners['filename']);
                                        }
                                        else
                                        {
											$imgsrc ='<img  src="uploads/products/'.$OrderList['ProductID'].'/'.$getBanners['filename'].'">';
											$imgurl = "uploads/products/".$OrderList['ProductID'].'/'.$getBanners['filename'];
                                           
                                        }
									
								
								}
								
								
								$ChangeRequest = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND `ProductID` = '".$OrderList['ProductID']."'  and type='Admin'", "ID", "DESC");
								if(!empty($ChangeRequest['Attachment'])){
									$ImageUrlData = unserialize($ChangeRequest['Attachment']);	
									if(!empty($ImageUrlData)){
										$imgurl = SITEURL."uploads/work/".$ImageUrlData[0];
										
										$ext = pathinfo($ImageUrlData[0], PATHINFO_EXTENSION);
											if(!in_array($ext,$allowed)) {
												
												$imgsrc ='<video width="130" height="200" controls>
                                                             <source src="'.$imgurl.'" type="video/mp4">
                                                        </video>';
											}else{
													 
										
												$imgsrc ='<img  src="'.$imgurl.'">';
											}
									}
								}
							  if(!empty($ChangeRequest)){

                                    //Submit

                                    ?>
                                    <li>


<h3 class="ProductTItleRight" style="font-size:22px;"><?= $productDisplayName;?>:</h3>
                                  
                                       
                                        <?php 
										
										if(!empty($NewOrderArray[$OrderList['Id']][$OrderList['ProductID']])){
											?>
                                            <span class="MultiPlayVartion">Multiple variations</span>
                                            <?php 
											$r=0;
											
											foreach($NewOrderArray[$OrderList['Id']][$OrderList['ProductID']] as $singleSize){
												
												$selectChangeRequest = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "ASC");
												
													$selectChangeRequestCount = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND  TypeBanner='static' AND Size='".$singleSize."' and `ProductID` = '".$OrderList['ProductID']."' AND `UserID` = '$AccessID' and FirstTime='no'", "ID", "ASC");
													
												if(!empty($selectChangeRequest)){
													
												$ChangeRequest = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."' and type='Admin'", "ID", "DESC");
												
												$CheckLastRevision = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='static' AND Size='".$singleSize."' AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "DESC");
													
												
								if(!empty($ChangeRequest['Attachment'])){
									$ImageUrlData = unserialize($ChangeRequest['Attachment']);	
									if(!empty($ImageUrlData)){
										$imgurl = SITEURL."uploads/work/".$ImageUrlData[0];
										
										$ext = pathinfo($ImageUrlData[0], PATHINFO_EXTENSION);
											if(!in_array($ext,$allowed)) {
												
												$imgsrc ='<video width="130" height="200" controls>
                                                             <source src="'.$imgurl.'" type="video/mp4">
                                                        </video>';
											}else{
													 
										
												$imgsrc ='<img  src="'.$imgurl.'">';
											}
									}
								}
													
												?>
                                                
                                                <div class="StaticType <?php if($CheckLastRevision['ResponseState']==3) {?>request_change<?php }else if($CheckLastRevision['ResponseState']==1){?>order_complete<?php }else{ ?>order_panding<?php } ?>">
     
                                               <div class="customer-media">

                                            <h3 class="SubtitleProduct">Static - <?php echo $ProductSize[$singleSize]['name']; ?></h3>
                                   

<div class="row_section">

                                                <div class="customer-image mr-3">

                                                                <div class="popup-gallery">
                                                                   
                                                                    <a href="<?=$imgurl;?>" class="magnificPopup"> <?php echo $imgsrc; ?></a>
                                                                      <div class="Download"><a  download href="<?php echo $imgurl ?>">Download</a></div>
                                                                  
                                                                </div>

                                                            </div>

                                                <div class="customer-options">
															<?php if($ChangeRequest['ResponseState']==1){ ?>	
															 <a href="javascript:void(0)" class="approve"  style="background: #1baa32;">approved</a>
                                                        <?php } ?>
                                                         <?php if(count($selectChangeRequestCount)>0 && $ChangeRequest['ResponseState']!=1){ ?>
                                                                <a class="rq-view_rivision text-white" data-type='static' data-size='<?php echo $singleSize; ?>'  data-orderid="<?= $OrderList['Id'];?>" data-productid="<?=$OrderList['ProductID'];?>" data-userid='<?php echo $AccessID; ?>'>View revisions (<?php echo count($selectChangeRequestCount); ?>)</a>
                                                                <?php } ?>
                                                        
                                                         <?php if($ChangeRequest['ResponseState']!=1){ ?>	
                                                         
                                                          <a href="order-details.php?order_id=<?php echo $_REQUEST['order_id']; ?>&id=<?php echo $OrderList['Id'] ?>&mode=approvemockup&RequestId=<?php echo $ChangeRequest['ID']; ?>" onClick="return confirm('Are you sure you want to approve?')" class="approve exta_mockup_app_btn status">Approve</a>
                                                          
                                                         <a class="rq-change text-white" data-title="<?php echo  $prod['Title'];?> - Static - <?php echo $ProductSize[$singleSize]['name']; ?>" data-id="<?=$OrderList['Id'];?>" data-image='<?php echo $filePath; ?>' data-productid="<?php echo $prod['id']; ?>" bannertype="Static" bannersize='<?php echo $singleSize; ?>' bannersizelabel='<?php echo $ProductSize[$singleSize]['name']; ?>'>Request revision</a>
                                                         <?php  }?>
                                                               
                                                         

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
										
													$selectChangeRequestCount = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND  TypeBanner='default' and `ProductID` = '".$OrderList['ProductID']."' AND `UserID` = '$AccessID' and FirstTime='no'", "ID", "ASC");
											
											?>
                                        
                                          <div class="SimpleType <?php if($CheckLastRevision['ResponseState']==3) {?>request_change<?php }else if($CheckLastRevision['ResponseState']==1){?>order_complete<?php }else{ ?>order_panding<?php } ?>">
     
                                               <div class="customer-media">

                                        
<div class="row_section">

                                                <div class="customer-image mr-3">

                                                                <div class="popup-gallery">
                                                                   
                                                                    <a href="<?=$imgurl;?>" class="magnificPopup"> <?php echo $imgsrc; ?></a>
                                                                     <div class="Download"><a  download href="<?php echo $imgurl ?>">Download</a></div>
                                                                  
                                                                </div>

                                                            </div>

                                                <div class="customer-options">
															<?php if($ChangeRequest['ResponseState']==1){ ?>	
															 <a href="javascript:void(0)" class="approve"  style="background: #1baa32;">approved</a>
                                                        <?php } ?>
                                                         <?php if(count($selectChangeRequestCount)>0 && $ChangeRequest['ResponseState']!=1){ ?>
                                                                <a class="rq-view_rivision text-white" data-type='' data-size=''  data-orderid="<?= $OrderList['Id'];?>" data-productid="<?=$OrderList['ProductID'];?>" data-userid='<?php echo $AccessID; ?>'>View revisions (<?php echo count($selectChangeRequestCount); ?>)</a>
                                                                <?php } ?>
                                                        
                                                         <?php if($ChangeRequest['ResponseState']!=1){ ?>	
                                                         
                                                          <a href="order-details.php?order_id=<?php echo $_REQUEST['order_id']; ?>&id=<?php echo $OrderList['Id'] ?>&mode=approvemockup&RequestId=<?php echo $ChangeRequest['ID']; ?>" onClick="return confirm('Are you sure you want to approve?')" class="approve exta_mockup_app_btn status">Approve</a>
                                                          
                                                         <a class="rq-change text-white" data-title="<?= $prod['Title'];?>" bannertype="" bannersize='' bannersizelabel='' data-id="<?=$OrderList['Id'];?>" data-image='<?php echo $filePath; ?>' data-productid="<?php echo $prod['id']; ?>">Request revision</a>
                                                         <?php  }?>
                                                               
                                                         

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
										$imgurl = SITEURL."uploads/work/".$ImageUrlData[0];
										
										$ext = pathinfo($ImageUrlData[0], PATHINFO_EXTENSION);
											if(!in_array($ext,$allowed)) {
												
												$imgsrc ='<video width="130" height="200" controls>
                                                             <source src="'.$imgurl.'" type="video/mp4">
                                                        </video>';
											}else{
													 
										
												$imgsrc ='<img  src="'.$imgurl.'">';
											}
									}
								}
								$CheckLastRevision = GetSglRcrdOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."'  AND TypeBanner='motion'  AND `ProductID` = '".$OrderList['ProductID']."'", "ID", "DESC");
									$selectChangeRequestCount = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "`OrderID` = '".$OrderList['Id']."' AND  TypeBanner='motion' and `ProductID` = '".$OrderList['ProductID']."' AND `UserID` = '$AccessID' and FirstTime='no'", "ID", "ASC");
													
												?>
                                                
                                             <div class="MotionType <?php if($CheckLastRevision['ResponseState']==3) {?>request_change<?php }else if($CheckLastRevision['ResponseState']==1){?>order_complete<?php }else{ ?>order_panding<?php } ?>">
     
                                               <div class="customer-media">

                                            <h3 class="SubtitleProduct">Motion</h3>
                                   

<div class="row_section">

                                                <div class="customer-image mr-3">

                                                                <div class="popup-gallery">
                                                                   
                                                                    <a href="<?=$imgurl;?>" class="magnificPopup"> <?php echo $imgsrc; ?></a>
                                                                     <div class="Download"><a  download href="<?php echo $imgurl ?>">Download</a></div>
                                                                  
                                                                </div>

                                                            </div>

                                                <div class="customer-options">
															<?php if($ChangeRequest['ResponseState']==1){ ?>	
															 <a href="javascript:void(0)" class="approve"  style="background: #1baa32;">approved</a>
                                                        <?php } ?>
                                                         <?php if(count($selectChangeRequestCount)>0 && $ChangeRequest['ResponseState']!=1){ ?>
                                                                <a class="rq-view_rivision text-white" data-type='motion' data-orderid="<?= $OrderList['Id'];?>" data-productid="<?=$OrderList['ProductID'];?>" data-userid='<?php echo $AccessID; ?>'>View revisions (<?php echo count($selectChangeRequestCount); ?>)</a>
                                                                <?php } ?>
                                                        
                                                         <?php if($ChangeRequest['ResponseState']!=1){ ?>	
                                                         
                                                          <a href="order-details.php?order_id=<?php echo $_REQUEST['order_id']; ?>&id=<?php echo $OrderList['Id'] ?>&mode=approvemockup&RequestId=<?php echo $ChangeRequest['ID']; ?>" onClick="return confirm('Are you sure you want to approve?')" class="approve exta_mockup_app_btn status">Approve</a>
                                                          
                                                         <a class="rq-change text-white" data-title="<?= $prod['Title'];?> - Motion" bannertype="Motion" bannersize='' bannersizelabel='' data-id="<?=$OrderList['Id'];?>" data-image='<?php echo $filePath; ?>' data-productid="<?php echo $prod['id']; ?>">Request revision</a>
                                                         <?php  }?>
                                                               
                                                         

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
                </div>
            </div>
        </div>
        


    </div>
</main>

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



                                <div class="d-flex pr-media-upload">

                                    <label class="custom-file">

                                        <input type="file" accept="image/*" name="filename[]" multiple  id="file" class="custom-file-input">

                                        <input type="hidden" name="orderIID" id="orderIID" value="" />
                                        <input type="hidden" name="orderType" id="orderType" value="" />
                                        <input type="hidden" name="ProductID" id="ProductID" value="" />
                                        <input type="hidden" name="BannerType" id="BannerType" value="" />
                                        <input type="hidden" name="BannerSize" id="BannerSize" value="" />
                                        <input type="hidden" name="BannerSizeLabel" id="BannerSizeLabel" value="" />
                                        <input type="hidden" name="ResponseFile" id="ResponseFile" value="" />

                                        <span class="custom-file-control"></span>

                                    </label>
                                    
                                    

                                 
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
<div id="popup1" class="popup">

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
<?php require_once 'files/footerSection.php' ?>
<!----SCRIPTS---------->
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.artarax.rating.star.js"></script>


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
   
    jQuery(".rq-view_rivision").click(function(){
        var  $ = jQuery;
		jQuery(".mainLoader").show();
        var orderIID = $(this).data("orderid")
		  var type = $(this).data('type');
		   var size = $(this).data('size');

        var ProductID = $(this).data("productid");
        jQuery.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/view-rivision.php",
            data: "id="+orderIID+'&ProductID='+ProductID+"&type="+type+"&size="+size,
            success: function(regResponse) {
   jQuery(".mainLoader").hide();
                /*console.log(regResponse);*/

                regResponse = JSON.parse(regResponse);

                $("#popupBoxRivision").html(regResponse.message);
				 $(".revision_title").html(regResponse.titleBlock);

                $("#popup1").addClass("active");

            }
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
	
	
   
    jQuery(".rq-change").click(function(){
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
                    jQuery("#rq-popup .prodTitle").html("Second Revision  = $<?php echo MEDIA_CHANGE_PRICE; ?>");
                    jQuery(".request-form .ml-auto").val("Proceed to pay $<?php echo MEDIA_CHANGE_PRICE; ?>");
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
    $(".close-btn").click(function(){
        $("#rq-popup").removeClass("active");
		 $("#popup1").removeClass("active");
		
     jQuery(".AddonsInstruction,.ProductInstruction").removeClass("active");

    });
</script>
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

#popup1 .customer_content .popup-gallery {
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
#popup1 .customer_content .popup-gallery img{object-fit: contain;}
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


</style>

</body>
</html>