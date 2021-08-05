<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	
	$PageTitle = "Orders";
	
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
	
	if(isset($_REQUEST['orderUID']) && !empty($_REQUEST['orderUID'])) {
		$orderUID = intval($_REQUEST['orderUID']);
		$_SESSION['ORDERUID'] = $orderUID;
		echo "<script> window.location.href = '".SITEURL."order-details.php?order_id=".$orderUID."';</script>";
		exit();
	}	
?>
<!DOCTYPE html>
<html lang="en">

<head>
<title>My Orders</title>
    <?php require_once 'files/headSection.php'; ?>
	<link rel="stylesheet" href="css/style-2.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
.DesktopVersionViewAction{}
.mobileVersionViewAction{ display:none;}
@media screen and (max-width: 768px) {
.DesktopVersionViewAction{ display:none;}
.mobileVersionViewAction{ display:table-cell !important;}
.orders-table thead th:nth-child(1){ width:130px !important ;}
.orders-table thead th:nth-child(2){ width:115px !important ;}
.orders-table thead th:nth-child(3){ width:130px !important ;}
.orders-table thead th:nth-child(4){ width:130px !important ;}
.orders-table thead th:nth-child(5){ width:130px !important ;}
}
.cursor_pointer{ cursor:pointer;}
.active_box .totals-box {
	background-color: #f9f8fe;
}
</style>
</head>

<body class="MyOrderPage">
    <?php require_once 'files/headerSection.php'; ?>
	<?php
	//Pagination Code

$getProdSizes = getProdSizeArr();
$prodsArr = getALLProductArr();	
	$searchTable = ORDER;
	$searchQuery = "`CustomerID` = '$AccessID' AND ";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";	
	
	
	
	if(isset($_REQUEST['mode_type']) && !empty($_REQUEST['mode_type']) && $_REQUEST['mode_type']=="top_filter") {
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="rivision") {
			$searchQuery .= " is_approve !='1' AND (ResponseState=2)  AND  ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="proccess") {
			$searchQuery .= " is_approve !='1' AND (ResponseState=1 OR ResponseState=3 OR ResponseState=0)  AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
		
		if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="finish") {
			$searchQuery .= " is_approve= '1' AND ";
			$searchURL .= "&mode=".$_REQUEST['mode']."&mode_type=".$_REQUEST['mode_type'];	
		}
	
	}else{
	
	}
	$searchQuery = rtrim($searchQuery, " AND ");
	
	if(!empty($searchQuery))
		$query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable;
	//echo $query;
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];
	
	
	
