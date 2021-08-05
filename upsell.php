<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	
    if(!empty($_SESSION['CART']))
    {
        ?>
        <div class="container">
                <div class="related-products pt-3 pb-5">
                    <h3>Upsell products</h3>
                    <div class="rlt-slider">
        <?php
        $productArr=[];
        foreach($_SESSION['CART'] as $product)
        {
           $productId='';

           if($product['dimensional']=='2D' && $product['type_banner'][0]=='static' && !isset($product['type_banner'][1]))
           {
              $productId=$product['id'];  
           }

           elseif(isset($product['customeProductFields']['flyer_design']) && $product['customeProductFields']['flyer_design']['secondary_options']['3d_or_2d']=='2D')

           {
               $productId=$product['id']; 
           }

           if($productId!="")
           {
            $product=GetSglRcrdOnCndi(PRODUCT, " Addon = '0' AND id='".$productId."'");
            $IsCustomProduct = $product['CustomProduct']; 
            if($IsCustomProduct=="yes"){
				$related = GetMltRcrdsOnCndi(PRODUCT, "id =".$product['id']." and CustomProduct='".$IsCustomProduct."' and Addon='0'");
			}else{
            	$related = GetMltRcrdsOnCndi(PRODUCT, "id =".$product['id']."  and Addon='0'");
			}
            ?>
            
                        <?php foreach($related as $r) {
							
							
							if($r['template_type']=="psd_customize_template" || $r['template_type']=="psd_only"){
								$r['Baseprice'] = $r['psd_price'];
							}

                               $getBanners = GetMltRcrdsOnCndi(PRODUCT_BANNER, "`prod_id` = '".$r['id']."' AND `filetype`= 'image' and set_default_facebookimage = 'no'");
                    
                     
						$getMainBanner = (!empty($getBanners[0]['filename'])) ? $getBanners[0]['filename'] : '';	
                            ?>
                            <div class="item">
                                <div class="flyer-product">
                                    <div class="flyer-img">
                                        <a href="<?=SITEURL.'p/'.$r['slug'];?>">
                                            <?php if($getMainBanner2 != '')
                                            {
											    
                                               echo productImageSrc($getMainBanner2,$r['id'],'354');
												?>
                                                
                                                   
											<?php 

                                            }
                                            else{
											echo productImageSrc($getMainBanner,$r['id'],'354');

                                                ?>     
                                                <?php
                                            }
                                            ?>
                                        </a>
                                    </div>
                                    <?php
                                    $bookmarkCls = "bookmark-btn";
                                    if(in_array($r['id'],$bookmarks)) {
                                        $bookmarkCls = "my-bookmark-btn";
                                    }
                                    ?>
                                    <a class="Pbook_<?=$r['id'];?> <?=$bookmarkCls?>" onClick="return action_bookmark(<?=$r['id'];?>);"><i class="far fa-heart"></i></a>
                                    <div class="relatedProductName">
                                    	<h2><a href="<?=SITEURL;?>p/<?=$r['slug'];?>"><?php echo $r['Title'] ?></a></h2>
                                    </div>
                                    <div class="buy-btns text-center">
                                        <a class="buy-btn" href="<?=SITEURL;?>p/<?=$r['slug'];?>"><span class="price">$<?=formatPrice($r['Baseprice'])?></span> <span class="buy-label"><i class="fas fa-shopping-cart"></i> Order</span></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                  


           <?php 
         }
                        
            ?>
        <?php
        }
        ?>
              </div>

</div>
</div>

        <?php
    }	

    else
    {
        echo 'No Products';
    }