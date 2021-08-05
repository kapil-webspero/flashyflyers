<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Product Types";
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="create" && $_REQUEST['create_category']=="create") {
		
		if($_REQUEST['product_type_option']=="parent"){
				$_REQUEST['name'] = $_REQUEST['Parentname'];
				$_REQUEST['parent_type'] = 0;
		}
		if($_REQUEST['name']==""){
			$_SESSION['ERROR'] = "Please enter name";		
		}else{
	
		$checkCodequery = "SELECT COUNT(*) as num FROM ".PRODUCT_TYPE." WHERE Name = '".$_REQUEST['name']."' and parent_id='".$_REQUEST['parent_type']."'";

			   $checkCodeRs = mysql_fetch_array(mysql_query($checkCodequery));
				if($checkCodeRs['num']>0){
						
						$_SESSION['ERROR'] = "Sorry, product type already exists.Please choose another product type.";	
				}else{
					
				
	
	if($_REQUEST['parent_type'] != 0)
	{
		$path = addslashes(getProductTypesPathById($_REQUEST['parent_type']))." > ";
	}
		$path .=$_REQUEST['name'];	
			$price =$design_labor_cost= 0;
			$fieldsSettings= "";
			if($_REQUEST['fieldsSettings']!="" && !empty($_REQUEST['fieldsSettings'])){
				$fieldsSettings = serialize($_REQUEST['fieldsSettings']);
			}
			
			InsertRcrdsByData(PRODUCT_TYPE,"`parent_id` = '".$_REQUEST['parent_type']."',`Price` = '".$price."',`design_labor_cost` = '".$design_labor_cost."',`fieldsSettings` = '".$fieldsSettings."', `Name` = '".$_REQUEST['name']."', `path` = '".$path."'");
			$_SESSION['SUCCESS'] = "Product type has been successfully added.";
			
			
			
			}
		}
				
		
	}
	
	
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit" && $_REQUEST['update_category']=="edit") {
	
			
		if($_REQUEST['product_type_option']=="parent"){
				$_REQUEST['name'] = $_REQUEST['Parentname'];
				$_REQUEST['parent_type'] = 0;
		}
		
		if($_REQUEST['name']==""){
			$_SESSION['ERROR'] = "Please enter name";		
		}else{
			
			$checkCodequery = "SELECT COUNT(*) as num FROM ".PRODUCT_TYPE." WHERE ID <> '".$_REQUEST['ID']."' and Name = '".$_REQUEST['name']."' and parent_id='".$_REQUEST['parent_type']."'";
			   $checkCodeRs = mysql_fetch_array(mysql_query($checkCodequery));
				if($checkCodeRs['num']>0){
						
						$_SESSION['ERROR'] = "Sorry, product type already exists.Please choose another product type.";	
				}else{
					
				
	
	if($_REQUEST['parent_type'] != 0)
	{
		$path = addslashes(getProductTypesPathById($_REQUEST['parent_type']))." > ";
	}
	
	$price =$design_labor_cost= 0;
	$fieldsSettings= "";
			if($_REQUEST['fieldsSettings']!="" && !empty($_REQUEST['fieldsSettings'])){
				$fieldsSettings = serialize($_REQUEST['fieldsSettings']);
			}
	
			$path .=$_REQUEST['name'];	
			UpdateRcrdOnCndi(PRODUCT_TYPE, "`parent_id` = '".$_REQUEST['parent_type']."', `fieldsSettings` = '".$fieldsSettings."', `Name` = '".$_REQUEST['name']."', `path` = '".$path."'","`ID` = '".$_REQUEST['ID']."'");
			$_SESSION['SUCCESS'] = "Product type successfully updated.";
			}
		}
				
		
	}
	
	if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
		$deleteID = intval($_REQUEST['deleteID']);
		DltSglRcrd(PRODUCT_TYPE, "`ID` = '$deleteID'");
		DltSglRcrd(PRODUCT_TYPE, "`parent_id` = '$deleteID'");
				$_SESSION['SUCCESS'] = "Product type has been successfully deleted.";
		echo "<script> window.location.href = '".ADMINURL."product-types.php';</script>";
		exit();
	}
	
	
	
