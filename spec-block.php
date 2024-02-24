<?php

  $content .= '<div class="zag"><h2>Спецпредложения</h2></div>';
  $selTyre = $dbcon->prepare("SELECT total_id, total.url as turl,tab2_id,tab1.translit as t1url, tb3_nm, tb4_nm, tb2_nm,tovimg,
  concat(profw.name,'/',profh.name) as prof, tb6_nm, tb7_nm, tb8_nm, imgs.imgname as image, price,tab4_id, tab2.translit as t2tr
  FROM total LEFT JOIN tab3 ON tb3_id = tab3_id LEFT JOIN tab4 ON tb4_id = tab4_id LEFT JOIN tab6 ON tb6_id = tab6_id LEFT JOIN tab2 ON tb2_id = tab2_id
  LEFT JOIN tab1 ON tb1_id = tab1_id LEFT JOIN tab7 ON tb7_id = tab7_id LEFT JOIN profw ON w_id = profw.id LEFT JOIN profh ON h_id = profh.id
  LEFT JOIN tab8 ON tb8_id = tab8_id LEFT JOIN imgs ON tab4_id = imgs.idmodel WHERE tab1_id=:type and spid=:wh and cnt>0 and wrk = 1   ORDER BY RAND() limit 5");
  $type = 1;
  $fenix = 21;
  $selTyre->bindParam(':type',$type);
  $selTyre->bindParam(':wh',$fenix);
  if ($selTyre->execute() && $selTyre->rowCount() > 0) {
    $content .= '<div class="special-price">
    <div class="zag1"><h3>Шины</h3><a href="/special/shini/" class="all_spec">все спец. предложения по шинам</a></div>
    <div class="blocks-line">';
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

        $onclick = 'onclick="return ShowZoomWindow(true,\'' . addslashes($resultObj->tb3_nm) . ' ' .
          addslashes($resultObj->tb4_nm) . '\',\'/images/tovar/tyres/' . $pic1 . '\');"';
        $style = '';
      }
      $content .= '<div class="tyre">
        <a href="/card/'. $resultObj->turl .'.html" class="img" style="background:url(' . $pic .
        ') left center no-repeat' . $style . '"' . $onclick . '></a>
        <a href="/card/' . $resultObj->turl .'.html" class="data">
          <span class="sp-brand">' . $resultObj->tb3_nm . '</span>
          <span class="sp-model">' . $resultObj->tb4_nm . '</span>
          <span class="sp-razmer">' . $resultObj->prof . ' ' . $resultObj->tb6_nm . ' ' . $resultObj->tb7_nm . $resultObj->tb8_nm . '</span>
        </a>
        <span class="sp-price">' . $resultObj->price . '</span>
      </div>';
    }
    $content .= '</div></div>';
  }

  $selTyre = $dbcon->prepare("SELECT total_id, tab2_id, total.url as turl, tab1.translit as t1url, tb2_nm,tovimg,
  tb3_nm, tb4_nm, tb5_nm, tb6_nm, tb7_nm, tb8_nm, imgs.imgname as image, price, tab4_id, tab2.translit as t2tr, tb9_nm, tb12_nm
  FROM total LEFT JOIN tab2 ON tb2_id = tab2_id LEFT JOIN tab3 ON tb3_id = tab3_id
  LEFT JOIN tab4 ON tb4_id = tab4_id LEFT JOIN tab6 ON tb6_id = tab6_id LEFT JOIN tab9 ON tb9_id = tab9_id
  LEFT JOIN tab12 ON tb12_id = tab12_id
  LEFT JOIN tab1 ON tb1_id = tab1_id LEFT JOIN tab7 ON tb7_id = tab7_id LEFT JOIN tab5 ON tb5_id = tab5_id
  LEFT JOIN tab8 ON tb8_id = tab8_id LEFT JOIN imgs ON tab4_id = imgs.idmodel and tab2_id = idcolor
  WHERE tab1_id=:type and spid=:wh and cnt>0 and wrk = 1  ORDER BY RAND() limit 5");
  $type = 2;
  $fenix = 21;
  $selTyre->bindParam(':type',$type);
  $selTyre->bindParam(':wh',$fenix);
  if ($selTyre->execute() && $selTyre->rowCount() > 0) {
    $content .= '<div class="special-price">
    <div class="zag1"><h3>Диски</h3><a href="/special/diski/" class="all_spec">все спец. предложения по дискам</a></div>
    <div class="blocks-line">';
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

        $onclick = 'onclick="return ShowZoomWindow(true,\'' . addslashes($resultObj->tb3_nm) . ' ' .
          addslashes($resultObj->tb4_nm) . ($resultObj->tb2_nm ? ' ' . addslashes($resultObj->tb2_nm) : '') . '\',\'/images/tovar/discs/' . $pic1 . '\');"';
        $style = '';
      }

      $content .= '<div class="tyre">
        <a href="/card/'. $resultObj->turl .'.html"' . $onclick . ' class="img" style="background:url('. $pic .
        ') left center no-repeat' . $style . '"></a>
        <a href="/card/' . $resultObj->turl .'.html" class="data">
          <span class="sp-brand">' . $resultObj->tb3_nm . '</span>
          <span class="sp-model">' . $resultObj->tb4_nm . '</span>
          <span class="sp-razmer">' . $resultObj->tb5_nm . '*' . $resultObj->tb6_nm . ' ' . $resultObj->tb7_nm . '/' . $resultObj->tb8_nm .
          ' ET' . $resultObj->tb9_nm . ($resultObj->tb12_nm ? ' D' . $resultObj->tb12_nm : '') . ($resultObj->tb2_nm ? ' ' . $resultObj->tb2_nm : '') . '</span>
        </a>
        <div class="sp-price">' . $resultObj->price . '</div>
      </div>';
    }
    $content .= '</div></div>';
  }

?>