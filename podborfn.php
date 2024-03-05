<?php
header("Content-type: text/plain; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
require("connect.php");
require ('funcSQL.php');
require ("callAPI.php");
require ("funcPodbor.php");

function prepareResult($res, $step=0) {
    $result = "";
    foreach ($res as $key => $value){
        if(!empty($result)){
            $result .= "$";
        }
        $result .= $value["slug"] . " | " . $value["name"];
        if($step == 3){
            if(count($value["trim_levels"])){
                $result .= " " . $value["trim_levels"][0];
            }
        }
    }
    return $result;
}


$step = filter_input(INPUT_POST, 'step', FILTER_VALIDATE_INT);
$vendor = filter_input(INPUT_POST, 'firm');
$car = filter_input(INPUT_POST, 'model');
$year = filter_input(INPUT_POST, 'year');
$url = "https://api.wheel-size.com/v2/";
$user_key = "user_key=467886d78550ce67d42cd4591173155a";

if ($step == 1) {
    $res = getData($vendor);
    //$res = callAPI($url . "models/?make=" . $vendor . "&" . $user_key);
    echo prepareResult($res);
}
if ($step == 2) {
    $res = getData($vendor, $car);
    //$res = callAPI($url . "years/?make=" . $vendor . "&model=" . $car . "&" . $user_key);
    echo prepareResult($res);
}
if ($step == 3) {
    $res = getData($vendor, $car, $year);
    //$res = callAPI($url . "modifications/?make=" . $vendor . "&model=" . $car . "&year=" . $year . "&" . $user_key);
    echo prepareResult($res, $step);
}
