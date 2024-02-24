<?php
if (isset($_COOKIE['cook_tmp']))
{ $uid = $_COOKIE['cook_tmp']; }
else {
srand(time());
$uid = md5(uniqid(""));
SetCookie("cook_tmp",$uid,time()+604800, "/"); }
?>