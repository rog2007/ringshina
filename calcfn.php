<?php
  header("Content-type: text/plain; charset=utf-8");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  require ("connect.php");

  if($_POST['clc'] == 3){

    $res=mysql_query("select wmin, wmax from calc3 where w_id = ".$_POST["wid"]."
      and h_id = ".$_POST["hid"]." and r_id = ".$_POST["rid"]);
    $rs = mysql_fetch_object($res);
    echo $rs->wmin . ' | ' . $rs->wmax;
  }
  if($_POST['clc'] == 2){

    $res=mysql_query("select euro from calc2 where w_id = ".$_POST["wid"]."
      and h_id = ".$_POST["hid"]." and r_id = ".$_POST["rid"]);
    $rs = mysql_fetch_object($res);
    echo $rs->euro;
  }

?>