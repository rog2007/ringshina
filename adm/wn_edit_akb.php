<?php
// функции =====================================================================
// функция генерация <select> ==================================================
  function SelectStr($query,$selname,$id,$fl)
  {
    $res=mysql_query($query);
    $tmpstr='<select id = "' . $selname . '" name="'.$selname . '"' . ($selname == 't3' ? ' onchange="return getModels(\'t3\', \'t4\')"' : '') . '>'.($fl==1?'<option value="0"'.(!$id?'selected="selected"':'').'>Не указан</option>':'');
    while($rs_sel=mysql_fetch_object($res))
      $tmpstr.='<option value="'.$rs_sel->id.'"'.($id==$rs_sel->id?'selected="selected"':'').'>'.$rs_sel->nm.'</option>';
    $tmpstr.='</select>';
    return $tmpstr;
  }
// /функция генерация <select> ==================================================
// /функции ====================================================================
  $str_error='';
  if(isset($_POST["save"])) {

    mysql_query("UPDATE akb_tovar_temp SET id_model = ".$_POST["id_model"] . ", id_volt = " . $_POST["id_volt"] .
      ", id_volume = " . $_POST["id_volume"] . ", rvrt = " . $_POST["rvrt"] . " where id = '" . $arg[0] . "'");
  }

  if(isset($_POST["work"])) {

    mysql_query("update akb_tovar_temp as att LEFT JOIN akb_tovar as at ON at.id_model = att.id_model
        AND at.id_volt = att.id_volt AND att.id_volume = at.id_v
        AND at.rvrt = att.rvrt SET att.id_tov_akb = at.id WHERE at.id IS NOT NULL AND att.id = " . $arg[0]);

    if(mysql_affected_rows() == 0) {

      mysql_query("INSERT INTO akb_tovar (id_model, id_volt, id_v, rvrt, cnt, price)
        (SELECT id_model, id_volt, id_volume, rvrt, akb_count, akb_price
        FROM akb_tovar_temp WHERE id_tov_akb = 0 AND id_model >0
        AND id_volt >0 AND id_volume >0 AND rvrt >0 AND id = " . $arg[0] . ")");

      if(mysql_affected_rows()>0) {

        $big_id=mysql_insert_id();
        $mes .= "<p>Вставил новую позицию в справочник - <b>" . $big_id . "</b></p>";
        mysql_query("UPDATE akb_tovar LEFT JOIN akb_model ON id_model = akb_model.id
        LEFT JOIN akb_volt ON akb_volt.id = id_volt
        LEFT JOIN akb_v ON akb_v.id = id_v
        LEFT JOIN akb_rvrt ON akb_rvrt.id = rvrt
        SET full_name = concat('АКБ ', akb_volt.name, 'В ', akb_v.name, ' А/ч',
          ' ', akb_model.name, ' ', akb_rvrt.short_name)
        WHERE akb_tovar.id = " . $big_id);

        $mes .= "<p>Обновил Полное наименование (" . mysql_affected_rows() . ")</p>";
        mysql_query("update akb_tovar_temp set id_tov_akb = " . $big_id . " WHERE id = '" . $arg[0] . "'");
        $mes .= "<p>Обновил tid (" . mysql_affected_rows() . ")</p>";
      }
    } else {
      $mes .= "<p>Обновил tid по параметрам</p>";
    }

     mysql_query("INSERT INTO akb_suppl (id_tov, id_sup, cnt_sup, prs_sup, prsb_sup, id_tov_sup, suppl_name)
      (SELECT id_tov_akb, akb_tovar_temp.id_sup, max(akb_tovar_temp.akb_count), max(akb_tovar_temp.akb_price), max(akb_tovar_temp.akb_price),
      akb_tovar_temp.id_tov, akb_tovar_temp.full_name
      FROM akb_tovar_temp LEFT JOIN akb_suppl ON akb_suppl.id_sup=akb_tovar_temp.id_sup AND id_tov_akb = akb_suppl.id_tov
      WHERE id_tov_akb > 0 AND akb_suppl.id_tov IS NULL GROUP BY id_tov_akb, akb_tovar_temp.id and akb_tovar_temp.id='".$arg[1]."')");

  }

  $res = mysql_query("select * from akb_tovar_temp where akb_tovar_temp.id = '" . $arg[0] . "'");
  $rs = mysql_fetch_object($res);
  $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
    <tr><td class="title">Наименование</td><td class="control1">'.$rs->full_name.'</td></tr>
    <tr><td class="title">Модель</td><td class="control1">' . SelectStr('select id, name as nm from akb_model order by name', 'id_model',$rs->id_model,1).'</td></tr>
    <tr><td class="title">Вольтаж</td><td class="control1">'.SelectStr('select id, name as nm from akb_volt order by name*1', 'id_volt', $rs->id_volt, 1).'</td></tr>
    <tr><td class="title">Объем</td><td class="control1">' . SelectStr('select id, name as nm from akb_v order by name*1', 'id_volume', $rs->id_volume, 1).'</td></tr>
    <tr><td class="title">Полярность</td><td class="control1">' . SelectStr('select id, name as nm from akb_rvrt order by name', 'rvrt', $rs->rvrt, 1).'</td></tr>
    <tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /><input name="work" type="submit" value="обработать" /></td></tr>
    <tr><td colspan="2">'.$mes.'</td></tr></table></form>';
    //<tr><td class="title">Евро</td><td class="control1"><input type="checkbox" name="rvrt" ' . ($rs->rvrt == 1 ? 'checked="checked"' : '').' /></td></tr>
?>