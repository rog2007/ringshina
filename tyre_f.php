<?php
  $r_sm_old=$_GET["r_sm_old"]*0.29;
  $r_sm_new=$_GET["r_sm_new"]*0.29;
  $r_bg_old=$_GET["r_bg_old"]*0.29;
  $r_bg_new=$_GET["r_bg_new"]*0.29;
  header ("Content-type: image/png");
  if ($r_bg_old>=$r_bg_new)
    $sz=$r_bg_old+25;
  else
    $sz=$r_bg_new+25;
  $im = imagecreatetruecolor($sz,$sz);
  $ink = imagecolorallocate($im, 234, 235, 235);
  imagefilledrectangle ($im,0,0,$sz, $sz,$ink);
  $ink = imagecolorallocate($im, 0, 0, 0);
  imagefilledellipse($im,$sz/2,$sz/2,$r_bg_old,$r_bg_old,$ink);
  $ink = imagecolorallocate($im, 234, 120, 27);
  imageellipse($im,$sz/2,$sz/2,$r_bg_new,$r_bg_new,$ink);
  $ink = imagecolorallocate($im, 125, 125, 125);
  imagefilledellipse($im,$sz/2,$sz/2,$r_sm_old,$r_sm_old,$ink);
  $ink = imagecolorallocate($im, 234, 120, 27);
  imageellipse($im,$sz/2,$sz/2,$r_sm_new,$r_sm_new,$ink);
  imagepng($im);
  imagedestroy($im);
?>
