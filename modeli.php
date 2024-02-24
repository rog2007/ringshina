<?php

function PagesCreate($link, $all_cn, $crpg, $autobr) {

    $strtmp = '<div class="pagination-centered"><ul class="pagination">';
    if ($all_cn < 8) {

        for ($i = 1; $i <= $all_cn; $i++) {

            $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . ($autobr ? '/' . $autobr : '') . '.html">' . $i . '</a></li>';
        }
    }
    if ($all_cn >= 8) {

        if ($crpg < 4) {

            for ($i = 1; $i <= 5; $i++) {

                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . ($autobr ? '/' . $autobr : '') . '.html">' . $i . '</a></li>';
            }
            $strtmp .= '<li class="unavailable"><a href="">&hellip;</a></li><li><a href="' . $link . $all_cn . ($autobr ? '/' . $autobr : '') . '.html">' . $all_cn . '</a></li>';
        }

        if ($crpg == 4) {

            for ($i = 1; $i <= 6; $i++) {

                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . ($autobr ? '/' . $autobr : '') . '.html">' . $i . '</a></li>';
            }
            $strtmp .= '<li class="unavailable"><a href="">&hellip;</a></li><li><a href="' . $link . $all_cn . ($autobr ? '/' . $autobr : '') . '.html">' . $all_cn . '</a></li>';
        }

        if ($crpg > $all_cn - 3) {

            $strtmp .= '<li><a href="' . $link . '1' . ($autobr ? '/' . $autobr : '') . '.html">1</a></li><li class="unavailable"><a href="">&hellip;</a></li>';

            for ($i = $all_cn - 4; $i <= $all_cn; $i++) {

                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . ($autobr ? '/' . $autobr : '') . '.html">' . $i . '</a></li>';
            }
        }

        if ($crpg == $all_cn - 3) {

            $strtmp .= '<li><a href="' . $link . '1' . ($autobr ? '/' . $autobr : '') . '.html">1</a></li><li class="unavailable"><a href="">&hellip;</a></li>';

            for ($i = $all_cn - 5; $i <= $all_cn; $i++) {

                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . ($autobr ? '/' . $autobr : '') . '.html">' . $i . '</a></li>';
            }
        }

        if ($crpg < $all_cn - 3 && $crpg > 4) {

            $strtmp .= '<li><a href="' . $link . '1' . ($autobr ? '/' . $autobr : '') . '.html">1</a></li><li class="unavailable"><a href="">&hellip;</a></li>';

            for ($i = $crpg - 2; $i <= $crpg + 2; $i++) {

                $strtmp .= '<li' . ($i == $crpg ? ' class="current"' : '') . '><a href="' . $link . $i . ($autobr ? '/' . $autobr : '') . '.html">' . $i . '</a></li>';
            }
            $strtmp .= '<li class="unavailable"><a href="">&hellip;</a></li><li><a href="' . $link . $all_cn . ($autobr ? '/' . $autobr : '') . '.html">' . $all_cn . '</a></li>';
        }
    }
    $strtmp .= '</ul></div>';

    return $strtmp;
}

$tov = IdByName($arg[0], "tab1", "tb1_id", "translit");

$a_n = "";
$auto = 0;
$curpg = (isset($arg[2]) && $arg[2] > 0 ? $arg[2] : 1);

switch ($tov) {

    case 1:
        $frm_id = IdByName($arg[1], "tab3", "tb3_id", "url");
        $frm_nm = IdByName($frm_id, "tab3", "tb3_nm", "tb3_id");
        $frm_dsc = IdByName($frm_id, "tab3", "tb3_dsc", "tb3_id");
        $pic = "tyre/";
        $brTop = "Каталог шин";
        $num_all = ceil(filter_cnt($tov, $frm_id, $auto) / 30);
        $res = filter($tov, $frm_id, $curpg, 30, $auto);
        $title = "RingShina: каталог шин";
        $descr = "RingShina: каталог шин";
        $keywords = "RingShina: каталог шин";
        $h1 = "RingShina: каталог шин";
        $sql = "select tb3_id,tb3_nm,tb3_pic, url from tab3 where tb3_tov_id=" . $tov . " and wrk3=1 order by tb3_nm";
        $tovName = "шины";
        break;
    case 2:
        $frm_id = IdByName($arg[1], "tab3", "tb3_id", "url");
        $frm_nm = IdByName($frm_id, "tab3", "tb3_nm", "tb3_id");
        $frm_dsc = IdByName($frm_id, "tab3", "tb3_dsc", "tb3_id");
        $pic = "diski/";
        $brTop = "Каталог дисков";
        $a_n = (isset($arg[3]) ? urldecode($arg[3]) : 0);
        $auto = IdByName($a_n, "t_auto", "t_auto_id", "t_auto_nm");
        $num_all = ceil(filter_cnt($tov, $frm_id, $auto) / 30);
        $res = filter($tov, $frm_id, $curpg, 30, $auto);
        $title = "RingShina: каталог дисков";
        $descr = "RingShina: каталог дисков";
        $keywords = "RingShina: каталог дисков";
        $h1 = "RingShina: каталог дисков";
        $sql = "select tb3_id,tb3_nm,tb3_pic, url from tab3 where tb3_tov_id=" . $tov . " and wrk3=1 order by tb3_nm";
        $tovName = "диски";
        break;
    case 3:
        //$frm_id = IdByName($arg[1], "akb_brand", "id","url");
        //$frm_nm = IdByName($frm_id, "akb_brand", "name", "id");
        //$frm_dsc = IdByName($frm_id, "akb_brand", "dsc", "id");
        $pic = "akb/";
        $num_all = ceil(filterCntAKB(/*$frm_id*/) / 30);
        $res = filterAKB(/*$frm_id, */
            $curpg, 30);
        $brTop = "Каталог АКБ";
        $title = "RingShina: каталог АКБ";
        $descr = "RingShina: каталог АКБ";
        $keywords = "RingShina: каталог АКБ";
        $h1 = "RingShina: каталог АКБ";
        $sql = "select id as tb3_id, name as tb3_nm, pic as tb3_pic, url from akb_brand where vis = 1 order by name";
        $tovName = "АКБ";
        break;
}

