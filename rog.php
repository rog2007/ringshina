<?php
require ("connect.php");
require ("callAPI.php");

$ost_zapros = 5 ;

function fill_data($vend = 0, $model = 0, $year = 0, $modif = 0){
    global $dbcon, $ost_zapros;
    echo $ost_zapros . "<br>";
    echo $vend . " " . $model . " " . $year . " " . $modif . '<br>';
    if($ost_zapros <= 0){
        exit(0);
    }
    if(!$model){
        $completed_vend = $dbcon->query("SELECT completed FROM vendors WHERE slug = '" . $vend . "'")->fetchAll(PDO::FETCH_ASSOC);
        $vendId = intval($dbcon->query("SELECT id FROM vendors WHERE slug = '" . $vend . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        if(sizeof($completed_vend) != 1){
            echo $vend . "problems\n";
            return;
        }
        if($completed_vend[0]["completed"] == "0"){
            sleep(rand(5, 10));
            echo "https://api.wheel-size.com/v2/models/?make=" . $vend . "&user_key=234e43a68352dd45dbb071917470f46f<br>";
            $res = callAPI("https://api.wheel-size.com/v2/models/?make=" . $vend . "&user_key=234e43a68352dd45dbb071917470f46f");
            if(!$res){
                var_dump($res);
                echo "check internet and API<br>";
                exit(0);
            }
            $ost_zapros--;
            for($i = 0;$i < sizeof($res);$i++){
                $query = $dbcon->prepare("INSERT INTO models (parentId, name, slug, data, completed) VALUES (:parentId, :name, :slug, :data, 0)");
                $query->bindParam(":parentId", $vendId);
                $query->bindParam(":name", $res[$i]["name"]);
                $query->bindParam(":slug", $res[$i]["slug"]);
                $encoded = json_encode($res[$i]);
                $query->bindParam(":data", $encoded);
                $query->execute();
            }
            $dbcon->query("UPDATE vendors SET completed = 1 WHERE slug = '" . $vend . "'");
        }
        $res = $dbcon->query("SELECT slug FROM models WHERE parentId = '" . $vendId . "'")->fetchAll(PDO::FETCH_ASSOC);
        for($i = 0;$i < sizeof($res);$i++){
            fill_data($vend, $res[$i]["slug"]);
        }
    }else if(!$year){
        $vendId = intval($dbcon->query("SELECT id FROM vendors WHERE slug = '" . $vend . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $modelId = intval($dbcon->query("SELECT id FROM models WHERE slug = '" . $model . "' AND parentId = '" . $vendId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $completed_model = $dbcon->query("SELECT completed FROM models WHERE slug = '" . $model . "' AND parentId='" . $vendId . "'")->fetch(PDO::FETCH_ASSOC);
        if($completed_model["completed"] == "0"){
            sleep(rand(5, 10));
            echo "https://api.wheel-size.com/v2/years/?make=" . $vend . "&model=" . $model . "&user_key=234e43a68352dd45dbb071917470f46f";
            $res = callAPI("https://api.wheel-size.com/v2/years/?make=" . $vend . "&model=" . $model . "&user_key=234e43a68352dd45dbb071917470f46f");
            if(!$res){
                var_dump($res);
                echo "check internet and API<br>";
                exit(0);
            }
            $ost_zapros--;
            for($i = 0;$i < sizeof($res);$i++){
                $query = $dbcon->prepare("INSERT INTO years (parentId, name, slug, data, completed) VALUES (:parentId, :name, :slug, :data, 0)");
                $query->bindParam(":parentId", $modelId);
                $query->bindParam(":name", $res[$i]["name"]);
                $query->bindParam(":slug", $res[$i]["slug"]);
                $encoded = json_encode($res[$i]);
                $query->bindParam(":data", $encoded);
                $query->execute();
            }
            $dbcon->query("UPDATE models SET completed = 1 WHERE slug = '" . $model . "' AND parentId=" . $vendId);
        }
        $res = $dbcon->query("SELECT slug FROM years WHERE parentId = '" . $modelId . "'")->fetchAll(PDO::FETCH_ASSOC);
        for($i = 0;$i < sizeof($res);$i++){
            if($res[$i]["slug"] >= 2015){
                fill_data($vend, $model, $res[$i]["slug"]);
            }
        }
    }else if(!$modif){
        $vendId = intval($dbcon->query("SELECT id FROM vendors WHERE slug = '" . $vend . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $modelId = intval($dbcon->query("SELECT id FROM models WHERE slug = '" . $model . "' AND parentId = '" . $vendId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $yearId = intval($dbcon->query("SELECT id FROM years WHERE slug = '" . $year . "' AND parentId = '" . $modelId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $completed_year = $dbcon->query("SELECT completed FROM years WHERE slug = '" . $year . "' AND parentId='" . $modelId . "'")->fetch(PDO::FETCH_ASSOC);
        if($completed_year["completed"] == "0"){
            sleep(rand(5, 10));
            echo "https://api.wheel-size.com/v2/modifications/?make=" . $vend . "&model=" . $model . "&year=" . $year . "&user_key=234e43a68352dd45dbb071917470f46f<br>";
            $res = callAPI("https://api.wheel-size.com/v2/modifications/?make=" . $vend . "&model=" . $model . "&year=" . $year . "&user_key=234e43a68352dd45dbb071917470f46f");
            if(!$res){
                var_dump($res);
                echo "check internet and API<br>";
                exit(0);
            }
            $ost_zapros--;
            for($i = 0;$i < sizeof($res);$i++){
                $query = $dbcon->prepare("INSERT INTO modifications (parentId, name, slug, data, completed) VALUES (:parentId, :name, :slug, :data, 0)");
                $query->bindParam(":parentId", $yearId);
                $query->bindParam(":name", $res[$i]["name"]);
                $query->bindParam(":slug", $res[$i]["slug"]);
                $encoded = json_encode($res[$i]);
                $query->bindParam(":data", $encoded);
                $query->execute();
            }
            $dbcon->query("UPDATE years SET completed = 1 WHERE slug = '" . $year . "' AND parentId = '" . $modelId . "'");
        }
        $res = $dbcon->query("SELECT slug FROM modifications WHERE parentId = '" . $yearId . "'")->fetchAll(PDO::FETCH_ASSOC);
        for($i = 0;$i < sizeof($res);$i++){
            fill_data($vend, $model, $year, $res[$i]["slug"]);
        }
    }else{
        $vendId = intval($dbcon->query("SELECT id FROM vendors WHERE slug = '" . $vend . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $modelId = intval($dbcon->query("SELECT id FROM models WHERE slug = '" . $model . "' AND parentId = '" . $vendId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $yearId = intval($dbcon->query("SELECT id FROM years WHERE slug = '" . $year . "' AND parentId = '" . $modelId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $modifId = intval($dbcon->query("SELECT id FROM modifications WHERE slug = '" . $modif . "' AND parentId = '" . $yearId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $completed_modif = $dbcon->query("SELECT completed FROM modifications WHERE slug = '" . $modif . "' AND parentId='" . $yearId . "'")->fetch(PDO::FETCH_ASSOC);
        if($completed_modif["completed"] == "0"){
            sleep(rand(5, 10));
            echo "https://api.wheel-size.com/v2/search/by_model/?make=" . $vend . "&model=" . $model . "&year=" . $year . "&modification=" . $modif . "&user_key=234e43a68352dd45dbb071917470f46f<br>";
            $res = callAPI("https://api.wheel-size.com/v2/search/by_model/?make=" . $vend . "&model=" . $model . "&year=" . $year . "&modification=" . $modif . "&user_key=234e43a68352dd45dbb071917470f46f");
            if(!$res){
                var_dump($res);
                echo "check internet and API<br>";
                exit(0);
            }
            $ost_zapros--;
            for($i = 0;$i < sizeof($res);$i++){
                $query = $dbcon->prepare("INSERT INTO wheelsInfo (parentId, name, slug, data) VALUES (:parentId, :name, :slug, :data)");
                $query->bindParam(":parentId", $modifId);
                $query->bindParam(":name", $res[$i]["name"]);
                $query->bindParam(":slug", $res[$i]["slug"]);
                $encoded = json_encode($res[$i]);
                $query->bindParam(":data", $encoded);
                $query->execute();
            }
            $dbcon->query("UPDATE modifications SET completed = 1 WHERE slug = '" . $modif . "' AND parentId='" . $yearId . "'");
        }
    }
}

// $res = $dbcon->query("SELECT name FROM vendors")->fetchAll(PDO::FETCH_ASSOC);
// var_dump($res);

$vend = explode("\n", file_get_contents("in.txt"));

for($i = 0;$i < sizeof($vend);$i++){
    $vend_slug = $dbcon->query("SELECT slug FROM vendors WHERE name = '" . $vend[$i] . "'")->fetchAll(PDO::FETCH_ASSOC);
    if(sizeof($vend_slug) != 1){
        echo $vend[$i] . "problems";
        continue;
    }
    fill_data($vend_slug[0]["slug"]);
}

?>