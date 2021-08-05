jQuery(document).ready(function($){$("input [type=file]").on("change",function(){alert("hey")});$('.inputfile').each(function()
{var $input=$(this),$label=$input.next('label'),labelVal=$label.html();$input.on('change',function(e)
{var fileName='';if(this.files&&this.files.length>1)
fileName=(this.getAttribute('data-multiple-caption')||'').replace('{count}',this.files.length);else if(e.target.value)
fileName=e.target.value.split('\\').pop();if(fileName)
$label.find('span').html(fileName);else $label.html(labelVal)});$input.on('focus',function(){$input.addClass('has-focus')}).on('blur',function(){$input.removeClass('has-focus')})});$('.product-slider').slick({slidesToShow:1,slidesToScroll:1,arrows:!0,fade:!0,asNavFor:'.product-slider-thumb',autoplay:!1,prevArrow:'<i class="icon-angle-left"></i>',nextArrow:'<i class="icon-angle-right"></i>',adaptiveHeight:!0});$('.product-slider-thumb').slick({slidesToShow:4,asNavFor:'.product-slider',dots:!0,arrows:!1,focusOnSelect:!0,responsive:[{breakpoint:600,settings:{slidesToShow:4,}},{breakpoint:380,settings:{slidesToShow:3,dots:!0,arrows:!1,}}]});$('.rlt-slider').slick({slidesToShow:4,arrowss:!0,focusOnSelect:!0,prevArrow:'<i class="icon-angle-left"></i>',nextArrow:'<i class="icon-angle-right"></i>',responsive:[{breakpoint:769,settings:{slidesToShow:3,}},{breakpoint:601,settings:{slidesToShow:2,}},{breakpoint:441,settings:{slidesToShow:1,}}]});$('.category-carousel').slick({slidesToShow:4,arrowss:!0,focusOnSelect:!0,prevArrow:'<i class="icon-angle-left"></i>',nextArrow:'<i class="icon-angle-right"></i>',responsive:[{breakpoint:769,settings:{slidesToShow:3,}},{breakpoint:601,settings:{slidesToShow:2,}},{breakpoint:441,settings:{slidesToShow:1,}}]})})


var expanded = false;
jQuery(document).ready(function(e) {
   // alert("ready");
    jQuery(".selectBoxAddons").click(function(event) {
		expanded = false;
		// jQuery(".checkboxesAddons").hide(); 
		  //	jQuery(this).parent('.multiselectAddons').find(".checkboxesAddons").removeClass("ActiveAddons");
		   jQuery('.checkboxesAddons').hide();
		/*  if(expanded && jQuery(this).parent('.multiselectAddons').find(".checkboxesAddons:visible")) {
			 	alert("1");
  			  jQuery('.checkboxesAddons').hide();
			    expanded = false;
				
		  }
		else*/ 
		 if (jQuery(this).parent('.multiselectAddons').find(".checkboxesAddons").hasClass("ActiveAddons")) {
		
			jQuery(this).parent('.multiselectAddons').find(".checkboxesAddons").hide(); 
			jQuery(this).parent('.multiselectAddons').find(".checkboxesAddons").removeClass("ActiveAddons");
			expanded = false;
		}else{
		
			jQuery(".checkboxesAddons").removeClass("ActiveAddons");
		
			jQuery(this).parent('.multiselectAddons').find(".checkboxesAddons").show(); 
			jQuery(this).parent('.multiselectAddons').find(".checkboxesAddons").addClass("ActiveAddons"); 
			   expanded = true;
		 
		 }
		 // expanded = false;
		 
		
    });
	
var $=jQuery;
$(document).on('click touchstart', function () {
  $target = $(event.target);
 // alert("12345674");
	
  if(!$target.closest('.checkboxesAddons').length &&  $('.checkboxesAddons').is(":visible") && $target.closest('.selectBoxAddons').length==0) {
    $('.checkboxesAddons').hide();
//	alert("test");
	 expanded = false;
	 jQuery(".checkboxesAddons").removeClass("ActiveAddons");
  }
      
});	
});
