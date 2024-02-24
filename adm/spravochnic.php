<?php

// функция генерация <select> ==================================================
function SelectStr($query, $selname, $id, $fl) {
    $res = mysql_query($query);
    $tmpstr = '<select name="' . $selname . '">' . ($fl == 1 ? '<option value="0"' . (!$id ? 'selected="selected"' : '') . '>Не указан</option>' : '');
    while ($rs_sel = mysql_fetch_object($res))
        $tmpstr .= '<option value="' . $rs_sel->id . '"' . ($id == $rs_sel->id ? 'selected="selected"' : '') . '>' . $rs_sel->nm . '</option>';
    $tmpstr .= '</select>';
    return $tmpstr;
}

// /функция генерация <select> ==================================================
$dop[5][1]['h1'] = 'профилей';
$dop[6][1]['h1'] = 'диаметров';
$dop[7][1]['select'] = ',tb7_gruz ';
$dop[7][1]['tblhd'] = '<td>Значение, кг</td>';
$dop[7][1]['tblhdadd'] = '<td></td>';
$dop[7][1]['tblval'] = '<td>{$rs->tb7_gruz}</td>';
$dop[7][1]['h1'] = 'индексов грузоподъемности';
$dop[8][1]['select'] = ',tb8_speed ';
$dop[8][1]['tblhd'] = '<td>Значение, км/ч</td>';
$dop[8][1]['tblhdadd'] = '<td></td>';
$dop[8][1]['tblval'] = '<td>{$rs->tb8_speed}</td>';
$dop[8][1]['h1'] = 'индексов скорости';

$str .= '<table><tr><td style="width:140px;vertical-align:top">';
include_once("lmenu.php");
$str .= '</td><td style="vertical-align:top">';
$str_error = '';
if (isset($arg[1]) && trim($arg[1]) != '')
    $str_error = '<div class="error"><p>Ошибка</p>' . ($arg[1] == 'er1' ? '<p>Не указано название</p>' : '') . ($arg[1] == 'er2' ? '<p>Запись уже существует</p>' : '') . '</div>';
