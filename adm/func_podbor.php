<?php
require("connect.php");
function get_head($arg){
    global $dbcon;
    if($arg == "vendors"){
        return "<tr class='head'>
                    <td class='idtov'>ID</td>
                    <td class='name'>Наименивание</td>
                    <td class='name'>URL</td>
                    <td>Сохранить</td>
                    <td>Удалить</td>
                </tr>
                <form action='/adm/podbor_save/vendors/' method='post'><tr class='skld'>
                    <input type='hidden' value='add' name='mode'>
                    <td style='padding:10px;'> - </td>
                    <td style='padding:10px;' class='nm'><input type='text' name='name' placeholder='Наименование марки авто'></td>
                    <td style='padding:10px;' class='nm'><input type='text' name='slug' placeholder='Отформатированое наименование'></td>
                    <td style='padding:10px;'><button type='submit'>Сохранить</button></td>
                </tr></form>";
    }else if($arg == "models"){
        $ans = "<tr class='head'>
                    <td class='idtov'>ID</td>
                    <td class='name'>Марка машины</td>
                    <td class='name'>Наименивание</td>
                    <td class='name'>URL</td>
                    <td>Сохранить</td>
                    <td>Удалить</td>
                </tr>
                <form action='/adm/podbor_save/models/' method='post'><tr class='skld'>
                    <input type='hidden' value='add' name='mode'>
                    <td style='padding:10px;'> - </td>
                    <td style='padding:10px;' class='nm'><select name='parent'>";

        $vendors = $dbcon->query("SELECT name, id FROM vendors")->fetchAll(PDO::FETCH_ASSOC);
        for($i = 0;$i < sizeof($vendors);$i++){
            $ans .= "<option value='" . $vendors[$i]["id"] . "'>" . $vendors[$i]["name"] . "</option>";
        }

        $ans .= "</select></td>
                    <td style='padding:10px;' class='nm'><input type='text' name='name' placeholder='Наименование модели авто'></td>
                    <td style='padding:10px;' class='nm'><input type='text' name='slug' placeholder='Отформатированое наименование'></td>
                    <td style='padding:10px;'><button type='submit'>Сохранить</button></td>
                </tr></form>";
        return $ans;
    }else if($arg == "years"){
        $ans = "<tr class='head'>
                    <td class='idtov'>ID</td>
                    <td class='name'>Марка машины</td>
                    <td class='name'>Модель машины</td>
                    <td>Наименивание</td>
                    <td>URL</td>
                    <td>Сохранить</td>
                    <td>Удалить</td>
                </tr>
                <form action='/adm/podbor_save/years/' method='post' name='add'><tr class='skld'>
                    <input type='hidden' value='add' name='mode'>
                    <td style='padding:10px;'>-</td>
                    <td style='padding:10px;' class='nm'><select name='vend' onchange='return admin_add_ajax(1)'>";
                    
        $ans .= "<option value='0'>все</option>";
        $vendors = $dbcon->query("SELECT name, id FROM vendors")->fetchAll(PDO::FETCH_ASSOC);
        for($i = 0;$i < sizeof($vendors);$i++){
            $ans .= "<option value='" . $vendors[$i]["id"] . "'>" . $vendors[$i]["name"] . "</option>";
        }
                    
        $ans .= "</select></td>
                    <td style='padding:10px;' class='nm'><select disabled name='model'><option>все</option></select></td>
                    <td style='padding:10px;'><input type='text' name='name' placeholder='Год'></td>
                    <td style='padding:10px;'><input type='text' name='slug' placeholder='Год'></td>
                    <td style='padding:10px;'><button type='submit'>Сохранить</button></td>
                </tr></form>";
        return $ans;
    }else if($arg == "modifications"){
        $ans =  "<tr class='head'>
                    <td class='idtov'>ID</td>
                    <td class='name'>Марка машины</td>
                    <td class='name'>Модель машины</td>
                    <td>Год</td>
                    <td>Модификация</td>
                    <td>URL</td>
                    <td>Сохранить</td>
                    <td>Удалить</td>
                    <td>Изменить</td>
                </tr>
                <form action='/adm/podbor_save/modifications/' method='post' name='add'><tr class='skld'>
                    <input type='hidden' value='add' name='mode'>
                    <td style='padding:10px;'> - </td>
                    <td style='padding:10px;' class='nm'><select name='vend' onchange='admin_add_ajax(1)'>";
                
        $ans .= "<option value='0'>все</option>";
        $vendors = $dbcon->query("SELECT name, id FROM vendors")->fetchAll(PDO::FETCH_ASSOC);
        for($i = 0;$i < sizeof($vendors);$i++){
            $ans .= "<option value='" . $vendors[$i]["id"] . "'>" . $vendors[$i]["name"] . "</option>";
        }
                    
        $ans .= "</select></td>
                    <td style='padding:10px;' class='nm'><select disabled name='model' onchange='admin_add_ajax(2)'><option>все</option></select></td>
                    <td style='padding:10px;'><select disabled name='year'><option>все</option></select></td>
                    <td style='padding:10px;'><input type='text' name='name' placeholder='Модификация'></td>
                    <td style='padding:10px;'><input type='text' name='slug' placeholder='URL'></td>
                    <td style='padding:10px;'><button type='submit'>Сохранить</button></td>
                </tr></form>";
        return $ans;
    }else if($arg == "wheelsInfo"){
        $ans =  "<tr class='head'>
                    <td class='idtov'>ID</td>
                    <td class='name'>Марка машины</td>
                    <td class='name'>Модель машины</td>
                    <td>Год</td>
                    <td>Модификация</td>
                    <td>URL</td>
                    <td>Сохранить</td>
                    <td>Удалить</td>
                </tr>";
        return $ans;
    }
}

