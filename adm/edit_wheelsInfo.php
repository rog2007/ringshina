<?php
require("func_podbor.php");
$str .= '<table><tr><td style="width:140px;vertical-align:top">';
include_once("lmenu.php");

$str .= "<td style='vertical-align:top;margin-top:0px;'>";

$str .= "<div style='margin-left:100px;'>
    <a href='/adm/spravochnic_podbor/vendors/page/1/'>Марки авто</a> | 
    <a href='/adm/spravochnic_podbor/models/page/1/'>Модели авто</a> | 
    <a href='/adm/spravochnic_podbor/years/page/1/'>Год выпуска авто</a> | 
    <a href='/adm/spravochnic_podbor/modifications/page/1/'>Модификация авто</a></div>";

$info = get_wheelsInfo($arg[0]);
$path = get_path($info, "wheelsInfo");
$data = json_decode($info["data"], $associative=true);

for($i = 0;$i < sizeof($path);$i++){
    $str .= $path[$i]["id"] . " ";
}

for($i = 0;$i < sizeof($path);$i++){
    $str .= $path[$i]["name"] . " ";
}

$str .= "<form action='/adm/podbor_save/wheelsInfo/" . $info["id"] . "/' method='post'>";

$str .= "<table style='width:auto;'>";

$keys = ["technical-wheel_fasteners-type", "technical-wheel_fasteners-thread_size", "technical-stud_holes", "technical-pcd", "technical-centre_bore"];
$names = ["Болты или гайки", "Размер", "Количество", "PCD", "Ступица"];

$str .= "<tr>";
for($i = 0;$i < sizeof($names);$i++){
    $str .= "<td><p style='margin-right:2px;'>" . $names[$i] . "</p></td>";
}
$str .= "</tr>";

$str .= "<tr>";
for($i = 0;$i < sizeof($keys);$i++){
    $str .= "<td>";
    if($keys[$i] == "technical-wheel_fasteners-type"){
        $value = get_value($data, explode("-", $keys[$i]), 0);
        $str .= "<select name='" . $keys[$i] . "'>";
        if($value == "Lug bolts"){
            $str .= "<option selected value='Lug bolts'>Болты</option>";
            $str .= "<option value='Lug nuts'>Гайки</option>";
        }else{
            $str .= "<option value='Lug bolts'>Болты</option>";
            $str .= "<option selected value='Lug nuts'>Гайки</option>";
        }
        $str .= "</select>";
        $str .= "</td>";
        continue;
    }
    $value = get_value($data, explode("-", $keys[$i]), 0);
    $str .= "<input name='" . $keys[$i] . "' value='" . to_string($value) . "'>";
    $str .= "</td>";
}
$str .= "</tr>";

$str .= "</table>";

$str .= "<p>Колёса</p>";

$str .= "<input id='count'  type='hidden' name='count' value='" . sizeof($data["wheels"]) . "'>";
$str .= "<input type='hidden' name='mode' value='update'>";

$keys = ["is_stock", "showing_fp_only", "front-rim_diameter", "front-rim_width", "front-rim_offset", "front-tire_construction", "front-tire_width", "front-tire_aspect_ratio",
                                                "rear-rim_diameter", "rear-rim_width", "rear-rim_offset", "rear-tire_construction", "rear-tire_width", "rear-tire_aspect_ratio"];
$names = ["Стоковые колёса", "Одинаковые оси", "Диаметр передних дисков", "Ширина переднего диска", "Вылет спереди", "Ширина профиля спереди", "Высота профиля спереди",
                                                "Диаметр задних дисков", "Ширина заднего диска", "Вылет заднего", "Ширина профиля сзади", "Высота профиля сзади"];

$str .= "<div id='wheels'>";
$str .= "<table style='width:auto;' id='wheels_table'>";

$str .= "<tr>";
for($i = 0;$i < sizeof($names);$i++){
    $str .= "<td><p style='margin-right:2px;'>" . $names[$i] . "</p></td>";
}
$str .= "</tr>";

for($i = 0;$i < sizeof($data["wheels"]);$i++){
    $str .= "<tr id='wheel" . $i . "'>";
    for($j = 0;$j < sizeof($keys);$j++){
        if($keys[$j] == "is_stock" || $keys[$j] == "showing_fp_only"){
            $value = get_value($data["wheels"][$i], explode("-", $keys[$j]), 0);
            $str .= "<td style='width:10px;'><select name='" . $keys[$j] . $i . "'>";
            if($value == "true"){
                $str .= "<option value='true' selected>да</option>";
                $str .= "<option value='false'>нет</option>";
            }else{
                $str .= "<option value='true'>да</option>";
                $str .= "<option value='false' selected>нет</option>";
            }
            $str .= "</select></td>";
            continue;
        }else if($keys[$j] == "front-tire_construction" || $keys[$j] == "rear-tire_construction"){
            $value = get_value($data["wheels"][$i], explode("-", $keys[$j]), 0);
            $str .= "<input type='hidden' style='width:130px;' name='" . $keys[$j] . $i . "' value='" . to_string($value) . "'>";
            continue;
        }
        $value = get_value($data["wheels"][$i], explode("-", $keys[$j]), 0);
        $str .= "<td style='width:10px;'><input style='width:130px;' name='" . $keys[$j] . $i . "' value='" . to_string($value) . "'></td>";
    }
    $str .= "<td><button type='button' onclick='del_elem_for_id(" . '"wheel' . $i . '"' . ")'>удалить</button></td></div>";
    $str .= "</tr>";
}
$str .= "</table>";
$str .= "</div>";

$str .= "<button onclick='add_wheels_config()' type='button'>Добавить конфиг колёс</button>";

$str .= "<button type='submit'>сохранить</button>";

$str .= "</form>";

$str .= "</td>"

?>