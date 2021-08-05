<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
require_once '../function/adminSession.php';

$PageTitle = "Create Product";

if(!isset($_SESSION['NEWPRODUCTID']) && empty($_SESSION['NEWPRODUCTID'])) {
    echo "<script> window.location.href = '".ADMINURL."create-product.php';</script>";
    exit();
}
$ProductID = intval($_SESSION['NEWPRODUCTID']);

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
		
		if(file_exists(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$getBanner['filename'])){
			unlink(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$getBanner['filename']);
		}
		if(file_exists(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$CheckSize354.".".$ext)){
			unlink(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$CheckSize354.".".$ext);
		}
		if(file_exists(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$CheckSize568.".".$ext)){
			unlink(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$CheckSize568.".".$ext);
		}
		
		
		if(file_exists(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$CheckSize354.".webp")){
			unlink(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$CheckSize354.".webp");
		}
		if(file_exists(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$CheckSize568.".webp")){
			unlink(SITE_BASE_PATH."uploads/products/".$getBanner['prod_id']."/".$CheckSize568.".webp");
		}
		

        DltSglRcrd(PRODUCT_BANNER, "id = '$bannerID'");
    }else{
		 DltSglRcrd(PRODUCT_BANNER, "id = '$bannerID'");
		
	}
	
   
    $_SESSION['SUCCESS'] = ucfirst($getBanner['fileType'])." successfully deleted.";
    header("location:create-product-2.php");
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
        <?php }else{?>
        <div class="main-content pl-60 pr-60 bx-shadow">
            <div class="page-head mb-4">
                <h1 class="page-heading">Create Product</h1>
                <a href="edit-product.php?product_id=<?=$ProductID;?>"><i class="fas fa-angle-left"></i> Back to product details</a>
            </div>

            <h2 class="blue">(2) Product media</h2>
			<?php 
			 $totalImages = GetNumOfRcrdsOnCndi(PRODUCT_BANNER,"`prod_id` = '$ProductID' AND `filetype` = 'image'");
			?>
            <form method="post" class="pr-media-upload mt-5 ml-sm-4 <?php if($totalImages > 0){ ?>pr-uploaded-media_images_form <?php } ?>" enctype="multipart/form-data">
                <label>Upload photos of this product one at a time</label>
                <br>
                <label class="custom-file">
                    <input type="file" name="image" id="file" class="custom-file-input inputtab" data-name="input_tab1" data-content="Choose file...">
                    <span class="custom-file-control input_tab1"></span>
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

                    <div class="pr-uploaded-media image d-flex pr-uploaded-media_images  pt-4 pb-1">
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

        /*function readURL(input) {
            if (input.files && input.files[0]) {
                var fileName = input.files[0].name;
                var bname = $(input).data('name');
                $("."+bname).text(fileName);
            }
        }
        $(".inputtab").on('change', function(){
            var bname = $(this).data('name');
            $("."+bname).text("Choose file...");
            readURL(this);
        });*/
    });
</script>
<script>
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
		
		 jQuery('.div_radiobtn .imag_radiobtn_facebook').change(function(){
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
				jQuery('.div_sucess_facebook').html('<div class="d-flex"><i class="fas fa-check"></i></div><span>Facebook Cover default set successfully. </span><button class="close-ntf"><i class="fas fa-times"></i></button>');
				
				 jQuery('html, body').animate({
                scrollTop: parseFloat(jQuery(".div_sucess_facebook").offset().top) - 150
			}, 1000);


                }
            });



        });
    });
	
</script>


</body>

</html>