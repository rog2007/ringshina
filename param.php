<?php

$tov = IdByName($arg[0], "tab1", "tb1_id", "translit");

function PagesCreate($link, $all_cn, $crpg, $order, $orderType)
{
    $strtmp = '<div class="pagination-centered"><ul class="pagination">';
    if ($all_cn < 8) {
        for ($i = 1; $i <= $all_cn; $i++) {
            $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . '-' . $order .
                '-' . $orderType . '.html">' . $i . '</a></li>';
        }
    }
    if ($all_cn >= 8) {
        if ($crpg < 4) {
            for ($i = 1; $i <= 5; $i++) {
                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . '-' . $order .
                    '-' . $orderType . '.html">' . $i . '</a></li>';
            }
            $strtmp .= '<li class="unavailable"><a href="">&hellip;</a></li><li><a href="' . $link . $all_cn . '-' .
                $order . '-' . $orderType . '.html">' . $all_cn . '</a></li>';
        }
        if ($crpg == 4) {
            for ($i = 1; $i <= 6; $i++) {
                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . '-' . $order .
                    '-' . $orderType . '.html">' . $i . '</a></li>';
            }
            $strtmp .= '<li class="unavailable"><a href="">&hellip;</a></li><li><a href="' . $link . $all_cn . '-' .
                $order . '-' . $orderType . '.html">' . $all_cn . '</a></li>';
        }
        if ($crpg > $all_cn - 3) {
            $strtmp .= '<li><a href="' . $link . '1_' . $sort . '.html">1</a></li><li class="unavailable">' .
                '<a href="">&hellip;</a></li>';
            for ($i = $all_cn - 4; $i <= $all_cn; $i++) {
                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . '-' . $order .
                    '-' . $orderType . '.html">' . $i . '</a></li>';
            }
        }
        if ($crpg == $all_cn - 3) {
            $strtmp .= '<li><a href="' . $link . '1.html">1</a></li><li class="unavailable"><a href="">&hellip;</a></li>';
            for ($i = $all_cn - 5; $i <= $all_cn; $i++) {
                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . '-' . $order .
                    '-' . $orderType . '.html">' . $i . '</a></li>';
            }
        }
        if ($crpg < $all_cn - 3 && $crpg > 4) {
            $strtmp .= '<li><a href="' . $link . '1-' . $order . '-' . $orderType . '.html">1</a></li>' .
                '<li class="unavailable"><a href="">&hellip;</a></li>';
            for ($i = $crpg - 2; $i <= $crpg + 2; $i++) {
                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . '-' . $order .
                    '-' . $orderType . '.html">' . $i . '</a></li>';
            }
            $strtmp .= '<li class="unavailable"><a href="">&hellip;</a></li><li><a href="' . $link . $all_cn . '-' .
                $order . '-' . $orderType . '.html">' . $all_cn . '</a></li>';
        }
    }
    $strtmp .= '</ul></div>';
    return $strtmp;
}

$pageArray = explode('-', (isset($_GET['page']) ? $_GET['page'] : 1));
$page = $pageArray[0];
$order = isset($pageArray[1]) ? $pageArray[1] : 'all_name';
$orderType = isset($pageArray[2]) ? $pageArray[2] : 'ASC';

