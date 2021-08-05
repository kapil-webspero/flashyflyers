<?php
session_start();
ob_start();

require_once '../function/constants.php';
require_once '../function/configClass.php';

require_once '../function/siteFunctions.php';

require_once '../function/adminSession.php';
//echo phpinfo();

$PageTitle = "Update Product";
if(!isset($_SESSION['EDITPRODUCTID']) && empty($_SESSION['EDITPRODUCTID'])) {
    echo '<script>window.history.back();</script>';
}
$ProductID = intval($_SESSION['EDITPRODUCTID']);
$target_dir = "uploads/products/".$ProductID."/";
$target_dir_video = SITE_BASE_PATH."uploads/products/".$ProductID."/";

    if(!file_exists("../".$target_dir)) {
        mkdir("../".$target_dir, 0777, true);
    }
    
if(isset($_POST['uploadimage'])) {
    //$target_dir = "../uploads/products/".$ProductID;
    
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION));
    $ImageNewName = time();
    $file_name = $ImageNewName.".".$imageFileType;
    $target_file = $target_dir . $file_name;
    // Check if image file is a actual image or fake image

    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $_SESSION['ERROR'] = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    /*if ($_FILES["image"]["size"] > 500000) {
        $_SESSION['ERROR'] = "Sorry, your file is too large.";
        $uploadOk = 0;
    }*/
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" ) {
        $_SESSION['ERROR'] = "Sorry, only JPG, JPEG, PNG files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 1){
      $original_name = basename( $_FILES["image"]["name"]);
	    $original_name = basename( $_FILES["image"]["name"]);
        list($width, $height) = getimagesize($_FILES["image"]["tmp_name"]);
				
			$set_default_facebookimage = "no";
        	if (isset($_REQUEST['facebook_cover_upload']) && $_REQUEST['facebook_cover_upload']=="yes"){
				$set_default_facebookimage = "yes";
        	}
        
		
            $countimage =  GetSumOnCndi(PRODUCT_BANNER, "id", "prod_id = '".$ProductID."'");
            $set_default_image = 'yes';
			
			
            if($countimage > 0){$set_default_image = 'no';}

		 $sourceProperties = getimagesize($_FILES["image"]["tmp_name"]);
  		//for listing	
		  ProductImageThumbCreate($sourceProperties,$imageFileType,$_FILES["image"]["tmp_name"],$ImageNewName."_245X354",SITE_BASE_PATH.$target_dir,354,'products/'.$ProductID);
		  //for product details
		  ProductImageThumbCreate($sourceProperties,$imageFileType,$_FILES["image"]["tmp_name"],$ImageNewName."_389X568",SITE_BASE_PATH.$target_dir,568,'products/'.$ProductID);
		  //for thumb
		 
        if (move_uploaded_file($_FILES["image"]["tmp_name"], SITE_BASE_PATH."/".$target_file)) {
        	digitalOceanUploadImage(SITE_BASE_PATH."/".$target_file,'products/'.$ProductID);
			   
			 $original_name = basename( $_FILES["image"]["name"]);
            $countimage =  GetSumOnCndi(PRODUCT_BANNER, "id", "prod_id = '".$ProductID."'");
            $set_default_image = 'yes';
            if($countimage > 0)
            {
                $set_default_image = 'no';
            }
			
            
       		if($set_default_facebookimage=="yes"){
				UpdateRcrdOnCndi(PRODUCT_BANNER, "set_default_facebookimage= 'no'", "prod_id = '$ProductID'");	
			}

            InsertRcrdsByData(PRODUCT_BANNER,"`prod_id` = '$ProductID', `filename` = '".$file_name."', `filetype` = 'image', `original_name` = '$original_name',`set_default_image` = '$set_default_image',set_default_facebookimage='$set_default_facebookimage'");
            $_SESSION['SUCCESS'] = "The file ".$original_name." has been uploaded.";
        	rmdir(SITE_BASE_PATH.'products/'.$ProductID);
			} else {
            $_SESSION['ERROR'] = "Sorry, there was an error uploading your file.";
        }
    }
}



