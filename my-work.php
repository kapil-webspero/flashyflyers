
text/x-generic my-work.php ( HTML document, ASCII text, with very long lines )
<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';

	$PageTitle = "Orders";
	$CurrentTime =   time(); //1 week ago
	$Last7Days =   strtotime("-1 week"); //1 week ago
		// FETCH ORDER STATS
		$currentYearTimestamp = strtotime('first day of January '.date('Y'));

	if(!is_login()) {
		header("location:".SITEURL."login.php")	;
		exit();
	}
	$AccessID = intval($_SESSION['userId']);
	$AccessType = $_SESSION['userType'];
	$AccessData = GetSglRcrdOnCndi(USERS, "UserID = '$AccessID'");

	if($AccessType!="designer"){
		echo "<script> window.location.href = '".SITEURL."';</script>";
		exit();

	}
	if(isset($_REQUEST['orderUID']) && !empty($_REQUEST['orderUID'])) {
		$orderUID = intval($_REQUEST['orderUID']);
		$_SESSION['ORDERUID'] = $orderUID;
		echo "<script> window.location.href = '".SITEURL."work-details.php?order_id=".$orderUID."';</script>";
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
<title>My Work</title>
<head>
    <?php require_once 'files/headSection.php'; ?>
	<link rel="stylesheet" href="css/style-2.css">
	<link rel="stylesheet" href="css/style-3.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="MyWorkPage">
    <?php require_once 'files/headerSection.php'; ?>
	<?php
	//Pagination Code
	$prodType = getProdTypeParentArr();
$getProdSizes = getProdSizeArr();
$prodsArr = getALLProductArr();
	$searchTable = ORDER;
	$searchQuery = "`AssignedTo` = '$AccessID' AND ";
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";

	if(isset($_REQUEST['order_number']) && !empty($_REQUEST['order_number'])) {
		$searchQuery .= "TransactionID = '".$_REQUEST['order_number']."' AND ";
		$searchURL .= "&order_number=".$_REQUEST['order_number'];
	}

	if( !empty($_REQUEST['order_status']) && $_REQUEST['order_status'] == 3) {
	  $orderStatus = 3;
	  $searchQuery .= " is_approve = '1' ";
   }else{
	  $orderStatus = 1;
	  // $searchQuery .= " is_approve != '1' ";

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
		$searchQuery .= " ProductID IN ($idsProd) AND ";
	}


	// START AND END DATE QUERY
	if(isset($_REQUEST['order_date']) && !empty($_REQUEST['order_date'])) {
		$currentTime = strtotime($_REQUEST['order_date']);
		$afterTime = strtotime('+1 day', $currentTime);
		$searchQuery .= "(OrderDate >= '".$currentTime."' AND OrderDate <= '".$afterTime."') AND ";
		$searchURL .= "&order_date=".$_REQUEST['order_date'];
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
			$searchQuery .= " is_approve !='1' AND ResponseState IN('0','3','4') and OrderStatus IN('1','2','3') AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];
		}

		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="revisions") {
			$searchQuery .= " AssignedTo > 0 AND   OrderStatus='1' AND ResponseState='2' AND is_approve!= '1' AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];
		}


		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="new_order") {
			$searchQuery .= " AssignedTo = '0' AND is_approve!= '1' AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];
		}

		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="finish") {
				$searchQuery .= " (is_approve= '1' OR ResponseState IN('1') AND OrderStatus IN('1'))  AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];
		}

	}else{
		$searchQuery .= " is_approve!= '1' AND ResponseState IN('0','3','4') and OrderStatus IN('1','2','3') AND ";
		$searchURL .= "&order_status=" . $orderStatus;
	}



	$searchURL .= "&order_status=" . $orderStatus;

	$searchQuery = rtrim($searchQuery, " AND ");
	if(!empty($searchQuery))
		$query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable;

	//echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];

	?>
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content pl-60 pr-60 bx-shadow">

            <section class="orders-top mb-4">
                    <div class="row">
                        <?php /*?><div class="col-lg-2 col-sm-6 mb-4">
                            <div class="totals-box red cursor_pointer"  onClick="window.location='my-work.php?mode_type=top_filter&mode=new_order'">
                                <h1><?php
										$NewOrdersearchQuery .= " AssignedTo = '0' AND is_approve!= '1'";



	  $Neworderquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." WHERE ".$NewOrdersearchQuery;
	$Newordertotal_pages = mysql_fetch_array(mysql_query($Neworderquery));
    $newOrder = (!empty($Newordertotal_pages['num'])) ?  $Newordertotal_pages['num'] : '0';


								echo $newOrder;?></h1>
                                <p>new orders</p>

                            </div>
                        </div><?php */?>
                        <div class="col-lg-4 col-sm-6 mb-4  cursor_pointer <?php echo($_REQUEST['mode'] =="" || $_REQUEST['mode']=="proccess") ?"active_box":"" ?>"  onClick="window.location='my-work.php?mode_type=top_filter&mode=proccess'">
                            <div class="totals-box cyan">
                                <h1><?php
								$inProgressStatsquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where `AssignedTo` = '".$AccessID."' and  is_approve != '1' AND ResponseState IN('0','3','4') and OrderStatus IN('1','2','3') ";

								$inProgressStats = mysql_fetch_array(mysql_query($inProgressStatsquery));


								echo $inProgressStats['num'];?></h1>
                                <p>in progress</p>

                            </div>
                        </div>
						<div class="col-lg-4 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="revisions") ?"active_box":"" ?>"  onClick="window.location='my-work.php?mode_type=top_filter&mode=revisions<?php echo (isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) ?"&productId=".$_REQUEST['productId']:""; ?>'">
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
                                <p>under revisions</p>

                            </div>
                        </div>

						<?php /*?>
						<div class="col-lg-3 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="week") ?"active_box":"" ?>"  onClick="window.location='my-work.php?mode_type=top_filter&mode=week'">
                            <div class="totals-box purple">
                                <h1><?php
							 	 $ComplatedStatsquery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where `AssignedTo` = '".$AccessID."' and  is_approve = '1' and finishDate<='".$CurrentTime."' AND finishDate>='".$Last7Days."'";

								$ComplatedStats = mysql_fetch_array(mysql_query($ComplatedStatsquery));
								echo $ComplatedStats['num'];


								?></h1>
                                <p>completed this week</p>

                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="year") ?"active_box":"" ?>"  onClick="window.location='my-work.php?mode_type=top_filter&mode=year'">
                            <div class="totals-box blue">
                                <h1><?php

									 $yearTotalOrderStatsQuery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." where `AssignedTo` = '".$AccessID."' and   is_approve = '1' and finishDate>='".$currentYearTimestamp."'";

										$yearTotalOrderStats = mysql_fetch_array(mysql_query($yearTotalOrderStatsQuery));

								echo $yearTotalOrderStats['num'];?></h1>
                                <p>this year</p>

                            </div>
                        </div><?php */?>

                        <div class="col-lg-4 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="finish") ?"active_box":"" ?>"  onClick="window.location='my-work.php?mode_type=top_filter&mode=finish'">
                            <div class="totals-box" style="color:green;">
                                <h1><?php
									$completedFinishOrderDataQuery = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".ORDER." WHERE `AssignedTo` = '".$AccessID."' and (is_approve = '1' OR (ResponseState IN('1') AND OrderStatus IN('1')))";

	$completedFinishOrderData = mysql_fetch_array(mysql_query($completedFinishOrderDataQuery));


							echo $completedFinishOrderData['num'];?></h1>
                                <p style="color:green;">completed</p>

                            </div>
                        </div>


                    </div>
                </section>
                <h1 class="page-heading mb-4">Orders (<?=$total_pages;?>)</h1>

                <form action="" class="top-search-options mb-5">
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

                        <div class="col-lg-2">
                      	<input type="hidden" name="mode_type" value="<?php echo $_REQUEST['mode_type'] ?>">
                      	<input type="hidden" name="mode" value="<?php echo $_REQUEST['mode'] ?>">
                            <input type="text" name="order_number" class="form-control" placeholder="order #" value="<?php if(isset($_REQUEST['order_number'])){ echo $_REQUEST['order_number']; } ?>">
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
                            <input type="text" name="order_date" class="form-control datepicker date-field"   placeholder="Order date" value="<?php if(isset($_REQUEST['order_date'])){ echo $_REQUEST['order_date']; } ?>">
                        </div>




                        <div class="col-lg-2">
                         <input type="hidden" name="order_status" value="<?php echo $_REQUEST['order_status'];  ?>">

                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
                    <?php


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
                                <th scope="col" class="header <?php if($key_order=="ID" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="ID" && $key_order_by=="ASC"){echo "headerSortDown";} ?>" onClick="window.location='my-work.php?<?php echo http_build_query($_GET); ?>&key_order=ID&key_order_by=<?php echo $ordeBy; ?>'">Order#</th>
                                <th scope="col" class="header <?php if($key_order=="OrderDate" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="OrderDate" && $key_order_by=="ASC"){echo "headerSortDown";} ?>" onClick="window.location='my-work.php?<?php echo http_build_query($_GET); ?>&key_order=OrderDate&key_order_by=<?php echo $ordeBy; ?>'">Order date</th>
                                <th scope="col">Product</th>
                                <th scope="col">Status</th>
                                <th scope="col">Time</th>
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
		$sql = "SELECT * FROM ".$searchTable." WHERE ".$searchQuery." GROUP BY `TransactionID` ORDER BY `".$key_order."` ".$key_order_by."  LIMIT $start, $limit";
	else
		$sql = "SELECT * FROM ".$searchTable. " GROUP BY `TransactionID`  ORDER BY `".$key_order."` ".$key_order_by."  LIMIT $start, $limit";


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




			$AssignTo = $showData['AssignedTo'];
			$AssignOn = $showData['AssignedOn'];
							$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$showData['TransactionID']."'", "ResponseState", "ASC");
							$getRespnseCode = array();
							$productNameData = "";
								$IndexCount=1;
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
                                    <td><?php

									echo $productNameData;?></td>
                                    <td class="data-progress"><?php


							$OrderStatus =$showData['OrderStatus'];
							 if($showData['is_approve'] == 1){
                                echo "Approved";
                            }else{
							if($AssignTo==0) {
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
									 <td>	
										 <span class="timerCountdown<?=$showData['Id'];?>"> TRACKER</span>
										 <?php if($showData['template_type']=="customize" || $showData['template_type']==""){
												$timerDelHours = 0;
												 if(!empty($showData['customeProductsFileds'])){
													 
													$customeProductsFiledsTimer = multidimation_to_single_array(unserialize($showData['customeProductsFileds']));

													$timerDelHours=$customeProductsFiledsTimer['turnaround_time'];	
													 
												 }else{
														$timerDelHours = $showData['TurnAroundTime']; 
													}
										?>
											<script>
													jQuery(document).ready(function(e) {
														jQuery('.timerCountdown<?php echo $showData['Id']; ?>').dateTimer(
																{
																	date:'<?php echo date('d-m-Y H:i:s',$AssignOn); ?>', // day-month-year HH:MM:SS
																	hours:<?php echo getTimerCounterHours($timerDelHours); ?>,
																	endMessage:'<div class="time-cell timer-hour"><div class="timerDigits"><div class="timerLabel">Hours</div><div class="digitWiseCount">0</div><div class="digitWiseCount">0</div></div></div><div class="time-cell timer-minute"><div class="timerDigitsPoint">:</div><div class="timerDigits"><div class="timerLabel">Minutes</div><div class="digitWiseCount">0</div><div class="digitWiseCount">0</div></div></div><div class="time-cell timer-second"><div class="timerDigitsPoint">:</div><div class="timerDigits"><div class="timerLabel">Seconds</div><div class="digitWiseCount">0</div><div class="digitWiseCount">0</div></div></div>'
																}
															);	
												 });
											 </script>
											 <?php } ?>
									 </td>
                                   	<td class="data-view" align="center" style="margin:0 auto; text-align:center;"><a style="margin:0 auto;width:80px;padding:5px;" href="?orderUID=<?=$showData['TransactionID'];?>" class="view">view</a></td>
                                </tr>
<?php
	}
} else {
	echo "<tr>
			<td colspan=\"5\">No Search Result Found.</td>
		</tr>";
}
?>

                            </tbody>
                        </thead>
                    </table>
                </div>

                <nav class="mt-5">
                    <ul class="data-pagination justify-content-center flex-wrap">
                        <?= $pagination; ?>
                    </ul>
                </nav>

            </div>


        </div>
    </main>

    <?php include "includes/footerSection.php"; ?>
 <script src="superadmin/js/jquery.js"></script>
 <script defer src="<?=SITEURL;?>js/popper.min.js"></script>

<script defer src="<?=SITEURL;?>js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.css" />
<script src="js/timer.js"></script>
<script>
jQuery(document).ready(function(e) {
		 $('.datepicker').datepicker({
        	minDate: 0
        });
		});

</script>
<style>
.cursor_pointer{ cursor:pointer;}
.active_box .totals-box {
	background-color: #f9f8fe;
}
</style>
</body>

</html>