if ($tov == 1) {
    $viewFlag = 1;
    $tyreDiamId = (isset($_GET['diam']) ? $_GET['diam'] : 0);
    if ($tyreDiamId) {
        $tyreDiamName = IdByName($tyreDiamId, "tab6", "tb6_nm", "tb6_id");
        $viewFlag = 2;
    }
    $tyreProfWId = (isset($_GET['prfw']) ? $_GET['prfw'] : 0);
    if ($tyreProfWId) {
        $tyreProfWName = IdByName($tyreProfWId, "profw", "name", "id");
        $viewFlag = 2;
    }
    $tyreProfHId = (isset($_GET['prfh']) ? $_GET['prfh'] : 0);
    if ($tyreProfHId) {
        $tyreProfHName = IdByName($tyreProfHId, "profh", "name", "id");
        $viewFlag = 2;
    }
    $tyreSeasId = (isset($_GET['seas']) ? $_GET['seas'] : 0);
    if ($tyreSeasId) {
        $tyreSeasName = IdByName($tyreSeasId, "tab10", "tb10_nm", "tb10_id");
        if ($tyreSeasId == 50) {
            $tyreSeasName = "Зима не шипованные";
        }
        if ($tyreSeasId == 53) {
            $tyreSeasName = "Зима шипованные";
        }
    }
    $tyrePriceFrom = (isset($_GET['tyre_price_from']) ? (int)$_GET['tyre_price_from'] : 0);
    $tyrePriceTo = (isset($_GET['tyre_price_to']) ? (int)$_GET['tyre_price_to'] : 0);
    if ($tyrePriceFrom || $tyrePriceTo) {
        $viewFlag = 2;
    }
    if (isset($_GET['brand'])) {
        if (is_string($_GET['brand']) && $_GET['brand'] != '0') {
            $tyreBrandsTmp = explode(' ', $_GET['brand']);
            foreach ($tyreBrandsTmp as $key) {
                array_push($tyreBrands, $key);
            }
        } else {
            if (is_array($_GET['brand'])) {
                foreach ($_GET['brand'] as $key => $value) {
                    if ($value == "on") {
                        array_push($tyreBrands, $key);
                    }
                }
            }
        }
    }
    if ($viewFlag == 1) {
        $num_full = ModelsCntTyre($tyreBrands, $tyreSeasId, 0);
        $res = ModelsTyres($tyreBrands, $tyreSeasId, 0, $page, 14);
    }
    if ($viewFlag == 2) {

        $num_full = NomenCntTyre($tyreBrands, $tyreProfWId, $tyreProfHId, $tyreDiamId, $tyreSeasId, $tyrePriceFrom, $tyrePriceTo, 0);
        $res = NomenTyres($tyreBrands, $tyreProfWId, $tyreProfHId, $tyreDiamId, $tyreSeasId, $tyrePriceFrom,
            $tyrePriceTo, 0, $page, 14, $order, $orderType);
    }

    $lnkk = '/param/' . $arg[0] . '/' . (!empty($tyreBrands) ? implode('+', $tyreBrands) : 0) . "/" . $tyreProfWId . "/" . $tyreProfHId . "/" . $tyreDiamId . "/" .
        $tyreSeasId . '/0/' . $tyrePriceFrom . '/' . $tyrePriceTo . '/';
}

