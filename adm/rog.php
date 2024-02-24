<?php
require("connect.php");
require("func_new.php");

$res = query('SELECT * FROM tab2 WHERE tb2_tov_id = 2');
if ($res['result'] === false) {
    echo '<p>Поиск загрузки. Ошибка обращения к БД. ' . dbLastErrorToString() . '</a>';
    exit;
}
foreach ($res as $rs) {
    echo $rs->tb2_sn . '<br>';
    preg_match_all("#" . $rs->tb2_sn . "#i", $s, $dm, PREG_SET_ORDER);
}
die;


$str .= '<h2>Логи</h2><ul>';
$loadId = filter_input(INPUT_POST, 'load_id', FILTER_VALIDATE_INT);
if ((int)$loadId < 0) {
    echo '<p>Укажите какой прайс обнуляем</p><a href="/lprices/">К началу загрузки прайса</a>';
    exit;
}
$res = query('SELECT * FROM parser WHERE id=:load_id', [':load_id' => $loadId]);
if ($res === false) {
    echo '<p>Поиск загрузки. Ошибка обращения к БД. ' . dbLastErrorToString() . '</a>';
    exit;
}
var_dump($res); die;
if (count($res) == 0) {
    echo '<p>Такая загрузка не найдена. Повторите попытку или обратитесь к разработчику</p>' .
        '<a href="/lprices/">К началу загрузки прайса</a>';
    exit;
}
$sqlsup = ($rs->suppl == 2 ? '(id_sup=2 or id_sup=6)' : 'id_sup=' . $rs->suppl) .
    ($rs->tyres != $rs->wheels ? ($rs->tyres == 1 ? ' and tab1_id=1' : ' and tab1_id=2') : '') . ' ' . $rs->obnul;
$result = execute('UPDATE total_suppl LEFT JOIN total ON total_id=id_tov SET cnt_sup=0 WHERE ' . $sqlsup);
$currentStepMessage = 'Обнуление наличие у поставщика по фильтру ';
if ($result === false) {
    $str .= '<li class="error">Ошибка (' . $currentStepMessage . ') ' . dbLastErrorToString() . ';</li>';
} else {
    $str .= '<li>' . $currentStepMessage . ': ' . $result . ' позиций</li>';
}
$str .= UpdateSp1();