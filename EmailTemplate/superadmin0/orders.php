<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	$CurrentTime =   time(); //1 week ago
	$PageTitle = "Orders";
	if(isset($_REQUEST['orderUID']) && !empty($_REQUEST['orderUID'])) {
		$orderUID = intval($_REQUEST['orderUID']);
		$_SESSION['ORDERUID'] = $orderUID;
		echo "<script> window.location.href = '".ADMINURL."order-details.php?order_id=".$orderUID."';</script>";
		exit();
	}
	if(isset($_POST['assignFBtn']) && !empty($_POST['assignFBtn'])) {
		extract($_POST);
		UpdateRcrdOnCndi(ORDER, "`AssignedTo` = '$assignedto', `AssignedOn` = '$systemTime', `OrderStatus` = '1'", "`TransactionID` = '$projectId'");
		$_SESSION['SUCCESS'] = "Project assigned to designer successfully.";
		
		
		/* Designer assigned mail */
		 $curl = curl_init();
        curl_setopt_array( $curl, [CURLOPT_RETURNTRANSFER => 1,
                                   CURLOPT_URL            => SITEURL . 'EmailTemplate/auto_assigned_order.php?designer_id=' . $assignedto . "&orderids=" . $projectOrderId,
                                   CURLOPT_USERAGENT      => 'Curl Test'] );
        $resp = curl_exec( $curl );
        curl_close( $curl );
		echo "<script> window.location.href = '".ADMINURL."orders.php';</script>";
		exit();
	}
	if(isset($_GET['deleteorder']) && !empty($_GET['deleteorder'])) {
		$orderid 		= $_GET['deleteorder'];
		$transactionid 	= $_GET['transactionid'];		
		DltSglRcrd(ORDER, "`TransactionID` = '$transactionid'");		
		DltSglRcrd(TRANSACTION, "`id` = '$transactionid'");		
		$_SESSION['SUCCESS'] = "Order Deleted Successfully";
		echo "<script> window.location.href = '".ADMINURL."orders.php';</script>";
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
		echo "<script> window.location.href = '".ADMINURL."orders.php';</script>";
		exit();
	}
	
	
	
	 if(!empty($_REQUEST['order_id']) && isset($_REQUEST['is_approve'])) {
        $order_id = intval($_REQUEST['order_id']);
        $is_approve = intval($_REQUEST['is_approve']);
        UpdateRcrdOnCndi(ORDER, "`is_approve` = '$is_approve',`finishDate`='".$CurrentTime."'", "`TransactionID` = '$order_id'");
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
        echo "<script> window.location.href = '".ADMINURL."orders.php';</script>";
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <?php if(isset($_SESSION['ERROR']) && !empty($_SESSION['ERROR'])) { ?>
    <div class="notification error">
        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
        <span>Error: <?=$_SESSION['ERROR'];?></span><button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['ERROR']); } if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { ?>	
    <div class="notification success">
        <div class="d-flex"><i class="fas fa-check"></i></div>
        <span><?=$_SESSION['SUCCESS'];?></span>
        <button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['SUCCESS']); } ?>
    
    <?php
	
	$Last7Days =   strtotime("-1 week"); //1 week ago
		// FETCH ORDER STATS
		$currentYearTimestamp = strtotime('first day of January '.date('Y'));
		if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
			$sfpid1 = intval($_REQUEST['productId']);
			$cndy1 = "ProductID = '".$sfpid1."'";
			$cndy2 = "ProductID = '".$sfpid1."'";
			$cndy3 = "ProductID = '".$sfpid1."' and OrderDate<=$CurrentTime AND OrderDate<=$Last7Days"; 
			$cndy4 = "OrderDate>=$currentYearTimestamp AND ProductID = '".$sfpid1."'";
			$cndy5 = "finishDate!='' AND finishDate<=$CurrentTime AND finishDate>=$Last7Days AND ProductID = '".$sfpid1."'";
		} else {
			$cndy1 = "Id!=''";
			$cndy2 = "Id!=''";
			$cndy3 = "OrderDate<=$CurrentTime AND OrderDate<=$Last7Days"; 
			$cndy4 = "OrderDate>=$currentYearTimestamp";
			$cndy5 = "finishDate!='' AND finishDate<=$CurrentTime AND finishDate>=$Last7Days";
		}
	//	$OrderStatsTotal = GetMltRcrdsOnCndi(ORDER, $cndy1);
		
		//$newOrderStats = GetNumOfRcrdsOnCndi(ORDER, $cndy1);
		//$inProgressStats = GetNumOfRcrdsOnCndi(ORDER, $cndy2); 
		
		
			if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
				$sfpid = intval($_REQUEST['productId']);
				$completedFinishOrderDataQuery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".ORDER." WHERE ProductID = '".$sfpid."' and (is_approve = '1' OR (ResponseState IN('1') AND OrderStatus IN('1')))";
				}else{
				$completedFinishOrderDataQuery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".ORDER." WHERE (is_approve = '1') OR (ResponseState IN('1') AND OrderStatus IN('1'))";
			}
	
	
	$completedFinishOrderData = mysql_fetch_array(mysql_query($completedFinishOrderDataQuery));
		
		$newOrderStats = $completedOrderStats = $inProgressStats = $yearTotalOrderStats =  $completedOrderWeekStats = 0;
	
		foreach($OrderStatsTotal as $showData){/*
		
		
		$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$showData['TransactionID']."'", "ResponseState", "ASC");
		$IndexCount=1;
								$getRespnseCode= array();
								foreach($OrderData as $k=>$v){
										$getRespnseCode[$v['ResponseState']] = $v['ResponseState'];	
										if($v['parent_product_id']>0){
											$productNameData .= 	$IndexCount.") ".$prodsArr[$v['parent_product_id']]['Title']." ".$prodsArr[$v['ProductID']]['Title']."<br>";
										}else{
											$productNameData .= 	$IndexCount.") ".$prodsArr[$v['ProductID']]['Title']."<br>";
										}
								$IndexCount++;
								}
								$AssignTo = $showData['AssignedTo'];
							$OrderStatus =$showData['OrderStatus'];
							 if($showData['is_approve'] == 1){
                              $completedOrderStats++;
                            }else{
							
							if($AssignTo==0 && $OrderStatus==4 ) {
								$inProgressStats++;
							}else if($AssignTo==0 &&  $OrderStatus==0) {
								$newOrderStats++;
							
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(3,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(2,$getRespnseCode)) {
								$inProgressStats++;
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(1,$getRespnseCode)  && !in_array(2,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
								$inProgressStats++;
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(2,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
								$inProgressStats++;
							}else if($AssignTo>0 && $OrderStatus==1 ) {
								$inProgressStats++;
							}
							}
		
		
						
		*/}
		/* Added to show completed order in current week`*/		
		$OrderStatsweekly = GetMltRcrdsOnCndi(ORDER, $cndy5);
		$completedOrderWeekStats	=	count($OrderStatsweekly);
	?>
    
	<?php
	//Pagination Code
	$prodType = getProdTypeParentArr();
	$getProdSizes = getProdSizeArr();
	
	$usersArr = getUserArr();
	$prodsArr = getALLProductArr();	
	$searchTable = ORDER;
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
	
	
	if(isset($_REQUEST['order_number']) && !empty($_REQUEST['order_number'])) {
		$searchQuery .= "TransactionID = '".$_REQUEST['order_number']."' AND ";
		$searchURL .= "&order_number=".$_REQUEST['order_number'];
	}
	
	if(isset($_REQUEST['status']) && !empty($_REQUEST['status']) && $_REQUEST['status'] != "all") {
		$searchQuery .= "OrderStatus = '".$_REQUEST['status']."' AND ";		
		$searchURL .= "&status=".$_REQUEST['status'];
	} else {
		$searchURL .= "&status=all";
	}
	
	if(isset($_REQUEST['assignedto']) && !empty($_REQUEST['assignedto']) && $_REQUEST['assignedto'] != "all") {
		$searchQuery .= "AssignedTo = '".$_REQUEST['assignedto']."' AND ";
		$searchURL .= "&assignedto=".$_REQUEST['assignedto'];
	}	
	
	if(isset($_REQUEST['rep_order']) && $_REQUEST['rep_order'] != "") {
		$searchQuery .= "rep_order = 'yes' AND ";		
		$searchURL .= "&rep_order=".$_REQUEST['rep_order'];
	}
	
	
	
	if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
		$sfpid = intval($_REQUEST['productId']);
		$searchQuery .= "`ProductID` = '".$sfpid."' AND ";
		$searchURL .= "&productId=".$sfpid;
	}
	
	
	
	// PRODUCT TABLE SEARCH 
	$productQuery = "";
	$searchprod = array();
	
	if(isset($_REQUEST['product_name']) && !empty($_REQUEST['product_name'])) {
		$productQuery .= "`Title` LIKE '%".$_REQUEST['product_name']."%' AND ";
		$searchURL .= "&product_name=".$_REQUEST['product_name'];
	}
	
	if(isset($_REQUEST['product_type']) && !empty($_REQUEST['product_type']) && $_REQUEST['product_type'] != "all") {
		$productQuery .= "`parent_product_cat_id` = '".$_REQUEST['product_type']."'";
		$searchURL .= "&product_type=".$_REQUEST['product_type'];
	}
	
	if(!empty($productQuery)) {
		$productQuery = rtrim($productQuery, " AND ");
		$getProducts = GetMltRcrdsOnCndi(PRODUCT, $productQuery);
		foreach($getProducts as $searchProduct) {
			$searchprod[] = $searchProduct['id'];	
		}
		
		$idsProd = implode("," , $searchprod);
		$searchQuery .= "ProductID IN ($idsProd) AND ";
	}
	
	// USER TABLE QUERY
	$userQuery = "";
	$userSearchArr = array();
	if(isset($_REQUEST['customer_email']) && !empty($_REQUEST['customer_email'])) {
		$userQuery .= "Email LIKE '%".$_REQUEST['customer_email']."%' AND ";
		$searchURL .= "&customer_email=".$_REQUEST['customer_email'];
	}
	if(!empty($_REQUEST['customer_name']) && !empty($_REQUEST['customer_name'])) {
		$userQuery .= "(FName LIKE '%".$_REQUEST['customer_name']."%' OR LName LIKE '%".$_REQUEST['customer_name']."%') AND ";
		$searchURL .= "&customer_name=".$_REQUEST['customer_name'];
	}
	
	if(!empty($userQuery)) {
		$userQuery = rtrim($userQuery, " AND");
		$getUsers = GetMltRcrdsOnCndi(USERS, $userQuery);
		foreach($getUsers as $searchProduct) {
			$userSearchArr[] = $searchProduct['UserID'];	
		}
		
		$idsProd = implode("," , $userSearchArr);
		$searchQuery .= "CustomerID IN ($idsProd) AND ";
	}
	
	// START AND END DATE QUERY
	if(isset($_REQUEST['order_date']) && !empty($_REQUEST['order_date'])) {
		$currentTime = strtotime($_REQUEST['order_date']);
		$afterTime = strtotime('+1 day', $currentTime);
		$searchQuery .= "(OrderDate >= '".$currentTime."' AND OrderDate <= '".$afterTime."') AND ";
		$searchURL .= "&order_date=".$_REQUEST['order_date'];
	}
    $isNewOrder = 0;
    if( !empty($_REQUEST['is_new_order']) && $_REQUEST['is_new_order'] == 1) {
        $isNewOrder = 1;
   		//   $startDate = date("Y-m-d")." 00:00:00";
		//		$EndDate = date("Y-m-d")." 23:58:00";
              
			//	$searchQuery .= " (AssignedTo = '0' OR CAST(OrderDate AS SIGNED) >  '".strtotime($startDate)."' and CAST(OrderDate AS SIGNED) <'".strtotime($EndDate)."') AND is_approve!= '1'";
			
		$searchQuery .= " AssignedTo = '0' AND";
        $searchURL .= "&is_new_order=" . $isNewOrder;
    }
	
 if( !empty($_REQUEST['order_status']) && $_REQUEST['order_status'] == 3) {
	  $orderStatus = 3;
	  $searchQuery .= " is_approve = '1' ";
   }else{
   
   if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
	// $orderStatus = 1;
		//$searchQuery .= " (is_approve != '1' OR is_approve = '1')   AND";
	}else{
	 //$orderStatus = 1;
	//  $searchQuery .= " is_approve != '1' ";
	}
	 

   }
	$searchURL .= "&order_status=" . $orderStatus;
			
	
	 if( !empty($_REQUEST['order_status1']) && $_REQUEST['order_status1'] > 0) {
        $orderStatus = $_REQUEST['order_status'];

        if($orderStatus > 0) {

            /* New */
            if ( $orderStatus == 1 ) {
				//$startDate = date("Y-m-d")." 00:00:00";
				//$EndDate = date("Y-m-d")." 23:58:00";
              
			//	$searchQuery .= " (AssignedTo = '0' OR CAST(OrderDate AS SIGNED) >  '".strtotime($startDate)."' and CAST(OrderDate AS SIGNED) <'".strtotime($EndDate)."') AND is_approve!= '1'";
			
			$searchQuery .= " AssignedTo = '0' AND is_approve!= '1'";
            }

            /* In progress */
            if ( $orderStatus == 2 ) {
                $searchQuery .= "AssignedTo > '0' AND OrderStatus = '1' AND is_approve != '1' AND ResponseState = '0'";
            }

            /* Finished */
            if ( $orderStatus == 3 ) {
                $searchQuery .= " is_approve = '1' and ";
            }

            $searchURL .= "&order_status=" . $orderStatus;
        }
    }
	
	
	if(isset($_REQUEST['mode_type']) && !empty($_REQUEST['mode_type']) && $_REQUEST['mode_type']=="top_filter") {
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="year") {
			$searchQuery .= " finishDate>='".$currentYearTimestamp."' AND is_approve='1' AND  ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="week") {
			$searchQuery .= " finishDate<='".$CurrentTime."' AND finishDate>='".$Last7Days."' AND  is_approve='1' AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="proccess") {
			$searchQuery .= " is_approve !='1'   AND ResponseState IN('0','3','4') and OrderStatus IN('1','2','3')  AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="new_order") {
			$searchQuery .= " AssignedTo = '0' AND is_approve!= '1' AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
		
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="revisions") {
			$searchQuery .= " AssignedTo > 0 AND   OrderStatus='1' AND ResponseState='2' AND is_approve!= '1' AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
		
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="pending_payment") {
			$searchQuery .= " AssignedTo = '0' AND   OrderStatus='4' AND is_approve!= '1' AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
		
		
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="finish") {
			
			$searchQuery .= " (is_approve= '1' OR ResponseState IN('1') AND OrderStatus IN('1'))  AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
	
	}else{
		if(!isset($_REQUEST['productId'])){
			$searchQuery .= " is_approve!= '1'   AND ResponseState IN('0','3','4') and OrderStatus IN('1','2','3')   AND ";
			$searchURL .= "&order_status=" . $orderStatus;
		}
	}
	$searchQuery = rtrim($searchQuery, " AND ");
	
	
	if(!empty($searchQuery))
		 $query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable;
	//echo $query;
	
	$total_pages = mysql_fetch_array(mysql_query($query));
    $total_pages = (!empty($total_pages['num'])) ?  $total_pages['num'] : '0';

	?> 
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content pl-60 pr-60 bx-shadow">
                <section class="orders-top mb-4">
                    <div class="row">
                        <div class="col-lg-2 col-sm-6 mb-4 cursor_pointer  cursor_pointer <?php echo($_REQUEST['mode']=="new_order") ?"active_box":"" ?>"  onClick="window.location='orders.php?mode_type=top_filter&mode=new_order<?php echo (isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) ?"&productId=".$_REQUEST['productId']:""; ?>'">
                            <div class="totals-box red">
                                <h1><?php 
								
								
								if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
									$sfpid = intval($_REQUEST['productId']);
									$NewOrdersearchQuery .= " ProductID` = '".$sfpid."' AND AssignedTo = '0' AND is_approve!= '1'";
								}else{
									$NewOrdersearchQuery .= " AssignedTo = '0' AND is_approve!= '1'";
								}
	
							  $Neworderquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." WHERE ".$NewOrdersearchQuery;
							$Newordertotal_pages = mysql_fetch_array(mysql_query($Neworderquery));
							$newOrder = (!empty($Newordertotal_pages['num'])) ?  $Newordertotal_pages['num'] : '0';
	
								
								echo $newOrder;?></h1>
                                <p>new</p>

                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 mb-4 cursor_pointer <?php echo(!isset($_REQUEST['mode']) || $_REQUEST['mode']=="proccess") ?"active_box":"" ?>"  onClick="window.location='orders.php?mode_type=top_filter&mode=proccess<?php echo (isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) ?"&productId=".$_REQUEST['productId']:""; ?>'">
                            <div class="totals-box cyan">
                                <h1><?php 
								
								if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
									$sfpid = intval($_REQUEST['productId']);
										$inProgressStatsquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where ProductID = '".$sfpid."' AND  is_approve != '1' AND ResponseState IN('0','3','4') and OrderStatus IN('1','2','3')";
								}else{
									$inProgressStatsquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where  is_approve != '1' AND ResponseState !='2' AND ResponseState IN('0','3','4') and OrderStatus IN('1','2','3') ";
								}
		
								$inProgressStats = mysql_fetch_array(mysql_query($inProgressStatsquery));
									
								echo $inProgressStats['num'];?></h1>
                                <p>in progress</p>

                            </div>
                        </div>
                        <?php /*?><div class="col-lg-2 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="week") ?"active_box":"" ?>"  onClick="window.location='orders.php?mode_type=top_filter&mode=week<?php echo (isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) ?"&productId=".$_REQUEST['productId']:""; ?>'">
                            <div class="totals-box purple">
                                <h1><?php 
							 	
								if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
									$sfpid = intval($_REQUEST['productId']);
										
										$ComplatedStatsquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where ProductID = '".$sfpid."' and  is_approve = '1' and finishDate <='".$CurrentTime."' AND finishDate>='".$Last7Days."'";
		
								}else{
									$ComplatedStatsquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where  is_approve = '1' and finishDate <='".$CurrentTime."' AND finishDate>='".$Last7Days."'";
		
								}
								
								$ComplatedStats = mysql_fetch_array(mysql_query($ComplatedStatsquery));
								echo $ComplatedStats['num'];
	
								
								?></h1>
                                <p>completed this week</p>

                            </div>
                        </div><?php */?>
                        
                        <div class="col-lg-3 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="pending_payment") ?"active_box":"" ?>"  onClick="window.location='orders.php?mode_type=top_filter&mode=pending_payment<?php echo (isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) ?"&productId=".$_REQUEST['productId']:""; ?>'">
                            <div class="totals-box purple">
                                <h1><?php  
								
								
								
								if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
									$sfpid = intval($_REQUEST['productId']);
										
										$Revisionquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where ProductID = '".$sfpid."' and  AssignedTo = 0  and OrderStatus='4' AND is_approve!= '1'";
		
								}else{
									$Revisionquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where  AssignedTo = 0  and OrderStatus='4'  AND is_approve!= '1'";
		
								}
								
								
								$RevisionqueryData = mysql_fetch_array(mysql_query($Revisionquery));
								echo $RevisionqueryData['num'];
								
							?></h1>
                                <p>pending payment</p>

                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="revisions") ?"active_box":"" ?>"  onClick="window.location='orders.php?mode_type=top_filter&mode=revisions<?php echo (isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) ?"&productId=".$_REQUEST['productId']:""; ?>'">
                            <div class="totals-box blue">
                                <h1><?php  
								
								
								
								if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
									$sfpid = intval($_REQUEST['productId']);
										
										$Revisionquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where ProductID = '".$sfpid."' and  AssignedTo > 0  and OrderStatus='1' and ResponseState='2' AND is_approve!= '1'";
		
								}else{
									$Revisionquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where  AssignedTo > 0  and OrderStatus='1' and ResponseState='2' AND is_approve!= '1'";
		
								}
								
								
								$RevisionqueryData = mysql_fetch_array(mysql_query($Revisionquery));
								echo $RevisionqueryData['num'];
								
							?></h1>
                                <p>under revision</p>

                            </div>
                        </div>
                        <?php /*?><div class="col-lg-2 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="year") ?"active_box":"" ?>"  onClick="window.location='orders.php?mode_type=top_filter&mode=year<?php echo (isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) ?"&productId=".$_REQUEST['productId']:""; ?>'">
                            <div class="totals-box blue">
                                <h1><?php 
								if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
									$sfpid = intval($_REQUEST['productId']);
									 $yearTotalOrderStatsQuery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where  ProductID = '".$sfpid."' and is_approve = '1' and finishDate>='".$currentYearTimestamp."'";
					
		
								}else{
									$yearTotalOrderStatsQuery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where  is_approve = '1' and finishDate>='".$currentYearTimestamp."'";
				
							}
				
									
									
										$yearTotalOrderStats = mysql_fetch_array(mysql_query($yearTotalOrderStatsQuery));
								
								echo $yearTotalOrderStats['num'];?></h1>
                                <p>this year</p>

                            </div>
                        </div><?php */?>
                        
                        <div class="col-lg-2 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="finish") ?"active_box":"" ?>"  onClick="window.location='orders.php?mode_type=top_filter&mode=finish<?php echo (isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) ?"&productId=".$_REQUEST['productId']:""; ?>'">
                            <div class="totals-box" style="color:green;">
                                <h1><?php  
							echo $completedFinishOrderData['num'];?></h1>
                                <p style="color:green;">completed</p>

                            </div>
                        </div>
                        
                        
                        
                    </div>
                </section>
                <h1 class="page-heading mb-4">Orders (<?=$total_pages;?>)</h1>
                <?php if(!isset($_REQUEST['productId']) && empty($_REQUEST['productId'])) { ?>
                <form action="" class="top-search-options mb-5">
                    <input type="hidden" name="mode_type" value="<?php echo $_REQUEST['mode_type'] ?>">
                      	<input type="hidden" name="mode" value="<?php echo $_REQUEST['mode'] ?>">
                    <div class="d-flex justify-content-stretch flex-wrap">
                        <div class="w-lg-20 w-100">
                            <?php 
                                $currentUrl = currentUrl(true);
                                if(!empty($isNewOrder) && $isNewOrder == 1){
                                 //   $currentUrl .='?is_new_order='.$isNewOrder;
                                }
                            ?>
                            <a href="<?php echo $currentUrl; ?>" class="btn-lg btn-block form-btn-grad">View all</a>
                        </div>
                        <div class="w-lg-20 w-100">
                            <select name="product_type" id="" class="form-control">
                                <option value="all">product type</option>
                                <?php foreach($prodType as $prodTypes) { ?>
                                <option value="<?=$prodTypes['id'];?>" <?php if(isset($_REQUEST['product_type']) && $_REQUEST['product_type']==$prodTypes['id']){ echo "selected"; } ?>><?=$prodTypes['name'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="w-lg-20 w-100">
                        
                            <input type="text" name="customer_name" class="form-control" placeholder="customer names" value="<?php if(isset($_REQUEST['customer_name'])){ echo $_REQUEST['customer_name']; } ?>">
                            <input type="hidden" name="is_new_order" value="<?php echo $isNewOrder; ?>">
                        </div>
                        <div class="w-lg-20 w-100">
                            <input type="email" name="customer_email" class="form-control" placeholder="customer email" value="<?php if(isset($_REQUEST['customer_email'])){ echo $_REQUEST['customer_email']; } ?>">
                        </div>
                        <div class="w-lg-20 w-100">
                            <input type="text" name="order_number" class="form-control" placeholder="order #" value="<?php if(isset($_REQUEST['order_number'])){ echo $_REQUEST['order_number']; } ?>">
                        </div>
                        
                        <div class="w-lg-20 w-100">
                            <select name="assignedto" class="form-control">
                                <option value="all">assigned to</option>
                                <?php foreach($usersArr as $designers) {
									if($designers['UserType'] != "designer") { continue; }
									echo '<option value="'.$designers['UserID'].'" '.((isset($_REQUEST["assignedto"]) && $_REQUEST["assignedto"]==$designers["UserID"])? "selected":"").'>'.$designers['FName'].' '.$designers['LName'].'</option>';
								} ?>
                            </select>
                        </div>

                        <div class="w-lg-20 w-100">
                            <input type="text" name="order_date" class="form-control date-field" readonly data-toggle="datepicker" placeholder="Order date" value="<?php if(isset($_REQUEST['order_date'])){ echo $_REQUEST['order_date']; } ?>">
                        </div>
                       <div class="w-lg-30 w-100" style="margin-top: 10px;">
                          <label class="custom-control custom-checkbox">
                                <input type="checkbox" name="rep_order" <?php if(isset($_REQUEST['rep_order']) && $_REQUEST['rep_order']=='1'){ echo "checked"; } ?> class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Orders placed by Reps</span>
                            </label>
                            </div>
                       <input type="hidden" name="order_status" value="<?php echo $_REQUEST['order_status'];  ?>">
                        <?php /*?><div class="w-lg-20 w-100">
                            <select name="status" id="" class="form-control">
                                <option value="all">status</option>
                                <option value="0" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']==0){ echo "selected"; } ?>>Not Assigned</option>
                                <option value="1" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']==1){ echo "selected"; } ?>>In Progress</option>
                                <option value="2" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']==2){ echo "selected"; } ?>>Complete</option>
                            </select>
                        </div><?php */?>
                        
                         <?php /*?><div class="w-lg-20 w-100">
                            <select name="order_status" class="form-control">
                                <option value="">Status</option>
                                <option value="1" <?php echo (isset($_REQUEST["order_status"]) && $_REQUEST["order_status"]==1)? "selected":""; ?> >New</option>
                                
                                <option value="2" <?php echo (isset($_REQUEST["order_status"]) && $_REQUEST["order_status"]==2)? "selected":""; ?> >In progress</option>
                                <option value="3" <?php echo (isset($_REQUEST["order_status"]) && $_REQUEST["order_status"]==3)? "selected":""; ?>>Finished</option>
                            </select>
                        </div><?php */?>
                        <div class="col-lg-2 ml-auto">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
				<?php }
				
						if(!isset($_REQUEST['key_order'])){
								$key_order  = "ID";	
							}
							if(!isset($_REQUEST['key_order_by'])){
								$key_order_by  = "DESC";	
							}
							if(isset($_REQUEST['key_order'])){
								$key_order  = $_REQUEST['key_order'];	
							}
							if(isset($_REQUEST['key_order_by'])){
								$key_order_by  = $_REQUEST['key_order_by'];	
							}
							$ordeBy = "DESC";
							if($key_order_by=="asc"){
								$ordeBy = "DESC";	
							}
							if($key_order_by=="DESC"){
								$ordeBy = "ASC";	
							}
							unset($_GET['key_order_by']);
							unset($_GET['key_order']);
				 ?>
                <div class="table-responsive">
                    <table class="table sorting orders-table table-1">
                        <thead>
                            <tr>
                                <th scope="col" class="header <?php if($key_order=="ID" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="ID" && $key_order_by=="ASC"){echo "headerSortDown";} ?>" onClick="window.location='orders.php?<?php echo http_build_query($_GET); ?>&key_order=ID&key_order_by=<?php echo $ordeBy; ?>'">Order#</th>
                                <th scope="col" class="header <?php if($key_order=="OrderDate" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="OrderDate" && $key_order_by=="ASC"){echo "headerSortDown";} ?>" onClick="window.location='orders.php?<?php echo http_build_query($_GET); ?>&key_order=OrderDate&key_order_by=<?php echo $ordeBy; ?>'">Order date</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Assigned to</th>
                                <th scope="col">Product</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>                                
                            </tr>
                            <tbody>
                            <?php
							
							
$adjacents = 2;
if($total_pages>0) {
	$targetpage = $searchURL;
	$limit = $searchResultsPerPage; 
	
	$page = $_GET['page'];
	
	if($page) {
		$start = ($page - 1) * $limit;
	} else {
		$start = 0;							
	}
	mysql_query("SET sql_mode = ''");
	if(!empty($searchQuery))
		$sql = "SELECT * FROM ".$searchTable." WHERE ".$searchQuery." GROUP BY `TransactionID` ORDER BY `".$key_order."` ".$key_order_by." LIMIT $start, $limit";
	else
		$sql = "SELECT * FROM ".$searchTable. " GROUP BY `TransactionID` ORDER BY `".$key_order."` ".$key_order_by." LIMIT $start, $limit";
        
	
		
    $result = mysql_query($sql);
                    
	if ($page == 0) $page = 1;
	
	$prev = $page - 1;
	
	$next = $page + 1;
	
	$lastpage = ceil($total_pages/$limit);

	$lpm1 = $lastpage - 1;
	
	$pagination = "";
	
	if(isset($key_order)){
		$targetpage .='&key_order='.$key_order;	
	}
	if(isset($key_order_by)){
		$targetpage .='&key_order_by='.$key_order_by;	
	}
                                    
    if($lastpage > 1)
	{	
		$pagination .= "<li>
                            <a href=\"$targetpage&page=1\" aria-label=\"First\">First</a>
                        </li>";
		//previous button
		if ($page > 1) 
			$pagination.= "<li>
                            <a href=\"$targetpage&page=$prev\" aria-label=\"Previous\">
                                <span aria-hidden=\"true\">&laquo;</span>
                                <span class=\"sr-only\">Previous</span>
                            </a>
                        </li>";
		else
			$pagination.= "<li>
                            <a aria-label=\"Previous\" disabled>
                                <span aria-hidden=\"true\">&laquo;</span>
                                <span class=\"sr-only\">Previous</span>
                            </a>
                        </li>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<li class=\"active\"><a>$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetpage&page=$counter\">$counter</a></li>";					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))
		{
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class=\"active\"><a>$counter</a></li>";
					else
						$pagination.= "<li><a href=\"$targetpage&page=$counter\">$counter</a></li>";					
				}
				$pagination.= "<li><a>...</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=$lpm1\">$lpm1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=$lastpage\">$lastpage</a></li>";		
			}
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<li><a href=\"$targetpage&page=1\">1</a>";
				$pagination.= "<li><a href=\"$targetpage&page=2\">2</a>";
				$pagination.= "<li><a>...</a></li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class=\"active\"><a>$counter</a></li>";
					else
						$pagination.= "<li><a href=\"$targetpage&page=$counter\">$counter</a></li>";					
				}
				$pagination.= "<li><a>...</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=$lpm1\">$lpm1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=$lastpage\">$lastpage</a></li>";		
			}
			else
			{
				$pagination.= "<li><a href=\"$targetpage&page=1\">1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=2\">2</a></li>";
				$pagination.= "<li><a>...</a></li>";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class=\"active\"><a>$counter</a></li>";
					else
						$pagination.= "<li><a href=\"$targetpage&page=$counter\">$counter</a></li>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination.= "<li>
                            <a href=\"$targetpage&page=$next\" aria-label=\"Next\" disabled>
                                <span aria-hidden=\"true\">&raquo;</span>
                                <span class=\"sr-only\">Next</span>
                            </a>
                        </li>";
		else
			$pagination.= "<li>
                            <a aria-label=\"Next\" disabled>
                                <span aria-hidden=\"true\">&raquo;</span>
                                <span class=\"sr-only\">Next</span>
                            </a>
                        </li>";
		$pagination.= "<li>
                            <a href=\"$targetpage&page=$lastpage\" aria-label=\"First\">Last</a>
                        </li>";		
	}
	$sr = 1;
	
	while($showData = mysql_fetch_array($result))
	{ 
	
	$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$showData['TransactionID']."'", "ResponseState", "ASC");
	$TransactionData = GetSglRcrdOnCndi(TRANSACTION, "`id` = '".$showData['TransactionID']."'");
							$productNameData = "";
								$IndexCount=1;
								$getRespnseCode= array();
								foreach($OrderData as $k=>$v){
										$getRespnseCode[$v['ResponseState']] = $v['ResponseState'];	
										if($v['parent_product_id']>0){
											$productNameData .= 	$IndexCount.") ".$prodsArr[$v['parent_product_id']]['Title']." ".$prodsArr[$v['ProductID']]['Title']."<br>";
										}else{
											$productNameData .= 	$IndexCount.") ".$prodsArr[$v['ProductID']]['Title']."<br>";
										}
								$IndexCount++;
								}
	?>                   
                                <tr>
                                    <td class="data-id"><span><?=$showData['TransactionID'];?></span></td>
                                    <td><?=date('M d, Y',$showData['OrderDate'])."<br>".date('g:i A',$showData['OrderDate']);?></td>
                                    <td><?php if(!empty($showData['CustomerID'])) { echo $usersArr[$showData['CustomerID']]['FName']." ".$usersArr[$showData['CustomerID']]['LName']." ".$usersArr[$showData['CustomerID']]['Email']; } else { echo "Not Available"; }?>
                                    </td>
                                    <td><?php if(!empty($showData['AssignedTo'])) { echo $usersArr[$showData['AssignedTo']]['FName']." ".$usersArr[$showData['AssignedTo']]['LName']." <br/>".date('M d, Y',$showData['AssignedOn'])."<br/>".date('g:i A',$showData['AssignedOn']); } else { echo "Not Assigned"; } ?>
                                    </td>
                                    <td style="vertical-align: middle;"><div class="ProductsNameOrder"><?=$productNameData;?></div></td>
                                     <td class="data-progress"><?php 
								$AssignTo = $showData['AssignedTo'];
									$OrderStatus =$showData['OrderStatus'];
							 if($showData['is_approve'] == 1){
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
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(2,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
								echo "Revisions";
							}else if($AssignTo>0 && $OrderStatus==1 ) {
								echo "In progress";
							}
							}
							
                            ?></td>
                              	<td class="data-view1">
								<div style="display:inline-block; float:left;">
                                		<?php 
											?>
                                         
                                           	<?php if($TransactionData['RepUserID']>0 && $TransactionData['PaymentStatus']=="Sales pending payment"){ ?> 
                                             <a class="finish-btn" style="background: #3bc178 ;padding: 5px 18px;color: #fff; display:block;border-radius: 10px;text-decoration: none; margin-bottom:5px;" href="<?php echo SITEURL; ?>cart.php?RepKeyModify=<?=$TransactionData['RepKey'];?>" >Edit</a>
                                             <?php } ?>
                                            <?php 
										 ?>
                                	<?php if(empty($showData['AssignedTo'])) { 
									if($_SESSION['userType']!="sale_rep"){
									?><a style="background: #7030a0;padding: 5px 18px;color: #fff;border-radius: 10px;text-decoration: none;margin-bottom:5px;display:block;" data-order_id="<?=$showData['Id'];?>" data-id="<?=$showData['TransactionID'];?>" data-value="<?=$prodsArr[$showData['ProductID']]['Title'];?>..." class="assign assignMBtn" id="">Assign</a><?php
									}
									 } ?><a style="background: #0070c0;padding: 5px 18px;color: #fff;border-radius: 10px;text-decoration: none; display:block; margin-bottom:5px;display:block;" href="?orderUID=<?=$showData['TransactionID'];?>" class="view">View</a>
								
                                
                                <?php if($showData['is_approve'] == 1) { ?>
                                        <a class="finish-btn" style="background:#3bc178 ;padding: 5px 18px;display:block;  color: #fff;border-radius: 10px;text-decoration: none;" href="?order_id=<?=$showData['TransactionID'];?>&is_approve=0" onClick="return confirm('Are you sure you want to approved order?')">Approved</a>
                                    <?php }else{ ?>
                                        <a class="finish-btn" style="background: #3bc178 ;padding: 5px 18px;color: #fff; display:block;border-radius: 10px;text-decoration: none;" href="?order_id=<?=$showData['TransactionID'];?>&is_approve=1" onClick="return confirm('Are you sure you want to approve order?')">Approve</a>
                                    <?php } ?>
                                
                                <a style="background: red ; margin-top:5px;padding: 5px 18px;color: #fff; display:block;border-radius: 10px;text-decoration: none;" href="?deleteorder=<?=$showData['Id'];?>&transactionid=<?=$showData['TransactionID'];?>" onClick="return confirm('Are you sure you want to delete order?')">Delete</a>
                                <?php 
									if($AssignTo==0 && $OrderStatus==4 && $TransactionData['PaymentStatus']!="Sales pending payment") {
								if($_SESSION['userType']!="sale_rep"){
								?>
                                
                                <a style="background: green ; margin-top:5px;padding: 5px 18px;color: #fff; display:block;border-radius: 10px;text-decoration: none;" href="?paidorder=<?=$showData['Id'];?>&transactionid=<?=$showData['TransactionID'];?>" onClick="return confirm('Are you sure you want to mark this order make as paid?')">Make as paid</a>
                                <?php  }} ?>
                                </div>
							

                                </td>
                            </tr>
