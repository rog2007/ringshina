<?php
require("func_podbor.php");
$str .= '<table><tr><td style="width:140px;vertical-align:top">';
include_once("lmenu.php");
$str .= '</td><td style="vertical-align:top">';
$str .= "<div style='margin-left:100px;'>
    <a href='/adm/spravochnic_podbor/vendors/page/1/'>Марки авто</a> | 
    <a href='/adm/spravochnic_podbor/models/page/1/'>Модели авто</a> | 
    <a href='/adm/spravochnic_podbor/years/page/1/'>Год выпуска авто</a> | 
    <a href='/adm/spravochnic_podbor/modifications/page/1/'>Модификация авто</a>";

$str .= "<div id='nomens'><table id='nomen'>";

$parsed_args = parse_args(1, $arg);
$name = filter_input(INPUT_POST, "name");
if(!$name && array_key_exists("name", $parsed_args)){
    $name = $parsed_args["name"];
}
$vendor = filter_input(INPUT_POST, "vend");
if(!$vendor && array_key_exists("vend", $parsed_args)){
    $vendor = $parsed_args["vend"];
}
$model = filter_input(INPUT_POST, "model");
if(!$model && array_key_exists("model", $parsed_args)){
    $model = $parsed_args["model"];
}
$year = filter_input(INPUT_POST, "year");
if(!$year && array_key_exists("year", $parsed_args)){
    $year = $parsed_args["year"];
}

$str .= get_head($arg[0]);

$data_list = get_rows_list($arg[0], $name, $vendor, $model, $year);

$str .= "<form action='/adm/spravochnic_podbor/" . $arg[0] . "/page/1/' method='POST' name='filters'>";

$str .= get_filters($arg[0]);

$str .=         "<input name='name' placeholder='Введите начало имени' value='" . $name . "'>
            <button type='submit'>Фильтровать</button>
        </form>";

$filters = "";

if($name){
    $filters .= "name/" . $name . "/";
}
if($vendor && $vendor != "all"){
    $filters .= "vend/" . $vendor . "/";
    $str .= "<script>set_selected('filter_vend', " . $vendor . ");</script>";
}
if($model && $model != "all"){
    $filters .= "model/" . $model . "/";
    $str .= '<script>document.getElementById("filter_model").innerHTML = "<option value=' . "'" . "all" . "'" . '>все</option>' . get_select_options($vendor, $model, "models") . '";document.getElementById("filter_model").disabled=false;</script>';
}else if($vendor && $vendor != "all"){
    $str .= "<script>filter_add(1)</script>";
}
if($year && $year != "all"){
    $filters .= "year/" . $year . "/";
    $str .= '<script>document.getElementById("filter_year").innerHTML = "<option value=' . "'" . "all" . "'" . '>все</option>' . get_select_options($model, $year, "years") . '";document.getElementById("filter_year").disabled=false;</script>';
}else if($model && $model != "all"){
    $str .= "<script>filter_add(2)</script>";
}

$page_size = 100;

$str .= "<div class='sp-pages'>";
$str .= "<span>Страницы: </span>";
$str .= PagesCreate(ceil(sizeof($data_list) / $page_size),  $parsed_args["page"], $arg[0], $filters);
$str .= "</div>";

for($i = ($parsed_args["page"] - 1) * $page_size;$i < min(sizeof($data_list), $parsed_args["page"] * $page_size);$i++){
    $str .= get_row($arg[0], $data_list[$i]);
}

$str .= "</table></div>";
$str .= "</td>";
$str .= "</tr></table>";
?>