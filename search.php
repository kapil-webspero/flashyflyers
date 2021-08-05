<?php
ob_start();

require_once 'function/constants.php';

require_once 'function/configClass.php';

require_once 'function/siteFunctions.php';

?>



<!DOCTYPE html>

<html lang="en">

<head>

    <title>Search | Flashy Flyers</title>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
     <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

    <?php require_once 'files/headSection.php'; ?>
    <script src="js/bootstrap.min.js" defer></script>



<script src="js/popper.min.js" defer></script>

<script src="js/slick.min.js"></script>

<script src="js/jquery.fCarousel.min.js" defer></script>

<script src="js/script.js" defer></script>

<script src="js/filter.js" defer></script>



</head>



<body class="<?php echo $_REQUEST['type'] ?>">

<?php require_once 'files/headerSection.php';
$productTypeList = getParentProductTypesListFront();
$tagLists = getProductActiveTags();

 ?>





<div class="page-wrap pb-5 searchBlockSection pt-3 bubble-bg-3" style="padding-top:0px !important;">

        <div class="container">
        <div class="notification success" id="success" style="display:none;">
                    <div class="d-flex"><i class="fas fa-check"></i></div> <span class="msg"></span><button class="close-ntf"><i class="fas fa-times"></i></button></div>
        <div class="row justify-content-center">
        
        <div class="col-sm-12 text-right" style="margin-bottom:10px;"> <span style="cursor:pointer;" class="resetLINK resetLINKTOp" onClick="resetForm()">Reset the search</span> </div>

			<div class="col-md-3">
            	<div class="SearchBlock">
                	
                    <?php if($_REQUEST['type']=="custom"){
						
						$tagLists = getCustomProductActiveTags();
					?>
                      <input type="hidden"  name="type" id="type" value="custom">
                         <h3>Sort by</h3>
                       <select class="form-control" name="search_sort_by" id="search_sort_by">
                    	<option value="bestseller">Bestseller</option>
                    	<option value="price_low_to_high">Price low to high</option>
                    	<option value="price_high_to_low">Price high to low</option>
                    	<option value="Latest">Latest</option>
                    </select>
                      
                     <?php if(!empty($tagLists)){ ?> 
                        
                    <h3 class="SearchByProductTypes">Tags</h3>
                    	  <input type="hidden" name="tags" id="search_tags" value="">
                        <ul class="SearchTagLists">
                        	<?php foreach($tagLists as $tag){ ?>
                            	<li data-id="<?php echo $tag['TagSlug']; ?>" <?php echo ($tag['TagSlug']==$_REQUEST['tag'])?"class='active'":""; ?> id="tags_id_<?php echo $tag['Id'] ?>"><?php echo $tag['TagName']; ?></li>
							<?php } ?>
                        </ul>
                        <div class="ViewMoreTags" data-name="view_more">View more</div>	
						<?php } ?>
                        
                    <?php 	
					}else{ ?>
                    <h3>Sort by</h3>
                    <select class="form-control" name="search_sort_by" id="search_sort_by">
                    	<option value="bestseller">Bestseller</option>
                    	<option value="price_low_to_high">Price low to high</option>
                    	<option value="price_high_to_low">Price high to low</option>
                    	<option value="Latest">Latest</option>
                    </select>
                    
                   <?php /*?> <h3 style="margin-top:10px;">Template Type</h3>
                    <select class="form-control" name="type" id="search_type" onChange="search_type()">
                    	<option value="custom" <?php echo (isset($_REQUEST['type']) && $_REQUEST['type']=="custom")?"selected":""; ?>>Custom</option>
                    	<option value="flayer" <?php echo (!isset($_REQUEST['type']) || $_REQUEST['type']=="flayer")?"selected":""; ?>>Flayer</option>
                    </select><?php */?>
                    
                    
                    <h3 class="SearchByProductTypes">Product types</h3>
                    	
                        <?php 
						//$_SERVER['HTTP_USER_AGENT'] = "mobile";
						if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'mobile') || strstr(strtolower($_SERVER['HTTP_USER_AGENT']), 'android')) {
						$GetProductTypes = array();
						if($_REQUEST['product_type']!=""){
							$GetProductTypes = explode(",",$_REQUEST['product_type']);
							
						}
						?>
                        <div class="multiselectAddons"> <div class="selectBoxAddons"> <select class="form-control"> <option>selelct product types</option> </select> <div class="overSelectAddons"></div></div><div id="checkboxesAddons2" class="checkboxesAddons"><ul class="CategorySearch acordianUl">
						<?php 
							if(!empty($productTypeList)){
								foreach($productTypeList as $singleKey=>$singleValue){
									
									$checked = "";
									if(!empty($GetProductTypes) && in_array($singleValue['main_cat']['Slug'],$GetProductTypes)){
										$checked = "checked='checked'";
									}
									echo '<li class="category_search_main"> <label class="main_cat custom-control custom-checkbox">
                                		<input '.$checked.' type="checkbox" name="MaincategorySearch[]"  class="custom-control-input" value="'.$singleValue['main_cat']['Slug'].'">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">'.$singleValue['main_cat']['Name'].'</span>
                            </label>';	
									if(!empty($singleValue['sub_cat'])){
										echo '<span class="handle"><i class="fa fa-plus"></i></span>';	
									}
									
									if(!empty($singleValue['sub_cat'])){
										echo "<ul class='acordianUl subUl'>";
											foreach($singleValue['sub_cat'] as $singleSubcat){
												
									$checked = "";
									if(!empty($GetProductTypes) && in_array($singleSubcat['Slug'],$GetProductTypes)){
										$checked = "checked='checked'";
									}
												echo '<li class="category_search_sub"> <label class="sub_cat custom-control custom-checkbox">
                                		<input '.$checked.' type="checkbox" name="SubcategorySearch[]" class="custom-control-input" value="'.$singleSubcat['Slug'].'">
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">'.$singleSubcat['Name'].'</span>
                            </label></li>';	
											}
										echo "</ul>";	
									}
									echo "</li>";
								}	
							}
						?>
                      
                        </ul> </div></div>
                        <?php }else{ ?>
                    
                    	<ul class="CategorySearch acordianUl">
						<?php 
						$GetProductTypes = array();
						if($_REQUEST['product_type']!=""){
							$GetProductTypes = explode(",",$_REQUEST['product_type']);
							
						}
						
							if(!empty($productTypeList)){
								foreach($productTypeList as $singleKey=>$singleValue){
									$checked = "";
									if(!empty($GetProductTypes) && in_array($singleValue['main_cat']['Slug'],$GetProductTypes)){
										$checked = "checked='checked'";
									}
									echo '<li class="category_search_main"> <label class="main_cat custom-control custom-checkbox">
                                		<input type="checkbox" name="MaincategorySearch[]"  class="custom-control-input" value="'.$singleValue['main_cat']['Slug'].'" '.$checked.'>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">'.$singleValue['main_cat']['Name'].'</span>
                            </label>';	
									if(!empty($singleValue['sub_cat'])){
										echo '<span class="handle"><i class="fa fa-plus"></i></span>';	
									}
									
									if(!empty($singleValue['sub_cat'])){
										echo "<ul class='acordianUl subUl'>";
											foreach($singleValue['sub_cat'] as $singleSubcat){
											
									$checked = "";
									if(!empty($GetProductTypes) && in_array($singleSubcat['Slug'],$GetProductTypes)){
										$checked = "checked='checked'";
									}
											
												echo '<li class="category_search_sub"> <label class="sub_cat custom-control custom-checkbox">
                                		<input type="checkbox"  name="SubcategorySearch[]" class="custom-control-input" value="'.$singleSubcat['Slug'].'" '.$checked.'>
                                <span class="custom-control-indicator"></span>
                                <span class="custom-control-description">'.$singleSubcat['Name'].'</span>
                            </label></li>';	
											}
										echo "</ul>";	
									}
									echo "</li>";
								}	
							}
						?>
                      
                        </ul>
                        <?php } ?>
                        
                        
                       <?php if(!empty($tagLists)){ ?> 
                        
                    <h3 class="SearchByProductTypes">Tags</h3>
                    	  <input type="hidden" name="tags" id="search_tags" value="">
                        <ul class="SearchTagLists">
                        	<?php foreach($tagLists as $tag){ ?>
                            	<li data-id="<?php echo $tag['TagSlug']; ?>" <?php echo ($tag['TagSlug']==$_REQUEST['tag'])?"class='active'":""; ?> id="tags_id_<?php echo $tag['Id'] ?>"><?php echo $tag['TagName']; ?></li>
							<?php } ?>
                        </ul>
                        <div class="ViewMoreTags" data-name="view_more">View more</div>	
						<?php } ?>
                        
                       <?php } ?> 
                        
                </div>
            </div>   
            <div class="col-md-9">
            
            <div class="ProductSearchResults">
            <div class="product-listing-inner">
                <div class="row" id="productList"></div>
            </div>         
             <div class="product-per_page pb-5 clearfix per_page"></div>
             <div class="product-pagination pb-5 clearfix paginations"></div>
