<?php

ob_start();
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';

if(empty($_SESSION['CART']) && empty( $_SESSION['CartRequest'] )){
	header("location:".SITEURL);
	exit;
}
if(empty($_SESSION['userId'] )){
header("location:".SITEURL);
	exit;
}
$OrderType = "cart";

if ( ! empty( $_SESSION['CartRequest'] ) ) {
    $jm = 0;
    $OrderType = "cartRequest";
    $orderIDS = "";
    $noOfOrders = count( $_SESSION['CartRequest'] );
    foreach ( $_SESSION['CartRequest'] as $key => $value ) {
        if ( $jm == 0 ) {
            $orderIDS = $key;
        }
        $jm++;

    }
}

if ( isset( $_SESSION['CART'] ) && $_REQUEST['paypalpayment'] == 'success' && isset( $_POST['txn_id'] ) && ! empty( $_POST['txn_id'] ) ) {


if($_SESSION['loginType']=="guest" && !empty($_SESSION['guest_checkout'])){

if(isset($_SESSION['guest_checkout']['create_account'])){
	
	$password = base64_encode(random_password(15));
	$fn = array("UserType", "Email", "Password", "CreatedDate", "CreatedOn", "Status","FName","LName");
			$fv = array("user", $_SESSION['guest_checkout']['email'], $password, time(), date("Y-m-d H:i:s"), 'active',$_SESSION['guest_checkout']['first_name'],$_SESSION['guest_checkout']['last_name']);
			$userID = InsertRcrdsGetID($fn,$fv,USERS);
			
			$curl = curl_init();
			// Set some options - we are passing in a useragent too here
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => SITEURL.'EmailTemplate/welcome.php?UID='.$userID,
				CURLOPT_USERAGENT => 'Curl Test'
			));
			// Send the request & save response to $resp
			$resp = curl_exec($curl);
			// Close request to clear up some resources
			curl_close($curl);
			
			$mailchimp=syncMailchimp($email,MAILCHIMP_API_KEY,MAILCHIMP_LIST_ID,$fname,$lname);
			$status = "success";
			$_SESSION['userId'] = $userID;
			$_SESSION['userType'] = "user";
			$_SESSION['loginType'] = "user";
			$UserOrderType = 'user';
	
}
}
    $OrderType = "cart";
    $cart = $_SESSION['CART'];
   // unset( $_SESSION['CART'] );

    $tID = $_POST['txn_id'];
    $tAmt = $_POST['payment_gross'];
    $tDate = date( "Y-m-d h:i:s", strtotime( $_POST['payment_date'] ) );
    $tStatus = str_replace( "Completed", "success", $_POST['payment_status'] );

  
          UpdateRcrdOnCndi( TRANSACTION, "`TransactionID` = '".$tID."',`PurchaseDate` = '".$tDate."',`PaymentStatus` = '".$tStatus."',`Status` = '0',`Createdon` = '".$systemTime."'", "`id` = '" . $_SESSION['paymentRecord'] . "'" );
	
  	$AssignedTo = 0;
	$AssignedOn ="";
     $settingarr = GetSglRcrdOnCndi( SETTINGS, "id=1" );
	 if ( $settingarr['auto_assign'] == 'Yes' ) {
            $query_user = "SELECT UserID FROM " . USERS . " WHERE UserType= 'designer'";
            $fetch_arr = mysql_query( $query_user );
            $usersArr = mysql_fetch_array( $fetch_arr );
        	$AssignedTo = $usersArr[0];
			$AssignedOn = $systemTime;
			
			
				  $curl = curl_init();
    curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                               CURLOPT_URL            => SITEURL . 'EmailTemplate/auto_assigned_order.php?designer_id=' . $AssignedTo . "&orderids=" . implode( ",", $_SESSION['thankorder']),
                               CURLOPT_USERAGENT      => 'Curl Test'] );
    $resp = curl_exec( $curl );
    curl_close( $curl );
	 }
	 
	 UpdateRcrdOnCndi( ORDER, "`OrderStatus` = '1',`CustomerID` = '".$_SESSION['userId']."',`ResponseState`='0',`AssignedTo` = '".$AssignedTo."',`AssignedOn` = '".$AssignedOn."'", "`TransactionID` = '" .  $_SESSION['paymentRecord'] . "'" );
  
  
    
    $curl = curl_init();

    curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                               CURLOPT_URL            => SITEURL . 'EmailTemplate/order.php?UID=' . $_SESSION['userId'] . "&orderids=" . implode( ",", $_SESSION['thankorder'] ),
                               CURLOPT_USERAGENT      => 'Curl Test'] );

    $resp = curl_exec( $curl );

    curl_close( $curl );
	
   unset( $_SESSION['CART'] );
    unset( $_SESSION['DISCOUNT_DATA'] );
	
	   unset( $_SESSION['paymentRecord'] );
}
print_r([$_SESSION['CartRequest'],$_REQUEST['paypalpayment'] , $_POST['txn_id']]);
if ( isset( $_SESSION['CartRequest'] ) && $_REQUEST['paypalpayment'] == 'success' && isset( $_POST['txn_id'] ) && ! empty( $_POST['txn_id'] ) ) {
    $tID = $_POST['txn_id'];
    $tAmt = $_POST['payment_gross'];
    $tDate = date( "Y-m-d h:i:s", strtotime( $_POST['payment_date'] ) );


    $paymentRecord = InsertRcrdsGetID( ["TransactionID",
                                        "UserID",
                                        "PurchaseDate",
                                        "Amount",
                                        "PaymentMethod",
                                        "PaymentStatus",
                                        "Status",
                                        "Createdon"], [$tID,
                                                       $_SESSION['userId'],
                                                       $tDate,
                                                       formatPrice($tAmt),
                                                       'paypal',
                                                       $tStatus,
                                                       "0",
                                                       $systemTime], TRANSACTION );


    $OrderType = "cartRequest";
    $jm = 0;
    $orderIDS = "";
    $noOfOrders = count( $_SESSION['CartRequest'] );
    foreach ( $_SESSION['CartRequest'] as $key => $value ) {
        if ( $jm == 0 ) {
            $orderIDS = $key;
        }
        $jm++;
        foreach ( $value as $single_key => $single_value ) {
           
		   		$HoursChk  =  $single_value['24HoursChk']; 
                if($single_value['24HoursChk']==""){
					$HoursChk = 0;		
				}
				
				
				if($single_value['TypeBanner']==""){
					$single_value['TypeBanner'] = "default";	
				}
				
				if($single_value['Size']==""){
					$single_value['Size'] = "0";	
				}
				
				$attachementName = $filenameAttachThumb  = array();
				if($single_value['Attachment']!=""){
					  $dynamic_dir = "uploads/work/";
					$Attachment = unserialize($single_value['Attachment']);
					foreach($Attachment as $single){
					
						$filename_arr = explode('.',$single);
						$file_size = filesize(getcwd()."/".$dynamic_dir."/".$single);
						$upload = 	$single;	
						$attachementName[]  = $upload;	
					}  	
				}
				
				if($single_value['AttachmentThumb']!=""){
					  $dynamic_dir = "uploads/work/";
					$AttachmentThumb = unserialize($single_value['AttachmentThumb']);
					foreach($AttachmentThumb as $single){
					
						$filename_arr = explode('.',$single);
						$file_size = filesize(getcwd().$dynamic_dir.$single);
						
						$uploadThumb = 	$single;	
						$filenameAttachThumb[] =array("large"=>$uploadThumb,"small"=>$uploadThumb);	
					}  	
				}
				
				InsertRcrdsByData( CHANGE_REQ, "`OrderID` = '" . $single_value['orderIID'] . "',`ProductID` = '".$single_key."',`TransactionOrderID` = '" . $single_value['TransactionOrderID'] . "', `UserID`='" . $single_value['UserID'] . "', `DesignerID`='" . $single_value['DesignerID'] . "', `MessageText` ='" . addslashes($single_value['MessageText']) . "', `Attachment`='" . serialize($attachementName) . "',`AttachmentThumb` = '" . serialize($filenameAttachThumb) . "', `AttechmentName` = '" . serialize($attachementName) . "', `AttachmentThumb` = '" . serialize($filenameAttachThumb) . "', `UserReadState` = '" . $single_value['UserReadState'] . "', `CreationDate` = '" . $systemTime . "', `SystemIP` = '" . $systemIp . "', `MediaPrice` = '" . $single_value['REQUEST_CHANGE_PRICE']. "', `status` = 'Paid', `transctionIDRequest` = '" . $paymentRecord . "', `24HoursChk` = '" . $HoursChk. "', `Type` = 'user',`TypeBanner` = '".$single_value['TypeBanner']."',`Size` = '".$single_value['Size']."',`ResponseState` = '3'");
				
				


           
            UpdateRcrdOnCndi( ORDER, "`ResponseState` = '2'", "`TransactionID` = '" . $single_value['TransactionOrderID'] . "' AND `Id` = '" . $single_value['orderIID'] . "' AND `CustomerID` = '" . $single_value['UserID'] . "'" );

            $curl = curl_init();
            // Set some options - we are passing in a useragent too here
            curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                                       CURLOPT_URL            => SITEURL . 'EmailTemplate/workResponse.php?OID=' . $single_value['orderIID'] . "&reset=" . $reset,
                                       CURLOPT_USERAGENT      => 'Curl Test'] );
            // Send the request & save response to $resp
            $resp = curl_exec( $curl );
            // Close request to clear up some resources
            curl_close( $curl );
		  }


    }


   unset( $_SESSION['CART'] );
   unset( $_SESSION['DISCOUNT_DATA'] );
   unset( $_SESSION['CartRequest'] );
}

