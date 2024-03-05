<?php

require ('funcSQL.php');

$title = 'Подбор шин и дисков по авто. Интернет магазин Ringshina.';
$desk = 'Подобрать шины и диски мировых производителей по параметрам автомобиля.';
$kw = 'интернет магазин зимняя резина шины диски купить дешево подбор по авто';

$autoVend = filter_input(INPUT_GET, 'vend');
$autoModel = filter_input(INPUT_GET, 'model');
$autoYear = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT);
$autoModif = filter_input(INPUT_GET, 'modif');
$autoVendName = filter_input(INPUT_GET, 'vend_name');
$autoModelName = filter_input(INPUT_GET, 'model_name');
$autoModifName = filter_input(INPUT_GET, 'modif_name');

//echo $autoModif; die;

$content .= '<ul class="breadcrumbs"><li><a href="/">Главная</a></li><li' . (!$autoModif ? 'class="current"' : '') . '>' .
    '<a href="/podborauto/">Подбор по авто</a></li>' . ($autoModif ? '<li class="current">' .
        '<a href="' . $_SERVER["REQUEST_URI"] . '">' . $autoVend . ' ' . $autoModel . ' ' . $autoYear . ' ' . $autoModif .
        '</a></li>' : '') . '</ul>';

if ($autoModif) {
    $content .= '<h2>Подбор по авто: ' . $autoVendName . ' ' . $autoModelName . ' ' . $autoYear . ' ' . $autoModifName . '</h2>';
//    $res = query('SELECT pcd, diametr, gaika, zavod_shini, zamen_shini, tuning_shini, zavod_diskov, zamen_diskov, ' .
//        'tuning_diski FROM podbor_shini_i_diski WHERE vendor = :vendor AND car = :car ' .
//        'AND `year` = :year AND modification = :modification',
//        [':vendor' => $autoVend, ':car' => $autoModel, ':year' => $autoYear, ':modification' => $autoModif]);
    $res = getData($autoVend, $autoModel, $autoYear, $autoModif);
    //$res = callAPI("https://api.wheel-size.com/v2/search/by_model/?make=" . $autoVend . "&model=" . $autoModel . "&modification=" . $autoModif . "&year=" . $autoYear . "&user_key=467886d78550ce67d42cd4591173155a");
//    if ($res['result'] === true) {
//        return $result;
//    }
//    foreach ($res['data'] as $rs) {
//        $result[] = $rs->id;
//    }

    //$res = mysql_query("SELECT pcd,diametr,gaika,zavod_shini,zamen_shini,tuning_shini,zavod_diskov,zamen_diskov,tuning_diski from podbor_shini_i_diski where vendor='" . $_GET["vend"] . "' and car='" . $_GET["model"] . "' and year='" . $_GET["year"] . "' and modification='" . $_GET["modif"] . "'");

    if ($res && count($res) > 0) {
        $rs = $res;
        $content .= '<div class="row"><div class="small-12 columns"><h3 class="podbor">Общие параметры шин и дисков</h3>
            <ul><li>Ступица: ' . $rs["technical"]["centre_bore"] . '</li><li>' .
            ($rs["technical"]["wheel_fasteners"]["type"] == "Lug bolts" ? "Болты: " : "Гайки: ") .
            $rs["technical"]["wheel_fasteners"]["thread_size"] . '</li><li>PCD: ' . $rs["technical"]["stud_holes"] . "*" .
            $rs["technical"]["pcd"] . '</li></ul></div></div>';
        $content .= '<div class="row">';
        /*Заводская комплектация*/
        $leftStr = '';
        $rightStr = '';
        //preg_match_all("#(\d)\*\s?(\d{2,3}([\.\,]\d)?(\/\d{1,3})?)#i", trim($rs->pcd), $posit, PREG_SET_ORDER);
        $otv = IdByName($rs["technical"]["stud_holes"], "tab7", "tb7_id", "tb7_nm");
        //$dcko = IdByName($rs["technical"]["pcd"], "tab8", "tb8_id", "tb8_nm");
        $pcd = IdByName($rs["technical"]["stud_holes"] . ' x ' . $rs["technical"]["pcd"], "tab_pcd", "id", "pcd_name");
        $stupName = str_replace('.', ',', $rs["technical"]["centre_bore"]);
        $stup = IdByNameAddStup($stupName);
        // стоковые диски
        $rimContent = getRimContent($rs["wheels"], true, $pcd, $stup);
        if ($rimContent["leftStr"] != '') {
            $content .= '<div class="large-6 columns"><h3 class="podbor">Заводская комплектация дисков</h3>';
            if ($rimContent["rightStr"] != '') {

                $content .= '<div class="row"><div class="small-6 columns"><ul>' . $rimContent["leftStr"] .
                    '</ul></div><div class="small-6 columns"><ul>' . $rimContent["rightStr"] . '</ul></div></div>';
            } else {

                $content .= '<ul>' . $rimContent["leftStr"] . '</ul>';
            }
            $content .= '</div>';
        }


        // стоковые шины
        $tireContent = getTireContent($rs["wheels"], true);

        if ($tireContent["leftStr"] != '') {

            $content .= '<div class="large-6 columns"><h3 class="podbor">Заводская комплектация шин</h3>';
            if ($tireContent["rightStr"] != '') {

                $content .= '<div class="row"><div class="small-6 columns"><ul>' . $tireContent["leftStr"] .
                    '</ul></div><div class="small-6 columns"><ul>' . $tireContent["rightStr"] . '</ul></div></div>';
            } else {

                $content .= '<ul>' . $tireContent["leftStr"] . '</ul>';
            }
            $content .= '</div>';
        }
        $content .= '</div>';

        /*Варианты замены*/
        $content .= '<div class="row">';
        $leftStr = '';
        $rightStr = '';
        //preg_match_all("#(\d)\*\s?(\d{2,3}([\.\,]\d)?(\/\d{1,3})?)#i", trim($rs->pcd), $posit, PREG_SET_ORDER);
        $otv = IdByName($rs["technical"]["stud_holes"], "tab7", "tb7_id", "tb7_nm");
        //$dcko = IdByName($rs["technical"]["pcd"], "tab8", "tb8_id", "tb8_nm");
        $pcd = IdByName($rs["technical"]["stud_holes"] . ' x ' . $rs["technical"]["pcd"], "tab_pcd", "id", "pcd_name");

        $rimContent = getRimContent($rs["wheels"], false, $pcd, $stup);

        if ($rimContent["leftStr"] != '') {

            $content .= '<div class="large-6 columns"><h3 class="podbor">Варианты замены дисков</h3>';
            if ($rimContent["rightStr"] != '') {

                $content .= '<div class="row"><div class="small-6 columns"><ul>' . $rimContent["leftStr"] .
                    '</ul></div><div class="small-6 columns"><ul>' . $rimContent["rightStr"] . '</ul></div></div>';
            } else {

                $content .= '<ul>' . $rimContent["leftStr"] . '</ul>';
            }
            $content .= '</div>';
        }

        $leftStr = '';
        $rightStr = '';
        $tireContent = getTireContent($rs["wheels"], false);

        if ($tireContent["leftStr"] != '') {

            $content .= '<div class="large-6 columns"><h3 class="podbor">Варианты замены шин</h3>';
            if ($tireContent["rightStr"] != '') {

                $content .= '<div class="row"><div class="small-6 columns"><ul>' . $tireContent["leftStr"] .
                    '</ul></div><div class="small-6 columns"><ul>' . $tireContent["rightStr"] . '</ul></div></div>';
            } else {

                $content .= '<ul>' . $tireContent["leftStr"] . '</ul>';
            }
            $content .= '</div>';
        }
        $content .= '</div>';

        /*Варианты тюнинга*/
        /*$content .= '<div class="row">';
        $leftStr = '';
        $rightStr = '';
        preg_match_all("#(\d)\*\s?(\d{2,3}([\.\,]\d)?(\/\d{1,3})?)#i", trim($rs->pcd), $posit, PREG_SET_ORDER);
        $otv = IdByName($posit[0][1], "tab7", "tb7_id", "tb7_nm");
        //$dcko = IdByName($posit[0][2], "tab8", "tb8_id", "tb8_nm");
        $pcd = IdByName($posit[0][1] . ' x ' . $posit[0][2], "tab_pcd", "id", "pcd_name");
        $sh = explode("|", $rs->tuning_diski);
        $fl = 0;

        for ($i = 0; $i < sizeof($sh); $i++) {

            if (strpos($sh[$i], "#") !== false) {

                $sh1 = explode("#", $sh[$i]);

                if (!$fl) {

                    $leftStr .= '<li>Передняя ось</li>';
                    $rightStr .= '<li>Задняя ось</li>';
                    $fl++;
                }
                preg_match_all("#(\d+([\.\,]\d)?)\s?x\s?(\d{1,2})\s?[EЕ][TТ]\s?((-)?\d{1,2}([\.\,]\d{1,2})?)#i", trim($sh1[0]), $posit1, PREG_SET_ORDER);
                $diam = IdByName($posit1[0][3], "tab6", "tb6_id", "tb6_nm");
                $vilb = IdByName($posit1[0][4], "tab9", "tb9_id", "tb9_nm");
                $leftStr .= '<li><a href="/param/diski/?paramsmb=1&pcd=' . $pcd . '&diamd=' . $diam . '&stup=' . $stup . '&type=0&vilb=' . $vilb . '" style="text-decoration: underline;" target="_blank">' . $sh1[0] . '</a></li>';
                preg_match_all("#(\d+([\.\,]\d)?)\s?x\s?(\d{1,2})\s?[EЕ][TТ]\s?((-)?\d{1,2}([\.\,]\d{1,2})?)#i", trim($sh1[1]), $posit1, PREG_SET_ORDER);
                $diam = IdByName($posit1[0][3], "tab6", "tb6_id", "tb6_nm");
                $vilb = IdByName($posit1[0][4], "tab9", "tb9_id", "tb9_nm");
                $rightStr .= '<li><a href="/param/diski/?paramsmb=1&pcd=' . $pcd . '&diamd=' . $diam . '&stup=' . $stup . '&type=0&vilb=' . $vilb . '" style="text-decoration: underline;" target="_blank">' . $sh1[1] . '</a></li>';
            } else {

                preg_match_all("#(\d+([\.\,]\d)?)\s?x\s?(\d{1,2})\s?[EЕ][TТ]\s?((-)?\d{1,2}([\.\,]\d{1,2})?)#i", trim($sh[$i]), $posit, PREG_SET_ORDER);
                $diam = IdByName($posit[0][3], "tab6", "tb6_id", "tb6_nm");
                $vilb = IdByName($posit[0][4], "tab9", "tb9_id", "tb9_nm");
                $leftStr .= '<li><a href="/param/diski/?paramsmb=1&pcd=' . $pcd . '&diamd=' . $diam . '&stup=' . $stup . '&type=0&vilb=' . $vilb . '" style="text-decoration: underline;" target="_blank">' . $sh[$i] . '</a></li>';
            }
        }

        if ($leftStr != '') {

            $content .= '<div class="large-6 columns"><h3 class="podbor">Диски - варианты тюнинга</h3>';
            if ($rightStr != '') {

                $content .= '<div class="row"><div class="small-6 columns"><ul>' . $leftStr . '</ul></div><div class="small-6 columns"><ul>' . $rightStr . '</ul></div></div>';
            } else {

                $content .= '<ul>' . $leftStr . '</ul>';
            }
            $content .= '</div>';
        }

        $leftStr = '';
        $rightStr = '';
        $sh = explode("|", $rs->tuning_shini);
        $fl = 0;
        for ($i = 0; $i < sizeof($sh); $i++) {

            if (strpos($sh[$i], "#") !== false) {

                $sh1 = explode("#", $sh[$i]);
                if (!$fl) {

                    $leftStr .= '<li>Передняя ось</li>';
                    $rightStr .= '<li>Задняя ось</li>';
                    $fl++;
                }
                preg_match_all("#(\d+)(\/(\d{1,2}))?\s(R\d{1,2})#i", trim($sh1[0]), $posit1, PREG_SET_ORDER);
                $diam = IdByName($posit1[0][4], "tab6", "tb6_id", "tb6_nm");
                $profw = IdByName($posit1[0][1], "profw", "id", "name");
                $profh = IdByName($posit1[0][3], "profh", "id", "name");
                $leftStr .= '<li><a href="/param/shini/?paramsmb=1&prfw=' . $profw . '&prfh=' . $profh . '&diam=' . $diam . '&seas=0" style="text-decoration: underline;" target="_blank">' . $sh1[0] . '</a></li>';
                preg_match_all("#(\d+)(\/(\d{1,2}))?\s(R\d{1,2})#i", trim($sh1[1]), $posit1, PREG_SET_ORDER);
                $diam = IdByName($posit1[0][4], "tab6", "tb6_id", "tb6_nm");
                $profw = IdByName($posit1[0][1], "profw", "id", "name");
                $profh = IdByName($posit1[0][3], "profh", "id", "name");
                $rightStr .= '<li><a href="/param/shini/?paramsmb=1&prfw=' . $profw . '&prfh=' . $profh . '&diam=' . $diam . '&seas=0" style="text-decoration: underline;" target="_blank">' . $sh1[1] . '</a></li>';
            } else {

                preg_match_all("#(\d+)(\/(\d{1,2}))?\s(R\d{1,2})#i", trim($sh[$i]), $posit, PREG_SET_ORDER);
                $diam = IdByName($posit[0][4], "tab6", "tb6_id", "tb6_nm");
                $profw = IdByName($posit[0][1], "profw", "id", "name");
                $profh = IdByName($posit[0][3], "profh", "id", "name");
                $leftStr .= '<li><a href="/param/shini/?paramsmb=1&prfw=' . $profw . '&prfh=' . $profh . '&diam=' . $diam . '&seas=0" style="text-decoration: underline;" target="_blank">' . $sh[$i] . '</a></li>';
            }
        }

        if ($leftStr != '') {

            $content .= '<div class="large-6 columns"><h3 class="podbor">Шины - варианты тюнинга</h3>';
            if ($rightStr != '') {

                $content .= '<div class="row"><div class="small-6 columns"><ul>' . $leftStr . '</ul></div><div class="small-6 columns"><ul>' . $rightStr . '</ul></div></div>';
            } else {

                $content .= '<ul>' . $leftStr . '</ul>';
            }
            $content .= '</div>';
        }
        $content .= '</div>';
        */
    }
}
?>