<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	
	$orderTable = ORDER;
	$productTable = PRODUCT;	
		
	if(isset($_POST['viewType']) && $_POST['viewType']=="salesByCategory") {	
		
		$productCategories = GetMltRcrdsOnCndi(PRODUCT_TYPE," ID>'0'") ;
	
		$productCatArr = array();
		foreach($productCategories as $productCategory) {
			$productCatArr[$productCategory['ID']] = $productCategory['Name'];
		}
		
		$productTypes = GetMltRcrdsOnCndi(PRODUCT_TYPE," ID !='0'") ;
		
		$productTypesArray = array();
		foreach($productTypes as $single){
			$productTypesArray[$single['ID']] = $single;
		}
		
		
		$fields = '`ProductID`,count(*) as salesCount,sum(`TotalPrice`) as salesAmount,`parent_product_cat_id`';
		$cndy = "pd.id=od.ProductID GROUP BY `ProductID`";	
		$salesTypeData = GetMltRcrdsWthSmFldsOnCndi(ORDER." as od,".PRODUCT." as pd", $cndy, $fields);	
		
		$salesArr = array();
		
		foreach($salesTypeData as $salesData) {
			if(!empty($salesData['parent_product_cat_id'])) {
				$amt = $salesArr[$salesData['parent_product_cat_id']]+$salesData['salesCount'];
				$salesArr[$salesData['parent_product_cat_id']]= $amt;
			}
			else {
				$amt = $salesArr[0]+$salesData['salesCount'];
				$salesArr[0]= $amt;
			}
		}
		
	
		
		
		$dtArr = array();
		foreach($salesArr as $key=>$value) {
			if($key==0) {
				$kk = "Others";
			}
			else {  
				$kk = trim($productTypesArray[$key]['Name']);
			}
			$dtArr[$kk] = $value;
		}
		
		echo json_encode($dtArr);
	} 
	
	if(isset($_POST['viewType']) && isset($_POST['catId']) && $_POST['viewType']=="salesCategoryData") {	
		$fields = 'od.ProductID,od.OrderDate';
		$cndy = "od.ProductID=pd.id and pd.parent_product_cat_id=".$_POST['catId'];	
		$businessCatData = GetMltRcrdsWthSmFldsOnCndi("$orderTable as od,$productTable as pd", $cndy, $fields);			
		$month1 = 0;$month2 = 0;$month3 = 0;$month4 = 0;$month5 = 0;$month6 = 0;$month7 = 0;
		foreach($businessCatData as $businessData) {
			if($businessData['OrderDate']>=strtotime('first day of -6 months') && $businessData['OrderDate']<strtotime('first day of -5 months')) {
				$month7++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -5 months') && $businessData['OrderDate']<strtotime('first day of -4 months')) {
				$month6++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -4 months') && $businessData['OrderDate']<strtotime('first day of -3 months')) {
				$month5++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -3 months') && $businessData['OrderDate']<strtotime('first day of -2 months')) {
				$month4++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -2 months') && $businessData['OrderDate']<strtotime('first day of -1 months')) {
				$month3++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -1 months') && $businessData['OrderDate']<strtotime('first day of -0 months')) {
				$month2++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -0 months') && $businessData['OrderDate']<time()) {
				$month1++;
			}
		}
		$dtArr = array($month7,$month6,$month5,$month4,$month3,$month2,$month1);
		echo json_encode($dtArr);
	}
	
	if(isset($_POST['viewType']) && $_POST['viewType']=="salesCategoryDataOther") {	
		$fields = 'od.ProductID,od.OrderDate';
		$cndy = "od.ProductID=pd.id and pd.parent_product_cat_id NOT IN (1,2,3,4)";	
		$businessCatData = GetMltRcrdsWthSmFldsOnCndi("$orderTable as od,$productTable as pd", $cndy, $fields);			
		$month1 = 0;$month2 = 0;$month3 = 0;$month4 = 0;$month5 = 0;$month6 = 0;$month7 = 0;
		foreach($businessCatData as $businessData) {
			if($businessData['OrderDate']>=strtotime('first day of -6 months') && $businessData['OrderDate']<strtotime('first day of -5 months')) {
				$month7++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -5 months') && $businessData['OrderDate']<strtotime('first day of -4 months')) {
				$month6++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -4 months') && $businessData['OrderDate']<strtotime('first day of -3 months')) {
				$month5++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -3 months') && $businessData['OrderDate']<strtotime('first day of -2 months')) {
				$month4++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -2 months') && $businessData['OrderDate']<strtotime('first day of -1 months')) {
				$month3++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -1 months') && $businessData['OrderDate']<strtotime('first day of -0 months')) {
				$month2++;
			}
			elseif($businessData['OrderDate']>=strtotime('first day of -0 months') && $businessData['OrderDate']<time()) {
				$month1++;
			}
		}
		$dtArr = array($month7,$month6,$month5,$month4,$month3,$month2,$month1);
		echo json_encode($dtArr);
	}
	
?>