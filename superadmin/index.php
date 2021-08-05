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
	
	
	//$startDate = date("Y-m-d")." 00:00:00";
		//		$EndDate = date("Y-m-d")." 23:58:00";
      //        
			//$NewOrdersearchQuery .= " (AssignedTo = '0' OR CAST(OrderDate AS SIGNED) >  '".strtotime($startDate)."' and CAST(OrderDate AS SIGNED) <'".strtotime($EndDate)."') AND is_approve!= '1'";
				$NewOrdersearchQuery .= " AssignedTo = '0' AND is_approve!= '1'";
				
				
				
	 $Neworderquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$orderTable." WHERE ".$NewOrdersearchQuery;
	$Newordertotal_pages = mysql_fetch_array(mysql_query($Neworderquery));
    $newOrder = (!empty($Newordertotal_pages['num'])) ?  $Newordertotal_pages['num'] : '0';
	

	// GET TOTAL CUSTOMER
	$customerCndy = 'UserType="user"';
	$customerData = GetNumOfRcrdsOnCndi(USERS, $customerCndy);	
	
	// GET RECENT CUSTOMER
	$customerCndy = 'UserType="user" ORDER BY UserID DESC LIMIT 6';
	$recentCustomer = GetMltRcrdsOnCndi(USERS, $customerCndy);	
	
	// GET PRODUCT SALES RANK
	$fields = 'count(`ProductID`) as totalSales,sum(`TotalPrice`) as totalAmount,`ProductID`,pd.Title';
	$salesCndy = 'pd.id=od.ProductID GROUP BY `ProductID` ORDER BY `totalSales` DESC LIMIT 10';		
	$salesData = GetMltRcrdsWthSmFldsOnCndi(ORDER." as od,".PRODUCT." as pd", $salesCndy, $fields);
	
	// GET WEEKLY TOP SELLING PRODUCT
	$beforeDate = strtotime("-7 day");
	$currentDate = time();
	
	$fields = 'count(*) as salesCount,`ProductID`,sum(`TotalPrice`) as salesAmount';
	$topCndy = "`OrderDate`>=$beforeDate AND `OrderDate`<=$currentDate GROUP BY `ProductID` ORDER BY salesCount DESC";
	$topProductData = GetSglRcrdWthSmFldsOnCndi(ORDER, $topCndy, $fields);
	
	$fields = 'Title';
	$cndy = "id='".$topProductData['ProductID']."'";	
	$topProductTitle = GetSglRcrdWthSmFldsOnCndi(PRODUCT, $cndy, $fields);
	
	$fields = 'filename';
	$cndy = "prod_id='".$topProductData['ProductID']."' AND `filetype` = 'image'";	
	$topProductImage = GetSglRcrdWthSmFldsOnCndi(PRODUCT_BANNER, $cndy, $fields);
	
	$prodTypeArr = getProdTypeArr();
	
