<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
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
	$CurrentTime =   time(); //1 week ago
	$Last7Days =   strtotime("-1 week"); //1 week ago
		// FETCH ORDER STATS
		$currentYearTimestamp = strtotime('first day of January '.date('Y'));
		if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
			$sfpid1 = intval($_REQUEST['productId']);
			$cndy1 = "ProductID = '".$sfpid1."'";
			$cndy2 = "ProductID = '".$sfpid1."'";
			$cndy3 = "ProductID = '".$sfpid1."' and OrderDate<=$CurrentTime AND OrderDate<=$Last7Days"; 
			$cndy4 = "OrderDate>=$currentYearTimestamp AND ProductID = '".$sfpid1."'";
		} else {
			$cndy1 = "Id!=''";
			$cndy2 = "Id!=''";
			$cndy3 = "OrderDate<=$CurrentTime AND OrderDate<=$Last7Days"; 
			$cndy4 = "OrderDate>=$currentYearTimestamp";
		}
		$OrderStatsTotal = GetMltRcrdsOnCndi(ORDER, $cndy1);
		
		//$newOrderStats = GetNumOfRcrdsOnCndi(ORDER, $cndy1);
		//$inProgressStats = GetNumOfRcrdsOnCndi(ORDER, $cndy2); 
		$completedOrderData = GetMltRcrdsOnCndi(ORDER, $cndy3); 
		$yearTotalOrderStatsData = GetMltRcrdsOnCndi(ORDER, $cndy4); 
		$newOrderStats = $completedOrderStats = $inProgressStats = $yearTotalOrderStats =  0  ;
		
	
	
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
		foreach($yearTotalOrderStatsData as $showData){
		$AssignTo = $showData['AssignedTo'];
							$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$showData['TransactionID']."'", "ResponseState", "ASC");
							$getRespnseCode = array();
								foreach($OrderData as $k=>$v){
										$getRespnseCode[$v['ResponseState']] = $v['ResponseState'];		
								}
							$OrderStatus =$showData['OrderStatus'];
							
							if($AssignTo==0) {
								
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(1,$getRespnseCode)  && !in_array(2,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
							$yearTotalOrderStats++;
							}
		}
		foreach($completedOrderData as $showData){
		$AssignTo = $showData['AssignedTo'];
							$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$showData['TransactionID']."'", "ResponseState", "ASC");
							$getRespnseCode = array();
								foreach($OrderData as $k=>$v){
										$getRespnseCode[$v['ResponseState']] = $v['ResponseState'];		
								}
							$OrderStatus =$showData['OrderStatus'];
							
							if($AssignTo==0) {
								
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(1,$getRespnseCode)  && !in_array(2,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
							$completedOrderStats++;
							}
		}
		
	?>
    
	<?php
	//Pagination Code
	$prodType = getProdTypeArr();
	$getProdSizes = getProdSizeArr();
	
	$usersArr = getUserArr();
	$prodsArr = getProductArr();	
	$searchTable = ORDER;
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
	
	
	if(isset($_REQUEST['order_number']) && !empty($_REQUEST['order_number'])) {
		$searchQuery .= "id = '".$_REQUEST['order_number']."' AND ";
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
	
	
	
	if(isset($_REQUEST['productId']) && !empty($_REQUEST['productId'])) {
		$sfpid = intval($_REQUEST['productId']);
		$searchQuery .= "`ProductID` = '".$sfpid."'";
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
		$productQuery .= "`ProductType` = '".$_REQUEST['product_type']."'";
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
        $searchQuery .= "Status = '0' AND ";
        $searchURL .= "&is_new_order=" . $isNewOrder;
    }
	
	$searchQuery = rtrim($searchQuery, " AND ");

	if(!empty($searchQuery))
		$query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable;
	
	$total_pages = mysql_fetch_array(mysql_query($query));
    $total_pages = (!empty($total_pages['num'])) ?  $total_pages['num'] : '0';

	?> 
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content pl-60 pr-60 bx-shadow">
                <section class="orders-top mb-4">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <div class="totals-box red">
                                <h1><?=$newOrderStats;?></h1>
                                <p>new orders</p>

                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <div class="totals-box cyan">
                                <h1><?=$inProgressStats;?></h1>
                                <p>in progress</p>

                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <div class="totals-box purple">
                                <h1><?=$completedOrderStats;?></h1>
                                <p>completed this week</p>

                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-4">
                            <div class="totals-box blue">
                                <h1><?=$yearTotalOrderStats;?></h1>
                                <p>this year</p>

                            </div>
                        </div>
                    </div>
                </section>
                <h1 class="page-heading mb-4">Orders (<?=$total_pages;?>)</h1>
                <?php if(!isset($_REQUEST['productId']) && empty($_REQUEST['productId'])) { ?>
                <form action="" class="top-search-options mb-5">
                    <div class="d-flex justify-content-stretch flex-wrap">
                        <div class="w-lg-20 w-100">
                            <?php 
                                $currentUrl = currentUrl(true);
                                if(!empty($isNewOrder) && $isNewOrder == 1){
                                    $currentUrl .='?is_new_order='.$isNewOrder;
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
                        	<input type="text" name="product_name" class="form-control" placeholder="product name" value="<?php if(isset($_REQUEST['product_name'])){ echo $_REQUEST['product_name']; } ?>">
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
                        <?php /*?><div class="w-lg-20 w-100">
                            <select name="status" id="" class="form-control">
                                <option value="all">status</option>
                                <option value="0" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']==0){ echo "selected"; } ?>>Not Assigned</option>
                                <option value="1" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']==1){ echo "selected"; } ?>>In Progress</option>
                                <option value="2" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']==2){ echo "selected"; } ?>>Complete</option>
                            </select>
                        </div><?php */?>
                        <div class="col-lg-5 ml-auto">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
				<?php } ?>
                <div class="table-responsive">
                    <table class="table sorting orders-table table-1">
                        <thead>
                            <tr>
                                <th scope="col">Order#</th>
                                <th scope="col">Order date</th>
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
		$sql = "SELECT * FROM ".$searchTable." WHERE ".$searchQuery." GROUP BY `TransactionID` ORDER BY `TransactionID` DESC LIMIT $start, $limit";
	else
		$sql = "SELECT * FROM ".$searchTable. " GROUP BY `TransactionID` ORDER BY `TransactionID` DESC LIMIT $start, $limit";
        
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
	{ 
	?>                   
                                <tr>
                                    <td class="data-id"><span><?=$showData['Id'];?></span></td>
                                    <td><?=date('n/d/Y h:i A',$showData['OrderDate']);?></td>
                                    <td><?php if(!empty($showData['CustomerID'])) { echo $usersArr[$showData['CustomerID']]['FName']." ".$usersArr[$showData['CustomerID']]['LName']." ".$usersArr[$showData['CustomerID']]['Email']; } else { echo "Not Available"; }?>
                                    </td>
                                    <td><?php if(!empty($showData['AssignedTo'])) { echo $usersArr[$showData['AssignedTo']]['FName']." ".$usersArr[$showData['AssignedTo']]['LName']." <br/>".date('n/d/Y h:i A',$showData['AssignedOn']); } else { echo "Not Assigned"; } ?>
                                    </td>
                                    <td><a target="_blank" href="https://www.flashyflyers.com/flyer-details.php?productId=<?=$showData['ProductID'];?>&&title=<?=$prodsArr[$showData['ProductID']]['Title'];?>"><?=$prodsArr[$showData['ProductID']]['Title'];?>...</a></td>
                                     <td class="data-progress"><?php 
							$AssignTo = $showData['AssignedTo'];
							$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$showData['TransactionID']."'", "ResponseState", "ASC");
							$getRespnseCode = array();
								foreach($OrderData as $k=>$v){
										$getRespnseCode[$v['ResponseState']] = $v['ResponseState'];		
								}
							$OrderStatus =$showData['OrderStatus'];
							if($AssignTo==0) {
								echo "New";
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(3,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(2,$getRespnseCode)) {
								echo "Submitted to customer";
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(1,$getRespnseCode)  && !in_array(2,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
								echo "Approved & done";
							}else if($AssignTo>0 && $OrderStatus==1 && in_array(2,$getRespnseCode) && !in_array(1,$getRespnseCode) && !in_array(3,$getRespnseCode)) {
								echo "Rivisions";
							}else if($AssignTo>0 && $OrderStatus==1 ) {
								echo "In progress";
							}
							
                           
                            ?></td>
                                   	<td class="data-view"><?php if(empty($showData['AssignedTo'])) { ?><a data-id="<?=$showData['TransactionID'];?>" data-value="<?=$prodsArr[$showData['ProductID']]['Title'];?>..." class="assign assignMBtn" id="">assign</a><?php } ?><a href="?orderUID=<?=$showData['TransactionID'];?>" class="view">view</a></td>
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
		$("#projectName").html("("+$(this).data('value')+")");
		$("#assignBox").modal('show');
	});
	</script>
</body>

</html>