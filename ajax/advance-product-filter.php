<?php
    ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';

    

	$page_id = 1;
	extract($_REQUEST);
	
	if(is_login()) {
		$myBookmarks = GetMltRcrdsOnCndiWthOdr(FAVOURITE,"`UserID` = '".$_SESSION['userId']."'", "`id`", "DESC");
		$bookmarks = array();
		foreach($myBookmarks as $myBookmarkList){
			$bookmarks[] = $myBookmarkList['ProductID'];	
		}
	} else {
		$bookmarks = array();
	}
	if(!isset($_REQUEST['limitPerPage'])){
		$_REQUEST['limitPerPage'] = 12;	
	}
	if($_REQUEST['limitPerPage']=="-1"){
		$_REQUEST['limitPerPage'] = 5000000;	
	}
	$totalLimit = $limit = $_REQUEST['limitPerPage'];
	$page = $page_id;

	$query = "p.is_regular = '1' AND p.Status = '1' AND p.Addon = '0' ";
	
	if($_REQUEST['type']=="custom"){
		$query .= " AND p.CustomProduct = 'yes'";	
	}else{
		$query .= " AND  p.CustomProduct = 'no'";
	}
	if($_REQUEST['template_type']=="psd_customize_template" || $_REQUEST['template_type']=="psd_only"){
		$query .= " AND  (p.template_type = 'psd_customize_template' OR p.template_type = 'psd_only')";
	}
	$adjacents = 2;
	$pagination =  $perPage = "";
	
	$hint_text = $search_term;
	
	$query1="";
	
	if(isset($MaincategorySearch ) && !empty($MaincategorySearch )) {
		if(count($MaincategorySearch )>1){
			$query1 .= " AND (";
			foreach($MaincategorySearch as $cat){
				$getProTypeID = GetSglRcrdOnCndi(PRODUCT_TYPE,"Slug='".$cat."'");
				
				$query1 .= " p.parent_product_cat_id='".$getProTypeID['ID']."'  OR ";	
			}
			$query .= substr($query1, 0, -3).")";
			
		}else{
			$getProTypeID = GetSglRcrdOnCndi(PRODUCT_TYPE,"Slug='".$MaincategorySearch[0]."'");
			$query .= " AND  p.parent_product_cat_id='".$getProTypeID['ID']."' ";
		}
	}
	
	
		if(isset($SubcategorySearch ) && !empty($SubcategorySearch )) {
		if(count($SubcategorySearch )>1){
			$query2 = " AND (";
			foreach($SubcategorySearch as $cat){
				$getProTypeID = GetSglRcrdOnCndi(PRODUCT_TYPE,"Slug='".$cat."'");
			
				$query2 .= " p.child_product_cat_id='".$getProTypeID['ID']."'  OR ";	
			}
			$query .= substr($query2, 0, -3).")";
			
		}else{
			$getProTypeID = GetSglRcrdOnCndi(PRODUCT_TYPE,"Slug='".$SubcategorySearch[0]."'");
			$query .= " AND  p.child_product_cat_id='".$getProTypeID['ID']."' ";
		}
	}
	
	if($tags!=""){
		$searchTag = GetMltRcrdsOnCndiWthOdr(PRODUCT_TAGS," TagSlug = '".$tags."'", "`Id`", "DESC");
		$query .= " AND FIND_IN_SET('".$searchTag[0]['Id']."', p.Tags) ";
				
	}
	
	if(isset($hint_text) && !empty($hint_text)) {
		$query .= " AND (p.Title like '%$hint_text%' OR p.Subtitle like '%$hint_text%' OR p.Description like '%$hint_text%' ";
		
		$searchTag = GetMltRcrdsOnCndiWthOdr(PRODUCT_TAGS," TagName LIKE'%".$hint_text."%'", "`Id`", "DESC");
		if(!empty($searchTag)){
			$query .=" OR ";
			foreach($searchTag as $tagsearch){
				$searchTagsData .= "  FIND_IN_SET('".$tagsearch['Id']."', p.Tags) OR";
			}
			$query .= substr($searchTagsData, 0, -2);
				
		}
		$query .=")";
		
	}
	

	$countRcd = $total_pages = getAdvanceProductSearchCount($query." group by p.ID");
	//Pagination Code
	
	if(!empty($page)) {
		$start = ($page - 1) * $totalLimit;
	} else {
		$start = 0;							
	}
	$orderBy = "p.sort_order DESC";
	if($search_sort_by!=""){
		
		$orderByPrice = "Baseprice";
		if($_REQUEST['template_type']=="psd_customize_template" || $_REQUEST['template_type']=="psd_only"){
			$orderByPrice = "psd_price";
		}
		
		if($search_sort_by=="price_low_to_high"){$orderBy = " p.".$orderByPrice." ASC";}
		else if($search_sort_by=="price_high_to_low"){$orderBy = " p.".$orderByPrice." DESC";}
		else if($search_sort_by=="Latest"){$orderBy = " p.id DESC";}
		else if($search_sort_by=="bestseller"){$orderBy = " totalProductSale DESC";}
	}
	$query .= " group by p.ID order by ".$orderBy." LIMIT $start, $limit";
	$getProducts = getAdvanceProductSearch($query);
	$html = "Sorry we could not find any products.";
        
        $html.= "<p style='width:100%;'><a onclick='resetForm()' class='resetLINK'>Reset the search</a></p>"; 
	if(count($getProducts)>0) {		
		
		$html = "<div id=\"fb-root\"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>";
		
		foreach($getProducts as $products) {
			
			
				$getBanners = GetMltRcrdsOnCndi(PRODUCT_BANNER, "`prod_id` = '".$products['id']."' AND `filetype`= 'image' and set_default_facebookimage = 'no'");
			
			if($products['template_type']=="psd_customize_template" || $products['template_type']=="psd_only"){
				$products['Baseprice'] = $products['psd_price'];
			}
			
			if(count($getBanners)>0) {

				/* Array Sorting */ 
			/*	foreach($getBanners as $photoKey => $photo){
                    
					//list($width, $height) = getimagesize($photo['filename']);
					
					$raw = rangerServerUrlGetImage($photo['filename']);
					$im = imagecreatefromstring($raw);
					$width = imagesx($im);
					$height = imagesy($im);
					
					if(!empty($width) && !empty($height)){
						if($width < $height &&  $photo['set_default_image']=="yes"){
							$getBanners[$photoKey]['sort_order'] = 1;
						}else{
							$getBanners[$photoKey]['sort_order'] = 2;
						}
						if($width == $height){
							$getBanners[$photoKey]['sort_order'] = 2;
						}

						if($width > $height){
							$getBanners[$photoKey]['sort_order'] = 3;
						}
					}else{
						if( $photo['set_default_image']=="yes"){
							$getBanners[$photoKey]['sort_order'] = 1;
						}else{
							$getBanners[$photoKey]['sort_order'] = 4;
						}
					}
				}
				usort($getBanners,"sortArrayByColumnValue");		
			*/	
				/* Array Sorting */ 
				
				$galleryImages = "";
				$j=0;
				foreach($getBanners as $banners) {
				//	if($j==2){continue;}
					$j++;
					$galleryImages .= '<li><a  onclick="return gtag_report_conversion(\''.SITEURL.'p/'.$products['slug'].'\')" href="'.SITEURL.'p/'.$products['slug'].'">
					'.productImageSrc($banners['filename'],$products['id'],'354').'</a></li>'; 
					
				}
			} else {
				$galleryImages = '<li><a  onclick="return gtag_report_conversion(\''.SITEURL.'p/'.$products['slug'].'\')" href="'.SITEURL.'p/'.$products['slug'].'"><img src="images/flyer-6.jpg" alt="" style="width:100%"></a></div><div class="item"><a href="'.SITEURL.'p/'.$products['slug'].'"><img src="images/flyer-5.jpg" alt="" style="width:100%"></a></div><div class="item"><a href="'.SITEURL.'p/'.$products['slug'].'"><img src="images/flyer-4.jpg" alt="" style="width:100%"></a></li>';
			}
			 $mygallery="";
                    if(count($getBanners)>0) {
                        $mygallery=$galleryImages;
                    } 
                    
					
			$bookmarkCls = "bookmark-btn";
			if(in_array($products['id'],$bookmarks)) {
				$bookmarkCls = "my-bookmark-btn";
			}
			
			$html .= '<div class="col-lg-4 col-sm-6">

                            <div class="flyer-product mb-3">

                                <div class="flyer-img-wrap">
                                    <div class="flyer-img">';
									  $html .= ' <div class="flexslider"><ul class="slides">'.$mygallery.'</ul></div><div class="ProductDetailsInfo">  <h2> <a   href="'.SITEURL.'p/'.$products['slug'].'">'.$products['Title'].'</a></h2>	
                                    </div>
                                    <div class="flyer-content">

                                        <div class="flyer-product-info">
                                            <h2>'.$products['Title'].'</h2>';
											$html .= '<div class="buy-btns text-center">
                                                <a  onclick="return gtag_report_conversion(\''.SITEURL.'p/'.$products['slug'].'\')" href="'.SITEURL.'p/'.$products['slug'].'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Order</span></a>
                                            </div>
                                            <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>
                                        </div>
										<div style="display:block; width:100%; text-align:center"><div class="fb-share-button" data-href="'.SITEURL.'p/'.$products['slug'].'" data-layout="button" data-size="large" data-mobile-iframe="true"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u='.SITEURL.'p/'.$products['slug'].'&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div></div>


                                    </div>
                                </div>

                                <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>

                                <div class="buy-btns text-center">
                                    <a  onclick="return gtag_report_conversion(\''.SITEURL.'p/'.$products['slug'].'\')" href="'.SITEURL.'p/'.$products['slug'].'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Order</span></a>
                                </div>

                            </div>

                        </div>
                        </div>';	
		}
	
		$selectTen = $selectTwentyFive =$selectFifty =$selectHunderd =$selectAll = "";
			if($limit==12){$selectTen = "selected";}
			if($limit==24){$selectTwentyFive = "selected";}
			if($limit==48){$selectFifty = "selected";}
			if($limit==96){$selectHunderd = "selected";}
			if($limit==5000000){$selectAll = "selected";}
			$perPage = "Items Per page <select name='per_page' id='per_page' class='form-control'>
			<option value='12' ".$selectTen.">12</option>
			<option value='24' ".$selectTwentyFive.">24</option>
			<option value='48' ".$selectFifty.">48</option>
			<option value='96' ".$selectHunderd.">96</option>
			<option value='-1' ".$selectAll.">All</option></select>";;	
		
	if ($page == 0) $page = 1;				
		$prev = $page - 1;
		$next = $page + 1;
		
		$lastpage = ceil($total_pages/$limit);

		$lpm1 = $lastpage - 1;
		
		
						
		if($lastpage > 1)
		{	
	
			$pagination .= '<div class="pagination">';
			//previous button
			if ($page > 1) 
				$pagination.= '<a class="prev-page bx-shadow" onclick="return pagged('.$prev.');"><img src="images/arrow-left.png" alt=""></a><ul>';
			else
				$pagination.= '<a class="prev-page bx-shadow"><img src="images/arrow-left.png" alt=""></a><ul>';	
			
			//pages	
			if ($lastpage < 7 + ($adjacents * 2))
			{	
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= '<li class="active"><a>'.$counter.'</a></li>';
					else
						$pagination.= '<li><a onclick="return pagged('.$counter.');">'.$counter.'</a></li>';					
				}
			}
			elseif($lastpage > 5 + ($adjacents * 2))
			{
				if($page < 1 + ($adjacents * 2))		
				{
					for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
					{
						if ($counter == $page)
							$pagination.= '<li class="active"><a>'.$counter.'</a><li>';
						else
							$pagination.= '<li><a onclick="return pagged('.$counter.');">'.$counter.'</a></li>';					
					}
					$pagination.= '...';
					$pagination.= '<li><a onclick="return pagged('.$lpm1.');">'.$lpm1.'</a></li>';
					$pagination.= '<li><a onclick="return pagged('.$lastpage.');">'.$lastpage.'</a></li>';		
				}
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination.= '<li><a onclick="return pagged(1);">1</a></li>';
					$pagination.= '<li><a onclick="return pagged(2);">2</a><li>';
					$pagination.= '<li>...</li>';
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$pagination.= '<li class="active"><a>'.$counter.'</a></li>';
						else
							$pagination.= '<li><a onclick="return pagged('.$counter.');">'.$counter.'</a></li>';					
					}
					$pagination.= "<li>...</li>";
					$pagination.= '<li><a onclick="return pagged('.$lpm1.');">'.$lpm1.'</a></li>';
					$pagination.= '<li><a onclick="return pagged('.$lastpage.');">'.$lastpage.'</a></li>';		
				}
				else
				{
					$pagination.= '<li><a onclick="return pagged(1);">1</a></li>';
					$pagination.= '<li><a onclick="return pagged(2);">2</a></li>';
					$pagination.= '<li>...</li>';
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination.= '<li class="active"><a>'.$counter.'</a></li>';
						else
							$pagination.= '<li><a onclick="return pagged('.$counter.');">'.$counter.'</a></li>';				
					}
				}
			}
			
			//next button
			if ($page < $counter - 1) 
				$pagination.= '</ul><a onclick="return pagged('.$next.');" class="next-page bx-shadow float-right"><img src="images/arrow-right.png" alt=""></a>';
			else
				$pagination.= '</ul><a class="next-page bx-shadow float-right"><img src="images/arrow-right.png" alt=""></a>';
			$pagination.= '</div>';		
		}
		//$pagination = $html = "";
		
	}
        
	echo json_encode(array( "query"=>$query,"html"=>$html, "pagination" => $pagination,"perPage"=>$perPage ));
?>