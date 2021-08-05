<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	
	$PageTitle = "Sales Orders";
	
	if(!is_login()) {
		header("location:".SITEURL."login.php")	;
		exit();
	}
	
	$AccessID = intval($_SESSION['userId']);
	$AccessType = $_SESSION['userType'];
	$AccessData = GetSglRcrdOnCndi(USERS, "UserID = '$AccessID'");
	
	if(isset($_REQUEST['orderUID']) && !empty($_REQUEST['orderUID'])) {
		$orderUID = intval($_REQUEST['orderUID']);
		$_SESSION['ORDERUID'] = $orderUID;
		echo "<script> window.location.href = '".SITEURL."sale-customer-order-details.php?order_id=".$orderUID."';</script>";
		exit();
	}	
	
	
?>
<!DOCTYPE html>
<html lang="en">

<head>
<title>Sales Orders</title>
    <?php require_once 'files/headSection.php'; ?>
	<link rel="stylesheet" href="css/style-2.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
.DesktopVersionViewAction{}
.mobileVersionViewAction{ display:none;}
@media screen and (max-width: 768px) {
.DesktopVersionViewAction{ display:none;}
.mobileVersionViewAction{ display:table-cell !important;}
.orders-table thead th:nth-child(1){ width:100px !important ;}
.orders-table thead th:nth-child(2){ width:115px !important ;}
.orders-table thead th:nth-child(3){ width:130px !important ;}
.orders-table thead th:nth-child(4){ width:130px !important ;}
.orders-table thead th:nth-child(5){ width:130px !important ;}
}
</style>
</head>

<body class="MyOrderPage">
    <?php require_once 'files/headerSection.php'; ?>
    
	<?php
	//Pagination Code
	$searchTable = REPRESENTATIVE_ORDERS;
	$searchQuery = "`RepCustomerID` = '$AccessID' AND OrderStatus='pending' AND ";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";	
	$searchQuery = rtrim($searchQuery, " AND ");
	if(!empty($searchQuery))
		$query = "SELECT COUNT(RepOrdeID) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(RepOrdeID) as num FROM ".$searchTable;
		
	
	$total_pages = mysql_fetch_array(mysql_query($query));
		$total_pages = $total_pages[num];
	
	?> 
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content pl-60 pr-60 bx-shadow">
                <h1 class="page-heading mb-4">Sales Orders (<?=$total_pages;?>)</h1>

                <div class="table-responsive">
                    <table class="table sorting orders-table table-1">
                        <thead>
                            <tr>
                                 <th scope="col" class="mobileVersionViewAction">Action</th>
                                <th scope="col">Order#</th>
                                <th scope="col">Date</th>
                               
                                <th scope="col">Product</th>
                                <th scope="col" class="DesktopVersionViewAction">Action</th>
                               
                                
                            </tr>
                            <tbody>
                            <?php
$adjacents = 2;
$usersArr = getUserArr();
$prodsArr = getALLProductArr();	
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
		$sql = "SELECT * FROM ".$searchTable." WHERE ".$searchQuery." GROUP BY `RepOrdeID` ORDER BY RepOrdeID DESC LIMIT $start, $limit";
	else
		$sql = "SELECT * FROM ".$searchTable. " GROUP BY `RepOrdeID` ORDER BY RepOrdeID DESC LIMIT $start, $limit";
		
   
    $result = mysql_query($sql);
                    
	if ($page == 0) $page = 1;
	
	$prev = $page - 1;
	
	$next = $page + 1;
	
	$lastpage = ceil($total_pages/$limit);

	$lpm1 = $lastpage - 1;
	
	$pagination = "";
                                    
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
	
						$productsArray = unserialize($showData['RepCartData']);
						
								$productNameData = "";
								$IndexCount=1;
								$getRespnseCode= array();
								foreach($productsArray as $k=>$v){
									
											
									
									
								if(strpos($k, "_") !== false){
									$explodeParentID = explode("_",$k);
											$productNameData .= 	$IndexCount.") ".$prodsArr[$explodeParentID[0]]['Title']." ".$prodsArr[$v['ProductID']]['Title']."<br>";
										}else{
											$productNameData .= 	$IndexCount.") ".$prodsArr[$v['id']]['Title']."<br>";
										}
								$IndexCount++;
								}
							
							
	?>                   
                                <tr>
                                <td class="data-view mobileVersionViewAction">
                                <div style="display:inline-block; float:left;">
                                
                                	<a style="background: #0070c0;padding: 5px 18px;color: #fff;border-radius: 10px;text-decoration: none; display:block; margin-bottom:5px;display:block;" href="<?=SITEURL;?>sale-customer-order-details.php?order_id=<?=$showData['RepOrdeID'];?>" class="view">View</a>
								<a class="finish-btn" style="background: #3bc178 ;padding: 5px 18px;color: #fff; display:block;border-radius: 10px;text-decoration: none;" href="<?php echo SITEURL; ?>checkout.php?key=<?=$showData['RepKey'];?>">Purchase</a>
                                                                    
                             
                                </div>
                                </td>
                               
                                    <td class="data-id"><span><?=$showData['RepOrdeID'];?></span></td>
                                    <td><?=date('M d, Y',strtotime($showData['RepAddedDate']))."<br>".date('g:i A',strtotime($showData['RepAddedDate']));?></td>
                               
                                    <td><?=$productNameData;?></td>
                                     	<td class="data-view DesktopVersionViewAction">
                                        <a style="background: #0070c0;padding: 5px 18px;color: #fff;border-radius: 10px;text-decoration: none; display:inline-block; margin-bottom:5px;" href="<?=SITEURL;?>sale-customer-order-details.php?order_id=<?=$showData['RepOrdeID'];?>" class="view">View</a>
								 <a class="finish-btn" style="background: #3bc178 ;padding: 5px 18px;color: #fff; display:inline-block;border-radius: 10px;text-decoration: none;" href="<?php echo SITEURL; ?>checkout.php?key=<?=$showData['RepKey'];?>">Purchase</a></td>
                                     
                                    
                                   
                                </tr>
<?php
	}
} else {
	echo "<tr>
			<td colspan=\"4\">No order Found.</td>
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