if ($tov == 2) {

    $viewFlag = 1;

    $discWidthId = (isset($_GET['widthd']) ? $_GET['widthd'] : 0);
    if ($discWidthId) {

        $discWidthName = IdByName($discWidthId, "tab5", "tb5_nm", "tb5_id");
        $viewFlag = 2;
    }

    $discDiamId = (isset($_GET['diamd']) ? $_GET['diamd'] : 0);
    if ($discDiamId) {

        $discDiamName = IdByName($discDiamId, "tab6", "tb6_nm", "tb6_id");
        $viewFlag = 2;
    }

    $discPcdId = (isset($_GET['pcd']) ? $_GET['pcd'] : 0);
    if ($discPcdId) {

        $discPcdName = IdByName($discPcdId, "tab_pcd", "pcd_name", "id");
        $discOtvId = IdByName($discPcdId, "tab_pcd", "t7_id", "id");
        $discDCKOId = IdByName($discPcdId, "tab_pcd", "t8_id", "id");
        $viewFlag = 2;
    }

    if (isset($_GET['page'])) {

        $discViletTmp = explode(' ', $_GET['vil_tmp']);
        $discViletId = ($discViletTmp[0] ? (int)$discViletTmp[0] : 0);
        $discViletIdE = ($discViletTmp[1] ? (int)$discViletTmp[1] : 0);
    } else {

        $discViletId = (isset($_GET['vilb']) ? $_GET['vilb'] : 0);
        $discViletIdE = (isset($_GET['vile']) ? $_GET['vile'] : 0);
    }

    if ($discViletId) {

        $discViletName = IdByName($discViletId, "tab9", "tb9_nm", "tb9_id");
    }

    if ($discViletIdE) {

        $discViletNameE = IdByName($discViletIdE, "tab9", "tb9_nm", "tb9_id");
    }

    if ($discViletId || $discViletIdE) {
        $viewFlag = 2;
    }

    $discStupId = (isset($_GET['stup']) ? $_GET['stup'] : 0);
    if ($discStupId) {

        $discStupName = IdByName($discStupId, "tab12", "tb12_nm", "tb12_id");
        $viewFlag = 2;
    }

    if (isset($_GET['page'])) {

        $diskPriceTmp = explode(' ', $_GET['disc_price_from']);
        $discPriceFrom = ($diskPriceTmp[0] ? (int)$diskPriceTmp[0] : 0);
        $discPriceTo = ($diskPriceTmp[1] ? (int)$diskPriceTmp[1] : 0);
    } else {

        $discPriceFrom = (isset($_GET['disc_price_from']) ? (int)$_GET['disc_price_from'] : 0);
        $discPriceTo = (isset($_GET['disc_price_to']) ? (int)$_GET['disc_price_to'] : 0);
    }
    if ($discPriceFrom || $discPriceTo) {
        $viewFlag = 2;
    }

    if (isset($_GET['brandd'])) {

        if (is_string($_GET['brandd'])) {
            if ($_GET['brandd'] != '0') {
                $discBrandsTmp = explode(' ', $_GET['brandd']);
                foreach ($discBrandsTmp as $key) {
                    array_push($discBrands, $key);
                }
            }
        } else {

            foreach ($_GET['brandd'] as $key => $value) {

                if ($value == "on") {
                    if ($key == 50) {

                        array_push($discBrands, 297);
                    }
                    array_push($discBrands, $key);
                }
            }
        }
    }

    if ($viewFlag == 1) {
        $num_full = ModelsCntDisc($discBrands);
        $res = ModelsDiscs($discBrands, $page, 14);
    }
    if ($viewFlag == 2) {
        $num_full = NomenCntDisc($discBrands, $discWidthId, $discDiamId, $discOtvId, $discDCKOId, $discViletName,
            $discViletNameE, $discStupId, $discPriceFrom, $discPriceTo);
        $res = NomenDiscs($discBrands, $discWidthId, $discDiamId, $discOtvId, $discDCKOId, $discViletName,
            $discViletNameE, $discStupId, $discPriceFrom, $discPriceTo, $page, 14, $order, $orderType);
    }

    $lnkk = '/param/' . $arg[0] . '/' . (!empty($discBrands) ? implode('+', $discBrands) : 0) . "/" . $discWidthId . "/" . $discDiamId . "/" . $discPcdId . "/" .
        $discViletId . '+' . $discViletIdE . "/" . $discStupId . '/' . $discPriceFrom . '+' . $discPriceTo . '/';
}

