<?php

function dbLastErrorToString($errorArray = null)
{
    global $dbcon;
    if ($errorArray != null) {
        $err = $errorArray;
    } else {
        $err = $dbcon->errorInfo();
    }
    return '[' . $err[0] . ' - ' . $err[1] . '] "' . $err[2] . '"';
}

function writeExecutingResult($result, $message)
{
    if ($result === false) {
        return '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
    }
    return '<li>' . $message . ': ' . $result . ' строк</li>';
}

function query($sql, $params = [])
{
    global $dbcon;
    $res = ['result' => false, 'error' => [], 'data' => []];
    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        $res['error'] = $dbcon->errorInfo();
        return $res;
    }
    if ($stmt->execute($params)) {
        $res['result'] = true;
        $res['data'] = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
        $res['error'] = $stmt->errorInfo();
    }
    return $res;
}

function execute($sql)
{
    global $dbcon;
    $affected = $dbcon->exec($sql);
    if ($affected === false) {
        $err = $dbcon->errorInfo();
        if ($err[0] === '00000' || $err[0] === '01000') {
            return true;
        }
    }
    return $affected;
}

/*ф. обработки артикла*/
function WorkThisArt($rs_art)
{
    global $d, $tires;
    $arart = explode("|", $rs_art);
    $ret[1] = eval("return \"" . $arart[0] . "\";");
    if (strstr($ret[1], "substr")) $ret[1] = eval("return " . $ret[1] . ";");
    if (isset($arart[1])) {
        $ret[2] = eval("return \"" . $arart[1] . "\";");
    } else $ret[2] = 0;
    return $ret;
}

/*ф. обработки цены*/
function WorkThisPrice($rs_cost)
{
    global $d, $tires;
    $arprice = explode("|", $rs_cost);
    $price = eval("return \"" . $arprice[0] . "\";");

    $price_tmp = explode(',', $price);
    $price_tmp1 = explode('.', $price_tmp[0]);
    //echo $price_tmp1[0] . ' | ';
    $ret[1] = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "", $price_tmp1[0]);
    //echo $ret[1] . '<br/>';

    if (isset($arprice[1])) {
        $price1 = eval("return \"" . $arprice[1] . "\";");
        $price_tmp = explode(',', $price1);
        $price_tmp1 = explode('.', $price_tmp[0]);
        $ret[2] = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "", $price_tmp1[0]);
    } else $ret[2] = 0;
    return $ret;
}

/*ф. обработки количества*/
function WorkThisCount($rs_cnt)
{
    global $d, $tires;
    $arcnt = explode("|", $rs_cnt);
    $cnt = eval("return \"" . $arcnt[0] . "\";");
    if (strpos($cnt, '+')) {
        $cnt = eval("return " . $cnt . ';');
    }
    $cnt = update_qty($cnt);
    preg_match_all('#(\d+)#i', $cnt, $cn, PREG_SET_ORDER);
    if (!isset($cn[0][1])) {
        $ret[1] = 0;
    } else {
        $ret[1] = $cn[0][1];
    }
    if (isset($arcnt[1])) {
        $cnt1 = eval("return \"" . $arcnt[1] . "\";");
        $cnt1 = update_qty($cnt1);
        preg_match_all('#(\d+)#i', $cnt1, $cn1, PREG_SET_ORDER);
        $ret[2] = $cn1[0][1];
    } else $ret[2] = 0;
    return $ret;
}

//ф. удаление слешей при преобразовании данных из MySql в массив =============================
function array_stripslashes($arr)
{
    if (!is_array($arr)) return $arr;
    foreach ($arr as $F_key => $F_val) if ($F_key != "reg") $arr[$F_key] = stripslashes($arr[$F_key]);
    return $arr;
}

//=============================================================================================
//ф. преобразования данных из MySql в массив c удалением слешей ===============================
function sql2arr($sql)
{
    global $dbcon;
    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        return [];
    }
    if ($stmt->execute()) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return [];
//    if (($res = mysql_query($sql)) === FALSE) return FALSE;
//    if (@mysql_num_rows($res) < 0) {
//        $result = FALSE;
//    } else for ($i = 1; $result[$i] = array_stripslashes(mysql_fetch_array($res, MYSQL_ASSOC)); $i++) ;
//    @mysql_free_result($res);
//    if (isset($i) and isset($result[$i])) unset ($result[$i]);
//    return $result;
}

function DelShlack($s)
{
    $res = query('SELECT id, `name` FROM shlak');
    if ($res['result']) {
        foreach ($res['data'] as $rs) {
            preg_match_all("#(^(.*\s)?(" . $rs->name . ")(\s.*)?$)#i", $s, $dm, PREG_SET_ORDER);
            if (isset($dm[0][3]) && $dm[0][3]) {
                $s = str_replace($dm[0][3] . " ", "", $s);
                $s = str_replace(" " . $dm[0][3], "", $s);
            }
        }
    }
    return $s;
}

/*универсальная функция поиска вхождений в строку точное наименование, альтернативные написания, регулярное выражение
входные данные: 1. строка для разбора, 2. массив данных для сравнения $areadata=Array("nm" => "","id" => "","alt" => "",reg" => "");
протестировать необходимо если много альтернативных названий*/
function FindNameAltReg($areadata, $s) {    
    $val = Array("nm" => "", "id" => "0", "del" => "");
    $i = 0;
    if (isset($areadata[0])) {
        $tmpdata = Array("nm" => "", "id" => "", "del" => "");
        foreach ($areadata as $currow) {
            $surName = trim($currow["nm"]);
            $surName = str_replace('|', '\|', $surName);
            $surName = str_replace('[\|]', '|', $surName);
            $surName = str_replace('*', '\*', $surName);
            $surName = str_replace('.', '\.', $surName);
            $surName = str_replace('/', '\/', $surName);
            $surName = str_replace('+', '\+', $surName);
            $surName = str_replace('(', '\(', $surName);
            $surName = str_replace(')', '\)', $surName);
            preg_match_all("#(^(.*\s)?(" . $surName . ")(\s.*)?$)#i", $s, $nm, PREG_SET_ORDER);
            if (isset($nm[0][3]) && $nm[0][3]) {
                $tmpdata[$i]["nm"] = $currow["nm"];
                $tmpdata[$i]["id"] = $currow["id"];
                $tmpdata[$i]["del"] = $currow["nm"];
                $i++;
                continue;
            }
            if (!empty($currow["reg"])) {
                $surName = trim($currow["reg"]);
                $surName = str_replace('|', '\|', $surName);
                $surName = str_replace('[\|]', '|', $surName);
                $surName = str_replace('*', '\*', $surName);
                $surName = str_replace('.', '\.', $surName);
                $surName = str_replace('/', '\/', $surName);
                $surName = str_replace('+', '\+', $surName);
                $surName = str_replace('(', '\(', $surName);
                $surName = str_replace(')', '\)', $surName);
                preg_match_all("#" . $surName . "#i", $s, $dm, PREG_SET_ORDER);
                $j = 0;
                while (isset($dm[$j][1]) && $dm[$j][1]) {
                    if (strlen($dm[$j][1]) > strlen($tmpdata[$i]["del"])) {
                        $tmpdata[$i]["del"] = $dm[$j][1];
                    }
                    $j++;
                }
                if ($j > 0) {
                    $tmpdata[$i]["nm"] = $currow["nm"];
                    $tmpdata[$i]["id"] = $currow["id"];
                    $i++;
                    continue;
                }
            }
            $alt = "";
            if (!empty($currow["alt"])) {
                $alt = str_replace('|', '\|', $currow['alt']);
                $alt = str_replace('[\|]', '|', $alt);
                $alt = str_replace('*', '\*', $alt);
                $alt = str_replace('.', '\.', $alt);
                $alt = str_replace('/', '\/', $alt);
                $alt = str_replace('+', '\+', $alt);
                preg_match_all("#(^(.*\s)?(" . $alt . ")(\s.*)?$)#i", $s, $dm, PREG_SET_ORDER);
                if (isset($dm[0][3]) && $dm[0][3]) {
                    $tmpdata[$i]["nm"] = $currow["nm"];
                    $tmpdata[$i]["id"] = $currow["id"];
                    $tmpdata[$i]["del"] = $dm[0][3];
                    $i++;
                    continue;
                }
            }
        }
        if (isset($tmpdata[0])) {
            $val = Array("nm" => "", "id" => "", "del" => "");
            foreach ($tmpdata as $currow) {

                $val["del"] = (isset($val["del"]) ? $val["del"] : '');
                $currow["del"] = (isset($currow["del"]) ? $currow["del"] : '');
                if (strlen($val["del"]) < strlen($currow["del"])) {

                    $val["nm"] = ($currow["nm"] ? $currow["nm"] : '');
                    $val["id"] = ($currow["id"] ? $currow["id"] : '0');
                    $val["del"] = ($currow["del"] ? $currow["del"] : '');
                }
            }
        }
    }
    return $val;
}

// ф. разбора АКБ
function parseAKB()
{

    global $all_rows_check, $d, $art, $cnt, $price, $priceb, $allbrands, $cname,
           $rs, $sqlAKBNoIdArray, $supplierid, $allpoll, $allklem;
    $tov = 3; // тип товара 3 (АКБ)

    // определение бренда АКБ
    /*if($rs->t3!='') {

    $brand["nm"] = eval("return \"" . $rs->t3 . "\";");
    $brand["nm"] = charset_x_win($brand["nm"]);
    $br_tm = $brand["nm"];
    $brand = FindNameAltReg($allbrands[$tov], $brand["nm"]);
    if($brand["nm"] == '') {

      $brand["nm"] = $br_tm;
    }
  } else {

    $brand = FindNameAltReg($allbrands[$tov], $cname);
  } */

    $str1 = global_decode($cname);
    $str1 = str_replace($brand["del"] . " ", " ", " " . $str1 . " ");
    $str1 = str_replace($art . " ", " ", " " . $str1 . " ");
    while (strpos($str1, "  ")) $str1 = str_replace("  ", " ", $str1);
    $str1 .= " ";

// определение кол-ва вольт АКБ
    $volt_tmp = '';
    $volt = '';
    if ($rs->t5 != '') {

        $volt_tmp = eval("return \"" . $rs->t5 . "\";");
    } else {

        $volt_tmp = $str1;
    }

    if ($rs->t5_reg != '') {

        $volt_reg = explode("$|$", eval('return "' . $rs->t5_reg . '";'));
        preg_match_all($volt_reg[0], $volt_tmp, $vlt, PREG_SET_ORDER);
        $volt_reg[1] = (int)$volt_reg[1];
        if ($vlt[0][$volt_reg[1]]) $volt = $vlt[0][$volt_reg[1]];
        $str1 = str_replace($vlt[0][1], "", $str1);
    } else {

        if ($rs->t5 != '') {

            $volt = $volt_tmp;
        } else {

            preg_match_all("#((\d{1,2})[вВvVbB])#i", $volt_tmp, $vlt, PREG_SET_ORDER);
            if ($vlt[0][2]) $volt = $vlt[0][2];
        }
    }

    // определение объема АКБ
    $volume_tmp = '';
    $volume = '';
    if ($rs->t6 != '') {

        $volume_tmp = eval("return \"" . $rs->t6 . "\";");
    } else {
        $volume_tmp = $str1;
    }

    if ($rs->t6_reg != '') {

        $volume_reg = explode("$|$", eval('return "' . $rs->t6_reg . '";'));
        preg_match_all($volume_reg[0], $volume_tmp, $vlm, PREG_SET_ORDER);
        $volume_reg[1] = (int)$volume_reg[1];
        if ($vlm[0][$volume_reg[1]]) $volume = $vlm[0][$volume_reg[1]];
    } else {
        if ($rs->t5 != '') {
            $volume = $volume_tmp;
        } else {
            preg_match_all("#((\d{2,3})\s?(А\/ч|a/h))#i", $volume_tmp, $vlm, PREG_SET_ORDER);
            if ($vlm[0][2]) $volume = $vlm[0][2];
        }
    }

    if ($rs->t6_reg != '') {
        $str1 = str_replace($vlm[0][1], "", $str1);
    }

    // определение обратной чего-то там АКБ
    /*if(stripos($str1, 'евро') !== false || stripos($str1, 'ЕВРО') !== false){
    $rvrt = 1;
  } else {
    $rvrt = 2;
  } */

    // определение модели АКБ
    if ($rs->t4 != '') {
        $mod_tmp = eval("return \"" . $rs->t4 . "\";");
        $mod_tm = $mod_tmp;
    } else {
        $mod_tmp = $str1;
    }

    $mod_tmp = iconv("windows-1251", "utf-8", charset_x_win($mod_tmp));
    $mod_tmp = trim(str_replace($brand["del"], " ", $mod_tmp));
    $mod_tmp = DelShlack($mod_tmp);
    if ($rs->t4 != '') {
        $mod_tm = $mod_tmp;
    }
    $armodels = sql2arr("SELECT id,name as nm, reg, alt FROM `akb_model`"); //  where akb_brand_id = " . $brand['id']
    $model = FindNameAltReg($armodels, $mod_tmp);
    if ($model['nm'] == '') {
        $model['nm'] = $mod_tm;
    }

    $rvrt = FindNameAltReg($allpoll, $mod_tmp);
    $klem = FindNameAltReg($allklem, $mod_tmp);

    array_push($sqlAKBNoIdArray, "('" . $art . "', '" . $cname . "', '', 0,
      '" . str_replace("'", "\'", $model['nm']) . "', " . $model['id'] . ", '" .
        $volt . "', 0, '" . $volume . "', 0, " . ($rvrt['id'] ? $rvrt['id'] : 2) . ", " . ($klem['id'] ? $klem['id'] : 1) . ", " . $price[1] . ", " . $cnt[1] . ", " . $supplierid . ")");
}

