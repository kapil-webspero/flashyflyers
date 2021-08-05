<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	
	if(isset($_POST['txn_id'])) {
		$tran=$_POST['txn_id'];
		UpdateRcrdOnCndi(TRANSACTION, "Status=1", "TransactionID='".$tran."'");
	}
?>	