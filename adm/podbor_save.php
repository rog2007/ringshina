<?php
require("connect.php");

function generate_json($name, $slug, $old_json = null){
    if($old_json == null){
        $arr = ["name" => $name, "slug" => $slug];
        return json_encode($arr);
    }
    $arr = json_decode($old_json, $associative=true);
    $arr["name"] = $name;
    $arr["slug"] = $slug;
    return json_encode($arr);
}

function update_vendor($name, $slug){
    global $dbcon, $arg;
    $json_data = $dbcon->query("SELECT data FROM vendors WHERE id=" . $arg[1])->fetch(PDO::FETCH_ASSOC)["data"];
    $new_json_data = generate_json($name, $slug, $json_data);
    $dbcon->query("UPDATE vendors SET name='" . $name . "', slug='" . $slug . "', data='" . $new_json_data . "' WHERE id=" . $arg[1])->fetch();
}

function update_model($name, $slug, $parentId){
    global $dbcon, $arg;
    $json_data = $dbcon->query("SELECT data FROM models WHERE id=" . $arg[1])->fetch(PDO::FETCH_ASSOC)["data"];
    $new_json_data = generate_json($name, $slug, $json_data);
    $dbcon->query("UPDATE models SET name='" . $name . "', slug='" . $slug . "', data='" . $new_json_data . "', parentId=" . $parentId . " WHERE id=" . $arg[1])->fetch();
}

function update_year($name, $slug, $parentId){
    global $dbcon, $arg;
    $json_data = $dbcon->query("SELECT data FROM years WHERE id=" . $arg[1])->fetch(PDO::FETCH_ASSOC)["data"];
    $new_json_data = generate_json($name, $slug, $json_data);
    $dbcon->query("UPDATE years SET name='" . $name . "', slug='" . $slug . "', data='" . $new_json_data . "', parentId=" . $parentId . " WHERE id=" . $arg[1])->fetch();
}

function update_modification($name, $slug, $parentId){
    global $dbcon, $arg;
    $json_data = $dbcon->query("SELECT data FROM modifications WHERE id=" . $arg[1])->fetch(PDO::FETCH_ASSOC)["data"];
    $new_json_data = generate_json($name, $slug, $json_data);
    $dbcon->query("UPDATE modifications SET name='" . $name . "', slug='" . $slug . "', data='" . $new_json_data . "', parentId=" . $parentId . " WHERE id=" . $arg[1])->fetch();
}

function update_wheelsInfo($id, $data){
    global $dbcon;
    $dbcon->query("UPDATE wheelsInfo SET data='" . $data . "' WHERE id=" . $id)->fetch();
}

function add_vendor($name, $slug){
    global $dbcon;
    $new_json_data = generate_json($name, $slug);
    $query = $dbcon->prepare("INSERT INTO vendors (name, slug, data, completed) VALUES (:name, :slug, :data, 0)");
    $query->bindParam(":name", $name);
    $query->bindParam(":slug", $slug);
    $query->bindParam(":data", $new_json_data);
    $query->execute();
}

function add_model($name, $slug, $parentId){
    global $dbcon;
    $new_json_data = generate_json($name, $slug);
    $query = $dbcon->prepare("INSERT INTO models (parentId, name, slug, data, completed) VALUES (:parentId, :name, :slug, :data, 0)");
    $query->bindParam(":parentId", $parentId);
    $query->bindParam(":name", $name);
    $query->bindParam(":slug", $slug);
    $query->bindParam(":data", $new_json_data);
    $query->execute();
}

function add_year($name, $slug, $parentId){
    global $dbcon;
    $new_json_data = generate_json($name, $slug);
    $query = $dbcon->prepare("INSERT INTO years (parentId, name, slug, data, completed) VALUES (:parentId, :name, :slug, :data, 0)");
    $query->bindParam(":parentId", $parentId);
    $query->bindParam(":name", $name);
    $query->bindParam(":slug", $slug);
    $query->bindParam(":data", $new_json_data);
    $query->execute();
}

function add_modification($name, $slug, $parentId){
    global $dbcon;
    $new_json_data = generate_json($name, $slug);
    $query = $dbcon->prepare("INSERT INTO modifications (parentId, name, slug, data, completed) VALUES (:parentId, :name, :slug, :data, 0)");
    $query->bindParam(":parentId", $parentId);
    $query->bindParam(":name", $name);
    $query->bindParam(":slug", $slug);
    $query->bindParam(":data", $new_json_data);
    $query->execute();
}

function delete_for_id($id, $table){
    global $dbcon;
    $tables = ["vendors", "models", "years", "modifications", "wheelsInfo"];
    $cur_table = array_search($table, $tables);
    if($cur_table != sizeof($tables) - 1){
        $children = $dbcon->query("SELECT id FROM " . $tables[$cur_table + 1] . " WHERE parentId=" . $id)->fetchAll(PDO::FETCH_ASSOC);
        for($i = 0;$i < sizeof($children);$i++){
            delete_for_id($children[$i]["id"], $tables[$cur_table + 1]);
        }
    }
    $dbcon->prepare("DELETE FROM " . $table . " WHERE id=" . $id)->execute();
}

