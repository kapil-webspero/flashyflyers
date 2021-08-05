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
	 
	
	$query = mysql_query("SELECT * FROM `tbl_prodphotos_dev` WHERE `filetype` = 'image' and filename LIKE '%cloudinary.com%' ORDER BY `tbl_prodphotos_dev`.`id` DESC limit ".$offset.",1");
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data = $row;
		}
		if(!empty($data)){
			 if (strpos($data['filename'],'res.cloudinary.com') !== false){
				$ProductID = $data['prod_id'];
				$target_dir = SITE_BASE_PATH."uploads/products/".$ProductID."/";	
				$target_dir_video = SITE_BASE_PATH."uploads/products/".$ProductID."/";
			
				if(!file_exists($target_dir)) {
					mkdir($target_dir, 0777, true);
				}
				
				$ext = pathinfo(basename($data['filename']), PATHINFO_EXTENSION);
				$ImageFileName = str_replace(".".$ext,"",basename($data['filename']));
		
				$image_url        = $data['filename']; // Define the image URL here
				$image_name       = $image_name;
				$image_data       = file_get_contents($image_url); // Get image data
				$unique_file_name = $target_dir_video.$ImageFileName.".".$ext; // Generate unique name
				$filename         = basename( $unique_file_name ); // Create image file name
				$file = $target_dir_video. '/' . $filename;
				file_put_contents( $file, $image_data );
				   $sourceProperties = getimagesize($file);
				  ProductImageThumbCreateForScript($sourceProperties,$ext,$file,$ImageFileName."_245X354",$target_dir_video,354);
				  ProductImageThumbCreateForScript($sourceProperties,$ext,$file,$ImageFileName."_389X568",$target_dir_video,568);
				
				  mysql_query("UPDATE tbl_prodphotos_dev SET filename ='".$filename."' WHERE id='".$data['id']."'") or die(mysql_error());
			}
		}else{
			echo "FINISH";
			die;
		}


$total = $page + 1;

?>
<script>window.location="http://localhost/flashyimagecode/script/script_product_images.php?page=<?php  echo $total;?>";</script>
<?php 
exit;
?>    
	