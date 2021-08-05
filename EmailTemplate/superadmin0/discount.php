<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Discount Codes";
	if(isset($_REQUEST['create_discount'])){
				$checkCodequery = "SELECT COUNT(*) as num FROM ".DISCOUNT." WHERE DiscountName = '".$_REQUEST['DiscountName']."' and finishUse='No'";
			   $checkCodeRs = mysql_fetch_array(mysql_query($checkCodequery));
				if(strtotime($_REQUEST['end_date'])>strtotime($_REQUEST['start_date'])){
				if($checkCodeRs['num']>0){
						
						$_SESSION['ERROR'] = "Sorry, discount name already exists.Please choose another discount name.";	
				}else{
				
				InsertRcrdsByData(DISCOUNT,"`DiscountName` = '".$_REQUEST['DiscountName']."', `Type` = '".$_REQUEST['Type']."', `Value` = '".$_REQUEST['amount_off']."', `StartDate` = '".strtotime($_REQUEST['start_date'])."', `EndDate` = '".strtotime($_REQUEST['end_date'])."', `Status` = '1', `Createdon` = '".strtotime(date("m/d/y"))."', `NumberOfUses` = '".$_REQUEST['number_of_uses']."', `CustomerID` = '".$_REQUEST['customers']."'");
				$_SESSION['SUCCESS'] = "Discount code has been successfully added.";	
				}
				}else{
					$_SESSION['ERROR'] = "Sorry, Please choose end date large of starting date.";		
				}
	}
	if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
		$deleteID = intval($_REQUEST['deleteID']);
		DltSglRcrd(DISCOUNT, "`Id` = '$deleteID'");
		echo "<script> window.location.href = '".ADMINURL."discount.php';</script>";
		exit();
	}
	
	
	
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
<style>
.select2-container--default .select2-selection--single .select2-selection__arrow {
	top: 12px !important;
}
.select2-container--default .select2-selection--single {
	border: 2px solid #d4d1d1 !important;
	border-radius: 18px !important;
	padding: 10px !important;
	height: auto !important;
}
</style>
</head>

<body>
    <?php include "includes/header.php"; ?>
	<?php
	//Pagination Code
	
	$searchTable = DISCOUNT;
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
		
	$searchQuery .= "finishUse = 'No' AND ";
	if(!empty($_REQUEST['name']) && !empty($_REQUEST['name'])) {
		$searchQuery .= "(DiscountName LIKE '%".$_REQUEST['name']."%') AND ";
		$searchURL .= "&name=".$_REQUEST['name'];
	}
	
	if(isset($_REQUEST['create_date']) && !empty($_REQUEST['create_date'])) {
		$dateCreated = strtotime($_REQUEST['create_date']);
		$searchQuery .= "Createdon = '".$dateCreated."' AND ";
		$searchURL .= "&create_date=".$_REQUEST['create_date'];
	}
	
	
	
	$searchQuery = rtrim($searchQuery, " AND ");
	
	if(!empty($searchQuery))
		$query = "SELECT COUNT(*) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(*) as num FROM ".$searchTable;
	
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];
	?>   
    <?php if(isset($_SESSION['ERROR']) && !empty($_SESSION['ERROR'])) { ?>
    <div class="notification error">
        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
        <span>Error: <?=$_SESSION['ERROR'];?></span><button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['ERROR']); } if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { ?>	
    <div class="notification success div_sucess">
        <div class="d-flex"><i class="fas fa-check"></i></div>
        <span><?=$_SESSION['SUCCESS'];?></span>
        <button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php } ?>
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
            <div class="page-head mb-4">
                    <h1 class="page-heading">Discount Codes</h1>
                                    </div>
                                 
            
           
                
<h2 class="blue"> Create Discount</h2>
                
<form method="post" class="create-discount brd-bottom">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Discount name</label>
                            <input name="DiscountName" value="<?php echo (!isset($_SESSION['SUCCESS']))?  $_REQUEST['DiscountName']:""; ?>"  class="form-control" placeholder="Please enter discount name" required type="text">
                        </div>
                        <div class="col-md-6">
                            <label>Discount type</label>
                         <select class="form-control" required name="Type">
                                <option value="">Discount type</option>
                                                                <option value="1" <?php echo ($_REQUEST['Type']=='1' && !isset($_SESSION['SUCCESS'])) ? "selected":""; ?>  >Fixed</option>
                                                                <option value="2" <?php echo ($_REQUEST['Type']=='2' && !isset($_SESSION['SUCCESS'])) ? "selected":""; ?>  >Percent</option>
                                                                
                                                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Amount off</label>
                             <input name="amount_off" class="form-control numberValue"  value="<?php echo  (!isset($_SESSION['SUCCESS']))? $_REQUEST['amount_off']:"" ?>"  placeholder="Please enter Amount off" value="" required type="text">
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Start Date</label>
                             <input type="text" class="form-control date-field"  data-toggle="datepicker" readonly value="<?php echo  (!isset($_SESSION['SUCCESS']))?$_REQUEST['start_date']:"" ?>" required placeholder="Start Date" name="start_date" >
                        </div>
                        <div class="col-md-6">
                            <label>End Date</label>
                                                     <input type="text" class="form-control date-field" readonly value="<?php echo  (!isset($_SESSION['SUCCESS']))?$_REQUEST['end_date']:""; ?>"  required data-toggle="datepicker" placeholder="End Date" name="end_date" >
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label>Number of uses<small>(if 0 add then unlimited uses)</small></label>
                             <input type="number" class="form-control" value="<?php echo  (!isset($_SESSION['SUCCESS']))?$_REQUEST['number_of_uses']:"" ?>" placeholder="Number of uses" name="number_of_uses" >
                        </div>
                        <div class="col-md-6">
                            <label>Customer</label>
                            	<?php 
								$allCustomers = getAllCustomer();
								?>
                                                  <select name="customers" id="customers" class="form-control">
                                                
                                                    <option value="-1">All</option>
                                                    <?php foreach($allCustomers as $single){
														$customerName  = "";
															if($single['FName']!=""){
																$customerName = $single['FName'];
															}
															if($single['LName']!=""){
																$customerName .= " ".$single['LName'];
															}
															if($single['Email']!=""){
																$customerName .= " (".$single['Email']." )";
															}
															
															
														 ?>
                                                    		<option value="<?php echo $single['UserID']; ?>" <?php  echo ($single['UserID']==$getSaleOrderByKey['UserID'])?"selected":"";?>><?php echo $customerName ; ?></option>
                                                    <?php } ?>
                                                	
                                                </select>
                        </div>
                    </div>
                    
                    
                    

                    

                    
                    

