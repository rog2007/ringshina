<?php
  if(isset($_POST["add_br"])) // добавление бренда шины/диска
  {
    $err=0;
    if(!isset($_POST["br_name"]) || trim($_POST["br_name"])=='') { $str.= '<p class="error">Не указано название бренда</p>'; $err++;}
    if(!isset($_POST["tovid"]) || $_POST["tovid"]==0) { $str.= '<p class="error">Не указан тип товара для данного бренда</p>'; $err++;}
    if(!$err) mysql_query("insert into tab3 (tb3_nm,tb3_tov_id) values ('".$_POST["br_name"]."',".$_POST["tovid"].")");
  }
  if(isset($_POST["add_br_sin"])) // добавление синонима для бренда шины/диска
  {
    $err=0;
    if(!isset($_POST["br_sin"]) || trim($_POST["br_sin"])=='') { $str.= '<p class="error">Не указан синоним</p>'; $err++;}
    if(!isset($_POST["br_id"]) || $_POST["br_id"]==0) { $str.= '<p class="error">Не указан бренд</p>'; $err++;}
    if(!$err) mysql_query("update tab3 set alt=if(alt='','".$_POST["br_sin"]."',concat(alt,'[|]','".$_POST["br_sin"]."')) where tb3_id=".$_POST["br_id"]);
  }
   $str.= '<div class="block" style="border:1px solid #000;width:200px;float:left"><form enctype="multipart/form-data" action="" method="post">
  <table><tr><td>Добавить бренд</td></tr><tr><td><input name="br_name" value="" /></td></tr>
  <tr><td><select name="tovid"><option value="0">не указан</option><option value="1">шины</option><option value="2">диски</option></select></td></tr>
  <tr><td><input type="submit" name="add_br" value="Добавить"></td></tr></table></form></div>
  <div class="block" style="border:1px solid #000;width:200px;float:left"><form enctype="multipart/form-data" action="" method="post">
  <table><tr><td>Добавить синоним для бренда</td></tr><tr><td><input name="br_sin" value="" /></td></tr>
  <tr><td><select name="br_id"><option value="0">не указан</option>';
  $res=mysql_query("select tb3_id,tb3_nm from tab3 order by tb3_nm");
  while($rs=mysql_fetch_object($res)) $str.="<option value=\"".$rs->tb3_id."\">".$rs->tb3_nm."</option>";
  $str.= '</select></td></tr>
  <tr><td><input type="submit" name="add_br_sin" value="Добавить"></td></tr></table></form></div>';
  $str.= "<h1>Работа с необработанными данными</h1>";
  if(!$arg[0]) $pg=1;
  else $pg=$arg[0];
  $fr=($pg-1)*100;
  $brsel1="<select name=\"selbr\"><option value=\"0\">не указан</option>";
  $res=mysql_query("select * from tab3 where tb3_tov_id=1");
  while($rs=mysql_fetch_object($res)) $brsel1="<option value=\"".$rs->tb3_id."\">".$rs->tb3_nm."</option>";
  $brsel1="</select>";
  $brsel2="<select name=\"selbr\"><option value=\"0\">не указан</option>";
  $res=mysql_query("select * from tab3 where tb3_tov_id=2");
  while($rs=mysql_fetch_object($res)) $brsel2="<option value=\"".$rs->tb3_id."\">".$rs->tb3_nm."</option>";
  $brsel2="</select>";
  $str.= "<table class=\"tovar\"><tr class=\"head\"><td class=\"id\">Ред</td><td class=\"id\">id</td><td>Название</td><td class=\"brand\">бренд</td><td>модель</td><td>ширина</td><td>диам</td><td>отв</td><td>pcd</td><td>ET</td><td>stup</td><td>цвет</td></tr>";
  $res=mysql_query("select * from power where tid=0 and brand<>'' order by brand");
  while($rs=mysql_fetch_object($res))
    $str.= "<tr><td><a href=\"/wn_edit/".$rs->id."/\">Ред</a></td><td class=\"id\">".$rs->id."</td><td class=\"name\">".$rs->price_name."</td>
    <td class=\"brand\">".($rs->t3>0?$rs->brand:"<span style=\"color:#f00\">".$rs->brand."</span>")."</td>
    <td class=\"model\">".($rs->t4>0?$rs->model:"<span style=\"color:#f00\">".$rs->model."</span>")."</td>
    <td class=\"prof\">".($rs->t5>0?$rs->prof:"<span style=\"color:#f00\">".$rs->prof."</span>")."</td>
    <td class=\"diam\">".($rs->t6>0?$rs->diam:"<span style=\"color:#f00\">".$rs->diam."</span>")."</td>
    <td class=\"gruz\">".($rs->t7>0?$rs->gruz:"<span style=\"color:#f00\">".$rs->gruz."</span>")."</td>
    <td class=\"speed\">".($rs->t8>0?$rs->speed:"<span style=\"color:#f00\">".$rs->speed."</span>")."</td>
    <td class=\"speed\">".($rs->t9>0?$rs->ship:"<span style=\"color:#f00\">".$rs->ship."</span>")."</td>
    <td class=\"speed\">".($rs->t71>0?$rs->p_w:"<a href=\"/worknew/add/12/".urlencode($rs->p_w)."/\" style=\"color:#f00\">".$rs->p_w."</a>")."</td>
    <td class=\"speed\">".($rs->t2>0?$rs->tp:"<span style=\"color:#f00\">".$rs->tp."</span>")."</td>
    </tr>";
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