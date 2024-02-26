<?php

$isDevelop = false;
$hostArr = explode('.', $_SERVER['HTTP_HOST']);
if ($hostArr[0] == 'www') {
    $domainIndex = 2;
} else {
    $domainIndex = 1;
}
if ($hostArr[$domainIndex] == 'dev' || $hostArr[$domainIndex] == 'loc') {
    $isDevelop = true;
}

$imgLinkPrefix = '';
if ($isDevelop) {

    $imgLinkPrefix = 'https://www.ringshina.ru';
    define('DB_DRIVER', 'mysql');
    define('DB_HOST', 'mysql');
    define('DB_NAME', 'ringshina');
    define('DB_USER', 'root');
    define('DB_PASS', 'secret');
    define('DB_PORT', '3306');
} else {

    define('DB_DRIVER', 'mysql');
    define('DB_HOST', 'mysql48.1gb.ru');
    define('DB_NAME', 'gb_newdb_ring');
    define('DB_USER', 'gb_newdb_ring');
    define('DB_PASS', '17a941ae1xvn');
    define('DB_PORT', '3306');
}

try {
    $connect_str = DB_DRIVER . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME;
    $dbcon = new PDO($connect_str, DB_USER, DB_PASS);
    $dbcon->exec("set names utf8");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
//mysqli_connect(DB_HOST, DB_USER, DB_PASS);
//mysqli_select_db(DB_NAME) or die("Unable to select database");
//
//mysqli_query("set character_set_client='utf8'");
//mysqli_query("set character_set_results='utf8'");
//mysqli_query("set collation_connection='utf8_general_ci'");

$mysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
mysqli_select_db($mysqli, DB_NAME) or die("Unable to select database");

mysqli_query($mysqli, "set character_set_client='utf8'");
mysqli_query($mysqli, "set character_set_results='utf8'");
mysqli_query($mysqli, "set collation_connection='utf8_general_ci'");