function update_wheels_info_data($data){
    $keys = ["technical-wheel_fasteners-type", "technical-wheel_fasteners-thread_size", "technical-stud_holes", "technical-pcd", "technical-centre_bore"];
    for($i = 0;$i < sizeof($keys);$i++){
        $new = filter_input(INPUT_POST, $keys[$i]);
        $path = explode("-", $keys[$i]);
        if(sizeof($path) == 1){
            $data[$path[0]] = $new;
        }else if(sizeof($path) == 2){
            $data[$path[0]][$path[1]] = $new;
        }else if(sizeof($path) == 3){
            $data[$path[0]][$path[1]][$path[2]] = $new;
        }
    }

    $cnt = filter_input(INPUT_POST, "count");
    $keys = ["is_stock", "showing_fp_only", "front-rim_diameter", "front-rim_width", "front-rim_offset", "front-tire_construction", "front-tire_width", "front-tire_aspect_ratio",
                                                "rear-rim_diameter", "rear-rim_width", "rear-rim_offset", "rear-tire_construction", "rear-tire_width", "rear-tire_aspect_ratio"];

    for($i = 0;$i < $cnt;$i++){
        if($i >= sizeof($data["wheels"])){
            $data["wheels"][] = [
                        "is_stock" => "",
                        "showing_fp_only" => "",
                        "front" => [
                                    "rim_diameter" => "",
                                    "rim_width" => "",
                                    "rim_offset" => "",
                                    "tire_construction" => "",
                                    "tire_width" => "",
                                    "tire_aspect_ratio" => ""
                                    ],
                        "front" => [
                                    "rim_diameter" => "",
                                    "rim_width" => "",
                                    "rim_offset" => "",
                                    "tire_construction" => "",
                                    "tire_width" => "",
                                    "tire_aspect_ratio" => ""
                                    ]
                                ];
        }
        for($j = 0;$j < sizeof($keys);$j++){
            $new = filter_input(INPUT_POST, $keys[$j] . $i);
            $path = explode("-", $keys[$j]);
            if(sizeof($path) == 1){
                $data["wheels"][$i][$path[0]] = $new;
            }else if(sizeof($path) == 2){
                $data["wheels"][$i][$path[0]][$path[1]] = $new;
            }else if(sizeof($path) == 3){
                $data["wheels"][$i][$path[0]][$path[1]][$path[2]] = $new;
            }
        }
    }

    while(sizeof($data["wheels"]) > $cnt){
        array_pop($data["wheels"]);
    }

    return $data;
}

if($arg[0] == "vendors"){
    $name = filter_input(INPUT_POST, "name");
    $slug = filter_input(INPUT_POST, "slug");
    $mode = filter_input(INPUT_POST, "mode");
    if($mode == "update"){
        update_vendor($name, $slug);
    }else if($mode == "add"){
        add_vendor($name, $slug);
    }else if($mode == "delete"){
        delete_for_id($arg[1], "vendors");
    }
}else if($arg[0] == "models"){
    $name = filter_input(INPUT_POST, "name");
    $slug = filter_input(INPUT_POST, "slug");
    $mode = filter_input(INPUT_POST, "mode");
    $parentId = filter_input(INPUT_POST, "parent");
    if($mode == "update"){
        update_model($name, $slug, $parentId);
    }else if($mode == "add"){
        add_model($name, $slug, $parentId);
    }else if($mode == "delete"){
        delete_for_id($arg[1], "models");
    }
}else if($arg[0] == "years"){
    $name = filter_input(INPUT_POST, "name");
    $slug = filter_input(INPUT_POST, "slug");
    $mode = filter_input(INPUT_POST, "mode");
    $parentId = filter_input(INPUT_POST, "model");
    if($mode == "update"){
        update_year($name, $slug, $parentId);
    }else if($mode == "add"){
        add_year($name, $slug, $parentId);
    }else if($mode == "delete"){
        delete_for_id($arg[1], "years");
    }
}else if($arg[0] == "modifications"){
    $name = filter_input(INPUT_POST, "name");
    $slug = filter_input(INPUT_POST, "slug");
    $mode = filter_input(INPUT_POST, "mode");
    $parentId = filter_input(INPUT_POST, "year");
    if($mode == "update"){
        update_modification($name, $slug, $parentId);
    }else if($mode == "add"){
        add_modification($name, $slug, $parentId);
    }else if($mode == "delete"){
        delete_for_id($arg[1], "modifications");
    }
}else if($arg[0] == "wheelsInfo"){
    $mode = filter_input(INPUT_POST, "mode");
    if($mode == "update"){
        $data = $dbcon->query("SELECT data FROM wheelsInfo WHERE id=" . $arg[1])->fetch(PDO::FETCH_ASSOC);
        $data = json_decode($data["data"], $associative=true);
        $data = update_wheels_info_data($data);
        update_wheelsInfo($arg[1], json_encode($data));
    }
}
$is_ajax = filter_input(INPUT_POST, "is_ajax");
if(!$is_ajax){
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}else{
    exit(0);
}
?>