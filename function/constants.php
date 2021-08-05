<?php
	ob_start();
//	error_reporting(0);
	session_start();
    // date_default_timezone_set('US/Eastern');
		date_default_timezone_set('Asia/Kolkata');


	//date_timezone_set("Asia/Kolkata");
	$systemIp = $_SERVER['REMOTE_ADDR'];
	$systemTime = time();

    define("SITENAME","Flashy");
    // define("SITEURL","https://staging.flashyflyers.com/");
    define("SITEURL","http://flashyflyers.local/");
	// define("ADMINURL","https://staging.flashyflyers.com/superadmin/");
	define("ADMINURL","http://flashyflyers.local/superadmin/");
	define("SITEURL_BASE","flashyflyers.local");

	define("SITE_BASE_PATH",$_SERVER['DOCUMENT_ROOT']."/");


	define("SMTP_PORT", "465");
	define("SMTP_HOST", "mail.flashyflyers.com");
	define("SMTP_USER", "noreply@flashyflyers.com");
	define("SMTP_PASS","~c@q(;}2W(1v");
	define("SMTP_SENDER","noreply@flashyflyers.com");

	#### CREATE CONSTANTS FOR TABLE NAME

	define("ADMIN_DATA","tbl_adminlogin");
	define("CATEGORIES","tbl_category");
	define("CATEGORIES_ADDON","tbl_category_addon");
	define("FAVOURITE","tbl_favourite");
	define("SHOP","tbl_shop");
	define("PRODUCT","tbl_products");
	define("SETTINGS","tbl_settings");
	define("PRODUCT_SIZE","tbl_productsizes");
	define("PRODUCT_TYPE","tbl_producttypes");
	define("PRODUCT_REL","tbl_related");
	define("PRODUCT_BANNER","tbl_prodphotos_dev");
	define("ADDON_PRICE","tbl_variation_prices");
	define("ORDER","tbl_orders");
	define("CHANGE_REQ", "tbl_change_req");
	define("TRANSACTION","tbl_transactions");
	define("USERS","tbl_users");
	define("USERS_ROLES","tbl_user_roles");
	define("ORDER_DISCUSSTIONS","tbl_order_discussions");
	define("BUG_REPORT","tbl_bug_reports");
	define("NOTIFICATIONS","tbl_notifications");
	define("NOTIFICATIONS2","tbl_notifications2");
	define("BUG_IMAGES","tbl_bug_images");
	define("DISCOUNT","tbl_discount");
	define("RESET","tbl_resetpw");
	define("FLYERS","tbl_flyers_dev");
	define("PRODUCT_TAGS","tbl_prodcut_tags");
	define("PRODUCT_RATING_COMMENT","tbl_product_rating_comment");
	define("EXTRA_MOCKUPS","tbl_extra_mockups");
	define("OPTION_PRICE","tbl_options");
	define("DESIGN_TRANSACTION","tbl_designer_transactions");
	define("ORDER_PRODUCT_MOCKUP","tbl_order_product_mokup");
	define("PRODUCTS_REVIEW","tbl_products_review");
	define("REPRESENTATIVE_ORDERS","tbl_rep_orders");

	#### Mailchimp
	//define("MAILCHIMP_API_KEY","8ac7c860c8731b68a4a85e842823a9eb-us19");
	//define("MAILCHIMP_LIST_ID","aba7f200fb");


	define("MAILCHIMP_API_KEY","fedcf27b64550e5c07fe9410dc593089-us20");
	define("MAILCHIMP_LIST_ID","52a96ada90");


	#### Facebook
//	define("FACEBOOK_APP_ID","1875996586028799");
//	define("FACEBOOK_APP_SECRET","ad75fcad4f0568e757dbc922dbd1e574");

	define("FACEBOOK_APP_ID","528195170934835");
	define("FACEBOOK_APP_SECRET","a566f0804d99ec487c3c7f5d84463519");


	### Stripe Keys
