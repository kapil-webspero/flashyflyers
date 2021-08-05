<?php

	ob_start();

	require_once '../function/constants.php';

	require_once '../function/configClass.php';

	require_once '../function/siteFunctions.php';


	$AccessType = $_SESSION['userType'];
	$selectCat = GetMltRcrdsOnCndiWthOdr(PRODUCT_TYPE, "`parent_id` = '".$_REQUEST['id']."'", "Name", "Desc");
	if(!empty($selectCat)){
		$html = "<option value=''>Please select child product type</option>";	
		foreach($selectCat as $singleKey=>$singleValue){
			$selectedCat= "";
			if($_REQUEST['child_id']==$singleValue['ID']){
				$selectedCat = 'selected';	
			}
			$html .= "<option ".$selectedCat." value='".$singleValue['ID']."'>".$singleValue['Name']."</option>";	
		}
	}else{
		$html = "<option value='0'>Please select child product type</option>";	
	}
	
	
	
	$myarray = array("html" =>$html);

	echo json_encode($myarray);
?>