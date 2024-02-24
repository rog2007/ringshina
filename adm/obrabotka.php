<?php
  $str.= "<h1>Работа с необработанными данными</h1>";
  if(!$arg[0]) $pg=1;
  else $pg=$arg[0];
  $fr=($pg-1)*100;
  $res=mysql_query("select * from power where tid=0 and brand not like '%Мото%'  order by brand limit 0,700");
  //echo "select * from power where tid=0 limit $fr 100";
  $str.= "<table class=\"tovar\"><tr class=\"head\"><td class=\"id\">id</td><td class=\"brand\">brand</td><td>t3</td><td>model</td><td>model1</td><td>t4</td><td>prof</td><td>t5</td><td>diam</td><td>t6</td><td>gruz</td><td>t7</td><td>speed</td><td>t8</td><td>vil</td><td>t9</td><td>stup</td><td>t12</td><td>Название</td></tr>";
  while($rs=mysql_fetch_object($res))
    $str.= "<tr><td class=\"id\">".$rs->id."</td><td class=\"brand\">".$rs->brand."</td><td class=\"t3\">".($rs->t3>0?$rs->t3:"<input type=\"button\" value=\"O\" />")."</td><td class=\"model\">".$rs->model."</td><td class=\"model\">".$rs->model1."</td>
    <td class=\"t4\">".($rs->t4>0?$rs->t4:"<input type=\"button\" value=\"O\" />")."</td><td class=\"prof\">".$rs->prof."</td><td class=\"t5\">".($rs->t5>0?$rs->t5:"<input type=\"button\" value=\"O\" />")."</td><td class=\"diam\">".$rs->diam."</td>
    <td class=\"t6\">".($rs->t6>0?$rs->t6:"<input type=\"button\" value=\"O\" />")."</td><td class=\"gruz\">".$rs->gruz."</td><td class=\"t7\">".($rs->t7>0?$rs->t7:"<input type=\"button\" value=\"O\" />")."</td><td class=\"speed\">".$rs->speed."</td>
    <td class=\"t8\">".($rs->t8>0?$rs->t8:"<input type=\"button\" value=\"O\" />")."</td><td class=\"speed\">".$rs->ship."</td><td class=\"t8\">".($rs->t9>0?$rs->t9:"<input type=\"button\" value=\"O\" />")."</td>
    <td class=\"speed\">".$rs->p_w."</td><td class=\"t8\">".($rs->t71>0?$rs->t71:"<input type=\"button\" value=\"O\" />")."</td><td class=\"name\">".$rs->price_name."</td></tr>";
  $str.= "</table>";
//
//
//  $res=mysql_query("select distinct(brand) as brnd from power where t3=0");
//  if(mysql_num_rows($res)>0)
//  {
//    $str.="<h2>Обработка брендов</h2>";
//    $res1=mysql_query("select tb3_id,tb3_nm from tab3 where tb3_tov_id=1 order by tb3_nm");
//    $strs="<select><option value=\"0\">новая запись</option>";
//    while($rs1=mysql_fetch_object($res1))
//      $strs.="<option value=\"".$rs1->tb3_id."\">".$rs1->tb3_nm."</option>";
//    $strs.="</select>";
//    $str.= "<div class=\"doptbl\"><table class=\"brand\"><tr><td>id</td><td>Бренд</td><td>Синоним</td><td>Обработать</td></tr>";
//    while($rs=mysql_fetch_object($res))
//      $str.= "<tr><td>0</td><td>".$rs->brnd."</td><td>".$strs."</td><td><input type=\"button\" onClick=\"return PostNewItem(this,3)\" value=\"О\"></td></tr>";
//    $str.= "</table></div>";
//  }
//  $res=mysql_query("select distinct(model) as brnd from power where t4=0");
//  if(mysql_num_rows($res)>0)
//  {
//    $res1=mysql_query("select tb4_id,tb4_nm from tab4 where tb4_tov_id=1 order by tb4_nm");
//    $strs="<select><option value=\"0\">новая запись</option>";
//    while($rs1=mysql_fetch_object($res1))
//      $strs.="<option value=\"".$rs1->tb4_id."\">".$rs1->tb4_nm."</option>";
//    $strs.="</select>";
//    $str.= "<div class=\"doptbl\"><table class=\"brand\"><tr><td>id</td><td>Модель</td><td>Синоним</td><td>Обработать</td></tr>";
//    while($rs=mysql_fetch_object($res))
//      $str.= "<tr><td class=\"id\">0</td><td class=\"syn\">".$rs->brnd."</td><td class=\"name\">".$strs."</td><td class=\"but\"><input type=\"button\" onClick=\"return PostNewItem(this,4)\" value=\"О\"></td></tr>";
//    $str.= "</table></div>";
//  }
//  $res=mysql_query("select * from power where tid=0");
//  $strres.= "<table class=\"tovar\"><tr><td>id</td><td>brand</td><td>t3</td><td>model</td><td>t4</td><td>prof</td><td>t5</td><td>diam</td><td>t6</td><td>gruz</td><td>t7</td><td>speed</td><td>t8</td><td>Название</td></tr>";
//  while($rs=mysql_fetch_object($res))
//    $strres.= "<tr><td class=\"id\">".$rs->id."</td><td class=\"brand\">".$rs->brand."</td><td class=\"t3\">".$rs->t3."</td><td class=\"model\">".$rs->model."</td>
//    <td class=\"t4\">".$rs->t4."</td><td class=\"prof\">".$rs->prof."</td><td class=\"t5\">".$rs->t5."</td><td class=\"diam\">".$rs->diam."</td>
//    <td class=\"t6\">".$rs->t6."</td><td class=\"gruz\">".$rs->gruz."</td><td class=\"t7\">".$rs->t7."</td><td class=\"speed\">".$rs->speed."</td>
//    <td class=\"t8\">".$rs->t8."</td><td class=\"name\">".$rs->price_name."</td></tr>";
//  $strres.= "</table>";


?>