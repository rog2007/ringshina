<?php
  function SelectStr($query,$selname,$id,$fl)
  {
    $res=mysql_query($query);
    $tmpstr='<select name="'.$selname.'">'.($fl==1?'<option value="0"'.(!$id?'selected="selected"':'').'>Не указан</option>':'');
    while($rs_sel=mysql_fetch_object($res))
      $tmpstr.='<option value="'.$rs_sel->id.'"'.($id==$rs_sel->id?'selected="selected"':'').'>'.$rs_sel->nm.'</option>';
    $tmpstr.='</select>';
    return $tmpstr;
  }
  $str .= '<table><tr><td style="width:140px;vertical-align:top">
  <h3 class="h3-lmenu">Калькуляторы</h3>
  <a class="a-l-menu" href="/adm/calculators/show/1/">Американские размеры</a>
  <a class="a-l-menu" href="/adm/calculators/show/2/">Ширина диска</a>
</td><td style="vertical-align:top">';

switch ($arg[0]){

  case 'show':
    $str .= showTable($arg[1]);
  break;
  case 'edit':
    $str .= editRow($arg[1], $arg[2]);
  break;
  case 'delete':
    deleteRow($arg[1], $arg[2]);
  break;
}

function deleteRow($type, $id){

  switch($type){

    case 1:

      mysql_query("delete from calc2 where id=" . $id);
    break;
    case 2:

      mysql_query("delete from calc3 where id=" . $id);
    break;
  }
  header('Location: '.$_SERVER['HTTP_REFERER']);
}

