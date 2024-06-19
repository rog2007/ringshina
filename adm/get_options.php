<?php
header("Content-type: text/plain; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

require("connect.php");

$vend = filter_input(INPUT_GET, "vend", FILTER_VALIDATE_INT);
$model = filter_input(INPUT_GET, "model", FILTER_VALIDATE_INT);
if($vend){
    $models = $dbcon->query("SELECT name, id FROM models WHERE parentId=" . $vend)->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($models);
}else if($model){
    $years = $dbcon->query("SELECT id, name FROM years where parentId=" . $model)->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($years);
}
?>