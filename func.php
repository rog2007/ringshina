<?php

function getBlock($key) {
    global $dbcon;
    $sel = $dbcon->prepare("SELECT html FROM html_blocks WHERE type_key = :type_key");
    $sel->bindParam(':type_key', $key, PDO::PARAM_STR);
    if ($sel->execute() && $rs = $sel->fetch(PDO::FETCH_OBJ)) {
        return $rs->html;
    }
    return 'ERROR';
}

function getBrandsPodbor($tov, $brands) {

    global $dbcon;

    $name = $tov == 1 ? 'brand' : 'brandd';

    $res = '';
    $i = 0;
    $selSeas = $dbcon->prepare('select tb3_id as id, tb3_nm as name from tab3 where tb3_tov_id=' . $tov . ' and wrk3=1 order by tb3_nm');
    if ($selSeas->execute() && $selSeas->rowCount() > 0) {

        $rows = $selSeas->rowCount();
        $res .= '<div class="row"><div class="large-6 columns">';
        while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {

            //if($i == round($rows/2, 0)) {
            if ($rows > 21 && $i == 21) {

                $res .= '</div><div class="large-6 columns">';
            }
            $res .= '<label for="' . $name . '[' . $resObj->id . ']">
          			 <input type="checkbox" id="' . $name . '[' . $resObj->id . ']" name="' . $name . '[' . $resObj->id . ']" style="display: none;" ' . (in_array($resObj->id,
                    $brands) ? 'checked="checked"' : '') . ' />
          			 <span class="custom checkbox"></span> ' . $resObj->name . '</label>';
            $i++;
        }
        $res .= '</div>';
    }
    return $res;
}

function replaceSymbols($name) {

    $result = str_replace("/", "_", $name);
    $result = str_replace("-", "_", $result);
    $result = str_replace("(", "_", $result);
    $result = str_replace(")", "_", $result);
    $result = str_replace(".", "_", $result);
    $result = str_replace(",", "_", $result);
    $result = str_replace("-", "_", $result);
    $result = str_replace("+", "_", $result);
    $result = str_replace("&", "_", $result);
    $result = str_replace("'", "_", $result);
    return $result;
}

// new functions
function getNomenId($url) {
    global $dbcon;

    $selectNomen = $dbcon->prepare('select tab1_id, total_id from total where url=:urlstr');
    $selectNomen->bindParam(':urlstr', $url);
    if ($selectNomen->execute() && $selectNomen->rowCount() > 0) {

        return $selectNomen->fetch(PDO::FETCH_OBJ);
    } else {

        $selectNomen = $dbcon->prepare('select 3 as tab1_id, id as total_id from akb_tovar where url = :urlstr');
        $selectNomen->bindParam(':urlstr', $url);
        if ($selectNomen->execute() && $selectNomen->rowCount() > 0) {

            return $selectNomen->fetch(PDO::FETCH_OBJ);
        }
    }
    return false;
}

