<?php
require("func_podbor.php");
$str .= '<table><tr><td style="width:140px;vertical-align:top">';
include_once("lmenu.php");
$str .= '</td><td style="vertical-align:top">';
$str .= "<div style='margin-left:100px;'>
    <a href='/adm/spravochnic_podbor/vendors/1/'>Марки авто</a> | 
    <a href='/adm/spravochnic_podbor/models/1/'>Модели авто</a> | 
    <a href='/adm/spravochnic_podbor/years/1/'>Год выпуска авто</a> | 
    <a href='/adm/spravochnic_podbor/modifications/1/'>Модификация авто</a>";

$str .= "<div id='nomens'><table id='nomen'>";

$str .= get_head($arg[0]);

$str .= "<form action='/adm/spravochnic_podbor/" . $arg[0] . "/1' method='POST' name='filters'>";

$str .= get_filters($arg[0]);

$name = filter_input(INPUT_POST, "name");

$str .=         "<input name='name' placeholder='Введите начало имени' value='" . $name . "'>
            <button type='submit'>Фильтровать</button>
        </form>";


$data_list = get_rows_list($arg[0]);

if($name){
    $data_list = filter_name($data_list, $name);
}
$vendor = filter_input(INPUT_POST, "vend");
if($vendor && $vendor != "all"){
    $data_list = filter_path($data_list, 0, $vendor, $arg[0]);
    $str .= "<script>set_selected('filter_vend', " . $vendor . ");</script>";
}
$model = filter_input(INPUT_POST, "model");
if($model && $model != "all"){
    $data_list = filter_path($data_list, 1, $model, $arg[0]);
    $str .= '<script>document.getElementById("filter_model").innerHTML = "<option value=' . "'" . "all" . "'" . '>все</option>' . get_select_options($vendor, $model, "models") . '";document.getElementById("filter_model").disabled=false;</script>';
}else if($vendor && $vendor != "all"){
    $str .= "<script>filter_add(1)</script>";
}
$year = filter_input(INPUT_POST, "year");
if($year && $year != "all"){
    $data_list = filter_path($data_list, 2, $year, $arg[0]);
    $str .= '<script>document.getElementById("filter_year").innerHTML = "<option value=' . "'" . "all" . "'" . '>все</option>' . get_select_options($model, $year, "years") . '";document.getElementById("filter_year").disabled=false;</script>';
}else if($model && $model != "all"){
    $str .= "<script>filter_add(2)</script>";
}

$page_size = 100;

$str .= "<div class='sp-pages'>";
$str .= "<span>Страницы: </span>";
$str .= PagesCreate(ceil(sizeof($data_list) / $page_size),  $arg[1], $arg[0]);
$str .= "</div>";

for($i = ($arg[1] - 1) * $page_size;$i < min(sizeof($data_list), $arg[1] * $page_size);$i++){
    $str .= get_row($arg[0], $data_list[$i]);
}

$str .= "</table></div>";
$str .= "</td>";
$str .= "</tr></table>";
?>