//=============================================================================================
// ф. разбора данных шины или диски ===========================================================
function parse_data()
{

    global $fl_noidtyre, $tires, $all_rows_check, $d, $art, $cnt, $price, $priceb, $sql_id,
           $sql_noid, $sql_nobr, $all_id, $all_noid, $all_tyres, $all_discs, $all_nobr, $str1, $rs,
           $cname, $allbrands, $allomolog, $allrof, $sql_noid_tyre, $sql_noid_disc;
// определение бренда и типа товара, если тип товара не будет определен, то запись будет внесена в таблицу
    $tov = 0;
    if ($rs->tyres == 1 && $rs->wheels == 0) {
        $tov = 1;
        $all_tyres++;
    }
    if ($rs->tyres == 0 && $rs->wheels == 1) {
        $tov = 2;
        $all_discs++;
    }
    if ($rs->t3 != '') {

        $brand["nm"] = eval("return \"" . $rs->t3 . "\";");
        $brand["nm"] = iconv("windows-1251", "utf-8", charset_x_win($brand["nm"]));
        $br_tm = $brand["nm"];
        $brand = FindNameAltReg($allbrands[$tov], $brand["nm"]);
        if ($brand["nm"] == '') $brand["nm"] = $br_tm;
    } else {

        $brand = FindNameAltReg($allbrands[$tov], $cname);
    }

    // TODO: one request needs
    if ($brand['nm']) {
        $res_br = query("SELECT no_load AS nl FROM tab3 WHERE tb3_id=:id", [':id' => $brand["id"]]);
        if ($res_br['result']) {
            if ($res_br[0]->nl == 1) {
                return;
            }
        }
    }

    if ($brand["nm"] && $tov == 0) {
        $res_br = mysql_query("select tb3_tov_id from tab3 where tb3_id=" . $brand["id"]);
        $rs_br = mysql_fetch_object($res_br);
        $tov = $rs_br->tb3_tov_id;
        if ($tov == 1) $all_tyres++;
        if ($tov == 2) $all_discs++;
    }

    /*добавление в таблицу записей, у которых не определен бренд*/
    if ($tov == 0) {
        if ($all_noid > 0) $sql_noid .= ",";
        $sql_nobr .= ($all_nobr > 0 ? ',' : '') . "(" . $all_rows_check . ",'" . $art . "','" . str_replace("'", "\'",
                $cname) . "'," . $cnt[1] . "," . $price[1] . "," . $priceb[1] . "," . ($cnt[2] ? $cnt[2] : "0") . "," . ($price[2] ? $price[2] : "0") . "," . ($priceb[2] ? $priceb[2] : "0") . "," . $rs->suppl . ")";
        $all_nobr++;
        return;
    }
    /*-добавление в таблицу записей, у которых не определен бренд*/
// конец определения типа товара и бренда

    if ($tov == 1) {
        $prof = "";
        $diam = "";
        $diam1 = "";
        $igruz = "";
        $ispeed = "";
        $info = "";
        $rof = 0;
        $str1 = global_decode($cname);
        //$str1=str_replace("_"," ",$str1);
        $str1 = str_replace($brand["del"] . " ", " ", " " . $str1 . " ");
        $str1 = str_replace($art . " ", " ", " " . $str1 . " ");
        while (strpos($str1, "  ")) $str1 = str_replace("  ", " ", $str1);
        $str1 .= " ";
// профиль
        $prof_tmp = '';
        if ($rs->t5 != '') {
            $prof_tmp = eval("return \"" . $rs->t5 . "\";");
        } else $prof_tmp = $str1;
        if ($rs->t5_reg != '') {
            $prof_reg = explode("$|$", eval('return "' . $rs->t5_reg . '";'));
            preg_match_all($prof_reg[0], $prof_tmp, $prf, PREG_SET_ORDER);
            $prof_reg[1] = (int)$prof_reg[1];
            if ($prf[0][$prof_reg[1]]) $prof = $prf[0][$prof_reg[1]];
            $str1 = str_replace($prf[0][1], "", $str1);
        } else {
            if ($rs->t5 != '') {
                $prof = $prof_tmp;
            } else {
                //preg_match_all("#(((\d{2}[05])(\/(\d[05]))?)\s?(z)?(R([12][0-9])([\,\.]5)?)\s?([cсC])?)#i",$prof_tmp,$prf,PREG_SET_ORDER);
                preg_match_all("#(((\d{2}[05])\/?((\d[05]))?)\s?(z)?[rRрР\/\-](([12][0-9])([\,\.]5)?)\s?([cсCС])?(\s|$))|(((\d{2})([xXхХ*\/](\d{1,2}([\,\.]50?)?)))\s?[rRрР\/\-]([12][0-9]))#i",
                    $prof_tmp, $prf, PREG_SET_ORDER);
                if (isset($prf[0][1]) && !empty($prf[0][1])) {

                    if (isset($prf[0][2]) && $prf[0][2]) $prof = $prf[0][2];
                    if (isset($prf[0][7]) && $prf[0][7]) $diam = $prf[0][7];
                } elseif (isset($prf[0][11]) && !empty($prf[0][11])) {

                    if (isset($prf[0][13]) && $prf[0][13]) {

                        $prof = $prf[0][13];
                        $prof = str_replace('.', ',', $prof);
                        $prof = str_replace(',50', ',5', $prof);
                        $prof = str_replace('/', 'x', $prof);
                        $prof = str_replace('*', 'x', $prof);
                        $prof = str_replace('х', 'x', $prof);
                        $prof = str_replace('Х', 'x', $prof);
                        $prof = str_replace('X', 'x', $prof);
                    }
                    if (isset($prf[0][18]) && $prf[0][18]) $diam = $prf[0][18];
                }

            }
        }
//диаметр, Z, C
        if ($diam == "") {
            $diam_tmp = '';
            if ($rs->t6 != '') {
                $diam_tmp = eval("return \"" . $rs->t6 . "\";");
            } else $diam_tmp = $str1;
            if ($rs->t6_reg != '') {
                $diam_reg = explode("$|$", eval('return "' . $rs->t6_reg . '";'));
                preg_match_all($diam_reg[0], $diam_tmp, $dm, PREG_SET_ORDER);
                $diam_reg[1] = (int)$diam_reg[1];
                if ($dm[0][$diam_reg[1]]) $diam = $dm[0][$diam_reg[1]];
            } else $diam = $diam_tmp;
            if ($rs->t6_reg != '') $str1 = str_replace($dm[0][1], "", $str1);
        } else $str1 = str_replace(trim($prf[0][0]), "", $str1);
// инедкс грузоподъемности
        if ($rs->t7 != '') {
            $igr_tmp = eval("return \"" . $rs->t7 . "\";");
        } else $igr_tmp = $str1;
        if ($rs->t7_reg != '') {
            $igr_reg = explode("$|$", eval('return "' . $rs->t7_reg . '";'));
            preg_match_all($igr_reg[0], $igr_tmp, $igr, PREG_SET_ORDER);
            //echo $igr_reg[0].",".$igr_tmp.",".$igr_reg[1]." | ".$igr[0][3]." | ".$rs->t7_reg."<br/>";
            $igr_reg[1] = (int)$igr_reg[1];

            if ($igr[0][$igr_reg[1]]) $igruz = ($igr[0][$igr_reg[1]] ? $igr[0][$igr_reg[1]] : "");
            if ($rs->t8_reg != '') {
                $isp_reg = explode("$|$", eval('return "' . $rs->t8_reg . '";'));
                $isp_reg[1] = (int)$isp_reg[1];
                if ($igr_reg[0] == $isp_reg[0]) $ispeed = ($igr[0][$isp_reg[1]] ? $igr[0][$isp_reg[1]] : "");
            }
            $str1 = str_replace($igr[0][1], "", $str1);
        } else {
            if ($rs->t7 != '') {
                $igruz = $igr_tmp;
            } else {
                preg_match_all("#\s((([1-9]\d{1,2})(\/[1-9]\d{1,2})?)([A-ZНМТ]))(\s|$)#i", $igr_tmp, $igr,
                    PREG_SET_ORDER);
                if (isset($igr[0][2]) && $igr[0][2]) $igruz = $igr[0][2];
                if (isset($igr[0][5]) && $igr[0][5]) {

                    $ispeed = $igr[0][5];
                    $prof = str_replace('Н', 'H', $prof);
                    $prof = str_replace('М', 'V', $prof);
                    $prof = str_replace('Т', 'T', $prof);
                }

            }
        }
// инедкс скорости
        if (!$ispeed) {
            if ($rs->t8 != '') {
                $isp_tmp = eval("return \"" . $rs->t8 . "\";");
            } else $isp_tmp = $str1;
            if ($rs->t8_reg != '') {
                $isp_reg = explode("$|$", eval('return "' . $rs->t8_reg . '";'));
                preg_match_all($isp_reg[0], $isp_tmp, $isp, PREG_SET_ORDER);
                $isp_reg[1] = (int)$isp_reg[1];
                if ($isp[0][$isp_reg[1]]) $ispeed = ($isp[0][$isp_reg[1]] ? $isp[0][$isp_reg[1]] : "");
                $str1 = str_replace($isp[0][1], "", $str1);
            } else $ispeed = $isp_tmp;
        }
// RunFlat
        if ($rs->trof != '') {
            $rof_tmp = eval("return \"" . $rs->trof . "\";");
        } else $rof_tmp = $str1;
        $rof = FindNameAltReg($allrof, $rof_tmp);
        $str1 = str_replace($rof['del'], "", $str1);
// модель
        if ($rs->t4 != '') {
            $mod_tmp = eval("return \"" . $rs->t4 . "\";");
            $mod_tm = $mod_tmp;
        } else $mod_tmp = $str1;        
        $mod_tmp = iconv("windows-1251", "utf-8", charset_x_win($mod_tmp));        
        $mod_tmp = trim(str_replace($brand["del"], " ", $mod_tmp));        
        if ($rs->t4 == $rs->tom) $mod_tmp = str_replace((isset($omolog['del']) ? $omolog['del'] : ""), "", $mod_tmp);
        if ($rs->t4 == $rs->trof) $mod_tmp = str_replace($rof['del'], "", $mod_tmp);
        $mod_tmp = DelShlack($mod_tmp);        
        if ($rs->t4 != '') $mod_tm = $mod_tmp;
        $armodels = sql2arr("SELECT tb4_id as id,tb4_nm as nm,tb4_nm1 as reg,alern as alt FROM `tab4` where brand_id=" . $brand['id']);        
        $model = FindNameAltReg($armodels, $mod_tmp);
        if ($model['nm'] == '') $model['nm'] = (isset($mod_tm) ? $mod_tm : "");

        if (strpos($prof, '/') !== false) {

            $prof_array = explode("/", $prof);
        } elseif (strpos($prof, 'x') !== false) {

            $prof_array = explode("x", $prof);
        } elseif (!empty($prof)) {
            $prof_array[0] = $prof;
        }

        if ($diam != '' && stripos($diam, 'r') !== 0) {

            $diam = 'R' . $diam;
        }

        $sql_noid_tyre .= ($fl_noidtyre > 0 ? "," : "") . "(" . $all_rows_check . ",'" . mysql_real_escape_string($art) . "',1,'" . mysql_real_escape_string($brand['nm']) . "'," .
            $brand['id'] . ",'" . mysql_real_escape_string($model['nm']) . "'," . $model['id'] . ",'" . str_replace("'",
                "\'", $prof) . "','" .
            (isset($prof_array[0]) ? $prof_array[0] : '') . "','" . (isset($prof_array[1]) ? $prof_array[1] : '') . "','" .
            str_replace("'", "\'", $diam) . "','" . str_replace("'", "\'", $igruz) . "','" . str_replace("'", "\'",
                $ispeed) . "', '" .
            mysql_real_escape_string($cname) . "','" . ($rof['nm'] != '' ? "1" : "0") . "'," . (isset($cnt[1]) && $cnt[1] ? $cnt[1] : "0") . "," .
            (isset($price[1]) && $price[1] ? $price[1] : "0") . "," . (isset($priceb[1]) && $priceb[1] ? $priceb[1] : "0") . "," .
            (isset($cnt[2]) && $cnt[2] ? $cnt[2] : "0") . "," . (isset($price[2]) && $price[2] ? $price[2] : "0") . "," .
            (isset($priceb[2]) && $priceb[2] ? $priceb[2] : "0") . "," . $rs->suppl . ")";
        $fl_noidtyre = 1;
    }
    if ($tov == 2) {
        $shir = "";
        $diamd = "";
        $otv = "";
        $dcko = "";
        $et = "";
        $stup = "";
        $str1 = global_decode(deekr($cname));
        while (strpos($str1, "  ")) $str1 = str_replace("  ", " ", $str1);
        $str1 .= " ";
        $str1 = str_replace("_", " ", $str1);
        $str1 = str_replace("х", "x", $str1);
        $str1 = str_replace(" ЕТ", " ET", $str1);
        $str1 = str_replace(" ET ", " ET", $str1);
        $str1 = str_replace("d-", "d", $str1);
        $str1 = str_replace("dXL", "d79", $str1);
        $str1 = str_replace("dST", "d87", $str1);
        $str1 = str_replace("dL", "d75", $str1);
        $str1 = str_replace("dS", "d68", $str1);
        //$str1=str_replace("d-PSY","d68",$str1);
        //ширина диска
        $shird_tmp = '';
        if ($rs->t5 != '') {
            $shird_tmp = eval("return \"" . $rs->t5 . "\";");
        } else $shird_tmp = $str1;
        if ($rs->t5_reg != '') {
            $shird_reg = explode("$|$", eval('return "' . $rs->t5_reg . '";'));
            preg_match_all($shird_reg[0], $shird_tmp, $shird, PREG_SET_ORDER);
            $shird_reg[1] = (int)$shird_reg[1];
            if ($shird[0][$shird_reg[1]]) $shir = str_replace(".", ",",
                (float)str_replace(",", ".", $shird[0][$shird_reg[1]]));
            $str1 = str_replace($shird[0][1], "", $str1);
        } else {
            if ($rs->t5 != '') {
                $shir = str_replace(".", ",", (float)str_replace(",", ".", $shird_tmp));
            } else {
                preg_match_all("#((\d{1,2}([\,\.][50])?)[\sJ]?([хx*]\s?([12]\d))\s)#i", $shird_tmp, $shird,
                    PREG_SET_ORDER);
                if ($shird[0][2] && (int)$shird[0][5] > 11) {

                    if ($shird[0][2]) $shir = str_replace(".", ",", (float)str_replace(",", ".", $shird[0][2]));
                    if ($shird[0][5]) $diamd = $shird[0][5];
                    $str1 = str_replace($shird[0][1], "", $str1);
                } else {
                    preg_match_all("#(J?([12]\d)([хx*]\s?(\d{1,2}([\,\.][50])?)))#i", $shird_tmp, $shird,
                        PREG_SET_ORDER);
                    if ($shird[0][2]) $diamd = $shird[0][2];
                    if ($shird[0][4]) $shir = str_replace(".", ",", (float)str_replace(",", ".", $shird[0][4]));
                    $str1 = str_replace($shird[0][1], "", $str1);
                }
            }
        }
        //диаметр
        if ($diamd == "") {
            $diamd_tmp = '';
            if ($rs->t6 != '') {
                $diamd_tmp = eval("return \"" . $rs->t6 . "\";");
            } else $diamd_tmp = $str1;
            if ($rs->t6_reg != '') {
                $diamd_reg = explode("$|$", eval('return "' . $rs->t6_reg . '";'));
                if ($diamd_reg[0] == $shird_reg[0] && $rs->t6 == $rs->t5) {
                    $diamd = $shird[0][$diamd_reg[1]];
                } else {
                    preg_match_all($diamd_reg[0], $diamd_tmp, $diamdd, PREG_SET_ORDER);
                    $diamd_reg[1] = (int)$diamd_reg[1];
                    if ($diamdd[0][$diamd_reg[1]]) $diamd = $diamdd[0][$diamd_reg[1]];
                    $str1 = str_replace($diamdd[0][1], "", $str1);
                }
            } else $diamd = $diamd_tmp;
        }
        // отверстия
        $otv_tmp = '';
        if ($rs->t7 != '') {
            $otv_tmp = eval("return \"" . $rs->t7 . "\";");
        } else $otv_tmp = $str1;
        if ($rs->t7_reg != '') {
            $otv_reg = explode("$|$", eval('return "' . $rs->t7_reg . '";'));
            preg_match_all($otv_reg[0], $otv_tmp, $otvd, PREG_SET_ORDER);
            $otv_reg[1] = (int)$otv_reg[1];
            if ($otvd[0][$otv_reg[1]]) $otv = $otvd[0][$otv_reg[1]];
            $str1 = str_replace($otvd[0][1], "", $str1);
        } else {
            if ($rs->t7 != '') {
                $otv = $otv_tmp;
            } else {
                preg_match_all("#(([3-8])\s?([x\*\/]\s?((\d{2,3})([\,\.]\d)?)))#i", $otv_tmp, $otvd, PREG_SET_ORDER);
                if ($otvd[0][2]) $otv = $otvd[0][2];
                if ($otvd[0][4]) $dcko = str_replace(".", ",", (float)str_replace(",", ".", $otvd[0][4]));
            }
        }
        // ДЦКО
        if ($dcko == "") {
            $dcko_tmp = '';
            if ($rs->t8 != '') {
                $dcko_tmp = eval("return \"" . $rs->t8 . "\";");
            } else $dcko_tmp = $str1;
            if ($rs->t8_reg != '') {
                $dcko_reg = explode("$|$", eval('return "' . $rs->t8_reg . '";'));
                if ($dcko_reg[0] == $otv_reg[0] && $rs->t7 == $rs->t8) {
                    $dcko = str_replace(".", ",", $otvd[0][$dcko_reg[1]]);
                } else {
                    preg_match_all($dcko_reg[0], $dcko_tmp, $dckod, PREG_SET_ORDER);
                    $dcko_reg[1] = (int)$dcko_reg[1];
                    if ($dckod[0][$dcko_reg[1]]) $dcko = str_replace(".", ",", $dckod[0][$dcko_reg[1]]);
                    $str1 = str_replace($dckod[0][1], "", $str1);
                }
            } else $dcko = str_replace(".", ",", $dcko_tmp);
        }
        // ET
        if ($rs->t9 != '') {
            $et_tmp = eval("return \"" . $rs->t9 . "\";");
        } else $et_tmp = $str1;
        if ($rs->t9_reg != '') {
            $et_reg = explode("$|$", eval('return "' . $rs->t9_reg . '";'));
            preg_match_all($et_reg[0], $et_tmp, $etd, PREG_SET_ORDER);
            $et_reg[1] = (int)$et_reg[1];
            if ($etd[0][$et_reg[1]]) $et = str_replace("00", "0",
                str_replace(".", ",", round((float)str_replace(",", ".", $etd[0][$et_reg[1]]), 1)));
            $str1 = str_replace($etd[0][1], "", $str1);
        } else {
            if ($rs->t9 != '') {
                $et = str_replace("00", "0", str_replace(".", ",", round((float)str_replace(",", ".", $et_tmp), 1)));
            } else {
                preg_match_all("#((ЕТ|ET)\s?(\-?\d{1,3}))#i", $et_tmp, $etd, PREG_SET_ORDER);
                if (isset($etd[0][3])) $et = str_replace("00", "0",
                    str_replace(".", ",", round((float)str_replace(",", ".", $etd[0][3]), 1)));
            }
        }

        // ступица
        if ($rs->t12 != '') {
            $stup_tmp = eval("return \"" . $rs->t12 . "\";");
        } else $stup_tmp = $str1;
        if ($rs->t12_reg != '') {
            $stup_reg = explode("$|$", eval('return "' . $rs->t12_reg . '";'));
            preg_match_all($stup_reg[0], $stup_tmp, $stupd, PREG_SET_ORDER);
            $stup_reg[1] = (int)$stup_reg[1];
            if ($stupd[0][$stup_reg[1]]) $stup = str_replace(".", ",",
                round((float)str_replace(",", ".", $stupd[0][$stup_reg[1]]), 1));
            $str1 = str_replace($stupd[0][1], "", $str1);
        } else {
            if ($rs->t12 != '') {
                $stup = str_replace(".", ",", round((float)str_replace(",", ".", $stup_tmp), 1));
            } else {
                preg_match_all("#\s(d\s?(\d{2,3}([\,\.]\d{1,3})?))#i", $stup_tmp, $stupd, PREG_SET_ORDER);
                if ($stupd[0][2]) $stup = str_replace(".", ",", round((float)str_replace(",", ".", $stupd[0][2]), 1));
            }
        }
        // модель
        if ($rs->t4 != '') {
            $mod_tmp = eval("return \"" . $rs->t4 . "\";");
            $mod_tm = $mod_tmp;
        } else $mod_tmp = $str1;
        $mod_tmp = iconv("windows-1251", "utf-8", charset_x_win($mod_tmp));
        $mod_tmp = trim(str_replace($brand["del"], " ", $mod_tmp));
        if ($rs->t4 != '') $mod_tm = $mod_tmp;
        $armodels = sql2arr("SELECT tb4_id as id,tb4_nm as nm,'' as reg,alern as alt FROM `tab4` where brand_id=" . $brand['id']);
        $model = FindNameAltReg($armodels, $mod_tmp);
        if ($model['nm'] == '') $model['nm'] = $mod_tm;
        // цвета
        if ($rs->t2 != '') {
            $col_tmp = eval("return \"" . $rs->t2 . "\";");
            $col_tm = $col_tmp;
        } else $col_tmp = $str1;
        $col_tmp = iconv("windows-1251", "utf-8", charset_x_win($col_tmp));
        $col_tmp = str_replace('@цвет диска', '', $col_tmp);
        if ($rs->t2 == $rs->t4) $col_tmp = trim(str_replace($model["del"], " ", $col_tmp));
        if ($rs->t2 != '') $col_tm = $col_tmp;
        $arcolors = sql2arr("SELECT tb2_id as id,tb2_nm as nm,tb2_sn as reg,alt FROM `tab2` where brid=" . $brand['id']);
        $color = FindNameAltReg($arcolors, $col_tmp);
        if ($color['nm'] == '') $color['nm'] = $col_tm;
        //echo $dcko . '<br/>';
        $sql_noid_disc .= ($all_discs > 1 ? ',' : '') . "(" . $all_rows_check . ",'" . str_replace("'", "\'",
                $art) . "',2,'" . str_replace("'", "\'",
                $brand["nm"]) . "'," . $brand['id'] . "," . $model['id'] . ",'" . str_replace("'", "\'",
                $model['nm']) . "','" . str_replace("'", "\'", trim($shir)) . "','" . str_replace("'", "\'",
                trim($diamd)) . "','" . str_replace("'", "\'", trim($otv)) . "',
    '" . str_replace("'", "\'", trim($dcko)) . "','" . str_replace("'", "\'", trim($et)) . "','" . str_replace("'",
                "\'", trim($stup)) . "','" . str_replace("'", "\'",
                trim($color['nm'])) . "'," . trim($color['id']) . ",'" . str_replace("'", "\'",
                $cname) . "'," . $cnt[1] . "," . $price[1] . "," . $priceb[1] . "," . ($cnt[2] ? $cnt[2] : "0") . "," . ($price[2] ? $price[2] : "0") . "," . ($priceb[2] ? $priceb[2] : "0") . "," . $rs->suppl . ")";
    }
}

