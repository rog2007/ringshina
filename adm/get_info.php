<?php
header("Content-type: text/plain; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

require("connect.php");

function get_path($obj, $table){
    global $dbcon;
    $path = [];
    if($table == "wheelsInfo"){
        $obj = $dbcon->query("SELECT * FROM modifications WHERE id=" . $obj["parentId"])->fetch(PDO::FETCH_ASSOC);
        $path = get_path($obj, "modifications");
        $path[] = $obj;
        return $path;
    }else if($table == "modifications"){
        $obj = $dbcon->query("SELECT * FROM years WHERE id=" . $obj["parentId"])->fetch(PDO::FETCH_ASSOC);
        $path = get_path($obj, "years");
        $path[] = $obj;
        return $path;
    }else if($table == "years"){
        $obj = $dbcon->query("SELECT * FROM models WHERE id=" . $obj["parentId"])->fetch(PDO::FETCH_ASSOC);
        $path = get_path($obj, "models");
        $path[] = $obj;
        return $path;
    }else if($table == "models"){
        $obj = $dbcon->query("SELECT * FROM vendors WHERE id=" . $obj["parentId"])->fetch(PDO::FETCH_ASSOC);
        $path[] = $obj;
        return $path;
    }
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$obj = $dbcon->query("SELECT * FROM modifications WHERE id=" . $id)->fetch(PDO::FETCH_ASSOC);
$path = get_path($obj, "modifications");
$path[] = $obj;
$info = $dbcon->query("SELECT * FROM wheelsInfo WHERE parentId=" . $obj["id"])->fetch(PDO::FETCH_ASSOC);
$path[] = $info;
echo json_encode($path);


?>