//  define("PUBLISHABLE_KEY","pk_live_yAfdNE10pBNQ24g9ird5uqWO");
//   define("SECRET_KEY","sk_live_pPEC8itbqAXeKpkYX9N4MoR7");

		define("PUBLISHABLE_KEY","pk_test_sYXjSbKO3xVtRRJRCm1f0cjM");
		define("SECRET_KEY","sk_test_K8hECiurc3auCx9uibvDvNz9");


		define("MEDIA_CHANGE_PRICE","9.95");
		define("MEDIA_CHANGE_PRICE_2","19.95");
		define('FILE_SIZE_MB', 2000000);


	### PAYPAL kEYS
// 	$paypalURL = "https://www.paypal.com/";
// 	$paypalId="support@flashyflyers.com";

$paypalURL = "https://www.sandbox.paypal.com/";
$paypalId="info@webspero.com";
//$paypalId="karan@evilla.com";

	### SENDGRID kEYS
	// name: flashy_api
	define("SENDGRID_API_KEY", "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");



	$searchResultsPerPage = 10;

//	$deliArr = array("Normal", "Next day", "In 2 days", "Next week");

    $deliArr = array(1 => "12 Hours Same-Day", 2 => "24 hours", 3 => "2-3 business days","4"=>"24 hours","5"=>"1-2 days","6"=>"2-3 days");
	$allowedVideo =  array('WEBM','MPG' ,'MP2','MPEG','MPE','MPV','OGG','MP4','M4P','M4V','AVI','WMV','FLV');

	$customProductDelTimePrice = array(4 => "25.00", 5 => "10",6=>"Free");
	$checkbox_sided = 25;
	$add_music = 25;
	$add_video = 25;
	$add_facebook_cover = 35;


	header("Access-Control-Allow-Origin: *");
	//header('Content-Type: text/html; charset=ISO-8859-1');

	$statesarr=array(
			  'AL' => 'Alabama',
			  'AK' => 'Alaska',
			  'AZ' => 'Arizona',
			  'AR' => 'Arkansas',
			  'CA' => 'California',
			  'CO' => 'Colorado',
			  'CT' => 'Connecticut',
			  'DE' => 'Delaware',
			  'DC' => 'District Of Columbia',
			  'FL' => 'Florida',
			  'GA' => 'Georgia',
			  'HI' => 'Hawaii',
			  'ID' => 'Idaho',
			  'IL' => 'Illinois',
			  'IN' => 'Indiana',
			  'IA' => 'Iowa',
			  'KS' => 'Kansas',
			  'KY' => 'Kentucky',
			  'LA' => 'Louisiana',
			  'ME' => 'Maine',
			  'MD' => 'Maryland',
			  'MA' => 'Massachusetts',
			  'MI' => 'Michigan',
			  'MN' => 'Minnesota',
			  'MS' => 'Mississippi',
			  'MO' => 'Missouri',
			  'MT' => 'Montana',
			  'NE' => 'Nebraska',
			  'NV' => 'Nevada',
			  'NH' => 'New Hampshire',
			  'NJ' => 'New Jersey',
			  'NM' => 'New Mexico',
			  'NY' => 'New York',
			  'NC' => 'North Carolina',
			  'ND' => 'North Dakota',
			  'OH' => 'Ohio',
			  'OK' => 'Oklahoma',
			  'OR' => 'Oregon',
			  'PA' => 'Pennsylvania',
			  'RI' => 'Rhode Island',
			  'SC' => 'South Carolina',
			  'SD' => 'South Dakota',
			  'TN' => 'Tennessee',
			  'TX' => 'Texas',
			  'UT' => 'Utah',
			  'VT' => 'Vermont',
			  'VA' => 'Virginia',
			  'WA' => 'Washington',
			  'WV' => 'West Virginia',
			  'WI' => 'Wisconsin',
			  'WY' => 'Wyoming',
			);

			define('PRODUCT_TYPE_FLYERS',1);
            define('PRODUCT_TYPE_MIXTAPE_COVERS',2);
            define('PRODUCT_TYPE_SINGLE_COVER',3);
            define('PRODUCT_TYPE_FACEBOOK_COVERS',4);
            define('PRODUCT_TYPE_MOTION_ANIMATED_FLYERS',11);
            define('PRODUCT_TYPE_LAPTOP_SKIN_DESIGN',12);
            define('FACEBOOK_PRODUCT_ID',11);

            /*define('PRODUCT_IMAGE_WIDTH',421);
            define('PRODUCT_IMAGE_HEIGHT',617);*/

            define('PRODUCT_IMAGE_WIDTH',375);
            define('PRODUCT_IMAGE_HEIGHT',550);

            define('FLYER_IMAGE_WIDTH',375);
            define('FLYER_IMAGE_HEIGHT',550);

            define('PRODUCT_FACEBOOK_COVER_IMAGE_WIDTH',375);
            define('PRODUCT_FACEBOOK_COVER_IMAGE_HEIGHT',139);

            define('PRODUCT_VIDEO_WIDTH',421);
            define('PRODUCT_VIDEO_HEIGHT',619);

            define('PRODUCT_COVER_NAME_1','FacebookCover');
            define('PRODUCT_COVER_NAME_2','facebook cover');
            $reviewStatus = array("Pending","Approved","Rejected");


