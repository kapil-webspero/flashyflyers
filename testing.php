<?php 
require_once 'function/constants.php';

require_once 'function/configClass.php';

require_once 'function/siteFunctions.php';
session_start();
echo "<pre>";
echo SITEURL;
print_r($_SESSION);
?>
