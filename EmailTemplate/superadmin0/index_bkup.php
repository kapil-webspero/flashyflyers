<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Dashboard";
	
	$userData = $AccessUData;
	
	$orderTable = ORDER;
	$productTable = PRODUCT;	
	
	
	// GET ALL ORDER DETAILS
	$orderCndy = 'PaymentStatus<>"failed" ORDER BY id DESC';
	$orderData = GetMltRcrdsOnCndi(TRANSACTION, $orderCndy);
	
	$totalOrder = 0;
	$totalSales = 0;
	$newOrder = 0;
	foreach($orderData as $orderInfo) {	
		$totalOrder++;	
		$totalSales+=$orderInfo['Amount'];
		if($orderInfo['Status']==0) {
			$newOrder++;
		}		
	}
	
	// GET TOTAL CUSTOMER
	$customerCndy = 'UserType="user"';
	$customerData = GetNumOfRcrdsOnCndi(USERS, $customerCndy);	
	
	// GET RECENT CUSTOMER
	$customerCndy = 'UserType="user" ORDER BY UserID DESC LIMIT 6';
	$recentCustomer = GetMltRcrdsOnCndi(USERS, $customerCndy);	
	
	// GET PRODUCT SALES RANK
	$fields = 'count(`ProductID`) as totalSales,sum(`TotalPrice`) as totalAmount,`ProductID`,pd.Title';
	$salesCndy = 'pd.id=od.ProductID GROUP BY `ProductID` ORDER BY `totalSales` DESC LIMIT 10';		
	$salesData = GetMltRcrdsWthSmFldsOnCndi("$orderTable as od,$productTable as pd", $salesCndy, $fields);
	
	// GET WEEKLY TOP SELLING PRODUCT
	$beforeDate = strtotime("-7 day");
	$currentDate = time();
	
	$fields = 'count(*) as salesCount,`ProductID`,sum(`TotalPrice`) as salesAmount';
	$topCndy = "`OrderDate`>=$beforeDate AND `OrderDate`<=$currentDate GROUP BY `ProductID` ORDER BY salesCount DESC";
	$topProductData = GetSglRcrdWthSmFldsOnCndi(ORDER, $topCndy, $fields);
	
	$fields = 'Title';
	$cndy = "id=".$topProductData['ProductID'];	
	$topProductTitle = GetSglRcrdWthSmFldsOnCndi(PRODUCT, $cndy, $fields);
	
	$fields = 'filename';
	$cndy = "prod_id=".$topProductData['ProductID'];	
	$topProductImage = GetSglRcrdWthSmFldsOnCndi(PRODUCT_BANNER, $cndy, $fields);
	
	
	// GET PRODUCT TOTAL BY CATEGORY
	$cndy = "`Category`=1";
	$businessProductCount = GetNumOfRcrdsOnCndi(PRODUCT, $cndy);
	
	$cndy = "`Category`=2";
	$nightclubProductCount = GetNumOfRcrdsOnCndi(PRODUCT, $cndy);
	
	$cndy = "`Category`=3";
	$eventsProductCount = GetNumOfRcrdsOnCndi(PRODUCT, $cndy);
	
	$cndy = "`Category` NOT IN (1,2,3)";
	$otherProductCount = GetNumOfRcrdsOnCndi(PRODUCT, $cndy);
	
	// GET SALES SUMMARY SUB TITLE FOR BUSSINESS	
	
	$previousWeek = strtotime("-14 day");
	$currentWeek = strtotime("-7 day");
	
	$cndy = "od.ProductID=pd.id AND pd.Category=1 AND OrderDate>=$previousWeek AND OrderDate<=$currentWeek";
	$businessSalesPrWeek = GetNumOfRcrdsOnCndi("$orderTable as od,$productTable as pd", $cndy);
	
	$cndy = "od.ProductID=pd.id AND pd.Category=1 AND OrderDate>=$currentWeek";
	$businessSalesCrWeek = GetNumOfRcrdsOnCndi("$orderTable as od,$productTable as pd", $cndy);
	
	$bussinessSummarySubTitle = "";
	if($businessSalesCrWeek>$businessSalesPrWeek) {
		if($businessSalesPrWeek>0) {
			$percentageHike = $businessSalesCrWeek-$businessSalesPrWeek;
			$percentageHike = $percentageHike/$businessSalesPrWeek;
			$percentageHike = $percentageHike*100;
		}
		else {
			$percentageHike = $businessSalesCrWeek*100;
		}
		$bussinessSummarySubTitle = "$percentageHike% more than last week";
	}
	else {
		if($businessSalesCrWeek>0) {
			$percentageHike = $businessSalesPrWeek-$businessSalesCrWeek;
			$percentageHike = $percentageHike/$businessSalesCrWeek;
			$percentageHike = $percentageHike*100;
		}
		else {
			$percentageHike = $businessSalesPrWeek*100;
		}
		$bussinessSummarySubTitle = "$percentageHike% less than last week";
	}
	
	
	// GET SALES SUMMARY SUB TITLE FOR NIGHTCLUB
		
	$cndy = "od.ProductID=pd.id AND pd.Category=2 AND OrderDate>=$previousWeek AND OrderDate<=$currentWeek";
	$nightclubSalesPrWeek = GetNumOfRcrdsOnCndi("$orderTable as od,$productTable as pd", $cndy);
	
	$cndy = "od.ProductID=pd.id AND pd.Category=2 AND OrderDate>=$currentWeek";
	$nightclubSalesCrWeek = GetNumOfRcrdsOnCndi("$orderTable as od,$productTable as pd", $cndy);
	
	$nightclubSummarySubTitle = "";
	if($nightclubSalesCrWeek>$nightclubSalesPrWeek) {
		if($nightclubSalesPrWeek>0) {
			$percentageHike = $nightclubSalesCrWeek-$nightclubSalesPrWeek;
			$percentageHike = $percentageHike/$nightclubSalesPrWeek;
			$percentageHike = $percentageHike*100;
		}
		else {
			$percentageHike = $nightclubSalesCrWeek*100;
		}
		$nightclubSummarySubTitle = "$percentageHike% more than last week";
	}
	else {
		if($nightclubSalesCrWeek>0) {
			$percentageHike = $nightclubSalesPrWeek-$nightclubSalesCrWeek;
			$percentageHike = $percentageHike/$nightclubSalesCrWeek;
			$percentageHike = $percentageHike*100;
		}
		else {
			$percentageHike = $nightclubSalesPrWeek*100;
		}
		$nightclubSummarySubTitle = "$percentageHike% less than last week";
	}
	
	// GET SALES SUMMARY SUB TITLE FOR EVENT SALES
		
	$cndy = "od.ProductID=pd.id AND pd.Category=3 AND OrderDate>=$previousWeek AND OrderDate<=$currentWeek";
	$eventSalesPrWeek = GetNumOfRcrdsOnCndi("$orderTable as od,$productTable as pd", $cndy);
	
	$cndy = "od.ProductID=pd.id AND pd.Category=3 AND OrderDate>=$currentWeek";
	$eventSalesCrWeek = GetNumOfRcrdsOnCndi("$orderTable as od,$productTable as pd", $cndy);
	
	$eventSummarySubTitle = "";
	if($eventSalesCrWeek>$eventSalesPrWeek) {
		if($businessSalesPrWeek>0) {
			$percentageHike = $eventSalesCrWeek-$eventSalesPrWeek;
			$percentageHike = $percentageHike/$eventSalesPrWeek;
			$percentageHike = $percentageHike*100;
		}
		else {
			$percentageHike = $eventSalesCrWeek*100;
		}
		$eventsSummarySubTitle = "$percentageHike% more than last week";
	}
	else {
		if($eventSalesCrWeek>0) {
			$percentageHike = $eventSalesPrWeek-$eventSalesCrWeek;
			$percentageHike = $percentageHike/$eventSalesCrWeek;
			$percentageHike = $percentageHike*100;
		}
		else {
			$percentageHike = $eventSalesPrWeek*100;
		}
		$eventsSummarySubTitle = "$percentageHike% less than last week";
	}
	
	// GET SALES SUMMARY SUB TITLE FOR OTHER PRODUCTS
		
	$cndy = "od.ProductID=pd.id AND pd.Category NOT IN (1,2,3) AND OrderDate>=$previousWeek AND OrderDate<=$currentWeek";
	$otherSalesPrWeek = GetNumOfRcrdsOnCndi("$orderTable as od,$productTable as pd", $cndy);
	
	$cndy = "od.ProductID=pd.id AND pd.Category NOT IN (1,2,3) AND OrderDate>=$currentWeek";
	$otherSalesCrWeek = GetNumOfRcrdsOnCndi("$orderTable as od,$productTable as pd", $cndy);
	
	$otherSummarySubTitle = "";
	if($otherSalesCrWeek>$otherSalesPrWeek) {
		if($otherSalesPrWeek>0) {
			$percentageHike = $otherSalesCrWeek-$otherSalesPrWeek;
			$percentageHike = $percentageHike/$otherSalesPrWeek;
			$percentageHike = $percentageHike*100;
		}
		else {
			$percentageHike = $otherSalesCrWeek*100;
		}
		$otherSummarySubTitle = "$percentageHike% more than last week";
	}
	else {
		if($otherSalesCrWeek>0) {
			$percentageHike = $otherSalesPrWeek-$otherSalesCrWeek;
			$percentageHike = $percentageHike/$otherSalesCrWeek;
			$percentageHike = $percentageHike*100;
		}
		else {
			$percentageHike = $otherSalesPrWeek*100;
		}
		$otherSummarySubTitle = "$percentageHike% less than last week";
	}
	
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
</head>

