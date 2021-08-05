<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Update Product";
    $selectedAddonProductList = array();
	if(isset($_REQUEST['product_id']) && !empty($_REQUEST['product_id'])) {
		unset($_SESSION['EDITPRODUCTID']);
		$_SESSION['EDITPRODUCTID'] = intval($_REQUEST['product_id']);
	}
	if(isset($_SESSION['EDITPRODUCTID']) && !empty($_SESSION['EDITPRODUCTID'])) {
		$productID = $_SESSION['EDITPRODUCTID'];
	
		if(isset($_REQUEST['updateproduct'])) {
			extract($_POST);

			$checkCodequery = "SELECT COUNT(*) as num FROM ".PRODUCT." WHERE Title = '".$name."' and  id !='".$productID."'";
			   $checkCodeRs = mysql_fetch_array(mysql_query($checkCodequery));
			
				if($checkCodeRs['num']>0){
						
						$_SESSION['ERROR'] = "Sorry, product name already exists.Please choose another product name.";	
				}else{
            $is_seasonal = 0;
            $is_sale_of_the_week = 0;
            $is_regular = 0;

            if(!empty($show_flyer) && count($show_flyer) > 0){
                $is_seasonal = (in_array('is_seasonal', $show_flyer)) ? 1 : 0;
                $is_sale_of_the_week = (in_array('is_sale_of_the_week', $show_flyer)) ? 1 : 0;
                $is_regular = (in_array('is_regular', $show_flyer)) ? 1 : 0;
            }
		
			$features = $_POST['feature'];
			if(in_array("motion", $features)) {
				$motion = 1;
			} else {
				$motion = 0;
			}
			if(in_array("animated", $features)) {
				$animated = 1;
			} else {
				$animated = 0;
			}
			if(count($_REQUEST['default_size'])>0) {
				$default_size = implode(",",$_REQUEST['default_size']);	
			}
			if(count($_REQUEST['other_size'])>0) {
				$other_size = implode(",",$_REQUEST['other_size']);	
			}
			if(count($_REQUEST['productTags'])>0) {
				$prod_tags = implode(",",$_REQUEST['productTags']);	
			}
			if(count($_REQUEST['product_cate'])>0) {
				$product_cates = implode(",",$_REQUEST['product_cate']);
			
			}
			if($threedav == "yes") {
				$threed = 1;
			} else {
				$threed = 0;
			}
			if($threededit == "yes") {
				$edir_threed = 1;
			} else {
				$edir_threed = 0;
			}
            $product_addon_id = "";
		//	$product_cates = $product_category_addon;
			if($addon == "no") {
				$addon_val = 0;
				
                if(!empty($_POST['product_addon_id'])){
                    $product_addon_id = implode($_POST['product_addon_id'],',');
                }
			} else {
				$addon_val = 1;
			}
			
			if($_REQUEST['parent_product_types']==""){
			$_REQUEST['parent_product_types'] = 0;	
		}
		if($_REQUEST['child_product_types']==""){
			$_REQUEST['child_product_types'] = 0;	
		}
		$slug = checkproductSlug(PRODUCT,'ID',$slug,$productID);
		
		
		//if($checkSlug>0){
			//$slug = $slug."-".($checkSlug+1);	
		//}	
			$CustomProduct = "no";
			$CustomeProductfieldsSettings = "";
			$selectFiledsType = "";
			$custom_pro_tooltip = 0;
			$custom_pro_tooltip_msg = "";
			
			if($_REQUEST['addon']=="no" && $_REQUEST['CustomProduct']=="yes"){
				$CustomProduct = $_REQUEST['CustomProduct'];
				$selectFiledsType = $_REQUEST['selectFiledsType'];
				$CustomeProductfieldsSettings = serialize($_REQUEST['CustomeProductfieldsSettings'][$selectFiledsType]);	
				$custom_pro_tooltip = $_REQUEST['custom_pro_tooltip'];
				$custom_pro_tooltip_msg = $_REQUEST['custom_pro_tooltip_msg'];
				 		
			}
			    $template_type = 'regular_template';
				$psd_price = 0;
				$psd_file = "";
			$file_name = "";
			if($_POST['CustomProduct']=="no" && ($_POST['template_type']=="psd_customize_template" || $_POST['template_type']=="psd_only")){
				$template_type = $_POST['template_type'];
				$psd_price = $_POST['psd_price'];
				$psd_file = $_POST['psd_file'];
				
				
				  $target_dir = "uploads/products/".$productID."/";
				 if(!file_exists("../".$target_dir)) {
    			    mkdir("../".$target_dir, 0777, true);
    			}
				if(isset($_FILES["psd_file"]["name"]) && !empty($_FILES["psd_file"]["name"])){
				    $imageFileType = strtolower(pathinfo(basename($_FILES["psd_file"]["name"]),PATHINFO_EXTENSION));
					$ImageNewName = time();
					$file_name = $ImageNewName.".".$imageFileType;
					$target_file = $target_dir . $file_name;
				   
				 if (move_uploaded_file($_FILES["psd_file"]["tmp_name"], SITE_BASE_PATH."/".$target_file)) {
        				digitalOceanUploadImage(SITE_BASE_PATH.$target_file,'products/'.$productID);
			
				 }
				}else{
					$file_name = $_POST['psd_file_hidden'];
				}
			}

			UpdateRcrdOnCndi(PRODUCT, "`Title` = '$name',`slug` = '$slug', `Subtitle` = '$subtitle', `Category` = '".$product_cates."', `ProductType` = '$product_type', `Addon` = '$addon_val', `Description` = '".addslashes($editor)."',`FullDescription` = '".addslashes($editor1)."', `2D` = '1', `3D` = '$threed', `Static` = '1', `Motion` = '$motion', `Animated` = '$animated', `Defaultsizes` = '$default_size', `OtherSizes` = '$other_size', `3dTextEditable` = '$edir_threed', `Baseprice` = '$price', `Tags` = '$prod_tags', `Status` = '$product_status',`is_seasonal` = '$is_seasonal',`is_sale_of_the_week` = '$is_sale_of_the_week',`is_regular` = '$is_regular',`product_addon_id` = '$product_addon_id',`parent_product_cat_id` = '".$_REQUEST['parent_product_types']."',`child_product_cat_id` = '".$_REQUEST['child_product_types']."',`CustomProduct` = '".$CustomProduct."',`CustomProductFiledsType` = '".$selectFiledsType."',`CustomeProductfieldsSettings` = '".$CustomeProductfieldsSettings."',`custom_pro_tooltip` = '".$custom_pro_tooltip."',`custom_pro_tooltip_msg` = '".$custom_pro_tooltip_msg."',`template_type` = '".$template_type."',`psd_price` = '".$psd_price."',`psd_file` = '".$file_name."'","`id` = '$productID'");
			$_SESSION['SUCCESS'] = "Product successfully updated.";
			echo "<script> window.location.href = '".ADMINURL."edit-product.php?product_id=".$productID."';</script>";
			exit();
		}}
		$getProduct = GetSglRcrdOnCndi(PRODUCT, "`id` = '$productID'");
        $selectedAddonProductList = explode(',',$getProduct['product_addon_id']);
	} else {
		echo '<script>window.history.back();</script>';
	}

    $addonProductList = GetMltRcrdsWthSmFldsOnCndi(PRODUCT, "Addon = 1 and Status='1'  ORDER BY `Title` ASC","`id`,`Title`");
