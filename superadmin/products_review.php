<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	
	$tableProduct = PRODUCT;
	$tableUser = USERS;
	$searchTable = PRODUCTS_REVIEW;
	
	$PageTitle = "Products Review";
	
	if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
		$deleteID = intval($_REQUEST['deleteID']);
		$_SESSION['SUCCESS'] = "Review Successfuly Delete!";
		DltSglRcrd(PRODUCTS_REVIEW, "`ReviewID` = '$deleteID'");
		echo "<script> window.location.href = '".ADMINURL."products_review.php';</script>";
		exit();
	}
	
	
	if(isset($_REQUEST['ID']) && !empty($_REQUEST['ID'])) {
		$ID = intval($_REQUEST['ID']);
		
		
		UpdateRcrdOnCndi($searchTable, "`ReviewStatus` = '".$_REQUEST['mode']."'","`ReviewID` = '".$ID."'");
		if($_REQUEST['mode']==1){
			$textStatus = "Approved";	
		}
		if($_REQUEST['mode']==2){
			$textStatus = "Rejected";	
		}
		
		
		$_SESSION['SUCCESS'] = "Review Successfuly ".$textStatus."!";
		//DltSglRcrd(PRODUCT, "`id` = '$deleteID'");
		echo "<script> window.location.href = '".ADMINURL."products_review.php';</script>";
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
	
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
	
	if(!empty($_REQUEST['review_date_from'])){
		$startDate = explode("/",$_REQUEST['review_date_from']);
		$startDate = $startDate[2]."-".$startDate[0]."-".$startDate[1];	
	}
	if(!empty($_REQUEST['review_date_to'])){
		$endDate = explode("/",$_REQUEST['review_date_to']);
		$endDate = $endDate[2]."-".$endDate[0]."-".$endDate[1];	
	}
	
	if(!empty($_REQUEST['review_date_from']) && empty($_REQUEST['review_date_to'])) {
		$searchQuery .= "RP.ReviewDate >='".$startDate." 00:00:00' AND ";
		$searchURL .= "&review_date_from=".$_REQUEST['review_date_from'];
	}	
	
	if(!empty($_REQUEST['review_date_to']) && empty($_REQUEST['review_date_from'])) {
		$searchQuery .= "RP.ReviewDate <='".$endDate." 23:59:59' AND ";
		$searchURL .= "&review_date_to=".$_REQUEST['review_date_to'];
	}
	
	if(!empty($_REQUEST['review_date_to']) && !empty($_REQUEST['review_date_from'])) {
		$searchQuery .= "(RP.ReviewDate >='".$startDate." 00:00:00' AND  RP.ReviewDate <='".$endDate." 23:59:59') AND ";
		$searchURL .= "&review_date_to=".$_REQUEST['review_date_to']."&review_date_from=".$_REQUEST['review_date_from'];
	}	
	
	
	
	if(!empty($_REQUEST['product_name'])) {
		$searchQuery .= "P.Title LIKE '%".$_REQUEST['product_name']."%' AND ";
		$searchURL .= "&product_name=".$_REQUEST['product_name'];
	}	
	
	if(isset($_REQUEST['customer_name']) && !empty($_REQUEST['customer_name'])) {
		$searchQuery .= "(U.FName LIKE '%".$_REQUEST['customer_name']."%' OR U.LName LIKE '%".$_REQUEST['customer_name']."%' OR CONCAT( U.FName,  ' ', U.LName) LIKE  '%".$_REQUEST['customer_name']."%' OR CONCAT( U.LName,  ' ', U.FName) LIKE  '%".$_REQUEST['customer_name']."%' ) AND ";
		$searchURL .= "&customer_name=".$_REQUEST['customer_name'];
	}
	
	if(isset($_REQUEST['status']) && $_REQUEST['status'] != "all") {
		$searchQuery .= "RP.ReviewStatus = '".$_REQUEST['status']."' AND ";		
		$searchURL .= "&status=".$_REQUEST['status'];
	} else {
		$searchURL .= "&status=all";
	}
	
	$searchQuery = rtrim($searchQuery, " AND ");
	
	if(!empty($searchQuery))
		$query = "SELECT COUNT(RP.ReviewID) as num FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID  WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(RP.ReviewID) as num FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID";
	
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
    <?php  unset($_SESSION['SUCCESS']);
	} ?> 
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
                
                <div class="page-head mb-4">
                    <h1 class="page-heading">Reviews (<?=$total_pages;?>)</h1>
                   
                   
                </div>
                
                   
                <form method="get" class="top-search-options mb-5">
                	<div class="d-flex justify-content-stretch flex-wrap">
                        <div class="w-lg-20 w-100">
                            <a href="products_review.php" class="btn-lg btn-block form-btn-grad">View all</a>
                        </div>
                        
                        <div class="w-lg-20 w-100">
                                <input type="text" class="form-control date-field"  value="<?php echo  $_REQUEST['review_date_from']; ?>"   data-toggle="datepicker" placeholder="Review date from" name="review_date_from" >
                        </div>
                        
                        <div class="w-lg-20 w-100">
                                <input type="text" class="form-control date-field" readonly value="<?php echo  $_REQUEST['review_date_to']; ?>"   data-toggle="datepicker" placeholder="Review date to" name="review_date_to" >
                        </div>
                        <div class="w-lg-20 w-100">
                        	<input type="text" name="product_name" class="form-control" placeholder="Product Name" value="<?php if(isset($_REQUEST['product_name'])){ echo $_REQUEST['product_name']; } ?>">
                        </div>
                        
                         <div class="w-lg-20 w-100">
                        	<input type="text" name="customer_name" class="form-control" placeholder="Customer Name" value="<?php if(isset($_REQUEST['customer_name'])){ echo $_REQUEST['customer_name']; } ?>">
                        </div>
                        
                        
                        <div class="w-lg-20 w-100">
                            <select name="status" id="" class="form-control">
                                <option value="all" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='all'){ echo "selected"; } ?>>All Status</option>
                              
                                <option value="0" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='0'){ echo "selected"; } ?>>Pending</option>
                                <option value="1" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='1'){ echo "selected"; } ?>>Approved</option>
                                <option value="2" <?php if(isset($_REQUEST['status']) && $_REQUEST['status']=='2'){ echo "selected"; } ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-lg-4 ml-auto">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
                 <?php 
				
						 	if(!isset($_REQUEST['key_order'])){
								$key_order  = "ReviewID";	
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
                    <table class="table sorting product-review-table table-1">
                        <thead>
                            <tr>
                            
                              <th scope="col" onClick="window.location='products_review.php?<?php echo http_build_query($_GET); ?>&key_order=ReviewID&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="ReviewID" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="ReviewID" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">#ID</th>
                               <th scope="col" onClick="window.location='products_review.php?<?php echo http_build_query($_GET); ?>&key_order=OrderID&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="OrderID" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="OrderID" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Order ID</th>
                            <th scope="col" onClick="window.location='products_review.php?<?php echo http_build_query($_GET); ?>&key_order=ReviewDate&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="ReviewDate" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="ReviewDate" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Date</th>
                           
                               <th scope="col">Product Name</th>
                                <th scope="col">Customer Name</th>
                                <th scope="col">Rating</th>
                               
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                                
                            </tr>
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
        if(!empty($searchQuery)) {
            $sql = "SELECT RP.ReviewID,RP.OrderID,RP.Rating,RP.ReviewDescription,RP.ReviewStatus,RP.ReviewDate,P.Title,P.slug,U.FName,U.LName,U.Email FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID WHERE " . $searchQuery." ORDER BY `RP`.`".$key_order."` ".$key_order_by;
        }else {
            $sql = "SELECT RP.ReviewID,RP.OrderID,RP.Rating,RP.ReviewDescription,RP.ReviewStatus,RP.ReviewDate,P.Title,P.slug,U.FName,U.LName,U.Email FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID ORDER BY `RP`.`".$key_order."` ".$key_order_by." ";
        }
    }else{
        if(!empty($searchQuery)) {
            $sql = "SELECT RP.ReviewID,RP.OrderID,RP.Rating,RP.ReviewDescription,RP.ReviewStatus,RP.ReviewDate,P.Title,P.slug,U.FName,U.LName,U.Email FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID WHERE " . $searchQuery . " ORDER BY `RP`.`".$key_order."` ".$key_order_by."  LIMIT $start, $limit";
        }else {
            $sql = "SELECT RP.ReviewID,RP.OrderID,RP.Rating,RP.ReviewDescription,RP.ReviewStatus,RP.ReviewDate,P.Title,P.slug,U.FName,U.LName,U.Email FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID ORDER BY `RP`.`".$key_order."` ".$key_order_by."  LIMIT $start, $limit";
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
		
	?>     
    <div id="ProductInstruction<?php echo $sr; ?>" class="popup ProductInstruction">

    <div class="popup-box">

        <div class="popup-middle">

            <div class="popup_block">

                <h4 class="title text-center mb-4"><strong class="prodTitle">Review Detail</strong> <span class="close-btn float-right"><i class="fa fa-times"></i></span></h4>
				
                
                 	
                <div class="popupfull_content">
                
                
                <div class="customer_details">
                                <strong>Review Date: </strong>
                                <span><?=date('M d, Y',strtotime($showData['ReviewDate']))." ".date('g:i A',strtotime($showData['ReviewDate']));?></span>
                            </div>
                            
                            <div class="customer_details">
                                <strong>Customer: </strong>
                                <span><?php echo $showData['FName']." ".$showData['LName']  ?> <small>(<?php echo $showData['Email']; ?>)</small></span>
                            </div>
                          <div class="customer_details">
                                <strong>Product: </strong>
                                <span ><a style="color:#FFF; text-decoration:none;" href="<?php echo SITEURL ?>p/<?php echo $showData['slug'] ?>" title="<?=$showData['Title'];?>" target="_blank"><?=$showData['Title'];?></a></span>
                            </div>  
                            
                               <div class="customer_details">
                                <strong>Review: </strong>
                                <span>	<?php echo $showData['ReviewDescription']; ?></span>
                            </div>  
					

                </div>

            </div>

        </div>

    </div>

</div>              
        <tr>
            <td class="data-id"><span><?=$showData['ReviewID'];?></span></td>
            <td><a href="<?php echo ADMINURL ?>order-details.php?order_id=<?php echo $showData['OrderID'] ?>" title="View Order" target="_blank"><span><?=$showData['OrderID'];?></span></a></td>
            
             <td><?=date('M d, Y',strtotime($showData['ReviewDate']))."<br>".date('g:i A',strtotime($showData['ReviewDate']));?></td>
                                    
            <td><a href="<?php echo SITEURL ?>p/<?php echo $showData['slug'] ?>" title="<?=$showData['Title'];?>" target="_blank"><?=$showData['Title'];?></a></td>
            <td><?php echo $showData['FName']." ".$showData['LName']  ?><br><small><?php echo $showData['Email']; ?></small></td>
            <td><?=$showData['Rating']?>/5</td>
          	<td><div class="review_status<?=$reviewStatus[$showData['ReviewStatus']]?>"><?=$reviewStatus[$showData['ReviewStatus']]?></div></td>
            <td class="review_action_btn"> 
            	<?php if($showData['ReviewStatus']==0 || $showData['ReviewStatus']==2){ ?>
                <a title="Approve" href="?ID=<?=$showData['ReviewID'];?>&mode=1" class="review_approved_btn btn btn-success" onClick="return confirm('Are you sure you want to approve this review?')">Approve</a>
                <?php } ?>
            	<?php if($showData['ReviewStatus']!=2){ ?>
                <a title="Reject" href="?ID=<?=$showData['ReviewID'];?>&mode=2" onClick="return confirm('Are you sure you want to reject this review?')"  class="review_rejected_btn btn-info btn">Reject</a>
                <?php } ?>
               <a title="View" href="javascript:void(0)" onClick="OpenReview('<?php echo $sr; ?>')"  class="review_view_btn btn btn-primary">View</a>
           	 <a title="Delete" class="review_delete_btn btn btn-danger" href="?deleteID=<?=$showData['ReviewID'];?>" onClick="return confirm('Are you sure you want to delete this review?')">Delete</a></td>
        </tr>
  	<?php
	$sr++;
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
                            $viewPaginationUrl = ADMINURL."products_review.php";
                            $pagination = "<li><a href='$viewPaginationUrl' aria-label=\"View All\">View Pagination</a></li>";
                        }
                        echo $pagination;

                        if( $_REQUEST['page'] != -1) {
                        ?>
                        <li>
                            <a href="<?=ADMINURL;?>products_review.php?page=-1" aria-label="View All">View All</a>
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
      function OpenReview(id){
	jQuery(".ProductInstruction").removeClass("active");
		$("#ProductInstruction"+id).addClass("active");
}
 $(".close-btn").click(function(){

     
     jQuery(".ProductInstruction").removeClass("active");

    });
    </script>
<style>
.review_action_btn a{ display:block; margin-bottom:5px; border:0px !important;}
</style>
</body>

</html>