if(isset($_POST['uploadvideo_motion'])) {
    if($_POST['video']!='')
    {
        InsertRcrdsByData(PRODUCT_BANNER,"`prod_id` = '$ProductID', `filename` = '".$_POST['video']."', `filetype` = 'video', `original_name` = '".$_POST['video']."',`set_default_image` = 'no',set_default_facebookimage='no',`type` = 'motion'");
        $_SESSION['SUCCESS'] = "Youtube url saved successfully.";
    }

}

/* if(isset($_FILES["video_motion"]["name"]) && $_FILES["video_motion"]["name"]!='')
    {
		
		
		if($_FILES["video_motion"]["name"] != ''){
	    $fileSize = $_FILES['video_motion']['size'];
	    $fileType = $_FILES['video_motion']['type'];
	    $fileName = basename($_FILES["video_motion"]["name"]);
		$targetDir = "videos/";
		$targetFile = $target_dir_video . $fileName;
		$allowedTypeArr = array("video/mp4", "video/avi", "video/mpeg", "video/mpg", "video/mov", "video/wmv", "video/rm");
		if(in_array($fileType, $allowedTypeArr)) {
		    if(move_uploaded_file($_FILES['video_motion']['tmp_name'], $targetFile)) {
		        $videoFilePath = $targetFile;
		    }else{
		        header('Location:edit-product-2.php?err=ue');
				exit;
		    }
		}else{
			header('Location:edit-product-2.php?err=fe');
			exit;
		}
	
		// insert video data
		
		
	}else{
		header('Location:edit-product-2.php?err=bf');
		exit;
	}
		
		
		
if (isset($_GET['code'])) {
	if (strval($_SESSION['state']) !== strval($_GET['state'])) {
	    $_SESSION['ERROR'] ='The session state did not match.';
	}

	$client->authenticate($_GET['code']);
	$_SESSION['token'] = $client->getAccessToken();

	header('Location: ' . REDIRECT_URI);
}

if (isset($_SESSION['token'])) {
	$client->setAccessToken($_SESSION['token']);
}

$htmlBody = '';
$checkValid = 0;
// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken()) {
  try{
	  $checkValid = 0;
	  $_SESSION['SUCCESS'] = "Video has  been uploaded successfully.";
	 
    // REPLACE this value with the path to the file you are uploading.
    $videoPath = $videoFilePath;

    // Create a snippet with title, description, tags and category ID
    // Create an asset resource and set its snippet metadata and type.
    // This example sets the video's title, description, keyword tags, and
    // video category.
	
	$getProduct = GetSglRcrdOnCndi(PRODUCT, "`id` = '$ProductID'");
   
	$snippet = new Google_Service_YouTube_VideoSnippet();
    $snippet->setTitle($getProduct['Title']);
  //  $snippet->setDescription( str_replace(['>', '<'], '',$getProduct['Description']));
   // $snippet->setTags(explode(",",$result['video_tags']));

    // Numeric video category. See
    // https://developers.google.com/youtube/v3/docs/videoCategories/list 
    $snippet->setCategoryId("22");

    // Set the video's status to "public". Valid statuses are "public",
    // "private" and "unlisted".
    $status = new Google_Service_YouTube_VideoStatus();
    $status->privacyStatus = "public";

    // Associate the snippet and status objects with a new video resource.
    $video = new Google_Service_YouTube_Video();
    $video->setSnippet($snippet);
    $video->setStatus($status);

    // Specify the size of each chunk of data, in bytes. Set a higher value for
    // reliable connection as fewer chunks lead to faster uploads. Set a lower
    // value for better recovery on less reliable connections.
    $chunkSizeBytes = 1 * 1024 * 1024;

    // Setting the defer flag to true tells the client to return a request which can be called
    // with ->execute(); instead of making the API call immediately.
    $client->setDefer(true);

    // Create a request for the API's videos.insert method to create and upload the video.
    $insertRequest = $youtube->videos->insert("status,snippet", $video);

    // Create a MediaFileUpload object for resumable uploads.
    $media = new Google_Http_MediaFileUpload(
        $client,
        $insertRequest,
        'video/*',
        null,
        true,
        $chunkSizeBytes
    );
    $media->setFileSize(filesize($videoPath));
	
	

    // Read the media file and upload it.
    $status = false;
    $handle = fopen($videoPath, "rb");
    while (!$status && !feof($handle)) {
      $chunk = fread($handle, $chunkSizeBytes);
      $status = $media->nextChunk($chunk);
    }
    fclose($handle);

    // If you want to make other calls after the file upload, set setDefer back to false
    $client->setDefer(false);
	
	// Update youtube video ID to database
	//$db->update($result['video_id'],$status['id']);
	// delete video file from local folder
	$checkValid = 0;
	@unlink($videoPath);
	
	 InsertRcrdsByData(PRODUCT_BANNER,"`prod_id` = '$ProductID', `filename` = '".$fileName."', `filetype` = 'video', `original_name` = '".$status['id']."',`set_default_image` = 'no',set_default_facebookimage='no',`type` = 'motion'");
	
	
//    $htmlBody .= "<p class='succ-msg'>Video has  been uploaded successfully.</p><ul>";
//	$htmlBody .= '<embed width="400" height="315" src="https://www.youtube.com/embed/'.$status['id'].'"></embed>';
//	$htmlBody .= '<li><b>Title: </b>'.$status['snippet']['title'].'</li>';
//	$htmlBody .= '<li><b>Description: </b>'.$status['snippet']['description'].'</li>';
//	$htmlBody .= '<li><b>Tags: </b>'.implode(",",$status['snippet']['tags']).'</li>';
 //  $htmlBody .= '</ul>';
//	$htmlBody .= '<a href="logout.php">Logout</a>';

  } catch (Google_ServiceException $e) {
    $checkValid = 1;
	$htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $checkValid = 1;
	$htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
	$htmlBody .= 'Please reset session <a href="youtube/logout.php">Logout</a>';
  }
  
  $_SESSION['token'] = $client->getAccessToken();
} else {

	$checkValid = 1;
	// If the user hasn't authorized the app, initiate the OAuth flow
	$state = mt_rand();
	$client->setState($state);
	$_SESSION['state'] = $state;
  
	$authUrl = $client->createAuthUrl();
	$htmlBody = <<<END
	<h3>Authorization Required</h3>
	<p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
}

    }
	*/
	
