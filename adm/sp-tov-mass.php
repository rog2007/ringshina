<?php
$print = filter_input(INPUT_POST, 'print');
$tov = filter_input(INPUT_POST, 'tov');
if ($print !== null && $tov !== null) {
    $idArray = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'dtov-')) {
            array_push($idArray, str_ireplace('idtov-', '', $key));
        }
    }
    if ($tov == 1) {
        $res = nomenTyresNew($idArray);
    }
    if ($tov == 2) {
        $res = nomenDiscsNew($idArray);
    }
    if ($tov == 3) {
        $res = nomenAkbNew($idArray);
    }
    $content = '<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>Администрирование RingShina</title>
        <link rel="stylesheet" href="/css/normalize.css">
        <link rel="stylesheet" href="/css/foundation.min.css">
        <link rel="stylesheet" href="/css/style-v2.css">
        <link rel="stylesheet" href="/css/style-add-v2.css">        
        <meta charset="utf-8" />        
    </head>
    <body><h2 style="text-align:center;padding:10px">Интернет магазин шин и дисков www.ringshina.ru. Телефон: (3812) 51-39-41, (3812) 38-76-90</h2>';
    $content .= '<div class="row"><div class="large-10 large-centered columns">';
    while ($tvone = mysql_fetch_object($res)) {
        $pic = '/images/tovar/nofoto160.jpg';
        if ($tov == 2) {
            $discImgs = getDiskModelImages_new($tvone->tab4_id, $tvone->tab2_id, 1);
            if ($discImgs->execute() && $discImgs->rowCount() > 0) {
                if ($imgObj = $discImgs->fetch(PDO::FETCH_OBJ)) {
                    $pic = ImageWork_new($imgObj->imgname, $tov, $imgObj->idcolor, $tvone->tab3_id, $tvone->tab4_id,
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
            $pic1 = $tvone->T4Pic;
            $pic = ImageWork_new($pic1, $tov, 0, $tvone->tab3_id, $tvone->tab4_id, $tvone->t2tr, $tvone->T3Nm, $tvone->T4Nm, '160');
            if (strpos($pic, 'nofoto')) {

                $onclick = '';
                $style = ';cursor:pointer';
            } else {
                $style = '';
            }
        }

        $link = '/card/' . $tvone->turl . '.html';
        if ($tov == 1)
            $razmer = $tvone->prof . ' ' . $tvone->tb6_nm . ' ' . $tvone->tb7_nm . $tvone->tb8_nm . ($tvone->rof ? ' run flat' : '');
        if ($tov == 2)
            $razmer = $tvone->tb5_nm . '*' . $tvone->tb6_nm . ' ' . $tvone->tb7_nm . '/' . $tvone->tb8_nm .
                    ' ET' . $tvone->tb9_nm . ($tvone->tb12_nm ? ' D' . $tvone->tb12_nm : '') . ($tvone->tb2_nm ? ' ' . $tvone->tb2_nm : '');
        if ($tov == 3)
            $razmer = $tvone->vname . 'Ач ' . $tvone->vlname . 'В ' . $tvone->rname;

        $content.='<div class="small-6 columns" style="width:50%">' .
                ($tov == 1 ? '<div class="ikonki">' .
                        ($tvone->t4ses == 3 ? '<img src="/img/soln.png" alt="">' : ($tvone->t4ses == 5 ? '<img src="/img/sneg.png" alt="">' : '')) .
                        ($tvone->t4sh == 3 ? '<img src="/img/ship.png" alt="">' : '') .
                        '</div>' : '') .
                '<img src="' . $pic . '">
              <div class="panel">
                <span class="data">' . $tvone->T3Nm . ' ' . $tvone->T4Nm .
                ($tvone->auto_brand ? ' (' . $tvone->t_auto_nm . ') ' : ' ') . $razmer . '</span>
                <h6 class="subheader">' . $tvone->price . '</h6>
              </div>
            </div>';
    }

    $content .= '</div></div></body></html>';
    echo $content;
    exit;
}
$setArray = array();
$idArray = array();

foreach ($_POST as $key => $value) {
    if (isset($_POST['upd'])) {
        if (strpos($key, '_upd')) {
            array_push($setArray, str_ireplace('_upd', '', $key) . '=' . $value);
        }
    }    
    if (strpos($key, 'dtov-')) {
        array_push($idArray, str_ireplace('idtov-', '', $key));
    }
}

if (isset($_POST['upd'])) {
    if (!empty($setArray) && !empty($idArray)) {
        $sql = 'update total set ' . implode(',', $setArray) . ', url = \'\' where total_id in (' . implode(',', $idArray) . ')';
        mysql_query($sql);
        if ($_POST['tov'] == 1) {
            mysql_query("update total left join (SELECT total_id as tid,tb3_nm as t3nm, tb4_nm as t4nm,
                profw.name as t5nm,if(h_id<>0,concat('/',profh.name),'') as t5h, tb6_nm as t6_nm,
                ifnull(t7.tb7_nm,'') AS mn7, ifnull(t8.tb8_nm,'') AS mn8, IF(rof=1,concat(' ',ifnull(run_flat.var,'run flat')),'') as run
                FROM total LEFT JOIN tab3 ON tab3_id = tb3_id LEFT JOIN tab4 ON tab4_id = tb4_id LEFT JOIN profw ON w_id = profw.id
                LEFT JOIN profh ON h_id = profh.id LEFT JOIN tab6 ON tab6_id = tb6_id
                LEFT JOIN tab7 AS t7 ON tab7_id = t7.tb7_id LEFT JOIN tab8 AS t8 ON tab8_id = t8.tb8_id LEFT JOIN tab9 ON t4sh = tb9_id
                left join run_flat on run_flat.br=tab3_id WHERE total_id in (" . implode(',', $idArray) . ")) as tb1 on total_id=tb1.tid
                set all_name = concat(t3nm,' ',t4nm,om,' ',t5nm,t5h,' ',t6_nm,' ',mn7,mn8,run) where total_id in (" . implode(',', $idArray) . ")");
        }
        if ($_POST['tov'] == 2) {
            mysql_query("update total LEFT JOIN(SELECT total_id AS tid, ifnull( concat( ' ', tab2.translit ) , '' ) t2mn, tb3_nm AS t3nm, tb4_nm AS t4nm, tb5_nm AS t5nm, tb6_nm AS t6_nm, t7.tb7_nm AS mn7, t8.tb8_nm AS mn8, ifnull( concat( ' ET', tb9_nm ) , '' ) AS mn9, ifnull( concat( ' D', tb12_nm ) , '' ) AS mn12
                FROM total LEFT JOIN tab2 ON tab2_id = tb2_id LEFT JOIN tab3 ON tab3_id = tb3_id LEFT JOIN tab4 ON tab4_id = tb4_id LEFT JOIN tab5 ON tab5_id = tb5_id
                LEFT JOIN tab6 ON tab6_id = tb6_id LEFT JOIN tab7 AS t7 ON tab7_id = t7.tb7_id LEFT JOIN tab8 AS t8 ON tab8_id = t8.tb8_id LEFT JOIN tab9 ON tab9_id = tb9_id
                LEFT JOIN tab12 ON tab12_id = tb12_id WHERE total_id in (" . implode(',', $idArray) . ")) AS tb1 ON total_id = tb1.tid set all_name=concat( t3nm, ' ', t4nm, ' ', t5nm, 'x', t6_nm, ' ', mn7, '/', mn8, mn9, mn12, t2mn )
                where total_id in (" . implode(',', $idArray) . ")");
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    exit;
}
if (isset($_POST['delete_selected'])) {    
    if ($_POST['tov'] == 3) {
        $delTotal = $dbcon->prepare('DELETE FROM akb_tovar WHERE id IN (' . implode(',', $idArray) . ')');
        $delTotal->execute();
        $delTotal = $dbcon->prepare('DELETE FROM akb_suppl WHERE id_tov IN (' . implode(',', $idArray) . ')');
        $delTotal->execute();
    } else {
        $delTotal = $dbcon->prepare('DELETE FROM total WHERE total_id IN (' . implode(',', $idArray) . ')');
        $delTotal->execute();
        $delTotal = $dbcon->prepare('DELETE FROM total_suppl WHERE id_tov IN (' . implode(',', $idArray) . ')');
        $delTotal->execute();
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}