if ($tov != 3) {

    $content .= '<div class="row"><ul class="breadcrumbs">
  <li><a href="/">Главная</a></li>
  <li><a href="/catalog/' . $arg[0] . '.html">' . $brTop . '</a></li>
  <li class="current"><a href="/modeli/' . $arg[0] . '/' . $arg[1] . '/1.html">' . $frm_nm . '</a></li>
	</ul>';
} else {

    $content .= '<div class="row"><ul class="breadcrumbs">
  <li><a href="/">Главная</a></li>
  <li class="current"><a href="/modeli/' . $arg[0] . '/1.html">Каталог АКБ</a></li>
	</ul>';
}


$strZak = "";
$lnkk = "/modeli/" . $arg[0] . "/" . $arg[1] . "/";
if ($num_all > 1) {
    $strZak = PagesCreate($lnkk, $num_all, $curpg, urlencode($a_n));
}

$content .= '<div class="large-10 large-centered columns">' . $strZak;

/*if($frm_dsc){
  $content .= '<div class="brand-dsc">' . $frm_dsc . '</div>';
} */

$i = 0;
while ($model = mysql_fetch_object($res)) {

    $pic = $imgLinkPrefix . '/images/tovar/nofoto160.jpg';
    if ($tov == 2) {

        $discImgs = getDiskModelImages($model->tab4_id, 1);

        if ($discImgs->execute() && $discImgs->rowCount() > 0) {

            if ($imgObj = $discImgs->fetch(PDO::FETCH_OBJ)) {

                $pic = ImageWork($imgObj->imgname, $tov, $imgObj->idcolor, $model->tab3_id, $model->tab4_id,
                    $imgObj->t2tr, $model->tb3_nm, $model->T4Nm, '160');

                if (strpos($pic, 'nofoto')) {

                    $onclick = '';
                    $style = ';cursor:pointer';

                } else {

                    $onclick = 'alt="' . $model->tab4_id . '" onclick="return ShowZoomWindow(true,\'' . addslashes($model->tb3_nm) . ' ' .
                        addslashes($model->T4Nm) . ($imgObj->tb2_nm ? ' ' . addslashes($imgObj->tb2_nm) : '') . '\',\'/images/tovar/' . ($tov == 1 ? "tyres" : "discs") . '/' . $imgObj->imgname . '\');"';
                    $style = '';
                }
            }
        }
    } else {

        $pic = ImageWork($model->T4Pic, $tov, $model->tab2_id, $model->tab3_id, $model->tab4_id, $model->t2nm,
            $model->tb3_nm, $model->T4Nm, "160");
        if (strpos($pic, 'nofoto')) {

            $onclick = '';
            $style = ';cursor:pointer';

        } else {

            $onclick = 'onclick="return ShowZoomWindow(true,\'' . addslashes($model->tb3_nm) . ' ' .
                addslashes($model->T4Nm) . '\',\'/images/tovar/' . ($tov == 1 ? "tyres" : "discs") . '/' . $model->T4Pic . '\');"';
            $style = '';
        }

    }

    if ($tov != 3) {

        $link = '/razm/' . $arg[0] . '/' . $model->t3url . '/' . $model->t4url . '.html';
    } else {

        $link = '/razm/' . $arg[0] . '/' . $model->t4url . '.html';
    }
    $content .= '<div class="large-4 small-6 columns">
		<div class="ikonki">' .
        ($model->t4ses == 3 ? '<img src="/img/soln.png" alt="">' : ($model->t4ses == 5 ? '<img src="/img/sneg.png" alt="">' : '')) .
        ($model->t4sh == 3 ? '<img src="/img/ship.png" alt="">' : '') .
        '</div>
              <img src="' . $pic . '">
              <div class="panel">
                <a href="' . $link . '" class="data">' . ($tov != 3 ? $model->tb3_nm . ' ' : '') . $model->T4Nm . ($model->auto_brand ? ' (' . $model->t_auto_nm . ')' : '') . '</a>
              </div>
            </div>';
}
$content .= ($strZak ? $strZak : '');
$content .= '</div></div>';