function get_rows_list($table, $name, $vendor, $model, $year){
    global $dbcon;
    $tables = ["vendors", "models", "years", "modifications"];
    $col = ["id", "name", "slug"];
    $query = "SELECT ";
    $joins = "";
    for($i = 0;$i < sizeof($tables);$i++){
        for($j = 0;$j < sizeof($col);$j++){
            if($i != 0 || $j != 0){
                $query .= ", ";
            }
            $query .= $tables[$i] . "." . $col[$j] . " " . substr($tables[$i], 0, 4) . $col[$j];
        }

        if($table == $tables[$i]){
            break;
        }

        $joins = "JOIN " . $tables[$i] . " ON " . $tables[$i] . ".id=" . $tables[$i + 1] . ".parentId " . $joins;
    }

    $query .= " FROM " . $table . " " . $joins;

    $cond = [];

    $order = "vendname";

    if($name && $name != "all"){
        $cond[] = "(" . $table . ".name LIKE '%". $name . "%' OR " . $table . ".slug LIKE '%" . $name . "%')";
    }
    if($vendor && $vendor != "all"){
        $cond[] = "vendors.id=" . $vendor;
        if($order != ""){
            $order .= ", ";
        }
        $order .= "modename";
    }else{
        if($order != ""){
            $order .= ", ";
        }
        $order .= "vendslug";
    }
    if($model && $model != "all"){
        $cond[] = "models.id=" . $model;
        if($order != ""){
            $order .= ", ";
        }
        $order .= "yearname";
    }else{
        if($order != ""){
            $order .= ", ";
        }
        $order .= "modeslug";
    }
    if($year && $year != "all"){
        $cond[] = "years.id=" . $year;
        if($order != ""){
            $order .= ", ";
        }
        $order .= "modiname";
        $order .= "modislug";
    }else{
        if($order != ""){
            $order .= ", ";
        }
        $order .= "yearslug";
    }

    if(sizeof($cond) != 0){
        $query .= " WHERE";
    }

    for($i = 0;$i < sizeof($cond);$i++){
        if($i != 0){
            $query .= " AND";
        }
        $query .= " " . $cond[$i];
    }

    if($order != ""){
        $query .= " ORDER BY " . $order;
    }

    $res = $dbcon->query($query)->fetchAll(PDO::FETCH_ASSOC);
    return $res;
}

