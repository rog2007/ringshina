<?php

  if(!isset($arg[1]) || !isset($arg[0])) {

    echo 'Не указан бренд шин или дисков. <a href="' . $_SERVER['HTTP_REFERER'] . '">Вернуться</a>';
  }

  $brandId = $arg[1];
  $tovId = $arg[0];

  $res = mysql_query('SELECT tb3_nm FROM tab3 WHERE tb3_id = ' . $brandId);
  $rsBr = mysql_fetch_object($res);
  $brandName = $rsBr->tb3_nm;

  if($tovId == 1) {

    $res = mysql_query('SELECT tb4_nm, imgname, tb4_id
      FROM total LEFT JOIN tab4 ON tab4_id = tb4_id
      LEFT JOIN `imgs` ON tab4_id = imgs.idmodel AND tab3_id = imgs.idbrand AND imgs.idcolor = 0
      WHERE wrk = 1 AND tab3_id = ' . $brandId . ' GROUP BY tb4_nm, imgname, tb4_id ORDER BY tb4_nm');

  } else {

    $res = mysql_query('SELECT tb4_nm, tb2_nm, imgname, tb4_id
      FROM total LEFT JOIN tab4 ON tab4_id = tb4_id
      LEFT JOIN tab2 ON tab2_id = tb2_id
      LEFT JOIN `imgs` ON tab4_id = imgs.idmodel AND tab3_id = imgs.idbrand  AND tab2_id = imgs.idcolor
      WHERE wrk = 1 AND tab3_id = ' . $brandId . ' GROUP BY tb4_nm, tb2_nm, imgname, tb4_id ORDER BY tb4_nm, tb2_nm');
  }


  $str .= '<h1>Список отсутствующих рисунков - ' . $brandName . '</h1>';

  $str .= '<table class="ed">
    <tr><td></td><td>ID</td><td>Модель</td>' . ($tovId == 2 ? '<td>Цвет</td>' : '') . '<td></td></tr>';

  while($rs = mysql_fetch_object($res)){

    if(!$rs->imgname || !file_exists($_SERVER["DOCUMENT_ROOT"] . '/images/tovar/' . ($tovId == 1 ? 'tyres' : 'discs') . '/' . $rs->imgname)){

      $str .= '<tr><td><a href="/adm/sp-edit/4/' . $rs->tb4_id . '/" target="_blank">Ред</a></td><td>' .
        $rs->tb4_id . '</td><td>' . $rs->tb4_nm . '</td>' .
        ($tovId == 2 ? '<td>' . $rs->tb2_nm . '</td>' : '') .
        '<td>' . ($rs->imgname ?
        '<a href="/images/tovar/' . ($tovId == 1 ? 'tyres' : 'discs') . '/' .
        $rs->imgname . '" target="_blank">/images/tovar/' . ($tovId == 1 ? 'tyres' : 'discs') . '/' . $rs->imgname : '') . '</a>' .
        '</td></tr>';
    }
  }

  $str.='</table>';
?>