if(isset($_REQUEST['removeBanner'])) {

    $bannerID = intval($_REQUEST['removeBanner']);
    $getBanner = GetSglRcrdOnCndi(PRODUCT_BANNER, "id = '$bannerID'");
    
  
	 if($getBanner['filetype']=='image')
    {
		
		$ext = pathinfo($getBanner['filename'], PATHINFO_EXTENSION);
		$fileName = str_replace(".".$ext,"",$getBanner['filename']);
		
		$CheckSize354 =$fileName."_245X354";
		$CheckSize568 =$fileName."_389X568";
		

        $file_name_arr = explode('/',$getBanner['filename']);

        $file_name_arr2=explode('.',$file_name_arr[7]);
		digitalOceanDeleteImage("products/".$getBanner['prod_id']."/".$getBanner['filename']);
		digitalOceanDeleteImage("products/".$getBanner['prod_id']."/".$CheckSize354.".".$ext);
		digitalOceanDeleteImage("products/".$getBanner['prod_id']."/".$CheckSize568.".".$ext);
		digitalOceanDeleteImage("products/".$getBanner['prod_id']."/".$CheckSize354.".webp");
		digitalOceanDeleteImage("products/".$getBanner['prod_id']."/".$CheckSize568.".webp");
        DltSglRcrd(PRODUCT_BANNER, "id = '$bannerID'");
    }else{
		 DltSglRcrd(PRODUCT_BANNER, "id = '$bannerID'");
		
	}
	
   
    $_SESSION['SUCCESS'] = ucfirst($getBanner['fileType'])." successfully deleted.";
    header("location:edit-product-2.php");
    exit();
}
$getAllBanners = GetMltRcrdsOnCndiWthOdr(PRODUCT_BANNER,"`prod_id` = '$ProductID'","id","ASC");
$totalBanner = count($getAllBanners);
$motionVideos = GetMltRcrdsOnCndiWthOdr(PRODUCT_BANNER,"`prod_id` = '$ProductID' AND `type` = 'motion'","id","ASC");
$animatedVideos = GetMltRcrdsOnCndiWthOdr(PRODUCT_BANNER,"`prod_id` = '$ProductID' AND `type` = 'animated'","id","ASC");


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
</head>

