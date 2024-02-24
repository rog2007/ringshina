<?php
$tov = IdByName($arg[0],"tab1","tb1_id","translit");

switch($tov) {

  case 1:
    $title = "RingShina: каталог шин";
    $descr = "RingShina: каталог шин";
    $keywords = "RingShina: каталог шин";
    $h1 = "RingShina: каталог шин";
    $h1Dop = '<a href="/param/shini/" class="button" title="">Перейти к подбору автошин</a>';
    $sql = "select tb3_id,tb3_nm,tb3_pic, url from tab3 where tb3_tov_id=" . $tov ." and wrk3=1 order by tb3_nm";
    $tovName = "шины";
  break;
  case 2:
    $title = "RingShina: каталог дисков";
    $descr = "RingShina: каталог дисков";
    $keywords = "RingShina: каталог дисков";
    $h1 = "RingShina: каталог дисков";
    $h1Dop = '<a href="/param/diski/" class="button" title="">Перейти к подбору автодисков</a>';
    $sql = "select tb3_id,tb3_nm,tb3_pic, url from tab3 where tb3_tov_id=" . $tov ." and wrk3=1 order by tb3_nm";
    $tovName = "диски";
  break;
  case 3:
    $title = "RingShina: каталог АКБ";
    $descr = "RingShina: каталог АКБ";
    $keywords = "RingShina: каталог АКБ";
    $h1 = "RingShina: каталог АКБ";
    $h1Dop = '<a href="/param/akb/" class="button" title="">Перейти к подбору АКБ</a>';
    $sql = "select id as tb3_id, name as tb3_nm, pic as tb3_pic, url from akb_brand where vis = 1 order by name";
    $tovName = "АКБ";
  break;
}

$content .= '<ul class="breadcrumbs">
  <li><a href="/">Главная</a></li>
  <li class="current"><a href="/catalog/' . $arg[0] . '.html">' . $h1 . '</a></li>
	</ul>';

$content .= '<div id="brands"><div class="head"><h1>' . $h1 . '</h1>' . $h1Dop . '</div>';
$res = mysql_query($sql);
while($brand = mysql_fetch_object($res)){

  if($tov == 2 && $brand->tb3_id == 297) continue;

  $link = ($tov == 2 && $brand->tb3_id==50 ? '/replay.html' : '/modeli/'.$arg[0].'/'.$brand->url.'/1.html');
  $content .= '<div class="brand"><a href="'. $link .
  '" class="pic" style="background:url(' . $imgLinkPrefix . '/images/tovar/brands/' . $brand->tb3_pic . ') no-repeat center center"></a>
  <a href="'.$link.'" class="nm">' . $tovName . " " . $brand->tb3_nm.'</a></div>';
}
$content .= "</div>";