function editRow($type, $id){

  switch($type){

    case 1:

      $selSql = 'SELECT * FROM calc2 WHERE id = ';
      $updSql = 'UPDATE calc2 SET w_id = ' . (is_numeric($_POST['w_id']) ? $_POST['w_id'] : 0) . ',
      h_id = ' . (is_numeric($_POST['h_id']) ? $_POST['h_id'] : 0) . ',
      r_id = ' . (is_numeric($_POST['r_id']) ? $_POST['r_id'] : 0) . ',
      euro = \'' . ($_POST['euro'] ? $_POST['euro'] : '') . '\'
      WHERE id = ' . $id;
      $insSql = 'INSERT INTO calc2 (w_id, h_id, r_id, euro)
        VALUES (' . (is_numeric($_POST['w_id']) ? $_POST['w_id'] : 0) . ',
        ' . (is_numeric($_POST['h_id']) ? $_POST['h_id'] : 0) . ',
        ' . (is_numeric($_POST['r_id']) ? $_POST['r_id'] : 0) . ',
        \'' . ($_POST['euro'] ? $_POST['euro'] : '') . '\');';
    break;
    case 2:

      $selSql = 'SELECT * FROM calc3 WHERE id = ';
      $updSql = 'UPDATE calc3 SET w_id = ' . (is_numeric($_POST['w_id']) ? $_POST['w_id'] : 0) . ',
      h_id = ' . (is_numeric($_POST['h_id']) ? $_POST['h_id'] : 0) . ',
      r_id = ' . (is_numeric($_POST['r_id']) ? $_POST['r_id'] : 0) . ',
      wmin = \'' . ($_POST['wmin'] ? $_POST['wmin'] : '') . '\',
      wmax = \'' . ($_POST['wmax'] ? $_POST['wmax'] : '') . '\'
      WHERE id = ' . $id;
      $insSql ='INSERT INTO calc3 (w_id, h_id, r_id, wmin, wmax)
        VALUES (' . (is_numeric($_POST['w_id']) ? $_POST['w_id'] : 0) . ',
        ' . (is_numeric($_POST['h_id']) ? $_POST['h_id'] : 0) . ',
        ' . (is_numeric($_POST['r_id']) ? $_POST['r_id'] : 0) . ',
        \'' . ($_POST['wmin'] ? $_POST['wmin'] : '') . '\',
        \'' . ($_POST['wmax'] ? $_POST['wmax'] : '') . '\');';
    break;
  }
  if(isset($_POST['save'])){

    if($id){

      mysql_query($updSql);
    } else {

      mysql_query($insSql);
      $id = mysql_insert_id();
      header('Location: /adm/calculators/edit/' . $type . '/' . $id . '/');
    }
  }
  if($id){

    $result = mysql_query($selSql . $id);
    $rs = mysql_fetch_object($result);
  }

  $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
    <tr><td class="title">Ширина шины</td><td class="control">' . SelectStr('select id,name as nm from profw order by name * 1','w_id',($rs->w_id ? $rs->w_id : 0),1).'</td></tr>
    <tr><td class="title">Профиль шины</td><td class="control">' . SelectStr('select id,name as nm from profh order by name * 1','h_id',($rs->h_id ? $rs->h_id : 0),1).'</td></tr>
    <tr><td class="title">Диаметр шины</td><td class="control">' . SelectStr('select tb6_id as id,tb6_nm as nm from tab6 where tb6_tov_id = 1 order by tb6_nm','r_id',($rs->r_id ? $rs->r_id : 0),1).'</td></tr>' .
    ($type == 2 ? '<tr><td class="title">Мин. ширина диска</td><td class="control"><input name="wmin" type="text" value="'.($rs->wmin ? $rs->wmin : '').'" /></td></tr>
    <tr><td class="title">Макс. ширина диска</td><td class="control"><input name="wmax" type="text" value="'.($rs->wmax ? $rs->wmax : '').'" /></td></tr>' :
    '<tr><td class="title">Европейский размер</td><td class="control"><input name="euro" type="text" value="'.($rs->euro ? $rs->euro : '').'" /></td></tr>') .
    '<tr><td></td><td class="but"><input name="save" type="submit" value="сохранить" /></td></tr>
  </table></form>';

  return $str;
}

function showTable($type){

  $str .= ' <h1>Калькулятор ' . ($type == 1 ? 'Американские размеры' : 'Ширина диска') . '</h1>';
  $str .= '<div id="orders-result">
  <table>
    <tr class="head">
      <td><a href="/adm/calculators/edit/' . $type . '/0/">Доб</a></td>
      <td class="idtov">ID</td>
      <td>Ширина</td>
      <td>Высота</td>
      <td>Диаметр</td>';
  if($type == 1){

    $str .= '<td>Европейский аналог</td>';
    $sql = "SELECT calc2.id as cid, tb6_nm, profw.name as wname, profh.name as hname, euro
      from calc2 left join profw on calc2.w_id = profw.id
      left join profh on calc2.h_id = profh.id left join tab6 on calc2.r_id = tb6_id
      order by tb6_nm*1, profw.name*1, profh.name*1";
  } else {

    $str .= '<td>Мин. ширина</td><td>Макс. ширина</td>';
    $sql = "SELECT calc3.id as cid, tb6_nm, profw.name as wname, profh.name as hname, wmin, wmax
      from calc3 left join profw on calc3.w_id = profw.id
      left join profh on calc3.h_id = profh.id left join tab6 on calc3.r_id = tb6_id
      order by tb6_nm*1, profw.name*1, profh.name*1";
  }

  $str .= '<td></td></tr>';

  $result=mysql_query($sql);

  while($nom = mysql_fetch_object($result)){

    $str.='<tr class=\"skld\"><td style="width:30px"><a href="/adm/calculators/edit/' . $type . '/' . $nom->cid . '/">Ред</a></td>
    <td>'.$nom->cid.'</td>
    <td>'.$nom->wname.'</td>
    <td>'.$nom->hname.'</td>
    <td>'.$nom->tb6_nm.'</td>';
    if($type == 1){

      $str .= '<td>'.$nom->euro. '</td>';
    } else {

      $str .= '<td>'.$nom->wmin. '</td><td>'.$nom->wmax.'</td>';
    }

    $str .= '<td><a href="/adm/calculators/delete/' . $type . '/' . $nom->cid . '/">Уд</a></td>
    </tr>';
  }

  $str.='</table>';
  $str.="</div>";
  return $str;
}
$str .= '</td></tr></table>';
?>