</div>
</div>
    </div>

</div>




</div>
</form>





<?php require_once 'files/footerSection.php' ?>
<script>
jQuery(document).on('click','.acordianUl li .handle',function(){

var _this=jQuery(this).parent();
var _thisHandle=jQuery(this);

if(jQuery(_this).hasClass('open'))
{
	jQuery(_this).removeClass('open');
	jQuery(_this).find('.acordianUl.subUl').first().fadeOut(300);
	jQuery(_thisHandle).html('<i class="fa fa-plus"></i>');
	
}
else
{
	jQuery(_this).addClass('open');
	jQuery(_this).find('.acordianUl.subUl').first().fadeIn(300);
	jQuery(_thisHandle).html('<i class="fa fa-minus"></i>');
}
});
function searchPageRedirect(tags="",product_types="",search_term=""){
	var searchUrl = [];
	var hasQuestion = '';
		
		if(tags!=""){
			searchUrl.push("tag="+tags);
		}
		if(product_types!=""){
			searchUrl.push("product_type="+product_types);
		}
		
		if(search_term!=""){
			searchUrl.push("search_term="+search_term);
		}
		hasQuestion += searchUrl.join('&');
		if(searchUrl.length>0){
			window.location = "search.php?"+hasQuestion;
		}else{
			window.location = "search.php";
		}
	
}

