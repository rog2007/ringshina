<?php
if (isset($group) && $group > 4) {
    Header("Location: /error/dostup/");
    exit;
}
$str = '<div class="block" style="width:400px"><h2>Новая загрузка прайсов</h2>' .
    '<form enctype="multipart/form-data" action="/adm/loadnew/" method="post">' .
    '<select name="load_id"><option value="0">укажите прайс</option>';
$res = query('SELECT id, `name` FROM parser WHERE vis=1 ORDER BY `name`');
if($res['result']) {
    foreach ($res['data'] as $rs) {
        $str .= "<option value=\"" . $rs->id . "\">" . $rs->name . "</option>";
    }
}
$str .= '</select>
  от <input type="text" name="idfrom" value="0"  style="width:50px"/>
  до <input type="text" name="idto" value="0" style="width:50px" />
  <input type="file" name="xls"><input type="submit" name="load_pnew" value="Загрузить">
  <input type="submit" name="obrsp" value="Обработать">
  <input type="submit" name="obrspakb" value="Обработать АКБ">
  <input type="submit" name="obnul" value="Обнулить"></form></div>';