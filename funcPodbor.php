<?php
function getRimParams($data){
    return [
        "diam" => IdByName($data["rim_diameter"], "tab6", "tb6_id", "tb6_nm"),
        "vilb" => IdByName($data["rim_offset"], "tab9", "tb9_id", "tb9_nm")
    ];
}

function getTireParams($data){
    return [
        "diam" => IdByName($data["tire_construction"] . $data["rim_diameter"], "tab6", "tb6_id", "tb6_nm"),
        "profw" => IdByName($data["tire_width"], "profw", "id", "name"),
        "profh" => IdByName($data["tire_aspect_ratio"], "profh", "id", "name")
    ];
}

function getRimContent($wheelList, $isStock, $pcd, $stup){
    $leftStr = "";
    $rightStr = "";
    $fl = 0;
    for ($i = 0; $i < sizeof($wheelList); $i++) {
        if($wheelList[$i]["is_stock"] != $isStock){
            continue;
        }
        if (!$wheelList[$i]["showing_fp_only"]) {

            $sh1 = [$wheelList[$i]["front"]["rim_width"] . " x " . $wheelList[$i]["front"]["rim_diameter"] . " ET" . $wheelList[$i]["front"]["rim_offset"],
                $wheelList[$i]["rear"]["rim_width"] . " x " . $wheelList[$i]["rear"]["rim_diameter"] . " ET" . $wheelList[$i]["rear"]["rim_offset"]];

            if (!$fl) {

                $leftStr .= '<li>Передняя ось</li>';
                $rightStr .= '<li>Задняя ось</li>';
                $fl++;
            }
            //preg_match_all("#(\d+([\.\,]\d)?)\s?x\s?(\d{1,2})\s?[EЕ][TТ]\s?((-)?\d{1,2}([\.\,]\d{1,2})?)#i", trim($sh1[0]), $posit1, PREG_SET_ORDER);
            $paramsId = getRimParams($wheelList[$i]["front"]);
//                $widthd = IdByName($posit1[0][1], "tab5", "tb5_id", "tb5_nm");
            $leftStr .= '<li><a href="/param/diski/?paramsmb=1&pcd=' . $pcd . '&diamd=' . $paramsId["diam"] . '&stup=' . $stup . '&type=0&vilb=' . $paramsId["vilb"] . '" style="text-decoration: underline;" target="_blank">' . $sh1[0] . '</a></li>';
            //preg_match_all("#(\d+([\.\,]\d)?)\s?x\s?(\d{1,2})\s?[EЕ][TТ]\s?((-)?\d{1,2}([\.\,]\d{1,2})?)#i", trim($sh1[1]), $posit1, PREG_SET_ORDER);
            $paramsId = getRimParams($wheelList[$i]["rear"]);

            $rightStr .= '<li><a href="/param/diski/?paramsmb=1&pcd=' . $pcd . '&diamd=' . $paramsId["diam"] . '&stup=' . $stup . '&type=0&vilb=' . $paramsId["vilb"] . '" style="text-decoration: underline;" target="_blank">' . $sh1[1] . '</a></li>';
        } else {

            //preg_match_all("#(\d+([\.\,]\d)?)\s?x\s?(\d{1,2})\s?[EЕ][TТ]\s?((-)?\d{1,2}([\.\,]\d{1,2})?)#i", trim($sh[$i]), $posit, PREG_SET_ORDER);
            $paramsId = getRimParams($wheelList[$i]["front"]);
            $sh = $wheelList[$i]["front"]["rim_width"] . " x " . $wheelList[$i]["front"]["rim_diameter"] . " ET" . $wheelList[$i]["front"]["rim_offset"];
            $leftStr .= '<li><a href="/param/diski/?paramsmb=1&pcd=' . $pcd . '&diamd=' . $paramsId["diam"] . '&stup=' . $stup . '&type=0&vilb=' . $paramsId["vilb"] . '" style="text-decoration: underline;" target="_blank">' . $sh . '</a></li>';
        }
    }
    return [
        "leftStr" => $leftStr,
        "rightStr" => $rightStr
    ];
}

