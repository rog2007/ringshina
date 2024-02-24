<?php
// (\s(\d{2,3}(\/(\d{2,3}))?)(\w{1,2}))?(\s\((\d{2,3}(\/(\d{2,3}))?)(\w{1,2})\))?  индексы скорости и нагрузки
function LoadFilToFtp($name, $mXLS)
{
    $uploaddir = $_SERVER["DOCUMENT_ROOT"] . '/adm/prices/' . $name . '.xls';
    if (move_uploaded_file($mXLS, $uploaddir)) return 'Файл загружен: ' . $uploaddir;
    else return 'Файл загружен не был: ' . $uploaddir;
}

function sql2arr2($sql)
{
    $result = [];
    $res = query($sql);
    if ($res['result'] === false) {
        return $result;
    }
    foreach ($res['data'] as $rs) {
        $result[] = $rs->id;
    }
    return $result;
}

function ruslow($s)
{
    $rusl = "абвгдеёжзийклмнопрстуфхцчшщъыьэюя";
    $rusb = "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
    $s = strtr($s, $rusb, $rusl);
    return $s;
}