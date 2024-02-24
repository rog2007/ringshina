<?php

// функции =====================================================================
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
// /функции ====================================================================

$str .= '<table><tr><td style="width:140px;vertical-align:top">';
include_once("lmenu.php");
$str .= '</td><td style="vertical-align:top">';
// добавление записи в БД ======================================================
if ($_POST['add']) {

    if ($arg[0] == 3) {

        if (trim($_POST["id_brand"]) == 0) {

            header('Location: /sp-tov/akb/er1/');
        } else {

            mysql_query("insert into akb_tovar (id_brand) values (" . $_POST["id_brand"] . ")");
            header('Location: /adm/sp-tov-edit/' . $_POST['tov'] . '/' . mysql_insert_id() . '/');
        }
    } else {

        if (trim($_POST["tab3_id"]) == 0) {

            header('Location: /sp-tov/' . ($_POST['tov'] == 1 ? 'shini' : 'diski') . '/er1/');
        } else {

            mysql_query("insert into total (tab1_id,tab3_id) values (" . $_POST["tov"] . "," . $_POST["tab3_id"] . ")");
            header('Location: /adm/sp-tov-edit/' . $_POST['tov'] . '/' . mysql_insert_id() . '/');
        }
    }
}
// /добавление записи в БД =====================================================
// удаление записи из БД =======================================================
if ($arg[2] == 'del') {

    if ($arg[0] == 3) {

        mysql_query("DELETE FROM akb_tovar where id = " . $arg[1]);
        mysql_query("DELETE FROM akb_suppl where id_tov = " . $arg[1]);
    } else {

        mysql_query('DELETE FROM total WHERE total_id = ' . $arg[1]);
        mysql_query('DELETE FROM total_suppl WHERE id_tov = ' . $arg[1]);
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
// /удаление записи из БД ======================================================
// удаление изображения для определенной позиции ===============================
if ($arg[2] == 'del-img') {

    $res = mysql_query("select tovimg from total where total_id=" . $arg[1]);
    $rs_img = mysql_fetch_object($res);
    $fileName = $rs_img->tovimg;
    mysql_query("update total set tovimg = '' where total_id=" . $arg[1]);
    $dirrect = $_SERVER["DOCUMENT_ROOT"] . "/images/tovar/";
    $path1 = ($arg[0] == 1 ? "tyres" : "discs");
    if ($fileName) {

        deleteImages($dirrect, $path1, $fileName);
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}
// /удаление изображения для определенной позиции ==============================
switch ($arg[0]) {
    case 1:
        $str_error = '';
        $strContent = '';
        if (isset($_POST["save"])) {

            $rof = isset($_POST["rof"]) ? 1 : 0;
            $selTyre = $dbcon->prepare('SELECT COUNT(*) AS cn FROM total WHERE tab3_id=:tab3_id AND tab4_id=:tab4_id' .
                ' AND w_id=:w_id AND h_id=:h_id AND tab6_id=:tab6_id AND tab7_id=:tab7_id AND tab8_id=:tab8_id ' .
                ' AND rof=:rof AND total_id<>:total_id');
            $selTyre->bindParam(':tab3_id', $_POST["tab3_id"], PDO::PARAM_INT);
            $selTyre->bindParam(':tab4_id', $_POST["tab4_id"], PDO::PARAM_INT);
            $selTyre->bindParam(':w_id', $_POST["w_id"], PDO::PARAM_INT);
            $selTyre->bindParam(':h_id', $_POST["h_id"], PDO::PARAM_INT);
            $selTyre->bindParam(':tab6_id', $_POST["tab6_id"], PDO::PARAM_INT);
            $selTyre->bindParam(':tab7_id', $_POST["tab7_id"], PDO::PARAM_INT);
            $selTyre->bindParam(':tab8_id', $_POST["tab8_id"], PDO::PARAM_INT);
            $selTyre->bindParam('rof', $rof, PDO::PARAM_INT);
            $selTyre->bindParam(':total_id', $arg[1], PDO::PARAM_INT);
            if ($selTyre->execute()) {

                $rs_check = $selTyre->fetch(PDO::FETCH_OBJ);
                if ($rs_check->cn == 0) {

                    $cnt = (int)$_POST["cnt"];
                    $price = (int)$_POST["price"];
                    $wrk = isset($_POST["wrk"]) ? 1 : 0;
                    $updateTyre = $dbcon->prepare('UPDATE total SET all_name=:all_name, url=:url, tab2_id=:tab2_id, ' .
                        'tab3_id=:tab3_id, tab4_id=:tab4_id, w_id=:w_id, h_id=:h_id, tab6_id=:tab6_id, tab7_id=:tab7_id, ' .
                        'tab8_id=:tab8_id, tab10_id=:tab10_id, cnt=:cnt, price=:price, rof=:rof, wrk=:wrk, omolog=:omolog ' .
                        'WHERE total_id=:total_id');
                    $updateTyre->bindParam(':all_name', $_POST["all_name"]);
                    $updateTyre->bindParam(':url', $_POST["url"]);
                    $updateTyre->bindParam(':tab2_id', $_POST["tab2_id"], PDO::PARAM_INT);
                    $updateTyre->bindParam(':tab3_id', $_POST["tab3_id"], PDO::PARAM_INT);
                    $updateTyre->bindParam(':tab4_id', $_POST["tab4_id"], PDO::PARAM_INT);
                    $updateTyre->bindParam(':w_id', $_POST["w_id"], PDO::PARAM_INT);
                    $updateTyre->bindParam(':h_id', $_POST["h_id"], PDO::PARAM_INT);
                    $updateTyre->bindParam(':tab6_id', $_POST["tab6_id"], PDO::PARAM_INT);
                    $updateTyre->bindParam(':tab7_id', $_POST["tab7_id"], PDO::PARAM_INT);
                    $updateTyre->bindParam(':tab8_id', $_POST["tab8_id"], PDO::PARAM_INT);
                    $updateTyre->bindParam(':tab10_id', $_POST["tab10_id"], PDO::PARAM_INT);
                    $updateTyre->bindParam('cnt', $cnt, PDO::PARAM_INT);
                    $updateTyre->bindParam('price', $price, PDO::PARAM_INT);
                    $updateTyre->bindParam('rof', $rof, PDO::PARAM_INT);
                    $updateTyre->bindParam('wrk', $wrk, PDO::PARAM_INT);
                    $updateTyre->bindParam(':omolog', $_POST["omolog"], PDO::PARAM_INT);
                    $updateTyre->bindParam(':total_id', $arg[1], PDO::PARAM_INT);
                    if ($updateTyre->execute()) {

                        $updateTyreName = $dbcon->prepare("UPDATE total LEFT JOIN " .
                            "(SELECT total_id AS tid, tb3_nm AS t3nm, tb4_nm AS t4nm, IF(omolog>'', CONCAT(' ', omolog), '') AS om, " .
                            "profw.name AS t5nm, IF(h_id<>0, CONCAT('/', profh.name), '') AS t5h, tb6_nm AS t6_nm, IFNULL(t7.tb7_nm,'') AS mn7, " .
                            "IFNULL(t8.tb8_nm, '') AS mn8, IF(rof=1, CONCAT(' ', ifnull(run_flat.var, 'run flat')), '') AS run " .
                            'FROM total LEFT JOIN tab3 ON tab3_id = tb3_id ' .
                            'LEFT JOIN tab4 ON tab4_id = tb4_id ' .
                            'LEFT JOIN profw ON w_id = profw.id ' .
                            'LEFT JOIN profh ON h_id = profh.id ' .
                            'LEFT JOIN tab6 ON tab6_id = tb6_id ' .
                            'LEFT JOIN tab7 AS t7 ON tab7_id = t7.tb7_id ' .
                            'LEFT JOIN tab8 AS t8 ON tab8_id = t8.tb8_id ' .
                            'LEFT JOIN tab9 ON t4sh = tb9_id ' .
                            'LEFT JOIN run_flat ON run_flat.br = tab3_id ' .
                            'WHERE total.total_id=:total_id) AS tb1 ON total_id=tb1.tid ' .
                            "SET all_name = concat(t3nm, ' ', t4nm, om, ' ', t5nm, t5h, ' ', t6_nm, ' ', mn7, mn8, run) " .
                            'WHERE total.total_id=:total_id');
                        $updateTyre->bindParam(':total_id', $arg[1], PDO::PARAM_INT);
                        if (!$updateTyre->execute()) {
                            $str_error .= '<p class="error">Ошибка выполнения запроса обновления наименования</p>';
                        }
                    } else {
                        $str_error .= '<p class="error">Ошибка выполнения запроса обновления</p>';
                    }
                } else {
                    $str_error .= '<p class="error">Обновление данных было остановленно, т.к. запись уже существует</p>';
                }
            } else {
                $str_error .= '<p class="error">Ошибка выполнения запроса при проверке повторной записи.</p>';
            }
        }
        $selTyre = $dbcon->prepare('SELECT suppl.name AS spname, total_id, tovimg, all_name, tab1_id, tab2_id, ' .
            'tab3_id, tab4_id, w_id, h_id, tab6_id, tab7_id, tab8_id, t4sh, tab10_id, cnt, price, rof, omolog, wrk, total.url ' .
            'FROM total LEFT JOIN tab4 ON tab4_id = tb4_id LEFT JOIN suppl ON suppl.id = spid WHERE total_id=:total_id');
        $selTyre->bindParam(':total_id', $arg[1], PDO::PARAM_INT);
        if ($selTyre->execute()) {

            $rs = $selTyre->fetch(PDO::FETCH_OBJ);
            $strContent .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control1"><input name="all_name" type="text" value="' . $rs->all_name . '" /></td></tr>
      <tr><td class="title">URL</td><td class="control1"><input name="url" type="text" value="' . $rs->url . '" /></td></tr>
      <tr><td class="title">Тип товара</td><td class="control1">' . SelectStr('select tb1_id as id,tb1_nm as nm from tab1 order by tb1_id',
                    'tab1_id', $rs->tab1_id, 0) . '</td></tr>
      <tr><td class="title">Бренд</td><td class="control1">' . SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id=' . $rs->tab1_id . ' order by tb3_nm',
                    'tab3_id', $rs->tab3_id, 1) . '</td></tr>
      <tr><td class="title">Модель</td><td class="control1">' . SelectStr('select tb4_id as id,tb4_nm as nm from tab4 where tb4_tov_id=' . $rs->tab1_id . ($rs->tab3_id > 0 ? ' and brand_id=' . $rs->tab3_id : '') . ' order by tb4_nm',
                    'tab4_id', $rs->tab4_id, 1) . '</td></tr>
      <tr><td class="title">Ширина пр.</td><td class="control1">' . SelectStr('select id,name as nm from profw order by name*1',
                    'w_id', $rs->w_id, 1) . '</td></tr>
      <tr><td class="title">Высота пр.</td><td class="control1">' . SelectStr('select id,name as nm from profh order by name*1',
                    'h_id', $rs->h_id, 1) . '</td></tr>
      <tr><td class="title">Диаметр</td><td class="control1">' . SelectStr('select tb6_id as id,tb6_nm as nm from tab6 where tb6_tov_id=' . $rs->tab1_id . ' order by tb6_nm',
                    'tab6_id', $rs->tab6_id, 1) . '</td></tr>
      <tr><td class="title">И. Нагрузки</td><td class="control1">' . SelectStr('select tb7_id as id,tb7_nm as nm from tab7 where tb7_tov_id=' . $rs->tab1_id . ' order by tb7_nm',
                    'tab7_id', $rs->tab7_id, 1) . '</td></tr>
      <tr><td class="title">И. Скорости</td><td class="control1">' . SelectStr('select tb8_id as id,tb8_nm as nm from tab8 where tb8_tov_id=' . $rs->tab1_id . ' order by tb8_nm',
                    'tab8_id', $rs->tab8_id, 1) . '</td></tr>
      <tr><td class="title">Шипованность</td><td class="control1">' . ($rs->t4sh == 3 ? 'да' : '') . '</td></tr>
      <tr><td class="title">Сезонность</td><td class="control1">' . SelectStr('select tb10_id as id,tb10_nm as nm from tab10 where tb10_tov_id=' . $rs->tab1_id . ' order by tb10_nm',
                    'tab10_id', $rs->tab10_id, 1) . '</td></tr>
      <tr><td class="title">Авто</td><td class="control1">' . SelectStr('select tb2_id as id,tb2_nm as nm from tab2 where tb2_tov_id=' . $rs->tab1_id . ' order by tb2_nm',
                    'tab2_id', $rs->tab2_id, 1) . '</td></tr>
      <tr><td class="title">RunFlat</td><td class="control1"><input type="checkbox" name="rof" ' . ($rs->rof ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td class="title">Омологация</td><td class="control1">' . SelectStr('select om as id,om as nm from omolog where omvis=1 order by om',
                    'omolog', $rs->omolog, 1) . '</td></tr>
      <tr><td class="title">Видимый</td><td class="control1"><input type="checkbox" name="wrk" ' . ($rs->wrk ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td class="title">Цена</td><td class="control1"><input name="price" type="text" value="' . $rs->price . '" /></td></tr>
      <tr><td class="title">Количество</td><td class="control1"><input name="cnt" type="text" value="' . $rs->cnt . '" /></td></tr>
      <tr><td class="title">Поставщик</td><td class="control1">' . $rs->spname . '</td></tr>
      <tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>
      <table class="colors">
        <tr>
          <td class="img">' . ($rs->tovimg ? '<img src="/images/tovar/tyres/' . $rs->tovimg . '" />' : 'добавить') . '</td>
          <td><form enctype="multipart/form-data" action="/adm/load_pic.php" method="post" class="load">
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
        <input type="hidden" name="tovar" value="' . $rs->tab1_id . '|' . $rs->total_id . '" />
        <input name="userfile" type="file" class="file"/><input type="submit" value="Загрузить"  class="but"/></form></td>
        <td><a href="/adm/sp-tov-edit/' . $rs->tab1_id . '/' . $rs->total_id . '/del-img/">Удалить</a></td></tr></table>';
            $res_sup = mysql_query('select suppl.name as splnm,suppl_name,cnt_sup,prs_sup,priceb,id_tov_sup,id_sup from total_suppl left join suppl on id_sup=suppl.id where id_tov=' . $arg[1]);
            $strContent .= '<table class="ed"><tr><td>Доб</td><td>Поставщик</td><td>Наименование Поставщик</td><td>ID поставщик</td><td>Кол-во</td><td>Цена закупки</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/15/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
      <td>' . SelectStr('select id,name as nm from suppl order by id', 'id_sup', 0,
                    1) . '</td><td></td><td><input name="id_tov_sup" type="text" value="" /></td><td><input name="id_tov" type="hidden" value="' . $arg[1] . '" /><input name="tab1" type="hidden" value="1" /></td><td></td><td></td></tr></form>';
            while ($rs_sup = mysql_fetch_object($res_sup))
                $strContent .= "<tr><td style='width:30px'></td>
        <td>" . $rs_sup->splnm . "</td><td class=\"nm\">" . $rs_sup->suppl_name . "</td><td class=\"nm\">" . $rs_sup->id_tov_sup . "</td>
        <td class=\"nm\">" . $rs_sup->cnt_sup . "</td><td class=\"nm\">" . $rs_sup->priceb . "</td>
        <td><a href=\"/adm/sp-edit/15/" . $rs_sup->id_sup . "/del/" . urlencode($rs_sup->id_tov_sup) . "/\">Уд</a></td></tr>";
            $strContent .= '</table>';
            $strContent .= "<h3>Возможно повторные записи</h3><table class=\"ed\"><tr class=\"head\"><td>Доб</td><td class=\"idtov\">ID</td><td class=\"name\">Наименование</td><td></td></tr>";

            $doubleTyre = $dbcon->prepare('SELECT tl2.total_id, tl2.all_name FROM total as tl1 ' .
                'LEFT JOIN total AS tl2 ON tl1.tab1_id=tl2.tab1_id AND tl1.tab3_id=tl2.tab3_id AND tl1.tab4_id=tl2.tab4_id ' .
                'AND tl1.w_id=tl2.w_id AND tl1.h_id=tl2.h_id AND tl1.tab6_id=tl2.tab6_id AND tl1.tab7_id=tl2.tab7_id ' .
                'AND tl1.tab8_id=tl2.tab8_id AND tl1.rof=tl2.rof AND tl1.omolog=tl2.omolog ' .
                'WHERE tl1.total_id=:total_id AND tl2.total_id<>:total_id ORDER BY all_name');
            $doubleTyre->bindParam(':total_id', $arg[1], PDO::PARAM_INT);
            if ($doubleTyre->execute()) {

                while ($nom = $doubleTyre->fetch(PDO::FETCH_OBJ)) {
                    $strContent .= '<tr class="skld"><td style="width:30px"><a href="/adm/sp-tov-edit/' . $rs->tab1_id . '/' .
                        $nom->total_id . '/">Ред</a></td><td>' . $nom->total_id . '</td><td class="nm">' . $nom->all_name .
                        '</td><td><a href="/adm/sp-tov-edit/' . $rs->tab1_id . '/' . $nom->total_id . '/del/">Уд</a></td></tr>';
                }
                $strContent .= '</table>';
            } else {
                $str_error .= '<p class="error">Ошибка выполнения запроса: "Получение скиска повторных записей".</p>';
            }
        } else {
            $str_error .= '<p class="error">Ошибка выполнения запроса: "Получение данных о текущей позиции".</p>';
        }
        $str .= $str_error . $strContent;
        break;
    case 2:
        $str_error = '';
        if (isset($_POST["save"])) {
            $res = mysql_query("select count(*) as cn from total where tab2_id=" . $_POST["tab2_id"] . " and tab3_id=" . $_POST["tab3_id"] . " and
          tab4_id=" . $_POST["tab4_id"] . " and tab5_id=" . $_POST["tab5_id"] . " and tab6_id=" . $_POST["tab6_id"] . " and tab7_id=" . $_POST["tab7_id"] . " and
          tab8_id=" . $_POST["tab8_id"] . " and tab9_id=" . $_POST["tab9_id"] . " and tab12_id=" . $_POST["tab12_id"] . " and total_id<>" . $arg[1]);
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {
                mysql_query("update total set all_name='" . mysql_real_escape_string($_POST["all_name"]) . "',url='" .
                    $_POST["url"] . "',tab2_id=" . $_POST["tab2_id"] . ",tab3_id=" . $_POST["tab3_id"] .
                    ", tab4_id=" . $_POST["tab4_id"] . ",tab5_id=" . $_POST["tab5_id"] .
                    ",tab6_id=" . $_POST["tab6_id"] . ",tab7_id=" . $_POST["tab7_id"] . ", tab8_id=" .
                    $_POST["tab8_id"] . ",tab9_id=" . $_POST["tab9_id"] . ",tab10_id=" .
                    $_POST["tab10_id"] . ",tab12_id=" . $_POST["tab12_id"] . ",cnt=" .
                    ((int)$_POST["cnt"]) . ", price=" . ((int)$_POST["price"]) . ",wrk=" .
                    ($_POST["wrk"] ? "1" : "0") . ",tovdsc='" . mysql_real_escape_string($_POST["tovdsc"]) . "' where total_id=" . $arg[1]);
                mysql_query("update total LEFT JOIN(SELECT total_id AS tid, ifnull( concat( ' ', tab2.tb2_nm ) , '' ) t2mn, tb3_nm AS t3nm, tb4_nm AS t4nm, tb5_nm AS t5nm, tb6_nm AS t6_nm, t7.tb7_nm AS mn7, t8.tb8_nm AS mn8, ifnull( concat( ' ET', tb9_nm ) , '' ) AS mn9, ifnull( concat( ' D', tb12_nm ) , '' ) AS mn12
          FROM total LEFT JOIN tab2 ON tab2_id = tb2_id LEFT JOIN tab3 ON tab3_id = tb3_id LEFT JOIN tab4 ON tab4_id = tb4_id LEFT JOIN tab5 ON tab5_id = tb5_id
          LEFT JOIN tab6 ON tab6_id = tb6_id LEFT JOIN tab7 AS t7 ON tab7_id = t7.tb7_id LEFT JOIN tab8 AS t8 ON tab8_id = t8.tb8_id LEFT JOIN tab9 ON tab9_id = tb9_id
          LEFT JOIN tab12 ON tab12_id = tb12_id WHERE total.total_id=" . $arg[1] . ") AS tb1 ON total_id = tb1.tid set all_name=concat( t3nm, ' ', t4nm, ' ', t5nm, 'x', t6_nm, ' ', mn7, '/', mn8, mn9, mn12, t2mn )
          where total.total_id=" . $arg[1]);
            } else {
                $str_error .= '<p class="error">Запись уже существует</p>';
            }
        }
        $res = mysql_query('select total_id, all_name,tab1_id,tab2_id,tab3_id,tab4_id,
        tab5_id,tab6_id,tab7_id,tab8_id,tab9_id,tab10_id,tab12_id,cnt,price,wrk, suppl.name as spname,
        url, tovdsc from total LEFT JOIN suppl on suppl.id = spid where total_id=' . $arg[1]);
        $rs = mysql_fetch_object($res);
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control1"><input name="all_name" type="text" value="' . $rs->all_name . '" /></td></tr>
      <tr><td class="title">URL</td><td class="control1"><input name="url" type="text" value="' . $rs->url . '" /></td></tr>
      <tr><td class="title">Тип товара</td><td class="control1">' . SelectStr('select tb1_id as id,tb1_nm as nm from tab1 order by tb1_id',
                'tab1_id', $rs->tab1_id, 0) . '</td></tr>
      <tr><td class="title">Бренд</td><td class="control1">' . SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id=' . $rs->tab1_id . ' order by tb3_nm',
                'tab3_id', $rs->tab3_id, 1) . '</td></tr>
      <tr><td class="title">Модель</td><td class="control1">' . SelectStr('select tb4_id as id,tb4_nm as nm from tab4 where tb4_tov_id=' . $rs->tab1_id . ($rs->tab3_id > 0 ? ' and brand_id=' . $rs->tab3_id : '') . ' order by tb4_nm',
                'tab4_id', $rs->tab4_id, 1) . '</td></tr>
      <tr><td class="title">Ширина</td><td class="control1">' . SelectStr('select tb5_id as id,tb5_nm as nm from tab5 where tb5_tov_id=' . $rs->tab1_id . ' order by tb5_nm',
                'tab5_id', $rs->tab5_id, 1) . '</td></tr>
      <tr><td class="title">Диаметр</td><td class="control1">' . SelectStr('select tb6_id as id,tb6_nm as nm from tab6 where tb6_tov_id=' . $rs->tab1_id . ' order by tb6_nm',
                'tab6_id', $rs->tab6_id, 1) . '</td></tr>
      <tr><td class="title">Отверстия</td><td class="control1">' . SelectStr('select tb7_id as id,tb7_nm as nm from tab7 where tb7_tov_id=' . $rs->tab1_id . ' order by tb7_nm',
                'tab7_id', $rs->tab7_id, 1) . '</td></tr>
      <tr><td class="title">PCD</td><td class="control1">' . SelectStr('select tb8_id as id,tb8_nm as nm from tab8 where tb8_tov_id=' . $rs->tab1_id . ' order by tb8_nm',
                'tab8_id', $rs->tab8_id, 1) . '</td></tr>
      <tr><td class="title">Выслет</td><td class="control1">' . SelectStr('select tb9_id as id,tb9_nm as nm from tab9 where tb9_tov_id=' . $rs->tab1_id . ' order by tb9_nm',
                'tab9_id', $rs->tab9_id, 1) . '</td></tr>
      <tr><td class="title">Тип</td><td class="control1">' . SelectStr('select tb10_id as id,tb10_nm as nm from tab10 where tb10_tov_id=' . $rs->tab1_id . ' order by tb10_nm',
                'tab10_id', $rs->tab10_id, 1) . '</td></tr>
      <tr><td class="title">Ступица</td><td class="control1">' . SelectStr('select tb12_id as id,tb12_nm as nm from tab12 where tb12_tov_id=' . $rs->tab1_id . ' order by tb12_nm',
                'tab12_id', $rs->tab12_id, 1) . '</td></tr>
      <tr><td class="title">Цвет</td><td class="control1">' . SelectStr('select tb2_id as id,tb2_nm as nm from tab2 where tb2_tov_id=' . $rs->tab1_id . ($rs->tab3_id > 0 ? ' and brid=' . $rs->tab3_id : '') . ' order by tb2_nm',
                'tab2_id', $rs->tab2_id, 1) . '</td></tr>
      <tr><td class="title">Описание</td><td class="control"><textarea name="tovdsc" id="tov_descr">' . $rs->tovdsc . '</textarea></td></tr>
      <tr><td class="title">Видимый</td><td class="control1"><input type="checkbox" name="wrk" ' . ($rs->wrk ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td class="title">Цена</td><td class="control1"><input name="price" type="text" value="' . $rs->price . '" /></td></tr>
      <tr><td class="title">Количество</td><td class="control1"><input name="cnt" type="text" value="' . $rs->cnt . '" /></td></tr>
      <tr><td class="title">Поставщик</td><td class="control1">' . $rs->spname . '</td></tr>
      <tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        $res_sup = mysql_query('select suppl.name as splnm,suppl_name,cnt_sup,prs_sup,priceb,id_tov_sup,id_sup from total_suppl left join suppl on id_sup=suppl.id where id_tov=' . $arg[1]);
        $str .= '<table class="ed"><tr><td>Доб</td><td>Поставщик</td><td>Наименование Поставщик</td><td>ID поставщик</td><td>Кол-во</td><td>Цена закупки</td><td></td></tr>
      <form enctype="multipart/form-data" action="/adm/sp-edit/15/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
      <td>' . SelectStr('select id,name as nm from suppl order by id', 'id_sup', 0,
                1) . '</td><td></td><td><input name="id_tov_sup" type="text" value="" /></td><td><input name="id_tov" type="hidden" value="' . $arg[1] . '" /><input name="tab1" type="hidden" value="1" /></td><td></td><td></td></tr></form>';
        while ($rs_sup = mysql_fetch_object($res_sup))
            $str .= "<tr><td style='width:30px'></td>
        <td>" . $rs_sup->splnm . "</td><td class=\"nm\">" . $rs_sup->suppl_name . "</td><td class=\"nm\">" . $rs_sup->id_tov_sup . "</td>
        <td class=\"nm\">" . $rs_sup->cnt_sup . "</td><td class=\"nm\">" . $rs_sup->priceb . "</td><td><a href=\"/adm/sp-edit/15/" . $rs_sup->id_sup . "/del/" . urlencode($rs_sup->id_tov_sup) . "/\">Уд</a></td></tr>";
        $str .= '</table>';

        $str .= "<h3>Возможно повторные записи</h3><table class=\"ed\"><tr class=\"head\"><td>Доб</td><td class=\"idtov\">ID</td><td class=\"name\">Наименование</td><td></td></tr>";
        $sql = "SELECT tl2.total_id,tl2.all_name from total as tl1 left join total as tl2 on tl1.tab1_id=tl2.tab1_id and tl1.tab2_id=tl2.tab2_id and tl1.tab3_id=tl2.tab3_id and
      tl1.tab4_id=tl2.tab4_id and tl1.tab5_id=tl2.tab5_id and tl1.tab6_id=tl2.tab6_id and tl1.tab7_id=tl2.tab7_id and tl1.tab8_id=tl2.tab8_id and tl1.tab9_id=tl2.tab9_id
      and tl1.tab12_id=tl2.tab12_id where tl1.total_id=" . $arg[1] . " and tl2.total_id<>" . $arg[1] . " order by all_name";
        $result = mysql_query($sql);
        $nomid = 0;
        while ($nom = mysql_fetch_array($result))
            $str .= "<tr class=\"skld\"><td style='width:30px'><a href=\"/adm/sp-tov-edit/" . $rs->tab1_id . "/" . $nom[0] . "/\">Ред</a></td><td>" . $nom[0] . "</td><td class=\"nm\">" . $nom[1] . "</td><td><a href=\"/adm/sp-tov-edit/" . $rs->tab1_id . "/" . $nom[0] . "/del/\">Уд</a></td></tr>";
        $str .= '</table>';
        break;
    case 3:
        $str_error = '';
        if (isset($_POST["save"])) {

            $res = mysql_query("SELECT count(*) AS cn FROM akb_tovar
        WHERE id_model = " . $_POST["id_model"] . " and
          id_volt = " . $_POST["id_volt"] . " and id_v = " . $_POST["id_v"] .
                " and rvrt = " . $_POST["rvrt"] . " and klem = " . $_POST["klem"] . " and total_id <> " . $arg[1]);
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {

                mysql_query("update akb_tovar set full_name = '" . mysql_real_escape_string($_POST["full_name"]) . "', url='" .
                    $_POST["url"] . "', id_model = " . $_POST["id_model"] .
                    ", id_volt = " . $_POST["id_volt"] . ", id_v = " . $_POST["id_v"] .
                    ", rvrt = " . $_POST["rvrt"] . ", klem = " . $_POST["klem"] . ", cnt = " . ((int)$_POST["cnt"]) .
                    ", price=" . ((int)$_POST["price"]) . ", vis=" . ($_POST["vis"] ? "1" : "0") .
                    ", decr = '" . mysql_real_escape_string($_POST["decr"]) . "' where id = " . $arg[1]);
                mysql_query("UPDATE akb_tovar
    LEFT JOIN akb_model ON id_model = akb_model.id
    LEFT JOIN akb_volt ON akb_volt.id = id_volt
    LEFT JOIN akb_v ON akb_v.id = id_v
    LEFT JOIN akb_rvrt ON akb_rvrt.id = rvrt
    LEFT JOIN akb_klemy ON akb_klemy.id = klem
    SET full_name = concat('АКБ ', akb_volt.name, 'В ', akb_v.name, ' А/ч',
      ' ', akb_model.name, ' ', akb_rvrt.short_name, akb_klemy.short_name)
            WHERE akb_tovar.id = " . $arg[1]);
            } else {
                $str_error .= '<p class="error">Запись уже существует</p>';
            }
        }

        $res = mysql_query('select * from akb_tovar where id = ' . $arg[1]);
        $rs = mysql_fetch_object($res);
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control1"><input name="full_name" type="text" value="' . $rs->full_name . '" /></td></tr>
      <tr><td class="title">URL</td><td class="control1"><input name="url" type="text" value="' . $rs->url . '" /></td></tr>
      <tr><td class="title">Модель</td><td class="control1">' . SelectStr('SELECT id, name as nm FROM akb_model ORDER BY name',
                'id_model', $rs->id_model, 1) . '</td></tr>
      <tr><td class="title">Вольтаж</td><td class="control1">' . SelectStr('SELECT id, name as nm FROM akb_volt ORDER BY name',
                'id_volt', $rs->id_volt, 1) . '</td></tr>
      <tr><td class="title">Объем</td><td class="control1">' . SelectStr('SELECT id, name as nm FROM akb_v ORDER BY name',
                'id_v', $rs->id_v, 1) . '</td></tr>
      <tr><td class="title">Обратный</td><td class="control1">' . SelectStr('SELECT id, name as nm FROM akb_rvrt ORDER BY id DESC',
                'rvrt', $rs->rvrt, 0) . '</td></tr>
      <tr><td class="title">Клеммы</td><td class="control1">' . SelectStr('SELECT id, name as nm FROM akb_klemy ORDER BY id',
                'klem', $rs->klem, 0) . '</td></tr>
      <tr><td class="title">Описание</td><td class="control"><textarea name="decr" id="decr">' . $rs->decr . '</textarea></td></tr>
      <tr><td class="title">Отображать</td><td class="control1"><input type="checkbox" name="vis" ' . ($rs->vis ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td class="title">Цена</td><td class="control1"><input name="price" type="text" value="' . $rs->price . '" /></td></tr>
      <tr><td class="title">Количество</td><td class="control1"><input name="cnt" type="text" value="' . $rs->cnt . '" /></td></tr>
      <tr><td class="but"><input name="upd_url" type="submit" value="Название" /><input name="upd_url" type="submit" value="URL" /></td>
      <td class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';

        $res_sup = mysql_query('select suppl.name as splnm, suppl_name, cnt_sup, prs_sup,
        priceb, id_tov_sup, id_sup from akb_suppl left join suppl on id_sup = suppl.id
        where id_tov = ' . $arg[1]);
        $str .= '<table class="ed"><tr><td>Доб</td><td>Поставщик</td><td>Наименование у поставщика</td>
        <td>ID у поставщика</td><td>Кол-во</td><td>Цена закупки</td><td></td></tr>
        <form enctype="multipart/form-data" action="/adm/sp-edit/45/0/" method="post"><tr><td><input name="add" type="submit" value="доб" /></td>
        <td>' . SelectStr('select id, name as nm from suppl order by id', 'id_sup', 0, 1) . '</td>
        <td></td><td><input name="id_tov_sup" type="text" value="" /></td>
        <td><input name="id_tov" type="hidden" value="' . $arg[1] . '" />
        <input name="tab1" type="hidden" value="3" /></td>
        <td></td><td></td></tr></form>';
        while ($rs_sup = mysql_fetch_object($res_sup))
            $str .= "<tr><td style='width:30px'></td>
        <td>" . $rs_sup->splnm . "</td><td class=\"nm\">" . $rs_sup->suppl_name .
                "</td><td class=\"nm\">" . $rs_sup->id_tov_sup . "</td>
        <td class=\"nm\">" . $rs_sup->cnt_sup . "</td><td class=\"nm\">" . $rs_sup->priceb .
                "</td><td><a href=\"/adm/sp-edit/45/" . $rs_sup->id_sup . "/del/" .
                urlencode($rs_sup->id_tov_sup) . "/\">уд</a></td></tr>";
        $str .= '</table>';
       break;
}
$str .= '</td></tr></table>';