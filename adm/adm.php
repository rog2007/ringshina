<?php
$str.="<div class=\"leftmenu\">";
$menu=mysql_query("select id,nm from adm_veiws order by sort");
while($men=mysql_fetch_object($menu))
  $str.="<a href=\"#\" onclick=\"return OpenMenu(".$men->id.")\">".$men->nm."</a>";
$str.="</div><div class=\"rght\" id=\"views\"></div>"; 
?>