<?php
	}
} else {
	echo "<tr>
			<td colspan=\"7\">No Search Result Found.</td>
		</tr>";
}
?>   

                            </tbody>
                        </thead>
                    </table>
                </div>
                    <div id="assignBox" class="modal fade" role="dialog">
                      <div class="modal-dialog">
                    
                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <h4 class="modal-title">Assign project <span id="projectName">&nbsp;</span></h4><button type="button" class="close" data-dismiss="modal">&times;</button>
                          </div>
                          <div class="modal-body">
                            <form name="assignFrm" method="post" >
                                <select name="assignedto" class="form-control">
                                    <option value="">assigned to</option>
                                    <?php foreach($usersArr as $designers) {
                                        if($designers['UserType'] != "designer") { continue; }
                                        echo '<option value="'.$designers['UserID'].'">'.$designers['FName'].' '.$designers['LName'].'</option>';
                                    } ?>
                                </select>
                                <input type="hidden" id="projectId" value="" name="projectId" />
                                <input type="hidden" id="projectOrderId" value="" name="projectOrderId" />
                                
                                <input type="submit" name="assignFBtn" class="btn-blue" value="Assign" />
                          	</form>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                          </div>
                        </div>
                    
                      </div>
                    </div>
                <nav class="mt-5">
                    <ul class="data-pagination justify-content-center flex-wrap">
                        <?= $pagination; ?>
                    </ul>
                </nav>

            </div>


        </div>
    </main>

    <?php include "includes/footer.php"; ?>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/script.js"></script>
    <script>
	$(".assignMBtn").on("click", function() {
		$("#projectId").val($(this).data('id'));
		$("#projectOrderId").val($(this).data('order_id'));
		
		$("#projectName").html("("+$(this).data('value')+")");
		$("#assignBox").modal('show');
	});
	</script>
    >
<style>
.cursor_pointer{ cursor:pointer;}
.active_box .totals-box {
	background-color: #f9f8fe;
}
</style>
</body>

</html>