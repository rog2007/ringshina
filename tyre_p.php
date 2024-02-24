<?php
  $s_old=$_GET["s_old"]*0.29;
  $s_new=$_GET["s_new"]*0.29;
  $r_bg_old=$_GET["r_bg_old"]*0.29;
  $r_bg_new=$_GET["r_bg_new"]*0.29;
  if ($r_bg_old>=$r_bg_new)
    $sz1=$r_bg_old+25;
  else
    $sz1=$r_bg_new+25;
  header ("Content-type: image/png");
  if ($s_old>=$s_new)
    $sz2=$s_old+5;
  else
    $sz2=$s_new+5;
  $im = imagecreatetruecolor($sz2,$sz1);
  $ink = imagecolorallocate($im, 234, 235, 235);
  imagefilledrectangle ($im,0,0,$sz2, $sz1,$ink);
  $ink = imagecolorallocate($im, 0, 0, 0);
  imagefilledrectangle($im,($sz2-$s_old)/2,($sz1-$r_bg_old)/2,$s_old+($sz2-$s_old)/2,$r_bg_old+($sz1-$r_bg_old)/2,$ink);
  $ink = imagecolorallocate($im, 234, 120, 27);
  imagerectangle($im,($sz2-$s_new)/2,($sz1-$r_bg_new)/2,$s_new+($sz2-$s_new)/2,$r_bg_new+($sz1-$r_bg_new)/2,$ink);
  imagepng($im);
  imagedestroy($im);
?>