function  productTypeFilter(){
	            var SelectedProductType = [];

            jQuery.each(jQuery(".CategorySearch  input.custom-control-input:checked"), function(){
                SelectedProductType.push($(this).val());
		});
		var tags = '<?php echo $_REQUEST['tag']; ?>';
		var product_types = SelectedProductType.join(",");
		var search_term = '<?php echo $_REQUEST['search_term']; ?>';
		searchPageRedirect(tags,product_types,search_term);
			
		
		
}
jQuery(document).on("change",".category_search_main label.main_cat input",function(e){
	
	var checked = jQuery(this).is(":checked");
		var _this = this;
		if(checked==true){
			jQuery(_this).parents("li").find("input").prop("checked",true);	
		}
		if(checked==false){
			jQuery(_this).parents("li").find("input").prop("checked",false);	
		}
		productTypeFilter();	
		//filter_products_search();
});


jQuery(document).on("change",".category_search_main label.sub_cat input",function(e){
	var totalLength = jQuery(this).parents('.subUl').find("input").length;
	var _this = this;
	var selecteLength = jQuery(this).parents('.subUl').find("input:checked").length;
	if(selecteLength==0){
		jQuery(_this).parents(".category_search_main").find(".main_cat input").prop("checked",false);	
	}
	if(selecteLength==totalLength){
		jQuery(_this).parents(".category_search_main").find(".main_cat input").prop("checked",true);	
	}
	if(selecteLength!=totalLength && selecteLength>0){
		jQuery(_this).parents(".category_search_main").find(".main_cat input").prop("checked",true);	
	}
	productTypeFilter();
	$("#page_id").val(1);
	//filter_products_search();
		
});
jQuery(document).ready(function(e) {
	<?php if($_REQUEST['tag']!=""){ ?>
    jQuery("#search_tags").val("<?php echo $_REQUEST['tag']; ?>");
	jQuery("#tags_id_<?php echo $_REQUEST['tag']; ?>").trigger("click");
	<?php }else{ ?>
	 jQuery("#search_tags").val("");
	<?php } ?>
});

jQuery(".SearchTagLists li").click(function(e){
	var _this = this;
	jQuery("#search_tags").val("");
	if(jQuery(_this).hasClass('active')){
		jQuery(_this).removeClass("active");
		jQuery(".SearchTagLists li").removeClass("active");
		
	}else{
		jQuery(".SearchTagLists li").removeClass("active");
		
		jQuery("#search_tags").val(jQuery(_this).attr("data-id"));
		jQuery(_this).addClass("active");
	}
	
	
		var tags = jQuery("#search_tags").val();
		var product_types = '<?php echo $_REQUEST['product_type'] ?>';
		var search_term = '<?php echo $_REQUEST['search_term'] ?>';
		searchPageRedirect(tags,product_types,search_term);
		
	
	
	$("#page_id").val(1);
//	filter_products_search();
	
	
});

jQuery(document).on("change","#search_sort_by,#search_type",function(e){
	filter_products_search();	
});




 


</script>