function get_row($arg, $data){
    global $dbcon;
    if($arg == "vendors"){
        return "<tr class='skld'><form action='/adm/podbor_save/vendors/" . $data[substr($arg, 0, 4) . "id"] . "/' method='post'>
                    <input type='hidden' value='update' name='mode'>
                    <td name='id' class='identificator'>" . $data[substr($arg, 0, 4) . "id"] . "</td>
                    <td class='nm'><input type='text' name='name' value='" . $data[substr($arg, 0, 4) . "name"] . "'></td>
                    <td class='nm'><input type='text' name='slug' value='" . $data[substr($arg, 0, 4) . "slug"] . "'></td>
                    <td><button type='submit'>Сохранить</button></td></form>
                    <td style='width:30px'>
                        <button onclick='delete_for_id(" . '"vendors"' . ", " . $data[substr($arg, 0, 4) . "id"] . ")'>Удалить</button>
                    </td>
                </tr>";
    }else if($arg == "models"){
        $vendors = $dbcon->query("SELECT name, id FROM vendors")->fetchAll(PDO::FETCH_ASSOC);
        $ans = "<tr class='skld'><form action='/adm/podbor_save/models/" . $data[substr($arg, 0, 4) . "id"] . "/' method='post'>
                    <input type='hidden' value='update' name='mode'>
                    <td name='id' class='identificator'>" . $data[substr($arg, 0, 4) . "id"] . "</td>
                    <td class='nm'><select name='parent'>";

        for($i = 0;$i < sizeof($vendors);$i++){
            if($vendors[$i]["id"] == $data["vendid"]){
                $ans .= "<option selected value='" . $vendors[$i]["id"] . "'>" . $vendors[$i]["name"] . "</option>";
            }else{
                $ans .= "<option value='" . $vendors[$i]["id"] . "'>" . $vendors[$i]["name"] . "</option>";
            }
        }

        $ans .= "</select></td>
                    <td class='nm'><input type='text' name='name' value='" . $data[substr($arg, 0, 4) . "name"] . "'></td>
                    <td class='nm'><input type='text' name='slug' value='" . $data[substr($arg, 0, 4) . "slug"] . "'></td>
                    <td><button type='submit'>Сохранить</button></td></form>
                    <td style='width:30px'>
                        <button onclick='delete_for_id(" . '"models"' . ", " . $data[substr($arg, 0, 4) . "id"] . ")'>Удалить</button>
                    </td>
                </tr>";
        return $ans;
    }else if($arg == "years"){
        $models = $dbcon->query("SELECT name, id FROM models WHERE parentId=" . $data["vendid"])->fetchAll(PDO::FETCH_ASSOC);
        $ans = "<tr class='skld'><form action='/adm/podbor_save/years/" . $data[substr($arg, 0, 4) . "id"] . "/' method='post'>
                    <input type='hidden' value='update' name='mode'>
                    <td name='id' class='identificator'>" . $data[substr($arg, 0, 4) . "id"] . "</td>
                    <td class='nm'>" . $data[substr($arg, 0, 4) . "name"] . "</td>
                    <td class='nm'><select name='model'>";

        for($i = 0;$i < sizeof($models);$i++){
            if($data["modeid"] == $models[$i]["id"]){
                $ans .= "<option selected value='" . $models[$i]["id"] . "'>" . $models[$i]["name"] . "</option>";
            }else{
                $ans .= "<option value='" . $models[$i]["id"] . "'>" . $models[$i]["name"] . "</option>";
            }
        }
        
        $ans .= "</select></td>
                    <td><input type='text' name='name' value='" . $data[substr($arg, 0, 4) . "name"] . "'></td>
                    <td><input type='text' name='slug' value='" . $data[substr($arg, 0, 4) . "slug"] . "'></td>
                    <td><button type='submit'>Сохранить</button></td></form>
                    <td style='width:30px'>
                        <button onclick='delete_for_id(" . '"years"' . ", " . $data[substr($arg, 0, 4) . "id"] . ")'>Удалить</button>
                    </td>
                </tr>";
        return $ans;
    }else if($arg == "modifications"){
        $years = $dbcon->query("SELECT name, id FROM years WHERE parentId=" . $data["modeid"])->fetchAll(PDO::FETCH_ASSOC);
        $ans = "<tr class='skld'><form action='/adm/podbor_save/modifications/" . $data[substr($arg, 0, 4) . "id"] . "/' method='post'>
                    <input type='hidden' value='update' name='mode' />
                    <td name='id' class='identificator'>" . $data[substr($arg, 0, 4) . "id"] . "</td>
                    <td class='nm'>" . $data["vendname"] . "</td>
                    <td>" . $data["modename"] . "</td>
                    <td class='nm'><select name='year'>";

        for($i = 0;$i < sizeof($years);$i++){
            if($data["yearname"] == $years[$i]["name"]){
                $ans .= "<option selected value='" . $years[$i]["id"] . "'>" . $years[$i]["name"] . "</option>";
            }else{
                $ans .= "<option value='" . $years[$i]["id"] . "'>" . $years[$i]["name"] . "</option>";
            }
        }
        
        $ans .= "</select></td>
                    <td><input type='text' name='name' value='" . $data[substr($arg, 0, 4) . "name"] . "'></td>
                    <td><input type='text' name='slug' value='" . $data[substr($arg, 0, 4) . "slug"] . "'></td>
                    <td><button type='submit'>Сохранить</button></td></form>
                    <td style='width:30px'>
                        <button onclick='delete_for_id(" . '"modifications"' . ", " . $data[substr($arg, 0, 4) . "id"] . ")'>Удалить</button>
                    </td>
                    <td><a href='/adm/edit_wheelsInfo/" . $data[substr($arg, 0, 4) . "id"] . "/'>изменить</a></td>
                </tr>";
        return $ans;
    }
}

