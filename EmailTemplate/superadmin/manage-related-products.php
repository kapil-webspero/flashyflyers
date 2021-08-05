<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Manage Related Products";
	if(isset($_REQUEST['product_id']) && !empty($_REQUEST['product_id'])) {
		unset($_SESSION['EDITPRODUCTID']);
		$_SESSION['EDITPRODUCTID'] = intval($_REQUEST['product_id']);
	}
	if(isset($_SESSION['EDITPRODUCTID']) && !empty($_SESSION['EDITPRODUCTID'])) {
		$productID = $_SESSION['EDITPRODUCTID'];
		$prodData = GetSglRcrdOnCndi(PRODUCT,"`id` = '".$productID."'");
	}
	if(isset($_REQUEST['processRelated']) && !empty($_REQUEST['processRelated'])) {
		if(isset($_REQUEST['related_product']) && !empty($_REQUEST['related_product'])) {
			$related_product = intval($_REQUEST['related_product']);
			if($_REQUEST['processRelated'] == "yes") {
				InsertRcrdsByData(PRODUCT_REL, "`ProductID` = '$productID', `RelatedProductID` = '$related_product'");
				InsertRcrdsByData(PRODUCT_REL, "`RelatedProductID` = '$productID', `ProductID` = '$related_product'");
				$_SESSION['SUCCESS'] = "Product successfully added from related product list.";
			} elseif($_REQUEST['processRelated'] == "no") {
				DltSglRcrd(PRODUCT_REL,"`ProductID` = '$productID' AND `RelatedProductID` = '$related_product'");
				DltSglRcrd(PRODUCT_REL,"`RelatedProductID` = '$productID' AND `ProductID` = '$related_product'");
				$_SESSION['SUCCESS'] = "Product successfully removed from related product list.";
			}
		}
		echo "<script> window.location.href = '".ADMINURL."manage-related-products.php?product_id=".$productID."';</script>";
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
    <style>
.thumb_image img{ width:40px;}
</style>

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
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
                <h1 class="page-heading mb-4"><?=$prodData['Title'];?></h1>
				<div class="table-responsive">
                    <table class="table sorting users-table table-1">
                        <thead>
                            <tr>
                                <th scope="col">#ID</th>
                                <th scope="col">Names</th>
                                <th scope="col">Image</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                                <th></th>
                            </tr>
                            <tbody>
<?php
$otherProducts=GetMltRcrdsOnCndiWthOdr(PRODUCT, "`id` <> '".$productID."'", "Title", "ASC");
foreach($otherProducts as $showData) {

$getBanners = GetSglRcrdOnCndiWthOdr(PRODUCT_BANNER, "`prod_id` = '".$showData['id']."' AND `filetype` = 'image'","id","ASC");
?>                   
        <tr>
            <td class="data-id"><span><?=$showData['id'];?></span></td>
            <td><?=$showData['Title'];?></td>
                <td class="thumb_image">
            <?php echo  productImageSrc($getBanners['filename'],$showData['id'],'354');?></td>
            <td class="data-<?php echo ($showData['Status']==1) ? "active" : "inactive"; ?>"><?php echo ($showData['Status']==1) ? "Active" : "Inactive"; ?></td>
            <td class="data-view">
            	<?php if(GetNumOfRcrdsOnCndi(PRODUCT_REL,"`ProductID` = '$productID' AND `RelatedProductID` = '".$showData['id']."'")>0 ) { ?>
            	<a href="<?=ADMINURL;?>manage-related-products.php?product_id=<?=$productID;?>&processRelated=no&related_product=<?=$showData['id'];?>" class="view">Remove</a>
                <?php } else { ?>
            	<a href="<?=ADMINURL;?>manage-related-products.php?product_id=<?=$productID;?>&processRelated=yes&related_product=<?=$showData['id'];?>" class="view">Add</a>
                <?php } ?>
            </td>
        </tr>
<?php 
}
?>
  	                           
                            </tbody>
                        </thead>
                    </table>
                </div>

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