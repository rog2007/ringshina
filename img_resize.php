<?php
  header ("Content-type: image/jpeg");
  $im=imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"].$_GET["infile"]);
  $old_h=imagesy($im);
  $old_w=imagesx($im);
  $per=$_GET["newh"]*100/$old_h;
  $new_w=$old_w*$per/100;
  $im1=imagecreatetruecolor($new_w,$_GET["newh"]);
  imagecopyresampled($im1,$im,0,0,0,0,$new_w,$_GET["newh"],$old_w,$old_h);
  imagedestroy($im);
  imagejpeg($im1);
?>