$sql = "SELECT DISTINCT TransactionID  FROM ".$searchTable. " where  `CustomerID` = '$AccessID' ";
   	
    $result = mysql_query($sql);
   $complated = $revision  = $inProccess = 0;
	while($showData = mysql_fetch_array($result))
	{ 
	
	$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$showData['TransactionID']."'", "ResponseState", "ASC");
	
	$getRespnseCode= array();
			foreach($OrderData as $k=>$v){
		$getRespnseCode[$v['ResponseState']] = $v['ResponseState'];
								}
	
	$AssignTo = $OrderData[0]['AssignedTo'];
	$OrderStatus =$OrderData[0]['OrderStatus'];
	if($OrderData[0]['is_approve'] == 1){
    	$complated++;
   }else{
							
							if($AssignTo==0 && $OrderStatus==4 ) {
									$inProccess++;
							}else if($AssignTo==0 &&  $OrderStatus==0) {
									$inProccess++;
							
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(3,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(2,$getRespnseCode)) {
								$inProccess++;
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(1,$getRespnseCode)  && !in_array(2,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
								$inProccess++;
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(2,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
							$revision++;
							}else if($AssignTo>0 && $OrderStatus==1 ) {
								$inProccess++;
							}
							}
}
	?> 
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content pl-60 pr-60 bx-shadow">
            <section class="orders-top mb-4">
                    <div class="row">
                        
                        <div class="col-lg-3 col-sm-6 mb-4 cursor_pointer  cursor_pointer <?php echo($_REQUEST['mode']=="") ?"active_box":"" ?>"  onClick="window.location='my-orders.php'">
                            <div class="totals-box blue">
                                <h1><?php 
								$allsQL = "SELECT COUNT(DISTINCT TransactionID) as num  FROM ".$searchTable. " where  `CustomerID` = '$AccessID' ";
								
								$AllOrders = mysql_fetch_array(mysql_query($allsQL));
								$AllOrders = $AllOrders['num'];
	
								echo $AllOrders;?></h1>
                                <p>All</p>

                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-4 cursor_pointer  cursor_pointer <?php echo($_REQUEST['mode']=="proccess") ?"active_box":"" ?>"  onClick="window.location='my-orders.php?mode_type=top_filter&mode=proccess'">
                            <div class="totals-box cyan">
                                <h1><?php 
								
								echo $inProccess;?></h1>
                                <p>In progress </p>

                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="rivision") ?"active_box":"" ?>"  onClick="window.location='my-orders.php?mode_type=top_filter&mode=rivision'">
                            <div class="totals-box red">
                                <h1><?php 
								echo $revision;?></h1>
                                <p>Under revision</p>

                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-4 cursor_pointer <?php echo($_REQUEST['mode']=="finish") ?"active_box":"" ?>"  onClick="window.location='my-orders.php?mode_type=top_filter&mode=finish'">
                            <div class="totals-box green" style="color:green;">
                                <h1><?php 
							 	echo $complated;
								
								?></h1>
                                <p style="color:green;">Completed</p>

                            </div>
                        </div>
                        
                        
                        
                    </div>
                </section>
                <h1 class="page-heading mb-4">Orders (<?=$total_pages;?>)</h1>
					<?php  	if(!isset($_REQUEST['key_order'])){
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
                                <th scope="col" class="mobileVersionViewAction">Action</th>
                                  <th scope="col" class="header <?php if($key_order=="ID" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="ID" && $key_order_by=="ASC"){echo "headerSortDown";} ?>" onClick="window.location='my-orders.php?<?php echo http_build_query($_GET); ?>&key_order=ID&key_order_by=<?php echo $ordeBy; ?>'">Order#</th>
                                <th scope="col" class="header <?php if($key_order=="OrderDate" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="OrderDate" && $key_order_by=="ASC"){echo "headerSortDown";} ?>" onClick="window.location='my-orders.php?<?php echo http_build_query($_GET); ?>&key_order=OrderDate&key_order_by=<?php echo $ordeBy; ?>'">Order date</th>
                                <th scope="col">Product</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="DesktopVersionViewAction">Action</th>
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
	{ //echo '<pre>';print_r($prodsArr);echo '</pre>'; exit;
	
						$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$showData['TransactionID']."'", "ResponseState", "ASC");
						$TransactionData = GetSglRcrdOnCndi(TRANSACTION, "`id` = '".$showData['TransactionID']."'");
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
                                	    	<td class="data-view mobileVersionViewAction"><a style="margin:0 auto;width:80px;padding:5px; background:#0070c0; margin-bottom:5px;"    href="<?=SITEURL;?>my-orders.php?orderUID=<?=$showData['TransactionID'];?>" class="view" title="View">View</a>
                                               <?php if($TransactionData['RepUserID']>0 && $TransactionData['PaymentStatus']=="Sales pending payment"){ ?> 
                                    <a class="finish-btn" style="background: #3bc178 ;padding: 5px 18px;color: #fff; display:block;border-radius: 10px;text-decoration: none;" href="<?php echo SITEURL; ?>checkout.php?key=<?=$TransactionData['RepKey'];?>" title="Pay Now">Pay Now</a>
                                    <?php } ?>
                                            
                                            </td>
                               
                                    <td class="data-id"><span><?=$showData['TransactionID'];?></span></td>
                                    <td><?=date('M d, Y',$showData['OrderDate'])."<br>".date('g:i A',$showData['OrderDate']);?></td>
                                    <td><?=$productNameData;?></td>
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
                                   	<td class="data-view DesktopVersionViewAction"><a style="background: #0070c0;padding: 5px 18px;color: #fff;border-radius: 10px;text-decoration: none; display:block; margin-bottom:5px;display:block;"    href="<?=SITEURL;?>my-orders.php?orderUID=<?=$showData['TransactionID'];?>" class="view" title="View">View</a>
                                    
                                    <?php if($TransactionData['RepUserID']>0 && $TransactionData['PaymentStatus']=="Sales pending payment"){ ?> 
                                    <a class="finish-btn" style="background: #3bc178 ;padding: 5px 18px;color: #fff; display:block;border-radius: 10px;text-decoration: none;" href="<?php echo SITEURL; ?>checkout.php?key=<?=$TransactionData['RepKey'];?>" title="Pay Now">Pay Now</a>
                                    <?php } ?>
                                    
                                    </td>
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

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/script.js"></script>

</body>

</html>