<!----SCRIPTS---------->



<script>

function LoadImages(){
}

  function recro(){if($(window).width()>768){$("#showprop").mouseleave(function(){var content="";$(this).hide(0).html(content)})}
$('#newcarousel').fCarousel({'distance':550,'perspective':0,'centerItem':0,'sepration':550,autoplay:6000,'responsive':{1200:{'separation':350},1024:{'separation':250},960:{'separation':350},600:{'separation':300},320:{'separation':150},0:{'sepration':150}}})}
function startSlickFunction(){var $slideshow=$('#showprop > .flyer-gallery');var ImagePauses=[60,4000,4000,4000];$slideshow.slick({slidesToShow:1,slidesToScroll:1,arrows:!0,autoplay:!0,prevArrow:'<i class="icon-angle-left"></i>',nextArrow:'<i class="icon-angle-right"></i>',initialSlide:2,autoplaySpeed:1,pauseOnHover:!1,pauseOnFocus:!1});$slideshow.on('afterChange',function(event,slick,currentSlide){$slideshow.slick('slickSetOption','autoplaySpeed',4000,!0)})}
function action_bookmark(productid){$.ajax({type:"POST",url:"<?=SITEURL;?>ajax/make-favorite.php",data:"favoriteTo="+productid,success:function(regResponse){regResponse=JSON.parse(regResponse);if(regResponse.status=="login"){window.location= '<?php echo SITEURL ?>login.php';}else if(regResponse.status=="error"){
					$(".Pbook_"+productid).removeClass("my-bookmark-btn");$(".Pbook_"+productid).addClass("bookmark-btn");$("#success").attr('style','display:flex;');$("#success").find("span.msg").html(regResponse.message);$('html, body').animate({scrollTop:$("#success").offset().top},1000);setTimeout(function(){$("#success").hide(500)},12000)}else if(regResponse.status=="success"){$(".Pbook_"+productid).removeClass("bookmark-btn");$(".Pbook_"+productid).addClass("my-bookmark-btn");$("#success").attr('style','display:flex;');$("#success").find("span.msg").html(regResponse.message);$('html, body').animate({scrollTop:$("#success").offset().top},1000);setTimeout(function(){$("#success").hide(500)},12000)}}})}
    <?php /*?>function action_buy(productid) {
           	 $.ajax({
				type: "POST",
				url: "<?=SITEURL;?>ajax/cart-process.php",
				data: "action=add&productID="+productid,
				success: function(regResponse) {
					regResponse = JSON.parse(regResponse);
                    $(".cart-product").html(regResponse.cart_count);
					setTimeout(function(){ window.location.href = "<?=SITEURL;?>cart.php"; }, 3000);
				}
			});
		}<?php */?>

function pagged(pageid){$("#page_id").val(pageid);filter_products_search()}
$(document).ready(function(){recro();});


</script>
	<link rel="stylesheet" href="flexslider/css/flexslider.css" type="text/css" media="screen" />

 <!-- jQuery -->
<script defer src="flexslider/js/jquery.flexslider.js" defer="defer"></script>

  <script type="text/javascript">
  
   jQuery(document).ready(function(){var $slider=jQuery('.flexslider');$slider.flexslider({controlNav:!1,animation:"slide",slideshow:!1,start:function(slider){slider.mouseover(function(){slider.flexslider("next");slider.manualPause=!1});slider.mouseout(function(){slider.manualPause=!0;slider.pause()})}})})

jQuery(document).ready(function(e) {
    jQuery(document).on("click",".ViewMoreTags",function(){
		var data_name = jQuery(this).attr("data-name");
		if(data_name=="view_more"){
			jQuery(this).attr("data-name","view_less");
			jQuery(this).html("View less");
			jQuery(".SearchTagLists li").css("display","inline-block");
		}
		if(data_name=="view_less"){
			jQuery(this).attr("data-name","view_more");
			jQuery(this).html("View more");
			jQuery(".SearchTagLists li:gt(5)").hide();
		}
			
	});
});

</script>
<style>
.ProductSearchResults{    padding: 40px 40px 0 40px;
    border: 2px solid #eee;
    border-radius: 10px;
}
.ProductSearchResults .product-listing-inner{ padding:0px !important; border:0px !important;}
.custom-checkbox .custom-control-input:checked ~ .custom-control-indicator{ background-image:url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3E%3Cpath fill='rgba(0,0,0,.5)' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26 2.974 7.25 8 2.193z'/%3E%3C/svg%3E") !important}
</style>
</body>
</html>