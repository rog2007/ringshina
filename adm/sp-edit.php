<?php

// error_reporting(E_ALL);
// ini_set('display_errors', '1');

function updateTyresDiametr($modelId = null) {

    mysql_query("update total LEFT JOIN tab4 AS t4 ON t4.tb4_id = tab4_id
    LEFT JOIN tab6 AS t6 ON t6.tb6_id = tab6_id LEFT JOIN tab6 AS tt6 ON tt6.tb6_nm = CONCAT( REPLACE(t6.tb6_nm, 'C', ''), t4c )
    set tab6_id = tt6.tb6_id" . ($modelId ? " WHERE tab4_id = " . $modelId : ""));
    return mysql_affected_rows();
}

function updateTyresSeasSh($modelId = null) {

    mysql_query("update total LEFT JOIN tab4 ON tb4_id=tab4_id set tab10_id=t4ses,tab9_id=t4sh
   WHERE tb4_nm IS NOT NULL " . ($modelId ? " AND tab4_id = " . $modelId : ""));
    return mysql_affected_rows();
}

function updateTyresName($modelId = null) {

    mysql_query("update total LEFT JOIN tab3 ON tab3_id = tb3_id LEFT JOIN tab4 ON tab4_id = tb4_id
      LEFT JOIN profw ON w_id = profw.id LEFT JOIN profh ON h_id = profh.id LEFT JOIN tab6 ON tab6_id = tb6_id
      LEFT JOIN tab7 AS t7 ON tab7_id = t7.tb7_id LEFT JOIN tab8 AS t8 ON tab8_id = t8.tb8_id LEFT JOIN tab9 ON t4sh = tb9_id
      left join run_flat on run_flat.br=tab3_id
      set all_name=concat(tb3_nm,' ',tb4_nm,IF(omolog>'',concat(' ',omolog),''),' ',
      profw.name,IF(ifnull(profh.name,'')>'',concat('/',profh.name),''),' ',tb6_nm,' ',ifnull(t7.tb7_nm,''),
      ifnull(t8.tb8_nm,''),IF(rof=1,concat(' ',ifnull(run_flat.var,'run flat')),''))" .
            ($modelId ? " WHERE total.tab4_id = " . $modelId : ""));
    return mysql_affected_rows();
}

function updateAkbName($modelId = null) {
//LEFT JOIN akb_brand ON id_brand = akb_brand.id
    mysql_query("UPDATE akb_tovar
            LEFT JOIN akb_model ON id_model = akb_model.id
            LEFT JOIN akb_volt ON akb_volt.id = id_volt
            LEFT JOIN akb_v ON akb_v.id = id_v
            LEFT JOIN akb_rvrt ON akb_rvrt.id = rvrt
            SET full_name = concat('АКБ ', akb_volt.name, 'В ', akb_v.name, ' А/ч',
              ' ', akb_model.name, akb_rvrt.name)" .
            ($modelId ? " WHERE id_model = " . $modelId : ""));
    return mysql_affected_rows();
}

// массив констант =============================================================
$dop[5][1]['h1'] = 'профилей';
$dop[6][1]['h1'] = 'диаметров';
$dop[7][1]['h1'] = 'индексов грузоподъемности';
$dop[8][1]['h1'] = 'индексов скорости';

$dop[7][1]['tbl'] = "<tr><td class='title'>Значение, кг</td><td class='control'><input name='tb7_gruz' type='text' value='{$rs1->tb7_gruz}' /></td></tr>";

//$dop[7][1]['tblhdadd']='<td></td>';
//$dop[7][1]['tblval']='<td>{$rs->tb7_gruz}</td>';
// /массив констант ============================================================
// функции =====================================================================
// функция генерация <select> ==================================================
function SelectStr($query, $selname, $id, $fl) {
    $res = mysql_query($query);
    $tmpstr = '<select name="' . $selname . '">' . ($fl == 1 ? '<option value="0"' . (!$id ? ' selected="selected"' : '') . '>Не указан</option>' : '');
    while ($rs_sel = mysql_fetch_object($res))
        $tmpstr .= '<option value="' . $rs_sel->id . '" ' . ($id == $rs_sel->id ? 'selected="selected"' : '') . '>' . $rs_sel->nm . '</option>';
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
    switch ($arg[0]) {
        case 2:
            if (trim($_POST["nm"]) == '')
                header('Location: /adm/spravochnic/' . $arg[0] . '-' . $_POST["tov"] . '/er1/');
            else {
                $res = mysql_query("select count(*) as cn from tab2 where tb2_nm = '" . $_POST["nm"] . "'" . ($_POST["tov"] == 2 ? " and brand_id=" . $_POST['tb3_id'] : "") . " and tb2_tov_id = " . $_POST["tov"]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {
                    mysql_query("insert into tab" . $arg[0] . " (tb" . $arg[0] . "_nm,tb" . $arg[0] . "_tov_id" . ($_POST["tov"] == 2 ? ", brid " : "") . ") values ('" . $_POST["nm"] . "'," . $_POST["tov"] .
                            ($_POST["tov"] == 2 ? ", " . $_POST["tb3_id"] : "") . ")");
                    header('Location: /adm/sp-edit/' . $arg[0] . '/' . mysql_insert_id() . '/');
                } else
                    header('Location: /adm/spravochnic/' . $arg[0] . '-' . $_POST["tov"] . '/er2/');
            }
            break;
        case 4:
            if (trim($_POST["nm"]) == '' || $_POST["tb3_id"] == 0) {
                header('Location: /adm/spravochnic/' . $arg[0] . '-' . $_POST["tov"] . '/er1/');
                exit;
            } else {
                $res = mysql_query("select count(*) as cn from tab" . $arg[0] . " where tb" . $arg[0] . "_nm='" . $_POST["nm"] . "' and brand_id=" . $_POST['tb3_id'] . " and tb" . $arg[0] . "_tov_id=" . $_POST["tov"]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {
                    mysql_query("insert into tab" . $arg[0] . " (tb" . $arg[0] . "_nm,tb" . $arg[0] . "_tov_id,brand_id) values ('" . $_POST["nm"] . "'," . $_POST["tov"] . "," . $_POST["tb3_id"] . ")");
                    header('Location: /adm/sp-edit/' . $arg[0] . '/' . mysql_insert_id() . '/');
                } else
                    header('Location: /adm/spravochnic/' . $arg[0] . '-' . $_POST["tov"] . '/er2/');
            }
            break;
        case 3: case 5: case 6: case 7: case 8: case 9: case 10: case 12:
            if (trim($_POST["nm"]) == '') {
                header('Location: /adm/spravochnic/' . $arg[0] . '-' . $_POST["tov"] . '/er1/');
                exit;
            } else {
                $res = mysql_query("select count(*) as cn from tab" . $arg[0] . " where tb" . $arg[0] . "_nm='" . $_POST["nm"] . "' and tb" . $arg[0] . "_tov_id=" . $_POST["tov"]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {
                    mysql_query("insert into tab" . $arg[0] . " (tb" . $arg[0] . "_nm,tb" . $arg[0] . "_tov_id) values ('" . $_POST["nm"] . "'," . $_POST["tov"] . ")");
                    header('Location: /adm/sp-edit/' . $arg[0] . '/' . mysql_insert_id() . '/');
                } else
                    header('Location: /adm/spravochnic/' . $arg[0] . '-' . $_POST["tov"] . '/er2/');
            }
            break;
        case 13:
            if (trim($_POST["name"]) == '')
                header('Location: /adm/spravochnic/13/er1/');
            $res = mysql_query("select count(*) as cn from shlak where name='" . $_POST['name'] . "'");
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {
                mysql_query("insert into shlak (name) values ('" . $_POST["name"] . "')");
                header('Location: /adm/sp-edit/13/' . mysql_insert_id() . '/');
            } else
                header('Location: /adm/spravochnic/13/er2/');
            break;
        case 14:
            mysql_query("insert into discount (disc_id_sup) values (" . $_POST["sup"] . ")");
            header('Location: /adm/sp-edit/14/' . mysql_insert_id() . '/');
            break;
        case 15:
            if ($_POST['id_sup'] == 0 || trim($_POST["id_tov_sup"]) == '' || trim($_POST["id_tov"]) == '')
                header('Location: /adm/sp-tov/shini/er1/');
            $res = mysql_query("select count(*) as cn from total_suppl where id_tov=" . $_POST["id_tov"] . " and id_sup=" . $_POST['id_sup'] . " and id_tov_sup='" . $_POST['id_tov_sup'] . "'");
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {
                mysql_query("insert into total_suppl (id_tov_sup,id_tov,id_sup) values ('" . urldecode($_POST["id_tov_sup"]) . "'," . $_POST["id_tov"] . "," . $_POST["id_sup"] . ")");
                header('Location: /adm/sp-tov-edit/' . $_POST["tab1"] . '/' . $_POST["id_tov"] . '/');
            } else
                header('Location: /adm/sp-tov-edit/' . $_POST["tab1"] . '/' . $_POST["id_tov"] . '/er2/');
            break;
        case 16:
            if ($_POST['suppl'] == 0 || trim($_POST["name"]) == '')
                header('Location: /adm/spravochnic/16/er1/');
            mysql_query("insert into parser (name,suppl) values ('" . $_POST["name"] . "'," . $_POST["suppl"] . ")");
            header('Location: /adm/sp-edit/16/' . mysql_insert_id() . '/');
            break;
        case 17:
            if (trim($_POST["name"]) == '') {
                header('Location: /adm/spravochnic/17/er1/');
                exit;
            }
            $res = mysql_query("select count(*) as cn from suppl where name='" . $_POST['name'] . "'");
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {
                mysql_query("insert into suppl (name) values ('" . $_POST["name"] . "')");
                header('Location: /adm/sp-edit/17/' . mysql_insert_id() . '/');
            } else
                header('Location: /adm/spravochnic/17/er2/');
            break;
        case 18:
            mysql_query("insert into news (title) values ('" . $_POST["title"] . "')");
            header('Location: /adm/sp-edit/18/' . mysql_insert_id() . '/');
            break;
        case 19:
            mysql_query("insert into nacen (id_sp) values (" . $_POST["id_sp"] . ")");
            header('Location: /adm/sp-edit/19/' . mysql_insert_id() . '/');
            break;
        case 20:
            mysql_query("insert into pages (nmpg) values ('" . $_POST["nmpg"] . "')");
            header('Location: /adm/sp-edit/20/' . mysql_insert_id() . '/');
            break;
        case 21:
            if (trim($_POST["name"]) == '') {
                header('Location: /adm/spravochnic/22/er1/');
                exit;
            }
            $res = mysql_query("select count(*) as cn from profw where name='" . $_POST['name'] . "'");
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {
                mysql_query("insert into profw (name) values ('" . $_POST["name"] . "')");
                header('Location: /adm/sp-edit/21/' . mysql_insert_id() . '/');
            } else
                header('Location: /adm/spravochnic/21/er2/');
            break;
        case 22:
            if (trim($_POST["name"]) == '') {
                header('Location: /adm/spravochnic/22/er1/');
                exit;
            }
            $res = mysql_query("select count(*) as cn from profh where name='" . $_POST['name'] . "'");
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {
                mysql_query("insert into profh (name) values ('" . $_POST["name"] . "')");
                header('Location: /adm/sp-edit/22/' . mysql_insert_id() . '/');
            } else
                header('Location: /adm/spravochnic/22/er2/');
            break;
        case 23:
            mysql_query("insert into nacenki (tb1id) values (" . $_POST["tb1id"] . ")");
            header('Location: /adm/sp-edit/23/' . mysql_insert_id() . '/');
            break;
        case 30:
            if (trim($_POST["name"]) == '') {

                header('Location: /adm/spravochnic/30/er1/');
                exit;
            }
            $res = mysql_query("select count(*) as cn from t_auto where t_auto_nm = '" . $_POST['name'] . "'");
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {
                mysql_query("insert into t_auto (t_auto_nm) values ('" . $_POST["name"] . "')");
                header('Location: /adm/sp-edit/30/' . mysql_insert_id() . '/');
            } else
                header('Location: /adm/spravochnic/30/er2/');
            break;
        case 41:
            if (trim($_POST["nm"]) == '') {

                header('Location: /adm/spravochnic/41/er1/');
                exit;
            }
            $res = mysql_query("select count(*) as cn from akb_brand where name = '" . $_POST['nm'] . "'");
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {

                mysql_query("insert into akb_brand (`name`) values ('" . $_POST["nm"] . "')");
                header('Location: /adm/sp-edit/41/' . mysql_insert_id() . '/');
            } else
                header('Location: /adm/spravochnic/41/er2/');
            break;
        case 42:
            if (trim($_POST["nm"]) == '') {

                header('Location: /adm/spravochnic/42/er1/');
                exit;
            } else {

                $res = mysql_query("select count(*) as cn from akb_model where name = '" .
                        $_POST["nm"]); //  . "' and akb_brand_id = " . $_POST['akb_brand_id']
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {

                    mysql_query("insert into akb_model (name, akb_brand_id) values ('" .
                            $_POST["nm"] . "',0)");
                    header('Location: /adm/sp-edit/' . $arg[0] . '/' . mysql_insert_id() . '/');
                } else
                    header('Location: /adm/spravochnic/42/er2/');
            }
            break;
        case 43: case 44:

            if (trim($_POST["nm"]) == '') {

                header('Location: /adm/spravochnic/' . $arg[0] . '/er1/');
                exit;
            } else {

                if ($arg[0] == 43) {

                    $tableName = 'akb_volt';
                }
                if ($arg[0] == 44) {

                    $tableName = 'akb_v';
                }
                $res = mysql_query("select count(*) as cn from " . $tableName . " where name = '" .
                        $_POST["nm"] . "'");
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {

                    mysql_query("insert into " . $tableName . " (name) values ('" . $_POST["nm"] . "')");
                    header('Location: /adm/sp-edit/' . $arg[0] . '/' . mysql_insert_id() . '/');
                } else
                    header('Location: /adm/spravochnic/' . $arg[0] . '/er2/');
            }
            break;
        case 45:
            if ($_POST['id_sup'] == 0 || trim($_POST["id_tov_sup"]) == '' || trim($_POST["id_tov"]) == '')
                header('Location: /adm/sp-tov/akb/er1/');
            $res = mysql_query("select count(*) as cn from akb_suppl where id_tov = " .
                    $_POST["id_tov"] . " and id_sup=" . $_POST['id_sup'] . " and id_tov_sup='" .
                    $_POST['id_tov_sup'] . "'");
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {

                mysql_query("insert into akb_suppl (id_tov_sup,id_tov,id_sup) values ('" .
                        urldecode($_POST["id_tov_sup"]) . "'," . $_POST["id_tov"] . "," .
                        $_POST["id_sup"] . ")");
                header('Location: /adm/sp-tov-edit/' . $_POST["tab1"] . '/' . $_POST["id_tov"] . '/');
            } else
                header('Location: /adm/sp-tov-edit/' . $_POST["tab1"] . '/' . $_POST["id_tov"] . '/er2/');
            break;
        case 46:
            mysql_query("insert into akb_nacenki (nac_per) values (0)");
            header('Location: /adm/sp-edit/46/' . mysql_insert_id() . '/');
            break;
        case 47:
            if (trim($_POST["nm"]) == '') {

                header('Location: /adm/spravochnic/47/er1/');
                exit;
            }
            $res = mysql_query("select count(*) as cn from akb_klemy where name = '" . $_POST['nm'] . "'");
            $rs_check = mysql_fetch_object($res);
            if ($rs_check->cn < 1) {

                mysql_query("insert into akb_klemy (`name`) values ('" . $_POST["nm"] . "')");
                header('Location: /adm/sp-edit/47/' . mysql_insert_id() . '/');
            } else
                header('Location: /adm/spravochnic/47/er2/');
            break;
        case 48:            
            $insertStm = $dbcon->prepare("INSERT INTO html_blocks (type_key) values ('')");
            $insertStm->execute();                        
            header('Location: /adm/sp-edit/48/' . $dbcon->lastInsertId() . '/');
            break;
    }
}
// /добавление записи в БД =====================================================
// удаление записи из БД =======================================================
if ($arg[2] == 'del') {
    switch ($arg[0]) {
        case 2: case 3: case 4: case 5: case 6: case 7: case 8: case 9: case 10: case 12:
            mysql_query("delete from tab" . $arg[0] . " where tb" . $arg[0] . "_id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 13:
            mysql_query("delete from shlak where id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 14:
            mysql_query("delete from discount where disc_id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 15:
            mysql_query("delete from total_suppl where id_sup=" . $arg[1] . " and id_tov_sup='" . urldecode($arg[3]) . "'");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 16:
            mysql_query("delete from parser where id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 17:
            mysql_query("delete from suppl where id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 18:
            mysql_query("delete from news where id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 19:
            mysql_query("delete from nacen  where id_tbl_n=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 20:
            mysql_query("delete from pages where id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 21:
            mysql_query("delete from profw where id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 22:
            mysql_query("delete from profh where id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 23:
            mysql_query("delete from nacenki where nac_id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 26:
            mysql_query("delete from imgs where " . ($arg[1] != 'null' ? 'idbrand = ' . $arg[1] : 'idbrand is null') . ' and ' .
                    ($arg[3] != 'null' ? 'idmodel = ' . $arg[3] : 'idmodel is null') . ' and ' .
                    ($arg[4] != 'null' ? 'idcolor = ' . $arg[4] : 'idcolor is null'));
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 30:
            mysql_query("delete from t_auto where t_auto_id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 41:
            mysql_query("delete from akb_brand where id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 42:
            mysql_query("delete from akb_model where id=" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 43: case 44:

            if ($arg[0] == 43) {

                $tableName = 'akb_volt';
            }
            if ($arg[0] == 44) {

                $tableName = 'akb_v';
            }
            mysql_query("delete from " . $tableName . " where id =" . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 45:
            mysql_query("delete from akb_suppl where id_sup=" . $arg[1] . " and id_tov_sup='" .
                    urldecode($arg[3]) . "'");
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 46:
            mysql_query("DELETE FROM akb_nacenki WHERE nac_id = " . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 47:
            mysql_query("DELETE FROM akb_klemy WHERE id = " . $arg[1]);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
        case 48:
            $selOrder = $dbcon->prepare("DELETE FROM html_blocks WHERE id = :id");
            $selOrder->bindParam(':id', $arg[1]);
            $selOrder->execute();
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            break;
    }
}
// /удаление записи из БД ======================================================
switch ($arg[0]) {
    case 2:
        $str_error = '';
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["tb2_nm"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {
                //echo "select count(*) as cn from tab2 where tb2_nm='".$_POST["tb2_nm"]."' and brid=".((int)$_POST["brid"])." and tb2_id<>".$arg[1];
                $res = mysql_query("select count(*) as cn from tab2 where tb2_nm='" . $_POST["tb2_nm"] . "' and brid=" . ((int) $_POST["brid"]) . " and tb2_id<>" . $arg[1]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1)
                    mysql_query("update tab2 set tb2_nm='" . $_POST["tb2_nm"] . "',tb2_tov_id=" . $_POST["tb2_tov_id"] . ",brid=" . ((int) $_POST["brid"]) . ",
            translit='" . $_POST["translit"] . "',tb2_sn='" . $_POST["tb2_sn"] . "',alt='" . $_POST["alt"] . "'
            where tb2_id=" . $arg[1]);
                else {
                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
        }
        $res = mysql_query('select tb2_id,tb2_nm,tb2_sn,translit,brid,tb2_tov_id,alt from tab2 where tb2_id=' . $arg[1]);
        $rs = mysql_fetch_object($res);

        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control"><input name="tb2_nm" type="text" value="' . $rs->tb2_nm . '" /></td></tr>
      <tr><td class="title">Тип товара</td><td class="control">' . SelectStr('select tb1_id as id,tb1_nm as nm from tab1 order by tb1_id', 'tb2_tov_id', $rs->tb2_tov_id, 0) . '</td></tr>';
        if ($rs->tb2_tov_id == 2)
            $str .= '<tr><td class="title">Бренд</td><td class="control">' . SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id=' . $rs->tb2_tov_id . ' order by tb3_nm', 'brid', $rs->brid, 1) . '</td></tr>
        <tr><td class="title">Краткое</td><td class="control"><input name="translit" type="text" value="' . $rs->translit . '" /></td></tr>
        <tr><td class="title">Reg</td><td class="control"><input name="tb2_sn" type="text" value="' . $rs->tb2_sn . '" /></td></tr>
        <tr><td class="title">Альтернативные названия <p class="snoska">(между названиями указывать "[|]" без ковычек)</p></td><td class="control"><textarea name="alt">' . $rs->alt . '</textarea></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr>
      <tr><td colspan="2" class="but">' . $str_error . '</td></tr></table></form>';
        break;
    case 3:
        $str_error = '';
        if (isset($_POST["save"])) {  // сохранить
            $error = 0;
            if (trim($_POST["tb3_nm"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {
                $res = mysql_query("select count(*) as cn from tab3 where (tb3_nm='" . $_POST["tb3_nm"] . "') and tb3_id<>" . $arg[1]);
                $rs_check = mysql_fetch_object($res);
                //echo "update tab3 set tb3_nm='".$_POST["tb3_nm"]."',tb3_dsc='".mysql_real_escape_string($_POST["tb3_dsc"])."', url='".$_POST["url"]."',tb3_tov_id=".$_POST["tb3_tov_id"].",dtp=".((int)$_POST["dtp"]).",alt='".trim($_POST["alt"])."',no_color=".(isset($_POST["nc"])?1:0).",no_load=".(isset($_POST["nl"])?1:0)." where tb3_id=".$arg[1];
                if ($rs_check->cn < 1)
                    mysql_query("update tab3 set tb3_nm='" . $_POST["tb3_nm"] . "',tb3_dsc='" . mysql_real_escape_string($_POST["tb3_dsc"]) . "', url='" . $_POST["url"] . "',tb3_tov_id=" . $_POST["tb3_tov_id"] . ",dtp=" . ((int) $_POST["dtp"]) . ",alt='" . trim($_POST["alt"]) . "',no_color=" . (isset($_POST["nc"]) ? 1 : 0) . ",no_load=" . (isset($_POST["nl"]) ? 1 : 0) . " where tb3_id=" . $arg[1]);
                else {
                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
        }
// отображение
        $res = mysql_query('select tb3_id,tb3_nm,tb3_tov_id,dtp,alt,no_color,no_load,tb3_pic,url,tb3_dsc
        from tab3 where tb3_id=' . $arg[1]);
        $rs = mysql_fetch_object($res);

        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control"><input name="tb3_nm" type="text" value="' . $rs->tb3_nm . '" /></td></tr>
      <tr><td class="title">URL</td><td class="control"><input name="url" type="text" value="' . $rs->url . '" /></td></tr>
      <tr><td class="title">Тип товара</td><td class="control">' . SelectStr('select tb1_id as id,tb1_nm as nm from tab1 order by tb1_id', 'tb3_tov_id', $rs->tb3_tov_id, 0) . '</td></tr>';
        if ($rs->tb3_tov_id == 2)
            $str .= '<tr><td class="title">Тип диска</td><td class="control">' . SelectStr('select tb10_id as id,tb10_nm as nm from tab10 where	tb10_tov_id=2 order by tb10_nm', 'dtp', $rs->dtp, 1) . '</td></tr>
        <tr><td class="title">Нет цветов</td><td class="control1"><input type="checkbox" name="nc" ' . ($rs->no_color ? 'checked="checked"' : '') . ' /></td></tr>';
        $str .= '<tr><td class="title">Не грузить</td><td class="control1"><input type="checkbox" name="nl" ' . ($rs->no_load ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td class="title">Альтернативные названия <p class="snoska">(между названиями указывать "[|]" без ковычек)</p></td>
      <td class="control"><textarea name="alt">' . $rs->alt . '</textarea></td></tr>
      <tr><td class="title">Описание</td><td class="control"><textarea name="tb3_dsc" id="brand_descr">' . $rs->tb3_dsc . '</textarea></td></tr>' .
                ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') .
                '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></form>
      <tr><td>' . ($rs->tb3_pic && file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/brands/" . $rs->tb3_pic) ?
                '<img src="' . "/images/tovar/brands/" . $rs->tb3_pic . '" />' : 'нет фото') . '</td>
      <td><form enctype="multipart/form-data" action="/adm/load_pic.php" method="post" class="load">
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
        <input type="hidden" name="brand" value="' . $rs->tb3_id . '" />
        <input name="userfile" type="file" class="file"/><input type="submit" value="Загрузить"  class="but"/></form></td></tr>
      </table>';
        break;
    case 4:
        $str_error = '';
        $str_success = '';
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["tb4_nm"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {
                $res = mysql_query("select count(*) as cn from tab4 where (tb4_nm='" . $_POST["tb4_nm"] . "') and brand_id=" . $_POST["brand_id"] . " and tb4_id<>" . $arg[1]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {

                    $newName = mysql_real_escape_string($_POST["tb4_nm"]);

                    /* if(isset($_POST["auto_brand"]) && $_POST["auto_brand"]){

                      $res = mysql_query('select t_auto_nm from t_auto where t_auto_id='.$_POST["auto_brand"]);
                      if($rs=mysql_fetch_object($res)) {

                      $newName = mysql_real_escape_string($_POST["tb4_nm"] . ' (' . $rs->t_auto_nm . ')');
                      }
                      } */

                    mysql_query("update tab4 set tb4_nm='" . $newName . "', url='" . $_POST["url"] . "',tb4_tov_id=" . $_POST["tb4_tov_id"] . ",tb4_nm1='" . mysql_real_escape_string($_POST["tb4_nm1"]) . "',
              t4ses=" . $_POST["t4ses"] . ", auto_brand=" . ($_POST["auto_brand"] ? $_POST["auto_brand"] : 0) . ",t4sh=" . ((int) $_POST["t4sh"]) . ",description='" . mysql_real_escape_string($_POST["description"]) . "',auto=" . ((int) $_POST["auto"]) . ",brand_id=" . $_POST["brand_id"] . ",alern='" . mysql_real_escape_string($_POST["alern"]) . "',
              t4c = '" . (isset($_POST["t4c"]) ? 'C' : '') . "' where tb4_id=" . $arg[1]);

                    $str_success .= 'Запись сохранена<br/>';
                } else {
                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
            if (isset($_POST["upd-nomen"])) {

                $str_success .= 'Обновлены диаметры у ' . updateTyresDiametr($arg[1]) . ' шин</br>';
                $str_success .= 'Обновлены наименования у ' . updateTyresName($arg[1]) . ' шин</br>';
                $str_success .= 'Обновлены наименования у ' . updateTyresSeasSh($arg[1]) . ' шин</br>';
            }
        }

        $res = mysql_query('select tb4_id,tb4_nm,tb4_nm1,tb4_tov_id,t4ses,t4sh,auto,alern,brand_id,t4c,description,url, auto_brand
      from tab4 where tb4_id=' . $arg[1]);
        $rs = mysql_fetch_object($res);

        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control"><input name="tb4_nm" type="text" value="' . $rs->tb4_nm . '" /></td></tr>
      <tr><td class="title">URL</td><td class="control"><input name="url" type="text" value="' . $rs->url . '" /></td></tr>
      <tr><td class="title">Тип товара</td><td class="control">' . SelectStr('select tb1_id as id,tb1_nm as nm from tab1 order by tb1_id', 'tb4_tov_id', $rs->tb4_tov_id, 0) . '</td></tr>
      <tr><td class="title">Бренд</td><td class="control">' . SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id=' . $rs->tb4_tov_id . ' order by tb3_nm', 'brand_id', $rs->brand_id, 1) . '</td></tr>
      <tr><td class="title">Сезон/Тип</td><td class="control">' . SelectStr('select tb10_id as id,tb10_nm as nm from tab10 where tb10_tov_id=' . $rs->tb4_tov_id . ' order by tb10_id', 't4ses', $rs->t4ses, 1) . '</td></tr>';
        if ($rs->tb4_tov_id == 1)
            $str .= '<tr><td class="title">Шип</td><td class="control">' . SelectStr('select tb9_id as id,tb9_nm as nm from tab9 where tb9_tov_id=1 order by tb9_id', 't4sh', $rs->t4sh, 1) . '</td></tr>
        <tr><td class="title">Тип авто</td><td class="control">' . SelectStr('select tb2_id as id,tb2_nm as nm from tab2 where tb2_tov_id=1 order by tb2_id', 'auto', $rs->auto, 1) . '</td></tr>
        <tr><td class="title">Усиленная</td><td class="control"><input type="checkbox" name="t4c" ' . ($rs->t4c == 'C' ? 'checked="checked"' : '') . ' /></td></tr>';
        if ($rs->tb4_tov_id == 2)
            $str .= '<tr><td class="title">Бренд авто</td><td class="control">' . SelectStr('select t_auto_id as id,t_auto_nm as nm from t_auto order by t_auto_nm', 'auto_brand', $rs->auto_brand, 1) . '</td></tr>
        <tr><td class="title">Цвет по умолчанию</td><td class="control">' . SelectStr('select tb2_id as id,tb2_nm as nm from tab2 where tb2_tov_id=2 and brid=' . $rs->brand_id . ' order by tb2_id', 'auto', $rs->auto, 1) . '</td></tr>';
        $str .= '<tr><td class="title">Reg</td><td class="control"><input name="tb4_nm1" type="text" value="' . $rs->tb4_nm1 . '" /></td></tr>
      <tr><td class="title">Альтернативные названия <p class="snoska">(между названиями указывать "[|]" без ковычек)</p></td><td class="control"><textarea name="alern">' . $rs->alern . '</textarea></td></tr>
      <tr><td class="title">Описание</td><td class="control"><textarea name="description" id="model_descr">' . $rs->description . '</textarea></td></tr>
      ' . ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') .
                '<tr><td>' . ($rs->tb4_tov_id == 1 ? 'Обновить записи шин <input type="checkbox" name="upd-nomen"  />' : '') . '</td><td class="but"><input name="save" type="submit" value="сохранить" /></td></tr>
      <tr><td colspan="2">' . $str_success . '</td></tr></table></form>';

        $sqlImg = 'SELECT imgs.imgname as imgname, tb2_id, tb2_nm, idbrand, idmodel, idcolor
          FROM imgs LEFT JOIN tab2 on tb2_id = imgs.idcolor
         WHERE idmodel = ' . $rs->tb4_id;
        if ($resImg = mysql_query($sqlImg)) {
            $str .= '<table class="colors"><tr><td>Рисунок</td>' . ($rs->tb4_tov_id == 1 ? '' : '<td>Цвет</td>') . '<td>Загрузить</td><td>Удалить</td></tr>
          <form enctype="multipart/form-data" action="/adm/load_pic.php" method="post" class="load">
          <tr><td></td>
          ' . ($rs->tb4_tov_id == 1 ? '' : '<td>' . SelectStr('select tb2_id as id,tb2_nm as nm from tab2 where tb2_tov_id=2 and brid=' . $rs->brand_id . ' order by tb2_id', 'auto', 0, 1) . '</td>') . '
          <td>
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
            <input type="hidden" name="data" value="' . $rs->tb4_tov_id . '|' . $rs->brand_id . '|' . $rs->tb4_id . '|0" />
            <input name="userfile" type="file" class="file"/><input type="submit" value="Загрузить"  class="but"/></td><td></td>
          </tr></form>';
            while ($objImg = mysql_fetch_object($resImg)) {

                $str .= '<tr>
              <td class="img"><img src="/images/tovar/' . ($rs->tb4_tov_id == 1 ? 'tyres' : 'discs') . '/' . $objImg->imgname . '" /></td>
              ' . ($rs->tb4_tov_id == 1 ? '' : '<td class="data">' . $objImg->tb2_nm . '</td>') . '

              <td><form enctype="multipart/form-data" action="/adm/load_pic.php" method="post" class="load">
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
        <input type="hidden" name="data" value="' . $rs->tb4_tov_id . '|' . $objImg->idbrand . '|' . $objImg->idmodel . '|' . $objImg->idcolor . '" />
        <input name="userfile" type="file" class="file"/><input type="submit" value="Загрузить"  class="but"/></form></td>
        <td><a href="/adm/sp-edit/26/' . (!is_numeric($objImg->idbrand) ? 'null' : (int) $objImg->idbrand) . '/del/' .
                        (!is_numeric($objImg->idmodel) ? 'null' : (int) $objImg->idmodel) . '/' .
                        (!is_numeric($objImg->idcolor) ? 'null' : (int) $objImg->idcolor) . '/">Удалить</a></td></tr>';
            }
            $str .= '</table>';
        }

        break;
    case 5: case 6: case 7: case 8: case 9: case 10: case 12:
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["nm"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {
                $res = mysql_query("select count(*) as cn from tab" . $arg[0] . " where tb" . $arg[0] . "_nm='" . $_POST["nm"] . "'" . ($arg[1] > 0 ? " and tb" . $arg[0] . "_id<>" . $arg[1] : ""));
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {
                    $dop_sql = '';
                    switch ($arg[0]) {
                        case 6:
                            $dop_sql .= " ,no_load=" . (isset($_POST["nl"]) ? 1 : 0);
                            break;
                        case 7:
                            $dop_sql .= " ,tb7_gruz='" . $_POST["tb7_gruz"] . "'";
                            break;
                    }
                    mysql_query("update tab" . $arg[0] . " set tb" . $arg[0] . "_nm='" . $_POST["nm"] . "',tb" . $arg[0] . "_tov_id=" . $_POST["tov"] . $dop_sql . " where tb" . $arg[0] . "_id=" . $arg[1]);
                } else {
                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
        }
        $res = mysql_query('select * from tab' . $arg[0] . ' where tb' . $arg[0] . '_id=' . $arg[1]);
        $rs = mysql_fetch_array($res);
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control"><input name="nm" type="text" value="' . $rs['tb' . $arg[0] . '_nm'] . '" /></td></tr>
      <tr><td class="title">Тип товара</td><td class="control">' . SelectStr('select tb1_id as id,tb1_nm as nm from tab1 order by tb1_id', 'tov', $rs['tb' . $arg[0] . '_tov_id'], 0) . '</td></tr></tr>';
        switch ($arg[0]) {
            case 6:
                $str .= '<tr><td class="title">Не грузить</td><td class="control1"><input type="checkbox" name="nl" ' . ($rs['no_load'] ? 'checked="checked"' : '') . ' /></td></tr>';
                break;
            case 7:
                $str .= '<tr><td class="title">Значение, кг</td><td class="control"><input name="tb7_gruz" type="text" value="' . $rs['tb7_gruz'] . '" /></td></tr>';
                break;
        }
        $str .= $rs->alern . '</textarea></td></tr>' . ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') .
                '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 13:
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["name"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0)
                mysql_query("update shlak set name='" . $_POST["name"] . "' where id=" . $arg[1]);
        }
        $rs = mysql_fetch_object(mysql_query('select * from shlak where id=' . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Название</td><td class="control"><input type="text" name="name" value="' . $rs->name . '"/></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 14:
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["disc_per"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0)
                mysql_query("update discount set disc_id_sup=" . $_POST["disc_id_sup"] . ",disc_id_brnd=" . $_POST["disc_id_brnd"] . ",disc_id_ses=" . $_POST["disc_id_ses"] . ",disc_per=" . $_POST["disc_per"] . " where disc_id=" . $arg[1]);
        }
        $rs = mysql_fetch_object(mysql_query('select * from discount where disc_id=' . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Поставщик</td><td class="control">' . SelectStr('select id,name as nm from suppl order by name', 'disc_id_sup', $rs->disc_id_sup, 0) . '</td></tr>
      <tr><td class="title">Бренд</td><td class="control">' . SelectStr('select tb3_id as id,tb3_nm as nm from tab3 order by tb3_nm', 'disc_id_brnd', $rs->disc_id_brnd, 1) . '</td></tr>
      <tr><td class="title">Сезон/Тип</td><td class="control">' . SelectStr('select tb10_id as id,tb10_nm as nm from tab10 order by tb10_nm', 'disc_id_ses', $rs->disc_id_ses, 1) . '</td></tr>
      <tr><td class="title">Скидка</td><td class="control"><input type="text" name="disc_per" value="' . $rs->disc_per . '"/></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    /* case 15:
      if(isset($_POST["save"]))
      mysql_query("update total_suppl set id_sup=".$_POST["id_sup"].",id_tov_sup='".$_POST["id_tov_sup"]."',cnt_sup=".$_POST["cnt_sup"].",prsb_sup=".$_POST["prsb_sup"]." where id_tbl_sp=".$arg[1]);
      $rs=mysql_fetch_object(mysql_query("select * from total_suppl left join total on total_id=id_tov where id_tbl_sp=".$arg[1]));
      $str.='<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td colspan="2"><a href="/sp-tov-edit/'.$rs->tab1_id.'/'.$rs->id_tov.'/">Вернуться к позиции</a></td></tr>
      <tr><td class="title">Наименование Поставщик</td><td class="control">'.$rs->suppl_name.'</td></tr>
      <tr><td class="title">Поставщик</td><td class="control">'.SelectStr('select id,name as nm from suppl order by id','id_sup',$rs->id_sup,0).'</td></tr>
      <tr><td class="title">ID Поставщик</td><td class="control"><input type="text" name="id_tov_sup" value="'.$rs->id_tov_sup.'"/></td></tr>
      <tr><td class="title">Кол-во</td><td class="control"><input type="text" name="cnt_sup" value="'.$rs->cnt_sup.'"/></td></tr>
      <tr><td class="title">Закупка</td><td class="control"><input type="text" name="prsb_sup" value="'.$rs->prsb_sup.'"/></td></tr>';
      $str.='<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
      break; */
    case 16:
        if (isset($_POST["save"])) {

            mysql_query("update parser set name='" . mysql_real_escape_string($_POST["name"]) . "',fileformat='" .
                    mysql_real_escape_string($_POST["fileformat"]) . "',cart='" . mysql_real_escape_string($_POST["cart"]) . "',
          ccost='" . mysql_real_escape_string($_POST["ccost"]) . "',ccostb='" . mysql_real_escape_string($_POST["ccostb"]) .
                    "',ccnt='" . mysql_real_escape_string($_POST["ccnt"]) . "',cname='" . mysql_real_escape_string($_POST["cname"]) . "',
          sheets='" . ($_POST["sheets"]) . "',tyres=" . ($_POST["tyres"] ? "1" : "0") . ",wheels=" . ($_POST["wheels"] ? "1" : "0") .
                    ",akb=" . ($_POST["akb"] ? "1" : "0") . ", suppl=" . ((int) $_POST["suppl"]) . ",obnul='" . mysql_real_escape_string($_POST["obnul"]) .
                    "',t2='" . mysql_real_escape_string($_POST["t2"]) . "',t3='" . mysql_real_escape_string($_POST["t3"]) . "',
          t4='" . mysql_real_escape_string($_POST["t4"]) . "',t5='" . mysql_real_escape_string($_POST["t5"]) .
                    "',t5_reg='" . mysql_real_escape_string($_POST["t5_reg"]) . "',t6='" . mysql_real_escape_string($_POST["t6"]) . "',
          t6_reg='" . mysql_real_escape_string($_POST["t6_reg"]) . "',t7='" . mysql_real_escape_string($_POST["t7"]) .
                    "',t7_reg='" . mysql_real_escape_string($_POST["t7_reg"]) . "', t8='" . mysql_real_escape_string($_POST["t8"]) .
                    "',t8_reg='" . mysql_real_escape_string($_POST["t8_reg"]) . "',t9='" . mysql_real_escape_string($_POST["t9"]) .
                    "',t9_reg='" . mysql_real_escape_string($_POST["t9_reg"]) . "', t12='" . mysql_real_escape_string($_POST["t12"]) .
                    "',t12_reg='" . mysql_real_escape_string($_POST["t12_reg"]) . "',tc='" . mysql_real_escape_string($_POST["tc"]) .
                    "',tc_reg='" . mysql_real_escape_string($_POST["tc_reg"]) . "', tzr='" . mysql_real_escape_string($_POST["tzr"]) .
                    "',tzr_reg='" . mysql_real_escape_string($_POST["tzr_reg"]) . "',tom='" . mysql_real_escape_string($_POST["tom"]) .
                    "',trof='" . mysql_real_escape_string($_POST["trof"]) . "', vis='" . ($_POST["vis"] ? "1" : "0") .
                    "',filename='" . mysql_real_escape_string($_POST["filename"]) . "' where id=" . $arg[1]);
        }
        $rs = mysql_fetch_object(mysql_query("select * from parser where id=" . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Название</td><td class="control"><input type="text" name="name" value="' . $rs->name . '"/></td></tr>
      <tr><td class="title">Формат файла</td><td class="control"><input type="text" name="fileformat" value="' . $rs->fileformat . '"/></td></tr>
      <tr><td class="title">Имя файла</td><td class="control"><input type="text" name="filename" value="' . $rs->filename . '"/></td></tr>
      <tr><td class="title">Страница в Excel</td><td class="control"><input type="text" name="sheets" value="' . $rs->sheets . '"/></td></tr>
      <tr><td class="title">Поставщик</td><td class="control">' . SelectStr('select id,name as nm from suppl order by id', 'suppl', $rs->suppl, 0) . '</td></tr>
      <tr><td class="title">Шины</td><td class="control1"><input type="checkbox" name="tyres" ' . ($rs->tyres == 1 ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td class="title">Диски</td><td class="control1"><input type="checkbox" name="wheels" ' . ($rs->wheels == 1 ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td class="title">АКБ</td><td class="control1"><input type="checkbox" name="akb" ' . ($rs->akb == 1 ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td class="title">Видимый</td><td class="control1"><input type="checkbox" name="vis" ' . ($rs->vis == 1 ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td class="title">Код поставщика</td><td class="control"><input type="text" name="cart" value="' . $rs->cart . '"/></td></tr>
      <tr><td class="title">Цена для наценки</td><td class="control"><input type="text" name="ccost" value="' . $rs->ccost . '"/></td></tr>
      <tr><td class="title">Цена закупка</td><td class="control"><input type="text" name="ccostb" value="' . $rs->ccostb . '"/></td></tr>
      <tr><td class="title">Количество</td><td class="control"><input type="text" name="ccnt" value="' . $rs->ccnt . '"/></td></tr>
      <tr><td class="title">Полное название</td><td class="control"><input type="text" name="cname" value="' . $rs->cname . '"/></td></tr>
      <tr><td class="title">Цвет диска ячейка</td><td class="control"><input type="text" name="t2" value="' . $rs->t2 . '"/></td></tr>
      <tr><td class="title">Бренд ячейка</td><td class="control"><input type="text" name="t3" value="' . $rs->t3 . '"/></td></tr>
      <tr><td class="title">Модель ячейка</td><td class="control"><input type="text" name="t4" value="' . $rs->t4 . '"/></td></tr>
      <tr><td class="title">Профиль/ширина ячейка</td><td class="control"><input type="text" name="t5" value="' . $rs->t5 . '"/></td></tr>
      <tr><td class="title">Профиль/ширина REG</td><td class="control"><input type="text" name="t5_reg" value="' . $rs->t5_reg . '"/></td></tr>
      <tr><td class="title">Диаметр ячейка</td><td class="control"><input type="text" name="t6" value="' . $rs->t6 . '"/></td></tr>
      <tr><td class="title">Диаметр REG</td><td class="control"><input type="text" name="t6_reg" value="' . $rs->t6_reg . '"/></td></tr>
      <tr><td class="title">Инд. Наг./болты ячейка</td><td class="control"><input type="text" name="t7" value="' . $rs->t7 . '"/></td></tr>
      <tr><td class="title">Инд. Наг./болты REG</td><td class="control"><input type="text" name="t7_reg" value="' . $rs->t7_reg . '"/></td></tr>
      <tr><td class="title">Инд. Ск./PCD ячейка</td><td class="control"><input type="text" name="t8" value="' . $rs->t8 . '"/></td></tr>
      <tr><td class="title">Инд. Ск./PCD REG</td><td class="control"><input type="text" name="t8_reg" value="' . $rs->t8_reg . '"/></td></tr>
      <tr><td class="title">Вылет ячейка</td><td class="control"><input type="text" name="t9" value="' . $rs->t9 . '"/></td></tr>
      <tr><td class="title">Вылет REG</td><td class="control"><input type="text" name="t9_reg" value="' . $rs->t9_reg . '"/></td></tr>
      <tr><td class="title">Ступица ячейка</td><td class="control"><input type="text" name="t12" value="' . $rs->t12 . '"/></td></tr>
      <tr><td class="title">Ступица REG</td><td class="control"><input type="text" name="t12_reg" value="' . $rs->t12_reg . '"/></td></tr>
      <tr><td class="title">ZR ячейка</td><td class="control"><input type="text" name="tzr" value="' . $rs->tzr . '"/></td></tr>
      <tr><td class="title">ZR REG</td><td class="control"><input type="text" name="tzr_reg" value="' . $rs->tzr_reg . '"/></td></tr>
      <tr><td class="title">C ячейка</td><td class="control"><input type="text" name="tc" value="' . $rs->tc . '"/></td></tr>
      <tr><td class="title">C REG</td><td class="control"><input type="text" name="tc_reg" value="' . $rs->tc_reg . '"/></td></tr>
      <tr><td class="title">Омологация ячейка</td><td class="control"><input type="text" name="tom" value="' . $rs->tom . '"/></td></tr>
      <tr><td class="title">Run Flat ячейка</td><td class="control"><input type="text" name="trof" value="' . $rs->trof . '"/></td></tr>
      <tr><td class="title">Обнуление</td><td class="control"><input type="text" name="obnul" value="' . $rs->obnul . '"/></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 17:
        if (isset($_POST["save"]))
            mysql_query("update suppl set name='" . $_POST["name"] . "',min_cnt = " . $_POST["min_cnt"] . ",isnal=" . ($_POST["isnal"] ? "1" : "0") . " where id=" . $arg[1]);
        $rs = mysql_fetch_object(mysql_query("select * from suppl where id=" . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control"><input type="text" name="name" value="' . $rs->name . '"/></td></tr>
      <tr><td class="title">Мин. кол-во</td><td class="control"><input type="text" name="min_cnt" value="' . $rs->min_cnt . '"/></td></tr>
      <tr><td class="title">Помечать в наличии</td><td class="control1"><input type="checkbox" name="isnal" ' . ($rs->isnal == 1 ? 'checked="checked"' : '') . ' /></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 18:
        if (isset($_POST["save"]))
            mysql_query("update news set title='" . $_POST["title"] . "',date='" . $_POST["date"] . "',preview='" . $_POST["preview"] . "',content='" . $_POST["content"] . "' where id=" . $arg[1]);
        $rs = mysql_fetch_object(mysql_query("select * from news where id=" . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Заголовок</td><td class="control"><input type="text" name="title" value="' . $rs->title . '"/></td></tr>
      <tr><td class="title">Дата</td><td class="control"><input type="text" name="date" value="' . $rs->date . '"/></td></tr>
      <tr><td class="title">Превью</td><td class="control"><textarea name="preview">' . $rs->preview . '</textarea></td></tr>
      <tr><td class="title">Текст</td><td class="control"><textarea name="content">' . $rs->content . '</textarea></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 19:
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["nac"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0)
                mysql_query("update nacen set id_sp=" . $_POST["id_sp"] . ",t10_id=" . $_POST["t10_id"] . ",t3_id=" . $_POST["t3_id"] . ",price_type=" . $_POST["price_type"] . ",nac=" . $_POST["nac"] . " where id_tbl_n=" . $arg[1]);
        }
        $rs = mysql_fetch_object(mysql_query('select * from nacen where id_tbl_n=' . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Поставщик</td><td class="control">' . SelectStr('select id,name as nm from suppl order by name', 'id_sp', $rs->id_sp, 0) . '</td></tr>
      <tr><td class="title">Сезон/Тип</td><td class="control">' . SelectStr('select tb10_id as id,tb10_nm as nm from tab10 order by tb10_nm', 't10_id', $rs->t10_id, 1) . '</td></tr>
      <tr><td class="title">Бренд</td><td class="control">' . SelectStr('select tb3_id as id,tb3_nm as nm from tab3 order by tb3_nm', 't3_id', $rs->t3_id, 1) . '</td></tr>
      <tr><td class="title">К цене</td><td class="control">' . SelectStr('select id,name as nm from p_type order by name', 'price_type', $rs->price_type, 0) . '</td></tr>
      <tr><td class="title">Наценка</td><td class="control"><input type="text" name="nac" value="' . $rs->nac . '"/></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 20:
        if (isset($_POST["save"]))
            mysql_query("update pages set pg='" . $_POST["pg"] . "',nmpg='" . $_POST["nmpg"] . "',txt='" . $_POST["txt"] . "',
         title='" . $_POST["title"] . "', description='" . $_POST["description"] . "', keywords='" . $_POST["keywords"] . "',
         link_name = '" . $_POST["link_name"] . "', type = '" . $_POST["type"] . "'  where id=" . $arg[1]);
        $rs = mysql_fetch_object(mysql_query("select * from pages where id=" . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Название</td><td class="control"><input type="text" name="nmpg" value="' . $rs->nmpg . '"/></td></tr>
      <tr><td class="title">URL</td><td class="control"><input type="text" name="pg" value="' . $rs->pg . '"/></td></tr>
      <tr><td class="title">Title</td><td class="control"><input type="text" name="title" value="' . $rs->title . '"/></td></tr>
      <tr><td class="title">Description</td><td class="control"><input type="text" name="description" value="' . $rs->description . '"/></td></tr>
      <tr><td class="title">Keywords</td><td class="control"><input type="text" name="keywords" value="' . $rs->keywords . '"/></td></tr>
      <tr><td class="title">Ссылка</td><td class="control"><input type="text" name="link_name" value="' . $rs->link_name . '"/></td></tr>
      <tr><td class="title">Тип</td><td class="control">' . SelectStr('select id,name as nm from menutypes order by name', 'type', $rs->type, 1) . '</td></tr>
      <tr><td class="title">html</td><td class="control"><textarea name="txt" id="page-content">' . $rs->txt . '</textarea></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 21:
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["name"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {
                $res = mysql_query("select count(*) as cn from profw where name='" . $_POST['name'] . "' and id<>" . $arg[1]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn > 0) {
                    $error++;
                    $str_error .= '<p class="error">Такая запись уже существует</p>';
                }
            }
            if ($error == 0)
                mysql_query("update profw set name='" . $_POST["name"] . "' where id=" . $arg[1]);
        }
        $rs = mysql_fetch_object(mysql_query('select * from profw where id=' . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Название</td><td class="control"><input type="text" name="name" value="' . $rs->name . '"/></td></tr>';
        $str .= ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') . '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 22:
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["name"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {
                $res = mysql_query("select count(*) as cn from profh where name='" . $_POST['name'] . "' and id<>" . $arg[1]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn > 0) {
                    $error++;
                    $str_error .= '<p class="error">Такая запись уже существует</p>';
                }
            }
            if ($error == 0)
                mysql_query("update profh set name='" . $_POST["name"] . "' where id=" . $arg[1]);
        }
        $rs = mysql_fetch_object(mysql_query('select * from profh where id=' . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Название</td><td class="control"><input type="text" name="name" value="' . $rs->name . '"/></td></tr>';
        $str .= ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') . '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 23:
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["nac_min"]) == '' && trim($_POST["nac_max"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0)
                mysql_query("update nacenki set tb1id=" . $_POST["tb1id"] . ",brand_id=" . $_POST["brand_id"] . ",ses_id=" . $_POST["ses_id"] . ",suppl_id=" . $_POST["suppl_id"] . ",nac_min=" . ($_POST["nac_min"] == '' ? 0 : $_POST["nac_min"]) . ",nac_max=" . ($_POST["nac_max"] == '' ? 0 : $_POST["nac_max"]) . ",nac_per=" . ($_POST["nac_per"] == '' ? 0 : $_POST["nac_per"]) . " where nac_id=" . $arg[1]);
        }
        $rs = mysql_fetch_object(mysql_query('select * from nacenki where nac_id=' . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Тип товара</td><td class="control">' . SelectStr('select tb1_id as id,tb1_nm as nm from tab1', 'tb1id', $rs->tb1id, 1) . '</td></tr>
      <tr><td class="title">Поставщик</td><td class="control">' . SelectStr('select id,name as nm from suppl order by name', 'suppl_id', $rs->suppl_id, 1) . '</td></tr>
      <tr><td class="title">Бренд</td><td class="control">' . SelectStr('select tb3_id as id,tb3_nm as nm from tab3 order by tb3_nm', 'brand_id', $rs->brand_id, 1) . '</td></tr>
      <tr><td class="title">Сезон</td><td class="control">' . SelectStr('select tb10_id as id,tb10_nm as nm from tab10 order by tb10_nm', 'ses_id', $rs->ses_id, 1) . '</td></tr>
      <tr><td class="title">Мин. цена</td><td class="control"><input type="text" name="nac_min" value="' . $rs->nac_min . '"/></td></tr>
      <tr><td class="title">Макс. цена</td><td class="control"><input type="text" name="nac_max" value="' . $rs->nac_max . '"/></td></tr>
      <tr><td class="title">Неценка, %</td><td class="control"><input type="text" name="nac_per" value="' . $rs->nac_per . '"/></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 30:
        $str_error = '';
        if (isset($_POST["save"])) {

            $error = 0;
            if (trim($_POST["t_auto_nm"]) == '') {

                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }

            if ($error == 0) {

                $res = mysql_query("select count(*) as cn from t_auto where t_auto_nm='" . $_POST["t_auto_nm"] . "' and t_auto_id<>" . $arg[1]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {

                    mysql_query("update t_auto set t_auto_nm='" . $_POST["t_auto_nm"] . "', t_auto_vis = '" . ($_POST["t_auto_vis"] ? "1" : "0") . "',
                rp='" . $_POST["rp"] . "', rph = '" . $_POST["rph"] . "' where t_auto_id=" . $arg[1]);
                } else {
                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
        }

        $res = mysql_query('select t_auto_id, t_auto_nm, t_auto_vis, t_auto_pic, rp, rph from t_auto where t_auto_id=' . $arg[1]);
        $rs = mysql_fetch_object($res);

        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Бренд авто</td><td class="control"><input name="t_auto_nm" type="text" value="' . $rs->t_auto_nm . '" /></td></tr>
      <tr><td class="title">Реплика</td><td class="control"><input name="rp" type="text" value="' . $rs->rp . '" /></td></tr>
      <tr><td class="title">Реплика H</td><td class="control"><input name="rph" type="text" value="' . $rs->rph . '" /></td></tr>
      <tr><td class="title">Видимый</td><td class="control1"><input type="checkbox" name="t_auto_vis" ' . ($rs->t_auto_vis ? 'checked="checked"' : '') . ' /></td></tr>
      <tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></form>
      <tr><td>' . ($rs->t_auto_pic && file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/cars/" . $rs->t_auto_pic) ?
                '<img src="/images/tovar/cars/' . $rs->t_auto_pic . '" />' : 'нет фото') . '</td>
      <td><form enctype="multipart/form-data" action="/adm/load_pic.php" method="post" class="load">
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
        <input type="hidden" name="t_auto" value="' . $rs->t_auto_id . '" />
        <input name="userfile" type="file" class="file"/><input type="submit" value="Загрузить"  class="but"/></form></td></tr>' .
                ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') . '</table>';
        break;
    case 41:

        $str_error = '';
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["nm"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {
                $res = mysql_query("select count(*) as cn from akb_brand where (name = '" . $_POST["nm"] . "' or url='" . $_POST["url"] . "') and id<>" . $arg[1]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1)
                    mysql_query("update akb_brand set name = '" . $_POST["nm"] . "', url='" .
                            $_POST["url"] . "', dsc='" . mysql_real_escape_string($_POST["dsc"]) .
                            "', alt='" . trim($_POST["alt"]) . "' where id=" . $arg[1]);
                else {

                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
        }

        $res = mysql_query('select id, name, url, alt, dsc, pic from akb_brand where id = ' . $arg[1]);
        $rs = mysql_fetch_object($res);

        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Название</td><td class="control"><input name="nm" type="text" value="' . $rs->name . '" /></td></tr>
      <tr><td class="title">URL</td><td class="control"><input name="url" type="text" value="' . $rs->url . '" /></td></tr>
      <tr><td class="title">Альтернативные названия <p class="snoska">(между названиями указывать "[|]" без ковычек)</p></td>
      <td class="control"><textarea name="alt">' . $rs->alt . '</textarea></td></tr>
      <tr><td class="title">Описание</td><td class="control"><textarea name="dsc" id="brand_descr">' . $rs->dsc . '</textarea></td></tr>' .
                ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') .
                '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></form>
      <tr><td>' . ($rs->pic && file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/brands/" . $rs->pic) ?
                '<img src="/images/tovar/brands/' . $rs->pic . '" />' : 'нет фото') . '</td>
      <td><form enctype="multipart/form-data" action="/adm/load_pic.php" method="post" class="load">
      <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
      <input type="hidden" name="akbbrand" value="' . $rs->id . '" />
      <input name="userfile" type="file" class="file"/>
      <input type="submit" value="загрузить"  class="but"/></form></td></tr>
      </table>';
        break;
    case 42:
        $str_error = '';
        $str_success = '';
        if (isset($_POST["save"])) {

            $error = 0;
            if (trim($_POST["nm"]) == '') {

                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }

            if ($error == 0) {

                $res = mysql_query("select count(*) as cn from akb_model where (name = '" .
                        $_POST["nm"] . "' or url = '" . $_POST["url"] . "') and id <> " . $arg[1]);
                // and akb_brand_id=" . $_POST["akb_brand_id"]."
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {

                    mysql_query("UPDATE akb_model SET name = '" . mysql_real_escape_string($_POST["nm"]) .
                            "', url = '" . $_POST["url"] . "', akb_brand_id = 0,
              alt = '" . mysql_real_escape_string($_POST["alern"]) . "',dsc = '" .
                            mysql_real_escape_string($_POST["dsc"]) . "' where id = " . $arg[1]);
                    $str_success .= 'Запись сохранена<br/>';
                } else {

                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
            if (isset($_POST["upd-nomen"])) {

                $str_success .= 'Обновлены названия АКБ у ' . updateAkbName($arg[1]) . ' товаров</br>';
            }
        }

        $res = mysql_query('SELECT id, name as nm, vis, alt, akb_brand_id, url, dsc, pic
      FROM akb_model WHERE id = ' . $arg[1]);
        $rs = mysql_fetch_object($res);

        //<tr><td class="title">Бренд</td><td class="control">' . SelectStr('SELECT id, name as nm FROM akb_brand order by name', 'akb_brand_id', $rs->akb_brand_id, 1).'</td></tr>

        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control"><input name="nm" type="text" value="' . $rs->nm . '" /></td></tr>
      <tr><td class="title">URL</td><td class="control"><input name="url" type="text" value="' . $rs->url . '" /></td></tr>
      <tr><td class="title">Альтернативные названия <p class="snoska">(между названиями указывать "[|]" без ковычек)</p></td>
      <td class="control"><textarea name="alern">' . $rs->alt . '</textarea></td>
      <tr><td class="title">Описание</td><td class="control"><textarea name="dsc" id="model_descr">' . $rs->dsc . '</textarea></td></tr>
      </tr>' . ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') .
                '<tr><td>Обновить записи АКБ <input type="checkbox" name="upd-nomen"  /></td>
           <td class="but"><input name="save" type="submit" value="сохранить" /></td></tr>
      <tr><td colspan="2">' . $str_success . '</td></tr></form>
      <tr><td>' . ($rs->pic && file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/akb/" . $rs->pic) ?
                '<img src="/images/tovar/akb/' . $rs->pic . '" />' : 'нет фото') . '</td>
      <td><form enctype="multipart/form-data" action="/adm/load_pic.php" method="post" class="load">
      <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
      <input type="hidden" name="akbmodel" value="' . $rs->id . '" />
      <input name="userfile" type="file" class="file"/>
      <input type="submit" value="загрузить"  class="but"/></form></td></tr>
      </table>';
        break;
    case 43: case 44:

        if ($arg[0] == 43) {

            $tableName = 'akb_volt';
            $h1 = '<h1>Справочник вольтажа АКБ</h1>';
        }
        if ($arg[0] == 44) {

            $tableName = 'akb_v';
            $h1 = '<h1>Справочник объем АКБ</h1>';
        }

        if (isset($_POST["save"])) {

            $error = 0;
            if (trim($_POST["nm"]) == '') {

                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {

                $res = mysql_query("SELECT count(*) as cn FROM " . $tableName . " WHERE name = '" .
                        $_POST["nm"] . "' and id <> " . $arg[1]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1) {

                    mysql_query("update " . $tableName . " set name = '" . $_POST["nm"] .
                            "', vis = " . ($_POST["vis"] ? "1" : "0") . " WHERE id = " . $arg[1]);
                } else {

                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
        }
        $res = mysql_query('SELECT * FROM ' . $tableName . ' WHERE id = ' . $arg[1]);
        $rs = mysql_fetch_array($res);
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Название</td><td class="control"><input name="nm" type="text" value="' .
                $rs['name'] . '" /></td></tr>
      <tr><td class="title">Видимый</td><td class="control1"><input type="checkbox" name="vis" ' .
                ($rs->vis ? 'checked="checked"' : '') . ' /></td></tr>';
        $str .= ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') .
                '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" />
        </td></tr></table></form>';
        break;
    case 46:
        if (isset($_POST["save"])) {

            $error = 0;
            if (trim($_POST["nac_min"]) == '' && trim($_POST["nac_max"]) == '') {

                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0)
                mysql_query("UPDATE akb_nacenki SET suppl_id = " . $_POST["suppl_id"] . ", nac_min = " .
                        ($_POST["nac_min"] == '' ? 0 : $_POST["nac_min"]) . ", nac_max=" . ($_POST["nac_max"] == '' ? 0 : $_POST["nac_max"]) .
                        ", nac_per = " . ($_POST["nac_per"] == '' ? 0 : $_POST["nac_per"]) . " where nac_id=" . $arg[1]);
        }
        $rs = mysql_fetch_object(mysql_query('SELECT * FROM akb_nacenki WHERE nac_id = ' . $arg[1]));
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Поставщик</td><td class="control">' . SelectStr('select id,name as nm from suppl order by name', 'suppl_id', $rs->suppl_id, 1) . '</td></tr>
      <tr><td class="title">Мин. цена</td><td class="control"><input type="text" name="nac_min" value="' . $rs->nac_min . '"/></td></tr>
      <tr><td class="title">Макс. цена</td><td class="control"><input type="text" name="nac_max" value="' . $rs->nac_max . '"/></td></tr>
      <tr><td class="title">Неценка, %</td><td class="control"><input type="text" name="nac_per" value="' . $rs->nac_per . '"/></td></tr>';
        $str .= '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></table></form>';
        break;
    case 47:

        $str_error = '';
        if (isset($_POST["save"])) {

            $error = 0;
            if (trim($_POST["nm"]) == '') {

                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {

                $res = mysql_query("select count(*) as cn from akb_klemy where name = '" . $_POST["nm"] . "' and id<>" . $arg[1]);
                $rs_check = mysql_fetch_object($res);
                if ($rs_check->cn < 1)
                    mysql_query("update akb_klemy set name = '" . $_POST["nm"] . "',
                alt='" . trim($_POST["alt"]) . "', vis = " . ($_POST["vis"] ? "1" : "0") .
                            " where id=" . $arg[1]);
                else {

                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
        }

        $res = mysql_query('select id, name, alt, vis from akb_klemy where id = ' . $arg[1]);
        $rs = mysql_fetch_object($res);

        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Название</td><td class="control"><input name="nm" type="text" value="' . $rs->name . '" /></td></tr>
      <tr><td class="title">Альтернативные названия <p class="snoska">(между названиями указывать "[|]" без ковычек)</p></td>
      <td class="control"><textarea name="alt">' . $rs->alt . '</textarea></td></tr>
      <tr><td class="title">Видимый</td><td class="control1"><input type="checkbox" name="vis" ' . ($rs->vis ? 'checked="checked"' : '') . ' /></td></tr>' .
                ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') .
                '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr></form>
      </table>';
        break;
    case 48:
        $str_error = '';
        $str_success = '';
        if (isset($_POST["save"])) {
            $error = 0;
            if (trim($_POST["type_key"]) == '') {
                $error++;
                $str_error .= '<p class="error">Пустая запись</p>';
            }
            if ($error == 0) {
                $typeKey = $_POST["type_key"];
                $sel = $dbcon->prepare("SELECT COUNT(*) as cn FROM html_blocks WHERE type_key = :type_key AND id <> :id");
                $sel->bindParam(':type_key', $typeKey);
                $sel->bindParam(':id', $arg[1]);
                if (!$sel->execute()) {
                    // TODO: error
                }                
                $rs_check = $sel->fetch(PDO::FETCH_OBJ);
                if ($rs_check->cn < 1) {                    
                    $upd = $dbcon->prepare("UPDATE html_blocks SET type_key = :type_key, html = :html, " .
                            "description = :description WHERE id = :id");
                    $upd->bindParam(':type_key', $typeKey, PDO::PARAM_STR);
                    $upd->bindParam(':html', $_POST["html"]);
                    $upd->bindParam(':description', $_POST["description"], PDO::PARAM_STR);
                    $upd->bindParam(':id', $arg[1], PDO::PARAM_INT);
                    if ($upd->execute()) {
                        $str_success .= 'Запись сохранена<br/>';
                    } else {
                        $str_error .= '<p class="error">Ошибка при обновлении данных: ' .
                                $dbcon->errorCode() . ', ' . $upd->errorCode() . '</p>';
                        $error++;
                    }                    
                } else {
                    $str_error .= '<p class="error">Запись уже существует</p>';
                    $error++;
                }
            }
        }
        $sel = $dbcon->prepare('SELECT id, type_key, description, html FROM html_blocks WHERE id = :id');
        $sel->bindParam(':id', $arg[1], PDO::PARAM_INT);
        if (!$sel->execute()) {
            // error
        }
        $rs = $sel->fetch(PDO::FETCH_OBJ);
        $str .= '<form enctype="multipart/form-data" action="" method="post"><table class="ed">' .
                '<tr><td class="title">Ключ</td><td class="control"><input name="type_key" type="text" value="' . $rs->type_key . '" /></td></tr>' .
                '<tr><td class="title">Описание</td><td class="control"><input name="description" type="text" value="' . $rs->description . '" /></td></tr>' .
                '<tr><td class="title">html</td><td class="control"><textarea name="html" id="page-content">' . $rs->html . '</textarea></td></tr>' .
                ($error > 0 ? '<tr><td colspan="2" class="but">' . $str_error . '</td></tr>' : '') .
                '<tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /></td></tr>' .
                '<tr><td colspan="2">' . $str_success . '</td></tr></table></form>';
        break;
}
$str .= '</td></tr></table>';
?>