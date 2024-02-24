<?php
$title = "RingShina: каталог дисков Replay по автомобилю.";
$descr = "RingShina: каталог дисков Replay по автомобилю.";
$keywords = "RingShina: каталог дисков Replay по автомобилю.";

$content .= '<ul class="breadcrumbs">
  <li><a href="/">Главная</a></li>
  <li><a href="/catalog/diski.html">Каталог дисков</a></li>
  <li class="current"><a href="/catalog/diski.html">Каталог дисков Replay</a></li>
	</ul>';

$content .= '<div id="brands"><div class="head"><h1>Каталог дисков Replay</h1></div>';

$res=mysql_query("select t_auto_id,t_auto_nm,t_auto_pic,rp from t_auto where rp<>'' order by t_auto_nm");
while($brand=mysql_fetch_object($res)){
  
  $link = '/modeli/diski/replica/1/' . urlencode($brand->t_auto_nm) . '.html';
  $content .= '<div class="brand"><a href="'. $link .
  '" class="pic" style="background:url(/images/tovar/cars/'.$brand->t_auto_pic.') no-repeat center center"></a>
  <a href="'.$link.'" class="nm">'.$brand->t_auto_nm.'</a></div>';
}
$content .= "</div>";
?>