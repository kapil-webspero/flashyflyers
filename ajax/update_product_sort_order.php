<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
    $status = 'success';

    if(!empty($_REQUEST['product_id'])){
        $productId = $_REQUEST['product_id'];
        $sortOrder = !empty($_REQUEST['sort_order']) ? $_REQUEST['sort_order'] : "NULL";
        $query = mysql_query("UPDATE ".PRODUCT." SET sort_order=".$sortOrder." WHERE id =".$productId) or die(mysql_error());
    }
	$myarray = array("Status" => $status);
	echo json_encode($myarray);
?>