<?php

function IdByName($nm, $tbl, $field_id, $field_name)
{
    $sql = 'SELECT ' . $field_id . ' FROM ' . $tbl . ' WHERE ' . $field_name . "='" . $nm . "'";
    $result = query($sql);
    if (count($result['data']) == 0) {
        return 0;
    } else {
        return $result['data'][0]->$field_id;
    }
}

// функция генерация <select> ==================================================
function SelectStr($query, $selname, $id, $fl, $dop = '')
{
    $tmpstr = '<select name="' . $selname . '" id="' . $selname . '"' . $dop . '>' . ($fl == 1 ? '<option value="0"' .
            (!$id ? 'selected="selected"' : '') . '>Не указан</option>' : '');
    $res = query($query);
    if ($res['result']) {
        foreach ($res['data'] as $rs_sel) {
            $tmpstr .= '<option value="' . $rs_sel->id . '"' . ($id == $rs_sel->id ? 'selected="selected"' : '') . '>' .
                $rs_sel->nm . '</option>';
        }
    }
    $tmpstr .= '</select>';
    return $tmpstr;
}

function createFilterItem($caption, $selName, $sql, $params, $selected, $selDop = '')
{
    $str = '<td><div>' . $caption . '</div><select id="' . $selName . '" name="' . $selName . '"' . $selDop . '>';
    $str .= '<option value="0"' . (!empty($selected) ? ' selected="selected"' : '') . '>все</option>';
    $resT = query($sql, $params);
    if ($resT['result']) {
        foreach ($resT['data'] as $rtp) {
            $str .= '<option value="' . $rtp->item_id . '"' . ($selected == $rtp->item_id ? ' selected="selected"' : '') .
                '>' . $rtp->item_nm . '</option>';
        }
    }
    $str .= "</select></td>";
    return $str;
}

function makeSortLink($link, $sort, $sortType, $key, $name)
{
    return '<a href="' . $link . '1/' . $key . '/' . ($sort === $key && $sortType == 'ASC' ? 'DESC' : 'ASC') .
        '.html">' . $name . ($sort === $key ? '<i class="' . ($sortType == 'ASC' ? 'down' : 'up') . '"></i>' : '') . '</a>';
}

