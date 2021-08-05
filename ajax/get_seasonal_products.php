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

$totalLimit = $limit = 4;
$page = $page_id;
$query = "`is_seasonal` = '1' AND `Status` = '1' ";
$adjacents = 2;
$pagination = "";

$countRcd = $total_pages = GetNumOfRcrdsOnCndi(PRODUCT, $query);
//Pagination Code

if(!empty($page)) {
    $start = ($page - 1) * $totalLimit;
} else {
    $start = 0;
}

$query .= "order by id ASC LIMIT $start, $limit";
//$query .= "order by -sort_order DESC LIMIT $start, $limit";

$getProducts = GetMltRcrdsOnCndi(PRODUCT, $query);
$html = "Sorry we could not find any products.";

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

        $getBanners = GetMltRcrdsOnCndi(PRODUCT_BANNER, "`prod_id` = '".$products['id']."' AND `filetype`= 'image'");
        $getMainBanner = GetSglDataOnCndi(PRODUCT_BANNER, "`prod_id` = '".$products['id']."' AND `filetype`= 'image'", "filename");
        $getMainBanner2 = GetSglDataOnCndi(PRODUCT_BANNER, "`prod_id` = '".$products['id']."' AND `filetype`= 'image' and set_default_image = 'yes'", "filename");

        if(count($getBanners)>0) {
            $galleryImages = "";
            $j=0;
            foreach($getBanners as $banners) {
                if($j==2){continue;}
                $j++;
                if (strpos($banners['filename'],'res.cloudinary.com') !== false)
                {
                    $galleryImages .= '<div class="item"><a href="'.SITEURL.'p/'.$products['slug'].'"><img src="'.httpToSecure($banners['filename']).'" alt="" style="width:100%"></a></div>';
                }
                else
                {
                    $galleryImages .= '<div class="item"><a href="'.SITEURL.'p/'.$products['slug'].'"><img src="../uploads/products/'.$products['id'].'/'.$banners['filename'].'" alt="" style="width:100%"></a></div>';
                }
            }
        } else {
            $galleryImages = '<div class="item"><a href="'.SITEURL.'p/'.$products['slug'].'"><img src="images/flyer-6.jpg" alt="" style="width:100%"></a></div><div class="item"><a href="'.SITEURL.'p/'.$products['slug'].'"><img src="images/flyer-5.jpg" alt="" style="width:100%"></a></div><div class="item"><a href="'.SITEURL.'p/'.$products['slug'].'"><img src="images/flyer-4.jpg" alt="" style="width:100%"></a></div>';
        }
        $mygallery="";
        if(count($getBanners)==1) {
            $mygallery=$galleryImages;
        } elseif(count($getBanners)>1) {
            $mygallery="<div class='flyer-gallery'>".$galleryImages."</div>";
        }
        $bookmarkCls = "bookmark-btn";
        if(in_array($products['id'],$bookmarks)) {
            $bookmarkCls = "my-bookmark-btn";
        }

        $html .= '<div class="col-lg-3 col-sm-6">

                            <div class="flyer-product mb-5">

                                <div class="flyer-img-wrap">
                                    <div class="flyer-img">
                                        <a href="'.SITEURL.'p/'.$products['slug'].'">';

        if($getMainBanner2 != '')
        {
            if (strpos($getMainBanner2,'res.cloudinary.com') !== false)
            {
                $html .= '<img src="'.httpToSecure($getMainBanner2).'" alt="">';
            }
            else{
                $html .= '<img src="../uploads/products/'.$products['id'].'/'.$getMainBanner2.'" alt="">';
            }
        }
        else
        {
            if (strpos($getMainBanner,'res.cloudinary.com') !== false)
            {
                $html .= '<img src="'.httpToSecure($getMainBanner).'" alt="">';
            }
            else{
                $html .= '<img src="../uploads/products/'.$products['id'].'/'.$getMainBanner.'" alt="">';
            }
        }

        $html .= '</a>
                                    </div>
                                    <div class="flyer-content">

                                        '.$mygallery.'
                                        <div class="flyer-product-info">
                                            <h2>'.$products['Title'].'</h2>';
        if(!empty($products['Description']))
        {

            $html .= '<p>'.substr($products['Description'],0,100).'</p>';
        }

        $html .= '<div class="buy-btns text-center">
                                                <a href="'.SITEURL.'p/'.$products['slug'].'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Order</span></a>
                                            </div>
                                            <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>
                                        </div>
										<div style="display:block; width:100%; text-align:center"><div class="fb-share-button" data-href="'.SITEURL.'p/'.$products['slug'].'" data-layout="button" data-size="large" data-mobile-iframe="true"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u='.SITEURL.'p/'.$products['slug'].'&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div></div>


                                    </div>
                                </div>

                                <a onClick="action_bookmark(\''.$products['id'].'\')" class="Pbook_'.$products['id'].' '.$bookmarkCls.'"><i class="far fa-heart"></i></a>

                                <div class="buy-btns text-center">
                                    <a href="'.SITEURL.'p/'.$products['slug'].'" class="buy-btn"><span class="price">$'.formatPrice($products['Baseprice']).'</span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Order</span></a>
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
            $pagination.= '<a class="prev-page bx-shadow" onclick="return seasonal_pagged('.$prev.');"><img src="images/arrow-left.png" alt=""></a><ul>';
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
                    $pagination.= '<li><a onclick="return seasonal_pagged('.$counter.');">'.$counter.'</a></li>';
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
                        $pagination.= '<li><a onclick="return seasonal_pagged('.$counter.');">'.$counter.'</a></li>';
                }
                $pagination.= '...';
                $pagination.= '<li><a onclick="return seasonal_pagged('.$lpm1.');">'.$lpm1.'</a></li>';
                $pagination.= '<li><a onclick="return seasonal_pagged('.$lastpage.');">'.$lastpage.'</a></li>';
            }
            elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
            {
                $pagination.= '<li><a onclick="return seasonal_pagged(1);">1</a></li>';
                $pagination.= '<li><a onclick="return seasonal_pagged(2);">2</a><li>';
                $pagination.= '<li>...</li>';
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= '<li class="active"><a>'.$counter.'</a></li>';
                    else
                        $pagination.= '<li><a onclick="return seasonal_pagged('.$counter.');">'.$counter.'</a></li>';
                }
                $pagination.= "<li>...</li>";
                $pagination.= '<li><a onclick="return seasonal_pagged('.$lpm1.');">'.$lpm1.'</a></li>';
                $pagination.= '<li><a onclick="return seasonal_pagged('.$lastpage.');">'.$lastpage.'</a></li>';
            }
            else
            {
                $pagination.= '<li><a onclick="return seasonal_pagged(1);">1</a></li>';
                $pagination.= '<li><a onclick="return seasonal_pagged(2);">2</a></li>';
                $pagination.= '<li>...</li>';
                for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
                {
                    if ($counter == $page)
                        $pagination.= '<li class="active"><a>'.$counter.'</a></li>';
                    else
                        $pagination.= '<li><a onclick="return seasonal_pagged('.$counter.');">'.$counter.'</a></li>';
                }
            }
        }

        //next button
        if ($page < $counter - 1)
            $pagination.= '</ul><a onclick="return seasonal_pagged('.$next.');" class="next-page bx-shadow float-right"><img src="images/arrow-right.png" alt=""></a>';
        else
            $pagination.= '</ul><a class="next-page bx-shadow float-right"><img src="images/arrow-right.png" alt=""></a>';
        $pagination.= '</div>';
    }
    //$pagination = $html = "";

}

echo json_encode(array("html"=>$html, "pagination" => $pagination));
?>