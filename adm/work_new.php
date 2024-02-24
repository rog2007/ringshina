<?php
  if(!$arg[0]) $pg=1;
  else $pg=$arg[0];
  $fr=($pg-1)*100;
  $str.= "<div style=\"float:left;width:99%\"><h1>Работа с необработанными данными</h1>";
  $res = query("select * from power left join tab3 on t3=tb3_id left join tab4 on t3=brand_id and t4=tb4_id  " .
      "left join tab2 on auto=tb2_id where tid=0 and price_name not like '% мото %' order by t1 desc,brand");
  $t1=-1;
  foreach ($res['data'] as $rs)
  {
    if($rs->t1<>$t1)
    {
      if($t1<>-1) $str.="</table>";
      $t1=$rs->t1;
      if($t1==0)
        $str.= "<h3>Не обработались</h3><table class=\"tovar\"><tr class=\"head\"><td class=\"id\">Ред</td><td class=\"id\">id</td><td>Кол-во</td><td>Название</td></tr>";
      if($t1==1)
        $str.= "<h3>Не обработались шины</h3><table class=\"tovar\"><tr class=\"head\"><td class=\"id\">Ред</td><td class=\"id\">id</td><td>Кол-во</td><td>Название</td><td class=\"brand\">бренд</td><td>модель</td><td>Ширина пр.</td><td>Высота пр.</td><td>диам</td><td>И. Г.</td><td>И. С.</td><td>ZR</td><td>C</td><td>Ом</td><td>RoF</td><td>xl</td></tr>";
      if($t1==2)
        $str.= "<h3>Не обработались диски</h3><table class=\"tovar\"><tr class=\"head\"><td class=\"id\">Ред</td><td class=\"id\">id</td><td>Кол-во</td><td>Название</td><td class=\"brand\">бренд</td><td>модель</td><td>ширина</td><td>диам</td><td>отв</td><td>pcd</td><td>ET</td><td>stup</td><td>цвет</td></tr>";
    }
    if($t1==0)
      $str.= "<tr><td><a href=\"/adm/wn_edit/0/".$rs->id_pow."/\">Ред</a></td><td class=\"id\">".$rs->id."</td><td>".$rs->cnt."</td><td class=\"name\">".$rs->price_name."</td></tr>";
    if($t1==1)
      $str.= "<tr><td><a href=\"/adm/wn_edit/1/".$rs->id_pow."/\">Ред</a></td><td class=\"id\">".$rs->id."</td>
      <td>".$rs->cnt."</td>
      <td class=\"name\">".$rs->price_name."</td>
      <td class=\"brand\">".($rs->t3>0?$rs->tb3_nm:"<span style=\"color:#f00\">".$rs->brand."</span>")."</td>
      <td class=\"model\">".($rs->t4>0?$rs->tb4_nm:"<span style=\"color:#f00\">".$rs->model."</span>")."</td>
      <td class=\"prof\">".($rs->t5>0?$rs->p_w:"<span style=\"color:#f00\">".$rs->p_w."</span>")."</td>
      <td class=\"prof\">".($rs->t71>0?$rs->p_h:"<span style=\"color:#f00\">".$rs->p_h."</span>")."</td>
      <td class=\"diam\">".($rs->t6>0?$rs->diam:"<span style=\"color:#f00\">".$rs->diam."</span>")."</td>
      <td class=\"gruz\">".($rs->t7>0?$rs->gruz:"<span style=\"color:#f00\">".$rs->gruz."</span>")."</td>
      <td class=\"speed\">".($rs->t8>0?$rs->speed:"<span style=\"color:#f00\">".$rs->speed."</span>")."</td>
      <td class=\"speed\">".$rs->zr."</td>
      <td class=\"speed\">".$rs->diam_c."</td>
      <td class=\"speed\">".$rs->om."</td>
      <td class=\"speed\">".$rs->run."</td>
      <td class=\"speed\">".$rs->xl."</td>
      </tr>";
    if($t1==2)
      $str.= "<tr><td><a href=\"/adm/wn_edit/2/".$rs->id_pow."/\">Ред</a></td><td class=\"id\">".$rs->id."</td>
      <td>".$rs->cnt."</td>
      <td class=\"name\">".$rs->price_name."</td>
      <td class=\"brand\">".($rs->t3>0?$rs->brand:"<span style=\"color:#f00\">".$rs->brand."</span>")."</td>
      <td class=\"model\">".($rs->t4>0?$rs->model:"<span style=\"color:#f00\">".$rs->model."</span>")."</td>
      <td class=\"prof\">".($rs->t5>0?$rs->prof:"<span style=\"color:#f00\">".$rs->prof."</span>")."</td>
      <td class=\"diam\">".($rs->t6>0?$rs->diam:"<span style=\"color:#f00\">".$rs->diam."</span>")."</td>
      <td class=\"gruz\">".($rs->t7>0?$rs->gruz:"<span style=\"color:#f00\">".$rs->gruz."</span>")."</td>
      <td class=\"speed\">".($rs->t8>0?$rs->speed:"<span style=\"color:#f00\">".$rs->speed."</span>")."</td>
      <td class=\"speed\">".($rs->t9>0?$rs->ship:"<span style=\"color:#f00\">".$rs->ship."</span>")."</td>
      <td class=\"speed\">".($rs->t71>0?$rs->p_w:"<span style=\"color:#f00\">".$rs->p_w."</span>")."</td>
      <td class=\"speed\">".($rs->t2>0?$rs->tp:($rs->tp=='' && $rs->auto>0?"<span style=\"color:#30F\">".$rs->tb2_nm." (Ор.)</span>":"<span style=\"color:#f00\">".$rs->tp."</span>"))."</td>
      </tr>";
  }
  $str.= "</table></div>";