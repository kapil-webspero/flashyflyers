<?php 
require_once 'function/constants.php';
require_once 'function/configClass.php';
require_once 'function/siteFunctions.php';

$threeDays = strtotime(date("Y-m-d H:i:s", strtotime("-3 day")));
$OneDays = strtotime(date("Y-m-d H:i:s", strtotime("-2 day")));

//pending
$selectPendingOrder = ExecCustomQuery("SELECT * FROM `".CHANGE_REQ."` where ResponseState!='1' and CreationDate <'".$threeDays."'");

$selectPendingOrderArray = array();
if(!empty($selectPendingOrder)){
	foreach($selectPendingOrder as $single){
		$selectPendingOrderArray[$single['OrderID']][$single['ProductID']][$single['Size']][$single['TypeBanner']] = $single;
	}
}
if(!empty($selectPendingOrderArray)){
	foreach($selectPendingOrderArray as $singleOrderID){
		if(!empty($singleOrderID)){
			foreach($singleOrderID as $singleProductID){
				if(!empty($singleProductID)){
					foreach($singleProductID as $singleSize){
					if(!empty($singleSize)){
						foreach($singleSize as $singleSize2){
							$whereClasue = " OrderID='".$singleSize2['OrderID']."' and ProductID='".$singleSize2['ProductID']."' and ProductID='".$singleSize2['ProductID']."' and Size='".$singleSize2['Size']."' and TypeBanner='".$singleSize2['TypeBanner']."'";
							
							echo  "Oendig".SITEURL.'EmailTemplate/RivisionPending.php?OID='.$singleSize2['OrderID'];
							echo "<br>";
						$curl = curl_init();
						// Set some options - we are passing in a useragent too here
						curl_setopt_array($curl, array(
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_URL => SITEURL.'EmailTemplate/RivisionComplated.php?OID='.$singleSize2['OrderID'],
							CURLOPT_USERAGENT => 'Curl Test'
						));
						
					
						// Send the request & save response to $resp
						$resp = curl_exec($curl);
						// Close request to clear up some resources
						curl_close($curl);

							
							//UpdateRcrdOnCndi(CHANGE_REQ,"`OneDaysMail` = 'Yes'", $whereClasue);
		
						}
					}
						
				}
			}
			
			}
		}
	}
}


//one days mail
$selectOneDays = ExecCustomQuery("SELECT * FROM `".CHANGE_REQ."` where ResponseState!='1' and CreationDate <'".$OneDays."'");

$selectOneDaysArray = array();
if(!empty($selectOneDays)){
	foreach($selectOneDays as $single){
		$selectOneDaysArray[$single['OrderID']][$single['ProductID']][$single['Size']][$single['TypeBanner']] = $single;
	}
}
if(!empty($selectOneDaysArray)){
	foreach($selectOneDaysArray as $singleOrderID){
		if(!empty($singleOrderID)){
			foreach($singleOrderID as $singleProductID){
				if(!empty($singleProductID)){
					foreach($singleProductID as $singleSize){
					if(!empty($singleSize)){
						foreach($singleSize as $singleSize2){
							$whereClasue = " OrderID='".$singleSize2['OrderID']."' and ProductID='".$singleSize2['ProductID']."' and ProductID='".$singleSize2['ProductID']."' and Size='".$singleSize2['Size']."' and TypeBanner='".$singleSize2['TypeBanner']."'";
							
							echo  SITEURL.'EmailTemplate/RivisionPending.php?OID='.$singleSize2['OrderID'];
							echo "<br>";
						$curl = curl_init();
						// Set some options - we are passing in a useragent too here
						curl_setopt_array($curl, array(
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_URL => SITEURL.'EmailTemplate/RivisionPending.php?OID='.$singleSize2['OrderID'],
							CURLOPT_USERAGENT => 'Curl Test'
						));
						
					
						// Send the request & save response to $resp
						$resp = curl_exec($curl);
						// Close request to clear up some resources
						curl_close($curl);

							
							UpdateRcrdOnCndi(CHANGE_REQ,"`OneDaysMail` = 'Yes'", $whereClasue);
		
						}
					}
						
				}
			}
			
			}
		}
	}
}




?>