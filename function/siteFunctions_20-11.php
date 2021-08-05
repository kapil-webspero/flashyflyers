<?php

	###@@@ DATE TIME FUNCTIONS	
	function DyMonYrFromTime($time) {
		$newdate = date("jS F Y", $time);
		return $newdate;
	}
	function DyMonYrTmFromTime($time) {
		$newdate = date("jS F Y H:i:s A", $time);

	}
	function DyMonYr($date) {
		$newdate = date("jS F Y",strtotime($date));
		return $newdate;
	}
	function DyMonYrTm($date) {
		$newdate = date("jS F Y H:i:s A",strtotime($date));
		return $newdate;
	}
	
	###@@@ ARRAY FUNCTIONS 

	function array_push_assoc($array, $key, $value){
		$array[$key] = $value;
		return $array;
	}
	function myUrlEncode($string) {
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%20', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "-", "$", ",", "/", "?", "%", "#", "[", "]");
		$string = str_replace($entities, $replacements, urlencode($string));		
		return str_replace('+','-',$string);
	}

	###@@@ GENRATE STRING
	function GenRandomString($length = 15) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	function clean($string) {
		$string = strtolower($string);
		$string = str_replace(' ', '-', $string); 
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
	}
    function removeslashs($string) {
		$string = strtolower($string);
		$string = str_replace('/n', '', $string); 
		$string = str_replace('/r', '', $string); 
		$string = str_replace('rn', '', $string);
		return $string; //
	}
	
	###@@@ DELETE RECORD FUNCTIONS	
	function DltAlRcrds($tName) {
		$query = mysql_query("DELETE FROM ".$tName) or die(mysql_error());
		if($query)
			$_SESSION['SUCCESS'] = 'Records Delete Successfuly !';
		else
			$_SESSION['ERROR'] = 'Opps Somthing was worng. Try again !';
	}

	function DltSglRcrd($tName, $cndy) {
		$query = mysql_query("DELETE FROM ".$tName." WHERE ".$cndy) or die(mysql_error());
		if($query)
			$_SESSION['SUCCESS'] = 'Record Delete Successfuly !';
		else
			$_SESSION['ERROR'] = 'Opps Somthing was worng. Try again !';
	}

	###@@@ FETCH MULTI RECORDS FUNCTIONS	
	function GetMltRcrds($tName) {
		$query = mysql_query("SELECT * FROM ".$tName) or die(mysql_error());
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
	}


	function GetMltRcrdsWthOdr($tName, $OrderFeild, $OrderType) {
		$query = mysql_query("SELECT * FROM ".$tName." ORDER BY ".$OrderFeild." ".$OrderType);
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
	}

	function GetMltRcrdsOnCndi($tName, $cndy) {
      	$query = mysql_query("SELECT * FROM ".$tName." WHERE ".$cndy) or die(mysql_error());
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
	}
	
	function ExecCustomQuery($sql) {
		$query = mysql_query($sql) or die(mysql_error());
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
	}

	function MyDownloads($tName, $user, $downloads) {
		$downloadList=array_map('intval', explode(',', $downloads));
		$downloadList = implode("','",$downloadList);
		$query=mysql_query("select * from ".$tName." where ID in ('".$downloadList."')") or die(mysql_error());
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
				$data[] = $row;
			}
		return $data;
	}

	function GetMltRcrdsOnCndiWthOdr($tName, $cndy, $OrderFeild, $OrderType) {
		$query = mysql_query("SELECT * FROM ".$tName." WHERE ".$cndy." ORDER BY ".$OrderFeild." ".$OrderType) or die(mysql_error());
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
	}

	function GetMltRcrdsWthSmFlds($tName, $Fields) {
		$query = mysql_query("SELECT ".$Fields." FROM ".$tName) or die(mysql_error());
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
	}

	function GetMltRcrdsWthSmFldsOnCndi($tName, $cndy, $Fields) {
		//echo "SELECT ".$Fields." FROM ".$tName." WHERE ".$cndy;
		$query = mysql_query("SELECT ".$Fields." FROM ".$tName." WHERE ".$cndy);
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
	}

	function GetMltRcrdsWthSmFldsOnCndiWthOdr($tName, $cndy, $Fields, $OrderFeild, $OrderType) {
		$query = mysql_query("SELECT ".$Fields." FROM ".$tName." WHERE ".$cndy." ORDER BY ".$OrderFeild." ".$OrderType);
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
	}

	###@@@ FETCH SINGLE RECORD FUNCTIONS	
	function GetSglRcrd($tName, $id) {
		$query = mysql_query("SELECT * FROM ".$tName." WHERE ID = '$id' ") or die(mysql_error());
		$row = mysql_fetch_assoc($query);
		return $row;
	}

	function GetSglRcrdOnCndi($tName, $cndy) {
	   	$query = mysql_query("SELECT * FROM ".$tName." WHERE ".$cndy) or die(mysql_error());
		$row = mysql_fetch_assoc($query);
		return $row;
	}

	function GetSglRcrdWthSmFldsOnCndi($tName, $cndy, $Fields) {
		$query = mysql_query("SELECT ".$Fields." FROM ".$tName." WHERE ".$cndy) or die(mysql_error());
		$row = mysql_fetch_assoc($query);
		return $row;
	}

	function GetSglDataOnCndi($tName, $cndy, $Field ){
		$query = mysql_query("SELECT ".$Field." FROM ".$tName." WHERE ".$cndy) or die(mysql_error());
		$row = mysql_fetch_array($query);
		return $row[0];
	}

	function GetSglRcrdOnCndiWthOdr($tName, $cndy, $OrderFeild, $OrderType) {
		$query = mysql_query("SELECT * FROM ".$tName." WHERE ".$cndy." ORDER BY ".$OrderFeild." ".$OrderType." LIMIT 1") or die(mysql_error());
		$row = mysql_fetch_array($query);
		return $row;
	}

	###@@@ UPDATE RECORD FUNCTIONS	
	function UpdateRcrdOnCndi($tName, $data, $cndy) {
	    $query = mysql_query("UPDATE ".$tName." SET ".$data." WHERE ".$cndy) or die(mysql_error());
		if($query)
			$_SESSION['SUCCESS'] = 'Detail Update Successfully';
		else
			$_SESSION['ERROR'] = 'Opps Something was worng with you. Try again';
	}

	###@@@ INSERT RECORD FUNCTIONS
	function InsertRcrds($fn,$fv,$tName) {
 		$sql="INSERT INTO ".$tName." (";  
		for($a=0;$a<sizeof($fn);$a++) {
			$sql.="`".$fn[$a]."`";
			if($a!=(sizeof($fn)-1)) {
				$sql.=",";
			}
		}  
		$sql.=") VALUES (";
		for($a=0;$a<sizeof($fv);$a++) {
			$sql.="'".mysql_real_escape_string(trim($fv[$a]))."'";
			if($a!=(sizeof($fv)-1)) {
				$sql.=",";
			}
		}  
    	$sql.=")"; 

		$query = mysql_query($sql)or die(mysql_error());

		if($query) 
			return "success";
		else 
			return "error";
	}

	function InsertRcrdsGetID($fn,$fv,$tName) {
 		$sql="INSERT INTO ".$tName." (";  
		for($a=0;$a<sizeof($fn);$a++) {
			$sql.="`".$fn[$a]."`";
			if($a!=(sizeof($fn)-1)) {
				$sql.=",";
			}
		}  
		$sql.=") VALUES (";
		for($a=0;$a<sizeof($fv);$a++) {
			$sql.="'".mysql_real_escape_string(trim($fv[$a]))."'";
			if($a!=(sizeof($fv)-1)) {
				$sql.=",";
			}
		}  
    	$sql.=")"; 
		mysql_query($sql)or die(mysql_error());
		$InsertId = mysql_insert_id();
		if($InsertId > 0) 
			return $InsertId;
		else 
			return "error";
	}

	function InsertRcrdsByData($tName, $data) {
 		$sql="INSERT INTO ".$tName." SET ".$data;  
		mysql_query($sql)or die(mysql_error());
		$InsertId = mysql_insert_id();
		if($InsertId > 0) 
			return $InsertId;
		else 
			return "error";
	}

	###@@@ FETCH NUM OF RECORDS
	function GetNumOfRcrds($tName) {
		$query = mysql_query("SELECT * FROM ".$tName."") or die(mysql_error());
		return mysql_num_rows($query);
	}

	function GetNumOfRcrdsOnCndi($tName, $cndy) {
	   
		$query = mysql_query("SELECT * FROM ".$tName." WHERE ".$cndy) or die(mysql_error());
		return mysql_num_rows($query);
	}

	function GetSumOnCndi($tName, $column, $cndy) {
		$query = mysql_query("SELECT SUM($column) as totalcount FROM ".$tName." WHERE ".$cndy);
		$totalCnt = 0;
		$row = mysql_fetch_assoc($query);
		if(!empty($row["totalcount"])) {
			$totalCnt = intval($row["totalcount"]);
		}
		return $totalCnt;
		
	}
	function recursiveRemoveDirectory($directory)
	{
		foreach(glob("{$directory}/*") as $file)
		{
			if(is_dir($file)) { 
				recursiveRemoveDirectory($file);
			} else {
				unlink($file);
			}
		}
		rmdir($directory);
	}
	###@@@ SEND EMAIL
	function SendEmail($email,$subject,$message) {
		$mail = new PHPMailer;
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = SMTP_HOST;  // Specify SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = SMTP_USER;                 // SMTP username
		$mail->Password = SMTP_PASS;                           // SMTP password
		$mail->Port = SMTP_PORT;                                    // TCP port to connect to
		
		$mail->setFrom(SMTP_SENDER, $subject);
		$mail->addAddress($email, $name);     // Add a recipient
		$mail->addAddress($email);               // Name is optional
		$mail->addReplyTo(SMTP_SENDER, 'Information');
		$mail->isHTML(true);                                  // Set email format to HTML
		
		$mail->Subject = $subject;
		$mail->Body    = $message;
		$mail->AltBody = $message;
		
		if(!$mail->send()) {
			return "error";
		} 
		else {
			return "success";
		}
	}

	function GetPageUrl(){
		return sprintf(
		"%s://%s%s",
		isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
		$_SERVER['SERVER_NAME'],
		$_SERVER['REQUEST_URI']
		);
	}
	
	#### CATEGORY FUNCTIONS
	function categoryNameFromId($ID) {
		$query = mysql_query("SELECT Name FROM ".PRODUCT_TYPE." WHERE ID = '$ID'");
		$row = mysql_fetch_assoc($query);
		return $row['Name'];
	}
	function getOptionValue($ID) {
		$query = mysql_query("SELECT option_price FROM ".OPTION_PRICE." WHERE ID = '$ID'");
		$row = mysql_fetch_assoc($query);
		return $row['option_price'];
	}
	
	function getProductArray() {
		$productList = GetMltRcrdsOnCndi(PRODUCT, "Status= '1'");
		foreach($productList as $productArr) {
			$prodArr[$productArr['id']] = $productArr;
		}
		return $prodArr;
	}
	function getProductTypeArray($ID) {
		//
		$query = mysql_query("SELECT Name FROM ".PRODUCT_TYPE." WHERE ID = '$ID'");
		$row = mysql_fetch_assoc($query);
		return $row['Name'];
	}
	function transactionData($ID) {
		$query = mysql_query("SELECT `designer_remain_payment` FROM ".DESIGN_TRANSACTION." WHERE `designer_id` = '$ID' ORDER BY `ID` DESC LIMIT 1");
		$row = mysql_fetch_assoc($query);
		return $row['designer_remain_payment'];
	}
	
	function getProductTypeArrayValue($ID) {
		//
		$query = mysql_query("SELECT design_labor_cost FROM ".PRODUCT_TYPE." WHERE ID = '$ID'");
		$row = mysql_fetch_assoc($query);
		return $row['design_labor_cost'];
	}
	function usernameFromId($userId) {
		$query = mysql_query("SELECT username FROM ".USERS." WHERE ID = '$userId'");
		$row = mysql_fetch_assoc($query);
		return $row['username'];
	}
	
	function usernameList($userId) {
		$query = mysql_query("SELECT * FROM ".USERS." WHERE UserID = '$userId'");
		$row = mysql_fetch_assoc($query);
		return $row['FName']." ".$row['LName'];
	}
	
	function syncMailchimp($email,$apiKey,$listId,$fname,$lname) { 
		$memberId = md5(strtolower($email));
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;
	
		$json = json_encode(array(
					'email_address' => $email,
					
					'status'        => 'subscribed',
					'merge_fields'  => array(
					'MEMBER' => 0,
					'FNAME'=>$fname,
					'LNAME'=>$lname,
				)));

		$ch = curl_init($url);
	
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);  
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	
    	return array('code'=>$httpCode,'result'=>$result);
    }
	function createVideoThumb($videoPath,$thumbPath,$width=200,$height=200,$frameTime) {
		$thumbSize = $width."x".$height;
		//remove previous thumb
		if(file_exists($thumbPath)) {
			unlink($thumbPath); 
		}
		$cmd = "ffmpeg -i $videoPath -deinterlace -an -ss $frameTime -t 00:00:01  -s $thumbSize -r 1 -y -vcodec mjpeg -f mjpeg $thumbPath 2>&1";
		exec($cmd, $output, $retval);
		return $retval;
	}
    function printImage($src) {
		if(!empty($src)) {
			return $src;
		} else {
			return SITEURL.'images/noPhotoFound.png';
		}
    }
	function allDesignTransactions($ID) {
		$transDatas = GetMltRcrdsOnCndi(DESIGN_TRANSACTION, "30");
		foreach($transDatas as $transData){
			$transID[] = array("id" =>$transData['designer_id'], "designer_payment" => $transData['designer_payment'], "designer_remain_payment" => $transData['designer_remain_payment'], "desigenr_date" => $transData['desigenr_date']);
		}
		return $transID;
	}
  	function getProdSizeArr() {
		$sizeList = GetMltRcrdsOnCndi(PRODUCT_SIZE, "1");
		foreach($sizeList as $sizeArr) {
			$sizeNArr[$sizeArr['ID']] = array("id" =>$sizeArr['ID'], "name" => $sizeArr['Size'], "price" => $sizeArr['Price']);
		}
		return $sizeNArr;
	}
	function getProdTypeArr() {
		$typeList = GetMltRcrdsOnCndi(PRODUCT_TYPE, "1");
		foreach($typeList as $typeArr) {
			$typeNArr[$typeArr['ID']] = array("id" =>$typeArr['ID'], "name" => $typeArr['Name'], "price" => $typeArr['Price']);
		}
		return $typeNArr;
	}
	
	function getProdTypeParentArr() {
		$typeList = GetMltRcrdsOnCndi(PRODUCT_TYPE, "parent_id='0'");
		foreach($typeList as $typeArr) {
			$typeNArr[$typeArr['ID']] = array("id" =>$typeArr['ID'], "name" => $typeArr['Name'], "price" => $typeArr['Price'], "design_labor_cost" => $typeArr['design_labor_cost']);
		}
		return $typeNArr;
	}
	
	
	function getProdOptionTypeArr() {
		$typeList = GetMltRcrdsOnCndi(OPTION_PRICE, "1");
		foreach($typeList as $typeArr) {
			$typeNArr[$typeArr['ID']] = array("id" =>$typeArr['ID'], "name" => $typeArr['option_name'], "price" => $typeArr['option_price']);
		}
		return $typeNArr;
	}
	function getProdAddonArr() {
		$addonList = GetMltRcrdsOnCndi(ADDON_PRICE, "1");
		foreach($addonList as $addonArr) {
			$addonNArr[$addonArr['id']] = array("id" =>$addonArr['id'], "name" => $addonArr['price_key'], "price" => $addonArr['price_value']);
		}
		return $addonNArr;
	}
    function getFlyers() {
        $flyersList = GetMltRcrdsOnCndi(FLYERS, "1 ORDER BY `tbl_flyers_dev`.`sort_order` ASC");
        foreach($flyersList as $flyerArr) {
            $flyerNArr[$flyerArr['id']] = array("id" =>$flyerArr['id'], "image_path" => $flyerArr['image_path'], "alt" => $flyerArr['alt'], "sort_order" => $flyerArr['sort_order'], "is_active" => $flyerArr['is_active']);
        }
        return $flyerNArr;
    }
  	function getAddonPriceArr() {
		$priceList = GetMltRcrdsOnCndi(ADDON_PRICE, "1");
		foreach($priceList as $priceArr) {
			$priceNArr[$priceArr['id']] = $priceArr['price_value'];
		}
		return $priceNArr;
	}
	
  	function getAddonArr() {
		$addonList = GetMltRcrdsOnCndi(PRODUCT, "`Addon` = '1'");
		foreach($addonList as $addonData) {
			$addonArr[$addonData['id']] = $addonData;
		}
		return $addonArr;
	}
	function getProductArr() { 
		$productList = GetMltRcrdsOnCndi(PRODUCT, "`Addon` = '0'");
		foreach($productList as $productData) {
			$productArr[$productData['id']] = $productData;
		}
		return $productArr;
	}
	
	function getALLProductArr() { 
		$productList = GetMltRcrdsOnCndi(PRODUCT, "`id` != ''");
		foreach($productList as $productData) {
			$productArr[$productData['id']] = $productData;
		}
		return $productArr;
	}
	function getUserArr() {
		$userList = GetMltRcrdsOnCndi(USERS, "1");
		foreach($userList as $userData) {
			$userArr[$userData['UserID']] = $userData;
		}
		return $userArr;
	}
	function is_cart_empty() {
		if((isset($_SESSION['CART']) && count($_SESSION['CART'])>0) || (isset($_SESSION['CartRequest']) && count($_SESSION['CartRequest'])>0)) {
			return false;
		} else {
			return true;
		}
	}
		function is_login(){
		if(isset($_SESSION['loginType']) && $_SESSION['loginType']=="guest"){
			
			return false;
			}
		if(isset($_SESSION['userId']) && !empty($_SESSION['userId'])) {
			return true;
		} else {
			return false;
		}
	}
	function printSession(){
		echo '<pre>';
		print_r($_SESSION);
		echo '</pre>';	
	}
	$addonPrices = getAddonPriceArr();
	
	$turnAround1 = $addonPrices['5'];
	$turnAround2 = $addonPrices['6'];
	$turnAround3 = $addonPrices['7'];
	$turnAround4 = $addonPrices['8'];  
	
	function currentUrl( $trim_query_string = false ) {
		$pageURL = (isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		if( ! $trim_query_string ) {
			return $pageURL;
		} else {
			$url = explode( '?', $pageURL );
			return $url[0];
		}
	}
	
	function formatPrice($price) {
		/*if(is_numeric( $price ) && floor( $price ) != $price) { return $price; }
		else { return number_format($price,2); } */
		//return (floatval($price) && intval($price)!=floatval($price) ? number_format($price,2): $price);
		return number_format($price,2);

	}
	function grab_image($url,$saveto){
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$raw=curl_exec($ch);
		curl_close ($ch);
		if(file_exists($saveto)){
			unlink($saveto);
		}
		$fp = fopen($saveto,'x');
		fwrite($fp, $raw);
		fclose($fp);
	}
	$adminEmail = "support@flashyflyers.com";
	
function getTurnArroundTypeByID($id){
	 $array = array("1"=>'12 Hours Same-Day',"2"=>'24 hours',"3"=>'2-3 business days',"4"=>'3-5 business days');
	 
	 return $array[$id];
	 
	
}
function getCurPageURL() {
	$pageURL = 'http';
	if(isset($_SERVER["HTTPS"]))
	if ($_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}
/* Array Sorting */ 
	function rangerServerUrlGetImage($url){
		
		$headers = array(
		"Range: bytes=0-32768"
		);
	
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($curl);
		curl_close($curl);
		return $data;
	}
	
	function sortArrayByColumnValue($a, $b)
	{
		if ($a["sort_order"] == $b["sort_order"]) {
			return 0;
		}
		return ($a["sort_order"] < $b["sort_order"]) ? -1 : 1;
	}  
	
/* Array Sorting */ 

function httpToSecure($url){
	
	return str_replace("http://","//",$url);
}	

function limit_text($text, $limit) {
	    if (str_word_count($text, 0) > $limit) {
          $words = str_word_count(strip_tags($text), 2);
          $pos = array_keys($words);
          $text = substr($text, 0, $pos[$limit]);
      }
	 // $text =substr($text,0,100);
      return $text;
    }	
	
function random_password( $length = 8 ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    $password = substr( str_shuffle( $chars ), 0, $length );
    return $password;
}

function getProductTypesPathById($iCatId)
{
	
	$query = mysql_query("SELECT path FROM ".PRODUCT_TYPE." WHERE ID = '$iCatId'");
	$row = mysql_fetch_assoc($query);
	return $row['path'];
}


function getProductTypesDetails($whereClause="")
{
	
	
		$query = mysql_query("SELECT * FROM ".PRODUCT_TYPE." ". $whereClause);
		$db_cat_rs = array();
		while($row = mysql_fetch_assoc($query)) {
			$db_cat_rs[] = $row;
		}
		
	$cat_assoc_arr = array();
	for($c=0 , $nc=count($db_cat_rs) ; $c<$nc ; $c++)
	{
		$cat_assoc_arr[$db_cat_rs[$c]['parent_id']][] = $db_cat_rs[$c];
	}
	return $cat_assoc_arr;
}


function getParentProductTypesList($parent_id=0, $old_cat="",$iCatIdNot="0", $loop=1, $maxloop=5)
{
	global $cat_assoc_arr, $par_cat_array;
	if($loop<=$maxloop && is_array($cat_assoc_arr[$parent_id]))
	{
		foreach($cat_assoc_arr[$parent_id] as $Pid=>$db_cat_rs)
		{
			if($iCatIdNot != $db_cat_rs['ID'])
			{  
				$par_cat_array[]=array('ID'=> $db_cat_rs['ID'], 'path' =>  $old_cat."".$db_cat_rs['Name'], 'loop'=>$loop);
				getParentProductTypesList($db_cat_rs['ID'], $old_cat."&nbsp;&nbsp;&nbsp;&nbsp;",$iCatIdNot,$loop+1, $maxloop);
			}
		}
	}
	$old_cat = "";
	return $par_cat_array;
}

function getParentProductTypesListFront()
{
		$query = mysql_query("SELECT * FROM ".PRODUCT_TYPE." where parent_id='0' order by Name asc");
		
		while($row = mysql_fetch_assoc($query)) {
			$db_cat_rs[$row['ID']]['main_cat'] = $row;
			$querySub = mysql_query("SELECT * FROM ".PRODUCT_TYPE." where parent_id='".$row['ID']."' order by name asc");
			while($rowSub = mysql_fetch_assoc($querySub)) {
				$db_cat_rs[$row['ID']]['sub_cat'][] = $rowSub;
			}
				
		}
	
	return $db_cat_rs;
}


function getProductActiveTags()
{
	
	
		$query = mysql_query("SELECT * FROM ".PRODUCT_TAGS." where TagStatus='Active' order by TagName asc");
		$db_cat_rs = array();
		while($row = mysql_fetch_assoc($query)) {
			$db_cat_rs[] = $row;
		}
		
	return $db_cat_rs;
}

function getCategoryByParentID($iCatId)
{
	
	$query = mysql_query("SELECT * FROM ".CATEGORIES." WHERE parent_id = '$iCatId'");
	$db_cat_rs = array();
		while($row = mysql_fetch_assoc($query)) {
			$db_cat_rs[] = $row;
		}
	return	$db_cat_rs;
}
function getProductTagsByTags($tags){
	
	if(!empty($tags)){
		$tagsIN = implode("','",explode(",",$tags));
		
			$query = mysql_query("SELECT * FROM ".PRODUCT_TAGS." WHERE Id IN('".$tagsIN."')");
	$db_cat_rs = array();
		while($row = mysql_fetch_assoc($query)) {
			$db_cat_rs[] = $row;
		}
	return	$db_cat_rs;	
	}
		
}

function getSettingFormFields($parent_id,$ID){
	
		$query = mysql_query("SELECT * FROM ".PRODUCT_TYPE." WHERE parent_id ='".$parent_id."' and ID='".$ID."'");
		$db_cat_rs = array();
		$row = mysql_fetch_assoc($query);
		if(empty($row)){
			$query = mysql_query("SELECT * FROM ".PRODUCT_TYPE." WHERE ID='".$parent_id."'");
			$row = mysql_fetch_assoc($query);
		
		}
		$ReturnArray = array();
		if(!empty($row) && !empty($row['fieldsSettings'])){
			$fieldsSettings = unserialize($row['fieldsSettings']);
			$ReturnArray = $fieldsSettings;	
		}
	return	$ReturnArray;	
	
		
}

function getAdvanceProductSearch($cndy) {

      	$query = mysql_query("SELECT p.*,count(o.Id) as totalProductSale FROM ".PRODUCT." as p left join ".ORDER." as  o on p.Id=o.ProductID  WHERE ".$cndy) or die(mysql_error());
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
}

function getAdvanceProductSearchCount($cndy) {
		$query = mysql_query("SELECT p.*,count(o.Id) as totalProductSale FROM ".PRODUCT." as p left join ".ORDER." as  o on p.Id=o.ProductID  WHERE ".$cndy) or die(mysql_error());
		return mysql_num_rows($query);
	}

function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}

function getProductReviewCountByProductID($productID){
	
		$tableProduct = PRODUCT;
	$tableUser = USERS;
	$searchTable = PRODUCTS_REVIEW;

$query = mysql_query("SELECT SUM(RP.Rating) as total, COUNT(RP.ReviewID) as num FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID WHERE RP.ReviewProductID='".$productID."' and RP.ReviewStatus='1'") or die(mysql_error());
		
		$row = mysql_fetch_assoc($query);
		
		return $row;
		
		
		
}



function getProductReviewByProductID($productID,$limit) {

	$tableProduct = PRODUCT;
	$tableUser = USERS;
	$searchTable = PRODUCTS_REVIEW;

      	$query = mysql_query("SELECT RP.ReviewID,RP.Rating,RP.ReviewDescription,RP.ReviewStatus,RP.ReviewDate,P.Title,U.FName,U.LName,U.Email FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID WHERE RP.ReviewProductID='".$productID."' and RP.ReviewStatus='1' order by RP.ReviewDate desc ".$limit) or die(mysql_error());
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
}

function getProductReviewByOrderID($OrderID,$productID=0) {
	$tableProduct = PRODUCT;
	$tableUser = USERS;
	$searchTable = PRODUCTS_REVIEW;
		if($productID>0){
			$query = mysql_query("SELECT RP.ReviewID,RP.Rating,RP.ReviewProductID,RP.ReviewDescription,RP.ReviewStatus,RP.ReviewDate,P.Title,P.slug,U.FName,U.LName,U.Email FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID WHERE RP.OrderID='".$OrderID."' and RP.ReviewProductID='".$productID."'  order by RP.ReviewDate desc") or die(mysql_error());

			
		}else{
			$query = mysql_query("SELECT RP.ReviewID,RP.Rating,RP.ReviewProductID,RP.ReviewDescription,RP.ReviewStatus,RP.ReviewDate,P.Title,P.slug,U.FName,U.LName,U.Email FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID WHERE RP.OrderID='".$OrderID."'  order by RP.ReviewDate desc") or die(mysql_error());
	
		}
      			$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
}

function getProductReviewOverAllRatings($productID) {

	$tableProduct = PRODUCT;
	$tableUser = USERS;
	$searchTable = PRODUCTS_REVIEW;

      	$query = mysql_query("SELECT RP.ReviewID,RP.Rating,RP.ReviewDescription,RP.ReviewStatus,RP.ReviewDate,P.Title,U.FName,U.LName,U.Email FROM ".$searchTable." as RP INNER JOIN ".$tableProduct." as P ON P.id =RP.ReviewProductID INNER JOIN ".$tableUser." AS U ON  U.UserID = RP.ReviewUserID WHERE RP.ReviewProductID='".$productID."' order by RP.ReviewDate desc") or die(mysql_error());
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data[] = $row;
		}
		return $data;
}

