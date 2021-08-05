<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
$page = 1;
if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
}
$items_per_page = 1;
$offset = ($page - 1) * $items_per_page;
	 $dir = SITE_BASE_PATH."uploads/products/".$data['prod_id']."/";
						echo $dir;
						digitalOceanUploadDir($dir,"products/".$data['prod_id']."/");
					
					
	
	$query = mysql_query("SELECT * FROM `tbl_prodphotos_dev` WHERE `filetype` = 'image' group by prod_id ORDER BY `tbl_prodphotos_dev`.`id` DESC  limit ".$offset.",1 ");
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data = $row;
		}
		
		if(!empty($data)){
						  mysql_query("UPDATE tbl_prodphotos_dev SET digital ='Yes' WHERE prod_id='".$data['prod_id']."'") or die(mysql_error());
						
						
					/*if (is_dir($dir)) {
						if ($dh = opendir($dir)) {
							while (($file = readdir($dh)) !== false) {
								if($file!=".." && $file!="."){
								if(digitalOceanCheckFile("products/".$data['prod_id']."/".$file)){
								
								}else{
										digitalOceanUploadImage($dir.$file,"products/".$data['prod_id']);
									}
								}
								echo "$file<br>";
							}
							  mysql_query("UPDATE tbl_prodphotos_dev SET digital ='Yes' WHERE prod_id='".$data['prod_id']."'") or die(mysql_error());
							closedir($dh);
						}
		}*/
		
		
		}else{
			echo "FINISH";
			die;
		}


$total = $page + 1;

?>
<script>window.location="http://localhost/flashyimagecode/script/script_product_images_digital.php?page=<?php  echo $total;?>";</script>
<?php 
exit;
?>    
	