$cat_assoc_arr = getProductTypesDetails();
$par_cat_array = getParentProductTypesList(0, $old_cat="", $menu_id, 1, 1);
$fieldsSettings = array();
$ParentIDType = 0;
if($_REQUEST['mode']=="edit" && $_REQUEST['ID']>0){
	$ID = $_REQUEST['ID'];
	$sql_sel = "SELECT * FROM ".PRODUCT_TYPE." WHERE ID = '".$ID."' LIMIT 1";
	$rs_types = mysql_fetch_assoc(mysql_query($sql_sel));
	
	if(empty($rs_types)){
		echo "<script> window.location.href = '".ADMINURL."product-types.php';</script>";
		exit();
	}
	$ID = $rs_types['ID'];
	$name= $rs_types['Name'];
	$path = $rs_types['path'];
	$parent_id = $rs_types['parent_id'];
	$fieldsSettings = $rs_types['fieldsSettings'];
	$Price = $rs_types['Price'];
	$design_labor_cost = $rs_types['design_labor_cost'];
	$ParentIDType = $rs_types['parent_id'];
	if(!empty($fieldsSettings)){
		$fieldsSettings = unserialize($fieldsSettings);	
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
	<?php
	//Pagination Code
	
	$searchTable = PRODUCT_TYPE;
	$searchQuery = "";	
	$searchURL = $_SERVER['PHP_SELF']."?search=trueval";
	
	
	
	//$searchQuery .= " parent_id > 0 ";
	if(!empty($_REQUEST['name']) && !empty($_REQUEST['name'])) {
		$searchQuery .= " name LIKE '%".$_REQUEST['name']."%' AND";
		$searchURL .= "&name=".$_REQUEST['name'];
	}	
	
	if(!empty($_REQUEST['parent_type_search']) && !empty($_REQUEST['parent_type_search'])) {
		$searchQuery .= "parent_id ='".$_REQUEST['parent_type_search']."' AND ";
		$searchURL .= "&parent_type_search=".$_REQUEST['parent_type_search'];
	}	
	
	$searchQuery = rtrim($searchQuery, " AND ");
	
	
	if(!empty($searchQuery))
		$query = "SELECT COUNT(*) as num FROM ".$searchTable." WHERE ".$searchQuery;
	else
		$query = "SELECT COUNT(*) as num FROM  ".$searchTable;
	
	$total_pages = mysql_fetch_array(mysql_query($query));
	$total_pages = $total_pages[num];
	?>   
    <main class="main-content-wrap">
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
    <?php unset($_SESSION['SUCCESS']);} ?>
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
                <?php if($_REQUEST['mode']=="create" || ($_REQUEST['mode']=="edit" && $_REQUEST['ID']>0)){ ?>
                
                <div class="page-head mb-4">
                    <h1 class="page-heading"><?php if($_REQUEST['mode']=="edit"){ ?>Edit<?php }else{ ?>Create<?php } ?> Product type</h1>
                    <a href="product-types.php">
                        <i class="fas fa-angle-left"></i>
                        Back to product type
                    </a>
                   
                </div>
               
                <form method="post">
                <input type="hidden" name="mode" value="<?php if($_REQUEST['mode']=="edit"){ ?>edit<?php }else{ ?>create<?php } ?>">
                
                
                <div class="row create-product">
                	
                    <div class="col-md-12" style="margin-bottom:10px;">
                    	<label class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input product_type_option" <?php if($ParentIDType==0 || $_REQUEST['product_type_option']=="parent"){ ?> checked <?php } ?> value="parent" name="product_type_option">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Parent Product type</span>
                        </label>
                        
                        <label class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input product_type_option" <?php if($ParentIDType>0 || $_REQUEST['product_type_option']=="child"){ ?> checked <?php } ?>  value="child" name="product_type_option">
                            <span class="custom-control-indicator"></span>
                            <span class="custom-control-description">Child Product type</span>
                        </label>
                    </div>
                    
                        <div class="col-md-6">
                        
                           <label class="ParentTypeData">Parent type name</label>
                        <input type="text" class="form-control ParentTypeData"  name="Parentname" value="<?php echo $name?>" />
                  
                  
                            <label class="childTypeData">Parent type</label>
                          <select name="parent_type"  class="form-control childTypeData">
                
						<?php
                        for($i=0 , $n=count($par_cat_array) ; $i<$n ; $i++)
                        {									
                            if($par_cat_array[$i]['ID'] == $parent_id)
                                $selected = "selected";
                            else
                                $selected = "";
                            echo "<option value='". $par_cat_array[$i]['ID'] ."' $selected>". $par_cat_array[$i]['path'] ."</option>";
                        }
                        ?>
                </select>
                
                 <label class="childTypeData">Child type name</label>
                        <input type="text" class="form-control childTypeData"  name="name" value="<?php echo $name?>" />
                        
                        
               
                        
                         <button type="submit" name="<?php if($_REQUEST['mode']=="edit"){ ?>update_category<?php }else{ ?>create_category<?php } ?>" value="<?php if($_REQUEST['mode']=="edit"){ ?>edit<?php }else{ ?>create<?php } ?>" class="form-btn-grad btn-block"><?php if($_REQUEST['mode']=="edit"){ ?>Update<?php }else{ ?>Create<?php } ?></button>
                        </div>
                        
                        <div class="col-md-6">
                        	<h3>Form Settings</h3>
                            <ul class="product_cat_settings"><li>
                           <label class="custom-control custom-checkbox">
                                <input type="checkbox" name="fieldsSettings[main_title]" <?php if (!empty($fieldsSettings) && array_key_exists("main_title",$fieldsSettings)){ echo "checked";} ?> class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Main Title</span>
                            </label>
                            </li>
                            <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("sub_title",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[sub_title]" value="1" class="custom-control-input">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Sub title</span>
                            </label>
                            </li>
                            
                            <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("single_title",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[single_title]" value="1" class="custom-control-input">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Single title</span>
                            </label>
                            </li>
                            
                             <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("deejay_name",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[deejay_name]" value="1" class="custom-control-input">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description"> Deejay Name </span>
                            </label>
                            </li>
                            
                            
                             <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("ename",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[ename]" value="1" class="custom-control-input">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Name </span>
                            </label>
                            </li>
                            
                            
                             <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("presenting",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[presenting]" value="1" class="custom-control-input">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Presenting </span>
                            </label>
                            </li>
                            
                            
                              
                             <li>
                           
                            
                            
                            
                            <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("date",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[date]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Date</span>
                            </label>
                            </li>
                            
                            
                              <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("music",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[music]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Music</span>
                            </label>
                            </li>
                            
                             
                              <li>
                           <label class="custom-control custom-checkbox">
                                <input <?php if (!empty($fieldsSettings) && array_key_exists("music_by",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[music_by]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Music by</span>
                            </label>
                            </li>
                            
                            
                              <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("own_song",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[own_song]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Own song</span>
                            </label>
                            </li>
                              <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("additional_info",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[additional_info]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Additional info</span>
                            </label>
                            </li>
                            
                              <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("requirements_note",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[requirements_note]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description"> Requirements note</span>
                            </label>
                            </li>
                           
                            
                                 <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("venue",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[venue]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Venue</span>
                            </label>
                            </li>
                            
                            
                             
                            
                                 <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("address",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[address]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Address</span>
                            </label>
                            </li>
                                 <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("mixtape_name",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[mixtape_name]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Mixtape name</span>
                            </label>
                            </li>
                                 <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("logo",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[logo]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Logo</span>
                            </label>
                            </li>
                            
                             <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("phonenumber",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[phonenumber]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Phone number</span>
                            </label>
                            </li>
                            
                             <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("email",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[email]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Email</span>
                            </label>
                            </li>
                            
                            
                             <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("produced_by",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[produced_by]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Produced by</span>
                            </label>
                            </li>
                            
                              <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("artist_name",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[artist_name]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Artist name</span>
                            </label>
                            </li>
                            
                            
                           
                             <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("facebook",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[facebook]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Facebook</span>
                            </label>
                            </li>
                             <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("instagram",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[instagram]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Instagram</span>
                            </label>
                            </li>
                            
                              <li>
                           <label class="custom-control custom-checkbox">
                                <input  <?php if (!empty($fieldsSettings) && array_key_exists("twitter",$fieldsSettings)){ echo "checked";} ?>  type="checkbox" name="fieldsSettings[twitter]" class="custom-control-input" value="1">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">Twitter</span>
                            </label>
                            </li>
                            </ul>
                            
                        </div>
                        
                    </div>
                    </form>
                <?php }else{ ?>
               
                <div class="page-head mb-4">
                    <h1 class="page-heading">Product types (<?=$total_pages;?>)</h1>
                    <a href="<?=ADMINURL;?>product-types.php?mode=create">
                       
                        Create new product type
                         <i class="fas fa-angle-right"></i>
                    </a>
                   
                   
                </div>
               	
                   
                <form method="get" class="top-search-options mb-5">
                	<div class="d-flex justify-content-stretch flex-wrap">
                        <div class="w-lg-20 w-100">
                            <a href="product-types.php" class="btn-lg btn-block form-btn-grad">View all</a>
                        </div>
                        <div class="w-lg-20 w-100">
                        	<input type="text" name="name" class="form-control" placeholder="product type" value="<?php if(isset($_REQUEST['name'])){ echo $_REQUEST['name']; } ?>">
                        </div>
                        
                       
                           <div class="col-lg-4 ml-auto">
                        
                        <select name="parent_type_search" class="form-control">
                	<option value="0">--------- Parent type ---------</option>	
						<?php
                        for($i=0 , $n=count($par_cat_array) ; $i<$n ; $i++)
                        {									
                            if($par_cat_array[$i]['ID'] == $_REQUEST['parent_type_search'])
                                $selected = "selected";
                            else
                                $selected = "";
                            echo "<option value='". $par_cat_array[$i]['ID'] ."' $selected>". $par_cat_array[$i]['path'] ."</option>";
                        }
                        ?>
                </select>
                        
                        </div>
                        
                        <div class="col-lg-3 ml-auto">
                            <button type="submit" class="form-btn-grad btn-lg btn-block">Search</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table sorting users-table table-1">
                        <thead>
                            <tr>
                                <th scope="col">#ID</th>
                                <th scope="col">Parent type</th>
                                <th scope="col">Child type</th>
                                <th scope="col">Action</th>
                              
                            </tr>
                            <tbody>
<?php


$adjacents = 2;
if($total_pages>0) {
	$targetpage = $searchURL;
	$limit = $searchResultsPerPage; 
	
	$page = !empty($_GET['page']) ? $_GET['page'] : 0;
	
	if($page) {
       $start = ($page - 1) * $limit;
	} else {
		$start = 0;							
	}
	if($page == -1){
        if(!empty($searchQuery)) {
            $sql = "SELECT * FROM " . $searchTable . " WHERE " . $searchQuery;
        }else {
            $sql = "SELECT * FROM " . $searchTable;
        }
    }else{
        if(!empty($searchQuery)) {
            $sql = "SELECT * FROM " . $searchTable . " WHERE " . $searchQuery . " order by ID desc LIMIT $start, $limit";
        }else {
            $sql = "SELECT * FROM " . $searchTable . " order by ID desc LIMIT $start, $limit";
        }
    }
	
	$result = mysql_query($sql);
                    
	if ($page == 0) $page = 1;
	
	$prev = $page - 1;
	
	$next = $page + 1;
	
	$lastpage = ceil($total_pages/$limit);

	$lpm1 = $lastpage - 1;
	
	$pagination = "";
                                    
    if($lastpage > 1)
	{	
		$pagination .= "<li>
                            <a href=\"$targetpage&page=1\" aria-label=\"First\">First</a>
                        </li>";
		//previous button
		if ($page > 1) 
			$pagination.= "<li>
                            <a href=\"$targetpage&page=$prev\" aria-label=\"Previous\">
                                <span aria-hidden=\"true\">&laquo;</span>
                                <span class=\"sr-only\">Previous</span>
                            </a>
                        </li>";
		else
			$pagination.= "<li>
                            <a aria-label=\"Previous\" disabled>
                                <span aria-hidden=\"true\">&laquo;</span>
                                <span class=\"sr-only\">Previous</span>
                            </a>
                        </li>";	
		
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))
		{	
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<li class=\"active\"><a>$counter</a></li>";
				else
					$pagination.= "<li><a href=\"$targetpage&page=$counter\">$counter</a></li>";					
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))
		{
			if($page < 1 + ($adjacents * 2))		
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class=\"active\"><a>$counter</a></li>";
					else
						$pagination.= "<li><a href=\"$targetpage&page=$counter\">$counter</a></li>";					
				}
				$pagination.= "<li><a>...</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=$lpm1\">$lpm1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=$lastpage\">$lastpage</a></li>";		
			}
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<li><a href=\"$targetpage&page=1\">1</a>";
				$pagination.= "<li><a href=\"$targetpage&page=2\">2</a>";
				$pagination.= "<li><a>...</a></li>";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class=\"active\"><a>$counter</a></li>";
					else
						$pagination.= "<li><a href=\"$targetpage&page=$counter\">$counter</a></li>";					
				}
				$pagination.= "<li><a>...</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=$lpm1\">$lpm1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=$lastpage\">$lastpage</a></li>";		
			}
			else
			{
				$pagination.= "<li><a href=\"$targetpage&page=1\">1</a></li>";
				$pagination.= "<li><a href=\"$targetpage&page=2\">2</a></li>";
				$pagination.= "<li><a>...</a></li>";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<li class=\"active\"><a>$counter</a></li>";
					else
						$pagination.= "<li><a href=\"$targetpage&page=$counter\">$counter</a></li>";					
				}
			}
		}
		
		//next button
		if ($page < $counter - 1) 
			$pagination.= "<li>
                            <a href=\"$targetpage&page=$next\" aria-label=\"Next\" disabled>
                                <span aria-hidden=\"true\">&raquo;</span>
                                <span class=\"sr-only\">Next</span>
                            </a>
                        </li>";
		else
			$pagination.= "<li>
                            <a aria-label=\"Next\" disabled>
                                <span aria-hidden=\"true\">&raquo;</span>
                                <span class=\"sr-only\">Next</span>
                            </a>
                        </li>";
		$pagination.= "<li>
                            <a href=\"$targetpage&page=$lastpage\" aria-label=\"First\">Last</a>
                        </li>";		
	}

	$sr = 1;
	while($showData = mysql_fetch_array($result))
	{
	
	?>                   
        <tr>
            <td class="data-id"><span><?=$sr;?></span></td>
          <td><?php echo ($showData['parent_id']>0)?categoryNameFromId($showData['parent_id']):$showData['Name']; ?></td>
         
            <td><?php echo ($showData['parent_id']>0) ? $showData['Name']:"---";?></td>
            <td class="data-view"><a href="?ID=<?=$showData['ID'];?>&mode=edit" class="view">Edit</a></td>
            <?php if( $_SESSION['userType'] == "admin"){ ?>
            <td class="data-delete"> <a href="?deleteID=<?=$showData['ID'];?>" onClick="return confirm('Are you sure you want to perform this action?')"><i class="fas fa-trash-alt"></i></a></td>
            <?php } ?>
        </tr>
  	<?php
	$sr++;
	}
} else {
	echo "<tr>
			<td colspan=\"4\">No Search Result Found.</td>
		</tr>";
}
?>                              
                            </tbody>
                        </thead>
                    </table>
                </div>

                <nav class="mt-5">
                    <ul class="data-pagination justify-content-center flex-wrap">
                        <?php
                        
                        if( !empty($_REQUEST['page']) && $_REQUEST['page'] == -1) {
                            $viewPaginationUrl = ADMINURL."product-types.php";
                            $pagination = "<li><a href='$viewPaginationUrl' aria-label=\"View All\">View Pagination</a></li>";
                        }
                        echo $pagination;

                        if( $_REQUEST['page'] != -1) {
                        ?>
                        <li>
                            <a href="<?=ADMINURL;?>product-types.php?page=-1" aria-label="View All">View All</a>
                        </li>
                        <?php } ?>
                    </ul>
                </nav>
                <?php } ?>

            </div>


        </div>
    </main>

    <?php include "includes/footer.php"; ?>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        function update_sort_order(e){
            var sort_order = $(e).val();

                if (/^\d+$/.test(sort_order) || sort_order == '') {

                    var product_id = $(e).parent().attr('data-id');

                    jQuery(".mainLoader").show();

                    $.ajax({
                        type: "POST",
                        url: "<?=SITEURL;?>ajax/update_product_sort_order.php",
                        data: "product_id="+product_id+"&sort_order="+sort_order,
                        success: function(regResponse) {
                            jQuery(".mainLoader").hide();
                        }
                    });

                }else{
                    alert("please enter a valid sort order number");
                    $(e).val("");
                }

        }
		
	jQuery(document).on("change",".product_type_option",function(){
			HideShowParentType();
	});	
	
	jQuery(document).ready(function(e) {
       HideShowParentType();
    });
	function HideShowParentType(){
		var type= jQuery(".product_type_option:checked").val();	
		if(type=="parent"){
				jQuery(".childTypeData").hide();
				jQuery(".ParentTypeData").show();	
		}else{
			jQuery(".childTypeData").show();
			jQuery(".ParentTypeData").hide();
		}	
		
	}
    </script>
<style>
.product_cat_settings{ margin:0px; padding:0px; margin-top:10px;}
.product_cat_settings li{ list-style:none; float:left; width:50%;}
</style>
</body>

</html>