?>
<!DOCTYPE html>
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
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
                                <h2>
                                <?php 
								

							$hour      = date('H');
							
							if ($hour >= 20) {
								$greetings = "Good Night";
							} elseif ($hour > 17) {
							   $greetings = "Good Evening";
							} elseif ($hour > 11) {
								$greetings = "Good Afternoon";
							} elseif ($hour < 12) {
							   $greetings = "Good Morning";
							}
							echo $greetings;


								?>
                                <strong><?=$userData['FName']." ".$userData['LName'];?></strong></h2>
                                <div class="row">
                                    <div class="col-md-8 dash-login-col">
                                        <p class="mt-4"><strong>Last login:</strong> <br> <?php if(empty($userData['LastLogin']) && $userData['LastLogin']==0) { echo "&nbsp;"; } else { echo date('m/d/Y g:ia',$userData['LastLogin']);  } ?></p>
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
                            <a href="<?=ADMINURL;?>orders.php" class="total-count-dbs">
                                <div class="dash-stats">
                                <p>Total orders</p>
                                <h2><?=$totalOrder;?></h2><span>orders</span>
                            </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-sm-6 mt-4">
                            <a href="<?=ADMINURL;?>transactions.php" class="total-count-dbs">
                            <div class="dash-stats">
                                <p>Total sales</p>
                                    <h2>$<?=intval($totalSales);?></h2><span>USD</span>
                            </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-sm-6 mt-4">
                            <a href="<?=ADMINURL;?>orders.php?is_new_order=1" class="total-count-dbs">
                            <div class="dash-stats">
                                <p>New orders</p>
                                <h2><?=$newOrder;?> </h2><span>orders</span>
                            </div>
                            </a>
                        </div>
                        <div class="col-lg-3 col-sm-6 mt-4">
                            <a href="<?=ADMINURL;?>users.php" class="total-count-dbs">
                            <div class="dash-stats">
                                <p>Total clients</p>
                                <h2><?=$customerData;?> </h2><span>customers</span>
                            </div>
                            </a>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="row justify-content-center">
                    	<?php 
						$previousWeek = strtotime("-14 day");
						$currentWeek = strtotime("-7 day");
					
						$j=0;
						foreach($prodTypeArr as $i=>$value) {
							if($j==4){continue;}
							$j++;
							$ProductCount = GetNumOfRcrdsOnCndi(PRODUCT, "`parent_product_cat_id`='".$i."'");
							$cndy = "od.ProductID=pd.id AND pd.parent_product_cat_id='".$i."' AND OrderDate>=$previousWeek AND OrderDate<=$currentWeek";
							$salesPrWeek = GetNumOfRcrdsOnCndi(ORDER." as od,".PRODUCT." as pd", $cndy);
							
							$cndy = "od.ProductID=pd.id AND pd.parent_product_cat_id='".$i."' AND OrderDate>=$currentWeek";
							$salesCrWeek = GetNumOfRcrdsOnCndi(ORDER." as od,".PRODUCT." as pd", $cndy);
							
							$summarySubTitle = "";
							if($salesCrWeek>$salesPrWeek) {
								if($salesPrWeek>0) {
									$percentageHike = $salesCrWeek-$salesPrWeek;
									$percentageHike = $percentageHike/$salesPrWeek;
									$percentageHike = $percentageHike*100;
								}
								else {
									$percentageHike = $salesCrWeek*100;
								}
								$summarySubTitle = formatPrice($percentageHike)."% more than last week";
							}
							else {
								if($salesCrWeek>0) {
									$percentageHike = $salesPrWeek-$salesCrWeek;
									$percentageHike = $percentageHike/$salesCrWeek;
									$percentageHike = $percentageHike*100;
								}
								else {
									$percentageHike = $salesPrWeek*100;
								}
								$summarySubTitle = formatPrice($percentageHike)."% less than last week";
							}
							
							?>
                        <div class="col-lg-6 col-md-6">
                            <div class="graph-box blue mt-4">
                                <p><?=ucwords($prodTypeArr[$i]['name']);?> sales</p>
                                <h3><?=$ProductCount;?> <span>products</span> </h3>
                                <small><?=$summarySubTitle;?></small>
                                <canvas id="sales-graph-<?=$i;?>"></canvas>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="col-lg-6 col-md-6">
     <?php					
	$cndy = "`parent_product_cat_id` NOT IN (1,2,3,4)";
	$ProductCountOther = GetNumOfRcrdsOnCndi(PRODUCT, $cndy);
	
	// GET SALES SUMMARY SUB TITLE FOR OTHER PRODUCTS
		
	$cndy = "od.ProductID=pd.id AND pd.parent_product_cat_id NOT IN (1,2,3,4) AND OrderDate>=$previousWeek AND OrderDate<=$currentWeek";
	$otherSalesPrWeek = GetNumOfRcrdsOnCndi("$orderTable as od,$productTable as pd", $cndy);
	
	$cndy = "od.ProductID=pd.id AND pd.parent_product_cat_id NOT IN (1,2,3,4) AND OrderDate>=$currentWeek";
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
		$otherSummarySubTitle = formatPrice($percentageHike)."% more than last week";
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
		$otherSummarySubTitle = formatPrice($percentageHike)."% less than last week";
	}
	?>
                            <div class="graph-box purple mt-4">
                                <p>All Products</p>
                                <h3><?=$ProductCountOther;?> <span>products</span> </h3>
                                <small><?=$otherSummarySubTitle;?></small>
                                <canvas id="sales-graph-other"></canvas>
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
									<li><span class="li-num">#<?=$i++;?></span><?=$sales['Title'];?> <span class="red">(<?=$sales['totalSales'];?> sales, $<?=formatPrice($sales['totalAmount']);?>)</span></li>
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
										$cndy = "UserID='".$orderInfo['UserID']."'";
										$userData = GetSglRcrdOnCndi(USERS, $cndy);

										$cndy = "TransactionID='".$orderInfo['id']."'";
										$mainOrderData = GetSglRcrdOnCndi(ORDER, $cndy);

										$cndy = "id='".$mainOrderData['ProductID']."'";

										$productData = GetSglRcrdOnCndi(PRODUCT, $cndy);
										?>										
											<tr>
                                                <td><?=$orderInfo['id'];?></td>
                                                <td><?=date('m/d/Y',$orderInfo['Createdon']);?></td>
                                                <td>$<?=formatPrice($orderInfo['Amount']);?></td>
                                                <td><?=$productData['Title'];?></td>
                                                <td><?php echo $userData['FName']; ?></td>
                                                <td style="width:120px;" class="<?php if($mainOrderData['Status']!=2) { echo "inprogress"; } else { echo "complete"; } ?>"><?php if($mainOrderData['Status']!=2) { echo "In progress"; } else { echo "Complete"; } ?></td>
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
                            <div class="table-responsive div_desh_footertable">
                                <table class="table table-2">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                        
                                            <th>Join date</th>
                                          <?php /*?>  <th>State</th><?php */?>
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
                                            <td><?=$userName;?><br><small style="font-size: 16px;"><?=$userInfo['Email'];?></small></td>
                                        
                                            <td><?=date('m/d/Y',$userInfo['CreatedDate']);?></td>
                                             <?php /*?><td><?php echo ($userInfo['State'] != '') ? $userInfo['State'] :'-';?></td><?php */?>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-6 mt-5 prd-week">
                            <h4 class="blue mb-4 text-center">Product of the week</h4>
                            <div class="top-week-prd text-center">
                            	<?php if(!empty($topProductTitle)) { ?>
                                <span class="num">#1</span><?=$topProductTitle['Title'];?> <span class="prd-stats">(<?=$topProductData['salesCount'];?> sales,$<?=formatPrice($topProductData['salesAmount']);?>)</span>
                                <figure class="mt-3 mb-3">
                                    <img src="<?=$topProductImage['filename'];?>" class="img-fluid" alt="">
                                </figure>
                                <a href="<?=ADMINURL;?>orders.php?productId=<?=$topProductData['ProductID'];?>" class="btn-lg form-btn-grad">view sales</a>
                                <?php } else { ?>
                                No Product Found.
                                <?php } ?>
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