$customeProductFields = array(
			"animated_flyer"=>
                                    array("primary_options"=>
                                        array("presenting"=>array("type"=>"text","label"=>"Presenting"),
                                        "main_title"=>array("type"=>"text","label"=>"Main Title"),
                                        "subtitle"=>array("type"=>"text","label"=>"Subtitle"),
                                        "date"=>array("type"=>"datepicker","label"=>"Date"),
                                        "music_by"=>array("type"=>"text","label"=>"Music by"),
                                        "addition_info"=>array("type"=>"textarea","label"=>"Additional Information"),
										"venue"=>array("type"=>"text","label"=>"Venue"),
                                        "address"=>array("type"=>"text","label"=>"Address"),

										"attach_any_logos"=>array("type"=>"multiple_file","label"=>"Attach any pictures/logos"),
                                        )
                                ,"secondary_options"=>
									array(
                                		  "sizes"=>array("type"=>"sizes","label"=>"Sizes"),
										  "3d_or_2d"=>array("type"=>"3d_or_2d","label"=>"3d or 2d"),
                                		  "add_music"=>array("type"=>"add_music","label"=>"Add music (+$".$add_music.")"),
										  "notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),

										  "turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),
                                )),
								"business_card"=>
                                    array("primary_options"=>
                                        array("first_and_last_name"=>array("type"=>"text","label"=>"First and last name"),
                                        "job_title"=>array("type"=>"text","label"=>"Job title"),
                                        "email"=>array("type"=>"text","label"=>"Email"),
                                        "phone_number"=>array("type"=>"text","label"=>"Phone number"),
                                        "website"=>array("type"=>"text","label"=>"Website"),
                                        "company_name"=>array("type"=>"text","label"=>"Company name"),
										"facebook"=>array("type"=>"text","label"=>"Facebook"),
										"twitter"=>array("type"=>"text","label"=>"Twitter"),
										"instagram"=>array("type"=>"text","label"=>"Instagram"),
										"attach_any_logos"=>array("type"=>"multiple_file","label"=>"Attach any logos"),

                                        )
                               ,"secondary_options"=>
									array("checkbox_sided"=>array("type"=>"checkbox_sided","label"=>"+$".$checkbox_sided." for double sided business card"),
                                		 "notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),
                                         "turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),
                                )),
							"flyer_design"=>
                                    array("primary_options"=>
                                        array("presenting"=>array("type"=>"text","label"=>"Presenting"),
                                        "main_title"=>array("type"=>"text","label"=>"Main Title"),
                                        "subtitle"=>array("type"=>"text","label"=>"Subtitle"),
                                        "date"=>array("type"=>"datepicker","label"=>"Date"),
                                        "music_by"=>array("type"=>"text","label"=>"Music by"),
                                        "addition_info"=>array("type"=>"textarea","label"=>"Additional Information"),
										"venue"=>array("type"=>"text","label"=>"Venue"),
                                        "address"=>array("type"=>"text","label"=>"Address"),
										"attach_any_logos"=>array("type"=>"multiple_file","label"=>"Attach any pictures/logos"),
                                        )
                                ,"secondary_options"=>
									array(
										  "sizes"=>array("type"=>"sizes","label"=>"Sizes"),
                                		  "3d_or_2d"=>array("type"=>"3d_or_2d","label"=>"3d or 2d"),
										  "add_facebook_cover"=>array("type"=>"add_facebook_cover","label"=>"Add Facebook Cover(+$".$add_facebook_cover.")"),
										  "notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),
										  "turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),
                                )),
								"flyer_conversion"=>
                                    array("primary_options"=>
                                        array("presenting"=>array("type"=>"text","label"=>"Presenting"),
                                        "main_title"=>array("type"=>"text","label"=>"Main Title"),
                                        "subtitle"=>array("type"=>"text","label"=>"Subtitle"),
                                        "date"=>array("type"=>"datepicker","label"=>"Date"),
                                        "music_by"=>array("type"=>"text","label"=>"Music by"),
                                        "addition_info"=>array("type"=>"textarea","label"=>"Additional Information"),
									    "venue"=>array("type"=>"text","label"=>"Venue"),
                                        "address"=>array("type"=>"text","label"=>"Address"),
                                        )
                                ,"secondary_options"=>
									array(
										  "notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),
									  	  "turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),
                                )),
								"facebook_cover"=>
                                    array("primary_options"=>
                                        array("presenting"=>array("type"=>"text","label"=>"Presenting"),
                                        "main_title"=>array("type"=>"text","label"=>"Main Title"),
                                        "subtitle"=>array("type"=>"text","label"=>"Subtitle"),
                                        "date"=>array("type"=>"datepicker","label"=>"Date"),
                                        "addition_info"=>array("type"=>"textarea","label"=>"Additional Information"),

										"attach_any_logos"=>array("type"=>"multiple_file","label"=>"Attach any picture"),
									    )
										,"secondary_options"=>
									array(
										  "notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),
									  	  "turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),
                                )),
								"logo_design"=>
                                    array("primary_options"=>
                                        array("logo_name"=>array("type"=>"text","label"=>"Logo name?"),
                                        "what_industry_are_you_in"=>array("type"=>"text","label"=>"What industry are you in?"),
                                        "slogan_incorporated_logo"=>array("type"=>"text","label"=>"Do you have a slogan you want to be incorporated in your logo?"),
                                        "any_specific_font"=>array("type"=>"text","label"=>"Preferred fonts"),
                                        "colors"=>array("type"=>"text","label"=>"Preferred colors"),
                                        "audience_organization_or_product"=>array("type"=>"textarea","label"=>"Tell us more about your audience, organization or product. Please also mention your special requirements"),
                                        "files"=>array("type"=>"multiple_file","label"=>"Upload any reference files/images"),
                                        )
                                ,"secondary_options"=>
									array(
									"notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),

									"turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),
                                )),
								"logo_intro"=>
                                    array("primary_options"=>
                                        array(
										"pick_intro"=>array("type"=>"pick_intro","label"=>"Pick your intro #?"),
                                        "attach_logo"=>array("type"=>"multiple_file","label"=>"Attached your logo"),
                                        )
                                ,"secondary_options"=>
									array(
									"notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),

									"turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),
                                )),
								"laptop_skin"=>
                                    array("primary_options"=>
                                        array("facebook"=>array("type"=>"text","label"=>"Facebook"),
                                        "instagram"=>array("type"=>"text","label"=>"Instagram"),
                                        "snapchat"=>array("type"=>"text","label"=>"Snapchat"),
                                        "twitter"=>array("type"=>"text","label"=>"Twitter"),
                                        "youtube"=>array("type"=>"text","label"=>"Youtube"),
                                        "phone_number"=>array("type"=>"text","label"=>"Phone Number"),
                                        "email"=>array("type"=>"text","label"=>"Email"),
                                        "additional_information"=>array("type"=>"textarea","label"=>"Additional Information"),
										"attach_your_logo_design"=>array("type"=>"multiple_file","label"=>"Attach your logo design"),
                                        )
                                ,"secondary_options"=>
									array(
                                		  "brand_model_year "=>array("type"=>"text","label"=>"Laptop:  Brand/Model/Year"),
										  "notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),
                                          "turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),

                                )),
								"mixtape_cover_design"=>
                                    array("primary_options"=>
                                        array("music_genre"=>array("type"=>"text","label"=>"Music genre"),
                                        "artist_name"=>array("type"=>"text","label"=>"Artist name"),
                                        "title"=>array("type"=>"text","label"=>"Title"),
                                        "subtitle"=>array("type"=>"text","label"=>"Subtitle"),
                                        "any_specific_font"=>array("type"=>"text","label"=>"Preferred fonts"),
                                        "stock_images"=>array("type"=>"textarea","label"=>"Stock images"),
                                        "attach_any_pictures"=>array("type"=>"multiple_file","label"=>"Attach any pictures"),
                                        "attach_any_logos"=>array("type"=>"multiple_file","label"=>"Attach any logos"),
                                        "attach_any_style_reference"=>array("type"=>"multiple_file","label"=>"Attach any style reference"),

                                        )
                               ,"secondary_options"=>
									array("sizes"=>array("type"=>"sizes","label"=>"Sizes"),
                                		  "3d_or_2d"=>array("type"=>"3d_or_2d","label"=>"3d or 2d"),
                                		   "notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),
										  "turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),
                                )),



								"video_flyer"=>
                                    array("primary_options"=>
                                        array("presenting"=>array("type"=>"text","label"=>"Presenting"),
                                        "main_title"=>array("type"=>"text","label"=>"Main Title"),
                                        "subtitle"=>array("type"=>"text","label"=>"Subtitle"),
                                        "date"=>array("type"=>"datepicker","label"=>"Date"),
                                        "music_by"=>array("type"=>"text","label"=>"Music by"),
                                        "addition_info"=>array("type"=>"textarea","label"=>"Additional Information"),
										"venue"=>array("type"=>"text","label"=>"Venue"),
                                        "address"=>array("type"=>"text","label"=>"Address"),
                                        )
                                ,"secondary_options"=>
									array(
                                		  "3d_or_2d"=>array("type"=>"3d_or_2d","label"=>"3d or 2d"),
                                		  "add_video"=>array("type"=>"add_video","label"=>"2-3 videos(More videos +$".$add_video.")"),
										  "notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),
                                          "turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),

                                )),

								"3d_logo_conversion"=>
                                    array("primary_options"=>
                                        array("vector_psd_pdf"=>array("type"=>"multiple_file","label"=>"Attach your logo in vector, psd, png,  or pdf"),
										)
										,"secondary_options"=>
									array(
                                		  "notes_to_graphic_designer"=>array("type"=>"notes_to_graphic_designer","label"=>"Notes to graphic designer"),
                                          "turnaround_time"=>array("type"=>"turnaround_time","label"=>"Turnaround Time"),

                                ),
                                ),
            );

$templateFiledsSettings  =  array('main_title'=>'Main Title','sub_title'=>'Sub title','single_title'=>'Single title'
,'deejay_name'=>'Deejay Name','ename'=>'Name','presenting'=>'Presenting'
,'date'=>'Date','music'=>'Music','music_by'=>'Music by'
,'own_song'=>'Own song'
,'additional_info'=>'Additional info'
,'requirements_note'=>'Notes to graphic designer'
,'venue'=>'Venue'
,'address'=>'Address'
,'mixtape_name'=>'Mixtape name'
,'logo'=>'Logo'
,'phonenumber'=>'Phone number'
,'email'=>'Email'
,'produced_by'=>'Produced by'
,'artist_name'=>'Artist name'
,'facebook'=>'Facebook'
,'instagram'=>'Instagram'
,'twitter'=>'Twitter','photos_and_logos'=>'Photos and logos');
?>
