<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	$carts = $_SESSION['CART'];
	session_destroy();
	ob_start();
	session_start();
	$_SESSION['CART'] = $carts;
	header("location:".SITEURL);
?>