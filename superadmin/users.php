<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Users";
	
	if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
		$deleteID = intval($_REQUEST['deleteID']);
		DltSglRcrd(USERS, "`UserID` = '$deleteID'");
		echo "<script> window.location.href = '".ADMINURL."users.php';</script>";
		exit();
	}
	if(isset($_REQUEST['viewID']) && !empty($_REQUEST['viewID'])) {
		$viewID = intval($_REQUEST['viewID']);
		$_SESSION['USERID'] = $viewID;
		echo "<script> window.location.href = '".ADMINURL."user-details.php?user_id=".$viewID."';</script>";
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
	
	$searchTable = USERS;
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
		
	if(isset($_REQUEST['user_type']) && !empty($_REQUEST['user_type']) && $_REQUEST['user_type'] != "all") {
		$searchQuery .= "UserType = '".$_REQUEST['user_type']."' AND ";
		$searchURL .= "&user_type=".$_REQUEST['user_type'];
		
	} else {
		$searchQuery .= "UserType = 'user' AND ";
		
		$searchURL .= "&user_type=user";
	}
	
	if(!empty($_REQUEST['name']) && !empty($_REQUEST['name'])) {
		$searchQuery .= "(FName LIKE '%".$_REQUEST['name']."%' OR LName LIKE '%".$_REQUEST['name']."%') AND ";
		$searchURL .= "&name=".$_REQUEST['name'];
	}
	
	if(isset($_REQUEST['email']) && !empty($_REQUEST['email'])) {
		$searchQuery .= "Email LIKE '%".$_REQUEST['email']."%' AND ";
		$searchURL .= "&email=".$_REQUEST['email'];
	}
	
	if(isset($_REQUEST['state']) && !empty($_REQUEST['state']) && $_REQUEST['state'] != "all") {
		$searchQuery .= "State = '".$_REQUEST['state']."' AND ";
		$searchURL .= "&state=".$_REQUEST['state'];
	} else {
		$searchURL .= "&state=all";
	}
	
	if(isset($_REQUEST['status']) && !empty($_REQUEST['status']) && $_REQUEST['status'] != "all") {
		$searchQuery .= "Status = '".$_REQUEST['status']."' AND ";
		$searchURL .= "&status=".$_REQUEST['status'];
	} else {
		$searchURL .= "&status=all";
	}
	
	$searchQuery = rtrim($searchQuery, " AND ");
    
	if(!empty($searchQuery))
		$query = "SELECT COUNT(*) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(*) as num FROM ".$searchTable;
	
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];
	?>   
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
                <h1 class="page-heading mb-4">Users (<?=$total_pages;?>)</h1>
                <form method="get" class="top-search-options mb-5">
                    <div class="d-flex justify-content-stretch flex-wrap">
                        <div class="w-lg-20 w-100">
                            <a href="?user_type=all&name=&email=&state=all&status=all" class="btn-lg btn-block form-btn-grad">View all</a>
                        </div>
                        <div class="w-lg-20 w-100">
                            <select name="user_type" id="" class="form-control">
                                <option value="all" <?php if($_REQUEST['user_type']=="all"){echo "selected";} ?>>user type</option> 
                                <option value="user"  <?php if($_REQUEST['user_type']=="user"){echo "selected";} ?>>Users</option>
                                <option value="designer"  <?php if($_REQUEST['user_type']=="designer"){echo "selected";} ?>>Designer</option>
                            </select>
                        </div>
                        <div class="w-lg-20 w-100">
                            <input name="name" type="text" value="<?php echo $_REQUEST['name']; ?>" class="form-control" placeholder="names">
                        </div>
                        <div class="w-lg-20 w-100">
                            <input name="email" type="email" value="<?php echo $_REQUEST['email']; ?>"  class="form-control" placeholder="email">
                        </div>
                        <div class="w-lg-20 w-100">
                            <select name="status" id="status" class="form-control">
                                <option value="all" <?php if($_REQUEST['status']=="all"){echo "selected";} ?>>status</option>
                                <option value="active" <?php if($_REQUEST['status']=="active"){echo "selected";} ?>>Active</option>
                                <option value="inactive" <?php if($_REQUEST['status']=="inactive"){echo "selected";} ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="col-lg-5 ml-auto">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>
                <?php 
				
						 	if(!isset($_REQUEST['key_order'])){
								$key_order  = "UserID";	
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
                             <th scope="col" onClick="window.location='users.php?<?php echo http_build_query($_GET); ?>&key_order=UserID&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="UserID" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="UserID" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">#ID</th>
                              <th scope="col" onClick="window.location='users.php?<?php echo http_build_query($_GET); ?>&key_order=FName&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="FName" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="FName" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Names</th>
                              <th scope="col" onClick="window.location='users.php?<?php echo http_build_query($_GET); ?>&key_order=CreatedOn&key_order_by=<?php echo $ordeBy; ?>'" class="header <?php if($key_order=="CreatedOn" && $key_order_by=="DESC"){echo "headerSortUp";} ?> <?php if($key_order=="CreatedOn" && $key_order_by=="ASC"){echo "headerSortDown";} ?>">Joined</th>
                                <th scope="col">Sales</th>
                                <th scope="col">Purchases</th>
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

	if(!empty($searchQuery))
		$sql = "SELECT * FROM ".$searchTable." WHERE ".$searchQuery." ORDER BY `".$key_order."` ".$key_order_by." LIMIT $start, $limit";
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
            <td class="data-id row_id_<?=$showData['UserID'];?>"><span><?=$showData['UserID'];?></span></td>
            <td><?=$showData['FName']." ".$showData['LName'];?> <br> <?=$showData['Email'];?></td>
            <td><?=date('m/d/Y',$showData['CreatedDate']);?></td>
            <td class="sales">$<?=GetSumOnCndi(TRANSACTION,"Amount", "UserID=".$showData['UserID']);?> <small><?=GetNumOfRcrdsOnCndi(TRANSACTION,"`UserID` = '".$showData['UserID']."'");?></small></td>
            <td class="number"><?=GetNumOfRcrdsOnCndi(ORDER, "CustomerID = '".$showData['UserID']."'");?></td>
            <td class="data-<?=$showData['Status'];?>"><?=ucfirst($showData['Status']);?></td>
            <td class="data-view"><a href="?viewID=<?=$showData['UserID'];?>" class="view">view</a><div class="data-delete"><a href="?deleteID=<?=$showData['UserID'];?>"><i class="fas fa-trash-alt"></i></a></div>
            </td>
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
<style>
.data-view a{ display:inline-block !important;}
.data-delete{ display:inline-block;}
</style>
</body>

</html>