<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	
	$PageTitle = "Transaction";
	
	if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
		$deleteID = intval($_REQUEST['deleteID']);
		DltSglRcrd(USERS, "`UserID` = '$deleteID'");
		echo "<script> window.location.href = '".ADMINURL."users.php';</script>";
		exit();
	}
	if(isset($_REQUEST['viewID']) && !empty($_REQUEST['viewID'])) {
		$viewID = intval($_REQUEST['viewID']);
		$_SESSION['TRANSACTIONID'] = $viewID;
		echo "<script> window.location.href = '".ADMINURL."transaction-details.php?transaction_id=".$viewID."';</script>";
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
    <?php
	//Pagination Code
	
	$searchTable = TRANSACTION;
	if(isset($_REQUEST['product_type']) || isset($_REQUEST['productId'])){
		$searchQuery .= "PaymentStatus !='Sales pending payment' AND ";	
	}else{
		$searchQuery .= "PaymentStatus !='Sales pending payment'";
	}	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
	
	// PRODUCT TABLE QUERY
	$productQuery = "";
	$productSearchArr = array();
	$orderSearchArr = array();
	
	if(!empty($_REQUEST['product_name']) && !empty($_REQUEST['product_name'])) {
		$productQuery .= "(Title LIKE '%".$_REQUEST['product_name']."%' OR Subtitle LIKE '%".$_REQUEST['product_name']."%') AND ";
		$searchURL .= "&product_name=".$_REQUEST['product_name'];
	}	
	
	if(isset($_REQUEST['product_type']) && !empty($_REQUEST['product_type']) && $_REQUEST['product_type'] != "all") {
	
		$productQuery .= "parent_product_cat_id = '".$_REQUEST['product_type']."' AND ";
		$searchURL .= "&product_type=".$_REQUEST['product_type'];
	} else {
		$searchURL .= "&product_type=all";
	}	
	
	if(!empty($productQuery)) {
		
		$productQuery = rtrim($productQuery, " AND");
		$getProducts = GetMltRcrdsOnCndi(PRODUCT, $productQuery);
		foreach($getProducts as $searchProduct) {
			$productSearchArr[] = $searchProduct['id'];	
		}
		
		$pdArr = implode("," , $productSearchArr);
		
		$ordersProductQuery = "ProductID IN ('$pdArr') ";		
		$getOrdersProduct = GetMltRcrdsOnCndi(ORDER, $ordersProductQuery);
		foreach($getOrdersProduct as $searchProduct) {
			$orderSearchArr[] = $searchProduct['TransactionID'];	
		}
		
		$idsProd = implode("," , $orderSearchArr);
		$searchQuery .= "id IN ($idsProd) AND ";
		
		//$execProductQuery = true;
		
	}
	
	
	// ORDER QUERY
	$orderQuery = "";
	$orderSearchArr = array();
	
	if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
		$sfpid = intval($_REQUEST['productId']);
		$orderQuery .= "`ProductID` = '".$sfpid."'";
		$searchURL .= "&productId=".$sfpid;
	}
	if(isset($_REQUEST['order_date']) && !empty($_REQUEST['order_date']) && $_REQUEST['order_date'] != "all") {
		$currentTime = strtotime($_REQUEST['order_date']);
		$afterTime = strtotime('+1 day', $currentTime);
		
		$orderQuery .= "OrderDate >= '".$currentTime."' AND OrderDate <= '".$afterTime."'";
		$searchURL .= "&order_date=".$_REQUEST['order_date'];
	} else {
		$searchURL .= "&order_date=all";
	}
	
	
	if(!empty($orderQuery)) {
		$orderQuery = rtrim($orderQuery, " AND");
		//echo $orderQuery;
		$getOrders = GetMltRcrdsOnCndi(ORDER, $orderQuery);
		foreach($getOrders as $searchProduct) {
			$orderSearchArr[] = $searchProduct['TransactionID'];	
		}		
		$idsProd1 = implode("," , $orderSearchArr);
		$searchQuery .= "id IN ($idsProd1) AND ";
	}
	
	/*else {
		if($execProductQuery==true) {
			$idsProd = implode("," , $orderSearchArr);
			$searchQuery .= "id IN ($idsProd) AND ";
		}
	}*/
		
	
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
		$searchQuery .= "UserID IN ($idsProd) AND ";
	}
	
	// TRANSCTION STATUS
	if(isset($_REQUEST['product_status']) && !empty($_REQUEST['product_status']) && $_REQUEST['product_status'] != "all") {
		if($_REQUEST['product_status']=="pending") {
			$searchQuery .= "Status =0 AND ";		
		}
		else {
			$searchQuery .= "PaymentStatus ='".$_REQUEST['product_status']."' AND ";			
		}
		$searchURL .= "&product_status=".$_REQUEST['product_status'];
	}
	
	if(isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id']) ) {
		$searchQuery .= "Id = '".$_REQUEST['order_id']."' AND ";
		$searchURL .= "&order_id=".$_REQUEST['order_id'];
	} 
	
	$searchQuery = rtrim($searchQuery, " AND ");
	
	
	if(!empty($searchQuery))
		$query = "SELECT COUNT(*) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(*) as num FROM ".$searchTable;
	
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages['num'];
	?>   
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
                <h1 class="page-heading mb-4">Transactions (<?=$total_pages;?>)</h1>
                 <?php if(!isset($_REQUEST['productId']) && empty($_REQUEST['productId'])) { ?>
                <form method="get" class="top-search-options mb-5">
                    <div class="d-flex justify-content-stretch flex-wrap">
                        
                        <div class="w-lg-20 w-100">
                            <a href="<?php echo currentUrl(true); ?>" class="btn-lg btn-block form-btn-grad">View all</a>
                        </div>
                        
                        <div class="w-lg-20 w-100">
                            <input type="text" class="form-control" placeholder="order #" name="order_id" value="<?php if(isset($_REQUEST['order_id'])){ echo $_REQUEST['order_id']; } ?>">
                        </div>
                        
                        <div class="w-lg-20 w-100">
                            <input type="text" class="form-control date-field" readonly data-toggle="datepicker" placeholder="date" name="order_date" value="<?php if(isset($_REQUEST['order_date'])){ echo $_REQUEST['order_date']; } ?>">
                        </div>
                        
                        <div class="w-lg-20 w-100">
                            <input type="email" class="form-control" placeholder="customer email" name="customer_email" value="<?php if(isset($_REQUEST['customer_email'])){ echo $_REQUEST['customer_email']; } ?>">
                        </div>
                        
                        <div class="w-lg-20 w-100">
                            <input type="text" class="form-control" placeholder="customer names" name="customer_name" value="<?php if(isset($_REQUEST['customer_name'])){ echo $_REQUEST['customer_name']; } ?>">
                        </div>
                        
                        <div class="w-lg-20 w-100">
                        	<input type="text" class="form-control" placeholder="Product name" name="product_name" value="<?php if(isset($_REQUEST['product_name'])){ echo $_REQUEST['product_name']; } ?>">
                        </div>
                        
                        <div class="w-lg-20 w-100">                            
                            <select class="form-control" name="product_type">                           	<option value="all">product type</option>
								<?php 
								$prodType = getProdTypeParentArr(); 
								foreach($prodType as $prodTypes) { ?>
                                	<option value="<?=$prodTypes['id'];?>" <?php if(isset($_REQUEST['product_type']) && $_REQUEST['product_type']==$prodTypes['id']){ echo "selected"; } ?>><?=$prodTypes['name'];?></option>
                                <?php } ?>
                           	</select>                            
                        </div>
                        <div class="w-lg-20 w-100">
                            
                            <select class="form-control" name="product_status"> 
								<option value="all">status</option>
                              <?php /*?>  <option value="pending" <?php if(isset($_REQUEST['product_status']) && $_REQUEST['product_status']=="pending"){ echo "selected"; } ?>>Pending</option><?php */?>
                                <option value="failed" <?php if(isset($_REQUEST['product_status']) && $_REQUEST['product_status']=="failed"){ echo "selected"; } ?>>Failed</option>
                                <option value="success" <?php if(isset($_REQUEST['product_status']) && $_REQUEST['product_status']=="success"){ echo "selected"; } ?>>Success</option>
                            </select>
                            
                        </div>
                        <div class="col pd-0">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
                <?php } ?>
                <?php 
				
						 	if(!isset($_REQUEST['key_order'])){
								$key_order  = "id";	
							}
							if(!isset($_REQUEST['key_order_by'])){
								$key_order_by  = "DESC";	
							}
							if(isset($_REQUEST['key_order'])){
								if($_REQUEST['key_order']=="Amount"){
									$key_order  = 'CAST('.$_REQUEST['key_order'].' as decimal)';		
								}else{
									$key_order  = $_REQUEST['key_order'];
								}
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
                    <table class="table transactions-table1 sorting table-1">
                        <thead>
                            <tr>
                              <th scope="col" onClick="window.location='transactions.php?<?php echo http_build_query($_GET); ?>&key_order=id&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="id" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="id" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">#Order</th>
                              <th scope="col" onClick="window.location='transactions.php?<?php echo http_build_query($_GET); ?>&key_order=Createdon&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="Createdon" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="Createdon" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Order Date</th>
                            
                                <th scope="col">Customer</th>
                             <th scope="col" onClick="window.location='transactions.php?<?php echo http_build_query($_GET); ?>&key_order=Amount&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="Amount" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="Amount" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Amount</th>
                            
                                <th scope="col">Product</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                      	</thead>
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
	
	if(!empty($searchQuery))
		$sql = "SELECT * FROM ".$searchTable." WHERE ".$searchQuery." ORDER BY ".$key_order." ".$key_order_by." LIMIT $start, $limit";
	else
		$sql = "SELECT * FROM ".$searchTable. " ORDER BY ".$key_order." ".$key_order_by."  LIMIT $start, $limit";
      
	 
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
	$usersArr = getUserArr();
	$prodsArr = getProductArr(); 
	while($showData = mysql_fetch_array($result))
	{
	?>
                                <tr>
                                    <td class="data-id"><span><?=$showData['id'];?></span></td>
                                    <td><?=date('M d, Y g:i A',$showData['Createdon']);?></td>
                                    <td> <?=$usersArr[$showData['UserID']]['FName']." ".$usersArr[$showData['UserID']]['LName']." ".$usersArr[$showData['UserID']]['Email']; ?>
                                    </td>
                                    <td class="amount">$<?=$showData['Amount'];?></td>
                                    <td>
									
									<?php
									
									 $productName = $prodsArr[GetSglRcrdOnCndiWthOdr(ORDER,"TransactionID = '".$showData['id']."'", "id", "ASC")['ProductID']]['Title'];
									if($productName!=""){
										echo $productName;	
									}else{
										echo "
Change request for ".$prodsArr[GetSglRcrdOnCndiWthOdr(CHANGE_REQ,"transctionIDRequest = '".$showData['id']."'", "id", "ASC")['ProductID']]['Title'];	
									}
									
									
									
									
									//prodsArr[GetSglRcrdOnCndiWthOdr(ORDER,"TransactionID = '".$showData['id']."'", "id", "ASC")['ProductID']]['Title'];?>...
                                    </td>
                                    <td class="data-success"><?=$showData['PaymentStatus'];?></td>
                                    <td class="data-view"><a href="?viewID=<?=$showData['id'];?>" class="view">view</a></td>
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

    <?php include "includes/footer.php"; ?>


    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>