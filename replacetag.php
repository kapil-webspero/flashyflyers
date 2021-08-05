<?php 
include("wordpress/wp-config.php");
global $wpdb;
$select = $wpdb->get_results("SELECT * FROM `tbl_prodcut_tags` ORDER BY `Id` DESC",ARRAY_A);
foreach($select  as $s){
	$wpdb->update('tbl_prodcut_tags',array('TagSlug'=>sanitize_title_with_dashes($s['TagName'])),array("Id"=>$s['Id']));
}

$select1 = $wpdb->get_results("SELECT * FROM `tbl_producttypes`",ARRAY_A);
foreach($select1  as $s){
	$wpdb->update('tbl_producttypes',array('Slug'=>sanitize_title_with_dashes($s['Name'])),array("ID"=>$s['ID']));
}
echo "<pre>";
print_r($select);
?>