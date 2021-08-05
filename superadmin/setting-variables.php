<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
require_once '../function/adminSession.php';

$PageTitle = "Settings";

if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
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
    } elseif($_REQUEST['action_for'] == "flyer") {
        $actionTable = FLYERS;
        $actionTitle = "Flyer";
    } elseif($_REQUEST['action_for'] == "product_option") {
        $actionTable = OPTION_PRICE;
        $actionTitle = "Product Option";
    } else {
        header("location:setting-variables.php");
        exit();
    }
    $recordID = intval($_REQUEST['deleteID']);
    if($_REQUEST['action_for'] == "product_category") {

        DltSglRcrd($actionTable, "cat_id = '$recordID'");
    } elseif($_REQUEST['action_for'] == "product_type") {
        DltSglRcrd($actionTable, "ID = '$recordID'");
    } elseif($_REQUEST['action_for'] == "product_size") {
        DltSglRcrd($actionTable, "ID = '$recordID'");
    } elseif($_REQUEST['action_for'] == "flyer") {
        DltSglRcrd($actionTable, "id = '$recordID'");
    } elseif($_REQUEST['action_for'] == "product_addon") {
        DltSglRcrd($actionTable, "id = '$recordID'");
    } elseif($_REQUEST['action_for'] == "product_option") {
        DltSglRcrd($actionTable, "id = '$recordID'");
    }
    unset($_SESSION['SUCCESS']);
    $_SESSION['SUCCESS'] = $actionTitle." successfully deleted.";
    header("location:setting-variables.php");
    exit();
}


$prodTypeArr = getProdTypeParentArr();
$prodSizeArr = getProdSizeArr();
$prodAddoArr = getProdAddonArr();
$flyersArr = getFlyers();
$proOptionArr = getProdOptionTypeArr();
$settingarr=GetSglRcrdOnCndi(SETTINGS, "id=1");


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
<div class="notification success" id="success-msg" style="display:none;">
    <div class="d-flex"><i class="fas fa-check"></i></div>
    <span><?='Auto assign updated successfully.';?></span>
    <button class="close-ntf"><i class="fas fa-times"></i></button>
