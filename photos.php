<?php
ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	
	require_once 'function/cloudinary_functions.php';
			$query = "SELECT * FROM  tbl_prodphotos where filetype='image' and prod_id=3";
			$fetch_photos = mysql_query($query);
			while($arr_photos=mysql_fetch_array($fetch_photos))
			{
				//if (strpos($arr_photos['filename'],'res.cloudinary.com') !== false){
				//}
				//else
				//{
					
					$filesize = filesize(getcwd() . DIRECTORY_SEPARATOR."../uploads/products/".$arr_photos['prod_id']."/".$arr_photos['filename']) * .0009765625;
					
					if($filesize<=10000)
					{
					$filename_arr = explode('.',$arr_photos['filename']);
					echo $arr_photos['filename'];
					echo $upload = do_uploads(getcwd() . DIRECTORY_SEPARATOR."../uploads/products/".$arr_photos['prod_id']."/".$arr_photos['filename'],$filename_arr[0].'_small');
					//$up = "update tbl_prodphotos set filename='".$upload."' where id=".$arr_photos['id'];
					//mysql_query($up);
					}
					//}
			}
?>