function checkproductSlug($tName, $column,$slug, $productID,$test= "") {
		
		
		$query = mysql_query("SELECT slug FROM ".$tName." WHERE slug ='".$slug."' and id!='".$productID."'");
		$totalCnt = 0;
		$totalrecord = mysql_num_rows($query);
		
		if($totalrecord>0){
			while($row = mysql_fetch_assoc($query)){
				$slugexplode =  end( explode( "-",$slug) );	
				if(is_numeric($slugexplode)){
					$slug = str_replace("-".$slugexplode,"",$slug)."-".($slugexplode+1)	;
					
					return  checkproductSlug($tName, $column,$slug, $productID,"raj");
				}else{
					
					if(is_numeric($slugexplode)){
						$slug = str_replace("-".$slugexplode,"",$slug)."-".($slugexplode+1)	;
					}else{
						$slug = $slug."-".($slugexplode+1)	;
					}
					
				
					return checkproductSlug($tName, $column,$slug, $productID,"niraj");
				}
			}
		}else{
			
			return $slug;	
		}
		
	}
	
function getReviewHtmlByProductIDOrderID($order_id,$productID){
	global $reviewStatus;
	
		$getProductsReview = getProductReviewByOrderID($order_id,$productID);
		$html= ""; 
		if(count($getProductsReview)>0) {
		
	            
                $html .='
                    <div class="ReviewAllListing">';
		
		foreach($getProductsReview as $reviews) {
				
				$selected1=$selected2=$selected3=$selected4=$selected5="";
				if($reviews['Rating']>=1){$selected1="selected";}
				if($reviews['Rating']>=2){$selected2="selected";}
				if($reviews['Rating']>=3){$selected3="selected";}
				if($reviews['Rating']>=4){$selected4="selected";}
				if($reviews['Rating']>=5 ){$selected5="selected";}
				
			
			$html .= '<div class="ReviewListingBlock">
                        	<div class="rating_star_review">
                                            <span data-id="100" data-val="1" class="'.$selected1.'" ></span>
                                            <span data-id="100" data-val="2" class="'.$selected2.'"></span>
                                            <span data-id="100" data-val="3" class="'.$selected3.'"></span>
                                            <span data-id="100" data-val="4" class="'.$selected4.'"></span>
                                            <span data-id="100" data-val="5" class="'.$selected5.'"></span>
                                        </div>
                            <div class="col-md-12">
                            	
								<div class="ReviewDate"><span>Review on </span><span class="ReviewDate">'.date("F j, Y",strtotime($reviews['ReviewDate'])).'</span></div>
                            	<div class="ReviewContent">'.stripcslashes($reviews['ReviewDescription']).'</div>
                            </div>            
                        </div>';	
		}
		$html .='</div>';
		
		
	
	}
	return $html;	
	
	
}

function getRelatedApproveImages($productID){
	$tableChangeReq = CHANGE_REQ;
	$tableOrder = ORDER;
	
	  	$query = mysql_query("SELECT Id FROM ".$tableOrder." WHERE `is_approve` = '1'") or die(mysql_error());
		
		$data = array();
		while($row2 = mysql_fetch_assoc($query)) {
				
				$query1 = mysql_query("SELECT Attachment FROM ".$tableChangeReq." WHERE  ResponseState='1' and ProductID='".$productID."' and Attachment!='a:0:{}' and OrderID='".$row2['Id']."' order by ID desc limit 1") or die(mysql_error());
				while($row = mysql_fetch_assoc($query1)) {
				
				
				$Attachment = unserialize($row['Attachment']);
				$ext= strtolower(pathinfo($Attachment[0], PATHINFO_EXTENSION));
				if(!empty($Attachment) && ($ext=="jpg" || $ext=="jpeg" || $ext=="png" || $ext=="gif" )){
						$data[] = $Attachment[0];
					}
				}
		}
		
		return $data;	
}
?>