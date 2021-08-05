<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	
	$PageTitle = "Favorite";
	$usersArr = getUserArr();
	
	if(!is_login()) {
		header("location:".SITEURL."login.php")	;
		exit();
	}
	
	if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
		$deleteID = intval($_REQUEST['deleteID']);
		DltSglRcrd(FAVOURITE, "`id` = '$deleteID'");
		echo "<script> window.location.href = '".SITEURL."my-bookmarks.php';</script>";
		exit();
	}
	
	
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>My BookMarks</title>
    <?php require_once 'files/headSection.php'; ?>
    <link rel="stylesheet" href="css/style-2.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body class="MyBookMarksPage">
    <style>
    .thumbImage img{ width:120px;}
    </style>
     <?php require_once 'files/headerSection.php'; ?>
	<?php
	//Pagination Code
	
	$searchTable = FAVOURITE;
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";

if(isset($_REQUEST['product_id']) && !empty($_REQUEST['product_id'])) {
		$searchQuery .= "ProductID = '".$_REQUEST['product_id']."'";
		$searchURL .= "&product_id=".$_REQUEST['product_id'];
	}
	$AccessID = intval($_SESSION['userId']);
	
		$query = "SELECT COUNT(*) as num FROM ".$searchTable." where UserID='".$AccessID."'";
	
	
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];
	?>   
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
                <h1 class="page-heading mb-4">My Bookmarks(<?=$total_pages;?>)</h1>
               
                <div class="table-responsive">
                    <table class="table sorting users-table table-1">
                        <thead>
                            <tr>
                            
                                <th scope="col">Names</th>
                                <th scope="col">Image</th>
                            
                                <th scope="col">Action</th>
                                <th></th>
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
	
	$sql = "SELECT * FROM ".$searchTable. " where UserID='".$AccessID."' LIMIT $start, $limit";
   
         
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
		$getProduct = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$showData['ProductID']."'");
		$getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$showData['ProductID']."' AND `filetype` = 'image'","id","ASC");
	
	?>                   
        <tr>
          
            <td><a href="<?php echo SITEURL;?>p/<?=$getProduct['slug'];?>"><?=$getProduct['Title'];?></a></td>
            <td class="thumbImage"><a href="<?php echo SITEURL;?>p/<?=$getProduct['slug'];?>">
            <?php echo productImageSrc($getBanners['filename'],$showData['ProductID'],'354');
											 ?>
			</a></td>
			
             
           <td class="data-delete"> <a onClick="return confirm('Are you sure you want to perform this action?')"><i class="fas fa-trash-alt"></i></a></td>
        </tr>
  	<?php
	}
} else {
	echo "<tr>
			<td colspan=\"4\">No Bookmarks Found.</td>
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

    <?php include "files/footerSection.php"; ?>
     <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>