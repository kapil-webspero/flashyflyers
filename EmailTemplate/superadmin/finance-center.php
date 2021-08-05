<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Finance Center";
	
	$isFilter = false;
	if(isset($_REQUEST['filter']) && $_REQUEST['filter'] == "yes") {
		if(!empty($_REQUEST['start_date']) && isset($_REQUEST['start_date'])) {
			$isFilter = true;
			$startDate = strtotime($_REQUEST['start_date']);
		} else {
			$startDate = 0;	
		}
		if(!empty($_REQUEST['end_date']) && isset($_REQUEST['end_date'])) {
			$isFilter = true;
			$endDate = strtotime($_REQUEST['end_date']);
		} else {
			$endDate = $systemTime;	
		}
		if(!empty($_REQUEST['product_type']) && isset($_REQUEST['product_type'])) {
			$isFilter = true;
			$prodfType = intval($_REQUEST['product_type']);
			$getProdsQuy = GetMltRcrdsOnCndi(PRODUCT,"`ProductType` = '".$prodfType."' AND `Status` = '1'");
			foreach($getProdsQuy as $productsQ) {
				$filtProdArr[] = $productsQ['id'];	
			}
			$productFIds = implode(",",$filtProdArr);
		} else {
			$prodfType = 'all';
		}
	}
	
	
	
	// GET ALL ORDER DETAILS
	$totalOrder = 0;
	$totalSales = 0;
	$newOrder = 0;
	if($isFilter) {
		if($prodfType != 'all') {
			
			$orderQuery = mysql_query("SELECT ".ORDER.".Id, ".ORDER.".TransactionID, ".ORDER.".TotalPrice, ".ORDER.".OrderStatus FROM ".ORDER." INNER JOIN ".PRODUCT." ON ".ORDER.".ProductID=".PRODUCT.".id INNER JOIN ".TRANSACTION." ON ".TRANSACTION.".id = ".ORDER.".TransactionID  WHERE ".TRANSACTION."PaymentStatus !='Sales pending payment' and   ".PRODUCT.".ProductType = '".$prodfType."' AND ".ORDER.".OrderDate >= $startDate AND ".ORDER.".OrderDate <= $endDate");
		} else {
			$orderQuery = mysql_query("SELECT ".ORDER.".Id, ".ORDER.".TransactionID, ".ORDER.".TotalPrice, ".ORDER.".OrderStatus FROM ".ORDER." INNER JOIN ".PRODUCT." ON ".ORDER.".ProductID=".PRODUCT.".id INNER JOIN ".TRANSACTION." ON ".TRANSACTION.".id = ".ORDER.".TransactionID  WHERE ".TRANSACTION.".PaymentStatus !='Sales pending payment' AND ".ORDER.".OrderDate >= $startDate AND ".ORDER.".OrderDate <= $endDate");
		
		
		
		
		}
		while($orderInfo = mysql_fetch_assoc($orderQuery)) {	
			
			$totaltOrder[$orderInfo['TransactionID']] = $orderInfo['TransactionID'];	
			$totalSales+=$orderInfo['TotalPrice'];
			if($orderInfo['OrderStatus']==0) {
				$newOrder++;
			}		
		}
		$totalOrder =count($totaltOrder);
	} else {
		
		$totalOrder =  GetNumOfRcrdsOnCndi(TRANSACTION,'PaymentStatus !="Sales pending payment"');		
		$orderCndy = 'PaymentStatus ="success" and PaymentStatus !="Sales pending payment" ORDER BY id DESC';
		$orderData = GetMltRcrdsOnCndi(TRANSACTION, $orderCndy);
		foreach($orderData as $orderInfo) {	
			// $totalOrder++;	
			$totalSales+=$orderInfo['Amount'];
			if($orderInfo['OrderStatus']==0) {
				$newOrder++;
			}		
		}
	}
	
	
	
	
	// GET TOTAL CUSTOMER
	$customerCndy = 'UserType="user"';
	$customerData = GetNumOfRcrdsOnCndi(USERS, $customerCndy);


	
	
	
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <div class="container finance-center-menu">
        <a href="<?=ADMINURL;?>transactions.php">Transactions</a>
		<a href="<?=ADMINURL;?>work-report.php">Work Report</a>
    </div>   
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content pl-60 pr-60 bx-shadow">
                <h1 class="page-heading mb-4">Finance center</h1>
                <form action="" class="top-search-options">
                    <div class="d-flex justify-content-stretch flex-wrap">
                        <div class="w-lg-20 w-100">
                            <a href="<?=ADMINURL;?>finance-center.php" class="btn-lg btn-block form-btn-grad">View all</a>
                        </div>
                        <div class="w-lg-20 w-100">
                            <input type="hidden" name="filter" value="yes" />  
                            <input type="text" name="start_date" class="form-control date-field" readonly data-toggle="datepicker" placeholder="start date" value="<?php if(!empty($_REQUEST['start_date'])) { echo $_REQUEST['start_date']; } ?>">
                        </div>
                        <div class="w-lg-20 w-100">
                            <input type="text" name="end_date" class="form-control date-field" readonly data-toggle="datepicker" placeholder="end date" value="<?php if(!empty($_REQUEST['start_date'])) { echo $_REQUEST['end_date']; } ?>">
                        </div>
                        <div class="w-lg-20 w-100">
                            <select name="product_type" id="" class="form-control">
                                <option value="">product type</option>
                                <?php 
								$prodType = getProdTypeParentArr();
								foreach($prodType as $keyType => $typeData) { ?>
                                <option value="<?=$keyType;?>" <?php if($prodfType == $keyType) { echo "selected";} ?>><?=$typeData['name'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="w-lg-20 w-100">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
                <section class="overall-totals mt-4 mb-5">
                    <h2 class="blue mb-3">Overall totals</h2>
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <div class="totals-box red">
                                <p>All Orders</p>
                                <h1><?=$totalOrder;?></h1>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <div class="totals-box cyan">
                                <p>Sales</p>
                                <h1>$<?=formatPrice($totalSales);?></h1>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <div class="totals-box purple">
                                <p>Customers</p>
                                <h1><?=$customerData;?></h1>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <div class="totals-box blue">
                                <p>New orders</p>
								<?php
									$cndy1 = "Id!=''";
									$OrderStatsTotal = GetMltRcrdsOnCndi(ORDER, $cndy1);
									$newOrderStats = $completedOrderStats = $inProgressStats =  0  ;
									foreach($OrderStatsTotal as $showData){
										$AssignTo = $showData['AssignedTo'];
										$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$showData['TransactionID']."'", "ResponseState", "ASC");
										$getRespnseCode = array();
											foreach($OrderData as $k=>$v){
													$getRespnseCode[$v['ResponseState']] = $v['ResponseState'];		
											}
										$OrderStatus =$showData['OrderStatus'];
										
										if($AssignTo==0) {
											$newOrderStats++;
										}else if($AssignTo>0 && $OrderStatus==1 && in_array(3,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(2,$getRespnseCode)) {
											$inProgressStats++;
											
										}else if($AssignTo>0 && $OrderStatus==1 && in_array(1,$getRespnseCode)  && !in_array(2,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
										$completedOrderStats++;
										}else if($AssignTo>0 && $OrderStatus==1 && in_array(2,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
											$inProgressStats++;
											
										}else if($AssignTo>0 && $OrderStatus==1 ) {
											$inProgressStats++;
										}
									}
								
								?>
                                <h1><?=$inProgressStats;?></h1>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="totals-category mb-4">
                    <h2 class="blue mb-3">Totals by product type</h2>
                    <div class="row">
                    	<?php foreach($prodType as $keyType => $typeData) { ?>
                        <div class="col-lg-3 col-sm-6 mb-5">
                            <div class="totals-brd-box cyan">
                                <p>
								<?php
								if(strtolower($typeData['name']) == "flyers") {
									echo 'Flyer<br>Template';
								} elseif(strtolower($typeData['name']) == "snapchat filter") {
									echo 'Snapchat<br>Filter';
								} else {
									echo $typeData['name'];
								}?>
                                </p> 
                                
                                <?php
									if($isFilter) {
										$orderQuery = mysql_query("SELECT ".ORDER.".Id FROM ".ORDER." INNER JOIN ".PRODUCT." ON ".ORDER.".ProductID=".PRODUCT.".id WHERE ".PRODUCT.".parent_product_cat_id = '".$keyType."' AND ".ORDER.".OrderDate >= $startDate AND ".ORDER.".OrderDate <= $endDate");
									} else {
										$orderQuery = mysql_query("SELECT ".ORDER.".Id FROM ".ORDER." INNER JOIN ".PRODUCT." ON ".ORDER.".ProductID=".PRODUCT.".id WHERE ".PRODUCT.".parent_product_cat_id = '".$keyType."'");
									}
									$orderIds = array();
									$ordertSale = 0;
									while($orderfArr = mysql_fetch_assoc($orderQuery)) {
										$orderIds[] = $orderfArr['Id'];	
									}
									if(count($orderIds)>0) {
										$ids = implode(",",$orderIds);
										$ordertSale = GetSumOnCndi(ORDER,"TotalPrice","Id IN($ids)");	
									}
								?>
                                <h2><?=count($orderIds);?> <small>orders</small></h2>
                                <h2>$<?=formatPrice($ordertSale);?><small>USD</small></h2>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </section>
                <section class="totals-listing">
                    <div class="row">
                    
	<?php
    // GET PRODUCT SALES RANK
	if($isFilter) {
		$productFIds;
		//SELECT count(`ProductID`) as totalSales, sum(`TotalPrice`) as totalAmount,od.ProductID,u.Title FROM tbl_orders as od,tbl_products as u WHERE u.id=od.ProductID AND u.ProductType = 1 AND od.OrderDate >= 1434896000 AND od.OrderDate <= 1538352000 GROUP BY `ProductID` ORDER BY `totalSales` DESC LIMIT 10
		if($prodfType != 'all') {
			$fields = 'count(`ProductID`) as totalSales, sum(`TotalPrice`) as totalAmount,od.ProductID,u.Title';
			$salesCndy = 'u.id=od.ProductID AND u.ProductType = '.$prodfType.' AND od.OrderDate >= '.$startDate.' AND od.OrderDate <= '.$endDate.' GROUP BY `ProductID` ORDER BY `totalSales` DESC LIMIT 10';
		} else {
			$fields = 'count(`ProductID`) as totalSales, sum(`TotalPrice`) as totalAmount,od.ProductID,u.Title';
			$salesCndy = 'u.id=od.ProductID AND od.OrderDate >= '.$startDate.' AND od.OrderDate <= '.$endDate.' GROUP BY `ProductID` ORDER BY `totalSales` DESC LIMIT 10';
		}
		$salesData = GetMltRcrdsWthSmFldsOnCndi(ORDER." as od,".PRODUCT." as u", $salesCndy, $fields);
	} else {
		$fields = 'count(`ProductID`) as totalSales,sum(`TotalPrice`) as totalAmount,`ProductID`,pd.Title';
		$salesCndy = 'pd.id=od.ProductID GROUP BY `ProductID` ORDER BY `totalSales` DESC LIMIT 10';
		$salesData = GetMltRcrdsWthSmFldsOnCndi(ORDER." as od,".PRODUCT." as pd", $salesCndy, $fields);
	}
	
	?>
                    	
                        <div class="col-lg-6 mb-4">
                            <h2 class="blue mb-3">Totals by product</h2>
                            <ol class="pt-3">
                            	<?php $i=1; foreach($salesData as $sales) { ?>
									<li><span class="li-num">#<?=$i++;?></span><?=$sales['Title'];?> <span class="red">(<?=$sales['totalSales'];?> sales, $<?=formatPrice($sales['totalAmount']);?>)</span></li>
								<?php } ?>
                            </ol>
                        </div>
   	<?php
	// GET BUYER RANK
	if($isFilter) {
		
		if($prodfType != 'all') {
			$fields2 = 'count(`CustomerID`) as totalSales,sum(`TotalPrice`) as totalAmount,`CustomerID`,u.FName, u.LName';
			$purchaseCndy = 'u.UserID=od.CustomerID AND od.ProductID IN ('.$productFIds.') AND od.OrderDate >= '.$startDate.' AND od.OrderDate <= '.$endDate.' GROUP BY `CustomerID` ORDER BY `totalSales` DESC LIMIT 10';
		} else {
			$fields2 = 'count(`CustomerID`) as totalSales,sum(`TotalPrice`) as totalAmount,`CustomerID`,u.FName, u.LName';
			$purchaseCndy = 'u.UserID=od.CustomerID AND od.OrderDate >= '.$startDate.' AND od.OrderDate <= '.$endDate.' GROUP BY `CustomerID` ORDER BY `totalSales` DESC LIMIT 10';
		}
		$purchaseData = GetMltRcrdsWthSmFldsOnCndi(ORDER." as od,".USERS." as u", $purchaseCndy, $fields2);
	} else {
		$fields2 = 'count(`CustomerID`) as totalSales,sum(`TotalPrice`) as totalAmount,`CustomerID`,u.FName, u.LName';
		$purchaseCndy = 'u.UserID=od.CustomerID GROUP BY `CustomerID` ORDER BY `totalSales` DESC LIMIT 10';		
		$purchaseData = GetMltRcrdsWthSmFldsOnCndi(ORDER." as od,".USERS." as u", $purchaseCndy, $fields2);
	}
	?>
                        <div class="col-lg-6 mb-4">
                            <h2 class="blue mb-3">Totals by client</h2>
                            <ol class="pt-3">
                                <?php $i=1; foreach($purchaseData as $purchase) { ?>
									<li><span class="li-num">#<?=$i++;?></span><?=$purchase['FName']. " " .$purchase['LName'];?> <span class="red">(<?=$purchase['totalSales'];?> orders, $<?=formatPrice($purchase['totalAmount']);?>)</span></li>
								<?php } ?>
                            </ol>
                        </div>
                    </div>
                </section>

            </div>


        </div>
    </main>

    <?php include "includes/footer.php"; ?>
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>