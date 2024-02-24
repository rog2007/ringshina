<?php
  $title="Шины и диски. Интернет магазин RingShina.";
  $descr="Шины и диски. Интернет магазин RingShina.";
  $keywords="Шины и диски. Интернет магазин RingShina.";

  $res=mysql_query("select txt from pages where pg='main'");
  if($rs=mysql_fetch_object($res))
  $content .= $rs->txt;
?>