//====================================================================================================================================

// обработка таблицы power для шин и дисков ==========================================================================================
function ObrabotkaPower($loadid)
{
    $strres = '<h2>Перенос данных в рабочую таблицу</h2><ul class="process">';
    $res = query('SELECT * FROM parser WHERE id=:load_id', [':load_id' => $loadid]);
    if ($res['result'] === false) {
        echo '<p>Поиск загрузки. Ошибка обращения к БД. ' . dbLastErrorToString($res['error']) . '</a>';
        exit;
    }
    if (count($res['data']) == 0) {
        echo '<p>Такая загрузка не найдена. Повторите попытку или обратитесь к разработчику</p>' .
            '<a href="/lprices/">К началу загрузки прайса</a>';
        exit;
    }
    $rs_load = $res['data'][0];
    if ($rs_load->akb == 1) {
        $strres .= ObrabotkaAKB();
        $strres .= "<a href=\"/adm/work_akb/\">Обработка не отработанных позиций</a>";
        return $strres;
    }
// шипованность и сезонность для шин
    if ($rs_load->tyres == 1) {
        $result = execute('UPDATE power LEFT JOIN tab4 ON tb4_id=t4 SET t10=t4ses, t9=t4sh ' .
            'WHERE tb4_nm IS NOT NULL and t1=1');
        $message = 'Обновил шипованность+сезонность для шин';
        if ($result === false) {
            $strres .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $strres .= '<li>' . $message . ': ' . $result . ' строк</li>';
        }
    }
    if ($rs_load->wheels == 1) {
        $result = execute('UPDATE power LEFT JOIN tab4 ON tb4_id=t4 SET t10=t4ses WHERE tb4_nm IS NOT NULL and t1=2');
        $message = 'Обновил тип диска';
        if ($result === false) {
            $strres .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $strres .= '<li>' . $message . ': ' . $result . ' строк</li>';
        }
    }
    if ($rs_load->tyres == 1) {
        $result = execute("UPDATE power SET prof=replace(prof, 'х', 'x')");
        $message = 'Обновил профили x=x';
        if ($result === false) {
            $strres .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $strres .= '<li>' . $message . ': ' . $result . ' строк</li>';
        }
        $result = execute('UPDATE power LEFT JOIN profw ON p_w = profw.name AND t1=1 SET t5=profw.id ' .
            'WHERE profw.name IS NOT NULL AND t1=1');
        $message = 'Обновил ширину профиля шины';
        if ($result === false) {
            $strres .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $strres .= '<li>' . $message . ': ' . $result . ' строк</li>';
        }
        $result = execute('UPDATE power LEFT JOIN profh ON p_h = profh.name AND t1=1 SET t71=profh.id ' .
            'WHERE profh.name IS NOT NULL');
        $message = 'Обновил высоту профиля шины';
        if ($result === false) {
            $strres .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $strres .= '<li>' . $message . ': ' . $result . ' строк</li>';
        }
    }
    if ($rs_load->wheels == 1) {
        $result = execute('UPDATE power LEFT JOIN tab5 ON tb5_nm = prof AND t1=tb5_tov_id SET t5=tb5_id ' .
            'WHERE tb5_nm IS NOT NULL AND t1=2');
        $message = 'Обновил ширину диска';
        if ($result === false) {
            $strres .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $strres .= '<li>' . $message . ': ' . $result . ' строк</li>';
        }
    }

// обработка диаметров
    if ($rs_load->tyres == 1) {
        $result = execute('UPDATE power  LEFT JOIN tab4 ON tb4_id=t4 LEFT JOIN tab6 ON tb6_nm = CONCAT( diam, t4c ) ' .
            'AND tb6_tov_id=t1 SET t6=tb6_id WHERE tb6_nm IS NOT NULL AND t1 = 1');
        $strres .= writeExecutingResult($result, 'Обновил диаметры шин');
    }
    if ($rs_load->wheels == 1) {
        $result = execute('UPDATE power LEFT JOIN tab6 ON tb6_nm = diam AND tb6_tov_id=t1 SET t6=tb6_id ' .
            'WHERE tb6_nm IS NOT NULL AND t1 = 2');
        $strres .= writeExecutingResult($result, 'Обновил диаметры дисков');
    }
// обработка индекса грузоподъемности
    $result = execute('UPDATE power LEFT JOIN tab7 ON power.gruz=tab7.tb7_nm AND tb7_tov_id=t1 SET t7=tab7.tb7_id');
    $strres .= writeExecutingResult($result, 'Обновил индексы грузоподъемности');
// обработка индекса скорости
    $result = execute('UPDATE power LEFT JOIN tab8 ON power.speed=tab8.tb8_nm AND tb8_tov_id=t1 SET t8=tb8_id');
    $strres .= writeExecutingResult($result, 'Обновил индексы скорости');
    if ($rs_load->wheels) {
        $result = execute('update power left join tab9 on power.ship=tab9.tb9_nm and tb9_tov_id=t1 set t9=tb9_id where t1=2');
        $strres .= writeExecutingResult($result, 'Обновил вылет');
        $result = execute('update power left join tab12 on TRIM(LCASE(p_w))=LCASE(tb12_nm) and tb12_tov_id=t1 set t71=tb12_id where t1=2');
        $strres .= writeExecutingResult($result, 'Обновил ступицу');
        $result = execute("update power left join tab4 on t4=tb4_id left join tab2 on tb2_id=auto set t2=auto,tp=tb2_nm where t1=2 and t4>0 and tb2_nm is not null and tp='-1';");
        $strres .= writeExecutingResult($result, 'Обновил цвет по оригинальному');
    }
// обработка tid по всем параметрам
    if ($rs_load->tyres == 1) {
        $currentStepMessage = 'Привязка новых шин по параметрам';
        $result = execute('UPDATE power LEFT JOIN total ON total.tab3_id = power.t3 AND total.tab4_id = power.t4 ' .
            'AND total.w_id = power.t5 AND total.h_id = power.t71 AND total.tab6_id = power.t6 ' .
            'AND total.tab7_id = power.t7 AND total.tab8_id = power.t8 AND total.rof = power.run ' .
            'SET tid=total_id WHERE t1=1 AND t3>0 AND t4>0 AND t5>0 AND t6>0 AND t7>0 ' .
            'AND t8>0 AND tid=0 AND total_id IS NOT NULL');
        if ($result === false) {
            $strres .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $strres .= '<li>' . $currentStepMessage . ': ' . $result . '</li>';
        }
    }
    if ($rs_load->wheels == 1) {
        $result = execute('update power LEFT JOIN total ON total.tab3_id = power.t3 AND total.tab4_id = power.t4 AND total.tab5_id = power.t5
      AND total.tab6_id = power.t6 AND total.tab7_id = power.t7 AND total.tab8_id = power.t8  AND total.tab9_id = power.t9
      AND total.tab12_id = power.t71
      AND total.tab2_id = power.t2 set tid=total_id WHERE t1=2 and total_id IS NOT NULL and tid=0 and t2>0 and t3>0 and t4>0 and t5>0 and t6>0 and t7>0 and t8>0 and t9>0 and t71>0');
        $strres .= writeExecutingResult($result, 'Обновил tid по параметрам');
        $result = execute('update power left join tab3 on t3=tb3_id LEFT JOIN total ON total.tab3_id = power.t3 AND total.tab4_id = power.t4 AND total.tab5_id = power.t5
      AND total.tab6_id = power.t6 AND total.tab7_id = power.t7 AND total.tab8_id = power.t8  AND total.tab9_id = power.t9 AND total.tab12_id = power.t71
      AND total.tab2_id = power.t2 set tid=total_id WHERE t1=2 and total_id IS NOT NULL and tid=0 and t2=0 and no_color=1 and t3>0 and t4>0 and t5>0 and t6>0 and t7>0 and t8>0 and t9>0 and t71>0');
        $strres .= writeExecutingResult($result, 'Обновил tid по параметрам');
    }

// вставка новых позиций в total
    if ($rs_load->tyres == 1) {
        $currentStepMessage = 'Вставка новых записи в справочник шин';
        $result = execute('INSERT INTO total (tab1_id, tab2_id, tab3_id, tab4_id, w_id, h_id, tab6_id, tab7_id, tab8_id,' .
            'tab9_id, tab10_id, rof, dc, `dt_create`, `dt_update`) ' .
            '(SELECT t1, t2, t3, t4, t5, t71, t6, t7, t8, t9, t10, run, diam_c, SYSDATE(), SYSDATE() FROM power ' .
            'WHERE tid = 0 AND t1=1 AND t3>0 AND t4>0 AND t5>0 AND t6>0 AND t7>0 AND t8>0 ' .
            'GROUP BY t1, t2, t3, t4, t5, t71, t6, t7, t8, t9, t10, run, diam_c)');
        if ($result === false) {
            $strres .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $strres .= '<li>' . $currentStepMessage . ': ' . $result . '</li>';
        }
        $currentStepMessage = 'Обновление полного наименования шин';
        $result = execute("UPDATE total LEFT JOIN tab3 ON tab3_id = tb3_id LEFT JOIN tab4 ON tab4_id = tb4_id " .
            "LEFT JOIN profw ON w_id = profw.id LEFT JOIN profh ON h_id = profh.id LEFT JOIN tab6 ON tab6_id = tb6_id " .
            "LEFT JOIN tab7 AS t7 ON tab7_id = t7.tb7_id LEFT JOIN tab8 AS t8 ON tab8_id = t8.tb8_id " .
            "LEFT JOIN tab9 ON tab9_id = tb9_id LEFT JOIN run_flat ON run_flat.br=tab3_id " .
            "SET all_name=CONCAT(tb3_nm, ' ', tb4_nm, ' ', profw.name, IF(IFNULL(profh.name, '')>'', " .
            "CONCAT('/', profh.name), ''), ' ', tb6_nm, ' ', IFNULL(t7.tb7_nm, ''), IFNULL(t8.tb8_nm,''), " .
            "IF(rof=1, CONCAT(' ', IFNULL(run_flat.var, 'run flat')), '')) WHERE all_name IS NULL AND tab1_id=1");
        if ($result === false) {
            $strres .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $strres .= '<li>' . $currentStepMessage . ': ' . $result . '</li>';
        }
    }
    if ($rs_load->wheels == 1) {
        $result = execute("insert into total (tab1_id,tab2_id,tab3_id,tab4_id,tab5_id,tab6_id,tab7_id,tab8_id,tab9_id,tab10_id,tab12_id)
    (select t1,t2,t3,t4,t5,t6,t7,t8,t9,t10,t71 from power
    WHERE tid=0 and t1=2 and t3>0 and t4>0 and t5>0 and t6>0 and t7>0 and t8>0 and t9>0 and t10>0 and t71>0 and t2>0
    group by t1,t2,t3,t4,t5,t6,t7,t8,t9,t10,t71)");
        $strres .= writeExecutingResult($result, 'Вставили новый записи в справочник диски');
        $result = execute('insert into total (tab1_id,tab2_id,tab3_id,tab4_id,tab5_id,tab6_id,tab7_id,tab8_id,tab9_id,tab10_id,tab12_id)
    (select t1,t2,t3,t4,t5,t6,t7,t8,t9,t10,t71 from power left join tab3 on t3=tb3_id
    WHERE tid=0 and t1=2 and t3>0 and t4>0 and t5>0 and t6>0 and t7>0 and t8>0 and t9>0 and t10>0 and t71>0 and t2=0 and no_color=1
    group by t1,t2,t3,t4,t5,t6,t7,t8,t9,t10,t71)');
        $strres .= writeExecutingResult($result, 'Вставили новый записи в справочник диски');
        $result = execute('update total LEFT JOIN(SELECT total_id AS tid, ifnull( concat( \' \', tab2.tb2_nm ) , \'\' ) t2mn, tb3_nm AS t3nm, tb4_nm AS t4nm, tb5_nm AS t5nm, tb6_nm AS t6_nm, t7.tb7_nm AS mn7, t8.tb8_nm AS mn8, ifnull( concat( \' ET\', tb9_nm ) , \'\' ) AS mn9, ifnull( concat( \' D\', tb12_nm ) , \'\' ) AS mn12
    FROM total LEFT JOIN tab2 ON tab2_id = tb2_id LEFT JOIN tab3 ON tab3_id = tb3_id LEFT JOIN tab4 ON tab4_id = tb4_id LEFT JOIN tab5 ON tab5_id = tb5_id
    LEFT JOIN tab6 ON tab6_id = tb6_id LEFT JOIN tab7 AS t7 ON tab7_id = t7.tb7_id LEFT JOIN tab8 AS t8 ON tab8_id = t8.tb8_id LEFT JOIN tab9 ON tab9_id = tb9_id
    LEFT JOIN tab12 ON tab12_id = tb12_id WHERE all_name IS NULL) AS tb1 ON total_id = tb1.tid set all_name=concat( t3nm, \' \', t4nm, \' \', t5nm, \'x\', t6_nm, \' \', mn7, \'/\', mn8, mn9, mn12, t2mn )
    WHERE total.all_name IS NULL and tab1_id=2');
        $strres .= writeExecutingResult($result, 'Обновил all_name диски');
    }
// обработка tid
    if ($rs_load->tyres == 1) {
        $result = execute('update power LEFT JOIN total ON total.tab3_id = power.t3 AND total.tab4_id = power.t4 AND total.w_id = power.t5 AND total.h_id = power.t71
    AND total.tab6_id = power.t6 AND total.tab7_id = power.t7 AND total.tab8_id = power.t8 AND total.rof = power.run
    set tid=total_id WHERE t1=1 and t3>0 and t4>0 and t5>0 and t6>0 and t7>0 and t8>0 and tid=0 and total_id IS NOT NULL');
        $strres .= writeExecutingResult($result, 'Обновлили tid у вставленных записей шин');
    }
    if ($rs_load->wheels == 1) {
        $result = execute('update power LEFT JOIN total ON total.tab3_id = power.t3 AND total.tab4_id = power.t4 AND total.tab5_id = power.t5
    AND total.tab6_id = power.t6 AND total.tab7_id = power.t7 AND total.tab8_id = power.t8  AND total.tab9_id = power.t9 AND total.tab12_id = power.t71
    AND total.tab2_id = power.t2 set tid=total_id WHERE t1=2 and total_id IS NOT NULL and tid=0 and t2>0 and t3>0 and t4>0 and t5>0 and t6>0 and t7>0 and t8>0 and t9>0 and t71>0');
        $strres .= writeExecutingResult($result, 'Обновил tid по параметрам c цветом');
        $result = execute('update power left join tab3 on t3=tb3_id LEFT JOIN total ON total.tab3_id = power.t3 AND total.tab4_id = power.t4 AND total.tab5_id = power.t5
    AND total.tab6_id = power.t6 AND total.tab7_id = power.t7 AND total.tab8_id = power.t8  AND total.tab9_id = power.t9 AND total.tab12_id = power.t71
    AND total.tab2_id = power.t2 set tid=total_id WHERE t1=2 and total_id IS NOT NULL and tid=0 and t2=0 and no_color=1 and t3>0 and t4>0 and t5>0 and t6>0 and t7>0 and t8>0 and t9>0 and t71>0');
        $strres .= writeExecutingResult($result, 'Обновил tid по параметрам c цветом');
    }
    $result = execute('UPDATE total_suppl LEFT JOIN power ON power.tid=id_tov ' .
        'AND id_sup=sspid AND power.id = id_tov_sup SET cnt_sup=power.cnt, prs_sup=power.price, prsb_sup=power.priceb, ' .
        'suppl_name=price_name WHERE tid>0 AND power.tid is not null');
    $strres .= writeExecutingResult($result, 'Обновил таблицу с ценами и количеством поставщиков');
    $result = execute('UPDATE total_suppl LEFT JOIN power ON power.tid = id_tov AND id_sup = sspid ' .
        'SET id_tov_sup = power.id WHERE tid > 0 AND power.tid IS NOT NULL');
    if ($result === false) {
        $strres .= '<li class="error">Ошибка (Обновил id-ки) ' . dbLastErrorToString() . ';</li>';
    } else {
        $strres .= '<li>Обновил id-ки (' . $result . ') строк;</li>';
    }
    $result = execute('INSERT INTO total_suppl (id_tov, id_sup, cnt_sup, prs_sup, prsb_sup, id_tov_sup, suppl_name)' .
        '(SELECT tid,' . $rs_load->suppl . ', max(power.cnt), max(power.price), max(power.priceb), power.id, price_name ' .
        'FROM power LEFT JOIN total_suppl ON id_sup=' . $rs_load->suppl . ' AND tid=id_tov WHERE tid>0 ' .
        'AND total_suppl.id_tov IS NULL GROUP BY tid, power.id)');
    $strres .= writeExecutingResult($result, 'Добавили записи в таблицу с ценами и количеством поставщиков');
// обновление позиции поставщиков
    if ($rs_load->suppl == 2) {
        $result = execute('UPDATE total_suppl LEFT JOIN power ON power.tid=id_tov AND id_sup=2 ' .
            'SET cnt_sup=power.cnt, prs_sup=power.price, prsb_sup=power.priceb, suppl_name=price_name ' .
            'WHERE tid>0 AND power.tid IS NOT NULL');
        $strres .= writeExecutingResult($result, 'Обновил таблицу с ценами и количеством поставщиков');
        $result = execute('UPDATE total_suppl LEFT JOIN power ON power.tid=id_tov AND id_sup=2 ' .
            'SET id_tov_sup=power.id WHERE tid>0 AND power.tid IS NOT NULL');
        $strres .= writeExecutingResult($result, 'Обновил таблицу с ценами и количеством поставщиков');
        $result = execute('INSERT INTO total_suppl(id_tov,id_sup,cnt_sup,prs_sup,prsb_sup,id_tov_sup,suppl_name)' .
            '(SELECT tid,6,power.cnt1,power.price1,power.priceb1,power.id,price_name ' .
            'FROM power LEFT JOIN total_suppl ON id_sup=6 AND tid=id_tov ' .
            'WHERE tid>0 AND total_suppl.id_tov IS NULL)');
        $strres .= writeExecutingResult($result, 'Добавили записи в таблицу с ценами и количеством поставщиков');
    }
    $strres .= "<a href=\"/adm/work_new/\">Обработка не отработанных позиций</a>";
    return $strres;
}

function ObrabotkaAKB()
{

    $resultStr = '';
    mysql_query("update akb_tovar_temp LEFT JOIN akb_volt
    ON name_volt = akb_volt.name set id_volt = akb_volt.id WHERE akb_volt.id IS NOT NULL");
    $resultStr .= "<li>Обновил вольтаж АКБ (" . mysql_affected_rows() . ") строк;</li>";
    mysql_query("update akb_tovar_temp LEFT JOIN akb_v
    ON name_volume = akb_v.name set id_volume = akb_v.id WHERE akb_v.id IS NOT NULL");
    $resultStr .= "<li>Обновил объем АКБ (" . mysql_affected_rows() . ") строк;</li>";
    mysql_query("update akb_tovar_temp as att LEFT JOIN akb_tovar as at ON
    at.id_model = att.id_model AND at.id_volt = att.id_volt AND att.id_volume = at.id_v
    AND at.rvrt = att.rvrt AND at.klem = att.klem SET att.id_tov_akb = at.id WHERE at.id IS NOT NULL");
    $resultStr .= "<li>Обновил tid по всем параметрам (" . mysql_affected_rows() . ") строк;</li>";
    mysql_query("INSERT INTO akb_tovar (id_model, id_volt, id_v, rvrt, klem, cnt, price)
    (SELECT id_model, id_volt, id_volume, rvrt, klem, akb_count, akb_price
    FROM akb_tovar_temp WHERE id_tov_akb = 0 AND id_model >0
    AND id_volt >0 AND id_volume >0)");
    $resultStr .= "<li>Вставили новый записи в справочник АКБ (" . mysql_affected_rows() . ") строк;</li>";
    mysql_query("UPDATE akb_tovar
    LEFT JOIN akb_model ON id_model = akb_model.id
    LEFT JOIN akb_volt ON akb_volt.id = id_volt
    LEFT JOIN akb_v ON akb_v.id = id_v
    LEFT JOIN akb_rvrt ON akb_rvrt.id = rvrt
    LEFT JOIN akb_klemy ON akb_klemy.id = klem
    SET full_name = concat('АКБ ', akb_volt.name, 'В ', akb_v.name, ' А/ч',
      ' ', akb_model.name, ' ', akb_rvrt.short_name, akb_klemy.short_name) WHERE full_name IS NULL OR full_name = ''");
    $resultStr .= "<li>Обновил полное наименование АКБ <b>(" . mysql_affected_rows() . ")</b> строк;</li>";
    mysql_query("update akb_tovar_temp as att LEFT JOIN akb_tovar as at ON at.id_model = att.id_model AND at.id_volt = att.id_volt AND att.id_volume = at.id_v
    AND at.rvrt = att.rvrt AND at.klem = att.klem SET att.id_tov_akb = at.id WHERE at.id IS NOT NULL");
    $resultStr .= "<li>Обновил tid по всем параметрам (" . mysql_affected_rows() . ") строк;</li>";
    mysql_query("UPDATE akb_suppl LEFT JOIN akb_tovar_temp ON akb_tovar_temp.id_tov_akb = akb_suppl.id_tov
    AND akb_suppl.id_sup = akb_tovar_temp.id_sup AND akb_suppl.id_tov_sup = akb_tovar_temp.id_tov
    SET cnt_sup = akb_tovar_temp.akb_count, prs_sup = akb_tovar_temp.akb_price, prsb_sup = akb_tovar_temp.akb_price,
    suppl_name = full_name WHERE id_tov_akb > 0 AND akb_tovar_temp.id_tov_akb IS NOT NULL");
    $resultStr .= "<li>Обновил таблицу с ценами и количеством поставщиков (" . mysql_affected_rows() . ") строк;</li>";
    mysql_query("UPDATE akb_suppl LEFT JOIN akb_tovar_temp ON akb_tovar_temp.id_tov_akb = akb_suppl.id_tov
    AND akb_suppl.id_sup = akb_tovar_temp.id_sup SET id_tov_sup =akb_tovar_temp.id_tov
    WHERE id_tov_akb > 0 and id_tov_akb IS NOT NULL");
    $resultStr .= "<li>Обновил id-ки (" . mysql_affected_rows() . ") строк;</li>";
    mysql_query("INSERT INTO akb_suppl (id_tov, id_sup, cnt_sup, prs_sup, prsb_sup, id_tov_sup, suppl_name)
    (SELECT id_tov_akb, akb_tovar_temp.id_sup, max(akb_tovar_temp.akb_count), max(akb_tovar_temp.akb_price), max(akb_tovar_temp.akb_price),
    akb_tovar_temp.id_tov, akb_tovar_temp.full_name
    FROM akb_tovar_temp LEFT JOIN akb_suppl ON akb_suppl.id_sup=akb_tovar_temp.id_sup AND id_tov_akb = akb_suppl.id_tov
    WHERE id_tov_akb > 0 AND akb_suppl.id_tov IS NULL GROUP BY id_tov_akb, akb_tovar_temp.id)");
    $resultStr .= "<li>Добавили записи в таблицу с ценами и количеством поставщиков (" . mysql_affected_rows() . ") строк;</li>";

    return $resultStr;
}

function UpdateSp1()
{

    $result = execute('UPDATE total_suppl SET priceb=prsb_sup WHERE cnt_sup>0 AND total_suppl.prs_sup>0');
    $currentStepMessage = 'Перенос цены ';
    if ($result === false) {
        $str = '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
    } else {
        $str = '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

    //mysql_query('update total_suppl set priceb=prsb_sup where cnt_sup>0 and total_suppl.prs_sup>0');

    $result = execute('UPDATE total_suppl LEFT JOIN total ON total_id=id_tov LEFT JOIN discount AS dsc1 ' .
        'ON dsc1.disc_id_sup=total_suppl.id_sup AND dsc1.disc_id_brnd=0 AND dsc1.disc_id_ses=0 ' .
        'SET priceb=prsb_sup-(prsb_sup/100*IFNULL(dsc1.disc_per,0)) WHERE dsc1.disc_per IS NOT NULL ' .
        'AND cnt_sup>0 AND total_suppl.prs_sup>0');
    $currentStepMessage = 'Обновил закупку (скидка) по контрагенту ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total_suppl left join total on total_id=id_tov
//    left join discount as dsc1 on dsc1.disc_id_sup=total_suppl.id_sup and dsc1.disc_id_brnd=0 and dsc1.disc_id_ses=0
//    set priceb=prsb_sup-(prsb_sup/100*ifnull(dsc1.disc_per,0)) where dsc1.disc_per is not null and cnt_sup>0 and total_suppl.prs_sup>0");
//    $str = "<li>Обновил закупку (скидка) по контрагенту (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('UPDATE total_suppl LEFT JOIN total ON total_id=id_tov LEFT JOIN discount AS dsc1 ' .
        'ON dsc1.disc_id_sup=total_suppl.id_sup and dsc1.disc_id_brnd=0 and dsc1.disc_id_ses=tab10_id and dsc1.disc_id_ses>0
        set priceb=prsb_sup-(prsb_sup/100*ifnull(dsc1.disc_per,0)) where dsc1.disc_per is not null and cnt_sup>0 
        and total_suppl.prs_sup>0');
    $currentStepMessage = 'Обновил закупку (скидка) по контрагенту+сезон(тип) ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total_suppl left join total on total_id=id_tov
//    left join discount as dsc1 on dsc1.disc_id_sup=total_suppl.id_sup and dsc1.disc_id_brnd=0 and dsc1.disc_id_ses=tab10_id and dsc1.disc_id_ses>0
//    set priceb=prsb_sup-(prsb_sup/100*ifnull(dsc1.disc_per,0)) where dsc1.disc_per is not null and cnt_sup>0 and total_suppl.prs_sup>0");
//    $str .= "<li>Обновил закупку (скидка) по контрагенту+сезон(тип) (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update total_suppl left join total on total_id=id_tov
    left join discount as dsc1 on dsc1.disc_id_sup=total_suppl.id_sup and dsc1.disc_id_ses=0 and dsc1.disc_id_brnd=tab3_id and dsc1.disc_id_brnd>0
    set priceb=prsb_sup-(prsb_sup/100*ifnull(dsc1.disc_per,0)) where dsc1.disc_per is not null and cnt_sup>0 and total_suppl.prs_sup>0');
    $currentStepMessage = 'Обновил закупку (скидка) по контрагенту + бренд ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update total_suppl left join total on total_id=id_tov
//    left join discount as dsc1 on dsc1.disc_id_sup=total_suppl.id_sup and dsc1.disc_id_ses=0 and dsc1.disc_id_brnd=tab3_id and dsc1.disc_id_brnd>0
//    set priceb=prsb_sup-(prsb_sup/100*ifnull(dsc1.disc_per,0)) where dsc1.disc_per is not null and cnt_sup>0 and total_suppl.prs_sup>0");
//    $str .= "<li>Обновил закупку (скидка) по контрагенту+бренд (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update total_suppl left join total on total_id=id_tov
    left join discount as dsc1 on dsc1.disc_id_sup=total_suppl.id_sup and dsc1.disc_id_ses=tab10_id 
    and dsc1.disc_id_brnd=tab3_id and dsc1.disc_id_brnd>0  and dsc1.disc_id_ses>0
    set priceb=prsb_sup-(prsb_sup/100*ifnull(dsc1.disc_per,0)) where dsc1.disc_per is not null and cnt_sup>0 and total_suppl.prs_sup>0');
    $currentStepMessage = 'Обновил закупку (скидка) по контрагенту + бренд + сезон(тип) ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total_suppl left join total on total_id=id_tov
//    left join discount as dsc1 on dsc1.disc_id_sup=total_suppl.id_sup and dsc1.disc_id_ses=tab10_id and dsc1.disc_id_brnd=tab3_id and dsc1.disc_id_brnd>0  and dsc1.disc_id_ses>0
//    set priceb=prsb_sup-(prsb_sup/100*ifnull(dsc1.disc_per,0)) where dsc1.disc_per is not null and cnt_sup>0 and total_suppl.prs_sup>0");
//    $str .= "<li>Обновил закупку (скидка) по контрагенту+бренд+сезон(тип) (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update total_suppl left join total on total_id=total_suppl.id_tov
    left join nacenki on total_suppl.priceb>=nac_min and total_suppl.priceb<=if(nac_max>0,nac_max,100000)
    and tb1id=tab1_id and brand_id=0 and suppl_id=0
    set price_roz=round(total_suppl.priceb*(1+nac_per/100))
    where cnt_sup>0 and nac_per is not null and total_suppl.prs_sup>0');
    $currentStepMessage = 'Обновил розницу без фильтра ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total_suppl left join total on total_id=total_suppl.id_tov
//    left join nacenki on total_suppl.priceb>=nac_min and total_suppl.priceb<=if(nac_max>0,nac_max,100000)
//    and tb1id=tab1_id and brand_id=0 and suppl_id=0
//    set price_roz=round(total_suppl.priceb*(1+nac_per/100))
//    where cnt_sup>0 and nac_per is not null and total_suppl.prs_sup>0");
//    $str .= "<li>Обновил розницу без фильтра(" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update total_suppl left join total on total_id=total_suppl.id_tov
    left join nacenki on tab1_id=tb1id and total_suppl.priceb>=nac_min
    and total_suppl.priceb<=if(nac_max>0,nac_max,100000)
    and brand_id=0 and suppl_id=id_sup
    set price_roz=round(total_suppl.priceb*(1+nac_per/100))
    where cnt_sup>0 and nac_per is not null and total_suppl.prs_sup>0');
    $currentStepMessage = 'Обновил розницу фильтр: поставщик + тип товара ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total_suppl left join total on total_id=total_suppl.id_tov
//    left join nacenki on tab1_id=tb1id and total_suppl.priceb>=nac_min
//    and total_suppl.priceb<=if(nac_max>0,nac_max,100000)
//    and brand_id=0 and suppl_id=id_sup
//    set price_roz=round(total_suppl.priceb*(1+nac_per/100))
//    where cnt_sup>0 and nac_per is not null and total_suppl.prs_sup>0");
//    $str .= "<li>Обновил розницу фильтр: поставщик+тип товара (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update total_suppl left join total on total_id=total_suppl.id_tov
    left join nacenki on tab1_id=tb1id and total_suppl.priceb>=nac_min and total_suppl.priceb<=if(nac_max>0,nac_max,100000)
    and brand_id=tab3_id and suppl_id=id_sup
    set price_roz=round(total_suppl.priceb*(1+nac_per/100))
    where cnt_sup>0 and nac_per is not null and total_suppl.prs_sup>0');
    $currentStepMessage = 'Обновил розницу фильтр: поставщик+тип товара + бренд ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total_suppl left join total on total_id=total_suppl.id_tov
//    left join nacenki on tab1_id=tb1id and total_suppl.priceb>=nac_min and total_suppl.priceb<=if(nac_max>0,nac_max,100000)
//    and brand_id=tab3_id and suppl_id=id_sup
//    set price_roz=round(total_suppl.priceb*(1+nac_per/100))
//    where cnt_sup>0 and nac_per is not null and total_suppl.prs_sup>0");
//    $str .= "<li>Обновил розницу фильтр: поставщик+тип товара+бренд (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update total_suppl left join total on total_id=total_suppl.id_tov
    left join nacenki on tab1_id=tb1id and total_suppl.priceb>=nac_min and total_suppl.priceb<=if(nac_max>0,nac_max,100000)
    and brand_id=tab3_id and suppl_id=id_sup and ses_id=tab10_id
    set price_roz=round(total_suppl.priceb*(1+nac_per/100))
    where cnt_sup>0 and nac_per is not null and total_suppl.prs_sup>0');
    $currentStepMessage = 'Обновил розницу фильтр: поставщик+тип товара + бренд + сезон ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total_suppl left join total on total_id=total_suppl.id_tov
//    left join nacenki on tab1_id=tb1id and total_suppl.priceb>=nac_min and total_suppl.priceb<=if(nac_max>0,nac_max,100000)
//    and brand_id=tab3_id and suppl_id=id_sup and ses_id=tab10_id
//    set price_roz=round(total_suppl.priceb*(1+nac_per/100))
//    where cnt_sup>0 and nac_per is not null and total_suppl.prs_sup>0");
//    $str .= "<li>Обновил розницу фильтр: поставщик+тип товара+бренд+сезон (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('UPDATE total SET cnt = 0, wrk = 0');
    $currentStepMessage = 'Обнулил все шины и диски ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total set cnt = 0, wrk = 0");
//    $str .= '<li>Обнулил все шины и диски</li>';

    $result = execute('update total left join total_suppl on total_id=total_suppl.id_tov left join
    (select min(if(id_sup=24,price_roz,100000+price_roz)) as mpr,
    id_tov from total_suppl left join suppl on suppl.id = total_suppl.id_sup
    where cnt_sup>min_cnt and prs_sup>0 group by id_tov) as tbl1 on tbl1.id_tov=total_suppl.id_tov
    and mpr=if(id_sup=24,price_roz,100000+price_roz) left join suppl on suppl.id = total_suppl.id_sup
    set spid=total_suppl.id_sup,price=FLOOR( price_roz /10 ) *10,cnt=cnt_sup,wrk = 1 where tbl1.id_tov is not null and total_suppl.cnt_sup>min_cnt
    and total_suppl.prs_sup>0');
    $currentStepMessage = 'Обновил цены для сайта ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total left join total_suppl on total_id=total_suppl.id_tov left join
//    (select min(if(id_sup=24,price_roz,100000+price_roz)) as mpr,
//    id_tov from total_suppl left join suppl on suppl.id = total_suppl.id_sup
//    where cnt_sup>min_cnt and prs_sup>0 group by id_tov) as tbl1 on tbl1.id_tov=total_suppl.id_tov
//    and mpr=if(id_sup=24,price_roz,100000+price_roz) left join suppl on suppl.id = total_suppl.id_sup
//    set spid=total_suppl.id_sup,price=FLOOR( price_roz /10 ) *10,cnt=cnt_sup,wrk = 1 where tbl1.id_tov is not null and total_suppl.cnt_sup>min_cnt
//    and total_suppl.prs_sup>0");
//    $str .= "<li>Обновил цены для сайта (" . mysql_affected_rows() . ") строк;</li>";

    $str .= "<h2>Обработка флагов в справочниках</h2><ul>";

    $result = execute('update total left join tab3 on tb3_id=tab3_id left join tab6 on tb6_id=tab6_id
    set cnt=0, wrk = 0 where (tab3.no_load=1 or tab6.no_load=1) and spid<>24');
    $currentStepMessage = 'Отключил не загружаемые бренды и диаметры ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }

//    mysql_query("update total left join tab3 on tb3_id=tab3_id left join tab6 on tb6_id=tab6_id
//    set cnt=0, wrk = 0 where (tab3.no_load=1 or tab6.no_load=1) and spid<>24");
//    $str .= "<li>Обнулил бренды no_load (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update total left join tab4 on tb4_id=tab4_id and tab1_id=tb4_tov_id set tab2_id=auto where tab2_id=0 and tab1_id=1');
    $currentStepMessage = 'Обновил тип авто у шин ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update total left join tab4 on tb4_id=tab4_id and tab1_id=tb4_tov_id set tab2_id=auto where tab2_id=0 and tab1_id=1");
//    $str .= "<liОбновил тип авто у шин (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update total left join tab4 on tb4_id=tab4_id and tab1_id=tb4_tov_id set tab10_id=t4ses where tab10_id=0 and tab1_id=1');
    $currentStepMessage = 'Обновил сезонность у шин ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update total left join tab4 on tb4_id=tab4_id and tab1_id=tb4_tov_id set tab10_id=t4ses where tab10_id=0 and tab1_id=1");
//    $str .= "<liОбновил сезонность у шин (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab3 set wrk3=0');
    $currentStepMessage = 'Обнулил все бренды в справочнике ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab3 set wrk3=0");
//    $str .= "<li>Обнулил wrk3 в tab3 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab3 left join total on total.tab3_id=tb3_id and total.tab1_id=tb3_tov_id set wrk3=1 where wrk=1');
    $currentStepMessage = 'Перевел бренды в работу ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab3 left join total on total.tab3_id=tb3_id and total.tab1_id=tb3_tov_id set wrk3=1 where wrk=1");
//    $str .= "<li>wrk=1 обновил tab3 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab4 set wrk4=0');
    $currentStepMessage = 'Отключил все модели ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab4 set wrk4=0");
//    $str .= "<li>Обнулил wrk4 в tab4 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab4 left join total on total.tab4_id=tb4_id and total.tab1_id=tb4_tov_id set wrk4=1 where wrk=1;');
    $currentStepMessage = 'Перевел модели в работу ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab4 left join total on total.tab4_id=tb4_id and total.tab1_id=tb4_tov_id set wrk4=1 where wrk=1;");
//    $str .= "<li>wrk=1 обновил tab4 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('UPDATE tab5 SET tb5_vis = 0');
    $currentStepMessage = 'Обнулил tb5_vis в tab5 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab5 set tb5_vis=0");
//    $str .= "<li>Обнулил tb5_vis в tab5 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab5 left join total on total.tab5_id=tb5_id and total.tab1_id=tb5_tov_id set tb5_vis=1 where wrk=1 and tab1_id=2');
    $currentStepMessage = 'tb5_vis=1 обновил tab5 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab5 left join total on total.tab5_id=tb5_id and total.tab1_id=tb5_tov_id set tb5_vis=1 where wrk=1 and tab1_id=2");
//    $str .= "<li>tb5_vis=1 обновил tab5 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('UPDATE profw SET vis=0');
    $currentStepMessage = 'Обнулил vis в profw ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update profw set vis=0");
//    $str .= "<li>Обнулил vis в profw (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update profw left join total on total.w_id=profw.id and total.tab1_id=1 set profw.vis=1 where wrk=1 and tab1_id=1');
    $currentStepMessage = 'vis=1 обновил profw ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update profw left join total on total.w_id=profw.id and total.tab1_id=1 set profw.vis=1 where wrk=1 and tab1_id=1");
//    $str .= "<li>vis=1 обновил profw (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('UPDATE profh SET vis=0');
    $currentStepMessage = 'Обнулил vis в profh ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update profh set vis=0");
//    $str .= "<li>Обнулил vis в profh (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('UPDATE profh LEFT JOIN total ON total.h_id=profh.id AND total.tab1_id=1 SET profh.vis=1 WHERE wrk=1 AND tab1_id=1');
    $currentStepMessage = 'vis=1 обновил profh ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update profh left join total on total.h_id=profh.id and total.tab1_id=1 set profh.vis=1 where wrk=1 and tab1_id=1");
//    $str .= "<li>vis=1 обновил profh (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('UPDATE tab6 SET tb6_vis = 0');
    $currentStepMessage = 'Отключил все диаметры ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab6 set tb6_vis=0");
//    $str .= "<li>Обнулил tb6_vis в tab6 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab6 left join total on total.tab6_id=tb6_id and total.tab1_id=tb6_tov_id set tb6_vis=1 where wrk=1');
    $currentStepMessage = 'Включил диаметры ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab6 left join total on total.tab6_id=tb6_id and total.tab1_id=tb6_tov_id set tb6_vis=1 where wrk=1");
//    $str .= "<li>wrk=1 обновил tab4 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab7 set tb7_vis=0');
    $currentStepMessage = 'Обнулил tb7_vis в tab7 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab7 set tb7_vis=0");
//    $str .= "<li>Обнулил tb7_vis в tab7 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab7 left join total on total.tab7_id=tb7_id and total.tab1_id=tb7_tov_id set tb7_vis=1 where wrk=1');
    $currentStepMessage = 'wrk=1 обновил tab7 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab7 left join total on total.tab7_id=tb7_id and total.tab1_id=tb7_tov_id set tb7_vis=1 where wrk=1");
//    $str .= "<li>wrk=1 обновил tab7 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab8 set tb8_vis=0');
    $currentStepMessage = 'Обнулил tb8_vis в tab8 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab8 set tb8_vis=0");
//    $str .= "<li>Обнулил tb8_vis в tab8 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab8 left join total on total.tab8_id=tb8_id and total.tab1_id=tb8_tov_id set tb8_vis=1 where wrk=1');
    $currentStepMessage = 'wrk=1 обновил tab8 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab8 left join total on total.tab8_id=tb8_id and total.tab1_id=tb8_tov_id set tb8_vis=1 where wrk=1");
//    $str .= "<li>wrk=1 обновил tab8 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute("INSERT INTO tab_pcd (t7_id, t8_id, pcd_name, pcd_vis) SELECT tab7_id, tab8_id, concat(tb7_nm, ' x ', tb8_nm), 0
        FROM total LEFT JOIN tab_pcd AS tp ON tp.t7_id = tab7_id AND tp.t8_id = tab8_id LEFT JOIN tab8 ON tab8_id = tb8_id
        LEFT JOIN tab7 ON tab7_id = tb7_id WHERE total.tab1_id = 2 and pcd_name IS NULL GROUP BY tab7_id, tab8_id");
    $currentStepMessage = 'Добавли PCd в справочник ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("INSERT INTO tab_pcd (t7_id, t8_id, pcd_name, pcd_vis) SELECT tab7_id, tab8_id, concat(tb7_nm, ' x ', tb8_nm), 0
//        FROM total LEFT JOIN tab_pcd AS tp ON tp.t7_id = tab7_id AND tp.t8_id = tab8_id LEFT JOIN tab8 ON tab8_id = tb8_id
//        LEFT JOIN tab7 ON tab7_id = tb7_id WHERE total.tab1_id = 2 and pcd_name IS NULL GROUP BY tab7_id, tab8_id");
//    $str .= "<li>Добавли PCd в справочник (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('UPDATE tab_pcd SET pcd_vis=0');
    $currentStepMessage = 'Обнулил pcd_vis в pcd ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab_pcd set pcd_vis=0");
//    $str .= "<li>Обнулил pcd_vis в pcd (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab_pcd left join total on total.tab8_id=t8_id and total.tab7_id=t7_id and total.tab1_id = 2 set pcd_vis=1 where wrk=1');
    $currentStepMessage = 'pcd_vis = 1 обновил tab_pcd ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab_pcd left join total on total.tab8_id=t8_id and total.tab7_id=t7_id and total.tab1_id = 2 set pcd_vis=1 where wrk=1");
//    $str .= "<li>pcd_vis = 1 обновил tab_pcd (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab2 set wr2=0');
    $currentStepMessage = 'Обнулил tb2_vis в tab2 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab2 set wr2=0");
//    $str .= "<li>Обнулил tb2_vis в tab2 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab2 left join total on total.tab2_id=tb2_id set wr2=1 where wrk=1');
    $currentStepMessage = 'wrk=1 обновил tab8 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab2 left join total on total.tab2_id=tb2_id set wr2=1 where wrk=1");
//    $str .= "<li>wrk=1 обновил tab8 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab10 set tb10_vis=0');
    $currentStepMessage = 'Обнулил tb10_vis в tab10 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab10 set tb10_vis=0");
//    $str .= "<li>Обнулил tb10_vis в tab10 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab10 left join total on total.tab10_id=tb10_id and total.tab1_id=tb10_tov_id set tb10_vis=1 where wrk=1');
    $currentStepMessage = 'wrk=1 обновил tab10 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab10 left join total on total.tab10_id=tb10_id and total.tab1_id=tb10_tov_id set tb10_vis=1 where wrk=1");
//    $str .= "<li>wrk=1 обновил tab10 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab12 set tb12_vis=0');
    $currentStepMessage = 'Обнулил tb12_vis в tab12 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab12 set tb12_vis=0");
//    $str .= "<li>Обнулил tb12_vis в tab12 (" . mysql_affected_rows() . ") строк;</li>";

    $result = execute('update tab12 left join total on total.tab12_id=tb12_id and total.tab1_id=tb12_tov_id set tb12_vis=1 where wrk=1');
    $currentStepMessage = 'wrk=1 обновил tab10 ';
    if ($result === false) {
        $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
        return $str;
    } else {
        $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
    }
//    mysql_query("update tab12 left join total on total.tab12_id=tb12_id and total.tab1_id=tb12_tov_id set tb12_vis=1 where wrk=1");
//    $str .= "<li>wrk=1 обновил tab10 (" . mysql_affected_rows() . ") строк;</li></ul>";

    return $str;
}

function UpdateSpAKB()
{

    mysql_query('UPDATE akb_suppl SET priceb = prsb_sup WHERE cnt_sup > 0 AND akb_suppl.prs_sup > 0');

    mysql_query("UPDATE akb_suppl AS asp LEFT JOIN akb_tovar as at ON at.id = asp.id_tov
    LEFT JOIN akb_nacenki ON asp.priceb > nac_min and asp.priceb <= if(nac_max>0, nac_max, 100000)
    and suppl_id=0 set price_roz = round(asp.priceb * (1+nac_per/100))
    where cnt_sup>0 and nac_per is not null and asp.prs_sup>0");
    $str .= "<li>Обновил наценку без учета поставщика (" . mysql_affected_rows() . ") шт;</li>";


    mysql_query("UPDATE akb_suppl AS asp LEFT JOIN akb_tovar as at ON at.id = asp.id_tov
    LEFT JOIN akb_nacenki ON asp.priceb > nac_min and asp.priceb <= if(nac_max>0, nac_max, 100000)
    and suppl_id=id_sup set price_roz = round(asp.priceb * (1+nac_per/100))
    where cnt_sup>0 and nac_per is not null and asp.prs_sup>0");
    $str .= "<li>Обновил наценку с учетом поставщика (" . mysql_affected_rows() . ") шт;</li>";

    mysql_query("update akb_tovar set cnt = 0, vis = 0");
    $str .= '<li>Сброс видимости АКБ (' . mysql_affected_rows() . ')</li>';

    mysql_query("UPDATE akb_tovar as at LEFT JOIN akb_suppl as asp ON at.id = asp.id_tov
        LEFT JOIN (SELECT MIN(IF( id_sup = 24, price_roz, 100000 + price_roz)) as mpr, id_tov
        FROM akb_suppl LEFT JOIN suppl on suppl.id = akb_suppl.id_sup
        WHERE cnt_sup > min_cnt AND prs_sup > 0 GROUP BY id_tov) AS tbl1 ON tbl1.id_tov = asp.id_tov
        and mpr = IF(id_sup = 24, price_roz, 100000 + price_roz) LEFT JOIN suppl ON suppl.id = asp.id_sup
        SET supid = asp.id_sup, price = FLOOR( price_roz /10 ) *10, cnt = cnt_sup, vis = 1
        WHERE tbl1.id_tov is not null and asp.cnt_sup > min_cnt and asp.prs_sup>0");
    $str .= "<li>Обновил цены, количество, поставщика и видимость в таблице АКБ (" . mysql_affected_rows() . ") шт;</li>";

    mysql_query("UPDATE akb_model SET vis = 0");
    $str .= "<li>Сбросил видимость моделей (" . mysql_affected_rows() . ") шт;</li>";
    mysql_query("UPDATE akb_model LEFT JOIN akb_tovar ON akb_tovar.id_model = akb_model.id
        SET akb_model.vis = 1 where akb_tovar.vis = 1;");
    $str .= "<li>Видимые модели (" . mysql_affected_rows() . ") шт;</li>";
    mysql_query("UPDATE akb_v SET vis = 0");
    $str .= "<li>Сбросил видимость емкости (" . mysql_affected_rows() . ") шт;</li>";
    mysql_query("UPDATE akb_v LEFT JOIN akb_tovar ON akb_tovar.id_v = akb_v.id
        SET akb_v.vis = 1 where akb_tovar.vis = 1;");
    $str .= "<li>Видимые емкости (" . mysql_affected_rows() . ") шт;</li>";
    mysql_query("UPDATE akb_volt SET vis = 0");
    $str .= "<li>Сбросил видимость напряжение (" . mysql_affected_rows() . ") шт;</li>";
    mysql_query("UPDATE akb_volt LEFT JOIN akb_tovar ON akb_tovar.id_volt = akb_volt.id
        SET akb_volt.vis = 1 where akb_tovar.vis = 1;");
    $str .= "<li>Видимые напряжения (" . mysql_affected_rows() . ") шт;</li>";
    mysql_query("UPDATE akb_rvrt SET vis = 0");
    $str .= "<li>Сбросил видимость полярность (" . mysql_affected_rows() . ") шт;</li>";
    mysql_query("UPDATE akb_rvrt LEFT JOIN akb_tovar ON akb_tovar.rvrt = akb_rvrt.id
        SET akb_rvrt.vis = 1 where akb_tovar.vis = 1;");
    $str .= "<li>Видимые полярности (" . mysql_affected_rows() . ") шт;</li>";

    mysql_query("UPDATE akb_klemy SET vis = 0");
    $str .= "<li>Сбросил видимость клемы (" . mysql_affected_rows() . ") шт;</li>";
    mysql_query("UPDATE akb_klemy LEFT JOIN akb_tovar ON akb_tovar.klem = akb_klemy.id
        SET akb_klemy.vis = 1 where akb_tovar.vis = 1;");
    $str .= "<li>Видимые клемы (" . mysql_affected_rows() . ") шт;</li>";

    return $str;
}

//====================================================================================================================================
function ekr($str)
{
    $str = str_replace("+", "(plus)", $str);
    $str = str_replace("~", "(tilda)", $str);
    $str = str_replace("'", "(apostrof)", $str);
    $str = str_replace("|", '(vslash)', $str);
    $str = str_replace('"', "(quote)", $str);
    $str = str_replace('/', "(slash)", $str);
    $str = str_replace('&', "(and)", $str);
    return $str;
}

function deekr($str)
{
    $str = str_replace("(plus)", "+", $str);
    $str = str_replace("(tilda)", "~", $str);
    $str = str_replace("(apostrof)", "'", $str);
    $str = str_replace('(vslash)', "|", $str);
    $str = str_replace("(quote)", '"', $str);
    $str = str_replace("(slash)", '/', $str);
    $str = str_replace("(and)", "&", $str);
    return $str;
}

function global_decode(
    $str
)
{
    return strtr($str, array(
        '%u0401' => 'Ё',

        '%u0410' => 'А',
        '%u0411' => 'Б',
        '%u0412' => 'В',
        '%u0413' => 'Г',
        '%u0414' => 'Д',
        '%u0415' => 'Е',
        '%u0416' => 'Ж',
        '%u0417' => 'З',
        '%u0418' => 'И',
        '%u0419' => 'Й',
        '%u041A' => 'К',
        '%u041B' => 'Л',
        '%u041C' => 'М',
        '%u041D' => 'Н',
        '%u041E' => 'О',
        '%u041F' => 'П',
        '%u0420' => 'Р',
        '%u0421' => 'С',
        '%u0422' => 'Т',
        '%u0423' => 'У',
        '%u0424' => 'Ф',
        '%u0425' => 'Х',
        '%u0426' => 'Ц',
        '%u0427' => 'Ч',
        '%u0428' => 'Ш',
        '%u0429' => 'Щ',
        '%u042A' => 'Ъ',
        '%u042B' => 'Ы',
        '%u042C' => 'Ь',
        '%u042D' => 'Э',
        '%u042E' => 'Ю',
        '%u042F' => 'Я',
        '%u0430' => 'а',
        '%u0431' => 'б',
        '%u0432' => 'в',
        '%u0433' => 'г',
        '%u0434' => 'д',
        '%u0435' => 'е',
        '%u0436' => 'ж',
        '%u0437' => 'з',
        '%u0438' => 'и',
        '%u0439' => 'й',
        '%u043A' => 'к',
        '%u043B' => 'л',
        '%u043C' => 'м',
        '%u043D' => 'н',
        '%u043E' => 'о',
        '%u043F' => 'п',
        '%u0440' => 'р',
        '%u0441' => 'с',
        '%u0442' => 'т',
        '%u0443' => 'у',
        '%u0444' => 'ф',
        '%u0445' => 'х',
        '%u0446' => 'ц',
        '%u0447' => 'ч',
        '%u0448' => 'ш',
        '%u0449' => 'щ',
        '%u044A' => 'ъ',
        '%u044B' => 'ы',
        '%u044C' => 'ь',
        '%u044D' => 'э',
        '%u044E' => 'ю',
        '%u044F' => 'я',

        '%u0451' => 'ё',

        '%u2116' => '№',

        chr(208) . chr(129) => 'Ё',

        chr(208) . chr(144) => 'А',
        chr(208) . chr(145) => 'Б',
        chr(208) . chr(146) => 'В',
        chr(208) . chr(147) => 'Г',
        chr(208) . chr(148) => 'Д',
        chr(208) . chr(149) => 'Е',
        chr(208) . chr(150) => 'Ж',
        chr(208) . chr(151) => 'З',
        chr(208) . chr(152) => 'И',
        chr(208) . chr(153) => 'Й',
        chr(208) . chr(154) => 'К',
        chr(208) . chr(155) => 'Л',
        chr(208) . chr(156) => 'М',
        chr(208) . chr(157) => 'Н',
        chr(208) . chr(158) => 'О',
        chr(208) . chr(159) => 'П',
        chr(208) . chr(160) => 'Р',
        chr(208) . chr(161) => 'С',
        chr(208) . chr(162) => 'Т',
        chr(208) . chr(163) => 'У',
        chr(208) . chr(164) => 'Ф',
        chr(208) . chr(165) => 'Х',
        chr(208) . chr(166) => 'Ц',
        chr(208) . chr(167) => 'Ч',
        chr(208) . chr(168) => 'Ш',
        chr(208) . chr(169) => 'Щ',
        chr(208) . chr(170) => 'Ъ',
        chr(208) . chr(171) => 'Ы',
        chr(208) . chr(172) => 'Ь',
        chr(208) . chr(173) => 'Э',
        chr(208) . chr(174) => 'Ю',
        chr(208) . chr(175) => 'Я',
        chr(208) . chr(176) => 'а',
        chr(208) . chr(177) => 'б',
        chr(208) . chr(178) => 'в',
        chr(208) . chr(179) => 'г',
        chr(208) . chr(180) => 'д',
        chr(208) . chr(181) => 'е',
        chr(208) . chr(182) => 'ж',
        chr(208) . chr(183) => 'з',
        chr(208) . chr(184) => 'и',
        chr(208) . chr(185) => 'й',
        chr(208) . chr(186) => 'к',
        chr(208) . chr(187) => 'л',
        chr(208) . chr(188) => 'м',
        chr(208) . chr(189) => 'н',
        chr(208) . chr(190) => 'о',
        chr(208) . chr(191) => 'п',

        chr(209) . chr(128) => 'р',
        chr(209) . chr(129) => 'с',
        chr(209) . chr(130) => 'т',
        chr(209) . chr(131) => 'у',
        chr(209) . chr(132) => 'ф',
        chr(209) . chr(133) => 'х',
        chr(209) . chr(134) => 'ц',
        chr(209) . chr(135) => 'ч',
        chr(209) . chr(136) => 'ш',
        chr(209) . chr(137) => 'щ',
        chr(209) . chr(138) => 'ъ',
        chr(209) . chr(139) => 'ы',
        chr(209) . chr(140) => 'ь',
        chr(209) . chr(141) => 'э',
        chr(209) . chr(142) => 'ю',
        chr(209) . chr(143) => 'я',

        chr(209) . chr(145) => 'ё',

        'в„–' => '№',
#        '&' => '&',
    ));
}

/*if (!function_exists('str_getcsv'))
{
  function str_getcsv($input, $delimiter=',', $enclosure='"', $escape=null, $eol=null)
  {
    $temp=fopen("php://memory", "rw");
    fwrite($temp, $input);
    fseek($temp, 0);
    $r=fgetcsv($temp, 4096, $delimiter, $enclosure);
    fclose($temp);
    return $r;
  }
}*/

function update_qty($qty)
{
    if (strstr($qty, "нет")) return 0;
    if (strstr($qty, "Да")) return 30;
    if (strstr($qty, "в наличии")) return 30;
    if (strstr($qty, "<4")) return 1;
    if (strstr($qty, "много")) return 20;
    if (strstr($qty, ">")) $qty = str_replace(">", "", $qty);
    if (strstr($qty, "более")) $qty = str_replace("более", "", $qty);
    if (strstr($qty, "больше")) $qty = str_replace("больше", "", $qty);
    $qty = trim($qty);
    return $qty;
}

function min_value($v1, $v2)
{
    if (empty($v1) and !empty($v2)) {
        return $v2 + 0;
    } elseif (!empty($v1) and empty($v2)) return $v1 + 0;
    elseif (empty($v1) and empty($v2)) return 0;
    if ($v1 == 0) return $v2 + 0;
    if ($v1 > $v2) return $v1 + 0; else return $v2 + 0;
}

?>