<div class="col-sm-4 ml-auto px-md-6" style="padding:0px;">
                        <button type="submit" name="create_discount" value="create" class="form-btn-grad btn-block">Create</button>
                    </div>
                  <br>
                </form>
                
                
         <br>                       
<h2 class="blue"> Current discounts</h2>
                
   <br>  
              <form method="get" class="top-search-options mb-5">
                    <div class="d-flex justify-content-stretch flex-wrap">
                        <div class="w-lg-20 w-100">
                            <a href="discount.php" class="btn-lg btn-block form-btn-grad">Reset</a>
                        </div>
                        
                        <div class="w-lg-20 w-100">
                            <input name="name" type="text" class="form-control" placeholder="Discount Name" value="<?php echo  $_REQUEST['name']; ?>">
                        </div>
                        <div class="w-lg-20 w-100">
                                <input type="text" class="form-control date-field" readonly value="<?php echo  $_REQUEST['create_date']; ?>"  required data-toggle="datepicker" placeholder="Create Date" name="create_date" >
                        </div>
                        
                        
                        <div class="col pd-0">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
                <?php 
				
						 	if(!isset($_REQUEST['key_order'])){
								$key_order  = "Id";	
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
                    <table class="table sorting users-table table-1">
                        <thead>
                            <tr>
                            
                           <?php /*?>   <th scope="col" onClick="window.location='discount.php?<?php echo http_build_query($_GET); ?>&key_order=Id&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="Id" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="Id" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">#ID</th><?php */?>
                              <th scope="col" onClick="window.location='discount.php?<?php echo http_build_query($_GET); ?>&key_order=Createdon&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="Createdon" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="Createdon" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Created</th>
                              <th scope="col" onClick="window.location='discount.php?<?php echo http_build_query($_GET); ?>&key_order=DiscountName&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="DiscountName" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="DiscountName" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Name</th>
                              <th scope="col" onClick="window.location='discount.php?<?php echo http_build_query($_GET); ?>&key_order=Type&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="Type" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="Type" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Type</th>
                              <th scope="col" onClick="window.location='discount.php?<?php echo http_build_query($_GET); ?>&key_order=Value&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="Value" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="Value" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Amount</th>
                           
                                
                                <th scope="col">Start</th>
                                <th scope="col">Expire</th>
                                <th scope="col">Uses</th>
                                <th scope="col">Customer</th>
                                <th>Action</th>
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
	
	if(!empty($searchQuery))
		$sql = "SELECT * FROM ".$searchTable." WHERE ".$searchQuery."  ORDER BY `".$key_order."` ".$key_order_by." LIMIT $start, $limit";
	else
		$sql = "SELECT * FROM ".$searchTable. " ORDER BY `".$key_order."` ".$key_order_by." LIMIT $start, $limit";
        
		
	 
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
	?>
                            
        <tr>
      <?php /*?>      <td class="data-id row_id_<?=$showData['Id'];?>"><span><?=$showData['Id'];?></span></td><?php */?>
            <td><?=date('m/d/Y',$showData['Createdon']);?></td>
            <td><?=$showData['DiscountName'] ?></td>
            <td><?=($showData['Type']==1)? "Fixed":"Percent" ?></td>
            <td ><?=$showData['Value']; ?></td>
            <td ><?=date('m/d/Y',$showData['StartDate']) ?></td>
            <td ><?=date('m/d/Y',$showData['EndDate']) ?></td>
            
            <td ><?php echo ($showData['NumberOfUses']==0)?"Unlimited":$showData['Used']."/".$showData['NumberOfUses']; ?></td>
            <td ><?php echo ($showData['CustomerID']==0 || $showData['CustomerID']=="-1")?"ALL":getCustomerByID($showData['CustomerID']); ?></td>
            
            <td class="data-delete"><a href="?deleteID=<?=$showData['Id'];?>"><i class="fas fa-trash-alt"></i></a></td>
        </tr>
  	<?php
	}
} else {
	echo "<tr>
			<td colspan=\"8\">No Search Result Found.</td>
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

    <?php include "includes/footer.php"; ?>


    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/script.js"></script>
<?php  unset($_SESSION['SUCCESS']); ?>

 <link href="//cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="//cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script> 

jQuery(document).ready(function(e) {
   $('.numberValue').keypress(function(event) {
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
});

   jQuery('#customers').select2({
					 		 placeholder: 'Please select customer'
					});
});

</script>
</body>


</html>