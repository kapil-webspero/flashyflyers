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
	
	$totalLimit = $limit = 16;
	$page = $page_id;
	$query = "`is_regular` = '1' AND `Status` = '1' AND `Addon` = '0' ";
	$adjacents = 2;
	$pagination = "";
	
	if(isset($category) && !empty($category)) {
		$query .= " AND FIND_IN_SET('$category', Category) ";
	}
	
	if(isset($hint_text) && !empty($hint_text)) {
		$query .= " AND (`Title` like '%$hint_text%' OR `Tags` like '%$hint_text%' OR `Subtitle` like '%$hint_text%' OR `Description` like '%$hint_text%')";
	}
	
	if(isset($dimensional) && !empty($dimensional)) {
		if($dimensional == "2D") {
			$query .= " AND `2D` = '1'";
		}elseif($dimensional == "3D") {
			$query .= " AND `3D` = '1'";
		}
	}
	$query1 = "";
	if(isset($static) && !empty($static)) {
		$query1 .= " OR `Static` = '1'";
	}
	
	if(isset($motion) && !empty($motion)) {
		$query1 .= " OR `Motion` = '1'";
	}
	
	if(isset($animated) && !empty($animated)) {
		$query1 .= " OR `Animated` = '1'";
	}
	if(!empty($query1)) {
		$query1 = ltrim($query1," OR ");
		if((strpos($query1, "OR") !== false) ) {
			$query .= " AND ( ".$query1." ) ";
		} else {
			$query .= " AND ".$query1." ";
		}
	}
	$countRcd = $total_pages = GetNumOfRcrdsOnCndi(PRODUCT, $query);
	//Pagination Code
	
	if(!empty($page)) {
		$start = ($page - 1) * $totalLimit;
	} else {
		$start = 0;							
	}

	$query .= "order by -sort_order DESC LIMIT $start, $limit";
	
	$getProducts = GetMltRcrdsOnCndi(PRODUCT, $query);
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
			
			if(count($getBanners)>0) {

				/* Array Sorting */ 
				foreach($getBanners as $photoKey => $photo){
                    
					//list($width, $height) = getimagesize($photo['filename']);
					
					$raw = rangerServerUrlGetImage($photo['filename']);
					$im = imagecreatefromstring($raw);
					$width = imagesx($im);
					$height = imagesy($im);
					
					if(!empty($width) && !empty($height)){
						if($width < $height){
							$getBanners[$photoKey]['sort_order'] = 1;
						}
						if($width == $height){
							$getBanners[$photoKey]['sort_order'] = 2;
						}

						if($width > $height){
							$getBanners[$photoKey]['sort_order'] = 3;
						}
					}else{
						$getBanners[$photoKey]['sort_order'] = 4;
					}
				}
				
				usort($getBanners,"sortArrayByColumnValue");		
				/* Array Sorting */ 

				$galleryImages = "";
				$j=0;
				foreach($getBanners as $banners) {
					if($j==2){continue;}
					$j++;
					if (strpos($banners['filename'],'res.cloudinary.com') !== false)
					{
						$galleryImages .= '<li><a   href="'.SITEURL.'p/'.$products['slug'].'"><img class="hompageimage1" src="'.httpToSecure($banners['filename']).'" alt="" style="width:100%"></a></li>'; 
					}
					else
					{
					$galleryImages .= '<li><a   href="'.SITEURL.'p/'.$products['slug'].'"><img class="hompageimage1" src="../uploads/products/'.$products['id'].'/'.$banners['filename'].'" alt="" style="width:100%"></a></li>'; 
					}
				}
			} else {
				$galleryImages = '<li><a   href="'.SITEURL.'p/'.$products['slug'].'"><img src="images/flyer-6.jpg" alt="" style="width:100%"></a></div><div class="item"><a href="'.SITEURL.'p/'.$products['slug'].'"><img src="images/flyer-5.jpg" alt="" style="width:100%"></a></div><div class="item"><a href="'.SITEURL.'p/'.$products['slug'].'"><img src="images/flyer-4.jpg" alt="" style="width:100%"></a></li>';
			}
			 $mygallery="";
                    if(count($getBanners)>0) {
                        $mygallery=$galleryImages;
                    } 
                    
					
			$bookmarkCls = "bookmark-btn";
			if(in_array($products['id'],$bookmarks)) {
				$bookmarkCls = "my-bookmark-btn";
			}
			
			$html .= '<div class="col-lg-3 col-sm-6">

                            <div class="flyer-product mb-3">

                                <div class="flyer-img-wrap">
                                    <div class="flyer-img">';
									  $html .= ' <div class="flexslider"><ul class="slides">'.$mygallery.'</ul></div><div class="ProductDetailsInfo">  <h2>'.$products['Title'].'</h2>	
                                    </div>
                                    <div class="flyer-content">

                                        <div class="flyer-product-info">
                                            <h2>'.$products['Title'].'</h2>';
											if(!empty($products['Description']))
											{
											
											//	$html .= '<p>'.substr(strip_tags($products['Description']),0,100).'</p>';
											}

                                            $html .= '<div class="buy-btns text-center">
                                                <a   href="'.SITEURL.'p/'.$products['slug'].'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Order</span></a>
                                            </div>
                                            <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>
                                        </div>
										<div style="display:block; width:100%; text-align:center"><div class="fb-share-button" data-href="'.SITEURL.'p/'.$products['slug'].'" data-layout="button" data-size="large" data-mobile-iframe="true"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u='.SITEURL.'p/'.$products['slug'].'&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div></div>


                                    </div>
                                </div>

                                <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>

                                <div class="buy-btns text-center">
                                    <a  href="'.SITEURL.'p/'.$products['slug'].'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Order</span></a>
                                </div>

                            </div>

                        </div>
                        </div>';	
		}
		
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
        
	echo json_encode(array("html"=>$html, "pagination" => $pagination, "query"=>$query ));
?>