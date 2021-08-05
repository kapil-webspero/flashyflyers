<?php
    ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';

    

	$page_id_review = 1;
	extract($_REQUEST);
	

	if(!isset($_REQUEST['LimitProductReviewPerPage'])){
		$_REQUEST['LimitProductReviewPerPage'] = 10;	
	}
	if($_REQUEST['LimitProductReviewPerPage']=="-1"){
		$_REQUEST['LimitProductReviewPerPage'] = 50000000;	
	}
	$totalLimit = $limit = $_REQUEST['LimitProductReviewPerPage'];
	$page = $page_id_review;
	$query = "p.is_regular = '1' AND p.Status = '1' AND p.Addon = '0' ";
	$adjacents = 2;
	$pagination =  $perPage = "";
	
	$hint_text = $search_term;
	

	
	$countReview = getProductReviewCountByProductID($_REQUEST['product_id']);
	
	$countRcd = $total_pages = $countReview['num'];
	//Pagination Code
	
	if(!empty($page)) {
		$start = ($page - 1) * $totalLimit;
	} else {
		$start = 0;							
	}

	$getProductsReview = getProductReviewByProductID($_REQUEST['product_id'],"LIMIT $start, $limit");
	
        
        $html= ""; 
	if(count($getProductsReview)>0) {
		
			$averageSum = floor($countReview['total']/$countReview['num']);
	
				$selected1=$selected2=$selected3=$selected4=$selected5="";
				if($averageSum>=1){$selected1="selected";}
				if($averageSum>=2){$selected2="selected";}
				if($averageSum>=3){$selected3="selected";}
				if($averageSum>=4){$selected4="selected";}
				if($averageSum>=5){$selected5="selected";}
				
		
				
			$html .="  <h3>  <label>Reviews</label></h3>";
			
                
                $html .='<div class="ReviewListing ">';
				
                	/*$html .='<div class="ReviewListingBlockOverall">
                    		<div class="rating_star_review_overall">
                                            <span data-id="100" data-val="1" class="'.$selected1.'"></span>
                                            <span data-id="100" data-val="2" class="'.$selected2.'"></span>
                                            <span data-id="100" data-val="3" class="'.$selected3.'"></span>
                                            <span data-id="100" data-val="4" class="'.$selected4.'"></span>
                                            <span data-id="100" data-val="5" class="'.$selected5.'"></span>
                                        </div>
                        	
                        	<div class="ReviewListingOverAllContent"> Based on '.$countReview['num'].' reviews';
							if($_SESSION['userId']>0 && $_SESSION['userType']=="user"){
							
							//$html .='<div class="ReviewListingOverAllWriteReview"> Write Review</div>';
							}
							$html .='</div>   
   
                                         
                    	
                    </div>';*/
                   $html .='<script>
				     jQuery(".ReviewSlider").slick({autoplay:true,dots:true,slidesToScroll:1});
				   </script><div class="ReviewAllListing ReviewSlider">';
		
		foreach($getProductsReview as $reviews) {
				
				$selected1=$selected2=$selected3=$selected4=$selected5="";
				if($reviews['Rating']>=1){$selected1="selected";}
				if($reviews['Rating']>=2){$selected2="selected";}
				if($reviews['Rating']>=3){$selected3="selected";}
				if($reviews['Rating']>=4){$selected4="selected";}
				if($reviews['Rating']>=5 ){$selected5="selected";}
				
			
			$html .= '<div class="ReviewListingBlock item">
                        	<div class="rating_star_review">
                                            <span data-id="100" data-val="1" class="'.$selected1.'" ></span>
                                            <span data-id="100" data-val="2" class="'.$selected2.'"></span>
                                            <span data-id="100" data-val="3" class="'.$selected3.'"></span>
                                            <span data-id="100" data-val="4" class="'.$selected4.'"></span>
                                            <span data-id="100" data-val="5" class="'.$selected5.'"></span>
                                        </div>
                            <div class="col-md-12">
                            	<div class="ReviewAuthor">'.ucfirst(substr($reviews['FName'],0,1)).". ".$reviews['LName'].' <span>on </span><span class="ReviewDate">'.date("F j, Y",strtotime($reviews['ReviewDate'])).'</span></div>
                            	<div class="ReviewContent">'.stripcslashes($reviews['ReviewDescription']).'</div>
                            </div>            
                        </div>';	
		}
		$html .='</div></div>';
		
		
		
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
				$pagination.= '<a class="prev-page bx-shadow" onclick="return paggedReview('.$prev.');"><img src="'.SITEURL.'images/arrow-left.png" alt=""></a><ul>';
			else
				$pagination.= '<a class="prev-page bx-shadow"><img src="'.SITEURL.'images/arrow-left.png" alt=""></a><ul>';	
			
			//pages	
			if ($lastpage < 7 + ($adjacents * 2))
			{	
				for ($counter = 1; $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= '<li class="active"><a>'.$counter.'</a></li>';
					else
						$pagination.= '<li><a onclick="return paggedReview('.$counter.');">'.$counter.'</a></li>';					
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
							$pagination.= '<li><a onclick="return paggedReview('.$counter.');">'.$counter.'</a></li>';					
					}
					$pagination.= '...';
					$pagination.= '<li><a onclick="return paggedReview('.$lpm1.');">'.$lpm1.'</a></li>';
					$pagination.= '<li><a onclick="return paggedReview('.$lastpage.');">'.$lastpage.'</a></li>';		
				}
				elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
				{
					$pagination.= '<li><a onclick="return paggedReview(1);">1</a></li>';
					$pagination.= '<li><a onclick="return paggedReview(2);">2</a><li>';
					$pagination.= '<li>...</li>';
					for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
					{
						if ($counter == $page)
							$pagination.= '<li class="active"><a>'.$counter.'</a></li>';
						else
							$pagination.= '<li><a onclick="return paggedReview('.$counter.');">'.$counter.'</a></li>';					
					}
					$pagination.= "<li>...</li>";
					$pagination.= '<li><a onclick="return paggedReview('.$lpm1.');">'.$lpm1.'</a></li>';
					$pagination.= '<li><a onclick="return paggedReview('.$lastpage.');">'.$lastpage.'</a></li>';		
				}
				else
				{
					$pagination.= '<li><a onclick="return paggedReview(1);">1</a></li>';
					$pagination.= '<li><a onclick="return paggedReview(2);">2</a></li>';
					$pagination.= '<li>...</li>';
					for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
					{
						if ($counter == $page)
							$pagination.= '<li class="active"><a>'.$counter.'</a></li>';
						else
							$pagination.= '<li><a onclick="return paggedReview('.$counter.');">'.$counter.'</a></li>';				
					}
				}
			}
			
			//next button
			if ($page < $counter - 1) 
				$pagination.= '</ul><a onclick="return paggedReview('.$next.');" class="next-page bx-shadow float-right"><img src="'.SITEURL.'images/arrow-right.png" alt=""></a>';
			else
				$pagination.= '</ul><a class="next-page bx-shadow float-right"><img src="'.SITEURL.'images/arrow-right.png" alt=""></a>';
			$pagination.= '</div>';		
		}
		//$pagination = $html = "";
		
	}
        
	echo json_encode(array( "html"=>$html, "pagination" => $pagination,"perPage"=>$perPage ));
?>