<body>
    <?php include "includes/header.php"; ?>

    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow">

                <section>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="dash-col-1">
                                <h2>Good morning <strong><?=$userData['FName']." ".$userData['LName'];?></strong></h2>
                                <div class="row">
                                    <div class="col-md-8 dash-login-col">
                                        <p class="mt-4"><strong>Last login:</strong> <br> <?php if(empty($userData['LastLogin']) && $userData['LastLogin']==0) { echo "&nbsp;"; } else { echo date('m/d/Y H:ia',$userData['LastLogin']);  } ?></p>
                                        <p class="mt-4"><strong>Your profile:</strong><br><a href="<?=ADMINURL;?>profile.php">Click here to view
                                            </a></p>
                                    </div>
                                    <div class="col-md-4 dash-login-col">
                                        <img src="images/date-big.png" width="70" alt="">
                                        <h1><strong><?php echo date('g:ia'); ?></strong></h1>
                                        <h3><strong><?php echo date('l'); ?>,</strong> <br> <?php echo date('M d, Y'); ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-lg-0 mt-4">
                            <div class="dash-col-2">
                                <h2 class="mb-4">Sales by <strong>Product Types</strong></h2>
                                <div class="d-flex align-items-center">
                                    <div class="pie-container">
                                        <canvas id="doughnut-chart"></canvas>
                                    </div>
                                    <div id="chart-legend"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="dash-stats-row">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 mt-4">
                            <div class="dash-stats">
                                <p>Total orders</p>
                                <h2><?=$totalOrder;?></h2><span>orders</span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mt-4">
                            <div class="dash-stats">
                                <p>Total sales</p>
                                    <h2>$<?=intval($totalSales);?></h2><span>USD</span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mt-4">
                            <div class="dash-stats">
                                <p>New orders</p>
                                <h2><?=$newOrder;?> </h2><span>orders</span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mt-4">
                            <div class="dash-stats">
                                <p>Total clients</p>
                                <h2><?=$customerData;?> </h2><span>customers</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-6">
                            <div class="graph-box blue mt-4">
                                <p>Business SALES</p>
                                <h3><?=$businessProductCount;?> <span>products</span> </h3>
                                <small><?=$bussinessSummarySubTitle;?></small>
                                <canvas id="sales-graph"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="graph-box red mt-4">
                                <p>Nightclub Sales</p>
                                <h3><?=$nightclubProductCount;?> <span>products</span> </h3>
                                <small><?=$nightclubSummarySubTitle;?></small>
                                <canvas id="animated-flyer-graph"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="graph-box green mt-4">
                                <p>Events Sales</p>
                                <h3><?=$eventsProductCount;?> <span>products</span> </h3>
                                <small><?=$eventsSummarySubTitle;?></small>
                                <canvas id="motion-flyer-graph"></canvas>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="graph-box purple mt-4">
                                <p>OTHER PRODUCTS</p>
                                <h3><?=$otherProductCount;?> <span>products</span> </h3>
                                <small><?=$otherSummarySubTitle;?></small>
                                <canvas id="other-products-graph"></canvas>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row mt-3">
                        <div class="col-lg-5 mt-5 product-sales">
                            <h4 class="blue mb-4"> Product sales rank
                            </h4>
                            <div class="ol-wrap">
                                <ol>
                                <?php $i=1; foreach($salesData as $sales) { ?>
									<li><span class="li-num">#<?=$i++;?></span><?=$sales['Title'];?> <span class="red">(<?=$sales['totalSales'];?> sales, $<?=$sales['totalAmount'];?>)</span></li>
								<?php } ?>	                                   
                                </ol>
                            </div>
                        </div>
                        <div class="col-lg-7 mt-5 recent-ord">
                            <h4 class="blue mb-3"> Recent orders
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-2">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>$$</th>
                                            <th>Product</th>
                                            <th>Client</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1;
										foreach($orderData as $orderInfo) { 
										$i++; if($i>=8) { break; } 
										$cndy = "UserID=".$orderInfo['UserID'];
										$userData = GetSglRcrdOnCndi(USERS, $cndy);
										
										$cndy = "TransactionID=".$orderInfo['id'];
										$mainOrderData = GetSglRcrdOnCndi(ORDER, $cndy);
										
										$cndy = "id=".$mainOrderData['ProductID'];
										$productData = GetSglRcrdOnCndi(PRODUCT, $cndy);
										?>										
											<tr>
                                                <td><?=$orderInfo['id'];?></td>
                                                <td><?=date('m/d/Y',$orderInfo['Createdon']);?></td>
                                                <td>$<?=$orderInfo['Amount'];?></td>
                                                <td><?=$productData['Title'];?></td>
                                                <td><?php echo $userData['FName']; ?></td>
                                                <td class="<?php if($orderInfo['Status']==0) { echo "inprogress"; } else { echo "complete"; } ?>"><?php if($orderInfo['Status']==0) { echo "In progress"; } else { echo "Complete"; } ?></td>
                                            </tr>
										<?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                <section>

                    <div class="row">
                        <div class="col-lg-6 mt-5 recent-customer">
                            <h4 class="blue mb-3"> RECENT CUSTOMERS </h4>
                            <div class="table-responsive">
                                <table class="table table-2">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>Email</th>
                                            <th>Join date</th>
                                            <th>State</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    	<?php foreach($recentCustomer as $userInfo) {
										if(!empty($userInfo['FName'])) {
											$userName = $userInfo['FName'].' '.$userInfo['LName'];
										}
										else {
											$nm = explode("@",$userInfo['Email']);
											$userName = $nm[0];
										}
										?>
                                        <tr>                                      
                                            <td><?=$userName;?></td>
                                            <td><?=$userInfo['Email'];?></td>
                                            <td><?=date('m/d/Y',$userInfo['CreatedDate']);?></td>
                                            <td>SC</td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-5 prd-week">
                            <h4 class="blue mb-4 text-center">Product of the week</h4>
                            <div class="top-week-prd text-center">
                                <span class="num">#1</span><?=$topProductTitle['Title'];?> <span class="prd-stats">(<?=$topProductData['salesCount'];?> sales,$<?=$topProductData['salesAmount'];?>)</span>
                                        <figure class="mt-3 mb-3">
                                            <img src="<?=$topProductImage['filename'];?>" class="img-fluid" alt="">
                                        </figure>
                                        <a href="#" class="btn-lg form-btn-grad">view sales</a>
                            </div>
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
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/home-charts.js"></script>
    <script src="js/script.js"></script>
</body>

</html>