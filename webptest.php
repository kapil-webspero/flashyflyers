<?php 
$file='testingimag.jpg';
$image=  imagecreatefromjpeg($file);
ob_start();
imagejpeg($image,NULL,100);
$cont=  ob_get_contents();
ob_end_clean();
imagedestroy($image);
$content =  imagecreatefromstring($cont);
imagewebp($content,'testingimag1.webp');
imagedestroy($content);	
?>