function get_step($arg){
    if($arg == "vendors"){
        return 0;
    }else if($arg == "models"){
        return 1;
    }else if($arg == "years"){
        return 2;
    }else if($arg == "modifications"){
        return 3;
    }
    return 4;
}

function get_filters($arg){
    global $dbcon;
    $st = get_step($arg);
    $ans = "";
    if($st >= 1){
        $ans .= "<select id='filter_vend' name='vend' onchange='filter_add(1)'>
                    <option value='all'>все</option>";
        
        $vendors = $dbcon->query("SELECT id, name FROM vendors")->fetchAll(PDO::FETCH_ASSOC);

        for($i = 0;$i < sizeof($vendors);$i++){
            $ans .= "<option value='" . $vendors[$i]["id"] . "'>" . $vendors[$i]["name"] . "</option>";
        }

        $ans .= "</select>";
    }
    if($st >= 2){
        $ans .= "<select id='filter_model' name='model' disabled onchange='filter_add(2)'>
                    <option value='all'>все</option>
                </select>";
    }
    if($st >= 3){
        $ans .= "<select id='filter_year' name='year' disabled>
                    <option value='all'>все</option>
                </select>";
    }
    return $ans;
}

function PagesCreate($all_cn, $crpg, $class, $filters)
{
    $strtmp = '';
    if ($all_cn < 8) {
        for ($i = 1; $i <= $all_cn; $i++) {
            $strtmp .= ($i != $crpg ? '<a href="/adm/spravochnic_podbor/' . $class . '/page/' . $i . '/' . $filters . '">' . $i .
                '</a>' : '<span>' . $i . '</span>');
        }
    }
    if ($all_cn >= 8) {
        if ($crpg < 4) {
            for ($i = 1; $i <= 5; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="/adm/spravochnic_podbor/' . $class . '/page/' . $i . '/' . $filters . '">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
            $strtmp .= '<a href="">&hellip;</a><a href="/adm/spravochnic_podbor/' . $class . '/page/' . $all_cn . '/' . $filters . '">' .
                $all_cn . '</a>';
        }
        if ($crpg == 4) {
            for ($i = 1; $i <= 6; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="/adm/spravochnic_podbor/' . $class . '/page/' . $i . '/' . $filters . '">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
            $strtmp .= '<a href="">&hellip;</a><a href="/adm/spravochnic_podbor/' . $class . '/page/' . $all_cn . '/' . $filters . '">' .
                $all_cn . '</a>';
        }
        if ($crpg > $all_cn - 3) {
            $strtmp .= '<a href="/adm/spravochnic_podbor/' . $class . '/page/1/' . $filters . '">1</a><a href="">&hellip;</a>';
            for ($i = $all_cn - 4; $i <= $all_cn; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="/adm/spravochnic_podbor/' . $class . '/page/' . $i . '/' . $filters . '">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
        }
        if ($crpg == $all_cn - 3) {
            $strtmp .= '<a href="/adm/spravochnic_podbor/' . $class . '/page/1/' . $filters . '">1</a><a href="">&hellip;</a>';
            for ($i = $all_cn - 5; $i <= $all_cn; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="/adm/spravochnic_podbor/' . $class . '/page/' . $i . '/' . $filters . '">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
        }
        if ($crpg < $all_cn - 3 && $crpg > 4) {
            $strtmp .= '<a href="/adm/spravochnic_podbor/' . $class . '/page/1/' . $filters . '">1</a><a href="">&hellip;</a>';
            for ($i = $crpg - 2; $i <= $crpg + 2; $i++) {
                $strtmp .= ($i != $crpg ? '<a href="/adm/spravochnic_podbor/' . $class . '/page/' . $i . '/' . $filters . '">' . $i .
                    '</a>' : '<span>' . $i . '</span>');
            }
            $strtmp .= '<a href="">&hellip;</a><a href="/adm/spravochnic_podbor/' . $class . '/page/' . $all_cn . '/' . $filters . '">' .
                $all_cn . '</a>';
        }
    }
    return $strtmp;
}

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
        //var_dump($obj);
        //var_dump($obj["parentId"]);
        $obj = $dbcon->query("SELECT * FROM vendors WHERE id=" . $obj["parentId"])->fetch(PDO::FETCH_ASSOC);
        $path[] = $obj;
        return $path;
    }
}

function filter_name($data, $name){
    $filtered_data = [];
    for($i = 0;$i < sizeof($data);$i++){
        if(strlen($name) <= strlen($data[$i]["name"]) && $name == substr($data[$i]["name"], 0, strlen($name)) || 
                strlen($name) <= strlen($data[$i]["slug"]) && $name == substr($data[$i]["slug"], 0, strlen($name))){
            $filtered_data[] = $data[$i];
        }
    }
    return $filtered_data;
}

function filter_path($data, $st, $id, $table){
    $filtered_data = [];
    for($i = 0;$i < sizeof($data);$i++){
        $path = get_path($data[$i], $table);
        if(intval($path[$st]["id"]) == intval($id)){
            $filtered_data[] = $data[$i];
        }
    }
    return $filtered_data;
}

function get_select_options($parentId, $selectedId, $table){
    global $dbcon;
    $data = $dbcon->query("SELECT name, id FROM " . $table . " WHERE parentId=" . $parentId)->fetchAll(PDO::FETCH_ASSOC);
    $ans = "";
    for($i = 0;$i < sizeof($data);$i++){
        if($data[$i]["id"] == $selectedId){
            $ans .= "<option selected value='" . $data[$i]["id"] . "'>" . $data[$i]["name"] . "</option>";
        }else{
            $ans .= "<option value='" . $data[$i]["id"] . "'>" . $data[$i]["name"] . "</option>";
        }
    }
    return $ans;
}

function get_wheelsInfo($id){
    global $dbcon;
    $q = $dbcon->query("SELECT * FROM wheelsInfo WHERE ParentId=" . $id)->fetch(PDO::FETCH_ASSOC);
    if(!$q){
        $parent = $dbcon->query("SELECT * FROM modifications WHERE id=" . $id)->fetch(PDO::FETCH_ASSOC);
        $query = $dbcon->prepare("INSERT INTO wheelsInfo (parentId, name, slug, data) VALUES (:parentId, :name, :slug, :data)");
        $query->bindParam(":parentId", $id);
        $query->bindParam(":name", $parent["name"]);
        $query->bindParam(":slug", $parent["slug"]);
        $data = [
                "slug" => $parent["slug"],
                "name" => $parent["name"],
                "technical" => [
                                "wheel_fasteners" => [
                                                    "type" => "", 
                                                    "thread_size" => ""
                                                    ], 
                                "stud_holes" => "", 
                                "pcd" => "", 
                                "centre_bore" => ""
                                ],
                "wheels" => [
                    ["is_stock" => "",
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
                            ]]
                ];
        $data = json_encode($data);
        $query->bindParam(":data", $data);
        $query->execute();
        return get_wheelsInfo($id);
    }
    return $q;
}

function get_value($data, $path, $st){
    if($st == sizeof($path) - 1){
        return $data[$path[$st]];
    }
    return get_value($data[$path[$st]], $path, $st + 1);
}

function to_string($value){
    if(gettype($value) == "boolean"){
        if($value){
            return "true";
        }
        return "false";
    }
    return $value;
}

function from_string($value){
    if($value == "true"){
        return true;
    }else if($value == "false"){
        return false;
    }
    return $value;
}

function parse_args($begin, $args){
    $parsed_args = array();
    for($i = $begin;$i < sizeof($args);$i += 2){
        $parsed_args[$args[$i]] = $args[$i + 1];
    }
    return $parsed_args;
}
?>
