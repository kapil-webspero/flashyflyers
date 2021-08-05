<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	
	if($_POST['totalvalue'] < $_POST['add_payment'] ){
			unset($_SESSION['ERROR']);
			$_SESSION['ERROR'] = "Please Add proper payment amount";
	} else {
		if( isset($_REQUEST['add_payment']) && !empty($_REQUEST['add_payment']) ){
			
			$deductedValue = $_POST['totalvalue'] - $_POST['add_payment'];
			$currentTimeIns = TIME();
			InsertRcrdsByData(DESIGN_TRANSACTION, "`designer_payment` = '".$_POST['add_payment']."',`designer_id`='".$_POST['designerid']."',`desigenr_date` = '$currentTimeIns',`designer_remain_payment` = '".$deductedValue."'");
			
			unset($_SESSION['SUCCESS']);
			$_SESSION['SUCCESS'] = "Transaction successfully updated.";
		}
	}
	
	$PageTitle = "Work Report";
	
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
	
	$prodType = getProdTypeParentArr(); 

?>

<!DOCTYPE html>
<html lang="en">

<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php include "includes/head.php"; ?>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <?php
	//Pagination Code
	
	$searchTable = ORDER;
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
	
	// PRODUCT TABLE QUERY
	$productQuery = "";
	$productSearchArr = array();
	$orderSearchArr = array();
	
		
	
	if(isset($_REQUEST['product_type']) && !empty($_REQUEST['product_type']) && $_REQUEST['product_type'] != "all") {
	
		$productQuery .= "ProductType = '".$_REQUEST['product_type']."' AND ";
		$searchURL .= "&product_type=".$_REQUEST['product_type'];
	} else {
		$searchURL .= "&product_type=all";
	}
	
	if(!empty( $_REQUEST['user_name'] ) && $_REQUEST['user_name'] != 'all' && !empty($_REQUEST['user_name']) ){
		$productQuery .= "AssignedTo = '".$_REQUEST['user_name']."' AND ";
		$searchURL .= "&AssignedTo=".$_REQUEST['user_name'];
	} else {
		$searchURL .= "&AssignedTo=all";
	}

	$searchQuery = rtrim($productQuery, " AND ");
	
	
	
	if(!empty($searchQuery))
		 $query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." WHERE AssignedTo > '0' AND ".$searchQuery."";
	else
		 $query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".$searchTable." WHERE AssignedTo > '0'";
	
	
	$total_pages = mysql_fetch_array(mysql_query($query));
	
	$total_pages = $total_pages['num'];
	?> 
	<?php if(isset($_SESSION['ERROR']) && !empty($_SESSION['ERROR'])) { ?>
    <div class="notification error">
        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
        <span>Error: <?=$_SESSION['ERROR'];?></span><button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['ERROR']); } ?>
	<?php  if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { ?>	
    <div class="notification success">
        <div class="d-flex"><i class="fas fa-check"></i></div>
        <span><?=$_SESSION['SUCCESS'];?></span>
        <button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['SUCCESS']); } ?>
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
				<?php  if(!empty($_REQUEST['user_name']) ) { 
					
						$name =  usernameList($_REQUEST['user_name']); 
				} else {
						$name = 'all';
				} ?>
                <h1 class="page-heading mb-4">Work report for <?php echo $name; ?> (<?=$total_pages;?>)</h1>
                 <?php if(!isset($_REQUEST['productId']) && empty($_REQUEST['productId'])) { ?>
                <form method="get" class="top-search-options mb-5">
                    <div class="d-flex justify-content-stretch flex-wrap">
                        
                        <div class="w-lg-20 w-100">
                            <a href="<?php echo currentUrl(true); ?>" class="btn-lg btn-block form-btn-grad">View all</a>
                        </div>
                        <div class="w-lg-20 w-100">                            
                            <select class="form-control" name="product_type">                           	
								<option value="all">product type</option>
								<?php 
								
								foreach($prodType as $prodTypes) { ?>
                                	<option value="<?=$prodTypes['id'];?>" <?php if(isset($_REQUEST['product_type']) && $_REQUEST['product_type']==$prodTypes['id']){ echo "selected"; } ?>><?=$prodTypes['name'];?></option>
                                <?php } ?>
                           	</select>                            
                        </div>
						<div class="w-lg-20 w-100">                            
                            <select class="form-control" name="user_name">                           	
								<option value="all">designer</option>
								<?php 
								$userLists = getUserArr(); 
								foreach($userLists as $userList) {
									if($userList['UserType'] == 'designer'){?>
										<option value="<?=$userList['UserID'];?>" <?php if(isset($_REQUEST['user_name']) && $_REQUEST['user_name']==$userList['UserID']){ echo "selected"; } ?>><?=$userList['FName'];?> <?=$userList['LName'];?></option>
									
									<?php } } ?>
                           	</select>                            
                        </div>
                        <div class="col pd-0">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
                <?php } 
				if(!isset($_REQUEST['key_order'])){
								$key_order  = "ID";	
							}
							if(!isset($_REQUEST['key_order_by'])){
								$key_order_by  = " DESC";	
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
                           
                                <th scope="col" class="header <?php if($key_order=="ID" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="ID" && $key_order_by=="ASC"){echo "headerSortDown";} ?>" onClick="window.location='work-report.php?<?php echo http_build_query($_GET); ?>&key_order=ID&key_order_by=<?php echo $ordeBy; ?>'">Order#</th>
                                <th scope="col">Designer</th>
                            
                              <?php /*?>  <th scope="col">Assigned</th>
                                <th scope="col">Finished</th><?php */?>
                                <th scope="col">Product</th>
                              <?php /*?>  <th scope="col">Options</th><?php */?>
                                <th scope="col">Cost</th>
                            </tr>
                      	</thead>
                        <tbody>
<?php
$adjacents = 2;
if($total_pages>0) {
	$targetpage = $searchURL;
	$limit = $searchResultsPerPage; 
	
	$page = !empty($_GET['page']) ? $_GET['page'] : 0;
	
	if($page) {
		$start = ($page - 1) * $limit;
	} else {
		$start = 0;							
	}
	if($page == -1){
		if(!empty($searchQuery))
			$sql = "SELECT DISTINCT `TransactionID` FROM ".$searchTable." WHERE AssignedTo > '0' AND ".$searchQuery."  order by `".$key_order."` ".$key_order_by ;
		else
			$sql = "SELECT DISTINCT `TransactionID` FROM ".$searchTable. " WHERE AssignedTo > '0' order by  `".$key_order."` ".$key_order_by;
    } else {
		if(!empty($searchQuery))
			$sql = "SELECT DISTINCT `TransactionID` FROM ".$searchTable." WHERE AssignedTo > '0' AND ".$searchQuery."   order by `".$key_order."` ".$key_order_by." LIMIT $start, $limit";
		else
			$sql = "SELECT DISTINCT `TransactionID` FROM ".$searchTable." WHERE AssignedTo > '0'  order by `".$key_order."` ".$key_order_by." LIMIT $start, $limit";	
	}
	

	
	
	
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
         
	$sr = 1;
	$usersArr = getUserArr();
	$prodsArr = getALLProductArr(); 
	$finalPayablePrice = array();
	while($showData1 = mysql_fetch_array($result))
	{
		
		$showData =GetSglRcrdOnCndi($searchTable,"TransactionID='".$showData1['TransactionID']."'");
		
		$timeID = $showData['TurnAroundTime'];
		if(!empty( $_REQUEST['user_name']) ){
			$finalAmount = transactionData($_REQUEST['user_name']);
		} else {
			$finalAmount = transactionData($showData['AssignedTo']);
		}	
		$ProductOrders =GetMltRcrdsOnCndi($searchTable,"TransactionID='".$showData1['TransactionID']."'");
		
	?>
      
		<tr>
			
            <td  class="data-id"><span><a style="text-decoration:none; color:#FFF;" href="<?php echo ADMINURL; ?>order-details.php?order_id=<?=$showData['TransactionID'];?>" target="_blank"><?=$showData['TransactionID'];?></a></span></td>
			<td> <?=$usersArr[$showData['AssignedTo']]['FName']." ".$usersArr[$showData['AssignedTo']]['LName']." ".$usersArr[$showData['AssignedTo']]['Email']; ?></td>
			
			<?php /*?><td><?=date('m/d/Y h:ia',$showData['AssignedOn']);?></td>
			<td><?=date('m/d/Y h:ia',$showData['finishDate']);?></td><?php */?>
			<td><?=getProductTypeArray($prodsArr[$showData['ProductID']]['ProductType']);?><br>
            <?php 
			$productName = "";
			$counterProduct = 0;
			$proudctBasePrice = 0;
			if(!empty($ProductOrders)){
				foreach($ProductOrders as $singleProducts){
					$counterProduct++;
					if($singleProducts['parent_product_id']>0){
						$productName .= 	 $counterProduct." )".$prodsArr[$singleProducts['parent_product_id']]['Title']." ".$prodsArr[$singleProducts['ProductID']]['Title']."<br>";
						$proudctBasePrice += $prodType[$prodsArr[$singleProducts['ProductID']]['parent_product_cat_id']]['design_labor_cost'];
					}else{
						$productName .=	$counterProduct." )".ucfirst(strtolower($prodsArr[$singleProducts['ProductID']]['Title']))."<br>";
						$proudctBasePrice += $prodType[$prodsArr[$singleProducts['ProductID']]['parent_product_cat_id']]['design_labor_cost'];
							
					}
				}	
			}
			echo $productName;
			 ?>
            </td>
			<?php /*?><td><?=ucfirst($showData['TypeBanner']);?>
           
            <br><?php if($timeID == 1) { echo '12 Hours Same-Day' ; } else if($timeID == 2) { echo "24 hours";} else if($timeID == 3) { echo "2-3 business days";} else if($timeID == 4) { echo "3-5 days";} ?></td><?php */?>
			<?php if($timeID=='1') { $timinggAround = $turnAround1; } elseif($timeID=='2') { $timingAround = $turnAround2; } elseif($timeID=='3') { $timingAround = $turnAround3; } else {$timingAround = "0"; } 
			//echo "parent_product_cat_id".$prodsArr[$showData['ProductID']['parent_product_cat_id']];
			
		
			$addExplode = explode(',',$showData['TypeBanner']);
			
			/*$option = array();
			foreach( $addExplode as $addEx ) {
				if($addEx == 'motion') {
					$option[] = 1;
				} elseif($addEx == 'animated' )	{
					$option[] = 2;
				} else {
					$option[] = 0;
				}
			}
			
			foreach($option as $op) {
				$OptionValues+= getOptionValue($op);
			}*/
				$finalPrice = $proudctBasePrice ;  
				$finalPayablePrice[] = $finalPrice;
			?>
			<td>$<?=number_format($finalPrice,2); ?></td>
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
	<?php  $totalAmountPayable = array_sum($finalPayablePrice); ?>
   <?php if($total_pages > 10 && $_REQUEST['page'] != -1) { ?>
    <nav class="mt-5">
		<a href="<?=ADMINURL;?>work-report.php?page=-1" class="pagination-work" aria-label="View More">View More</a>
	</nav>
	<?php } ?>
	<?php if( (!empty($_REQUEST['user_name']) && $_REQUEST['user_name'] != 'all') )	{ 
			
			$designerId = $_REQUEST['user_name'];
            $paidResultsQuery = "SELECT sum(`designer_payment`) as `totalAmount` FROM ".DESIGN_TRANSACTION." WHERE `designer_id` = ".$designerId;
            $paidResults = ExecCustomQuery($paidResultsQuery);
            $totalAmountForDesigner = $paidResults[0]['totalAmount'];
			
				
			$paidResultsForCurentMonthQuery = "SELECT sum(`designer_payment`) as `totalAmount` FROM ".DESIGN_TRANSACTION." WHERE `designer_id` = ".$designerId." AND MONTH( from_unixtime(`desigenr_date`)) = MONTH(CURRENT_DATE()) AND YEAR(from_unixtime(`desigenr_date`)) = YEAR(CURRENT_DATE())";
            $paidForCurentMonthResults = ExecCustomQuery($paidResultsForCurentMonthQuery);
            $totalAmountForDesignerForCurentMonth = $paidForCurentMonthResults[0]['totalAmount'];
	?>
		<div class="row user-stats mt-3 pl-md-3 pr-md-3 work-page">
			<div class="col-lg-12" style="text-align:center;border-bottom: 1px solid #d9d9d9;padding-bottom: 22px;margin-bottom: 28px;">
                <span class="balance">Balance:</span>
                <span class="amount">  
				<?php if ( $finalAmount != '' ) { 
						$amountRemain = $totalAmountPayable - $totalAmountForDesigner;
                    } else { 
                        $amountRemain = $totalAmountPayable;
                    }
                    if(empty($total_pages) ){ 
						
                        $amountRemain = '0.00';
                    }
                    echo "$".number_format($amountRemain,2); ?>
                </span>
			</div>
            <div class="col-lg-2 col-sm-6 mb-4">
				<p>Balance:</p>
				<h3>$<?=number_format($amountRemain,2);?></h3>
				
			</div>
           
		    <div class="col-lg-3 col-sm-6 mb-4 pl-22">
			    <p>Paid to Date:</p>
			    <h3>$<?php if($amountRemain == 0) {echo '0.00';
				            }else{
				            echo number_format($totalAmountForDesigner,2);
				            }?></h3>
			    <a href="designer-transaction.php?user_id=<?php echo $_REQUEST['user_name']; ?>&action=total" class="btn btn-blue">view report</a>
		    </div>
			<div class="col-lg-3 col-sm-6 mb-4 ml-45">
				<p>Paid this Month:</p>
				<h3>$<?php if($amountRemain == 0) {
				                echo '0.00';
				            }else{
				            echo number_format($totalAmountForDesignerForCurentMonth,2);
				            }?></h3>
				<a href="designer-transaction.php?user_id=<?php echo $_REQUEST['user_name']; ?>&month=yes&action=list" class="btn btn-blue">view report</a>
			</div>
			<div class="col-lg-3 col-sm-6 mb-4 pd-14">
				<form name= "remain_payment" action=""	 method="post" />			
				<p>Make a payment:</p>
					<input type="text" placeholder="Amounts" name="add_payment" class="form-control mb-4" id="add_payment" onblur='paymentFormat(this,event)' onkeypress='validate(this,event)' value=""  >
					<input type="hidden" name = "designerid" value= "<?php echo $_REQUEST['user_name']; ?>">
					<input type="hidden" name = "totalvalue" value= "<?php echo $amountRemain; ?>">
					<button type="submit" name="pay_value" class="btn-block form-btn-grad">Pay</button>
 				</form>
			</div>
        </div>
	<?php } ?>
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
function validate(self,evt) {
	
  var theEvent = evt || window.event;

  // Handle paste
  if (theEvent.type === 'paste') {
      key = event.clipboardData.getData('text/plain');
  } else {
  // Handle key press
      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
  }
  var regex = /[0-9]|\./;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
	
  }

}	
function paymentFormat(self,evt) {
	var amount = $(self).val();
	
	if(amount != undefined && amount != '' && amount != null){
		amount = parseFloat(amount).toFixed(2);
	}
	$(self).val(amount);
}
        $(document).ready(function () {
			$('.close-ntf').click(function() {
				$(this).parent().fadeOut(300, function() {
					$(this).hide();
				});
			});
			setTimeout(function(){ $('.close-ntf').click(); }, 12000);
		});
    </script>
</body>

</html>