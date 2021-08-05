<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	
	$PageTitle = "Transaction Details";
	
	if(isset($_REQUEST['transaction_id']) && !empty($_REQUEST['transaction_id'])) {
		unset($_SESSION['TRANSACTIONID']);
		$_SESSION['TRANSACTIONID'] = intval($_REQUEST['transaction_id']);
	}
	if(!isset($_SESSION['TRANSACTIONID']) && empty($_SESSION['TRANSACTIONID'])) {
		echo "<script> window.location.href = '".ADMINURL."transactions.php';</script>";
		exit();
	}
	$TransactionID = intval($_SESSION['TRANSACTIONID']);
	
	$transactionData = GetSglRcrdOnCndi(TRANSACTION, "`id` = '$TransactionID'");
	
	$userData = GetSglRcrdOnCndi(USERS,"UserID = '".$transactionData['UserID']."'");
	$productsList = GetMltRcrdsOnCndiWthOdr(ORDER, "TransactionID = '$TransactionID'", "id", "ASC");
	
	
									
									 
									
									
									
									
									//prodsArr[GetSglRcrdOnCndiWthOdr(ORDER,"TransactionID = '".$showData['id']."'", "id", "ASC")['ProductID']]['Title'];
	
	$prodsArr = getALLProductArr();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
</head>

<body>
    <?php include "includes/header.php"; ?>
    

    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60">
                <h1 class="page-heading mb-4">Transaction (#<?=$transactionData['id'];?>)</h1>

                <div class="row mt-5">
                    <div class="col-lg-6 brd-lg-right pb-5">
                        <h2 class="blue text-center mb-4">Payee</h2>
                        <div class="payee-details">
                            <img src="<?php echo (!empty($userData['ProfilePhoto'])) ? SITEURL."uploads/profileImages/".$userData['ProfilePhoto'] : "images/user.png"; ?>" alt="">
                            <div>
                                <h3><?=$userData['FName']." ".$userData['LName'];?></h3>
                                <p><?=$userData['Eame'];?></p>
                                <a href="#" class="btn btn-blue">send email</a>
                        	</div>
                        </div>
                    </div>
                    <div class="col-lg-5 pl-sm-5 pb-5">
                        <h2 class="blue text-center mb-4">Payment details</h2>
                        <div class="payment-details">
                            <label>Product</label>
                            <p>
                            <?php
							
							if(!empty($productsList)!=""){
								
								
								$OrderData = GetMltRcrdsOnCndiWthOdr(ORDER, "`TransactionID` = '".$TransactionID."'", "ResponseState", "ASC");
							
							$productNameData = "";
								$IndexCount=1;
								$getRespnseCode= array();
								foreach($OrderData as $k=>$v){
										if($v['parent_product_id']>0){
											$productNameData .= 	$IndexCount.") ".$prodsArr[$v['parent_product_id']]['Title']." ".$prodsArr[$v['ProductID']]['Title']."<br>";
										}else{
											$productNameData .= 	$IndexCount.") ".$prodsArr[$v['ProductID']]['Title']."<br>";
										}
								$IndexCount++;
								}
								echo $productNameData;
									
							}else{
										
									$productsList = GetMltRcrdsOnCndiWthOdr(CHANGE_REQ, "transctionIDRequest = '$TransactionID'", "Id", "ASC");
											
										 foreach($productsList as $products) {
											echo "Change request for ".$prodsArr[$products['ProductID']]['Title']." for order #".$products['OrderID']."<br>";
										} 
									
									
									}
									
							?>
                            </p>
                            <label>Paid on:</label>
                            <p>
								<?=date('l m/d/Y', $transactionData['Createdon']);?><br>
                            	<?=date('g:i A', $transactionData['Createdon']);?>
                            </p>
                            <label>Method :</label>
                            <p><?=ucfirst($transactionData['PaymentMethod']);?> checkout</p>
                            <label><?=ucfirst($transactionData['PaymentMethod']);?> Transaction ID :</label>
                            <p><?=$transactionData['TransactionID'];?></p>
                        </div>
                    </div>
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
</body>

</html>