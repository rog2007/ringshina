<?php
  if(!$arg[0]){

    $pg=1;
  } else {
    $pg=$arg[0];
  }
  $fr = ($pg-1) * 100;
  $str .= "<div style=\"float:left;width:99%\"><h1>Работа с необработанными данными</h1>";
  $res=mysql_query("SELECT att.id as pid, att.id_tov as pidt, akb_count, full_name, id_brand,
    name_brand, id_model, name_model, id_volt, name_volt, id_volume, name_volume, att.rvrt as attrvrt, akb_rvrt.name as rvrt_name
    FROM akb_tovar_temp AS att LEFT JOIN akb_model on akb_model.id = att.id_model
    LEFT JOIN akb_rvrt on akb_rvrt.id = att.rvrt
    where id_tov_akb = 0 ORDER BY name_model, full_name");

  $str .= "<table class=\"tovar\"><tr class=\"head\"><td class=\"id\">Ред</td><td class=\"id\">id</td><td>Кол-во</td>
    <td>Название</td><td>модель</td><td>Напряжение</td><td>Объем</td><td>Обрат.</td></tr>";
  while($rs=mysql_fetch_object($res)) {

    $str.= "<tr><td><a href=\"/adm/wn_edit_akb/" . $rs->pid . "/\">Ред</a></td><td class=\"id\">" . $rs->pidt . "</td>
      <td>" . $rs->akb_count . "</td><td class=\"name\">" . $rs->full_name . "</td>
      <td class=\"model\">" . ($rs->id_model > 0 ? $rs->name_model : "<span style=\"color:#f00\">" . $rs->name_model . "</span>") . "</td>
      <td class=\"prof\">" . ($rs->id_volt > 0 ? $rs->name_volt : "<span style=\"color:#f00\">" . $rs->name_volt . "</span>") . "</td>
      <td class=\"prof\">" . ($rs->id_volume > 0 ? $rs->name_volume : "<span style=\"color:#f00\">" . $rs->name_volume . "</span>") . "</td>
      <td class=\"speed\">" . $rs->rvrt_name . "</td></tr>";
  }
  $str .= "</table></div>";
?>