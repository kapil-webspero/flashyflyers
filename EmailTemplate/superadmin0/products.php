<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Products";
	$prodType = getProdTypeParentArr();
	unset($_SESSION['NEWPRODUCTID']);
	unset($_SESSION['EDITPRODUCTID']);
	$productTypeNameArr = array();
	foreach($prodType as $pro) {
		$productTypeNameArr[$pro['id']] = $pro['name'];		
	}
	
	
	if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
		$deleteID = intval($_REQUEST['deleteID']);
		DltSglRcrd(PRODUCT, "`id` = '$deleteID'");
		DltSglRcrd(PRODUCT_BANNER, "`prod_id` = '$deleteID'");
		recursiveRemoveDirectory("../uploads/products/".$deleteID."/");
		DltSglRcrd(PRODUCT_REL, "ProductID = '$deleteID' OR RelatedProductID = '$deleteID'");
		echo "<script> window.location.href = '".ADMINURL."products.php';</script>";
		exit();
	}
	if(isset($_REQUEST['viewID']) && !empty($_REQUEST['viewID'])) {
		$viewID = intval($_REQUEST['viewID']);
		$_SESSION['EDITPRODUCTID'] = $viewID;
		echo "<script> window.location.href = '".ADMINURL."edit-product.php?product_id=".$viewID."';</script>";
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
	
	$searchTable = PRODUCT;
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
	
	
	if(!empty($_REQUEST['name']) && !empty($_REQUEST['name'])) {
		$searchQuery .= "Title LIKE '%".$_REQUEST['name']."%' AND ";
		$searchURL .= "&name=".$_REQUEST['name'];
	}	
	
	if(isset($_REQUEST['product_type']) && !empty($_REQUEST['product_type']) && $_REQUEST['product_type'] != "all") {
		$searchQuery .= "parent_product_cat_id = '".$_REQUEST['product_type']."' AND ";
		$searchURL .= "&product_type=".$_REQUEST['product_type'];
	} else {
		$searchURL .= "&product_type=all";
	}
		
	if(isset($_REQUEST['product_id']) && !empty($_REQUEST['product_id'])) {
		$searchQuery .= "id = '".$_REQUEST['product_id']."' AND ";
		$searchURL .= "&product_id=".$_REQUEST['product_id'];
	}
	
	if(isset($_REQUEST['status']) && $_REQUEST['status'] != "all") {
		$searchQuery .= "Status = ".$_REQUEST['status']." AND ";		
		$searchURL .= "&status=".$_REQUEST['status'];
	} else {
		$searchURL .= "&status=all";
	}
	
	if(isset($_REQUEST['custom_prodcut']) && $_REQUEST['custom_prodcut'] != "") {
		$searchQuery .= "CustomProduct = 'yes' AND ";		
		$searchURL .= "&custom_prodcut=".$_REQUEST['custom_prodcut'];
	} else {
	}
	
	
	
	$searchQuery = rtrim($searchQuery, " AND ");
	
	if(!empty($searchQuery))
		$query = "SELECT COUNT(*) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(*) as num FROM ".$searchTable;
	
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];
	?>
    <div class="container product-menu">
    	<a href="<?=ADMINURL;?>product-tags.php">Tags</a>
        <a href="<?=ADMINURL;?>product-types.php">Types</a>
        <a href="<?=ADMINURL;?>products_review.php">Reviews</a>
    </div>   
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
                
                <div class="page-head mb-4">
                    <h1 class="page-heading">Products (<?=$total_pages;?>)</h1>
                    <a href="<?=ADMINURL;?>create-product.php">
                       
                        Create product
                         <i class="fas fa-angle-right"></i>
                    </a>
                   
                   
                </div>
                
                   
                <form method="get" class="top-search-options mb-5">
                	<div class="d-flex justify-content-stretch flex-wrap">
                        <div class="w-lg-20 w-100">
                            <a href="products.php" class="btn-lg btn-block form-btn-grad">View all</a>
                        </div>
                        <div class="w-lg-20 w-100">
                        	<input type="text" name="name" class="form-control" placeholder="Product Name" value="<?php if(isset($_REQUEST['name'])){ echo $_REQUEST['name']; } ?>">
                          
                      
                      <label class="custom-control custom-checkbox">
                                <input type="checkbox" name="custom_prodcut" <?php if(isset($_REQUEST['custom_prodcut']) && $_REQUEST['custom_prodcut']=='1'){ echo "checked"; } ?> class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Custom Product</span>
                            </label>
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
                            <input type="text" name="product_id" class="form-control" placeholder="product #" value="<?php if(isset($_REQUEST['product_id'])){ echo $_REQUEST['product_id']; } ?>">
                        </div>
                        <div class="w-lg-20 w-100">
                            <select name="status" id="" class="form-control">
                                <option value="all" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='all'){ echo "selected"; } ?>>status</option>
                                <option value="1" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='1'){ echo "selected"; } ?>>Active</option>
                                <option value="0" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='0'){ echo "selected"; } ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-lg-4 ml-auto">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
                
                <?php
				
							if(!isset($_REQUEST['key_order'])){
								$key_order  = "id";	
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
                              	<th scope="col" onClick="window.location='products.php?<?php echo http_build_query($_GET); ?>&key_order=id&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="id" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="id" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">ID</th>
                                <th scope="col" onClick="window.location='products.php?<?php echo http_build_query($_GET); ?>&key_order=Title&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="Title" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="Title" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Names</th>
                                <th scope="col">Type</th>
                                <th scope="col">Sales</th>
                                <th scope="col">Sale Amt.</th>
                                <th scope="col">Sort Order</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                                 <?php if( $_SESSION['userType'] == "admin"){ ?>
                                <th></th>
                                <?php } ?>
                            </tr>
                            <tbody>
<?php
$prodType = getProdTypeArr();
$getProdSizes = getProdSizeArr();

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
        if(!empty($searchQuery)) {
            $sql = "SELECT * FROM " . $searchTable . " WHERE " . $searchQuery."  ORDER BY `".$key_order."` ".$key_order_by;
        }else {
            $sql = "SELECT * FROM " . $searchTable."  ORDER BY `".$key_order."` ".$key_order_by;
        }
    }else{
        if(!empty($searchQuery)) {
            $sql = "SELECT * FROM " . $searchTable . " WHERE " . $searchQuery . "  ORDER BY `".$key_order."` ".$key_order_by." LIMIT $start, $limit";
        }else {
            $sql = "SELECT * FROM " . $searchTable . "  ORDER BY `".$key_order."` ".$key_order_by." LIMIT $start, $limit";
        }
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
		$totalSales=GetNumOfRcrdsOnCndi(ORDER,"ProductID=".$showData['id']);
		$totalSalesamount=GetSglRcrdWthSmFldsOnCndi(ORDER,"ProductID=".$showData['id'],"coalesce(sum(TotalPrice),0) as totalsalesfig");
	?>                   
        <tr>
            <td class="data-id"><span><?=$showData['id'];?></span></td>
            <td><?=$showData['Title'];?></td>
            <td><?php if($showData['Addon'] == "0") { if(!empty($productTypeNameArr[$showData['parent_product_cat_id']])) { echo $productTypeNameArr[$showData['parent_product_cat_id']]; } else { echo "-"; } } else { echo "Addon"; } ?></td>
            <td class="number"><?=$totalSales?></td>
            <td class="amount">$<?=number_format($totalSalesamount['totalsalesfig'],2)?></td>
            <td data-id="<?=$showData['id'];?>"><input type="text" value="<?php echo !empty($showData['sort_order']) ? $showData['sort_order'] : ''; ?>" class="product-order" onchange="update_sort_order(this);"></td>
            <td class="data-<?php echo ($showData['Status']==1) ? "active" : "inactive"; ?>"><?php echo ($showData['Status']==1) ? "Active" : "Inactive"; ?></td>
            <td class="data-view"><a href="?viewID=<?=$showData['id'];?>" class="view">view</a> </td>
            <?php if( $_SESSION['userType'] == "admin"){ ?>
            <td class="data-delete"> <a href="?deleteID=<?=$showData['id'];?>" onClick="return confirm('Are you sure you want to perform this action?')"><i class="fas fa-trash-alt"></i></a></td>
            <?php } ?>
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

                <nav class="mt-5">
                    <ul class="data-pagination justify-content-center flex-wrap">
                        <?php
                        
                        if( !empty($_REQUEST['page']) && $_REQUEST['page'] == -1) {
                            $viewPaginationUrl = ADMINURL."products.php";
                            $pagination = "<li><a href='$viewPaginationUrl' aria-label=\"View All\">View Pagination</a></li>";
                        }
                        echo $pagination;

                        if( $_REQUEST['page'] != -1) {
                        ?>
                        <li>
                            <a href="<?=ADMINURL;?>products.php?page=-1" aria-label="View All">View All</a>
                        </li>
                        <?php } ?>
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
        function update_sort_order(e){
            var sort_order = $(e).val();

                if (/^\d+$/.test(sort_order) || sort_order == '') {

                    var product_id = $(e).parent().attr('data-id');

                    jQuery(".mainLoader").show();

                    $.ajax({
                        type: "POST",
                        url: "<?=SITEURL;?>ajax/update_product_sort_order.php",
                        data: "product_id="+product_id+"&sort_order="+sort_order,
                        success: function(regResponse) {
                            jQuery(".mainLoader").hide();
                        }
                    });

                }else{
                    alert("please enter a valid sort order number");
                    $(e).val("");
                }

        }
    </script>

</body>

</html>