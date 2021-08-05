<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
require_once '../function/adminSession.php';

$actionTitle = "Flyer";
$PageTitle = "Manage ".$actionTitle;

$id = 0;
if(!empty($_REQUEST['id']) && $_REQUEST['id'] > 0){
    $id = $_REQUEST['id'];
}
$isError = false;
if(isset($_REQUEST['save'])) {


    $isActive = 0;
    if( !empty($_REQUEST['is_active']) && ($_REQUEST['is_active'] == 'on' || $_REQUEST['is_active']==1)){
        $isActive = 1;
    }
    $alt = $_REQUEST['alt'];
    $sortOrder = $_REQUEST['sort_order'];
    $data="`alt` = '".$alt."', `sort_order` = '".$sortOrder."', `is_active` = '".$isActive."'";

    if(empty($_FILES['image']['name']) && $id == 0) {
        $_SESSION['ERROR'] = "Please upload image.";
        $isError = true;
    }

    if(!empty($_FILES['image']['name'])) {

        $UpDir				= 'uploads/flyer/';
        $tmp_name = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $allowed = array("image/jpeg", "image/gif", "image/png", "image/webp");
		
		
        if(in_array($file_type, $allowed)) {

            $folderPath = SITE_BASE_PATH.'uploads/temp/';
            if(!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
			$imageFileType = strtolower(pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION));
			 $sourceProperties = getimagesize($_FILES["image"]["tmp_name"]);
		
			$ImageNewName = time();
			 $file_name = $ImageNewName.".".$imageFileType;
		
				ProductImageThumbCreate($sourceProperties,$imageFileType,$_FILES["image"]["tmp_name"],$ImageNewName."_375X550",SITE_BASE_PATH.$UpDir,550,'flyer');
		  
	


            if(move_uploaded_file($tmp_name, SITE_BASE_PATH.$UpDir.$file_name ))
            {
				digitalOceanUploadImage(SITE_BASE_PATH.$UpDir.$file_name ,'flyer');
                $filename_arr = explode('.',$NewFileName);

              
                //unlink($UpDir.$NewFileName);
                $data = $data.", image_path='$file_name'";
				
            }
        }else{
            $_SESSION['ERROR'] = "Only jpg, gif, and png files are allowed.";
            $isError = true;
        }

    }

    if($isError == false) {
        if($id > 0) {
		  UpdateRcrdOnCndi( FLYERS, $data, "id = '$id'" );
            $_SESSION['SUCCESS'] = $actionTitle . " successfully updated.";
        }else {
            InsertRcrdsByData( FLYERS, $data );
            $_SESSION['SUCCESS'] = $actionTitle . " successfully added.";
        }
    }
}

if($id > 0){
    $flyer = GetSglRcrdOnCndi(FLYERS, "id = '$id'");

    if(! isset($flyer['id'])){
        header("location:setting-variables.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
</head>

<body>
<?php include "includes/header.php"; ?>
<?php if(isset($_SESSION['ERROR']) && !empty($_SESSION['ERROR'])) { ?>
    <div class="notification error">
        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
        <span>Error: <?=$_SESSION['ERROR'];?></span><button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['ERROR']); } if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { ?>
    <div class="notification success">
        <div class="d-flex"><i class="fas fa-check"></i></div>
        <span><?=$_SESSION['SUCCESS'];?></span>
        <button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['SUCCESS']); } ?>
<main class="main-content-wrap">
    <div class="container">
        <div class="main-content bx-shadow pl-60 pr-60">
            <h1 class="page-heading mb-4">Manage Product Variables - <?=$actionTitle;?> <a href="setting-variables.php" class="addnewlink">Back</a></h1>

            <div class="row brd-bottom mt-5">
                <div class="col-lg-6 brd-lg-right">
                    <h2 class="blue text-center mb-4"><?=$actionTitle;?></h2>
                    <form method="post" class="profile-form pl-md-5 pr-md-5 pb-5" enctype="multipart/form-data">


                        <label>Image Alt</label>
                        <input type="text" name="alt" placeholder="Enter Alt" value="<?=(!empty($flyer['alt'])) ? $flyer['alt'] : "" ;?>" class="form-control mb-4" required>
                        <label>Sort Order</label>
                        <input type="text" name="sort_order" placeholder="Enter Sort Order" value="<?=(!empty($flyer['sort_order'])) ? $flyer['sort_order'] : "" ;?>" class="form-control mb-4" required>
                        <label class="custom-control custom-checkbox new-product-notification">Is Active?
                            <input type="checkbox" name="is_active" class="custom-control-input otherSizeCheck" value="1" <?php echo ($flyer['is_active'] == 1 ) ? "checked" : ""; ?> >
                            <span class="custom-control-indicator"></span>
                        </label>
                        <div>
                            <?php
                                $flyerImagePath = SITEURL."uploads/flyer/".$flyer['image_path'];
                                $check_filepath =SITE_BASE_PATH."uploads/flyer/".$flyer['image_path'];
                            if(!empty($flyer['image_path'])){



                                ?>
                                <div>
                                    <?php 
							echo  FlayerImageSrc($flyer['image_path'],'354');
							?>
                          
                                </div>
                            <?php } ?>
                            <div style="overflow: hidden; text-align: center; margin: 22px 58px;">
                                <a class="btn btn-blue uploadbtn" style="display:block">Upload</a>
                                <input type="file" name="image" id="filefield" data-name="file" style="display: none;" />
                                <label for="file"><span class="file">Select file</span></label>

                            </div>
                            <!--<div style="color:#F00; padding-bottom:10px">
                                <strong>Note : </strong>Please upload .
                            </div>-->
                        </div>
                        <div class="text-center">
                            <button type="submit" name="save" class="btn-block form-btn-grad"><?=(!empty($flyer['id'])) ? "Update" : "Add" ;?></button>
                        </div>
                    </form>
                </div>
            </div>

        </div>


    </div>
</main>

<?php include "includes/footer.php"; ?>

<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/datepicker.min.js"></script>
<script src="js/script.js"></script>
<script>
    jQuery(".uploadbtn").click(function() {
        jQuery("#filefield").click();
    });

    jQuery(document).ready(function () {
        function readURL(input) {
            if (input.files && input.files[0]) {
                var fileName = input.files[0].name;
                var bname = jQuery(input).data('name');
                jQuery("."+bname).text(fileName);
            }
        }
        jQuery("#filefield").on('change', function(){
            var bname = jQuery(this).data('name');


            //Get reference of FileUpload.
            var fileUpload = document.getElementById("filefield");



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
                        /*if (height > 550 || width > 375) {
                            alert("Width and Height must not exceed 550 X 375 px.");
                            jQuery('#filefield').val('');
                            jQuery("."+bname).text("Select file");
                            return false;
                        }*/

                        /* if (height < 550 || width < 375) {
                            alert("min width 375px and min height 550px needed.");
                             jQuery('#filefield').val('');
                             jQuery("."+bname).text("Select file");
                            return false;
                        }*/
                        return true;
                    };

                }
            }

            jQuery("."+bname).text("Select file");
            readURL(this);
        });
    });
</script>
</body>

</html>