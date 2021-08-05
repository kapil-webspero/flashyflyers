<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	require_once '../wordpress/wp-config.php';
	
	$PageTitle = "Product Tags";
	
	if(isset($_REQUEST['create_tag'])){
				
				$checkCodequery = "SELECT COUNT(*) as num FROM ".PRODUCT_TAGS." WHERE TagName = '".$_REQUEST['TagName']."'";
			   $checkCodeRs = mysql_fetch_array(mysql_query($checkCodequery));
				if($checkCodeRs['num']>0){
						
						$_SESSION['ERROR'] = "Sorry, tag name already exists.Please choose another tag name.";	
				}else{
				
				$TagID = InsertRcrdsGetID(array('TagName','TagSlug','TagStatus','Createdon'), array($_REQUEST['TagName'],sanitize_title_with_dashes($_REQUEST['TagName']),$_REQUEST['TagStatus'],strtotime(date("m/d/y"))), PRODUCT_TAGS);
			
				$_SESSION['SUCCESS'] = "Tag has been successfully added.";	
				}
				
	}
	if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
		$deleteID = intval($_REQUEST['deleteID']);
		DltSglRcrd(PRODUCT_TAGS, "`Id` = '$deleteID'");
		$_SESSION['SUCCESS'] = "Tag has been successfully deleted.";
		echo "<script> window.location.href = '".ADMINURL."product-tags.php';</script>";
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
	
	$searchTable = PRODUCT_TAGS;
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
		
	
	
	if(!empty($_REQUEST['name']) && !empty($_REQUEST['name'])) {
		$searchQuery .= "(TagName LIKE '%".$_REQUEST['name']."%') AND ";
		$searchURL .= "&name=".$_REQUEST['name'];
	}
	
	if(isset($_REQUEST['create_date']) && !empty($_REQUEST['create_date'])) {
		$dateCreated = strtotime($_REQUEST['create_date']);
		$searchQuery .= "Createdon = '".$dateCreated."' AND ";
		$searchURL .= "&create_date=".$_REQUEST['create_date'];
	}
	
	if(isset($_REQUEST['StatusSearch']) && !empty($_REQUEST['StatusSearch'])) {
		$dateCreated = strtotime($_REQUEST['create_date']);
		$searchQuery .= "TagStatus = '".$_REQUEST['StatusSearch']."' AND ";
		$searchURL .= "&StatusSearch=".$_REQUEST['StatusSearch'];
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
                    <h1 class="page-heading">Product Tags</h1>
                                    </div>
                                 
            
           
                
<h2 class="blue"> Create Tag</h2>
                
<form method="post" class="create-tag brd-bottom">
                    <div class="row create-product">
                        <div class="col-md-6">
                            <label>Tag name</label>
                            <input name="TagName" value="<?php echo (!isset($_SESSION['SUCCESS']))?  $_REQUEST['TagName']:""; ?>"  class="form-control" placeholder="Please enter tag name" required type="text">
                        </div>
                        
                       
                        <div class="col-md-6">
                            <label>Status</label>
                           <select class="form-control" name="TagStatus">
                           	<option value="Active" <?php echo (!isset($_SESSION['SUCCESS']) && $_REQUEST['TagStatus']=="Active")?  "selected":""; ?>>Active</option>
                            <option value="Inactive" <?php echo (!isset($_SESSION['SUCCESS']) && $_REQUEST['TagStatus']=="Inactive")?  "selected":""; ?>>Inactive</option>
                           
                           </select>
                        </div>
                        
                    </div>
                    
                    
                    
                    
                    

                    

                    
                    

<div class="col-sm-4 ml-auto px-md-6" style="padding:0px;">
                        <button type="submit" name="create_tag" value="create" class="form-btn-grad btn-block">Create</button>
                    </div>
                  <br>
                </form>
                
                
         <br>                       
<h2 class="blue"> Current tags</h2>
                
   <br>  
              <form method="get" class="top-search-options mb-5">
                    <div class="d-flex justify-content-stretch flex-wrap">
                        <div class="w-lg-20 w-100">
                            <a href="product-tags.php" class="btn-lg btn-block form-btn-grad">Reset</a>
                        </div>
                        
                        <div class="w-lg-20 w-100">
                            <input name="name" type="text" class="form-control" placeholder="Tag Name" value="<?php echo  $_REQUEST['name']; ?>">
                        </div>
                        <div class="w-lg-20 w-100">
                                <input type="text" class="form-control date-field" readonly value="<?php echo  $_REQUEST['create_date']; ?>"  required data-toggle="datepicker" placeholder="Create Date" name="create_date" >
                        </div>
                        
                          <div class="w-lg-20 w-100">
                            <select class="form-control" name="StatusSearch">
                           	<option value="">Select Status</option>
                            <option value="Active" <?php echo ($_REQUEST['StatusSearch']=="Active")?  "selected":""; ?>>Active</option>
                            <option value="Inactive" <?php echo ($_REQUEST['StatusSearch']=="Inactive")?  "selected":""; ?>>Inactive</option>
                           
                           </select>
                        </div>
                        
                        
                        <div class="w-lg-20 w-100">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
				<?php 
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
                    <table class="table sorting users-table table-1">
                        <thead>
                            <tr>
                                
                                <th scope="col" onClick="window.location='product-tags.php?<?php echo http_build_query($_GET); ?>&key_order=Id&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="Id" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="Id" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">#ID</th>
                                <th scope="col" onClick="window.location='product-tags.php?<?php echo http_build_query($_GET); ?>&key_order=TagName&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="TagName" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="TagName" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Name</th>
                                
                                <th scope="col">Status</th>
                                
                                <th scope="col">Created</th>
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
		$sql = "SELECT * FROM ".$searchTable. "  ORDER BY `".$key_order."` ".$key_order_by." LIMIT $start, $limit";
         
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
            <td class="data-id row_id_<?=$showData['Id'];?>"><span><?=$showData['Id'];?></span></td>
          
            <td><?=$showData['TagName'] ?></td>
            <td ><?=$showData['TagStatus'] ?></td>
              <td><?=date('m/d/Y',$showData['Createdon']);?></td>
            
            <td class="data-delete"><a href="?deleteID=<?=$showData['Id'];?>"><i class="fas fa-trash-alt"></i></a></td>
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

    <?php include "includes/footer.php"; ?>


    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/script.js"></script>
<?php  unset($_SESSION['SUCCESS']); ?>
<script> 

jQuery(document).ready(function(e) {
   $('.numberValue').keypress(function(event) {
  if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
    event.preventDefault();
  }
});
});
</script>
</body>


</html>