if ($tov == 3) {

    $volume = (isset($_GET['volume']) ? $_GET['volume'] : 0);
    if ($volume) {

        $volumeName = IdByName($volume, "akb_v", "name", "id");
    }
    $volumeFrom = (isset($_GET['volumeFrom']) ? $_GET['volumeFrom'] : 0);
    if ($volumeFrom) {

        $volumeFromName = IdByName($volumeFrom, "akb_v", "name", "id");
    }
    $volumeTo = (isset($_GET['volumeTo']) ? $_GET['volumeTo'] : 0);
    if ($volumeTo) {

        $volumeToName = IdByName($volumeTo, "akb_v", "name", "id");
    }
    $volt = (isset($_GET['volt']) ? $_GET['volt'] : 0);
    if ($volt) {

        $voltName = IdByName($volt, "akb_volt", "name", "id");
    }
    $klem = (isset($_GET['klem']) ? $_GET['klem'] : 0);
    if ($klem) {

        $klemName = IdByName($klem, "akb_klemy", "name", "id");
    }
    $rvrt = (isset($_GET['rvrt']) ? $_GET['rvrt'] : 0);
    if ($rvrt) {

        $rvrtName = IdByName($rvrt, "akb_rvrt", "name", "id");
    }

    if (isset($_GET['page'])) {

        $akbPriceTmp = explode(' ', $_GET['akb_price_from']);
        $akbPriceFrom = ($akbPriceTmp[0] ? (int)$akbPriceTmp[0] : 0);
        $akbPriceTo = ($akbPriceTmp[1] ? (int)$akbPriceTmp[1] : 0);
    } else {

        $akbPriceFrom = (isset($_GET['akb_price_from']) ? (int)$_GET['akb_price_from'] : 0);
        $akbPriceTo = (isset($_GET['akb_price_to']) ? (int)$_GET['akb_price_to'] : 0);
    }

    $num_full = NomenCntAkb($volume, ($volumeFrom ? $volumeFromName : ''), ($volumeTo ? $volumeToName : ''), $volt,
        $rvrt, $klem, $akbPriceFrom, $akbPriceTo);
    $res = NomenAkb($volume, ($volumeFrom ? $volumeFromName : ''), ($volumeTo ? $volumeToName : ''), $volt, $rvrt, $klem,
        $akbPriceFrom, $akbPriceTo, $page, 15, $order, $orderType);

    $lnkk = '/param/' . $arg[0] . "/" . $volume . "/" . $volumeFrom . "/" . $volumeTo . "/" . $volt . "/" .
        $rvrt . '/' . $klem . '/' . $akbPriceFrom . '+' . $akbPriceTo . '/';
    $viewFlag = 2;
}

$num_all = ceil($num_full / 14);

$content .= '<ul class="breadcrumbs">
  <li><a href="/">Главная</a></li>
  <li class="current"><a href="/param/' . $arg[0] . '/">Подбор ' . ($tov == 1 ? 'шин' : ($tov == 3 ? 'АКБ' : 'дисков')) .
    ' по параметрам</a></li>
	</ul>';

