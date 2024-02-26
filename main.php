<?php
  $title="Шины и диски. Интернет магазин RingShina.";
  $descr="Шины и диски. Интернет магазин RingShina.";
  $keywords="Шины и диски. Интернет магазин RingShina.";

  $res=mysqli_query($mysqli, "select txt from pages where pg='main'");
  if($rs=mysqli_fetch_object($res))
  $content .= $rs->txt;
?>