$getProductActiveTags = getProductActiveTags();
$cat_assoc_arr = getProductTypesDetails();
$par_cat_array = getParentProductTypesList(0, $old_cat="", $menu_id, 1, 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>

    <link rel="stylesheet" href="css/trumbowyg.min.css">
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="css/jquery.multiselect.css">
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
            <div class="main-content pl-60 pr-60 bx-shadow">
                <div class="page-head mb-4">
                    <h1 class="page-heading">Edit Product</h1>
                    <a href="products.php">
                        <i class="fas fa-angle-left"></i>
                        Back to products
                    </a>
                   
                </div>
                <div class="page-head mb-4">
                <h1 class="page-heading"></h1>
                 <a href="edit-product-2.php">View product media
                        <i class="fas fa-angle-right"></i>
                    </a>
                </div>

                <h2 class="blue">(1) Product details</h2>

                <form method="post" class="create-product brd-bottom" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Parent product type</label>
                          <select class="form-control" name="parent_product_types" id="parent_product_types">
                          	<option  value="0">Please select parent product type </option>
                            <?php
                        for($i=0 , $n=count($par_cat_array) ; $i<$n ; $i++)
                        {									
                            if($par_cat_array[$i]['ID'] == $getProduct['parent_product_cat_id'])
                                $selected = "selected";
                            else
                                $selected = "";
                            echo "<option value='". $par_cat_array[$i]['ID'] ."' $selected>". $par_cat_array[$i]['path'] ."</option>";
                        }
						
						  ?>
                          </select>
                        </div>
                        <div class="col-md-6">
                            <label>Child product type</label>
                           <select class="form-control"  name="child_product_types" id="child_product_types">
                          	<option  value="0">Please select child product type </option>
                          </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label>Product title/name</label>
                            <input type="text" name="name" onChange="changeSlug(this.value)" onKeyUp="changeSlug(this.value)" class="form-control" placeholder="Please enter title/name" value="<?=$getProduct['Title'];?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label>Product slug</label>
                            <input type="text" name="slug" id="slug" class="form-control"  value="<?=$getProduct['slug'];?>" required>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Product subtitle</label>
                            <input type="text" name="subtitle" class="form-control" placeholder="Please enter subtitle" value="<?=$getProduct['Subtitle'];?>">
                        </div>
                        <div class="col-md-6">
                            <label>Product Status</label>
                            <select class="form-control" name="product_status">
                                <option value="1" <?php if($getProduct['Status'] == 1) { echo "selected"; } ?>>Active</option>
                                <option value="0" <?php if($getProduct['Status'] != 1) { echo "selected"; } ?>>Inactive</option>
                            </select>
                        </div>
                        
                        
                        
                        
                    </div>
                    <div class="row">
                    <div class="col-md-6">
                            <label>Template Type</label>
                          
                           <label style="display:block;" class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input template_type" value="regular_template" name="template_type" <?php if($getProduct['template_type'] == 'regular_template' || $getProduct['template_type']=="") { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Regular Template</span>
                        </label>
                        <label style="display:block;" class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input template_type" value="psd_customize_template" name="template_type" <?php if($getProduct['template_type'] == 'psd_customize_template') { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">PSD & Customize Template</span>
                        </label>
                        
                        <label style="display:block;" class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input template_type" value="psd_only" name="template_type" <?php if($getProduct['template_type'] == 'psd_only') { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">PSD Only</span>
                        </label>
                        </div>
                        <div  class="col-md-6 psd_customized_block"  style="display:none;">
                        	<label>PSD File</label>
                            <label style="display:block;" class="custom-file">
                            <br>
                    <input type="file" name="psd_file" id="psd_file" accept=".psd" class="custom-file-input smallImageUpload">
                    <span class="custom-file-control"></span>
                </label>
                
                                      <div class="outputFilename"></div>
                                      
                                        <input type="hidden" name="psd_file_hidden" id="psd_file_hidden" value="<?php
										if($getProduct['psd_file']!="" ){echo $getProduct['psd_file'];} ?>">					<?php if($getProduct['psd_file']!="" ){ ?>
                   	<a style="float:right;" class="btn btn-primary" href="<?php echo digitalOceanGetFileImage("products/".$productID."/".$getProduct['psd_file']); ?>" download>Download</a>
                    <?php } ?>
                    </div>
                        </div>
                       
                    <div class="row">
                    	<div class="col-md-6 psd_customized_block" style="display:none;">
                            <label>PSD Price</label>
                            <input type="text" name="psd_price"  class="form-control" placeholder="Please enter psd price" value="<?=$getProduct['psd_price'];?>">
                        </div>
                        
                        <div class="col-md-6 priceBox">
                            <label>Product Price</label>
                            <input type="text" name="price" class="form-control" placeholder="Please enter base price" value="<?=$getProduct['Baseprice'];?>" required>
                        </div>
						<?php /*?><div class="col-md-6">
                            <label>Product Category</label><br>
							<?php 
                            $oldCategories = $getProduct['Category'];
                            $oldCategoriesArr = explode(',',$oldCategories);
                            $categoryList = GetMltRcrdsOnCndi(CATEGORIES, "category_name != ''");
                            foreach($categoryList as $categoryData) { ?>
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" name="product_cate[]" class="custom-control-input" value="<?=$categoryData['cat_id'];?>" <?php if(in_array($categoryData['cat_id'],$oldCategoriesArr)) { echo "checked"; } ?>>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description"><?=$categoryData['category_name'];?></span>
                            </label>
                            <?php } ?>
                        </div><?php */?>
                        
                        <?php /*?><div class="col-md-6">
                            <label>Product Tags</label>
                            <?php 
							$arrTags = explode(",",$getProduct['Tags']);
							?>
                            <select id="tags" name="tags[]" class="form-control" multiple>
                                <option value="Facebook Cover" <?php if(in_array("Facebook Cover", $arrTags)) { echo "selected"; } ?>>Facebook Cover</option>
                                <option value="Other Cover" <?php if(in_array("Other Cover", $arrTags)) { echo "selected"; } ?>>Other Cover</option>
                            </select>
                        </div><?php */?>
                        
                    </div>
                    <label>Product description</label>
                    <div id="editor"><?=$getProduct['Description'];?></div>
                                        <label>Product Full description</label>
                    <div id="editor1"><?=$getProduct['FullDescription'];?></div>
                    <div class="TagsBlock">
					<label>Select Tags</label>
                    <?php 
					 $oldTags = $getProduct['Tags'];
                      $oldTagsArr = explode(',',$oldTags);
						
					?>
                    	<?php if(!empty($getProductActiveTags)){ ?>
                    
                    <select id="productTags" name="productTags[]" multiple>
                        <?php
						 foreach($getProductActiveTags as $singleTag){ ?>
                    	
                        <option value="<?php echo $singleTag['Id'] ?>" <?php if(in_array($singleTag['Id'],$oldTagsArr)) { echo "selected"; } ?>><?php echo $singleTag['TagName'] ?></option>
                        <?php } ?>
                    </select>
                        <?php }  else{ ?>
                    	<br>No tags available
					<?php } ?>
                    </div>
                    <?php /*?><div class="mb-4">
                        <label>Is this available in 3D?</label>
                        <br>
                        <label class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" value="yes" name="threedav" <?php if($getProduct['3D'] == 1) { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Yes</span>
                        </label>
                        <label class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" value="no" name="threedav" <?php if($getProduct['3D'] <> 1) { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">No</span>
                        </label>
                    </div><?php */?>

                    <div class="customeFilesHideShowBase">
                    <div class="mb-4">
                        <label>Is this available as motion?</label>
                        <br>
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" value="motion" name="feature[]" <?php if($getProduct['Motion'] > 0) { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Motion</span>
                        </label>
                        <?php /*?><label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" value="animated" name="feature[]" <?php if($getProduct['Animated'] > 0) { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Animated</span>
                        </label><?php */?>
                    </div>
                    </div>
                    <div class="customeFilesHideShowBase">

                    <div class="mb-4">
                        <label>Is the 3D title on this flyer editable?</label>
                        <br>
                        <label class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" value="yes" name="threededit" <?php if($getProduct['3dTextEditable'] == 1) { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Yes</span>
                        </label>
                        <label class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" value="no" name="threededit" <?php if($getProduct['3dTextEditable'] != 1) { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">No</span>
                        </label>

                    </div>
                    </div>
                    
                    <div class="row CustomProductFiledsSetting" <?php if($getProduct['Addon'] == 1) { echo 'style="display:none;"'; } ?>>
                    	<div class="col-md-12">
                            <label>Is this an custom product?</label>
                            <br>
                            <label class="custom-control custom-radio" style="margin-bottom: 10px;">
                                <input type="radio" class="custom-control-input" value="yes" name="CustomProduct" <?php if($getProduct['CustomProduct'] == "yes") { echo "checked"; } ?>>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Yes</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" value="no" name="CustomProduct" <?php if($getProduct['CustomProduct'] == "no" || $getProduct['CustomProduct']=="") { echo "checked"; } ?>>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">No</span>
                            </label>
                            
                            <div class="CustomProductFileds" <?php if($getProduct['CustomProduct'] == "yes") { echo 'style="display:block;"'; } ?>>
                            	  <label>Select Type</label>
                                <select class="form-control" name="selectFiledsType" id="selectFiledsType">
                                	<option value="animated_flyer"  <?php echo ($getProduct['CustomProductFiledsType']=="animated_flyer")?"selected":""; ?> >Animated Flyer</option>
                                    <option value="business_card"  <?php echo ($getProduct['CustomProductFiledsType']=="business_card")?"selected":""; ?> >Business Card</option>
                                    <option value="flyer_design" <?php echo ($getProduct['CustomProductFiledsType']=="flyer_design")?"selected":""; ?> >Flyer Design</option>
                                    <option value="flyer_conversion" <?php echo ($getProduct['CustomProductFiledsType']=="flyer_conversion")?"selected":""; ?> >Flyer Conversion</option>
                                      <option value="facebook_cover"  <?php echo ($getProduct['CustomProductFiledsType']=="facebook_cover")?"selected":""; ?> >Facebook Cover</option>
                                    <option value="logo_design"  <?php echo ($getProduct['CustomProductFiledsType']=="logo_design")?"selected":""; ?> >Logo Design</option>
                                    <option value="logo_intro"  <?php echo ($getProduct['CustomProductFiledsType']=="logo_intro")?"selected":""; ?> >Logo Intro</option>
                                   <option value="laptop_skin"  <?php echo ($getProduct['CustomProductFiledsType']=="laptop_skin")?"selected":""; ?> >Laptop Skin</option>
                                
                                    <option value="mixtape_cover_design"  <?php echo ($getProduct['CustomProductFiledsType']=="mixtape_cover_design")?"selected":""; ?> >Mixtape Cover Design</option>
                                    <option value="video_flyer"  <?php echo ($getProduct['CustomProductFiledsType']=="video_flyer")?"selected":""; ?> >Video Flyer</option>
                                    <option value="3d_logo_conversion"  <?php echo ($getProduct['CustomProductFiledsType']=="3d_logo_conversion")?"selected":""; ?> >3d Logo Conversion</option>
                                
                                   
                                    
                                </select>	
                            	<div class="FiledsSettings">
                                  <fieldset> 
                               
                                <legend>Fields Settings</legend>
                               	
                                <?php 
								$fieldsSettings = array();
								if(!empty($getProduct['CustomeProductfieldsSettings'])){
									$fieldsSettings = unserialize($getProduct['CustomeProductfieldsSettings']);	
								}
								foreach($customeProductFields as $singleKey=>$singleValue){
									?>
                                    <div class="row custom_fileds_block custom_fileds_block_<?php echo $singleKey; ?>">
                                    <?php 
									foreach($singleValue as $singleFiledsKey=>$singleFiledsValue){
									?>
                                    <div class="col-md-6">
                                    <div class="primaryTitleFileds"><?php echo str_replace("_"," ",$singleFiledsKey); ?></div>
                                   	<ul>
                                    
                                    	<?php
										 foreach($singleFiledsValue as $singleFiledsKey1=>$singleFiledsValue1){ ?>
                            <li>
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" name="CustomeProductfieldsSettings[<?php echo $singleKey?>][<?php echo $singleFiledsKey; ?>][<?php echo $singleFiledsKey1; ?>]" <?php if (!empty($fieldsSettings) && array_key_exists($singleFiledsKey1,$fieldsSettings[$singleFiledsKey])){ echo "checked";} ?> class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description"><?php echo $singleFiledsValue1['label']; ?></span>
                            </label>
                            </li>
                            <?php } ?>
                                    </ul>
                                    </div>
                                    <?php 
									}
									?>
                                    </div>
                                    <?php
								}
								
								?>
                                 <label class="custom-control custom-checkbox">
                                <input type="checkbox" name="custom_pro_tooltip" <?php if($getProduct['custom_pro_tooltip'] == 1) { echo "checked"; } ?> class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Tooltip</span>
                            </label>
                                <div class="toolTipBox">
                                
                                  <input type="text" name="custom_pro_tooltip_msg" class="form-control" placeholder="Please enter tooltip" value="<?=$getProduct['custom_pro_tooltip_msg'];?>">


                                </div>
                            
                            </fieldset>
                            </div>
                        </div>
                        
                    </div>
                    </div>
                    <div class="row customeFilesHideShowBase">
                    	<div class="col-md-6">
                            <label>Is this an Add on product?</label>
                            <br>
                            <label class="custom-control custom-radio" style="margin-bottom: 40px;">
                                <input type="radio" class="custom-control-input" value="yes" name="addon" <?php if($getProduct['Addon'] == 1) { echo "checked"; } ?>>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Yes</span>
                            </label>
                            <label class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" value="no" name="addon" <?php if($getProduct['Addon'] == 0) { echo "checked"; } ?>>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">No</span>
                            </label>
                        </div>
                        <div class="col-md-6" id="addonCategory" <?php if($getProduct['Addon'] == 0) { echo 'style="display:none;"'; } ?>>
                            <label>Addon Category</label>
                        	<br>
                            <select class="form-control" name="product_category_addon">
                                <?php 
								$addonCategoryList = GetMltRcrdsOnCndi(CATEGORIES_ADDON, "category_name != '' ORDER BY ID ASC");
								foreach($addonCategoryList as $addonCategoryData) { ?>
                                <option value="<?=$addonCategoryData['ID'];?>" <?php if($getProduct['Category'] == $addonCategoryData['ID']) { echo "selected"; } ?>><?=$addonCategoryData['category_name'];?></option>
                                <?php } ?>
                            </select>                     
                        </div>
                    </div>
                    
                    
                    
                    
                    <div class="row show_flyer_section customeFilesHideShowBase" <?php if($getProduct['Addon'] == 1) { echo 'style="display:none;"'; } ?>> 
                        <div class="col-md-6">
                            <label>Show flyer section</label>
                            <br>
                            <select name="show_flyer[]" multiple="multiple" class="select-flyer-section form-control">
                                <option value="is_regular" <?php echo ($getProduct['is_regular'] == 1) ? "selected" : ""; ?> >Regular</option>
                                <option value="is_seasonal" <?php echo ($getProduct['is_seasonal'] == 1) ? "selected" : ""; ?> >Seasonal</option>
                                <option value="is_sale_of_the_week" <?php echo ($getProduct['is_sale_of_the_week'] == 1) ? "selected" : ""; ?> >Sale of the week</option>
                            </select>
                            <br>
                        </div>
                    </div>

                    <?php if(!empty($addonProductList) && count($addonProductList) > 0){ ?>
                    <div class="row customeFilesHideShowBase" id="product_addon" <?php if($getProduct['Addon'] == 1) {  } ?> style="display:none;" >
                        <div class="col-md-6">
                            <label>Addons: </label>
                            <br>
                            <select name="product_addon_id[]" multiple="multiple" class="select-addon-product form-control">
                            <?php foreach ($addonProductList as $addonProductKey => $addonProductValue ){?>
                                <option value="<?php echo $addonProductValue['id']; ?>" <?php echo (in_array( $addonProductValue['id'], $selectedAddonProductList )) ? "selected" : ""; ?> ><?php echo $addonProductValue['Title']; ?></option>
                            <?php } ?>
                            </select>
                            <br>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="customeFilesHideShowBaseType">
                    <label>Select the default sizes for this product?</label>
                    <br>
                    <div class="mb-4 size-opt">
                    	<?php
						$oldDefaSizes = $getProduct['Defaultsizes'];
						$oldDefaSizesArr = explode(',',$oldDefaSizes);
						$getProdSizes = getProdSizeArr();
						foreach($getProdSizes as $keySize1 => $mainSize) { ?>
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" name="default_size[]" class="custom-control-input defaultSize" value="<?=$keySize1;?>" <?php if(in_array($keySize1,$oldDefaSizesArr)) { echo "checked"; } ?>>
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description"><?=$mainSize['name'];?></span>
                        </label>
                        <?php } ?>
                    </div>
                    </div>
                    <div class="customeFilesHideShowBase">

                    <label>What other sizes are available for this product?</label>
                    <br>
                    <div class="mb-4 size-opt">
                    	<?php
						$oldOtherSizes = $getProduct['OtherSizes'];
						$oldOtherSizesArr = explode(',',$oldOtherSizes);
                        foreach($getProdSizes as $keySize2 => $otherSize) { ?>
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" name="other_size[]" class="custom-control-input otherSizeCheck otherSizes other_<?=$keySize2;?>" value="<?=$keySize2;?>" <?php if(in_array($keySize2,$oldDefaSizesArr)) { echo "disabled"; } else { if(in_array($keySize2,$oldOtherSizesArr)) { echo "checked"; } } ?> >
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description"><?=$otherSize['name'];?></span>
                        </label>
                        <?php } ?>                        
                    </div>
                    </div>

                    <button type="submit" name="updateproduct" class="form-btn-grad btn-block">Update</button>
                </form>
				
                 <?php if( $_SESSION['userType'] == "admin"){ ?>
                <div class="row user-stats mt-5 pl-md-5 pr-md-5">
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <p>Product#:</p>
                        <h3><?=$productID;?></h3>
                        <?php 
						
						?>
                        <a href="<?=SITEURL;?>p/<?=$getProduct['slug'];?>" class="btn btn-blue">view product</a>
                    </div>
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <p>Sales:
                        </p>
                        <h3><?=GetNumOfRcrdsOnCndi(ORDER,"ProductID = '".$productID."'");?>
                        </h3>
                        <a href="<?=ADMINURL;?>orders.php?productId=<?=$productID;?>" class="btn btn-blue">view orders
                        </a>
                    </div>
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <p>Sales amount:</p>
                        <h3>$<?= GetSumOnCndi(ORDER, "TotalPrice", "ProductID = '".$productID."'"); ?></h3>
                        <a href="<?=ADMINURL;?>transactions.php?productId=<?=$productID;?>" class="btn btn-blue">view transactions</a>
                    </div>
                    <?php /*?><div class="col-lg-3 col-sm-6 mb-4">
                        <p>Related products:</p>
                        <h3><?= GetNumOfRcrdsOnCndi(PRODUCT_REL, "ProductID = '".$productID."'"); ?></h3>
                        <a href="<?=ADMINURL;?>manage-related-products.php?productId=<?=$productID;?>" class="btn btn-blue">view related products</a>
                    </div><?php */?>
                </div>
                <?php } ?>
            </div>


        </div>
    </main>

    <?php include "includes/footer.php"; ?>
    
    <div id="trumbowyg-icons">
        <svg xmlns="http://www.w3.org/2000/svg">
            <symbol id="trumbowyg-del" viewBox="0 0 72 72">
                <path d="M45.8 45c0 1-.3 1.9-.9 2.8-.6.9-1.6 1.6-3 2.1s-3.1.8-5 .8c-2.1 0-4-.4-5.7-1.1-1.7-.7-2.9-1.7-3.6-2.7-.8-1.1-1.3-2.6-1.5-4.5l-.1-.8-6.7.6v.9c.1 2.8.9 5.4 2.3 7.6 1.5 2.3 3.5 4 6.1 5.1 2.6 1.1 5.7 1.6 9.4 1.6 2.9 0 5.6-.5 8-1.6 2.4-1.1 4.3-2.7 5.6-4.7 1.3-2 2-4.2 2-6.5 0-1.6-.3-3.1-.9-4.5l-.2-.6H44c0 .1 1.8 2.3 1.8 5.5zM29 28.9c-.8-.8-1.2-1.7-1.2-2.9 0-.7.1-1.3.4-1.9.3-.6.7-1.1 1.4-1.6.6-.5 1.4-.9 2.5-1.1 1.1-.3 2.4-.4 3.9-.4 2.9 0 5 .6 6.3 1.7 1.3 1.1 2.1 2.7 2.4 5.1l.1.9 6.8-.5v-.9c-.1-2.5-.8-4.7-2.1-6.7s-3.2-3.5-5.6-4.5c-2.4-1-5.1-1.5-8.1-1.5-2.8 0-5.3.5-7.6 1.4-2.3 1-4.2 2.4-5.4 4.3-1.2 1.9-1.9 3.9-1.9 6.1 0 1.7.4 3.4 1.2 4.9l.3.5h11.8c-2.3-.9-3.9-1.7-5.2-2.9zm13.3-6.2zM22.7 20.3zM13 34.1h46.1v3.4H13z"
                />
            </symbol>
            <symbol id="trumbowyg-em" viewBox="0 0 72 72">
                <path d="M26 57l10.1-42h7.2L33.2 57H26z" />
            </symbol>
            <symbol id="trumbowyg-horizontal-rule" viewBox="0 0 72 72">
                <path d="M9.1 32h54v8h-54z" />
            </symbol>
            <symbol id="trumbowyg-italic" viewBox="0 0 72 72">
                <path d="M26 57l10.1-42h7.2L33.2 57H26z" />
            </symbol>
            <symbol id="trumbowyg-justify-center" viewBox="0 0 72 72">
                <path d="M9 14h54v8H9zM9 50h54v8H9zM18 32h36v8H18z" />
            </symbol>
            <symbol id="trumbowyg-justify-full" viewBox="0 0 72 72">
                <path d="M9 14h54v8H9zM9 50h54v8H9zM9 32h54v8H9z" />
            </symbol>
            <symbol id="trumbowyg-justify-left" viewBox="0 0 72 72">
                <path d="M9 14h54v8H9zM9 50h54v8H9zM9 32h36v8H9z" />
            </symbol>
            <symbol id="trumbowyg-justify-right" viewBox="0 0 72 72">
                <path d="M9 14h54v8H9zM9 50h54v8H9zM27 32h36v8H27z" />
            </symbol>
            <symbol id="trumbowyg-ordered-list" viewBox="0 0 72 72">
                <path d="M27 14h36v8H27zM27 50h36v8H27zM27 32h36v8H27zM11.8 15.8V22h1.8v-7.8h-1.5l-2.1 1 .3 1.3zM12.1 38.5l.7-.6c1.1-1 2.1-2.1 2.1-3.4 0-1.4-1-2.4-2.7-2.4-1.1 0-2 .4-2.6.8l.5 1.3c.4-.3 1-.6 1.7-.6.9 0 1.3.5 1.3 1.1 0 .9-.9 1.8-2.6 3.3l-1 .9V40H15v-1.5h-2.9zM13.3 53.9c1-.4 1.4-1 1.4-1.8 0-1.1-.9-1.9-2.6-1.9-1 0-1.9.3-2.4.6l.4 1.3c.3-.2 1-.5 1.6-.5.8 0 1.2.3 1.2.8 0 .7-.8.9-1.4.9h-.7v1.3h.7c.8 0 1.6.3 1.6 1.1 0 .6-.5 1-1.4 1-.7 0-1.5-.3-1.8-.5l-.4 1.4c.5.3 1.3.6 2.3.6 2 0 3.2-1 3.2-2.4 0-1.1-.8-1.8-1.7-1.9z"
                />
            </symbol>
            <symbol id="trumbowyg-strikethrough" viewBox="0 0 72 72">
                <path d="M45.8 45c0 1-.3 1.9-.9 2.8-.6.9-1.6 1.6-3 2.1s-3.1.8-5 .8c-2.1 0-4-.4-5.7-1.1-1.7-.7-2.9-1.7-3.6-2.7-.8-1.1-1.3-2.6-1.5-4.5l-.1-.8-6.7.6v.9c.1 2.8.9 5.4 2.3 7.6 1.5 2.3 3.5 4 6.1 5.1 2.6 1.1 5.7 1.6 9.4 1.6 2.9 0 5.6-.5 8-1.6 2.4-1.1 4.3-2.7 5.6-4.7 1.3-2 2-4.2 2-6.5 0-1.6-.3-3.1-.9-4.5l-.2-.6H44c0 .1 1.8 2.3 1.8 5.5zM29 28.9c-.8-.8-1.2-1.7-1.2-2.9 0-.7.1-1.3.4-1.9.3-.6.7-1.1 1.4-1.6.6-.5 1.4-.9 2.5-1.1 1.1-.3 2.4-.4 3.9-.4 2.9 0 5 .6 6.3 1.7 1.3 1.1 2.1 2.7 2.4 5.1l.1.9 6.8-.5v-.9c-.1-2.5-.8-4.7-2.1-6.7s-3.2-3.5-5.6-4.5c-2.4-1-5.1-1.5-8.1-1.5-2.8 0-5.3.5-7.6 1.4-2.3 1-4.2 2.4-5.4 4.3-1.2 1.9-1.9 3.9-1.9 6.1 0 1.7.4 3.4 1.2 4.9l.3.5h11.8c-2.3-.9-3.9-1.7-5.2-2.9zm13.3-6.2zM22.7 20.3zM13 34.1h46.1v3.4H13z"
                />
            </symbol>
            <symbol id="trumbowyg-strong" viewBox="0 0 72 72">
                <path d="M51.1 37.8c-1.1-1.4-2.5-2.5-4.2-3.3 1.2-.8 2.1-1.8 2.8-3 1-1.6 1.5-3.5 1.5-5.3 0-2-.6-4-1.7-5.8-1.1-1.8-2.8-3.2-4.8-4.1-2-.9-4.6-1.3-7.8-1.3h-16v42h16.3c2.6 0 4.8-.2 6.7-.7 1.9-.5 3.4-1.2 4.7-2.1 1.3-1 2.4-2.4 3.2-4.1.9-1.7 1.3-3.6 1.3-5.7.2-2.5-.5-4.7-2-6.6zM40.8 50.2c-.6.1-1.8.2-3.4.2h-9V38.5h8.3c2.5 0 4.4.2 5.6.6 1.2.4 2 1 2.7 2 .6.9 1 2 1 3.3 0 1.1-.2 2.1-.7 2.9-.5.9-1 1.5-1.7 1.9-.8.4-1.7.8-2.8 1zm2.6-20.4c-.5.7-1.3 1.3-2.5 1.6-.8.3-2.5.4-4.8.4h-7.7V21.6h7.1c1.4 0 2.6 0 3.6.1s1.7.2 2.2.4c1 .3 1.7.8 2.2 1.7.5.9.8 1.8.8 3-.1 1.3-.4 2.2-.9 3z"
                />
            </symbol>
            <symbol id="trumbowyg-underline" viewBox="0 0 72 72">
                <path d="M36 35zM15.2 55.9h41.6V59H15.2zM21.1 13.9h6.4v21.2c0 1.2.1 2.5.2 3.7.1 1.3.5 2.4 1 3.4.6 1 1.4 1.8 2.6 2.5 1.1.6 2.7 1 4.8 1 2.1 0 3.7-.3 4.8-1 1.1-.6 2-1.5 2.6-2.5.6-1 .9-2.1 1-3.4.1-1.3.2-2.5.2-3.7V13.9H51v23.3c0 2.3-.4 4.4-1.1 6.1-.7 1.7-1.7 3.2-3 4.4-1.3 1.2-2.9 2-4.7 2.6-1.8.6-3.9.9-6.1.9-2.2 0-4.3-.3-6.1-.9-1.8-.6-3.4-1.5-4.7-2.6-1.3-1.2-2.3-2.6-3-4.4-.7-1.7-1.1-3.8-1.1-6.1V13.9z"
                />
            </symbol>
            <symbol id="trumbowyg-unordered-list" viewBox="0 0 72 72">
                <path d="M27 14h36v8H27zM27 50h36v8H27zM9 50h9v8H9zM9 32h9v8H9zM9 14h9v8H9zM27 32h36v8H27z" />
            </symbol>
            <symbol id="trumbowyg-col-delete" viewBox="0 0 24 24">
                <g transform="translate(-326 -532.36)">
                    <rect width="6" height="15" x="335" y="537.36" fill="#ed5565" stroke="#000" stroke-linejoin="round" stroke-linecap="round"
                        stroke-width=".837" rx=".646" />
                    <path d="M347.58 536.498c-.051-.618-.55-1.138-1.178-1.138H329.6c-.628 0-1.127.52-1.18 1.138h-.02v15.663a1.2 1.2 0 0 0 1.2 1.2h16.801a1.2 1.2 0 0 0 1.2-1.2v-15.663h-.021M334.4 552.16h-4.8v-3.6h4.8v3.6m0-4.7h-4.8v-3.7h4.8v3.7m0-4.9h-4.8v-3.601h4.8v3.6m6 9.601h-4.8v-3.6h4.8v3.6m0-4.7h-4.8v-3.7h4.8v3.7m0-4.9h-4.8v-3.601h4.8v3.6m6.001 9.601h-4.8v-3.6h4.8v3.6m0-4.7h-4.8v-3.7h4.8v3.7m0-4.9h-4.8v-3.601h4.8v3.6"
                    />
                </g>
            </symbol>
            
        </svg>
    </div> 


    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/trumbowyg.min.js"></script>
    <script src="js/script.js"></script>
    <script src="../plugins/select2/js/select2.min.js"></script>
    <script src="js/jquery.multiselect.js"></script>
    <script>
        $(document).ready(function () {
			
			
			<?php 
			if($getProduct['CustomProduct']=="yes") {
			?>
				jQuery(".CustomProductFileds").show(500);
				jQuery(".customeFilesHideShowBase").hide();
				<?php if($getProduct['CustomProductFiledsType']=="flyer_design"  || $getProduct['CustomProductFiledsType']=="mixtape_cover_design" || $getProduct['CustomProductFiledsType']=="animated_flyer") { ?>
				jQuery(".customeFilesHideShowBaseType").show();	
				<?php }else{ ?>
				jQuery(".customeFilesHideShowBaseType").hide();
				<?php } ?>
				
			<?php 
			}else{
			?>
			jQuery(".CustomProductFileds").hide(500);
				jQuery(".customeFilesHideShowBase").show();
			<?php } ?>
			$("input[name='CustomProduct']").change(function(){
				var  selectFiledsType = jQuery("#selectFiledsType").val();
				 checkTemplateType();
					jQuery("input[name='template_type']:eq(1)").attr("checked",false);
				if ($(this).val() === 'yes') {
					
					jQuery("input[name='template_type']:eq(0)").attr("checked",true);
					jQuery(".CustomProductFileds").show(500);
					jQuery(".customeFilesHideShowBase").hide();
					if(selectFiledsType=="flyer_design"  || selectFiledsType=="mixtape_cover_design" || selectFiledsType=="animated_flyer" ){
						jQuery(".customeFilesHideShowBaseType").show();	
					}else{
						jQuery(".customeFilesHideShowBaseType").hide();
					}
					
				} else {
					jQuery(".CustomProductFileds").hide(500);
					jQuery(".customeFilesHideShowBase").show();
					jQuery(".customeFilesHideShowBaseType").show();

				}
				
			});
			$("#selectFiledsType").change(function(){
				var  selectFiledsType = jQuery("#selectFiledsType").val();
				 
					if(selectFiledsType=="flyer_design"  || selectFiledsType=="mixtape_cover_design" || selectFiledsType=="animated_flyer" ){
						jQuery(".customeFilesHideShowBaseType").show();	
					}else{
						jQuery(".customeFilesHideShowBaseType").hide();
					}
					
				
			});
			
			
			
			$("input[name='addon']").change(function(){
				if ($(this).val() === 'yes') {
					$("#addonCategory").show(500);
                    $("#product_addon").hide(500);
					jQuery(".CustomProductFiledsSetting").hide(500);
					
					jQuery(".show_flyer_section").hide(500);
				} else {
					$("#addonCategory").hide(500);
                    $("#product_addon").hide(500);
					jQuery(".show_flyer_section").show(500);
					jQuery(".CustomProductFiledsSetting").show(500);

				}
			});
            $('#editor,#editor1').trumbowyg({
                btns: [
				    ['viewHTML'],
					['strong', 'em', 'del'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['unorderedList', 'orderedList'],
                    ['horizontalRule'],
                ],
                svgPath: '/assets/icons.svg'
            });
			$("#tags").select2({
				placeholder: "Select Tags"
			});
			$('.close-ntf').click(function() {
				$(this).parent().fadeOut(300, function() {
					$(this).hide();
				});
			});
			setTimeout(function(){ $('.close-ntf').click(); }, 12000);
			$(".defaultSize").on("change",function(){
				var $thisVal = $(this).val();
				if($(this).is(":checked")) {
					if($('.other_'+$thisVal).is(":checked")) {
						$('.other_'+$thisVal).click();
					}
					$('.other_'+$thisVal).attr("disabled", true);
				} else {
					$('.other_'+$thisVal).attr("disabled", false);
				}
			});
        });

        /* Dropdown */
        $(function () {
            $('select[multiple].select-flyer-section').multiselect({
                columns: 1,
                placeholder: 'Select Section',
                search: true,
                searchOptions: {
                    'default': 'Search Section'
                },
                selectAll: true
            });

            $('select[multiple].select-addon-product').multiselect({
                columns: 1,
                placeholder: 'Select Addons',
                search: true,
                searchOptions: {
                    'default': 'Search Addons'
                },
                selectAll: true
            });

        });
		
		jQuery(document).ready(function(e) {
            jQuery("#parent_product_types").change();
			
			
   	 });
	jQuery(document).on("change","#parent_product_types",function(e) {
				var id= jQuery(this).val();
					var child_id='<?php echo $getProduct['child_product_cat_id'] ; ?>';
				var  $ = jQuery;
             $("#child_product_types").empty();
			 $("#child_product_types").html("<option value='0'>Please select child product type</option>");
			 if(id>0){
			      
				
				 jQuery.ajax({
					type: "POST",
					url: "<?=SITEURL;?>ajax/child-product-types.php",
					data: "id="+id+"&child_id="+child_id,
					success: function(regResponse) {
						regResponse = JSON.parse(regResponse);
						$("#child_product_types").html(regResponse.html);
					}
					
					
				});
				}
         	 });

    </script>


<script>
jQuery(document).ready(function() {
	jQuery('#productTags').multiselect({
            maxHeight: 200,
			 search: true,
			 texts: {
            	placeholder    : 'Select tags',
			 },
         
        });
});
function changeSlug(name){

var replacestring = name.replace(/ /g, "-");
    jQuery("#slug").val(replacestring.toLowerCase());
	
}
jQuery('#slug').keyup(function() {
var name = jQuery(this).val();
jQuery(this).val(name.replace(/ /g, "-").toLowerCase());
});
jQuery(document).on("change","#selectFiledsType",function(){
		var v  = jQuery(this).val();
		jQuery(".custom_fileds_block").hide();
		jQuery(".custom_fileds_block_"+v).css("display","flex");
		
	});
jQuery(document).ready(function(e) {
    jQuery("#selectFiledsType").trigger("change");
	checkTemplateType();
});	
function checkTemplateType(){
var template_type = jQuery("input[name='template_type']:checked").val();
var CustomProduct = jQuery("input[name='CustomProduct']:checked").val();
var psd_file_hidden = jQuery("#psd_file_hidden").val();

	jQuery(".psd_customized_block").hide();
	jQuery("input[name='psd_price'],input[name='psd_file']").removeAttr("required");
	jQuery(".customeFilesHideShowBaseType,.customeFilesHideShowBase,.priceBox").show();
	if((template_type=="psd_customize_template" || template_type=="psd_only") && CustomProduct=="no"){
		jQuery(".psd_customized_block").show();
		
		
	if(template_type=="psd_only"){
			jQuery(".customeFilesHideShowBaseType,.customeFilesHideShowBase,.priceBox").hide();	
		}
		if(psd_file_hidden==""){
			
		jQuery("input[name='psd_file']").attr("required","required");	
		}
		
		jQuery("input[name='psd_price']").attr("required","required");
		
				
	}
}
jQuery(document).on("change",".template_type",function(){
	checkTemplateType();
});

jQuery(document).on("change","#psd_file",function(){
		 var ele = document.getElementById('psd_file');
    	 var result = ele.files;
    	 var arrayListing = "";	
		 for(var x = 0;x< result.length;x++){
    		 var fle = result[x];
        	arrayListing += "<label>"+fle.name+"</label>";
			      
    	}
		jQuery(".outputFilename").html(arrayListing);  
	});
	
</script>
<style>.trumbowyg-button-pane button.trumbowyg-viewHTML-button{
    background: url(images/arrow_source.png);
    background-repeat: no-repeat;
    background-position: center;
}
.outputFilename{display: block;clear: both;width: 100%;margin-left: 10px; margin-top:5px;font-weight: bold;}
.outputFilename label::after {
	content: ", ";
	width: 10px;
	display: inline-block;
}
.outputFilename label:last-child::after{ display:none;}

</style>
</body>

</html>