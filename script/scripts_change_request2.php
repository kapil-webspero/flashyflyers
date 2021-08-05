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
	 
	
	$query = mysql_query("SELECT * FROM `tbl_change_req` WHERE `AttachmentThumb` LIKE '%cloudinary%' ORDER BY `ID` ASC  limit ".$offset.",1");
		$data = array();
		while($row = mysql_fetch_assoc($query)) {
			$data = $row;
		}
		if(!empty($data)){
			$Attachment = unserialize($data['AttachmentThumb']);
				$AttachmentArray= array();
			
			if(!empty($Attachment) && is_array($Attachment)){
		 		foreach($Attachment as $singleKey=>$singleAttachement){
					$AttachmentArray[$singleKey]['small'] = $singleAttachement['small'];	
					$AttachmentArray[$singleKey]['large'] = $singleAttachement['large'];	
				if (strpos($singleAttachement,'res.cloudinary.com') !== false){
				$target_dir = SITE_BASE_PATH."uploads/work/";	
				$target_dir_video = SITE_BASE_PATH."uploads/work/";
			
				if(!file_exists($target_dir)) {
					mkdir($target_dir, 0777, true);
				}
				
				$ext = pathinfo(basename($singleAttachement['large']), PATHINFO_EXTENSION);
				$ImageFileName = str_replace(".".$ext,"",basename($singleAttachement['large']));
				if(strtolower($ext)=="jpg" || strtolower($ext)=="jpeg" || strtolower($ext)=="png" || strtolower($ext)=="gif"){
				$image_url        = $singleAttachement['large']; // Define the image URL here
				$image_name       = $image_name;
				$image_data       = file_get_contents($image_url); // Get image data
				$unique_file_name = $target_dir_video.$ImageFileName.".".$ext; // Generate unique name
				$filename         = basename( $unique_file_name ); // Create image file name
				$file = $target_dir_video. '/' . $filename;
				file_put_contents( $file, $image_data );
				   $sourceProperties = getimagesize($file);
					 ImageThumbCreate($sourceProperties,$ext,$file,$ImageFileName,$target_dir_video);
					$AttachmentArray[$singleKey]['large'] = $filename;
					$AttachmentArray[$singleKey]['small'] = $ImageFileName."_thum.".$ext;
				
			//echo "UPDATE tbl_orders SET ResponseFile ='".$filename."',finish_script='Yes' WHERE id='".$data['id']."'";
				//  mysql_query("UPDATE tbl_orders SET ResponseFile ='".$filename."',finish_script='Yes' WHERE id='".$data['id']."'") or die(mysql_error());
				  }
				
			}
				}
				echo "UPDATE tbl_change_req SET AttachmentThumb ='".serialize($AttachmentArray)."' WHERE id='".$data['ID']."'";
				if(!empty($AttachmentArray)){
					 mysql_query("UPDATE tbl_change_req SET AttachmentThumb ='".serialize($AttachmentArray)."' WHERE ID='".$data['ID']."'") or die(mysql_error());
				}
			
			}
		}else{
			echo "FINISH";
			die;
		}


$total = $page + 1;

?>
<script>window.location="http://localhost/flashyimagecode/script/scripts_change_request2.php?page=<?php  echo $total;?>";</script>
<?php 
exit;
?>    
	