<body>
<?php include "includes/header.php"; ?>
<?php 

if(isset($_GET['err'])){
	if($_GET['err'] == 'bf'){
		$_SESSION['ERROR'] = 'Please select a video file for upload.';
	}elseif($_GET['err'] == 'ue'){
		$_SESSION['ERROR'] = 'Sorry, there was an error uploading your file.';
	}elseif($_GET['err'] == 'fe'){
		$_SESSION['ERROR'] = 'Sorry, only MP4, AVI, MPEG, MPG, MOV & WMV files are allowed.';
	}else{
		$_SESSION['ERROR'] = 'Some problems occured, please try again.';
	}
}

?>
  <div class="notification success div_sucess div_sucess_facebook">
       
    </div>
<?php if(isset($_SESSION['ERROR']) && !empty($_SESSION['ERROR'])) { ?>
    <div class="notification error">
        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
        <span>Error: <?=$_SESSION['ERROR'];?></span><button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['ERROR']); } if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { ?>
    <div class="notification success div_sucess">
        <div class="d-flex"><i class="fas fa-check"></i></div>
        <span><?=$_SESSION['SUCCESS'];?></span>
        <button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['SUCCESS']); } ?>

<main class="main-content-wrap">
    <div class="container">
    <?php 
	
	if($checkValid==1){ 
		echo "<div class='main-content bx-shadow pl-60 pr-60'>".$htmlBody."</div>";
	?>
        <?php }else{ ?>
        <div class="main-content pl-60 pr-60 bx-shadow">
            <div class="page-head mb-4">
                <h1 class="page-heading">Edit Product</h1>
                <a href="edit-product.php"><i class="fas fa-angle-left"></i> Back to product details</a>
            </div>

            <h2 class="blue">(2) Product media</h2>
				<?php 
				  $totalImages = GetNumOfRcrdsOnCndi(PRODUCT_BANNER,"`prod_id` = '$ProductID' AND `filetype` = 'image'");
           
				?>
            <form method="post" class="pr-media-upload mt-5 ml-sm-4 <?php if($totalImages > 0){ ?>pr-uploaded-media_images_form <?php } ?>" enctype="multipart/form-data">
                <label>Upload photos of this product</label>
                <br>
                <label class="custom-file">
                    <input type="file" name="image" id="file" onChange="limitImagesize()" class="custom-file-input smallImageUpload">
                    <span class="custom-file-control"></span>
                </label>
                
                
                <button type="submit" class="btn-blue" name="uploadimage">Upload</button>
                
                              <label class="custom-control custom-checkbox" style="margin-left:10px;">
                                    <input type="checkbox" class="custom-control-input facebook_cover_upload" value="yes" name="facebook_cover_upload" >
                                    <span class="custom-control-indicator" style="width:19px;height:19px;"></span>
                                    This is the Facebook cover
                                </label>
                                
                                <br>
                                <font class="facebookCoverNote">Note : If you are uploading facebook cover then tick above checkbox.</font>
                          
            </form>

            <?php
           if($totalImages > 0) {
                foreach($getAllBanners as $imagesList) {
                    if($imagesList['filetype'] == "video") { continue; }
                    ?>

                    <div class="pr-uploaded-media pr-uploaded-media_images image d-flex  pt-4 pb-1" style="max-width:700px;">
                     
                       <a href="?removeBanner=<?=$imagesList['id'];?>" class="delete"><i class="fas fa-trash-alt"></i></a>
                        <div class="col-lg-1 div_radiobtn">
                            <div class="defaultimage">
                                <input type="hidden" name="product_id" value="<?php echo $ProductID;  ?>" class="divproductid">
                                <input type="hidden" name="photoimage_id" value="<?php echo $imagesList['id']  ?>" class="divphotoimage_id" >
                                <label class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input features defaultSize imag_radiobtn" value="yes" name="set_defaultimage" <?php if($imagesList['set_default_image'] == 'yes') { echo 'checked'; }  ?>>
                                    <span class="custom-control-indicator" style="width:19px;height:19px;"></span>
                                </label>
                            </div>

                        </div>
                        <?php echo  productImageSrc($imagesList['filename'],$ProductID,'354');?>
                        <span><?=$imagesList['original_name'];?></span>
                 
                 <div class="col-lg-6 div_radiobtn">
                            <div class="defaultimage">
                             <input type="hidden" name="product_id" value="<?php echo $ProductID;  ?>" class="divproductid">
                                <input type="hidden" name="photoimage_id" value="<?php echo $imagesList['id']  ?>" class="divphotoimage_id" >
                               
                                <label class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input features defaultSize imag_radiobtn_facebook" value="yes" name="set_default_facebookimage" <?php if($imagesList['set_default_facebookimage'] == 'yes') { echo 'checked'; }  ?>>
                                    <span class="custom-control-indicator" style="width:19px;height:19px;"></span>
                                    This is the Facebook cover
                                </label>
                            </div>

                        </div>
                      
                    </div>

                    <?php
                }
            } else {
                echo '<div class="pr-uploaded-media image d-flex  pt-4 pb-1">No banner found for this product.</div>';
            } ?>

            
            <form method="post" class="pr-media-upload mt-5 ml-sm-4" enctype="multipart/form-data">
                <label>YouTube link for the Motion video flyer</label>
                <br>
                <input type="text" name="video" class="form-control" placeholder="YouTube URL" value="">
                <button type="submit" class="btn-blue" name="uploadvideo_motion">Upload</button>
            </form>

            <?php
            if(!empty($motionVideos) && count($motionVideos) > 0){

                foreach ($motionVideos as $motionKey => $motionValue) {
                    ?>
                    <div class="pr-uploaded-media image d-flex  pt-4 pb-1">
                        <span style="width:100%;"><?= $motionValue['original_name']; ?></span>
                        <a href="?removeBanner=<?= $motionValue['id']; ?>" class="delete"><i
                                    class="fas fa-trash-alt"></i></a>
                    </div>

                    <?php
                }
            } else {
                echo '<div class="pr-uploaded-media image d-flex  pt-4 pb-1">No motion video found for this product.</div>';
            }
            ?>

        </div>
        <?php } ?>
        
