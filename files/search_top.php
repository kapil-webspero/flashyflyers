<div class="SearchTop" <?php if($_REQUEST['type']=="custom"){  ?> style="display:none;"<?php } ?>>
<div class="container">
<form id="filter_form_search_top" method="post" onsubmit="return false;">
<?php 
$template_type ="regular_template";
if(basename($_SERVER['SCRIPT_NAME'])=="psdshop.php"){
	$template_type ="psd_customize_template";
} ?>
<input type="hidden" name="template_type" id="template_type" value="<?php echo $template_type; ?>">
<input type="hidden" name="limitPerPage" id="limitPerPage" value="12">
<input type="hidden" name="page_id" id="page_id" value="1">
	<input type="text" value="<?php if(basename($_SERVER['SCRIPT_NAME'])=="search.php" || basename($_SERVER['SCRIPT_NAME'])=="psdshop.php" ){ echo str_replace("%20"," ",$_REQUEST['search_term']);}?>" onkeydown="if (event.keyCode == 13) {jQuery('#searchTopBtn').trigger('click'); return false; }" placeholder="What are you looking for? E.g. Party flyer" name="search_term" id="search_term" />
   <i class="fa fa-search" id="searchTopBtn"></i>
<?php if(basename($_SERVER['SCRIPT_NAME'])!="search.php" && basename($_SERVER['SCRIPT_NAME'])!="psdshop.php"){ ?>
</form>
<?php } ?>
    </div>
</div>

<script>
   function filter_products_search(){
	   $(".mainLoader").show();var formData=$('#filter_form_search_top').serialize();$.ajax({type:"POST",url:"<?=SITEURL;?>ajax/advance-product-filter.php",data:formData,success:function(regResponse){console.log(regResponse);regResponse=JSON.parse(regResponse);$("#productList").html(regResponse.html);
   
   $(".per_page").html(regResponse.perPage);
   $(".paginations").html(regResponse.pagination);$(".mainLoader").hide();recro();startSlickFunction(); 
 	  

   	$("#productList img.hompageimage").each(function() {
		$(this).attr('src',$(this).attr('data-src')).removeClass('hompageimage');
    });
		  

	
   
   
   var $slider=jQuery('.flexslider');$slider.flexslider({controlNav:!1,animation:"slide",slideshow:!1,start:function(slider){slider.mouseover(function(){slider.flexslider("next");slider.manualPause=!1});slider.mouseout(function(){slider.manualPause=!0;slider.pause()})}})}})}
jQuery(document).ready(function(e) {
    jQuery("#searchTopBtn").click(function(e) {
		$("#page_id").val(1);
		
		var tags = jQuery("#search_tags").val();
		var product_types = '<?php echo $_REQUEST['product_type'] ?>';
		var search_term = jQuery("#search_term").val();
		searchPageRedirect(tags,product_types,search_term);
		
		
		<?php if(basename($_SERVER['SCRIPT_NAME'])!="search.php"){ ?>
	    	//	window.location ='<?php echo SITEURL ?>search.php?search_term='+jQuery("#search_term").val();
		<?php }else{ ?>
		//filter_products_search();
		<?php } ?>
    });
	<?php if(basename($_SERVER['SCRIPT_NAME'])=="search.php" || basename($_SERVER['SCRIPT_NAME'])=="psdshop.php"){ ?> 
jQuery(document).ready(function(e) {
	filter_products_search();

});
<?php } ?>
});

function resetForm(){
jQuery("#page_id").val("1");
jQuery("#limitPerPage").val("12");	
jQuery("#search_term").val("");
jQuery(".acordianUl li input").prop("checked",false);
jQuery("#search_sort_by").prop('selectedIndex', 0);
jQuery("#search_tags").val("");
jQuery(".SearchTagLists li").removeClass("active");
<?php if(isset($_REQUEST['type']) && $_REQUEST['type']=='custom'){ ?>
window.location = "search.php?type=custom";
<?php }else if(basename($_SERVER['SCRIPT_NAME'])=="psdshop.php"){
?>
window.location = "psdshop.php";
<?php 	
}else{ ?>
window.location = "search.php";
<?php } ?>
//filter_products_search();
}
jQuery(document).on("change","#per_page",function(){
	jQuery("#page_id").val("1");
	jQuery("#limitPerPage").val(jQuery(this).val());	
	filter_products_search();
});
</script>