function getTireContent($wheelList, $isStock){
    $leftStr = '';
    $rightStr = '';
    $fl = 0;
    for ($i = 0; $i < sizeof($wheelList); $i++) {
        if($wheelList[$i]["is_stock"] != $isStock){
            continue;
        }

        if (!$wheelList[$i]["showing_fp_only"]) {

            $sh1 = [$wheelList[$i]["front"]["tire_width"] . "/" . $wheelList[$i]["front"]["tire_aspect_ratio"] . " " . $wheelList[$i]["front"]["tire_construction"] . $wheelList[$i]["front"]["rim_diameter"],
                $wheelList[$i]["rear"]["tire_width"] . "/" . $wheelList[$i]["rear"]["tire_aspect_ratio"] . " " . $wheelList[$i]["rear"]["tire_construction"] . $wheelList[$i]["rear"]["rim_diameter"]];
            if (!$fl) {

                $leftStr .= '<li>Передняя ось</li>';
                $rightStr .= '<li>Задняя ось</li>';
                $fl++;
            }
            //preg_match_all("#(\d+)(\/(\d{1,2}))?\s(R\d{1,2})#i", trim($sh1[0]), $posit1, PREG_SET_ORDER);
            $paramsId = getTireParams($wheelList[$i]["front"]);
            $leftStr .= '<li><a href="/param/shini/?paramsmb=1&prfw=' . $paramsId["profw"] . '&prfh=' . $paramsId["profh"] . '&diam=' . $paramsId["diam"] . '&seas=0" style="text-decoration: underline;" target="_blank">' . $sh1[0] . '</a></li>';
            //preg_match_all("#(\d+)(\/(\d{1,2}))?\s(R\d{1,2})#i", trim($sh1[1]), $posit1, PREG_SET_ORDER);
            $paramsId = getTireParams($wheelList[$i]["rear"]);
            $rightStr .= '<li><a href="/param/shini/?paramsmb=1&prfw=' . $paramsId["profw"] . '&prfh=' . $paramsId["profh"] . '&diam=' . $paramsId["diam"] . '&seas=0" style="text-decoration: underline;" target="_blank">' . $sh1[1] . '</a></li>';
        } else {

            //preg_match_all("#(\d+)(\/(\d{1,2}))?\s(R\d{1,2})#i", trim($sh[$i]), $posit, PREG_SET_ORDER);
            $paramsId = getTireParams($wheelList[$i]["front"]);
            $sh = $wheelList[$i]["front"]["tire_width"] . "/" . $wheelList[$i]["front"]["tire_aspect_ratio"] . " " . $wheelList[$i]["front"]["tire_construction"] . $wheelList[$i]["front"]["rim_diameter"];
            $leftStr .= '<li><a href="/param/shini/?paramsmb=1&prfw=' . $paramsId["profw"] . '&prfh=' . $paramsId["profh"] . '&diam=' . $paramsId["diam"] . '&seas=0" style="text-decoration: underline;" target="_blank">' . $sh . '</a></li>';
        }
    }

    return [
        "leftStr" => $leftStr,
        "rightStr" => $rightStr
    ];
}

function getIdByName($name, $table, $columnName, $columnId){
    global $dbcon;
    $res = $dbcon->query("SELECT {$columnId} FROM {$table} WHERE {$columnName} = '{$name}'");
}