</main>

<?php 
if (!$client->getAccessToken()) { ?>
<div id="login-authorize-rq-popup" class="login-authorize-popup">

    <div class="login-authorize-popup-box">

        <div class="login-authorize-popup-middle">

            <div class="login-authorize-popup_block bg-white">

              <div class="authorizeLoginPopup">
	<div class="AuthorizeLoginContain">
    <?php 
	
	$checkValid = 1;
	// If the user hasn't authorized the app, initiate the OAuth flow
	$state = mt_rand();
	$client->setState($state);
	$_SESSION['state'] = $state;
  
	$authUrl = $client->createAuthUrl();
	?>
    <h3>Authorization Required <span class="login-authorize-close-btn float-right"><i class="fa fa-times" <="" span=""></i></span></h3>
	<p>You need to <a href="<?php echo $authUrl; ?>">authorize access</a> before proceeding.<p>
	
    </div>
</div>

            </div>

        </div>

    </div>

</div>
<?php } ?>

<?php include "includes/footer.php"; ?>


<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/datepicker.min.js"></script>
<script src="js/jquery.tablesorter.min.js"></script>
<script src="js/script.js"></script>
<script>
    $(document).ready(function () {
        $('.close-ntf').click(function() {
            $(this).parent().fadeOut(300, function() {
                $(this).hide();
            });
        });
        setTimeout(function(){ $('.close-ntf').click(); }, 12000);
    });
