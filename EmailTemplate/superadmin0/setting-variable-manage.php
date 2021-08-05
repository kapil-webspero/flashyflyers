<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
require_once '../function/adminSession.php';

include(SITE_BASE_PATH."wordpress/wp-config.php");
if(!isset($_REQUEST['action_for']) && empty($_REQUEST['action_for'])) {
    header("location:setting-variables.php");
    exit();
}



if($_REQUEST['action_for'] == "product_category") {
    $actionTable = CATEGORIES;
    $actionTitle = "Product Category";
} elseif($_REQUEST['action_for'] == "product_type") {
    $actionTable = PRODUCT_TYPE;
    $actionTitle = "Product Type";
} elseif($_REQUEST['action_for'] == "product_size") {
    $actionTable = PRODUCT_SIZE;
    $actionTitle = "Product Size";
} elseif($_REQUEST['action_for'] == "product_addon") {
    $actionTable = ADDON_PRICE;
    $actionTitle = "Product Addon Prices";
} elseif($_REQUEST['action_for'] == "product_option") {
    $actionTable = OPTION_PRICE;
    $actionTitle = "Product Options";
} elseif($_REQUEST['action_for'] == "tooltip_update") {
	
    $actionTable = "";
    $actionTitle = "Form Fields Tooltip";
} else {
    header("location:setting-variables.php");
    exit();
}
$PageTitle = "Manage ".$actionTitle;
$recordData['title'] = "";
$recordData['price'] = "";
$recordData['design_labor_cost'] = "";
if(isset($_REQUEST['action']) && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'update') {
    if(!isset($_REQUEST['record_id']) && empty($_REQUEST['record_id'])) {
        header("location:setting-variables.php");
        exit();
    }
    $recordID = intval($_REQUEST['record_id']);
    if($_REQUEST['action_for'] == "product_category") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "cat_id = '$recordID'");
        $recordData['title'] = $recordIDArr['category_name'];
    } elseif($_REQUEST['action_for'] == "product_type") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "ID = '$recordID'");
        $recordData['title'] = $recordIDArr['Name'];
        $recordData['price'] = $recordIDArr['Price'];
        $recordData['design_labor_cost'] = $recordIDArr['design_labor_cost'];
    } elseif($_REQUEST['action_for'] == "product_size") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "ID = '$recordID'");
        $recordData['title'] = $recordIDArr['Size'];
        $recordData['price'] = $recordIDArr['Price'];
		$recordData['image_path'] = $recordIDArr['image_path'];
    } elseif($_REQUEST['action_for'] == "product_addon") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "id = '$recordID'");
        $recordData['title'] = $recordIDArr['price_key'];
        $recordData['price'] = $recordIDArr['price_value'];
        $recordData['design_labor_cost'] = $recordIDArr['design_labor_cost'];
    } elseif($_REQUEST['action_for'] == "product_option") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "id = '$recordID'");
        $recordData['title'] = $recordIDArr['option_name'];
        $recordData['design_labor_cost'] = $recordIDArr['option_price'];
    }
}
if(isset($_REQUEST['update'])) {
    extract($_POST);
    if($_REQUEST['action_for'] == "product_category") {
        UpdateRcrdOnCndi($actionTable, "`category_name` = '".$val1."'", "cat_id = '$recordID'");
    } elseif($_REQUEST['action_for'] == "product_type") {
        UpdateRcrdOnCndi($actionTable, "`Price` = '".$val2."', `design_labor_cost` = '".$val3."'", "ID = '$recordID'");
    } elseif($_REQUEST['action_for'] == "product_size") {
			if(!empty($_FILES['image']['name'])) {

        $File_Name          = strtolower($_FILES['image']['name']);
        $imageFileType 		= strtolower(pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION));
        $file_name = time().".".$imageFileType;
        $UpDir				= 'uploads/productsize/';
        $file_link 			= $UpDir.$file_name;
        $tmp_name = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $allowed = array("image/jpeg", "image/gif", "image/png");

        if(in_array($file_type, $allowed)) {

            $folderPath = SITE_BASE_PATH.'uploads/productsize/';
            if(!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
			

            if(move_uploaded_file($tmp_name, SITE_BASE_PATH.$UpDir.$file_name ))
            {
				
                $filename_arr = explode('.',$NewFileName);

              
                //unlink($UpDir.$NewFileName);
                $image = $data.", image_path='$file_name'";
				
            }
        }else{
            $_SESSION['ERROR'] = "Only jpg, gif, and png files are allowed.";
            $isError = true;
        }

    }
        UpdateRcrdOnCndi($actionTable, "`Size` = '".$val1."', `Price` = '".$val2."', `image_path` = '".$file_name."'", "ID = '$recordID'");
    } elseif($_REQUEST['action_for'] == "product_addon") {
        UpdateRcrdOnCndi($actionTable, "`price_key` = '".$val1."', `price_value` = '".$val2."',`design_labor_cost` = '". $val3."'" , "id = '$recordID'");
    }  elseif($_REQUEST['action_for'] == "product_option") {
        UpdateRcrdOnCndi($actionTable, "`option_name` = '".$val1."',`option_price` = '". $val3."'" , "id = '$recordID'");
    }
    unset($_SESSION['SUCCESS']);
    $_SESSION['SUCCESS'] = $actionTitle." successfully updated.";
    $recordID = intval($_REQUEST['record_id']);
    if($_REQUEST['action_for'] == "product_category") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "cat_id = '$recordID'");
        $recordData['title'] = $recordIDArr['category_name'];
    } elseif($_REQUEST['action_for'] == "product_type") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "ID = '$recordID'");
        $recordData['title'] = $recordIDArr['Name'];
        $recordData['price'] = $recordIDArr['Price'];
        $recordData['design_labor_cost'] = $recordIDArr['design_labor_cost'];
    } elseif($_REQUEST['action_for'] == "product_size") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "ID = '$recordID'");
        $recordData['title'] = $recordIDArr['Size'];
        $recordData['price'] = $recordIDArr['Price'];
		$recordData['image_path'] = $recordIDArr['image_path'];
    } elseif($_REQUEST['action_for'] == "product_addon") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "id = '$recordID'");
        $recordData['title'] = $recordIDArr['price_key'];
        $recordData['price'] = $recordIDArr['price_value'];
        $recordData['design_labor_cost'] = $recordIDArr['price_labour_cost'];
    } elseif($_REQUEST['action_for'] == "product_option") {
        $recordIDArr = GetSglRcrdOnCndi($actionTable, "id = '$recordID'");
        $recordData['title'] = $recordIDArr['option_name'];
        $recordData['design_labor_cost'] = $recordIDArr['option_price'];
    }
}
if(isset($_REQUEST['add'])) {
    extract($_POST);
    if($_REQUEST['action_for'] == "product_category") {
        InsertRcrdsByData($actionTable, "`category_name` = '".$val1."'");
    } elseif($_REQUEST['action_for'] == "product_type") {
        InsertRcrdsByData($actionTable, "`Name` = '".$val1."', `Price` = '".$val2."', `design_labor_cost` = '".$val3."'");
    } elseif($_REQUEST['action_for'] == "product_size") {
		if(!empty($_FILES['image']['name'])) {

        $File_Name          = strtolower($_FILES['image']['name']);
        $imageFileType 		= strtolower(pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION));
        $file_name = time().".".$imageFileType;
        $UpDir				= 'uploads/productsize/';
        $file_link 			= $UpDir.$file_name;
        $tmp_name = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $allowed = array("image/jpeg", "image/gif", "image/png");

        if(in_array($file_type, $allowed)) {

            $folderPath = SITE_BASE_PATH.'uploads/productsize/';
            if(!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
			

            if(move_uploaded_file($tmp_name, SITE_BASE_PATH.$UpDir.$file_name ))
            {
				
                $filename_arr = explode('.',$NewFileName);

              
                //unlink($UpDir.$NewFileName);
                $image = $data.", image_path='$file_name'";
				
            }
        }else{
            $_SESSION['ERROR'] = "Only jpg, gif, and png files are allowed.";
            $isError = true;
        }

    }
        InsertRcrdsByData($actionTable, "`Size` = '".$val1."', `Price` = '".$val2."',`image_path` = '".$file_name."'");
    } elseif($_REQUEST['action_for'] == "product_addon") {
        InsertRcrdsByData($actionTable, "`price_key` = '".$val1."', `price_value` = '".$val2."', `design_labor_cost` = '".$val3."'");
    } elseif($_REQUEST['action_for'] == "product_option") {
        InsertRcrdsByData($actionTable, "`option_name` = '".$val1."', `option_price` = '".$val3."'");
    }
    $_SESSION['SUCCESS'] = $actionTitle." successfully added.";
}