$argex = explode("-", $arg[0]);
switch ($argex[0]) {
    case 2:
        $res = mysql_query('select tb2_id,tb2_nm,tb2_sn,tb2_pic,translit,tb3_nm,tb2_desc,tb2_tov_id,dopinfo,tab2.alt from tab2
      left join tab3 on tb3_id=brid where tb2_tov_id=' . $argex[1] . ' order by tb3_nm,tb2_nm');
        $str .= '<h1>Справочник ' . ($argex[1] == 2 ? 'цветов дисков' : 'типов авто') . '</h1>' . $str_error . '<table class="ed">
      <tr><td></td><td>ID</td><td>Наименование</td>' . ($argex[1] == 2 ? '<td>Бренд</td>
      <td>Сокращение</td><td>Reg</td><td>Альт. Наз</td>' : '') . '<td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/2/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td>
      <td></td><td><input name="nm" type="text" value="" /></td>
      <td> ' . ($argex[1] == 2 ? SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id=' . $argex[1] . ' order by tb3_nm', 'tb3_id', 0, 0) : '') . '<input name="tov" type="hidden" value="' . $argex[1] . '" /></td>
      ' . ($argex[1] == 2 ? '<td></td><td></td><td></td><td></td>' : '') . '</tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/2/' . $rs->tb2_id . '/">Ред</a></td><td>' . $rs->tb2_id . '</td><td>' . $rs->tb2_nm . '</td>
        ' . ($argex[1] == 2 ? '<td>' . $rs->tb3_nm . '</td><td>' . $rs->translit . '</td>
        <td>' . $rs->tb2_sn . '</td><td>' . $rs->alt . '</td>' : '') . '<td><a href="/adm/sp-edit/2/' . $rs->tb2_id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 3:
        $res = mysql_query('select tb3_id,tb3_nm,tb10_nm,alt,no_load from tab3 left join tab10 on tb10_id=dtp where tb3_tov_id=' . $argex[1] . ' order by tb3_nm');
        $str .= '<h1>Справочник производителей ' . ($argex[1] == 1 ? 'шин' : 'дисков') . '</h1>' . $str_error .
                '<table class="ed"><tr><td></td><td>ID</td><td>Наименование</td>' . ($argex[1] == 1 ? '' : '<td>Тип диска</td>') . '<td>Альт. Наз</td><td>Не грузить</td><td></td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/3/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
      <td></td><td><input name="nm" type="text" value="" /></td><td><input name="tov" type="hidden" value="' . $argex[1] . '" /></td>' . ($argex[1] == 1 ? '' : '<td></td>') . '<td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/3/' . $rs->tb3_id . '/">Ред</a></td><td>' . $rs->tb3_id . '</td><td>' . $rs->tb3_nm . '</td>' .
                    ($argex[1] == 1 ? '' : '<td>' . $rs->tb10_nm . '</td>') . '<td>' . $rs->alt . '</td><td>' . ($rs->no_load == 1 ? 'да' : '') .
                    '</td><td><a href="/adm/sp-edit/3/' . $rs->tb3_id . '/del/">Уд</a></td>
          <td><a href="/adm/noimage/' . $argex[1] . '/' . $rs->tb3_id . '/" target="_blank">Изображения</a></td></tr>';
        $str .= '</table>';
        break;
    case 4:
        $res = mysql_query('select tb4_id,tb4_nm,tb4_nm1,tb9_nm,tb10_nm,tb3_nm,tb2_nm,alern,t4c,tab4.url as t4url, wrk4 from tab4
      left join tab10 on tb10_id=t4ses left join tab9 on tb9_id=t4sh left join tab3 on tb3_id=brand_id left join tab2 on tb2_id=auto
      where tb3_tov_id=' . $argex[1] . ' order by tb3_nm,tb4_nm');
        $str .= '<h1>Справочник моделей ' . ($argex[1] == 1 ? 'шин' : 'дисков') . '</h1>' . $str_error . '<table class="ed">
      <tr><td></td><td>ID</td><td>Наименование</td><td>Бренд</td>
      <td>' . ($argex[1] == 1 ? 'Сезон' : 'Тип') . '</td>' . ($argex[1] == 1 ? '<td>Шип</td>' : '') . '<td>' . ($argex[1] == 1 ? 'Авто' : 'Цвет') . '</td>
      ' . ($argex[1] == 1 ? '<td>C</td>' : '') . '<td>URL</td><td>в работе</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/4/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
      <td></td><td><input name="nm" type="text" value="" /></td>
      <td>' . SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id=' . $argex[1] . ' order by tb3_nm', 'tb3_id', 0, 0) . '<input name="tov" type="hidden" value="' . $argex[1] . '" /></td>
      <td></td>' . ($argex[1] == 1 ? '<td></td>' : '') . '<td></td>' . ($argex[1] == 1 ? '<td></td>' : '') . '<td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/4/' . $rs->tb4_id . '/">Ред</a></td><td>' . $rs->tb4_id . '</td><td>' . $rs->tb4_nm . '</td>
        <td>' . $rs->tb3_nm . '</td><td>' . $rs->tb10_nm . '</td>' . ($argex[1] == 1 ? '<td>' . $rs->tb9_nm . '</td>' : '') . '
        <td>' . $rs->tb2_nm . '</td>' . ($argex[1] == 1 ? '<td>' . $rs->t4c . '</td>' : '') . '<td>' . $rs->t4url . '</td><td>' . $rs->wrk4 . '</td><td><a href="/adm/sp-edit/4/' . $rs->tb4_id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 5: case 7: case 8: case 9: case 10: case 12:
        $res = mysql_query('select tb' . $argex[0] . '_id as id,tb' . $argex[0] . '_nm as nm,tb' . $argex[0] . '_tov_id' . $dop[$argex[0]][$argex[1]]['select'] . ' from tab' . $argex[0] . ' where tb' . $argex[0] . '_tov_id=' . $argex[1] . ' order by (tb' . $argex[0] . '_nm*1)');
        //echo 'select tb'.$argex[0].'_id as id,tb'.$argex[0].'_nm as nm,tb'.$argex[0].'_tov_id'.$dop[$argex[0]][$argex[1]]['select'].' from tab'.$argex[0].' where tb'.$argex[0].'_tov_id='.$argex[1].' order by (tb'.$argex[0].'_nm*1)';
        $str .= '<h1>Справочник ' . $dop[$argex[0]][$argex[1]]['h1'] . ' ' . ($argex[1] == 1 ? 'шин' : 'дисков') . '</h1>' . $str_error . '<table class="ed"><tr><td></td><td>ID</td><td>Наименование</td>' . $dop[$argex[0]][$argex[1]]['tblhd'] . '<td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/' . $argex[0] . '/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
      <td></td><td><input name="nm" type="text" value="" /></td>' . $dop[$argex[0]][$argex[1]]['tblhdadd'] . '<td><input name="tov" type="hidden" value="' . $argex[1] . '" /></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/' . $argex[0] . '/' . $rs->id . '/">Ред</a></td><td>' . $rs->id . '</td><td>' . $rs->nm . '</td>' . eval("return \"" . $dop[$argex[0]][$argex[1]]['tblval'] . "\";") . '<td><a href="/adm/sp-edit/' . $argex[0] . '/' . $rs->id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 6:
        $res = mysql_query('select tb6_id as id,tb6_nm as nm,tb6_tov_id,no_load from tab6 where tb6_tov_id=' . $argex[1] . ' order by (tb6_nm*1)');
        $str .= '<h1>Справочник диаметров ' . ($argex[1] == 1 ? 'шин' : 'дисков') . '</h1>' . $str_error . '<table class="ed"><tr><td></td><td>ID</td><td>Наименование</td><td>Не грузить</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/6/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
      <td></td><td><input name="nm" type="text" value="" /></td><td></td><td><input name="tov" type="hidden" value="' . $argex[1] . '" /></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/6/' . $rs->id . '/">Ред</a></td><td>' . $rs->id . '</td><td>' . $rs->nm . '</td><td>' . ($rs->no_load == 1 ? 'да' : '') . '</td><td><a href="/adm/sp-edit/6/' . $rs->id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 13:
        $res = mysql_query('select id,name from shlak order by name');
        $str .= '<table class="ed"><tr><td></td><td>ID</td><td>Наименование</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/13/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td><td></td><td><input name="name" type="text" value="" /></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/13/' . $rs->id . '/">Ред</a></td><td>' . $rs->id . '</td><td>' . $rs->name . '</td><td><a href="/adm/sp-edit/13/' . $rs->id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 14:
        $res = mysql_query('select disc_id,suppl.name as snm,tb3_nm,tb10_nm,disc_per from discount left join tab3 on tb3_id=disc_id_brnd  left join tab10 on tb10_id=disc_id_ses left join suppl on suppl.id=disc_id_sup order by suppl.name, tb3_nm');
        $str .= '<table class="ed"><tr><td></td><td>Поставщик</td><td>Бренд</td><td>Сезон</td><td>Скидка</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/14/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td><td>' . SelectStr('select id,name as nm from suppl order by name', 'sup', 0, 0) . '</td><td></td><td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/14/' . $rs->disc_id . '/">Ред</a></td><td>' . $rs->snm . '</td><td>' . $rs->tb3_nm . '</td><td>' . $rs->tb10_nm . '</td><td>' . $rs->disc_per . '</td><td class="edit-row"><a href="/adm/sp-edit/14/' . $rs->disc_id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 16:
        $res = mysql_query('select parser.id as prsid,parser.name as prsname,suppl.name as supname,fileformat,tyres,wheels, akb from parser left join suppl on parser.suppl=suppl.id order by parser.name');
        $str .= '<table class="ed"><tr><td></td><td>Название</td><td>Поставщик</td><td>Формат</td><td>Шины</td><td>Диски</td><td>АКБ</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/16/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td><td><input name="name" type="text" value="" /></td>
      <td>' . SelectStr('select id,name as nm from suppl order by name', 'suppl', 0, 0) . '</td><td></td><td></td><td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/16/' . $rs->prsid . '/">Ред</a></td><td>' .
                    $rs->prsname . '</td><td>' . $rs->supname . '</td><td>' . $rs->fileformat . '</td><td>' .
                    $rs->tyres . '</td><td>' . $rs->wheels . '</td><td>' . $rs->akb . '</td><td class="edit-row"><a href="/adm/sp-edit/16/' . $rs->prsid . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 17:
        $res = mysql_query('SELECT * FROM suppl ORDER BY name');
        $str .= '<table class="ed"><tr><td></td><td>Название</td><td>В наличии</td><td>Минимум</td><td></td></tr>' .
                '<form enctype="multipart/form-data" action="/adm/sp-edit/17/0/" method="post">' .
                '<tr><td><input name="add" type="submit" value="доб" /></td><td><input name="name" type="text" value="" /></td>' .
                '<td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res)) {

            $str .= '<tr><td><a href="/adm/sp-edit/17/' . $rs->id . '/">Ред</a></td><td>' .
                    $rs->name . '</td><td>' . $rs->isnal . '</td><td>' . $rs->min_cnt .
                    '</td><td class="edit-row"><a href="/adm/sp-edit/17/' . $rs->id .
                    '/del/">Уд</a></td></tr>';
        }
        $str .= '</table>';
        break;
    case 18:
        $res = mysql_query('select * from news order by date desc');
        $str .= '<table class="ed"><tr><td></td><td>Заголовок</td><td>Дата</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/18/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td><td><input name="title" type="text" value="" /></td>
      <td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/18/' . $rs->id . '/">Ред</a></td><td>' . $rs->title . '</td><td>' . $rs->date . '</td>
        <td class="edit-row"><a href="/adm/sp-edit/18/' . $rs->id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 19:
        $res = mysql_query('select p_type.name as ptnm,id_tbl_n,id_sp,t10_id,t3_id,nac,suppl.name as snm,tb10_nm,tb3_nm from nacen left join tab10 on tb10_id=t10_id left join tab3 on tb3_id=t3_id left join p_type on p_type.id=price_type left join suppl on suppl.id=id_sp order by suppl.name, tb10_nm');
        $str .= '<table class="ed"><tr><td></td><td>Поставщик</td><td>Сезон</td><td>Бренд</td><td>Наценка</td><td>К цене</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/19/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td><td>' . SelectStr('select id,name as nm from suppl order by name', 'id_sp', 0, 0) . '</td><td></td><td></td><td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/19/' . $rs->id_tbl_n . '/">Ред</a></td><td>' . $rs->snm . '</td><td>' . $rs->tb10_nm . '</td><td>' . $rs->tb3_nm . '</td><td>' . $rs->nac . '</td><td>' . $rs->ptnm . '</td><td class="edit-row"><a href="/adm/sp-edit/19/' . $rs->id_tbl_n . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 20:
        $res = mysql_query('select * from pages order by nmpg');
        $str .= '<table class="ed"><tr><td></td><td>Название</td><td>URL</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/20/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td><td><input name="nmpg" type="text" value="" /></td>
      <td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/20/' . $rs->id . '/">Ред</a></td><td>' . $rs->nmpg . '</td><td>' . $rs->pg . '</td>
        <td class="edit-row"><a href="/adm/sp-edit/20/' . $rs->id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 21:
        $res = mysql_query('select id,name as nm from profw order by (name*1)');
        $str .= '<h1>Справочник ширина профиля шины</h1>' . $str_error . '<table class="ed"><tr><td></td><td>ID</td><td>Наименование</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/21/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
      <td></td><td><input name="name" type="text" value="" /></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/21/' . $rs->id . '/">Ред</a></td>
        <td>' . $rs->id . '</td><td>' . $rs->nm . '</td><td><a href="/adm/sp-edit/21/' . $rs->id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 22:
        $res = mysql_query('select id,name as nm from profh order by (name*1)');
        $str .= '<h1>Справочник высота профиля шины</h1>' . $str_error . '<table class="ed"><tr><td></td><td>ID</td><td>Наименование</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/22/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
      <td></td><td><input name="name" type="text" value="" /></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/22/' . $rs->id . '/">Ред</a></td>
        <td>' . $rs->id . '</td><td>' . $rs->nm . '</td><td><a href="/adm/sp-edit/22/' . $rs->id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 23:
        $res = mysql_query("select nac_id,nac_min,nac_max,nac_per,tb1_nm,tb3_nm,suppl.name as spnm,tb10_nm
      from nacenki left join tab1 on tb1id=tb1_id left join tab10 on tb10_id=ses_id left join tab3 on tb3_id=brand_id left join suppl on suppl_id=suppl.id
      order by tb1id, spnm, tb3_nm, nac_min");
        $str .= '<table class="ed"><tr><td></td><td>Поставщик</td><td>Товар</td><td>Бренд</td><td>Сезон</td><td>Цена от (>)</td><td>Цена до (<=)</td><td>Наценка</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/23/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td>
      <td>' . SelectStr('select tb1_id as id,tb1_nm as nm from tab1', 'tb1id', 0, 0) . '</td>
      <td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/23/' . $rs->nac_id . '/">Ред</a></td><td>' . $rs->spnm . '</td><td>' . $rs->tb1_nm . '</td><td>' . $rs->tb3_nm . '</td><td>' . $rs->tb10_nm . '</td><td>' . $rs->nac_min . '</td><td>' . $rs->nac_max . '</td><td>' . $rs->nac_per . '</td><td><a href="/adm/sp-edit/23/' . $rs->nac_id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 25:
        $res = mysql_query("select nac_id,nac_min,nac_max,nac_per,tb1_nm,tb3_nm,suppl.name as spnm from nacenki left join tab1 on tb1id=tb1_id left join tab3 on tb3_id=brand_id left join suppl on suppl_id=suppl.id
      order by tb1id, spnm, tb3_nm, nac_min");
        $str .= '<table class="ed"><tr><td></td><td>Поставщик</td><td>Товар</td><td>Бренд</td><td>Цена от (>)</td><td>Цена до (<=)</td><td>Наценка</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/23/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td>
      <td>' . SelectStr('select tb1_id as id,tb1_nm as nm from tab1', 'tb1id', 0, 0) . '</td>
      <td></td><td></td><td></td><td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/23/' . $rs->nac_id . '/">Ред</a></td><td>' . $rs->spnm . '</td><td>' . $rs->tb1_nm . '</td><td>' . $rs->tb3_nm . '</td><td>' . $rs->nac_min . '</td><td>' . $rs->nac_max . '</td><td>' . $rs->nac_per . '</td><td><a href="/adm/sp-edit/23/' . $rs->nac_id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 30:
        $res = mysql_query("select t_auto_id, t_auto_nm, t_auto_pic, t_auto_vis, rp, rph from t_auto order by t_auto_nm");
        $str .= '<table class="ed"><tr><td></td><td>Бренд авто</td><td>Изображения</td><td>Видимость</td><td>Реплика</td><td>Реплика H</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/30/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td>
      <td><input name="name" type="text" value="" /></td>
      <td></td><td></td><td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/30/' . $rs->t_auto_id . '/">Ред</a></td><td>' . $rs->t_auto_nm . '</td>
        <td>' . $rs->t_auto_pic . '</td><td>' . $rs->t_auto_vis . '</td><td>' . $rs->rp . '</td><td>' . $rs->rph . '</td>
        <td><a href="/adm/sp-edit/30/' . $rs->t_auto_id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 41:
        $res = mysql_query('SELECT `id`, `name`, alt FROM akb_brand ORDER BY `name`');
        $str .= '<h1>Справочник производителей АКБ</h1>' . $str_error .
                '<table class="ed"><tr><td></td><td>ID</td><td>Наименование</td><td>Альт. Наз</td><td></td></tr>
        <form enctype="multipart/form-data" action="/adm/sp-edit/41/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
        <td></td><td><input name="nm" type="text" value="" /></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res)) {
            $str .= '<tr><td><a href="/adm/sp-edit/41/' . $rs->id . '/">Ред</a></td><td>' .
                    $rs->id . '</td><td>' . $rs->name . '</td><td>' . $rs->alt . '</td><td>
        <a href="/adm/sp-edit/41/' . $rs->id . '/del/">Уд</a></td></tr>';
        }
        $str .= '</table>';
        break;
    case 42:
        $res = mysql_query('select am.id as mid, am.name as mnm, am.alt as malt,
        am.reg as mreg, am.url as murl, am.vis as mvis
        FROM akb_model AS am
        order by am.name');
        $str .= '<h1>Справочник моделей АКБ</h1>' . $str_error . '<table class="ed">
      <tr><td></td><td>ID</td><td>Наименование</td>
      <td>URL</td><td>в работе</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/42/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td>
      <td></td><td><input name="nm" type="text" value="" /></td>
      <td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res)) {

            $str .= '<tr><td><a href="/adm/sp-edit/42/' . $rs->mid . '/">Ред</a></td><td>' . $rs->mid . '</td><td>' . $rs->mnm . '</td>
          <td>' . $rs->murl . '</td><td>' . $rs->mvis . '</td>
          <td><a href="/adm/sp-edit/42/' . $rs->mid . '/del/">Уд</a></td></tr>';
        }
        $str .= '</table>';
        break;
    case 43: case 44:

        if ($argex[0] == 43) {

            $tableName = 'akb_volt';
            $h1 = '<h1>Справочник вольтажа АКБ</h1>';
        }
        if ($argex[0] == 44) {

            $tableName = 'akb_v';
            $h1 = '<h1>Справочник объем АКБ</h1>';
        }
        $res = mysql_query('SELECT id, name as nm, vis FROM ' . $tableName . ' ORDER BY (name * 1)');
        $str .= $h1 . $str_error . '<table class="ed">
      <tr><td></td><td>ID</td><td>Наименование</td><td>Видимый</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/' . $argex[0] . '/0/" method="post">
      <tr><td><input name="add" type="submit" value="доб" /></td>
      <td></td><td><input name="nm" type="text" value="" /></td><td></td><td></td></tr>
      </form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/' . $argex[0] . '/' . $rs->id . '/">Ред</a></td>
        <td>' . $rs->id . '</td><td>' . $rs->nm . '</td><td>' . $rs->vis .
                    '</td><td><a href="/adm/sp-edit/' . $argex[0] . '/' . $rs->id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 46:
        $res = mysql_query("SELECT nac_id, nac_min, nac_max, nac_per, suppl.name AS spnm
        FROM akb_nacenki LEFT JOIN suppl ON suppl_id=suppl.id ORDER BY spnm, nac_min");
        $str .= '<table class="ed"><tr><td></td><td>Поставщик</td><td>Цена от (>)</td>
        <td>Цена до (<=)</td><td>Наценка</td><td></td></tr>
        <form enctype="multipart/form-data" action="/adm/sp-edit/46/0/" method="post">
        <tr><td><input name="add" type="submit" value="доб" /></td>
        <td></td><td></td><td></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res))
            $str .= '<tr><td><a href="/adm/sp-edit/45/' . $rs->nac_id . '/">Ред</a></td><td>' .
                    $rs->spnm . '</td><td>' . $rs->nac_min . '</td><td>' . $rs->nac_max .
                    '</td><td>' . $rs->nac_per . '</td><td><a href="/adm/sp-edit/46/' .
                    $rs->nac_id . '/del/">Уд</a></td></tr>';
        $str .= '</table>';
        break;
    case 47:
        $res = mysql_query('SELECT `id`, `name`, alt FROM akb_klemy ORDER BY `name`');
        $str .= '<h1>Справочник клемм АКБ</h1>' . $str_error .
                '<table class="ed"><tr><td></td><td>ID</td><td>Наименование</td><td>Альт. Наз</td><td></td></tr>
        <form enctype="multipart/form-data" action="/adm/sp-edit/47/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
        <td></td><td><input name="nm" type="text" value="" /></td><td></td><td></td></tr></form>';
        while ($rs = mysql_fetch_object($res)) {
            $str .= '<tr><td><a href="/adm/sp-edit/47/' . $rs->id . '/">Ред</a></td><td>' .
                    $rs->id . '</td><td>' . $rs->name . '</td><td>' . $rs->alt . '</td><td>
        <a href="/adm/sp-edit/47/' . $rs->id . '/del/">Уд</a></td></tr>';
        }
        $str .= '</table>';
        break;
    case 48:
        $selItems = $dbcon->prepare("SELECT id, type_key, description FROM html_blocks ORDER BY id");
        $str .= '<table class="ed"><tr><td></td><td>Ключ</td><td>Описание</td><td></td></tr>' .
                '<form enctype="multipart/form-data" action="/adm/sp-edit/48/0/" method="post">' .
                '<tr><td><input name="add" type="submit" value="доб" /></td><td></td>' .
                '<td></td><td></td></tr></form>';
        if ($selItems->execute() && $selItems->rowCount() > 0) {
            while ($objItem = $selItems->fetch(PDO::FETCH_OBJ)) {
                $str .= '<tr><td><a href="/adm/sp-edit/48/' . $objItem->id . '/">Ред</a></td><td>' .
                    $objItem->type_key . '</td><td>' . $objItem->description . '</td><td><a href="/adm/sp-edit/48/' .
                    $objItem->id . '/del/">Уд</a></td></tr>';
            }
        }
        $str .= '</table>';
        break;
}
$str .= '</td></tr></table>';