</script>
<script>

    function limitImagesize() {
        //Get reference of FileUpload.
        var fileUpload = document.getElementById("file");



        //Check whether HTML5 is supported.
        if (typeof (fileUpload.files) != "undefined") {
            //Initiate the FileReader object.
            var reader = new FileReader();
            //Read the contents of Image File.
            reader.readAsDataURL(fileUpload.files[0]);
            reader.onload = function (e) {
                //Initiate the JavaScript Image object.
                var image = new Image();

                //Set the Base64 string return from FileReader as source.
                image.src = e.target.result;

                //Validate the File Height and Width.
                image.onload = function () {
                    var height = this.height;
                    var width = this.width;
                    /*if (height > 1144 || width > 778) {
                        alert("Width and Height must not exceed 778 X 1144 px.");
                        jQuery('#file').val('');
                        jQuery('#file').parents('.custom-file').find('.custom-file-control').removeClass('selected').data('after','');
                        return false;
                    }*/
                    return true;
                };

            }
        }
    }

    jQuery(document).ready(function () {
		

        jQuery('.div_radiobtn .imag_radiobtn').change(function(){
           
		      jQuery(".div_sucess_facebook").empty();
            jQuery('.div_radiobtn').removeClass('activecoachbox');
            jQuery(this).closest('.div_radiobtn').addClass('activecoachbox');
            var radiobtnvalue = jQuery( this ).val();
            var productid_value = $(".col-lg-1.div_radiobtn.activecoachbox .divproductid").val();
            var photoimage_id = $(".col-lg-1.div_radiobtn.activecoachbox .divphotoimage_id").val();

            jQuery.ajax({
                url: "<?php echo ADMINURL ?>setproductdefaultimage.php",
                dataType: 'json',
                type: "POST",
                cache: false,
                data: {
                    'radiobtnvalue' : radiobtnvalue,
                    'productid_value' : productid_value,
                    'photoimage_id' : photoimage_id

                },
                success: function (data) {

                    
jQuery('.div_sucess_facebook').html('<div class="d-flex"><i class="fas fa-check"></i></div><span>'+data.msg+'</span><button class="close-ntf"><i class="fas fa-times"></i></button>');
 jQuery('html, body').animate({
                scrollTop: parseFloat(jQuery(".div_sucess_facebook").offset().top) - 150
			}, 1000);


                }
            });



        });
		
		
    });
	 jQuery(document).on("change",'.div_radiobtn .imag_radiobtn_facebook',function(){
		
            jQuery(".div_sucess_facebook").empty();
            jQuery('.div_radiobtn').removeClass('activecoachbox');
            jQuery(this).closest('.div_radiobtn').addClass('activecoachbox');
            var radiobtnvalue = jQuery( this ).val();
            var productid_value = $(".col-lg-6.div_radiobtn.activecoachbox .divproductid").val();
            var photoimage_id = $(".col-lg-6.div_radiobtn.activecoachbox .divphotoimage_id").val();

            jQuery.ajax({
                url: "<?php echo ADMINURL ?>setproductdefaultFacebook.php",
                dataType: 'json',
                type: "POST",
                cache: false,
                data: {
                    'radiobtnvalue' : radiobtnvalue,
                    'productid_value' : productid_value,
                    'photoimage_id' : photoimage_id

                },
                success: function (data) {
				jQuery('.div_sucess_facebook').html('<div class="d-flex"><i class="fas fa-check"></i></div><span>Facebook Cover default set successfully. </span><button class="close-ntf"><i class="fas fa-times"></i></button>');3
				
				 jQuery('html, body').animate({
                scrollTop: parseFloat(jQuery(".div_sucess_facebook").offset().top) - 150
			}, 1000);

                }
            });



        });
<?php if (!$client->getAccessToken()) { ?>
jQuery(document).ready(function(e) {
    jQuery(".motion_video_upload_form,.motion_upload_form").submit(function(event) {
        event.preventDefault();
		jQuery(".login-authorize-popup").addClass("active");
    });
	
  jQuery(".login-authorize-close-btn").click(function(){
        jQuery("#login-authorize-rq-popup").removeClass("active");
    });	
});
</script>
<?php } ?>
</body>

</html>