if(isset($_REQUEST['update_tooltip'])){
	
	$ToolTipTemplateFileds = get_option('ToolTipTemplateFileds');
	$ToolTipTemplateFileds[key($_REQUEST['ToolTipTemplateFileds'])] = $_REQUEST['ToolTipTemplateFileds'][key($_REQUEST['ToolTipTemplateFileds'])];
	
	update_option('ToolTipTemplateFileds',$ToolTipTemplateFileds);	
	
}
if($_REQUEST['action_for'] == "tooltip_update" &&  isset($_POST['update_tooltip'])) {

	 $_SESSION['SUCCESS'] = "Tooltip successfully updated.";
}
	$ToolTipTemplateValue = get_option('ToolTipTemplateFileds');
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
            <h1 class="page-heading mb-4">
            <?php if($_REQUEST['action_for']=="tooltip_update"){
				echo "Tooltip for ".$templateFiledsSettings[$_REQUEST['id']];	
			}else{ ?>
            Manage Product Variables - <?=$actionTitle;?> 
            <?php } ?>
            <a href="setting-variables.php" class="addnewlink">Back</a></h1>
            <?php  if($_REQUEST['action_for']=="tooltip_update"){ ?>
            <form method="post" class="profile-form  pr-md-5 pb-5" enctype="multipart/form-data">
                        <label>Tooltip</label>
                        <input type="text" name="ToolTipTemplateFileds[<?php echo $_REQUEST['id']; ?>]"  placeholder="Enter tooltip for <?php echo $templateFiledsSettings[$_REQUEST['id']]; ?>" value="<?=$ToolTipTemplateValue[$_REQUEST['id']];?>" class="form-control mb-4">
                        
						
                        <div class="text-center">
                         
                                <button type="submit" name="update_tooltip" class="btn-block form-btn-grad">Update</button>
                            
                        </div>
                    </form>
                    <?php }else{ ?>

            <div class="row brd-bottom mt-5">
                <div class="col-lg-6 brd-lg-right">
                	
                    <h2 class="blue text-center mb-4"><?=$actionTitle;?></h2>
                    <form method="post" class="profile-form pl-md-5 pr-md-5 pb-5" enctype="multipart/form-data">
                        <label>Title</label>
                        <input type="text" name="val1" <?php  if($_REQUEST['action_for'] == "product_type"){echo "readonly";}  ?>  placeholder="Enter Title" value="<?=$recordData['title'];?>" class="form-control mb-4" required>
                        <?php if($_REQUEST['action_for'] != "product_category" && $_REQUEST['action_for'] != "product_option") { ?>
                            <label>Price</label>
                            <input type="text" name="val2" placeholder="Enter Price" value="<?=$recordData['price'];?>" class="form-control mb-4" required>
                        <?php } ?>
                        <?php if($_REQUEST['action_for'] == "product_type" || $_REQUEST['action_for'] == "product_addon" || $_REQUEST['action_for'] == "product_option") { ?>
                            <label>Design Labor Cost</label>
                            <input type="text" name="val3" placeholder="Enter Design Labor Cost" value="<?=$recordData['design_labor_cost'];?>" class="form-control mb-4">
                        <?php } ?>
                        <?php if($_REQUEST['action_for'] == "product_size"){?>
                        
                        <div class="image_upload">
                        <?php
                            
                                $product_sizeimage = SITEURL."uploads/productsize/".$recordData['image_path'];
                                $check_filepath =SITE_BASE_PATH."uploads/productsize/".$recordData['image_path'];
                            
                            if(!empty($recordData['image_path'])){
                        ?>
                          <div> <img src="<?php echo $product_sizeimage; ?>" alt=""> </div>
                          <?php } ?>
                          <div style="overflow: hidden; text-align: center; margin: 22px 58px;"> <a class="btn btn-blue uploadbtn" style="display:block">Upload</a>
                            <input type="file" name="image" id="filefield" data-name="file" style="display: none;" accept="image/x-png,image/gif,image/jpeg"/>
                            <label for="file"><span class="file">Select file</span></label>
                        </div>
                        </div>


                        <?php } ?>
						
                        <div class="text-center">
                            <?php if($_REQUEST['action'] == 'update') { ?>
                                <button type="submit" name="update" class="btn-block form-btn-grad">Update</button>
                            <?php } else { ?>
                                <button type="submit" name="add" class="btn-block form-btn-grad">Add</button>
                            <?php } ?>
                        </div>
                    </form>
                   
                </div>
            </div>
            <?php } ?>

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
    $(".uploadbtn").click(function() {
        $("#filefield").click();
    });

    $(document).ready(function () {
        $('.close-ntf').click(function() {
            $(this).parent().fadeOut(300, function() {
                $(this).hide();
            });
        });
        setTimeout(function(){ $('.close-ntf').click(); }, 12000);

        function readURL(input) {
            if (input.files && input.files[0]) {
                var fileName = input.files[0].name;
                var bname = $(input).data('name');
                $("."+bname).text(fileName);
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