function getDiskModelImages($modelId, $limit = 0) {
    global $dbcon;
    /*echo 'select imgname, idcolor, tab2.translit as t2tr, tb2_nm from imgs
    LEFT JOIN tab2 ON tb2_id = idcolor
    LEFT JOIN tab4 ON tb4_id = idmodel
    where idmodel=:mid order by if(auto = idcolor, 1, 0) desc, idcolor' . ($limit ? ' limit 0, ' . $limit : '');*/
    $selectNomen = $dbcon->prepare('select imgname, idcolor, tab2.translit as t2tr, tb2_nm from imgs
    LEFT JOIN tab2 ON tb2_id = idcolor
    LEFT JOIN tab4 ON tb4_id = idmodel
    where idmodel=:mid order by if(auto = idcolor, 1, 0) desc, idcolor' . ($limit ? ' limit 0, ' . $limit : ''));
    $selectNomen->bindParam(':mid', $modelId);
    return $selectNomen;

}

function getModel($tov, $url, $urlBr) {
    global $dbcon;

    $selectNomen = $dbcon->prepare('select tb1_nm, tb4_id, tb4_tov_id, tb3_nm, tb4_nm, tb2_nm, tb10_nm, description, t4ses, t4sh,
    tab1.translit as t1url, tab3.url as t3url, tab4.url as t4url, t4sh, imgs.imgname as image, auto, brand_id, tab2.translit as t2tr,
    auto_brand, t_auto_nm
    FROM tab4
    LEFT JOIN tab3 ON tb3_id = brand_id
    LEFT JOIN tab10 ON tb10_id = t4ses
    LEFT JOIN tab1 ON tb1_id = tb4_tov_id
    LEFT JOIN tab2 ON tb2_id = auto
    LEFT JOIN t_auto ON auto_brand = t_auto_id
    LEFT JOIN imgs ON tb4_id = imgs.idmodel AND imgs.idcolor=0
    where tab4.url=:urlstr and tab3.url=:urlbr');
    $selectNomen->bindParam(':urlstr', $url);
    $selectNomen->bindParam(':urlbr', $urlBr);
    if ($selectNomen->execute() && $selectNomen->rowCount() > 0) {

        return $selectNomen->fetch(PDO::FETCH_OBJ);
    } else {

        $arr = $selectNomen->errorInfo();
        print_r($arr);
    }
    return false;
}

function getModelAKB($url/*, $urlBr*/) {
    global $dbcon;

    $selectNomen = $dbcon->prepare('select tb1_nm, akb_model.id as tb4_id, 3 as tb4_tov_id,
      akb_brand.name as tb3_nm, akb_model.name as tb4_nm, -1 as tb2_nm, -1 as tb10_nm,
      akb_model.dsc as description, tab1.translit as t1url, akb_brand.url as t3url,
      akb_model.url as t4url, -1 as t4sh, akb_model.pic as image, -1 as auto,
      akb_brand_id as brand_id, -1 as t2tr
      FROM akb_model
      LEFT JOIN akb_brand ON akb_brand.id = akb_brand_id
      LEFT JOIN tab1 ON tb1_id = 3
      where akb_model.url=:urlstr'); //  and akb_brand.url=:urlbr
    $selectNomen->bindParam(':urlstr', $url);
//    $selectNomen->bindParam(':urlbr', $urlBr);
    if ($selectNomen->execute() && $selectNomen->rowCount() > 0) {

        return $selectNomen->fetch(PDO::FETCH_OBJ);
    } else {

        $arr = $selectNomen->errorInfo();
        print_r($arr);
    }
    return false;
}

function getRazmerTyre($modelId) {
    global $dbcon;

    $res = $dbcon->prepare('select total_id, tb6_nm, tb7_nm, tb8_nm, profw.name as wname, profh.name as hname,
    total.url as turl, price, cnt, spid
    FROM total
    LEFT JOIN tab6 ON tb6_id = tab6_id
    LEFT JOIN tab7 ON tb7_id = tab7_id
    LEFT JOIN tab8 ON tb8_id = tab8_id
    LEFT JOIN profw ON w_id = profw.id
    LEFT JOIN profh ON h_id = profh.id
    where tab4_id=:id and total.wrk = 1 and total.cnt > 0
    order by tb6_nm*1, profw.name*1, profh.name*1');
    $res->bindParam(':id', $modelId);
    return $res;
}

function getRazmerDisc($modelId) {
    global $dbcon;

    $res = $dbcon->prepare('select total_id, tb5_nm, tb6_nm, tb7_nm, tb8_nm, tb9_nm, tb12_nm,
    total.url as turl, price, cnt, tb2_nm, cnt, spid
    FROM total
    LEFT JOIN tab5 ON tb5_id = tab5_id
    LEFT JOIN tab6 ON tb6_id = tab6_id
    LEFT JOIN tab7 ON tb7_id = tab7_id
    LEFT JOIN tab8 ON tb8_id = tab8_id
    LEFT JOIN tab9 ON tb9_id = tab9_id
    LEFT JOIN tab12 ON tb12_id = tab12_id
    LEFT JOIN tab2 ON tb2_id = tab2_id
    where tab4_id=:id and total.wrk = 1 and total.cnt > 0
    order by tb6_nm*1, tb5_nm*1, tb7_nm*1, tb8_nm*1');
    $res->bindParam(':id', $modelId);
    return $res;
}

function getRazmerAKB($modelId) {
    global $dbcon;

    $res = $dbcon->prepare('select full_name, akb_tovar.id as total_id, akb_v.name as vname,
    akb_volt.name as volname, akb_tovar.url as turl, price, cnt, supid as spid, ar.name as arnm
    FROM akb_tovar
    LEFT JOIN akb_v ON akb_tovar.id_v = akb_v.id
    LEFT JOIN akb_volt ON akb_tovar.id_volt = akb_volt.id
    LEFT JOIN akb_rvrt as ar ON ar.id = akb_tovar.rvrt
    where id_model=:id and akb_tovar.vis = 1 and akb_tovar.cnt > 0
    order by akb_v.name*1, akb_volt.name*1');
    $res->bindParam(':id', $modelId);
    return $res;
}

function getModelsTyre($brandId) {
    global $dbcon;
//echo $brandId;
    $res = $dbcon->prepare('select tb4_nm, tab4.url as t4url, translit as t1url, tb4_nm, imgs.imgname as image
    FROM tab4 LEFT JOIN imgs ON tb4_id = imgs.idmodel
    left join tab1 on tb4_tov_id = tb1_id
    where brand_id=:id');
    $res->bindParam(':id', $brandId);
    return $res;
}

function getTyre($id) {

    global $dbcon;

    if (!$id) {
        return false;
    }

    $selTyre = $dbcon->prepare("SELECT all_name, cnt, tab1_id, concat(profw.name, IF(ifnull(profh.name, '') > '', " .
        "concat('/', profh.name), '')) AS prof, tb1_nm, total_id, tb10_pic AS T10Pic, description, tb3_dsc, imgs.imgname as T4Pic, " .
        "tb4_nm AS T4Nm, tb3_nm AS T3Nm, tb2_pic AS T2Pic, tb2_nm AS T2Nm, tab4_id, price, tb6_nm AS T6Nm,tab2.translit as t2tr, " .
        "tb10_nm AS T10Nm, tb7_nm AS T7Nm, tb7_gruz, tb8_nm AS T8Nm, tb8_speed, t4sh, tab10_id,tab2_id, " .
        "tab3_id, tab4_id, spid, tab1.translit as t1url, tab3.url as t3url, tab4.url as t4url, total.url as tturl, tovimg, cnt, wrk " .
        "FROM total LEFT JOIN tab2 ON tb2_id = tab2_id " .
        "LEFT JOIN tab10 ON tb10_id = tab10_id " .
        "LEFT JOIN tab4 ON tb4_id = tab4_id " .
        "LEFT JOIN tab3 ON tb3_id = tab3_id " .
        "LEFT JOIN tab6 ON tb6_id = tab6_id " .
        "LEFT JOIN tab7 ON tb7_id = tab7_id " .
        "LEFT JOIN profw ON w_id = profw.id " .
        "LEFT JOIN profh ON h_id = profh.id " .
        "LEFT JOIN tab1 ON tb1_id = tab1_id " .
        "LEFT JOIN tab8 ON tb8_id = tab8_id " .
        "LEFT JOIN imgs ON tab4_id = imgs.idmodel " .
        "WHERE total_id=:id limit 0, 1");
    $selTyre->bindParam(':id', $id);
    if ($selTyre->execute() && $selTyre->rowCount() > 0) {

        return $selTyre->fetch(PDO::FETCH_OBJ);
    }

    return false;
}

function getDisc($id) {
    global $dbcon;

    if (!$id) {
        return false;
    }

    $selTyre = $dbcon->prepare("SELECT all_name, cnt, tab1_id, tb5_nm, tb1_nm,
    total_id, tb10_pic AS T10Pic, auto_brand, t_auto_nm,
    imgs.imgname as T4Pic,tb4_nm AS T4Nm, tb3_nm AS T3Nm, tb2_pic AS T2Pic, description,
    tb2_nm AS T2Nm, tab4_id, price, tb6_nm AS T6Nm,tab2.translit as t2tr,
    tb10_nm AS T10Nm, tb5_nm AS T5Nm,tb7_nm AS T7Nm, tb7_gruz, tb8_nm AS T8Nm,
    tb8_speed, tb9_nm AS T9Nm, tab10_id,tab2_id,tab3_id,tab4_id,spid, tb12_nm,
    tab1.translit as t1url, tab3.url as t3url, tab4.url as t4url,
    total.url as tturl, tovimg, cnt, wrk, tovdsc
    FROM total LEFT JOIN tab2 ON tb2_id = tab2_id LEFT JOIN tab9 ON tb9_id = tab9_id
    LEFT JOIN tab10 ON tb10_id = tab10_id LEFT JOIN tab4 ON tb4_id = tab4_id
    LEFT JOIN tab3 ON tb3_id = tab3_id LEFT JOIN tab6 ON tb6_id = tab6_id
    LEFT JOIN tab5 ON tb5_id = tab5_id LEFT JOIN tab7 ON tb7_id = tab7_id
    LEFT JOIN tab1 ON tb1_id = tab1_id LEFT JOIN tab8 ON tb8_id = tab8_id
    LEFT JOIN tab12 ON tb12_id = tab12_id LEFT JOIN t_auto ON auto_brand = t_auto_id
    LEFT JOIN imgs ON tab4_id = imgs.idmodel AND imgs.idcolor=tab2_id
    WHERE total_id=:id limit 1");
    $selTyre->bindParam(':id', $id);
    if ($selTyre->execute() && $selTyre->rowCount() > 0) {

        return $selTyre->fetch(PDO::FETCH_OBJ);
    }

    return false;
}

function getAKB($id) {
    global $dbcon;

    if (!$id) {
        return false;
    }

    $selTyre = $dbcon->prepare("SELECT full_name as all_name, cnt, 3 as tab1_id,
        at.id as total_id, tb1_nm, am.pic as T4Pic, av.name as volname, avl.name as voltname,
        am.name AS T4Nm, ab.name AS T3Nm, at.decr as description, supid as spid, tab1.translit as t1url,
        ab.url as t3url, am.url as t4url, at.url as tturl, at.vis, rvrt, price
        FROM akb_tovar as at
        LEFT JOIN tab1 ON tb1_id = 3
        LEFT JOIN akb_brand as ab ON ab.id = at.id_brand
        LEFT JOIN akb_model as am ON am.id = at.id_model
        LEFT JOIN akb_v as av ON av.id = at.id_v
        LEFT JOIN akb_volt as avl ON avl.id = at.id_volt
        LEFT JOIN akb_rvrt as ar ON ar.id = at.rvrt
        WHERE at.id=:id limit 1");
    $selTyre->bindParam(':id', $id);
    if ($selTyre->execute() && $selTyre->rowCount() > 0) {

        return $selTyre->fetch(PDO::FETCH_OBJ);
    }

    return false;
}

//new functions

$months[1] = "января";
$months[2] = "февраля";
$months[3] = "марта";
$months[4] = "апреля";
$months[5] = "мая";
$months[6] = "июня";
$months[7] = "июля";
$months[8] = "августа";
$months[9] = "сентября";
$months[10] = "октября";
$months[11] = "ноября";
$months[12] = "декабря";
function rustolow1($s) {
    $rus = "АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЫЪЬЭЮЯ";
    $lat = "абвгдежзийклмнопрстуфхцчшщыъьэюя";
    $s = strtr($s, $rus, $lat);
    return $s;
}

function rus2lat($s) {
    $s = rustolow1($s);
    $s = str_replace("ыа", "yha", $s);
    $s = str_replace("ыо", "yho", $s);
    $s = str_replace("ыу", "yhu", $s);
    $s = str_replace("ё", "yo", $s);
    $s = str_replace("ж", "zh", $s);
    $rus = "абвгдезийклмнопрстуфхц";
    $lat = "abvgdezijklmnoprstufxc";
    $s = strtr($s, $rus, $lat);
    $s = str_replace("ч", "ch", $s);
    $s = str_replace("ш", "sh", $s);
    $s = str_replace("щ", "shh", $s);
    $s = str_replace("ъ", "qh", $s);
    $s = str_replace("ы", "y", $s);
    $s = str_replace("ь", "q", $s);
    $s = str_replace("э", "eh", $s);
    $s = str_replace("ю", "yu", $s);
    $s = str_replace("я", "ya", $s);
    return $s;
}

function basket_count() {

    global $dbcon;
    global $uid;

    $stmtBask = $dbcon->prepare('SELECT sum(order_tmp.cnt) AS s_cnt, sum(order_tmp.cnt*price) AS s_tot FROM order_tmp ' .
        'LEFT JOIN total ON total.total_id = order_tmp.id_name WHERE us_id=:uid');
    $stmtBask->bindParam(':uid', $uid);
    if ($stmtBask->execute() && $stmtBask->rowCount() > 0) {
        return $stmtBask->fetch(PDO::FETCH_OBJ);
    }
    return false;
}

function normalize_mysqldate($mysqldate) {
    global $months;
    return $mysqldate[8] . $mysqldate[9] . " " . $months[$mysqldate[5] * 10 + $mysqldate[6]] . " " . $mysqldate[0] . $mysqldate[1] . $mysqldate[2] . $mysqldate[3];
}

function to_mysqldate($normaldate) {
    $normaldate = trim($normaldate);
    return "20" . $normaldate[6] . $normaldate[7] . "-" . $normaldate[3] . $normaldate[4] . "-" . $normaldate[0] . $normaldate[1];
}

function SelBrand($tb1) {
    $str = "select tb3_id, tb3_nm from tab3 where tb3_tov_id={$tb1} and wrk3=1 order by tb3_nm";
    $result = mysql_query($str);
    return $result;
}

function SelSeas($tb1) {
    $str = "select tb10_id, tb10_nm from tab10 where tb10_tov_id={$tb1} and tb10_vis=1 order by tb10_id";
    $result = mysql_query($str);
    return $result;
}

function IdByName($nm, $tbl, $field_id, $field_name) {
    $sql = "select {$field_id} from {$tbl} where {$field_name}='{$nm}'";
    $result = mysql_query($sql);
    if (@mysql_num_rows($result) == 0) {
        return 0;
    } else {
        return @mysql_result($result, 0, $field_id);
    }
}

function IdByNameAddStup($nm) {

    $sql = "select tb12_id from tab12 where tb12_nm = '{$nm}'";
    $result = mysql_query($sql);
    if (@mysql_num_rows($result) == 0) {

        $sql = "INSERT INTO tab12 (tb12_nm, tb12_tov_id, tb12_vis) values ('{$nm}', 2, 0)";
        $result = mysql_query($sql);

        $sql = "select tb12_id from tab12 where tb12_nm = '{$nm}'";
        $result = mysql_query($sql);
        return mysql_result($result, 0, 'tb12_id');;
    } else {
        return mysql_result($result, 0, 'tb12_id');
    }
}

/*функция для отображения номенклатур по моделям "group by tab4_id"*/
function filter($tb1, $tab3, $pg, $cnt_pg, $auto) {
    $sql_lim = '';
    if ($cnt_pg > 0) {
        $first = ($pg - 1) * $cnt_pg;
        $last = $cnt_pg;
        $sql_lim = " limit {$first},{$last}";
    }
    $sql = "where tb4_tov_id={$tb1} and wrk4=1";
    if ($tab3 || $tab3 == -1) {
        if ($tab3 == 50) {
            $sql = $sql . " and (brand_id=50 or brand_id=297)";
        } else {
            $sql = $sql . " and brand_id=" . $tab3;
        }
    }

    if ($auto) {

        $sql .= " and auto_brand = " . $auto;
    }

    $order = '';
    if ($tb1 == 1) {
        if (!CheckSeas()) {
            $order = 't4ses,';
        } else {
            $order = 't4ses desc,';
        }
    }

    $sql = "select imgs.imgname as T4Pic, tb4_nm as T4Nm, tb4_id as tab4_id,
    auto as tab2_id, brand_id as tab3_id, tb3_nm, tab4.url as t4url, tab3.url as t3url,
    t4ses, t4sh, t_auto_nm, auto_brand
    from tab4 left join imgs on imgs.idmodel=tb4_id left join tab3 on tb3_id=brand_id
    left join t_auto on auto_brand = t_auto_id " . $sql . " group by tb4_id order by " . $order . "tb4_nm " . $sql_lim;
    $result = mysql_query($sql);

    return $result;
}

/*функция возвращает количество номенклатур по моделям "group by tab4_id"*/
function filter_cnt($tb1, $tab3, $auto) {
    $sql = "where tb4_tov_id={$tb1} and wrk4=1";
    if ($tab3 || $tab3 == -1) {
        if ($tab3 == 50) {
            $sql = $sql . " and (brand_id=50 or brand_id=297)";
        } else {
            $sql = $sql . " and brand_id=" . $tab3;
        }
    }
    if ($auto) {

        $sql .= " and auto_brand = " . $auto;
    }
    $sql = "select count(*) as cnt1 from tab4 " . $sql;

    return mysql_result(mysql_query($sql), 0, "cnt1");
}

function filterCntAKB(/*$tab3*/) {

    $sql = "select count(*) as cnt1 from akb_model where vis = 1;"; // and akb_brand_id = " . $tab3;
    return mysql_result(mysql_query($sql), 0, "cnt1");
}

function filterAKB(/*$tab3,*/
    $pg, $cnt_pg) {
    $sql_lim = '';
    if ($cnt_pg > 0) {

        $first = ($pg - 1) * $cnt_pg;
        $last = $cnt_pg;
        $sql_lim = " limit {$first},{$last}";
    }
    $sql = "where amd.vis = 1"; // and amd.akb_brand_id = " . $tab3;

    $sql = "select amd.pic as T4Pic, amd.name as T4Nm, amd.id as tab4_id,
    -1 as tab2_id, abr.id as tab3_id, abr.name as tb3_nm, amd.url as t4url, abr.url as t3url
    from akb_model as amd left join akb_brand as abr on abr.id = amd.akb_brand_id " . $sql . " order by amd.name " . $sql_lim;

    $result = mysql_query($sql);


    return $result;
}

function NomenDiscs($tab3, $tab5, $tab6, $tab7, $tab8, $tab9, $tab91, $tab12, $priceFrom, $priceTo, $pg, $cnt_pg, $order,
                    $orderType) {

    $str_lim = '';
    if ($cnt_pg > 0) {
        $first = ($pg - 1) * $cnt_pg;
        $last = $cnt_pg;
        $str_lim = " limit {$first},{$last}";
    }
    $sql = 'where wrk=1 and tab1_id=2';
    if (!empty($tab3)) {
        $sql .= " and tab3_id  in (" . implode(',', $tab3) . ')';
    }
    if ($tab5) {
        $sql .= ' and tab5_id=' . $tab5;
    }
    if ($tab8) {
        $sql .= ' and tab8_id=' . $tab8;
    }
    if ($tab6) {
        $sql .= ' and tab6_id=' . $tab6;
    }
    if ($tab7) {
        $sql .= ' and tab7_id=' . $tab7;
    }
    if ($tab9 <> "") {
        $sql .= ' and (REPLACE(tb9_nm, \',\', \'.\')*1)>=' . str_replace(',', '.', $tab9);
    }
    if ($tab91 <> "") {
        $sql .= ' and (REPLACE(tb9_nm, \',\', \'.\')*1)<=' . str_replace(',', '.', $tab91);
    }
    if ($tab12) {

        $tab12Nm = IdByName($tab12, 'tab12', 'tb12_nm', 'tb12_id');
        $tb12Ar = split('[,.]', $tab12Nm);
        $tab12Nm = (int)$tb12Ar[0];

        $sql .= ' and (tab12_id = ' . $tab12 . ' OR (tb12_nm * 1) BETWEEN ' . $tab12Nm . ' AND ' . ($tab12Nm + 0.99) . ')';

    }
    if ($priceFrom) {
        $sql .= ' and price >= ' . $priceFrom;
    }
    if ($priceTo) {
        $sql .= ' and price <= ' . $priceTo;
    }
    $sql = "select total.url as turl, total_id,all_name,tb3_pic as T3Pic,imgs.imgname as T4Pic,
    tb4_nm as T4Nm,tb6_nm,tb7_nm,tb8_nm,price,tb3_nm as T3Nm, tb9_nm, tb12_nm, tovimg,
    tb5_nm, tb4_nm as T4Nm,tab3_id,tab4_id,cnt, tb2_nm, tab2_id, auto_brand, t_auto_nm
    from total left join imgs on imgs.idmodel=tab4_id and imgs.idcolor=tab2_id
    left join tab3 on tb3_id=tab3_id left join tab6 on tb6_id=tab6_id
    left join tab4 on tb4_id=tab4_id left join tab7 on tb7_id=tab7_id
    left join tab8 on tb8_id=tab8_id left join tab5 on tb5_id=tab5_id
    left join tab2 on tb2_id=tab2_id left join tab10 on tb10_id=tab10_id
    left join tab9 on tb9_id=tab9_id left join tab12 on tb12_id=tab12_id
    left join t_auto ON t_auto_id = auto_brand
    " . $sql . " ORDER BY " . $order . ' ' . $orderType . ' ' . $str_lim;
    //echo $sql;
    $result = mysql_query($sql);
    return $result;
}

function ModelsDiscs($tab3, $pg, $cnt_pg) {

    $str_lim = '';
    if ($cnt_pg > 0) {

        $first = ($pg - 1) * $cnt_pg;
        $last = $cnt_pg;
        $str_lim = " limit " . $first . ", " . $last;
    }
    $sql = 'where wrk4=1 and tb4_tov_id=2';
    if (!empty($tab3)) {
        $sql .= " and tab3_id in (" . implode(',', $tab3) . ')';
    }

    $sql = "select imgs.imgname as T4Pic, tb4_nm as T4Nm,tab4_id,tab2_id,tab3_id,tb3_nm as T3Nm,tab2.translit as t2nm,
    tab4.url as t4url, tab3.url as t3url, tb2_nm, auto_brand, t_auto_nm
    from total left join imgs on imgs.idmodel=tab4_id and imgs.idcolor=tab2_id
    left join tab2 on tb2_id=tab2_id left join tab3 on tb3_id=tab3_id
    left join tab4 on tb4_id=tab4_id left join t_auto ON t_auto_id = auto_brand " . $sql . " group by tab4_id order by tb3_nm, tb4_nm " . $str_lim;
    $result = mysql_query($sql);
    //echo $sql;
    return $result;
}

function ModelsTyres($tab3, $tab10, $ship, $pg, $cnt_pg) {

    $str_lim = '';
    if ($cnt_pg > 0) {

        $first = ($pg - 1) * $cnt_pg;
        $last = $cnt_pg;
        $str_lim = " limit " . $first . ", " . $last;
    }
    $sql = 'where wrk4=1 and tb4_tov_id=1';
    if (!empty($tab3)) {
        $sql .= " and brand_id in (" . implode(',', $tab3) . ')';
    }
    if ($tab10) {

        switch ($tab10) {

            case 3:
                $sql .= ' and (t4ses=3 or t4ses=4)';
                break;
            case 53:
                $sql .= ' and t4ses = 5 and t4sh = 3';
                break;
            case 50:
                $sql .= ' and t4ses = 5 and t4sh = 0';
                break;
            default:
                $sql .= ' and t4ses=' . $tab10;
        }
    }
    if ($ship) {
        $sql .= ' and t4sh = ' . $ship;
    }

    if (!CheckSeas()) {
        $order = 't4ses,';
    } else {
        $order = 't4ses desc,';
    }

    $sql = "select tab4.url as t4url, tab3.url as t3url, imgs.imgname as T4Pic,
    tb4_nm as T4Nm,tb3_nm as T3Nm, brand_id,tb4_id, t4ses, t4sh
    FROM tab4 left join imgs on imgs.idmodel=tb4_id and brand_id = idbrand
    left join tab3 on tb3_id=brand_id " . $sql . " order by " . $order . " tb4_nm " . $str_lim;
    $result = mysql_query($sql);
    return $result;
}

function NomenTyres($tab3, $profw, $profh, $tab6, $tab10, $priceFrom, $priceTo, $ship, $pg, $cnt_pg, $order, $orderType) {
    $str_lim = '';
    if ($cnt_pg > 0) {
        $first = ($pg - 1) * $cnt_pg;
        $last = $cnt_pg;
        $str_lim = ' limit ' . $first . ', ' . $last;
    }
    $sql = 'WHERE wrk = 1 AND tab1_id = 1';
    if (!empty($tab3)) {
        $sql .= ' AND tab3_id  IN (' . implode(',', $tab3) . ')';
    }
    if ($profw) {
        $sql .= ' AND w_id=' . $profw;
    }
    if ($profh) {
        $sql .= ' AND h_id=' . $profh;
    }
    if ($tab6) {
        $sql .= ' AND tab6_id=' . $tab6;
    }
    if ($tab10) {
        switch ($tab10) {
            case 3:
                $sql .= ' AND (t4ses=3 OR t4ses=4)';
                break;
            case 53:
                $sql .= ' AND t4ses = 5 AND t4sh = 3';
                break;
            case 50:
                $sql .= ' AND t4ses = 5 AND t4sh = 0';
                break;
            default:
                $sql .= ' AND t4ses=' . $tab10;
        }
    }
    if ($priceFrom) {
        $sql .= ' AND price >= ' . $priceFrom;
    }
    if ($priceTo) {
        $sql .= ' AND price <= ' . $priceTo;
    }
    if ($ship) {
        $sql .= ' AND t4sh = ' . $ship;
    }
    $sql = 'SELECT total.url AS turl, total_id, all_name, tb3_pic as T3Pic, imgs.imgname as T4Pic, tb4_nm as T4Nm, ' .
        "tb6_nm, tb7_nm, tb8_nm, price, tb3_nm as T3Nm, tovimg, t4ses, t4sh, CONCAT(profw.name, IF(IFNULL(profh.name, '') > '', " .
        "CONCAT('/', profh.name), '')) AS prof, tb4_nm as T4Nm,tab3_id,tab4_id, rof, cnt " .
        'FROM total LEFT JOIN imgs ON imgs.idmodel = tab4_id ' .
        'LEFT JOIN tab3 ON tb3_id = tab3_id ' .
        'LEFT JOIN tab6 ON tb6_id = tab6_id ' .
        'LEFT JOIN tab4 ON tb4_id = tab4_id ' .
        'LEFT JOIN tab7 ON tb7_id = tab7_id ' .
        'LEFT JOIN tab8 ON tb8_id = tab8_id ' .
        'LEFT JOIN tab5 ON tb5_id = tab5_id ' .
        'LEFT JOIN tab2 on tb2_id=tab2_id ' .
        'LEFT JOIN tab10 on tb10_id=tab10_id ' .
        'LEFT JOIN tab9 on tb9_id=tab9_id ' .
        'LEFT JOIN tab12 on tb12_id=tab12_id ' .
        'LEFT JOIN profw ON w_id = profw.id ' .
        'LEFT JOIN profh ON h_id = profh.id ' . $sql . ' ORDER BY ' . $order . ' ' . $orderType . $str_lim;
    $result = mysql_query($sql);
//    echo $sql;
    return $result;
}

function NomenRasp($tb1) {

    $sql = 'where wrk = 1 and tab1_id = ' . $tb1;
    $sql = "select total_id,all_name,tb3_pic as T3Pic,ifnull(nullif(trim(pid),''),'-') as T4Pic,tb4_nm as T4Nm,tb6_nm,tb7_nm,tb8_nm,price,tb3_nm as T3Nm,tab9_id,tb9_nm as t9n,tb12_nm as t12n,tb5_nm as t5n,
    tab10_id,tb4_nm as T4Nm,tab2_id,tb10_pic as t10p,tb2_pic as t2p,cnt from total left join imgs on mid=tab4_id " . ($tb1 == 2 ? "and cid=tab2_id" : "") . " left join tab3 on tb3_id=tab3_id left join tab6 on tb6_id=tab6_id
    left join tab4 on tb4_id=tab4_id left join tab7 on tb7_id=tab7_id left join tab8 on tb8_id=tab8_id left join tab5 on tb5_id=tab5_id
    left join tab2 on tb2_id=tab2_id left join tab10 on tb10_id=tab10_id left join tab9 on tb9_id=tab9_id left join tab12 on tb12_id=tab12_id
    {$sql} order by cnt desc,all_name  limit 0,6";
    $result = mysql_query($sql);
    return $result;
}

function NomenCntDisc($tab3, $tab5, $tab6, $tab7, $tab8, $tab9, $tab91, $tab12, $priceFrom, $priceTo) {
    $str = 'where wrk=1 and tab1_id=2';
    if (!empty($tab3)) {
        $str .= " and tab3_id  in (" . implode(',', $tab3) . ')';
    }
    if ($tab5) {
        $str .= ' and tab5_id=' . $tab5;
    }
    if ($tab7) {
        $str .= ' and tab7_id=' . $tab7;
    }
    if ($tab6) {
        $str .= ' and tab6_id=' . $tab6;
    }
    if ($tab8) {
        $str .= ' and tab8_id=' . $tab8;
    }
    /*if($tab9)
      $str.=' and tab9_id='.$tab9;*/
    if ($tab9 <> "") {
        $str .= ' and (REPLACE(tb9_nm, \',\', \'.\')*1)>=' . str_replace(',', '.', $tab9);
    }
    if ($tab91 <> "") {
        $str .= ' and (REPLACE(tb9_nm, \',\', \'.\')*1)<=' . str_replace(',', '.', $tab91);
    }
    if ($tab12) {

        $tab12Nm = IdByName($tab12, 'tab12', 'tb12_nm', 'tb12_id');
        $tb12Ar = split('[,.]', $tab12Nm);
        $tab12Nm = (int)$tb12Ar[0];

        $str .= ' and (tab12_id = ' . $tab12 . ' OR (tb12_nm * 1) BETWEEN ' . $tab12Nm . ' AND ' . ($tab12Nm + 0.99) . ')';
    }

    if ($priceFrom) {
        $str .= ' and price >= ' . $priceFrom;
    }
    if ($priceTo) {
        $str .= ' and price <= ' . $priceTo;
    }
    $str = 'select count(*) as cnt from total left join tab9 on tb9_id=tab9_id LEFT JOIN tab12 ON tab12_id = tb12_id ' . $str;
    return mysql_result(mysql_query($str), 0, "cnt");
}

function NomenAkb($volume, $volumeFrom, $volumeTo, $volt, $rvrt, $klem, $priceFrom, $priceTo, $pg, $cnt_pg, $order,
                  $orderType) {
    $str_lim = '';
    if ($cnt_pg > 0) {

        $first = ($pg - 1) * $cnt_pg;
        $last = $cnt_pg;
        $str_lim = " limit " . $first . ", " . $last;
    }
    $str = 'where at.vis=1';
    if ($volumeFrom <> "" || $volumeTo <> "") {

        if ($volumeFrom <> "") {
            $str .= ' and (akb_v * 1) >= ' . $volumeFrom;
        }
        if ($volumeTo <> "") {
            $str .= ' and (akb_v * 1) <= ' . $volumeTo;
        }
    } else {

        if ($volume) {
            $str .= ' and id_v = ' . $volume;
        }
    }
    if ($volt) {
        $str .= ' and id_volt = ' . $volt;
    }
    if ($rvrt) {
        $str .= ' and rvrt = ' . $rvrt;
    }
    if ($klem) {
        $str .= ' and klem = ' . $klem;
    }
    if ($priceFrom) {
        $str .= ' and price >= ' . $priceFrom;
    }
    if ($priceTo) {
        $str .= ' and price <= ' . $priceTo;
    }

    $sql = "SELECT at.url as turl, at.id as total_id, at.full_name as all_name,
        ab.pic as T3Pic, am.pic as T4Pic, am.name as T4Nm, avl.name as vlname, ar.name as rname,
        price, ab.name as T3Nm, ab.id as tab3_id, am.id as tab4_id, cnt, av.name as vname
        FROM akb_tovar as at left join akb_brand as ab on at.id_brand = ab.id
        left join akb_model as am on at.id_model = am.id
        left join akb_v as av on at.id_v = av.id
        left join akb_rvrt as ar on at.rvrt = ar.id
        left join akb_volt as avl on at.id_volt = avl.id " . $str . "
        ORDER BY " . $order .' ' . $orderType . $str_lim;
    $result = mysql_query($sql);
    return $result;
}

function NomenCntAkb($volume, $volumeFrom, $volumeTo, $volt, $rvrt, $klem, $priceFrom, $priceTo) {

    $str = 'where akb_tovar.vis=1';
    if ($volumeFrom <> "" || $volumeTo <> "") {

        if ($volumeFrom <> "") {
            $str .= ' and (akb_v * 1) >= ' . $volumeFrom;
        }
        if ($volumeTo <> "") {
            $str .= ' and (akb_v * 1) <= ' . $volumeTo;
        }
    } else {

        if ($volume) {
            $str .= ' and id_v = ' . $volume;
        }
    }
    if ($volt) {
        $str .= ' and id_volt = ' . $volt;
    }
    if ($rvrt) {
        $str .= ' and rvrt = ' . $rvrt;
    }
    if ($klem) {
        $str .= ' and klem = ' . $klem;
    }
    if ($priceFrom) {
        $str .= ' and price >= ' . $priceFrom;
    }
    if ($priceTo) {
        $str .= ' and price <= ' . $priceTo;
    }
    $str = 'select count(*) as cn from akb_tovar left join akb_v on id_v = akb_v.id ' . $str;
    return mysql_result(mysql_query($str), 0, "cn");
}

function ModelsCntTyre($tab3, $tab10, $ship) {

    $str = 'where wrk4=1 and tb4_tov_id=1';
    if (!empty($tab3)) {
        $str .= " and brand_id in (" . implode(',', $tab3) . ')';
    }
    if ($tab10) {

        switch ($tab10) {

            case 3:
                $str .= ' and (t4ses=3 or t4ses=4)';
                break;
            case 53:
                $str .= ' and t4ses = 5 and t4sh = 3';
                break;
            case 50:
                $str .= ' and t4ses = 5 and t4sh = 0';
                break;
            default:
                $str .= ' and t4ses=' . $tab10;
        }
    }
    if ($ship) {
        $str .= ' and t4sh = ' . $ship;
    }
    $str = 'select count(*) as cnt from tab4 ' . $str;

    return mysql_result(mysql_query($str), 0, "cnt");
}

function ModelsCntDisc($tab3) {

    $str = 'where wrk4=1 and tb4_tov_id=2';
    if (!empty($tab3)) {
        $str .= " and brand_id in (" . implode(',', $tab3) . ')';
    }
    $str = 'select count(*) as cnt from tab4 ' . $str;
    return mysql_result(mysql_query($str), 0, "cnt");
}

function NomenCntTyre($tab3, $profw, $profh, $tab6, $tab10, $priceFrom, $priceTo, $ship) {

    $str = 'where wrk=1 and tab1_id=1';
    if (!empty($tab3)) {
        $str .= " and tab3_id  in (" . implode(',', $tab3) . ')';
    }
    if ($profw) {
        $str .= ' and w_id=' . $profw;
    }
    if ($profh) {
        $str .= ' and h_id=' . $profh;
    }
    if ($tab6) {
        $str .= ' and tab6_id=' . $tab6;
    }
    if ($tab10) {

        switch ($tab10) {

            case 3:
                $str .= ' and (t4ses=3 or t4ses=4)';
                break;
            case 53:
                $str .= ' and t4ses = 5 and t4sh = 3';
                break;
            case 50:
                $str .= ' and t4ses = 5 and t4sh = 0';
                break;
            default:
                $str .= ' and t4ses=' . $tab10;
        }
    }
    if ($priceFrom) {
        $str .= ' and price >= ' . $priceFrom;
    }
    if ($priceTo) {
        $str .= ' and price <= ' . $priceTo;
    }
    if ($ship) {
        $str .= ' and t4sh = ' . $ship;
    }
    $str = 'select count(*) as cnt from total left join tab4 on tb4_id=tab4_id ' . $str;
//    echo $str;
    return mysql_result(mysql_query($str), 0, "cnt");
}


function rustolow3($s) {
    return mb_strtolower($s);
}

function rus3lat($s) {

    $s = str_ireplace("ыа", "yha", $s);
    $s = str_ireplace("ыо", "yho", $s);
    $s = str_ireplace("ыу", "yhu", $s);
    $s = str_ireplace("ё", "yo", $s);
    $s = str_ireplace("ж", "zh", $s);
    $rus = "абвгдезийклмнопрстуфхц";
    $lat = "abvgdezijklmnoprstufxc";
    $s = strtr($s, $rus, $lat);
    $s = str_ireplace("ч", "ch", $s);
    $s = str_ireplace("Ч", "ch", $s);
    $s = str_ireplace("ш", "sh", $s);
    $s = str_ireplace("Ш", "sh", $s);
    $s = str_ireplace("щ", "shh", $s);
    $s = str_ireplace("Щ", "shh", $s);
    $s = str_ireplace("ъ", "qh", $s);
    $s = str_ireplace("Ъ", "qh", $s);
    $s = str_ireplace("ы", "y", $s);
    $s = str_ireplace("Ы", "y", $s);
    $s = str_ireplace("ь", "q", $s);
    $s = str_ireplace("Ь", "q", $s);
    $s = str_ireplace("э", "eh", $s);
    $s = str_ireplace("Э", "eh", $s);
    $s = str_ireplace("ю", "yu", $s);
    $s = str_ireplace("Ю", "yu", $s);
    $s = str_ireplace("я", "ya", $s);
    $s = str_ireplace("Я", "ya", $s);
    $s = str_ireplace("(", "_", $s);
    $s = str_ireplace(")", "_", $s);
    $s = str_ireplace(" ", "_", $s);
    $s = str_ireplace("/", "_", $s);
    $s = str_ireplace("\\", "_", $s);
    $s = str_ireplace(".", "_", $s);
    $s = str_ireplace(",", "_", $s);
    return $s;
}

function imgResSave($infile, $path_s, $path_d, $newh, $neww) {
    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "images/" . $path_s . "/" . $infile)) {
        return;
    }
    $im = imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"] . "images/" . $path_s . "/" . $infile);
    $old_h = imagesy($im);
    $old_w = imagesx($im);
    $per_h = $old_h / $newh;
    $per_w = $old_w / $neww;
    if ($per_h >= $per_w) {
        $new_w = $old_w / $per_h;
        $im1 = imagecreatetruecolor($neww, $newh);
        $ink = imagecolorallocate($im1, 255, 255, 255);
        imagefilledrectangle($im1, 0, 0, $neww, $newh, $ink);
        imagecopyresampled($im1, $im, ($neww - $new_w) / 2, 0, 0, 0, $new_w, $newh, $old_w, $old_h);
    } else {
        $new_h = $old_h / $per_w;
        $im1 = imagecreatetruecolor($neww, $newh);
        $ink = imagecolorallocate($im1, 255, 255, 255);
        imagefilledrectangle($im1, 0, 0, $neww, $newh, $ink);
        imagecopyresampled($im1, $im, 0, ($newh - $new_h) / 2, 0, 0, $neww, $new_h, $old_w, $old_h);
    }
    imagedestroy($im);
    imagejpeg($im1, "images/" . $path_d . "/" . $infile);
    imagedestroy($im1);
    return "images/" . $path_d . "/" . $infile . "<br/>";
}

function imgProtectSave($infile, $path_s, $path_d) {
    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "images/" . $path_s . "/" . $infile)) {
        return;
    }
    $im = imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"] . "images/" . $path_s . "/" . $infile);
    $im1 = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"] . "images/logo/protect.png");
    $old_h = imagesy($im);
    $old_w = imagesx($im);
    $prot_h = imagesy($im1);
    $prot_w = imagesx($im1);
    $new_w = $old_w;
    $new_h = $prot_h * $old_w / $prot_w;
    imagecopyresampled($im, $im1, 0, $old_h - $new_h, 0, 0, $new_w, $new_h, $prot_w, $prot_h);
    imagedestroy($im1);
    imagejpeg($im, "images/" . $path_d . "/" . $infile);
    imagedestroy($im);
    return "images/" . $path_d . "/" . $infile . "<br/>";
}

function ImageProtectSave($infile, $path_s, $path_d) {
    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/" . $path_s . "/" . $infile)) {
        return;
    }
    $im = imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"] . "/images/" . $path_s . "/" . $infile);
    $im1 = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"] . "/images/logo/protect.png");
    $old_h = imagesy($im);
    $old_w = imagesx($im);
    $prot_h = imagesy($im1);
    $prot_w = imagesx($im1);
    $new_w = $old_w;
    $new_h = $prot_h * $old_w / $prot_w;
    imagecopyresampled($im, $im1, 0, $old_h - $new_h, 0, 0, $new_w, $new_h, $prot_w, $prot_h);
    imagedestroy($im1);
    imagejpeg($im, "images/" . $path_d . "/" . $infile);
    imagedestroy($im);
    return 1;
}

function ImageResSave($infile, $path_s, $path_d, $newh, $neww) {
    if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $path_s . "/" . $infile)) return 0;
    $im = imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $path_s . "/" . $infile);
    $old_h = imagesy($im);
    $old_w = imagesx($im);
    $per_h = $old_h / $newh;
    $per_w = $old_w / $neww;
    if ($per_h >= $per_w) {
        $new_w = $old_w / $per_h;
        $im1 = imagecreatetruecolor($neww, $newh);
        $ink = imagecolorallocate($im1, 255, 255, 255);
        imagefilledrectangle($im1, 0, 0, $neww, $newh, $ink);
        imagecopyresampled($im1, $im, ($neww - $new_w) / 2, 0, 0, 0, $new_w, $newh, $old_w, $old_h);
    } else {
        $new_h = $old_h / $per_w;
        $im1 = imagecreatetruecolor($neww, $newh);
        $ink = imagecolorallocate($im1, 255, 255, 255);
        imagefilledrectangle($im1, 0, 0, $neww, $newh, $ink);
        imagecopyresampled($im1, $im, 0, ($newh - $new_h) / 2, 0, 0, $neww, $new_h, $old_w, $old_h);
    }
    imagedestroy($im);
    imagejpeg($im1, "images/tovar/" . $path_d . "/" . $infile);
    imagedestroy($im1);
    return 1;
}

function ImageWork($imgname, $t1, $t2, $t3, $t4, $t2tr, $t3nm, $t4nm, $pth) {

    switch ($t1) {
        case 1:
            $t2 = 0;
            $fold = 'tyres';
            break;
        case 2:
            $fold = 'discs';
            break;
        case 3:
            $t2 = 0;
            $fold = 'akb';
            break;
    }
    //if($t1==1) {$t2=0;}
    /*if($imgname==null || $imgname=='')
    {
      $imgname_old=$imgname;
      $col=str_ireplace('(','',str_ireplace(')','',$t2tr));
      $imgname=str_replace(" ","_",rus3lat(rustolow3($t3nm)."_".rustolow3($t4nm).($col>'' && $t2>0?"_".rus3lat(rustolow3($col)):"")));
      if($imgname_old==null)
        mysql_query("insert into imgs (idbrand,idmodel,idcolor,imgname) values (".$t3.",".$t4.",".$t2.",'".$imgname.".jpg')");
      if($imgname_old=='')
        mysql_query("update imgs set imgname='".$imgname.".jpg' where idbrand=".$t3." and idmodel=".$t4." and idcolor=".$t2);
    } */
    $pic = '/images/tovar/nofoto' . $pth . '.jpg';
    if ($imgname) {

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $fold . "/" . $imgname)) {

            $pic = "/images/tovar/" . $fold . $pth . "/" . $imgname;
            if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $fold . $pth)) {
                mkdir($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $fold . $pth, 0777);
            }
            if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $fold . $pth . "/" . $imgname)) {
                ImageResSave($imgname, $fold, $fold . $pth, $pth, $pth);
            }
        }
    }
    return $pic;
}

// ф. проверки сезона продаж return: 0 - summer,1 - winter
function CheckSeas() {
    $sept = mktime(0, 0, 0, 9, 1, date("Y"));
    $aip = mktime(0, 0, 0, 4, 1, date("Y"));
    $now = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    if ($sept >= $now && $aip < $now) {
        return 0;
    } else {
        return 1;
    }
}

?>