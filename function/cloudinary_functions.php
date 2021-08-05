<?php
// Fallback to legacy autoloader

    require_once __DIR__.'/../cloudinary_php-master/autoload.php';

if (file_exists(__DIR__.'/../cloudinary_php-master/settings.php')) {
    include __DIR__.'/../cloudinary_php-master/settings.php';
}

$default_upload_options = array('tags' => 'basic_sample');
$eager_params = array('width' => 200, 'height' => 150, 'crop' => 'scale');
$files = array();


/**
 * This function, when called uploads all files into your Cloudinary storage and saves the
 * metadata to the $files array.
 */
function do_uploads($file_path,$file_name)
{
	$fileType = strtolower(pathinfo(basename($file_path),PATHINFO_EXTENSION));
	
		
		
    global $files, $sample_paths, $default_upload_options, $eager_params;

    if($fileType=="mp4"){
			$default_upload_options["resource_type"] = "video";
	}
	

    # Same image, uploaded with a public_id
    $files['named_local'] = \Cloudinary\Uploader::upload(
        $file_path,
        array_merge(
            $default_upload_options,
            array('public_id' => $file_name)
        )
    );
	
	return $files['named_local']['url'];

  
}
function delete_uploads($file_name)
{
	global $files, $sample_paths, $default_upload_options, $eager_params;
	$files['named_local'] =\Cloudinary\Uploader::destroy($file_name);
}


?>