function getData($vend = null, $model = null, $year = null, $modif = null){
    global $dbcon;
    if($vend == null){
        $res = $dbcon->query("SELECT data FROM vendors")->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        for($i = 0;$i < sizeof($res);$i++){
            $mp = json_decode($res[$i]["data"], $associative=true);
            array_push($data, $mp);
        }
        return $data;
    }else if($model == null){
        $vendId = intval($dbcon->query("SELECT id FROM vendors WHERE slug = '" . $vend . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $isNextLevelCompleted = $dbcon->query("SELECT completed FROM vendors WHERE slug = '" . $vend . "'")->fetch(PDO::FETCH_ASSOC);
        if($isNextLevelCompleted["completed"] == 0){
            $res = callAPI("https://api.wheel-size.com/v2/models/?make=" . $vend . "&user_key=467886d78550ce67d42cd4591173155a");
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
        $res = $dbcon->query("SELECT data FROM models WHERE parentId = '" . $vendId . "'")->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        for($i = 0;$i < sizeof($res);$i++){
            $mp = json_decode($res[$i]["data"], $associative=true);
            array_push($data, $mp);
        }
        return $data;
    }else if($year == null){
        $vendId = intval($dbcon->query("SELECT id FROM vendors WHERE slug = '" . $vend . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $modelId = intval($dbcon->query("SELECT id FROM models WHERE slug = '" . $model . "' AND parentId = '" . $vendId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $isNextLevelCompleted = $dbcon->query("SELECT completed FROM models WHERE slug = '" . $model . "' AND parentId='" . $vendId . "'")->fetch(PDO::FETCH_ASSOC);
        if($isNextLevelCompleted["completed"] == 0){
            $res = callAPI("https://api.wheel-size.com/v2/years/?make=" . $vend . "&model=" . $model . "&user_key=467886d78550ce67d42cd4591173155a");
            for($i = 0;$i < sizeof($res);$i++){
                $query = $dbcon->prepare("INSERT INTO years (parentId, name, slug, data, completed) VALUES (:parentId, :name, :slug, :data, 0)");
                $query->bindParam(":parentId", $modelId);
                $query->bindParam(":name", $res[$i]["name"]);
                $query->bindParam(":slug", $res[$i]["slug"]);
                $encoded = json_encode($res[$i]);
                $query->bindParam(":data", $encoded);
                $query->execute();
            }
            $dbcon->query("UPDATE models SET completed = 1 WHERE slug = '" . $model . "'");
        }
        $res = $dbcon->query("SELECT data FROM years WHERE parentId = '" . $modelId . "'")->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        for($i = 0;$i < sizeof($res);$i++){
            $mp = json_decode($res[$i]["data"], $associative=true);
            array_push($data, $mp);
        }
        return $data;
    }else if($modif == null){
        $vendId = intval($dbcon->query("SELECT id FROM vendors WHERE slug = '" . $vend . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $modelId = intval($dbcon->query("SELECT id FROM models WHERE slug = '" . $model . "' AND parentId = '" . $vendId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $yearId = intval($dbcon->query("SELECT id FROM years WHERE slug = '" . $year . "' AND parentId = '" . $modelId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $isNextLevelCompleted = $dbcon->query("SELECT completed FROM years WHERE slug = '" . $year . "' AND parentId='" . $modelId . "'")->fetch(PDO::FETCH_ASSOC);
        if($isNextLevelCompleted["completed"] == 0){
            $res = callAPI("https://api.wheel-size.com/v2/modifications/?make=" . $vend . "&model=" . $model . "&year=" . $year . "&user_key=467886d78550ce67d42cd4591173155a");
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
        $res = $dbcon->query("SELECT data FROM modifications WHERE parentId = '" . $yearId . "'")->fetchAll(PDO::FETCH_ASSOC);
        $data = [];
        for($i = 0;$i < sizeof($res);$i++){
            $mp = json_decode($res[$i]["data"], $associative=true);
            array_push($data, $mp);
        }
        return $data;
    }else{
        $vendId = intval($dbcon->query("SELECT id FROM vendors WHERE slug = '" . $vend . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $modelId = intval($dbcon->query("SELECT id FROM models WHERE slug = '" . $model . "' AND parentId = '" . $vendId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $yearId = intval($dbcon->query("SELECT id FROM years WHERE slug = '" . $year . "' AND parentId = '" . $modelId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $modifId = intval($dbcon->query("SELECT id FROM modifications WHERE slug = '" . $modif . "' AND parentId = '" . $yearId . "'")->fetch(PDO::FETCH_ASSOC)["id"]);
        $isNextLevelCompleted = $dbcon->query("SELECT completed FROM modifications WHERE slug = '" . $modif . "' AND parentId='" . $yearId . "'")->fetch(PDO::FETCH_ASSOC);
        if($isNextLevelCompleted["completed"] == 0){
            $res = callAPI("https://api.wheel-size.com/v2/search/by_model/?make=" . $vend . "&model=" . $model . "&year=" . $year . "&modification=" . $modif . "&user_key=467886d78550ce67d42cd4591173155a");
            for($i = 0;$i < sizeof($res);$i++){
                $query = $dbcon->prepare("INSERT INTO wheelsInfo (parentId, name, slug, data) VALUES (:parentId, :name, :slug, :data)");
                $query->bindParam(":parentId", $modifId);
                $query->bindParam(":name", $res[$i]["name"]);
                $query->bindParam(":slug", $res[$i]["slug"]);
                $encoded = json_encode($res[$i]);
                $query->bindParam(":data", $encoded);
                $query->execute();
            }
            $dbcon->query("UPDATE modifications SET completed = 1 WHERE slug = '" . $modif . "'");
        }
        $res = $dbcon->query("SELECT data FROM wheelsInfo WHERE parentId = '" . $modifId . "'")->fetchAll(PDO::FETCH_ASSOC);
        $data = json_decode($res[0]["data"], $associative=true);
        return $data;
    }
}

?>