</div>
<main class="main-content-wrap">
    <div class="container">
        <div class="main-content pl-60 pr-60 bx-shadow">
            <h1 class="page-heading mb-4">Website Setting</h1>
            <section class="overall-totals mt-4 mb-5">
                <h2 class="blue mb-3">Overall totals</h2>
                <div class="row">
                    <?php /*?><div class="col-lg-3 col-sm-6 mb-4">
                        <div class="totals-box red">
                            <p>Product Categories</p>
                            <h1><?=count($prodCateArr);?></h1>
                        </div>
                    </div><?php */?>
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <div class="totals-box cyan">
                            <p>Product Types</p>
                            <h1><?=count($prodTypeArr);?></h1>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <div class="totals-box purple">
                            <p>Product Sizes</p>
                            <h1><?=count($prodSizeArr);?></h1>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 mb-4">
                        <div class="totals-box blue">
                            <p>Product Addon Prices</p>
                            <h1><?=count($prodAddoArr);?></h1>
                        </div>
                    </div>
                </div>
            </section>
            <div class="row">
            <div class="col-md-6">
            <section class="overall-totals mt-4 mb-5">
                <h2 class="blue mb-3">Auto-assign</h2>
                <div class="row">
                    <div class="col-lg-3 col-sm-6 mb-4">
                        <label class="switch">
                            <input type="checkbox" name="auto_assign" value="Yes" id="auto_assign" <?php if($settingarr['auto_assign']=='Yes'){ echo 'checked'; } ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </section>
            </div>
            
            </div>
            <section class="totals-listing">
                <div class="row">
                    <?php /*?><div class="col-lg-6 mb-4 brd-lg-right">
                        <h2 class="blue mb-3">Product Categories <a href="setting-variable-manage.php?action=add&action_for=product_category" class="addnewlink">Add New</a></h2>
                        <div>
                            <table class="table transactions-table sorting table-1">
                                <thead>
                                <tr>
                                    <th scope="col">S.No.</th>
                                    <th scope="col">Title</th>
                                    <th scope="col" colspan="2">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                foreach($prodCateArr as $key1 => $categoryArr)
                                {
                                    ?>
                                    <tr>
                                        <td class="data-id"><span><?=$key1;?></span></td>
                                        <td><?=$categoryArr;?></td>
                                        <td class="data-view"><a href="setting-variable-manage.php?action=update&action_for=product_category&record_id=<?=$key1;?>" class="view">view</a></td>
                                        <td class="data-delete"> <a href="?action_for=product_category&deleteID=<?=$key1;?>" onclick="return confirm('Are you sure you want to perform this action?')"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div><?php */?>
                    <?php /*?><?php */?><div class="col-lg-6 mb-4 brd-lg-right">
                        <h2 class="blue mb-3">Product Types </h2>
                        <div>
                            <table class="table transactions-table sorting table-1">
                                <thead>
                                <tr>
                                    <th scope="col">S.No.</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Price</th>
                                    <th scope="col" colspan="2">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
								$n=0;
                                foreach($prodTypeArr as $key2 => $typeArr)
                                {
									$n++;
                                    ?>
                                    <tr>
                                        <td class="data-id"><span><?=$n;?></span></td>
                                        <td><?=$typeArr['name'];?></td>
                                        <td>$<?=$typeArr['price'];?></td>
                                        <td class="data-view" colspan="2"><a href="setting-variable-manage.php?action=update&action_for=product_type&record_id=<?=$key2;?>" class="view">view</a></td>
                                      
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    
                    <div class="col-lg-6 mb-4">
                    
                    <div class="row">
                        <div class="col-lg-12 mb-4">
                        <h2 class="blue mb-3">Top of homepage flyers <a href="add-flyer.php" class="addnewlink">Add New</a></h2>
                        <div>
                            <table class="table transactions-table sorting table-1" style="border:1px solid #d9d9d9">
                                <thead>
                                <tr>
                                    <th scope="col">S.No.</th>
                                    <th scope="col">Sort Order</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" colspan="3">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
								$n=0;
                                foreach($flyersArr as $key5 => $flyerArr){ 
								$n++;
								?>
                                    <tr>
                                        <td class="data-id"><span><?=$n;?></span></td>
                                        <td><?=$flyerArr['sort_order'];?></td>
                                        <td><?=($flyerArr['is_active'] == 1)?"Active":"InActive";?></td>
                                        <td class="data-view" colspan="2"><a href="add-flyer.php?id=<?=$flyerArr['id'];?>" class="view">view</a></td>
                                        <td class="data-delete"><a href="?action_for=flyer&deleteID=<?=$flyerArr['id'];?>" onclick="return confirm('Are you sure you want to perform this action?')"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>
                                    <?php
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    
                        </div>
                           <div class="row">
                        <div class="col-lg-12 mb-4">
                    	 <h2 class="blue mb-3">Product Options <a href="setting-variable-manage.php?action=add&action_for=product_option" class="addnewlink">Add New</a></h2>
                            <div>
                                <table class="table transactions-table sorting table-1">
                                    <thead>
                                    <tr>
                                        <th scope="col">S.No.</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">Price</th>
                                        <th scope="col" colspan="3">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									$n=0;
                                    foreach($proOptionArr as $key2 => $typeArr)
                                    {
										$n++;
                                        ?>
                                        <tr>
                                            <td class="data-id"><span><?=$n;?></span></td>
                                            <td><?=$typeArr['name'];?></td>
                                            <td>$<?=$typeArr['price'];?></td>
                                            <td class="data-view" colspan="2"><a href="setting-variable-manage.php?action=update&action_for=product_option&record_id=<?=$key2;?>" class="view">view</a></td>
                                            <td class="data-delete"> <a href="?action_for=product_option&deleteID=<?=$key2;?>" onclick="return confirm('Are you sure you want to perform this action?')"><i class="fas fa-trash-alt"></i></a></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            </div>
                            </div>
                     
                    </div>

                </div>
                <div class="row">
                    
                    <div class="col-lg-6 mb-4 brd-lg-right">
                        <h2 class="blue mb-3">Product Addon Prices <a href="setting-variable-manage.php?action=add&action_for=product_addon" class="addnewlink">Add New</a></h2>
                        <div>
                            <table class="table transactions-table sorting table-1">
                                <thead>
                                <tr>
                                    <th scope="col">S.No.</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Price</th>
                                    <th scope="col"<?php /*?> colspan="3"<?php */?>>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sn = 1;
								$n=0;
                                foreach($prodAddoArr as $key4 => $addonArr)
                                {
									$n++;
                                    ?>
                                    <tr>
                                        <td class="data-id"><span><?=$n;?></span></td>
                                        <td><?=$addonArr['name'];?></td>
                                        <td>$<?=$addonArr['price'];?></td>
                                        <td class="data-view"<?php /*?> colspan="2"<?php */?>><a href="setting-variable-manage.php?action=update&action_for=product_addon&record_id=<?=$key4;?>" class="view">view</a></td>
                                        <?php /*?><td class="data-delete"> <a href="?action_for=product_addon&deleteID=<?=$key4;?>" onclick="return confirm('Are you sure you want to perform this action?')"><i class="fas fa-trash-alt"></i></a></td><?php */?>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        
                        	<br>
                           <h2 class="blue mb-3">Form Fields Tooltip</h2>
                        <div>
                            <table class="table transactions-table sorting table-1">
                                <thead>
                                <tr>
                                    <th scope="col">S.No.</th>
                                    <th scope="col" style="width:215px;">Title</th>
                                  
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sn = 1;
								$n=0;
								 foreach($templateFiledsSettings as $key => $single)
                                {
									$n++;
                                    ?>
                                    <tr>
                                        <td class="data-id"><span><?=$n;?></span></td>
                                        <td><?=$single;?></td>
                                        <td class="data-view"<?php /*?> colspan="2"<?php */?>><a href="setting-variable-manage.php?action_for=tooltip_update&id=<?=$key;?>" class="view" style=" width:70px; display:inline-block;">Edit</a></td>
                                        <?php /*?><td class="data-delete"> <a href="?action_for=product_addon&deleteID=<?=$key4;?>" onclick="return confirm('Are you sure you want to perform this action?')"><i class="fas fa-trash-alt"></i></a></td><?php */?>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <h2 class="blue mb-3">Product Sizes <a href="setting-variable-manage.php?action=add&action_for=product_size" class="addnewlink">Add New</a></h2>
                        <div>
                            <table class="table transactions-table sorting table-1">
                                <thead>
                                <tr>
                                    <th scope="col">S.No.</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Price</th>
                                    <th scope="col" colspan="3">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
								$n=0;

                                foreach($prodSizeArr as $key3 => $sizeArr)
                                {
									$n++;
                                    ?>
                                    <tr>
                                        <td class="data-id"><span><?=$n;?></span></td>
                                        <td><?=$sizeArr['name'];?></td>
                                        <td>$<?=$sizeArr['price'];?></td>
                                        <td class="data-view" colspan="2"><a href="setting-variable-manage.php?action=update&action_for=product_size&record_id=<?=$key3;?>" class="view">view</a></td>
                                        <td class="data-delete"> <a href="?action_for=product_size&deleteID=<?=$key3;?>" onclick="return confirm('Are you sure you want to perform this action?')"><i class="fas fa-trash-alt"></i></a></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                        
                    </div>
                    

                    
                </div>
            </section>

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
    $('#auto_assign').click(function() {
        var ischecked= $(this).is(':checked');

        if(!ischecked)
        {
            var auto_assign='No';
        }
        else
        {
            var auto_assign='Yes';
        }
        $.ajax({
            type: "POST",
            url: "<?=SITEURL;?>ajax/auto-assign.php",
            data: "auto_assign="+auto_assign,
            success: function(regResponse) {
                $('#success-msg').css("display", "flex");

                /*console.log(regResponse);*/



            }
        });
    });
</script>
</body>
</html>