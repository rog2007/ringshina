<?php

function PagesCreate($link,$all_cn,$crpg)
{
  $strtmp="<div class=\"pages-row\">";
  if($all_cn<8)
  {
    for($i=1;$i<=$all_cn;$i++)
    {
      if ($i==$crpg) $strtmp.="<span>$i</span>";
      else $strtmp.="<a href='".$link."$i.html'>$i</a>";
    }
  }
  if($all_cn>=8)
  {
    if($crpg<4)
    {
      for($i=1;$i<=5;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."$i.html'>$i</a>";
      }
      $strtmp.="<span class=\"dots\">...</span><a href='".$link."$all_cn.html'>$all_cn</a>";
    }
    if($crpg==4)
    {
      for($i=1;$i<=6;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."$i.html'>$i</a>";
      }
      $strtmp.="<span class=\"dots\">...</span><a href='".$link."$all_cn.html'>$all_cn</a>";
    }
    if($crpg>$all_cn-3)
    {
      $strtmp.="<a href='".$link."1.html'>1</a><span class=\"dots\">...</span>";
      for($i=$all_cn-4;$i<=$all_cn;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."$i.html'>$i</a>";
      }
    }
    if($crpg==$all_cn-3)
    {
      $strtmp.="<a href='".$link."1.html'>1</a><span class=\"dots\">...</span>";
      for($i=$all_cn-5;$i<=$all_cn;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."$i.html'>$i</a>";
      }
    }
    if($crpg<$all_cn-3 && $crpg>4)
    {
      $strtmp.="<a href='".$link."1.html'>1</a><span class=\"dots\">...</span>";
      for($i=$crpg-2;$i<=$crpg+2;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."$i.html'>$i</a>";
      }
      $strtmp.="<span class=\"dots\">...</span><a href='".$link."$all_cn.html'>$all_cn</a>";
    }
  }
  $strtmp.="</div>";
  return $strtmp;
}

  $tov=IdByName($arg[0],"tab1","tb1_id","translit");
  $content .= '<div id="bread">
    <a href="/">Главная</a>
    <span>&gt;</span>
    <a href="/catalog/' . $arg[0] . '.html">Специальные предложения - ' . ($tov == 1 ? 'шины' : 'диски') . '</a>
  </div>';

$title="Buy-tyres: специальные предложения ".($tov==1?"шин":"дисков");
$descr="В каталоге представленны специальные предложения ".($tov==1?"шин":"дисков")." со скидкой, которые есть в наличии на нашем складе.";
$keywords=($tov==1?"шин":"дисков")." купить специальные предложения";

$content .= '<div id="params" class="special-price"><div class="head"><h1>Специальные предложения по ' . ($tov == 1 ? 'шинам' : 'дискам') . '</h1></div>';

if(!isset($arg[1])){

  $page = 1;
} else {

  $page = $arg[1];
}
$cnt_pg = 20;
$fenix = 21;

$str_lim='';
if ($cnt_pg>0){

  $first=($page-1)*$cnt_pg;
  $last=$cnt_pg;
  $str_lim=" limit ".$first.", " .$last;

  $selTyre = $dbcon->prepare("SELECT count(*) as cnt FROM total WHERE tab1_id=:type and spid=:wh and cnt>0 and wrk = 1");

  $selTyre->bindParam(':type',$tov);
  $selTyre->bindParam(':wh',$fenix);

  if ($selTyre->execute() && $selTyre->rowCount() > 0) {

    if ($resultObj = $selTyre->fetch(PDO::FETCH_OBJ)) {

      $pagesCount = ceil($resultObj->cnt/20);
    }
  }

  $strZak="";
  if ($pagesCount>1)
  {

    $strZak = PagesCreate('/special/' . $arg[0] .'/', $pagesCount, $page);
  }
}