if ( $_GET['stripepayment'] == 'success' && isset( $_GET['orderids'] ) ) {
    $_SESSION['thankorder'] = explode( ",", $_GET['orderids'] );
}

?>
<!DOCTYPE html>

<html lang="en">


<head>

    <title>Thank you | Flashy Flyers</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php require_once 'files/headSection.php'; ?>

</head>


<body>


<?php require_once 'files/headerSection.php'; ?>
<?php

 if(($_SESSION['userType']=="sale_rep" || $_SESSION['userType']=="admin") && $_SESSION['RepCreateOrder']=="yes"){ ?>
<div class="page-wrap bubble-bg-1">
    <div class="container">
        <div class="thankyou-msg bx-shadow">
            <h2 class="udr-sub-heading">Order has been successfully <?php echo (!empty($_SESSION['RepKeyModify']))? "updated":"created"; ?>.</h1>
               <a href="<?= ADMINURL . 'orders.php' ?>"  class="btn-grad btn-lg" style="margin-bottom:15px;/*! text-align: center; */margin: 0 auto;display: block;">View
                         orders</a></h2>
           
        </div>
    </div>
</div>
<?php 
//	unset($_SESSION['RepCreateOrder']);
//	unset($_SESSION['CART']);
	//unset($_SESSION['DISCOUNT_DATA']);
	//unset($_SESSION['RepKeyModify']);

}else{ 
if(isset($_SESSION['repOrderCart']) && !empty($_SESSION['repOrderCart'])){
	UpdateRcrdOnCndi(REPRESENTATIVE_ORDERS, "`OrderStatus` = 'success'","`RepKey` = '".$_SESSION['repOrderCart']."'");	
	//unset($_SESSION['repOrderCart']);
}


if(isset($_SESSION['CartRequest']) && count($_SESSION['CartRequest'])>0){
    foreach($_SESSION['CartRequest']  as $cartProducts1) {
			$cartProduct = count($cartProducts1);
		
			foreach($cartProducts1 as $cartProductsKey=>$cartProductsValue) {
				$cartAmount = $cartAmount +$cartProductsValue['REQUEST_CHANGE_PRICE'];
			}
	}
}else{
    $newCart = $_SESSION['CART'];
    $cartProduct = count($newCart);
    if($cartProduct>0) {
        foreach($newCart as $cartProducts) {
            $cartAmount = $cartAmount + $cartProducts['totalPrice'];
        }
    }
}
?>

<div class="page-wrap bubble-bg-1">
    <div class="container">
        <div class="thankyou-msg bx-shadow">
             <h2 class="udr-sub-heading">Thanks for your bussiness!</h2>
            <h3 class="udr-sub-date"><?=date('D M d, Y');?> <span><?=date('g:i A');?></h3>
            <?php
			 $prodOtherSizes = getProdSizeArr();
                            $totalAmount = 0;
			 if ( ! empty( $_SESSION['thankorder'] ) && $OrderType != "cartRequest" ) {
                $orderID = $_SESSION['thankorder'][0];
                $getTransID = GetSglDataOnCndi( ORDER, "`Id` = '" . $orderID . "'", "TransactionID" );
                  if(isset($_SESSION['CART']) && !empty($_SESSION['CART'])) {
                                $cart = $_SESSION['CART'];
                                foreach($cart as $key => $product ) {
                                    if($product['type'] == "main") { 
                                    $getProduct = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$key."'");
                                    
                                    ?>
                                    <div class="cart-row pt-4 pb-4" id="cart_id_<?=$getProduct['id'];?>">
                                        
                                        <div class="cart-pr-info">
                                            <h3><?=$getProduct['Title'];?></h3>
                                            <?php
                                             if($product['template_type']=="psd" && $getProduct['psd_file']!=""){
												
												
											if($product['template_type']=="psd" && $product['psd3dtitle']=="Yes"){
												  echo '3D Title<br>';
											 }
												?>
                                                 <a class="downloadPSD btn" data-type='userThankyou'  href="javascript:void(0)" data-name="<?php echo "uploads/".$key."/".$getProduct['psd_file']; ?>" title="Download" class="btn btn-primaray" style="background: #0070c0;
color: #fff;" >Download PSD</a>
                                                <?php 
											 }?>
                                             <?php 
											
											$extraDetails ="";
                                            
											
											 if($product['template_type']!="psd"){
											if(isset($product['dimensional']))
											if(!isset($product['customeProductFields']) && empty($product['customeProductFields'])){
                                                $extraDetails .= $product['dimensional'].', ';
											}
                                            if(isset($product['type_banner']))
                                                $extraDetails .= implode(",",$product['type_banner']).', ';
											 }
										   if(!isset($product['customeProductFields']) && empty($product['customeProductFields'])){
										    if(isset($product['defaultSize'])  && $product['type'] == "main")
                                                $extraDetails .= $prodOtherSizes[$product['defaultSize']]['name'].', ';
                                            if(!empty($product['otherSize'])  && $product['type'] == "main") {
                                                $otherSized = $product['otherSize'];
                                                $sn =1;
                                                foreach($otherSized as $extraSize)
                                                    $extraDetails .= $sn++.'.: '.$prodOtherSizes[$extraSize]['name'].', ';
                                            }
											
                                            if(isset($product['deliveryTime'])  && $product['type'] == "main"){
                                                $extraDetails .= $deliArr[$product['deliveryTime']].', ';
                                            }
                                            echo rtrim($extraDetails,", ");
										   }
											$customeProduct = "";
											
											
											if(isset($product['customeProductFields']) && !empty($product['customeProductFields'])){
												echo "<ul>";
												$checkCustomProduct =1;
												
												if(isset($product['defaultSize'])  && $product['type'] == "main")
                                               		echo "<li>Default Size: ".$prodOtherSizes[$product['defaultSize']]['name'].'</li>';
                                            if(!empty($product['otherSize'])  && $product['type'] == "main") {
                                                $otherSized = $product['otherSize'];
                                                $sn =1;
                                                foreach($otherSized as $extraSize)
                                                    echo "<li>Other Sizes: ".$prodOtherSizes[$extraSize]['name'].'</li>';
                                            }
												foreach($product['customeProductFields'] as $customeProductFieldsKey=>$customeProductFieldsValue){
													
													foreach($customeProductFieldsValue as $filedsPrimaryIndex=>$filedsPrimaryIndexValue){
														
															foreach($filedsPrimaryIndexValue as $filedsIndex=>$filedsIndexValue){
												
														$FiledsLabal = $customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex];
														$Filedsvalue = $filedsIndexValue;
														if($filedsIndex=="turnaround_time"){
															$Filedsvalue = $deliArr[$Filedsvalue];
														}
														if($filedsIndex=="3d_or_2d"){
															echo "<li>".$Filedsvalue."</li>";	
														}else if(($filedsIndex=="checkbox_sided" || $filedsIndex=="add_music" ||    $filedsIndex=="add_video") && $Filedsvalue=="on"){
															 		echo '<li>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].': Yes</li>';	
														
														}else{
														if($Filedsvalue!="" && $filedsIndex!="music_file" && $filedsIndex!="defaultSize" && $filedsIndex!="add_facebook_cover" && $filedsIndex!="otherSize" && $filedsIndex!="attach_any_logos" && $filedsIndex!="attach_your_logo_design" && $filedsIndex!="vector_psd_pdf" && $filedsIndex!="attach_any_pictures"  && $filedsIndex!="attach_any_style_reference"  && $filedsIndex!="files" && $filedsIndex!="attach_logo"){	
													 		echo '<li>'.$customeProductFields[$customeProductFieldsKey][$filedsPrimaryIndex][$filedsIndex]['label'].': '.$Filedsvalue.'</li>';	
														}
														}
													}
													
													}
												}
												echo "</ul>";
												
											}
											  

                                            $currentCartAddon = array_filter($cart, function ($var) use ($key) {
                                                return ($var['ProductBaseID'] == $key && $var['type'] == 'addon');
                                            });

                                            if(!empty($currentCartAddon) && count($currentCartAddon) > 0){
                                                ?>
                                                <div>
                                                    <h3>Addons</h3>
                                                    <ul>
                                                        <?php
                                                        foreach ($currentCartAddon as $addonKey => $addonValue){
                                                            if($addonValue['ProductBaseID'] ==  $key && $addonValue['type'] == 'addon' ){
                                                                $addonKey = explode("_",$addonKey);
                                                                $addonKey = $addonKey[0];
                                                                $getAddonData = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$addonKey."'");
                                                                echo "<li>".$getAddonData['Title'];
                                                                /*if(!empty($addonValue['type_banner'])){
                                                                    echo " - ".implode($addonValue['type_banner'],', ');
                                                                }*/
                                                                if(!empty($addonValue['dimensional'])){
                                                                    echo " - ".$addonValue['dimensional'];
                                                                }
                                                                if(!empty($addonValue['totalPrice']) && $addonValue['totalPrice'] > 0 && $addonKey != FACEBOOK_PRODUCT_ID) {
                                                                    echo " ( $" . formatPrice($addonValue['totalPrice']) . " )";
                                                                }
                                                                if($addonKey == FACEBOOK_PRODUCT_ID){
                                                                    $getFacebookAddonsData = GetSglRcrdOnCndiWthOdr(PRODUCT,"`id` = '".FACEBOOK_PRODUCT_ID."'","`id`", "ASC");
                                                                    $priceFacebookcover = $getFacebookAddonsData['Baseprice'];

                                                                    if(!empty($addonValue['totalPrice']) && $addonValue['totalPrice'] > 0){
                                                                        $priceFacebookcover = $addonValue['totalPrice'];
                                                                    }
                                                                    echo " ( $" . formatPrice($priceFacebookcover) . " )";
                                                                }
                                                                echo "</li>";
                                                                $product['totalPrice'] = $product['totalPrice'] + $addonValue['totalPrice'];
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="cart-price text-right">
                                            $<?php echo formatPrice($product['totalPrice']); $totalAmount += $product['totalPrice'];?>
                                        </div>
                                    </div>
                                    <?php
									}else{
										
										$AddonsKey  = explode("_",$key);
										if($AddonsKey[1]>0){continue;}
											$addonsCartProduct[] = $product;
										?>
                                        
                                        <?php 	
									}
									
                                }
								if(!empty($addonsCartProduct)){
									
										?>
                                        <div class="AddonsCheckOut" style="margin-top:10px;">
                                        
                                                    <h3 style="font-size:18px;">Addons</h3>
                                                <div class="row">
                                                   <div class="col-lg-10">
                                                    <ul>
                                                        <?php
                                                        foreach ($addonsCartProduct as $addonKey => $addonValue){
                                                            if($addonValue['type'] == 'addon' ){
                                                                $addonKey = explode("_",$addonValue['id']);
                                                                $addonKey = $addonKey[0];
                                                                $getAddonData = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$addonKey."'");
                                                                echo "<li>".$getAddonData['Title'];
                                                                /*if(!empty($addonValue['type_banner'])){
                                                                    echo " - ".implode($addonValue['type_banner'],', ');
                                                                }*/
                                                                if(!empty($addonValue['dimensional'])){
                                                                    echo " - ".$addonValue['dimensional'];
                                                                }
                                                                if(!empty($addonValue['totalPrice']) && $addonValue['totalPrice'] > 0 && $addonKey != FACEBOOK_PRODUCT_ID) {
                                                                    echo " ( $" . formatPrice($addonValue['totalPrice']) . " )";
                                                                }
                                                                
                                                                echo "</li>";
                                                                $product['totalPrice'] = $product['totalPrice'] + $addonValue['totalPrice'];
																$totalAmount +=$addonValue['totalPrice'];
																$totalAddonsPrice +=$addonValue['totalPrice'];
                                                            }
                                                        }
                                                        ?>
                                                    </ul>
                                                    </div>
                                                    <div class="col-lg-2">
                                                    <div class="TotalAddonsPrice"><?php  echo " $" . formatPrice($totalAddonsPrice); ?></div>
                                                    </div>
                                                    </div>
                                                </div>
                                        <?php 	
									}
                            } else if(isset($_SESSION['CartRequest']) && !empty($_SESSION['CartRequest'])) {
                                $cart1 = $_SESSION['CartRequest'];
                                foreach($cart1 as $key2=>$cart){
                                    foreach($cart as $key => $product ) {
                                        $getProduct = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$key."'");
                                        $getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$key."' AND `filetype` = 'image'","id","ASC");
                                        ?>
                                        <div class="cart-row pt-4 pb-4" id="cart_id_<?= $key2."_".$getProduct['id'];?>">
                                            <div class="product-img">
                                               <?php 
											 echo productImageSrc($getBanners['filename'],$getProduct['id'],'354');
											 ?>
                                             
                                            </div>
                                            <div class="cart-pr-info">
                                                <h3>Change request for  <?=$getProduct['Title'];?>  <?php echo ($product['TypeBanner']!="") ? " - ".ucfirst($product['TypeBanner']):""; echo ($product['SizeLabel']!="") ? " - ".ucfirst($product['SizeLabel']):""; ?>  for order #<?php echo $key2; ?></h3>

                                            </div>
                                            <div class="cart-price text-right">
                                                $<?php echo formatPrice($product['REQUEST_CHANGE_PRICE']); $totalAmount += $product['REQUEST_CHANGE_PRICE'];?>
                                            </div>
                                        </div>
                                        <?php
                                    }}
                            } 
				?>
                
                                    <div class="cart-row pt-2 pb-2 sub_total">
                                            <div class="cart-pr-info">Sub Total: </div>
                                            <div class="cart-price text-right">
                                            $<?=formatPrice($totalAmount);?>                   </div>
                                            
                            </div>
                            
                            
                            
                            
                             <?php
                                $newCartAmount = $cartAmount;
                                if(isset($_SESSION['DISCOUNT_DATA'])) {
                                    $newCartAmount=$cartAmount-$_SESSION['DISCOUNT_DATA']['discountapplied'];
                                ?>
                                
                                <div class="cart-row pt-2 pb-2 discount_block">
                                            <div class="cart-pr-info">Discount <?php echo ($_SESSION['DISCOUNT_DATA']['discountper']>0) ? " (".$_SESSION['DISCOUNT_DATA']['discountper']."%) ":"" ?>: </div>
                                            <div class="cart-price text-right">
                                            <?="$".formatPrice($_SESSION['DISCOUNT_DATA']['discountapplied']);?>              </div>
                                            
                            </div>
                            <?php } ?>
                            <div class="cart-row pt-2 pb-2 total_block">
                                            <div class="cart-pr-info">Total: </div>
                                            <div class="cart-price text-right">
                                            <?="$".formatPrice($newCartAmount);?>              </div>
                                            
                            </div>
                                
                                
                                
					<div class="thankyouMsg">Notice something wrong? <a href="contact">Contact our support team</a> and we'll be happy to help.</div>
                
                <div class="thankyouButton">
                <a href="<?= SITEURL . 'my-orders.php?orderUID=' . $getTransID ?>" class="btn-grad btn-lg"
                   style="margin-bottom:15px;">View my order #<?= $getTransID ?></a>
                   </div>
                   
                   
					<div class="thankyouMsg">Thanks for being a great customer.</div>
            <?php
			    //unset( $_SESSION['thankorder'] );
			 }
            if ( $OrderType == "cartRequest" ) {
            
			
                                $cart1 = $_SESSION['CartRequest'];
                                foreach($cart1 as $key2=>$cart){
                                    foreach($cart as $key => $product ) {
                                        $getProduct = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$key."'");
                                       
                                        ?>
                                        <div class="cart-row pt-4 pb-4" id="cart_id_<?= $key2."_".$getProduct['id'];?>">
                                            
                                            <div class="cart-pr-info">
                                                <h3>Change request for  <?=$getProduct['Title'];?>  <?php echo ($product['TypeBanner']!="") ? " - ".ucfirst($product['TypeBanner']):""; echo ($product['SizeLabel']!="") ? " - ".ucfirst($product['SizeLabel']):""; ?>  for order #<?php echo $key2; ?></h3>

                                            </div>
                                            <div class="cart-price text-right">
                                                $<?php echo formatPrice($product['REQUEST_CHANGE_PRICE']); $totalAmount += $product['REQUEST_CHANGE_PRICE'];?>
                                            </div>
                                        </div>
                                        <?php
                                    }}
                            
			  ?>
                                                  <div class="cart-row pt-2 pb-2 sub_total">
                                            <div class="cart-pr-info">Sub Total: </div>
                                            <div class="cart-price text-right">
                                            $<?=formatPrice($totalAmount);?>                   </div>
                                            
                            </div>
                            
                            
                            
                            
                             <?php
                                $newCartAmount = $cartAmount;
                                if(isset($_SESSION['DISCOUNT_DATA'])) {
                                    $newCartAmount=$cartAmount-$_SESSION['DISCOUNT_DATA']['discountapplied'];
                                ?>
                                
                                <div class="cart-row pt-2 pb-2 discount_block">
                                            <div class="cart-pr-info">Discount <?php echo ($_SESSION['DISCOUNT_DATA']['discountper']>0) ? " (".$_SESSION['DISCOUNT_DATA']['discountper']."%) ":"" ?>: </div>
                                            <div class="cart-price text-right">
                                            <?="$".formatPrice($_SESSION['DISCOUNT_DATA']['discountapplied']);?>              </div>
                                            
                            </div>
                            <?php } ?>
              <div class="cart-row pt-2 pb-2 total_block">
                                            <div class="cart-pr-info">Total: </div>
                                            <div class="cart-price text-right">
                                            <?="$".formatPrice($newCartAmount);?>              </div>
                                            
                            </div>
              <div class="thankyouMsg">Notice something wrong? <a href="contact">Contact our support team</a> and we'll be happy to help.</div>
                <div class="thankyouButton">
              <?php 
			    if ( $noOfOrders > 1 ) {

                    ?>
                    <a href="<?= SITEURL . 'my-orders.php' ?>" class="btn-grad btn-lg" style="margin-bottom:15px;">View
                        my orders</a>
                    <?php
                } else {

                    ?>
                    <a href="<?= SITEURL . 'my-orders.php?orderUID=' . $orderIDS ?>" class="btn-grad btn-lg"
                       style="margin-bottom:15px;">View my order #<?= $orderIDS ?></a>
                    <?php
                }
				?>
                					<div class="thankyouMsg">Thanks for being a great customer.</div>

                <?php 
            }
            ?>
        </div>
    </div>
</div>
<?php } ?>


<?php require_once 'files/footerSection.php' ?>


<!----SCRIPTS---------->

<script src="js/jquery.js"></script>

<script src="js/bootstrap.min.js"></script>

<script src="js/popper.min.js"></script>
<?php
unset( $_SESSION['CART'] );
unset( $_SESSION['DISCOUNT_DATA'] );
unset( $_SESSION['CartRequest'] );
unset( $_SESSION['guest_checkout'] );
unset( $_SESSION['thankorder'] );
unset( $_SESSION['paymentRecord'] );
unset($_SESSION['RepCreateOrder']);
unset($_SESSION['RepKeyModify']);
unset($_SESSION['ERROR']);
unset($_SESSION['SUCCESS']);
		
	
?>
<script>

	jQuery(document).on("click",".downloadPSD",function(){
		var name =  jQuery(this).attr("data-name");
		var type =  jQuery(this).attr("data-type");
		var id =  jQuery(this).attr("data-id");
		
		window.location = "download.php?name="+name+"&type="+type+"&id=1";
	});
	
</script>
<style>
    .cart-desktop { display: none !important;}
</style>
</body>


</html>