function PagesCreate($link, $all_cn, $crpg, $sort, $sortType)
{
    $strtmp = '';
    if ($all_cn < 8) {
        for ($i = 1; $i <= $all_cn; $i++) {
            $strtmp .= ($i != $crpg ? '<a href="' . $link . $i . '/' . $sort . '/' . $sortType . '.html">' . $i .
                '</a>' : '<span>' . $i . '</span>');
        }
    }
    if ($all_cn >= 8) {
        if ($crpg < 4) {
            for ($i = 1; $i <= 5; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="' . $link . $i . '/' . $sort . '/' . $sortType . '.html">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
            $strtmp .= '<a href="">&hellip;</a><a href="' . $link . $all_cn . '/' . $sort . '/' . $sortType . '.html">' .
                $all_cn . '</a>';
        }
        if ($crpg == 4) {
            for ($i = 1; $i <= 6; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="' . $link . $i . '/' . $sort . '/' . $sortType . '.html">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
            $strtmp .= '<a href="">&hellip;</a><a href="' . $link . $all_cn . '/' . $sort . '/' . $sortType . '.html">' .
                $all_cn . '</a>';
        }
        if ($crpg > $all_cn - 3) {
            $strtmp .= '<a href="' . $link . '1.html">1</a><a href="">&hellip;</a>';
            for ($i = $all_cn - 4; $i <= $all_cn; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="' . $link . $i . '/' . $sort . '/' . $sortType . '.html">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
        }
        if ($crpg == $all_cn - 3) {
            $strtmp .= '<a href="' . $link . '1.html">1</a><a href="">&hellip;</a>';
            for ($i = $all_cn - 5; $i <= $all_cn; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="' . $link . $i . '/' . $sort . '/' . $sortType . '.html">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
        }
        if ($crpg < $all_cn - 3 && $crpg > 4) {
            $strtmp .= '<a href="' . $link . '1' . '/' . $sort . '/' . $sortType . '.html">1</a><a href="">&hellip;</a>';
            for ($i = $crpg - 2; $i <= $crpg + 2; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="' . $link . $i . '/' . $sort . '/' . $sortType . '.html">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
            $strtmp .= '<a href="">&hellip;</a><a href="' . $link . $all_cn . '/' . $sort . '/' . $sortType . '.html">' .
                $all_cn . '</a>';
        }
    }
    return $strtmp;
}

$sort = 'r_name';
$sortType = 'ASC';

$sql = "";
$tov_nm = "";
$h1 = "";
$res = query("SELECT tb1_id, tb1_nm FROM tab1 WHERE translit = :translit", [':translit' => $arg[0]]);
if ($res['result'] === false || count($res['data']) == 0) {
    echo '<p>Получение данных о типе товара. Ошибка обращения к БД. ' . dbLastErrorToString($res['error']) . '</p>';
    exit;
}
$tov = $res['data'][0]->tb1_id;
$tov_nm = $res['data'][0]->tb1_nm;
$sql .= " tab1_id=" . $tov;
$h1 = $tov_nm;
$ftip_nm = "";

if ($tov == 3) {
    $sql = 'akb_tovar.id <> 0 ';
    if (isset($arg[7])) {
        $_POST["pg"] = $arg[7];
    }
    if (!$_POST["pg"]) {
        $_POST["pg"] = 1;
    }
    if (isset($arg[8])) {
        $sort = $arg[8];
    }
    if (isset($arg[9])) {
        $sortType = $arg[9];
    }
    // определение модели ========================================================
    $tmodel_nm = "";
    if (isset($arg[2])) {
        $_POST["tmodel"] = $arg[2];
    }
    if ($_POST["tmodel"]) {
        $tmodel_nm = IdByName($_POST["tmodel"], "akb_model", "name", "id");
        $sql .= " and id_model = " . $_POST["tmodel"];
        $h1 .= " " . $tmodel_nm;
    }
    // /определение модели =======================================================
    $fvolt_id = 0;
    if (isset($arg[3])) {
        $_POST["tvolt"] = $arg[3];
    }
    if ($_POST["tvolt"]) {
        $fvolt_nm = IdByName($_POST["tvolt"], "akb_volt", "name", "id");
        $sql .= " and id_volt=" . $_POST["tvolt"];
        $h1 .= " " . $fvolt_nm;
    }
    $fvol_id = 0;
    if (isset($arg[4])) {
        $_POST["tvol"] = $arg[4];
    }
    if ($_POST["tvol"]) {
        $fvol_nm = IdByName($_POST["tvol"], "akb_v", "name", "id");
        $sql .= " and id_v=" . $_POST["tvol"];
        $h1 .= " " . $fvol_nm;
    }
    if (isset($arg[5])) {
        $_POST["find"] = $arg[5];
    }
    if ($_POST["find"] == "0") {
        $_POST["find"] = "";
    }
    if ($_POST["find"]) {
        $sql .= " and full_name like '%" . urldecode($_POST["find"]) . "%'";
    }
    $inwork = 0;
    if (isset($arg[6])) {
        $_POST["inwork"] = $arg[6];
    }
    if ($_POST["inwork"]) {
        $sql .= " and akb_tovar.vis = 1";
        $h1 .= " в работе";
    }
    $innal = 0;
    if (isset($arg[7])) {
        $_POST["innal"] = $arg[7];
    }
    if ($_POST["innal"]) {
        $sql .= " AND cnt > 0";
        $h1 .= " в наличии";
    }
} else {
// определение текущей страницы ================================================
    if (isset($arg[14])) {
        $_POST["pg"] = $arg[14];
    }
    if (!$_POST["pg"]) {
        $_POST["pg"] = 1;
    }
    if (isset($arg[15])) {
        $sort = $arg[15];
    }
    if (isset($arg[16])) {
        $sortType = $arg[16];
    }
// /определение текущей страницы ===============================================
// определение типа авто =======================================================
    if (isset($arg[1]) && $arg[1] != 0) {
        $_POST["ftip"] = $arg[1];
    }
    if ($_POST["ftip"]) {
        $ftip_nm = IdByName($_POST["ftip"], "tab2", "tb2_nm", "tb2_id");
        $sql .= " and tab2_id=" . $_POST["ftip"];
        $h1 .= " " . $ftip_nm;
    }
    // /определение типа авто ======================================================
    // определение фирмы ===========================================================
    $ffirm_nm = "";
    if (isset($arg[2]) && $arg[2] != 0) {
        $_POST["ffirm"] = $arg[2];
    }
    if ($_POST["ffirm"]) {
        $ffirm_nm = IdByName($_POST["ffirm"], "tab3", "tb3_nm", "tb3_id");
        $sql .= " and tab3_id=" . $_POST["ffirm"];
        $h1 .= " " . $ffirm_nm;
    }
    // /определение фирмы ==========================================================
    // определение модели ==========================================================
    $tmodel_nm = "";
    if (isset($arg[3])) {
        $_POST["tmodel"] = $arg[3];
    }
    if ($_POST["tmodel"]) {
        $tmodel_nm = IdByName($_POST["tmodel"], "tab4", "tb4_nm", "tb4_id");
        $sql .= " and tab4_id=" . $_POST["tmodel"];
        $h1 .= " " . $tmodel_nm;
    }
    // /определение модели =========================================================
    if ($tov == 1) {
        $fprofilw_nm = 0;
        if (isset($arg[4])) {
            $_POST["fprofilw"] = $arg[4];
        }
        if ($_POST["fprofilw"]) {
            $fprofilw_nm = IdByName($_POST["fprofilw"], "profw", "name", "id");
            $sql .= " and w_id=" . $_POST["fprofilw"];
            $h1 .= " " . $fprofilw_nm;
        }
        $fprofilh_nm = 0;
        if (isset($arg[11])) {
            $_POST["fprofilh"] = $arg[11];
        }
        if ($_POST["fprofilh"]) {
            $fprofilh_nm = IdByName($_POST["fprofilh"], "profh", "name", "id");
            $sql .= " and h_id=" . $_POST["fprofilh"];
            $h1 .= " " . $fprofilh_nm;
        }
    }
    if ($tov == 2) {
        $fprofil_nm = 0;
        if (isset($arg[4])) {
            $_POST["fprofil"] = $arg[4];
        }
        if ($_POST["fprofil"]) {
            $fprofil_nm = IdByName($_POST["fprofil"], "tab5", "tb5_nm", "tb5_id");
            $sql .= " AND tab5_id=" . $_POST["fprofil"];
            $h1 .= " " . $fprofil_nm;
        }
    }
    $fradius_id = 0;
    if (isset($arg[5])) {
        $_POST["fradius"] = $arg[5];
    }
    if ($_POST["fradius"]) {
        $fradius_nm = IdByName($_POST["fradius"], "tab6", "tb6_nm", "tb6_id");
        $sql .= " and tab6_id=" . $_POST["fradius"];
        $h1 .= " " . $fradius_nm;
    }
    $gruz_nm = 0;
    if (isset($arg[6])) {
        $_POST["gruz"] = $arg[6];
    }
    if ($_POST["gruz"]) {
        $gruz_nm = IdByName($_POST["gruz"], "tab7", "tb7_nm", "tb7_id");
        $sql .= " and tab7_id=" . $_POST["gruz"];
        $h1 .= " " . $gruz_nm;
    }
    $speed_id = 0;
    if (isset($arg[7])) {
        $_POST["speed"] = $arg[7];
    }
    if ($_POST["speed"]) {
        $fradius_nm = IdByName($_POST["speed"], "tab8", "tb8_nm", "tb8_id");
        $sql .= " and tab8_id=" . $_POST["speed"];
        $h1 .= " " . $speed_nm;
    }
    if ($tov == 1) {
        $ship_id = 0;
        if (isset($arg[8])) {
            $_POST["ship"] = $arg[8];
        }
        if ($_POST["ship"]) {
            $ship_nm = IdByName($_POST["ship"], "tab9", "tb9_nm", "tb9_id");
            if ($tov == 1) {
                $sql .= " and t4sh = " . $_POST["ship"];
            } else {
                $sql .= " and tab9_id = " . $_POST["ship"];
            }
            $h1 .= " " . $ship_nm;
        }
    }
    if ($tov == 2) {
        $vilet_from = filter_input(INPUT_POST, 'vilet_from', FILTER_VALIDATE_INT);
        $vilet_to = filter_input(INPUT_POST, 'vilet_to', FILTER_VALIDATE_INT);
        if (isset($arg[8])) {
            $viletArray = explode('_', $arg[8]);
            $vilet_from = $viletArray[0];
            $vilet_to = $viletArray[1];
        }
        if (!$vilet_from) {
            $vilet_from = 0;
        }
        if (!$vilet_to) {
            $vilet_to = 0;
        }
        if ($vilet_from) {
            $vilet_from_nm = IdByName($vilet_from, 'tab9', 'tb9_nm', 'tb9_id');
            $sql .= " AND (REPLACE(tab9.tb9_nm, ',', '.')*1)>=" . str_replace(',', '.', $vilet_from_nm);
            $h1 .= " вылет от " . $vilet_from_nm;
        }
        if ($vilet_from) {
            $vilet_to_nm = IdByName($vilet_to, 'tab9', 'tb9_nm', 'tb9_id');
            $sql .= " AND (REPLACE(tab9.tb9_nm, ',', '.')*1)<=" . str_replace(',', '.', $vilet_to_nm);
            $h1 .= " до " . $vilet_to_nm;
        }
    }
    $fseason_id = 0;
    if (isset($arg[9])) {
        $_POST["fseason"] = $arg[9];
    }
    if ($_POST["fseason"]) {
        $fseason_nm = IdByName($_POST["fseason"], "tab10", "tb10_nm", "tb10_id");
        if ($tov == 1) {
            if ($_POST["fseason"] == 50) {
                $fseason_nm = "Зима не шипованные";
                $sql .= " and tab10_id = 5 AND t4sh = 0";
            }
            if ($_POST["fseason"] == 53) {
                $fseason_nm = "Зима шипованные";
                $sql .= " and tab10_id = 5 AND t4sh = 3";
            }
        }
        if ($_POST["fseason"] < 50) {
            $sql .= " and tab10_id = " . $_POST["fseason"];
        }
        $h1 .= " " . $fseason_nm;
    }

    if (isset($arg[10])) {
        $_POST["find"] = $arg[10];
    }
    if ($_POST["find"] == "0") {
        $_POST["find"] = "";
    }
    if ($_POST["find"]) {
        $sql .= " and all_name like '%" . urldecode($_POST["find"]) . "%'";
    }
    if ($tov == 2) {
        $stup_id = 0;
        if (isset($arg[11])) {
            $_POST["stup"] = $arg[11];
        }
        if ($_POST["stup"]) {
            $stup_nm = IdByName($_POST["stup"], "tab12", "tb12_nm", "tb12_id");
            $sql .= " and tab12_id=" . $_POST["stup"];
            $h1 .= " " . $stup_nm;
        }
    }

    $inwork = 0;
    if (isset($arg[12])) {
        $_POST["inwork"] = $arg[12];
    }
    if ($_POST["inwork"]) {

        $sql .= " and wrk = 1";
        $h1 .= " в работе";
    }

    $innal = 0;
    if (isset($arg[13])) {
        $_POST["innal"] = $arg[13];
    }
    if ($_POST["innal"]) {
        $sql .= " AND isnal = 1";
        $h1 .= " в наличии";
    }
}

$str .= '<table><tr><td style="width:140px;vertical-align:top">';
include_once("lmenu.php");
$str .= '</td><td style="vertical-align:top">';

if ($tov == 3) {
    $str .= '<div id="sp-pod" style="height: 200px"><form method="post" name="ftyre" action="/adm/sp-tov/' . $arg[0] . '/">' .
        '<div class="flt" style="height: 200px"><table><tr><td><div>Тип товара</div><a href="/adm/sp-tov/shini/" class="tov"' .
        ($tov == 1 ? ' style="color:#CC0033"' : '') . '>Шины</a>' .
        '<a href="/adm/sp-tov/diski/" class="tov"' . ($tov == 2 ? ' style="color:#CC0033"' : '') . '>Диски</a>' .
        '<a href="/adm/sp-tov/akb/" class="tov"' . ($tov == 3 ? ' style="color:#CC0033"' : '') . '>АКБ</a></td>' .
        '<td><div>Модель</div>' .
        SelectStr('SELECT id, `name` AS nm FROM akb_model ORDER BY `name`', 'tmodel', $_POST['tmodel'], 1) .
        '</td><td><div>Вольтаж</div>' .
        SelectStr('SELECT id, `name` AS nm FROM akb_volt ORDER BY `name`', 'tvolt', $_POST['tvolt'], 1) .
        '</td><td><div>Объем</div>' .
        SelectStr('SELECT id, `name` AS nm FROM akb_v ORDER BY `name`', 'tvol', $_POST['tvol'], 1) .
        '</td></tr><tr><td colspan="3" class="poisk"><div>Поиск по названию</div><input type="text" value="' .
        $_POST['find'] . '" name="find" /></td><td></td></tr><tr><td><input type="checkbox" name="inwork" ' .
        ($_POST['inwork'] ? 'checked="checked"' : '') . ' />Видимый<br/><input type="checkbox" name="innal" ' .
        ($_POST['innal'] ? 'checked="checked"' : '') . ' />В наличии</td><td colspan=2>' .
        '<a class="update" href="/adm/updateurl/' . $arg[0] . '/brand/">Обновить URL брендов</a><br/>' .
        '<a class="update" href="/adm/updateurl/' . $arg[0] . '/model/">Обновить URL моделей</a><br/>' .
        '<a class="update" href="/adm/updateurl/' . $arg[0] . '/tovar/">Обновить URL АКБ</a></td>' .
        '<td class="button"><input type="submit" name="pst" value="Фильтровать"/></td></tr></table></div></form></div>';
    $sql1 = "";
    $i = 0;
} else {
    $str .= '<div id="sp-pod"><form method="post" name="ftyre" action="/adm/sp-tov/' . $arg[0] . '/"><div class="flt">' .
        '<table><tr><td><div>Тип товара</div><a href="/adm/sp-tov/shini/" class="tov"' .
        ($tov == 1 ? ' style="color:#CC0033"' : '') . '>Шины</a><a href="/adm/sp-tov/diski/" class="tov"' .
        ($tov == 2 ? ' style="color:#CC0033"' : '') . '>Диски</a><a href="/adm/sp-tov/akb/" class="tov"' .
        ($tov == 3 ? ' style="color:#CC0033"' : '') . '>АКБ</a></td>';
    $str .= createFilterItem(($tov == 1 ? 'Тип авто' : 'Цвет'), 'ftip',
        'SELECT tb2_id AS item_id, tb2_nm AS item_nm FROM tab2 WHERE tb2_tov_id=:tov' .
        ($tov == 2 && $_POST["ffirm"] > 0 ? ' AND brid=' . $_POST["ffirm"] : '') . ' ORDER BY tb2_sort', [':tov' => $tov],
        ($_POST["ftip"] ? $_POST["ftip"] : ''));
    $str .= createFilterItem('Производитель', 'ffirm',
        'SELECT tb3_id AS item_id, tb3_nm AS item_nm FROM tab3 WHERE tb3_tov_id=:tov_id ORDER BY tb3_nm',
        [':tov_id' => $tov], ($_POST["ffirm"] ? $_POST["ffirm"] : ''),
        " onchange=\"return getModels('ffirm', 'tmodel')\"");
    $str .= createFilterItem('Модель', 'tmodel',
        'SELECT tb4_id AS item_id, tb4_nm AS item_nm FROM tab4 WHERE tb4_tov_id=:tov_id' .
        ($_POST["ffirm"] > 0 ? " and brand_id=" . $_POST["ffirm"] : "") . ' ORDER BY tb4_nm',
        [':tov_id' => $tov], ($_POST["tmodel"] ? $_POST["tmodel"] : ''));
    $str .= '</tr><tr>';
    if ($tov == 1) {
        $str .= '<td><div>Профиль</div>' .
            SelectStr('SELECT id, `name` AS nm FROM profw ORDER BY `name` * 1', 'fprofilw', $_POST["fprofilw"], 1) .
            SelectStr('SELECT id, `name` AS nm FROM profh ORDER BY `name` * 1', 'fprofilh', $_POST["fprofilh"], 1) .
            '</td>';
    }
    if ($tov == 2) {
        $str .= createFilterItem('Ширина', 'fprofil',
            'SELECT tb5_id AS item_id, tb5_nm AS item_nm FROM tab5 WHERE tb5_tov_id=:tov_id ORDER BY tb5_nm * 1',
            [':tov_id' => $tov], ($_POST["fprofil"] ? $_POST["fprofil"] : ''));
    }
    $str .= createFilterItem('Диаметр', 'fradius',
        'SELECT tb6_id AS item_id, tb6_nm AS item_nm FROM tab6 WHERE tb6_tov_id=:tov_id ORDER BY tb6_nm',
        [':tov_id' => $tov], ($_POST["fradius"] ? $_POST["fradius"] : ''));
    $str .= createFilterItem(($tov == 1 ? "Грузоподъемность" : "Кол-во отверстий"), 'gruz',
        'SELECT tb7_id AS item_id, tb7_nm AS item_nm FROM tab7 WHERE tb7_tov_id=:tov_id ORDER BY tb7_nm',
        [':tov_id' => $tov], ($_POST["gruz"] ? $_POST["gruz"] : ''));
    $str .= createFilterItem(($tov == 1 ? "Скорость" : "Сверловка"), 'speed',
        'SELECT tb8_id AS item_id, tb8_nm AS item_nm FROM tab8 WHERE tb8_tov_id=:tov_id ORDER BY tb8_nm' .
        ($tov == 2 ? '*1' : ''), [':tov_id' => $tov], ($_POST["speed"] ? $_POST["speed"] : ''));
    $str .= '</tr><tr>';
    if ($tov == 1) {
        $str .= createFilterItem("Шипованность", 'ship',
            'SELECT tb9_id AS item_id, tb9_nm AS item_nm FROM tab9 WHERE tb9_tov_id=:tov_id ORDER BY tb9_nm',
            [':tov_id' => $tov], ($_POST["ship"] ? $_POST["ship"] : ''));
    } else {
        $vilsql = 'SELECT tb9_id AS id, tb9_nm AS nm FROM tab9 WHERE tb9_tov_id=2 ORDER BY tb9_nm * 1';
        $str .= '<td><div>Вылет</div><div class="sel-dop">от' . SelectStr($vilsql, 'vilet_from', $vilet_from, 1) .
            '</div><div class="sel-dop">до' . SelectStr($vilsql, 'vilet_to', $vilet_to, 1) . '</div></td>';
    }
    $str .= '<td><div>' . ($tov == 1 ? "Сезонность" : "Тип диска") . '</div><select name="fseason">';
    $resT = query('SELECT tb10_id, tb10_nm FROM tab10 WHERE tb10_tov_id=:tov ORDER BY tb10_nm', [':tov' => $tov]);
    $str .= '<option value="0"' . (!$_POST["fseason"] ? ' selected="selected"' : '') . '>все</option>';
    if ($resT['result'] === true) {
        foreach ($resT['data'] as $rseas) {
            $str .= '<option value="' . $rseas->tb10_id . '"' . ($_POST["fseason"] == $rseas->tb10_id ? ' selected="selected"' : '') .
                '>' . $rseas->tb10_nm . '</option>';
        }
    }
    if ($tov == 1) {
        $str .= '<option value="50"' . ($_POST["fseason"] == 50 ? ' selected="selected"' : '') . '>Зима не шипованные</option>';
        $str .= '<option value="53"' . ($_POST["fseason"] == 53 ? ' selected="selected"' : '') . '>Зима шипованные</option>';
    }
    $str .= '</select></td><td colspan="2" class="poisk"><div>Поиск по названию</div><input type="text" value="' .
        $_POST["find"] . '" name="find" /></td></tr><tr>';
    if ($tov == 2) {
        $str .= createFilterItem('Ступица', 'stup',
            'SELECT tb12_id AS item_id, tb12_nm AS item_nm FROM tab12 WHERE tb12_tov_id=:tov_id ORDER BY tb12_nm' .
            ($tov == 2 ? '*1' : ''), [':tov_id' => $tov], ($_POST["stup"] ? $_POST["stup"] : ''));
    } else {
        $str .= '<td></td>';
    }
    $str .= "<td><input type=\"checkbox\" name=\"inwork\" " . ($_POST["inwork"] ? 'checked="checked"' : '') . " />Видимый<br/>
      <input type=\"checkbox\" name=\"innal\" " . ($_POST["innal"] ? 'checked="checked"' : '') . " />В наличии</td>
  <td colspan=\"2\">
  <a class='update' href='/adm/updateurl/" . $arg[0] . "/brand/'>Обновить URL брендов</a><br/>
  <a class='update' href='/adm/updateurl/" . $arg[0] . "/model/'>Обновить URL моделей</a><br/>
  <a class='update' href='/adm/updateurl/" . $arg[0] . "/tovar/'>Обновить URL товаров</a>
  </td></tr><tr><td class=\"button\" colspan='4'>" .
        "<input type=\"submit\" name=\"pst\" value=\"Фильтровать\"/></td></tr></table></div>";
    $sql1 = "";
    $i = 0;
    $str .= '</form></div>';
}
$str .= '<h1>' . $h1 . ', страница ' . $_POST["pg"] . '</h1><div id="nomens">';

switch ($tov) {
    case 1:
        $result = query("SELECT COUNT(*) AS cn FROM total LEFT JOIN suppl ON suppl.id = total.spid " .
            "LEFT JOIN tab4 ON tab4_id = tb4_id WHERE " . $sql);
        break;
    case 2:
    default:
        $result = query("SELECT COUNT(*) AS cn FROM total LEFT JOIN suppl ON suppl.id = total.spid LEFT JOIN tab9 ON tb9_id = total.tab9_id WHERE " . $sql);
        break;
    case 3:
        $result = query("SELECT COUNT(*) as cn FROM akb_tovar LEFT JOIN suppl ON suppl.id = akb_tovar.supid " .
            ($sql == '' ? '' : ' WHERE ' . $sql));
}

if ($result['result'] === false) {
    echo '<p>Получение количества товаров. Ошибка обращения к БД. ' . dbLastErrorToString($result['error']) . '</a>';
    exit;
}

$allcn = 0;
if (count($result['data']) > 0) {
    $allcn = ceil($result['data'][0]->cn / 100);
}
if ($tov == 3) {
    $lnkk = '/adm/sp-tov/' . $arg[0] . '/' .
        ($_POST["ffirm"] ? $_POST["ffirm"] : '0') . "/" . ($_POST["tmodel"] ? $_POST["tmodel"] : '0') .
        "/" . ($_POST["ftvolt"] ? $_POST["ftvolt"] : "0") . "/" .
        ($_POST["ftvol"] ? $_POST["ftvol"] : '0') . "/" .
        ($_POST["find"] ? $_POST["find"] : "0") . "/" . ($_POST["inwork"] ? '1' : '0') . '/' . ($_POST["innal"] ? '1' : '0') . '/';
} else {

    $lnkk = "/adm/sp-tov/" . $arg[0] . "/" . ($_POST["ftip"] ? $_POST["ftip"] : '0') .
        "/" . ($_POST["ffirm"] ? $_POST["ffirm"] : '0') . "/" . ($_POST["tmodel"] ? $_POST["tmodel"] : '0') .
        "/" . ($tov == 1 ? ($_POST["fprofilw"] ? $_POST["fprofilw"] : "0") : ($_POST["fprofil"] ? $_POST["fprofil"] : "0")) .
        "/" . ($_POST["fradius"] ? $_POST["fradius"] : '0') . "/" . ($_POST["gruz"] ? $_POST["gruz"] : '0') . "/" .
        ($_POST["speed"] ? $_POST["speed"] : '0') . "/" . ($tov == 1 ? ($_POST["ship"] ? $_POST["ship"] : '0') :
            $vilet_from . '_' . $vilet_to) . "/" . ($_POST["fseason"] ? $_POST["fseason"] : '0') .
        "/" . ($_POST["find"] ? $_POST["find"] : "0") . "/" .
        ($tov == 1 ? ($_POST["fprofilh"] ? $_POST["fprofilh"] : "0") : ($_POST["stup"] ? $_POST["stup"] : "0")) .
        "/" . ($_POST["inwork"] ? '1' : '0') . '/' . ($_POST["innal"] ? '1' : '0') . '/';
}
if ($allcn > 1) {
    $strZak = PagesCreate($lnkk, $allcn, $_POST["pg"], $sort, $sortType);
    $str .= "<div class=\"sp-pages\"><span>Страницы:</span> " . $strZak . "</div>";
}

$str .= '<table id="nomen"><tr class="head"><td><input type="checkbox" value="" name="idall" onchange="return selectAllTov(this)"/></td>' .
    '<td>Доб</td><td class="idtov">' . makeSortLink($lnkk, $sort, $sortType, 'r_id', 'ID') .
    '</td><td class="name">' . makeSortLink($lnkk, $sort, $sortType, 'r_name', 'Наименование') .
    '</td><td class="name">' . makeSortLink($lnkk, $sort, $sortType, 'r_url', 'URL') .
    '</td><td>' . makeSortLink($lnkk, $sort, $sortType, 'snm', 'Поставщик') .
    '</td><td>' . makeSortLink($lnkk, $sort, $sortType, 'cnt', 'Кол-во') .
    '</td><td>' . makeSortLink($lnkk, $sort, $sortType, 'price', 'Цена') .
    '</td><td>' . makeSortLink($lnkk, $sort, $sortType, 'wrk', 'Видимый') .
    '</td><td>Изобр.</td><td></td></tr>';

switch ($tov) {
    case 1:
        $sql = "SELECT total_id AS r_id, all_name AS r_name, total.url AS r_url, wrk, cnt, price, suppl.name AS snm, imgname " .
            "FROM total LEFT JOIN suppl ON suppl.id = total.spid LEFT JOIN tab4 ON tab4_id = tb4_id " .
            "LEFT JOIN imgs ON imgs.idmodel = tab4_id WHERE " . $sql . " ORDER BY " . $sort . ' ' . $sortType .
            "  LIMIT " . (($_POST["pg"] - 1) * 100) . ",100";
        break;
    case 2:
    default:
        $sql = "SELECT total_id AS r_id, all_name AS r_name, url AS r_url, wrk, cnt, price, suppl.name AS snm, imgname " .
            "FROM total LEFT JOIN suppl ON suppl.id = total.spid " .
            "LEFT JOIN imgs ON imgs.idmodel = tab4_id AND imgs.idcolor = tab2_id  LEFT JOIN tab9 ON tb9_id = total.tab9_id " .
            "WHERE " . $sql . " ORDER BY " . $sort . ' ' . $sortType . " LIMIT " . (($_POST["pg"] - 1) * 100) . ",100";
        break;
    case 3:
        $sql = "SELECT akb_tovar.id AS r_id, full_name AS r_name, akb_tovar.url AS r_url, akb_tovar.vis AS wrk, cnt, " .
            "price, suppl.name AS snm, am.pic AS imgname FROM akb_tovar " .
            "LEFT JOIN suppl ON suppl.id = akb_tovar.supid left join akb_model as am on id_model = am.id " .
            ($sql == '' ? '' : ' WHERE ' . $sql) . " ORDER BY " . $sort . ' ' . $sortType . " LIMIT " .
            (($_POST["pg"] - 1) * 100) . ",100";
}

if ($tov == 3) {
    $str .= '<form enctype="multipart/form-data" action="/adm/sp-tov-edit/0/0/" method="post">
    <tr><td>
    <input name="ids" type="hidden" value="" /></td><td></td>
    <td class="idtov"><input name="add" type="submit" value="доб" /></td>
    <td class="name">' . SelectStr('SELECT id, name as nm FROM akb_brand ORDER BY name', 'id_brand', 0, 0) .
        '<input name="tov" value="' . $tov . '" type="hidden" /></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr></form>
    <form enctype="multipart/form-data" action="/adm/sp-tov-mass/" method="post">
    <tr><td><input name="upd" type="submit" value="об" />
    <input name="ids" type="hidden" value="" /></td><td></td>
    <td class="idtov"></td><td class="name" colspan="2">' .
        SelectStr('SELECT id, name as nm FROM akb_brand ORDER BY name', 'brand_id_upd', 0, 1) .
        SelectStr('SELECT id, name as nm FROM akb_model ORDER BY name', 'model_id_upd', 0, 1) .
        '<input name="tov" value="' . $tov . '" type="hidden" /></td><td></td><td></td><td></td><td></td><td></td>'
            . '<td><input name="delete_selected" type="submit" onclick="return confirm(\'Вы уверены, что хотите удалить?\');" value="уд" /></td></tr>';

} else {
    $str .= '<form enctype="multipart/form-data" action="/adm/sp-tov-edit/0/0/" method="post">
    <tr><td>
    <input name="ids" type="hidden" value="" /></td><td></td>
    <td class="idtov"><input name="add" type="submit" value="доб" /></td><td class="name">' .
        SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id=' . $tov . ' order by tb3_nm', 'tab3_id',
            0, 0) .
        '<input name="tov" value="' . $tov . '" type="hidden" /></td><td></td><td></td>' .
        '<td></td><td></td><td></td><td></td><td></td></tr></form>
    <form enctype="multipart/form-data" action="/adm/sp-tov-mass/" method="post" target="_blank">
    <tr><td><input name="upd" type="submit" value="об" />
    <input name="ids" type="hidden" value="" /></td><td></td>
    <td class="idtov"><input name="print" type="submit" value="печать" /></td><td class="name" colspan="2">' .
        SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id=' . $tov . ' order by tb3_nm',
            'tab3_id_upd', 0, 1, ' onchange="getModelsColors(\'tab3_id_upd\', \'tab4_id_upd\', \'tab2_id_upd\')"') .
        SelectStr('select tb4_id as id,tb4_nm as nm from tab4 where tb4_tov_id=' . $tov . ' order by tb4_nm',
            'tab4_id_upd', 0, 1) .
        SelectStr('select tb2_id as id,tb2_nm as nm from tab2 where tb2_tov_id=' . $tov . ' order by tb2_nm',
            'tab2_id_upd', 0, 1) .
        '<input name="tov" value="' . $tov . '" type="hidden" /></td><td></td><td></td><td></td>' .
        '<td></td><td></td><td><input name="delete_selected" type="submit" onclick="return confirm(\'Вы уверены, что хотите удалить?\');" value="уд" /></td></tr>';
}

$result = query($sql);
if ($result['result'] === false) {
    $str .= '<p>Получение списка товаров. Ошибка обращения к БД. ' . dbLastErrorToString($result['error']) . '</a>';
}
$nomid = 0;
$path = '';
switch ($tov) {
    case 1:
        $path = 'tyres';
        break;
    case 2:
        $path = 'discs';
        break;
    case 3:
        $path = 'akb';
        break;
}
foreach ($result['data'] as $nom) {
    $str .= '<tr class="skld"><td class="chb"><input type="checkbox" value="" name="idtov-' .
        $nom->r_id . '" /></td><td style="width:30px"><a href="/adm/sp-tov-edit/' .
        $tov . '/' . $nom->r_id . '/" target="_blank">Ред</a></td><td>' .
        $nom->r_id . '</td><td class="nm">' . $nom->r_name . '</td><td class="nm">' .
        $nom->r_url . '</td><td>' . $nom->snm . '</td><td>' . $nom->cnt . '</td><td>' .
        $nom->price . '</td><td>' . $nom->wrk . '</td><td>' .
        ($nom->imgname ? '<a class="gallery" title="" href="' . $imgLinkPrefix . '/images/tovar/' . $path . '/' .
            $nom->imgname . '">Изобр</a>' : '') . '</td><td><a href="/adm/sp-tov-edit/' . $tov . '/' . $nom->r_id .
        '/del/" onclick="return confirm(\'Вы уверены, что хотите удалить?\')">Уд</a>' .
        '</td></tr>';
}

$str .= '</table></form>';
if ($allcn > 1) {

    $str .= "<div class=\"sp-pages\"><span>Страницы:</span> " . $strZak . "</div>";
}
$str .= "</div>";
$str .= '</td></tr></table>';