if($tov == 1){

  $selTyre = $dbcon->prepare("SELECT total_id, total.url as turl,tab2_id,tab1.translit as t1url, tb3_nm, tb4_nm,tovimg,
  concat(profw.name,'/',profh.name) as prof, tb6_nm, tb7_nm, tb8_nm, imgs.imgname as image, price,tab4_id, tab2.translit as t2tr
  FROM total LEFT JOIN tab3 ON tb3_id = tab3_id LEFT JOIN tab4 ON tb4_id = tab4_id LEFT JOIN tab6 ON tb6_id = tab6_id LEFT JOIN tab2 ON tb2_id = tab2_id
  LEFT JOIN tab1 ON tb1_id = tab1_id LEFT JOIN tab7 ON tb7_id = tab7_id LEFT JOIN profw ON w_id = profw.id LEFT JOIN profh ON h_id = profh.id
  LEFT JOIN tab8 ON tb8_id = tab8_id LEFT JOIN imgs ON tab3_id=imgs.idbrand and tab4_id = imgs.idmodel
  WHERE tab1_id=:type and spid=:wh and cnt>0 and wrk = 1  ORDER BY all_name " . $str_lim);


  $selTyre->bindParam(':type',$tov);
  $selTyre->bindParam(':wh',$fenix);

  if ($selTyre->execute() && $selTyre->rowCount() > 0) {

    $i = 0;
    while ($resultObj = $selTyre->fetch(PDO::FETCH_OBJ)) {

      if(trim($resultObj->tovimg)){

        $pic1 = $resultObj->tovimg;
      } else {

        $pic1 = $resultObj->image;
      }
      $pic=ImageWork($pic1, $tov, $resultObj->tab2_id, $resultObj->brand_id,
        $resultObj->tab4_id, $resultObj->t2tr, $resultObj->tb3_nm, $resultObj->tb4_nm, '160');
      if(strpos($pic, 'nofoto')){

        $onclick = '';
        $style = ';cursor:pointer';

      } else {

        $onclick = ' onclick="return ShowZoomWindow(true,\'' . addslashes($resultObj->tb3_nm) . ' ' .
          addslashes($resultObj->tb4_nm) . '\',\'/images/tovar/tyres/' . $pic1 . '\');"';
        $style = '';
      }

      if($i%5 == 0 ) $content.='<div class="blocks-line">';
      $content .= '<div class="tyre">
        <a href="/card/' . '/' . $resultObj->turl .'.html" class="img" style="background:url(' . $pic .
        ') left center no-repeat' . $style . '"' . $onclick . '></a>
        <a href="/card/' . $resultObj->turl .'.html" class="data">
          <span class="sp-brand">' . $resultObj->tb3_nm . '</span>
          <span class="sp-model">' . $resultObj->tb4_nm . '</span>
          <span class="sp-razmer">' . $resultObj->prof . ' ' . $resultObj->tb6_nm . ' ' . $resultObj->tb7_nm . $resultObj->tb8_nm . '</span>
        </a>
        <span class="sp-price">' . $resultObj->price . '</span>
      </div>';
      $i++;
      if($i%5 == 0 ) $content.='</div>';
    }
    if($i%5)  $content.='</div>';
  }
}

if($tov == 2){

  $selTyre = $dbcon->prepare("SELECT total_id, tab2_id, total.url as turl, tab1.translit as t1url,tovimg,
  tb3_nm, tb4_nm, tb5_nm, tb6_nm, tb7_nm, tb8_nm, imgs.imgname as image, price, tab4_id, tab2.translit as t2tr, tb9_nm, tb12_nm, tb2_nm
  FROM total LEFT JOIN tab2 ON tb2_id = tab2_id LEFT JOIN tab3 ON tb3_id = tab3_id LEFT JOIN tab4 ON tb4_id = tab4_id
  LEFT JOIN tab6 ON tb6_id = tab6_id LEFT JOIN tab9 ON tb9_id = tab9_id LEFT JOIN tab12 ON tb12_id = tab12_id
  LEFT JOIN tab1 ON tb1_id = tab1_id LEFT JOIN tab7 ON tb7_id = tab7_id LEFT JOIN tab5 ON tb5_id = tab5_id
  LEFT JOIN tab8 ON tb8_id = tab8_id LEFT JOIN imgs ON tab4_id = imgs.idmodel and tab2_id = idcolor
  WHERE tab1_id=:type and spid=:wh and cnt>0 and wrk = 1   ORDER BY all_name " . $str_lim);


  $selTyre->bindParam(':type',$tov);
  $selTyre->bindParam(':wh',$fenix);

  if ($selTyre->execute() && $selTyre->rowCount() > 0) {

    $i = 0;
    while ($resultObj = $selTyre->fetch(PDO::FETCH_OBJ)) {

      if(trim($resultObj->tovimg)){

        $pic1 = $resultObj->tovimg;
      } else {

        $pic1 = $resultObj->image;
      }
      $pic=ImageWork($pic1, $type, $resultObj->tab2_id, $resultObj->brand_id,
        $resultObj->tab4_id, $resultObj->t2tr, $resultObj->tb3_nm, $resultObj->tb4_nm, '160');
      if(strpos($pic, 'nofoto')){

        $onclick = '';
        $style = ';cursor:pointer';

      } else {

        $onclick = ' onclick="return ShowZoomWindow(true,\'' . addslashes($resultObj->tb3_nm) . ' ' .
          addslashes($resultObj->tb4_nm) . ($resultObj->tb2_nm ? ' ' . addslashes($resultObj->tb2_nm) : '') . '\',\'/images/tovar/discs/' . $pic1 . '\');"';
        $style = '';
      }

      if($i%5 == 0 ) $content.='<div class="blocks-line">';
      $content .= '<div class="tyre">
        <a href="/card/' . $resultObj->turl .'.html" class="img" style="background:url('. $pic .
        ') left center no-repeat' . $style . '"' . $onclick . '></a>
        <a href="/card/' . $resultObj->turl .'.html" class="data">
          <span class="sp-brand">' . $resultObj->tb3_nm . '</span>
          <span class="sp-model">' . $resultObj->tb4_nm . '</span>
          <span class="sp-razmer">' . $resultObj->tb5_nm . '*' . $resultObj->tb6_nm . ' ' . $resultObj->tb7_nm . '/' . $resultObj->tb8_nm .
          ' ET' . $resultObj->tb9_nm . ($resultObj->tb12_nm ? ' D' . $resultObj->tb12_nm : '') . ($resultObj->tb2_nm ? ' ' . $resultObj->tb2_nm : '') . '</span>
        </a>
        <div class="sp-price">' . $resultObj->price . '</div>
      </div>';
      $i++;
      if($i%5 == 0 ) $content.='</div>';
    }
    if($i%5)  $content.='</div>';
  }
}
$content.=$strZak."</div>";
//$content .= '</div>';


?>