if (!isset($_GET['paramsmb'])) {

    $content .= '<p>Укажите параметры для подбора</p>';
} else {

    if ($num_full == 0) {

        $content .= '<p>К сожалению товара с данными параметрами в наличии нет</p>';
    }
    $strZak = "";
    if ($num_all > 1) {
        $strZak = PagesCreate($lnkk, $num_all, $page, $order, $orderType);
    }
    $i = 0;
    $content .= '<div class="row">';
    $content .= '<div class="large-10 large-centered columns sort-panel" style=""><span class="caption">Сортировка:</span>' .
        '<a href="' . $lnkk . '1-all_name.html"' . ($order == 'all_name' ? ' class="current"' : '') . '>Наименование</a>' .
        '<a href="' . $lnkk . '1-price-ASC.html"' . ($order == 'price' && $orderType == 'ASC' ? ' class="current"' : '') . '>По возрастанию цены</a>' .
        '<a href="' . $lnkk . '1-price-DESC.html"' . ($order == 'price' && $orderType == 'DESC' ? ' class="current"' : '') . '>По убыванию цены</a></div>';
    $content .= '<div class="large-10 large-centered columns">' . $strZak;
    while ($tvone = mysql_fetch_object($res)) {

        if ($tov == 2 && $viewFlag == 1) {

            $discImgs = getDiskModelImages($tvone->tab4_id, 1);

            if ($discImgs->execute() && $discImgs->rowCount() > 0) {

                if ($imgObj = $discImgs->fetch(PDO::FETCH_OBJ)) {

                    $pic = ImageWork($imgObj->imgname, $tov, $imgObj->idcolor, $tvone->tab3_id, $tvone->tab4_id,
                        $imgObj->t2tr, $tvone->T3Nm, $tvone->T4Nm, '160');
                    if (strpos($pic, 'nofoto')) {

                        $onclick = '';
                        $style = ';cursor:pointer';

                    } else {

                        $onclick = 'onclick="return ShowZoomWindow(true,\'' . addslashes($tvone->T3Nm) . ' ' .
                            addslashes($tvone->T4Nm) . ($imgObj->tb2_nm ? ' ' . addslashes($imgObj->tb2_nm) : '') .
                            '\',\'/images/tovar/' . ($tov == 1 ? "tyres" : "discs") . '/' . $imgObj->imgname . '\');"';
                        $style = '';
                    }
                }
            }
        } else {

            if ($viewFlag == 2) {

                if (trim($tvone->tovimg)) {

                    $pic1 = $tvone->tovimg;
                } else {

                    $pic1 = $tvone->T4Pic;
                }
            } else {

                $pic1 = $tvone->T4Pic;
            }
            $pic = ImageWork($pic1, $tov, $tvone->tab2_id, $tvone->tab3_id, $tvone->tab4_id, $tvone->t2tr, $tvone->T3Nm, $tvone->T4Nm, '160');
            if (strpos($pic, 'nofoto')) {

                $onclick = '';
                $style = ';cursor:pointer';

            } else {

                /*$onclick = 'onclick="return ShowZoomWindow(true,\'' . addslashes($tvone->T3Nm) . ' ' .
                  addslashes($tvone->T4Nm) . ($tov == 2 && $tvone->tab2_id ? ' ' . addslashes($tvone->tb2_nm) : '') . '\',\'/images/tovar/' . ($tov==1?"tyres":"discs") . '/' . $pic1 . '\');"';
                */
                $style = '';
            }
        }

        if ($viewFlag == 1) {

            $link = '/razm/' . $arg[0] . '/' . $tvone->t3url . '/' . $tvone->t4url . '.html';
        }

        if ($viewFlag == 2) {

            $link = '/card/' . $tvone->turl . '.html';
            if ($tov == 1)
                $razmer = $tvone->prof . ' ' . $tvone->tb6_nm . ' ' . $tvone->tb7_nm . $tvone->tb8_nm . ($tvone->rof ? ' run flat' : '');
            if ($tov == 2)
                $razmer = $tvone->tb5_nm . '*' . $tvone->tb6_nm . ' ' . $tvone->tb7_nm . '/' . $tvone->tb8_nm .
                    ' ET' . $tvone->tb9_nm . ($tvone->tb12_nm ? ' D' . $tvone->tb12_nm : '') . ($tvone->tb2_nm ? ' ' . $tvone->tb2_nm : '');
            if ($tov == 3)
                $razmer = $tvone->vname . 'Ач ' . $tvone->vlname . 'В ' . $tvone->rname;
        }

        $content .= '<div class="small-6 columns">' .
            ($tov == 1 ? '<div class="ikonki">' .
                ($tvone->t4ses == 3 ? '<img src="/img/soln.png" alt="">' : ($tvone->t4ses == 5 ? '<img src="/img/sneg.png" alt="">' : '')) .
                ($tvone->t4sh == 3 ? '<img src="/img/ship.png" alt="">' : '') .
                '</div>' : '') .
            '<img src="' . $pic . '">
              <div class="panel">
                <a href="' . $link . '" class="data">' . $tvone->T3Nm . ' ' . $tvone->T4Nm . ($tvone->auto_brand ? ' (' . $tvone->t_auto_nm . ')' : '') . ($viewFlag == 2 ? ' ' . $razmer : '') . '</a>
                <h6 class="subheader">' . $tvone->price . '</h6>
              </div>
            </div>';
    }

    $content .= $strZak . "</div></div>";
}
?>