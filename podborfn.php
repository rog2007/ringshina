<?php
header("Content-type: text/plain; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
require("connect.php");
require ('funcSQL.php');

function prepareResult($res) {
    if (!$res['result']) {
        return 'ERROR [' . $res['error'][0] . ' - ' . $res['error'][1] . '] "' . $res['error'][2] . '"';
    }
    $str_pod = '';
    if (count($res['data']) > 0) {
        foreach ($res['data'] as $rs) {
            if (!empty($str_pod)) {
                $str_pod .= '$';
            }
            $str_pod .= $rs->nm . " | " . $rs->nm;
        }
    }
    return $str_pod;
}


$step = filter_input(INPUT_POST, 'step', FILTER_VALIDATE_INT);
$vendor = filter_input(INPUT_POST, 'firm');
$car = filter_input(INPUT_POST, 'model');
$year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);

if ($step == 1) {
    $res = query('SELECT car AS nm FROM podbor_shini_i_diski WHERE vendor=:vendor GROUP BY car ORDER BY car',
        [':vendor' => $vendor]);
    echo prepareResult($res);
}
if ($step == 2) {
    $res = query('SELECT `year` AS nm FROM podbor_shini_i_diski WHERE vendor = :vendor AND car = :car ' .
        'GROUP BY year ORDER BY year DESC', [':vendor' => $vendor, ':car' => $car]);
    echo prepareResult($res);
}
if ($step == 3) {
    $res = query('SELECT modification AS nm FROM podbor_shini_i_diski WHERE vendor = :vendor AND car = :car ' .
        'AND `year` = :year GROUP BY modification ORDER BY modification',
        [':vendor' => $vendor, ':car' => $car, ':year' => $year]);
    echo prepareResult($res);
}
