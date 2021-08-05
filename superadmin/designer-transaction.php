<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Work Report";

	$currentUrl = getCurPageURL();
	$currentUrl = explode('&delete_transaction_id',$currentUrl);
	$currentUrl = $currentUrl[0];

	if(isset($_REQUEST['delete_transaction_id']) && !empty($_REQUEST['delete_transaction_id'])) {
		$deleteTransactionID = intval($_REQUEST['delete_transaction_id']);
		if(!empty($deleteTransactionID) && $deleteTransactionID > 0){
			$designerTransactionData = ExecCustomQuery("SELECT * FROM ".DESIGN_TRANSACTION." WHERE `ID` = ".$deleteTransactionID);
			if(!empty($designerTransactionData[0]['designer_payment'])){
				$designerPayment = $designerTransactionData[0]['designer_payment'];
				$designerId = $designerTransactionData[0]['designer_id'];
				ExecCustomQuery("UPDATE `tbl_designer_transactions` SET `designer_remain_payment` = (`designer_remain_payment` + ".$designerPayment.") WHERE `ID` > ".$deleteTransactionID." AND `designer_id` = ".$designerId);
			}
			DltSglRcrd(DESIGN_TRANSACTION, "`ID` = '$deleteTransactionID'");
		}
		$_SESSION['SUCCESS'] = "Transaction deleted successfully.";
		echo "<script> window.location.href = '".$currentUrl."';</script>";
		exit();
	}
	
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
	
	$searchTable = DESIGN_TRANSACTION;
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
	
	
	if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'total'  ) { 
		$productQuery .= "`designer_id` = ".$_REQUEST['user_id']." AND ";
		$searchURL .= "&designer_id=".$_REQUEST['user_id'];
	}
	if( isset($_REQUEST['month']) && $_REQUEST['month'] == 'yes' ){
		$productQuery .= "`designer_id` = ".$_REQUEST['user_id']." AND MONTH( from_unixtime(`desigenr_date`)) = MONTH(CURRENT_DATE()) AND YEAR(from_unixtime(`desigenr_date`)) = YEAR(CURRENT_DATE()) AND ";
	}
	
    if( $_REQUEST['start_date'] != ''  ){
		$productQuery .= "`desigenr_date` >= ".strtotime($_REQUEST['start_date'])." AND ";
	}
	
	if(  $_REQUEST['end_date'] != ''  ){
		$productQuery .= "`desigenr_date` <= ".strtotime($_REQUEST['end_date'])." AND ";
	}

	$searchQuery = rtrim($productQuery, " AND ");
	
	
	
	if(!empty($searchQuery))
		$query = "SELECT COUNT(*) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(*) as num FROM ".$searchTable;
	
	
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages['num'];
	?>   

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

    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
				<?php  if(!empty($_REQUEST['user_id']) ) { 
					
						$name =  usernameList($_REQUEST['user_id']); 
				}  ?>
                <h1 class="page-heading mb-4">Work report for <?php echo $name; ?> (<?=$total_pages;?>)</h1>
                 <?php if(!isset($_REQUEST['productId']) && empty($_REQUEST['productId'])) { ?>
                <form method="get" class="top-search-options mb-5">
                    <div class="d-flex justify-content-stretch flex-wrap">
                        
                        <div class="w-lg-20 w-100">
                            <a href="<?php echo currentUrl(true); ?>" class="btn-lg btn-block form-btn-grad">View all</a>
                        </div>
                        <div class="w-lg-20 w-100">
                            <input type="text" class="form-control date-field"  data-toggle="datepicker" placeholder="Start date" name="start_date" value="<?php if(isset($_REQUEST['start_date'])){ echo $_REQUEST['start_date']; } ?>">
                        </div>
						<div class="w-lg-20 w-100">
                            <input type="text" class="form-control date-field"  data-toggle="datepicker" placeholder="End date" name="end_date" value="<?php if(isset($_REQUEST['end_date'])){ echo $_REQUEST['end_date']; } ?>">
                        </div>
                        <input type="hidden" name="user_id" value="<?php echo $_REQUEST['user_id']; ?>" />
                        <div class="col pd-0">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table work-report-history table-1">
                        <thead>
                            <tr>
                                <th scope="col">#ID</th>
                                <th scope="col">Designer</th>
                                <th scope="col">Date</th>
                                <th scope="col">By</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Balance</th>
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
	if($page == -1){
		if(!empty($searchQuery))
			$sql = "SELECT * FROM ".$searchTable." WHERE ".$searchQuery." ORDER BY desigenr_date DESC";
		else
			$sql = "SELECT * FROM ".$searchTable." ORDER BY desigenr_date DESC";
	} else {
		if(!empty($searchQuery))
			$sql = "SELECT * FROM ".$searchTable." WHERE ".$searchQuery." ORDER BY desigenr_date DESC LIMIT $start, $limit" ;
		else
			$sql = "SELECT * FROM ".$searchTable. " ORDER BY desigenr_date DESC LIMIT $start, $limit";
	}
	$result = mysql_query($sql);
          // echo $sql;          
	if ($page == 0) $page = 1;
	
	$prev = $page - 1;
	
	$next = $page + 1;
	
	$lastpage = ceil($total_pages/$limit);

	$lpm1 = $lastpage - 1;
	
	$pagination = "";
                                    
    
	$sr = 1;
	$usersArr = getUserArr();
	$prodsArr = getProductArr(); 
	
	$i=1;
	
	while($allCurrentD = mysql_fetch_array($result)){ ?>
	
		<tr>
			<td class="data-id"><span><?=$i;?></span></td>
			<td> <?=$usersArr[$allCurrentD['designer_id']]['FName']." ".$usersArr[$allCurrentD['designer_id']]['LName']." ".$usersArr[$allCurrentD['designer_id']]['Email']; ?></td>
				<td><?php echo  date('l', $allCurrentD['desigenr_date']); ?><br><?=date('m/d/Y h:ia',$allCurrentD['desigenr_date']);?></td>
				<td><?php echo $AccessUData['FName']." ".substr($AccessUData['LName'],0,1).'.'; ?></td>
			<td>$<?=number_format( $allCurrentD['designer_payment'], 2);?></td>
			<td>$<?=number_format($allCurrentD['designer_remain_payment'], 2);?></td>
			<td class="data-delete"><a href="<?=$currentUrl;?>&delete_transaction_id=<?=$allCurrentD['ID']?>"><i class="fas fa-trash-alt"></i></a></td>
		</tr>
		
	<?php	
	$i++;}
} else {
	echo "<tr>
			<td colspan=\"7\">No Search Result Found.</td>
		</tr>";
}
?>   
                            </tbody>
                    	</table>
                </div>

<?php if($total_pages > 10 && $_REQUEST['page'] != -1) { ?>
    <nav class="mt-5">
	    <a href="<?=ADMINURL;?>designer-transaction.php?page=-1&user_id=<?=$_REQUEST['user_id'];?>" class="pagination-work" aria-label="View More">View More</a>
	</nav>
<?php } ?>
                <?php if( $_REQUEST['user_id'] || $_REQUEST['page'] == -1 ) { ?>
				<div class="row user-stats mt-3 pl-md-5 pr-md-5">
				<div class="col-lg-12" style="text-align:center">
                    <span class="balance">Balance:</span>
                    <span class="amount">  $<?php 
						if(empty($total_pages) )
							echo "0.00"; 
						else	
							echo number_format( transactionData($_REQUEST['user_id']), 2); ?></span>
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