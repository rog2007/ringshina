<?php
if (isset($_POST["ajax"]) && $_POST["ajax"]) {

    header('Content-type: text/html; charset=windows-1251');
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    require("connect.php");
    require("cookies.php");
    require("func.php");
    $arg[1] = $_POST["event"];
    $arg[0] = $_POST["nomen"];
}
require("func_basket.php");
if (isset($_POST["ajax"]) && $_POST["ajax"] && $arg[1] == "top") {

    $tcnt = 0;
    $tsum = 0;
    $bResult = basket_count();
    if($bResult !== false) {
        $tcnt = $bResult->s_cnt;
        $tsum = $bResult->s_tot;
    }
    echo $tsum . '|' . $tcnt;
    return;
}

if (isset($_POST["cnt"])) {
    $cn = $_POST["cnt"];
} else $cn = 4;
switch ($arg[1]) {

    case "add":
        add_good_bask($arg[0], $cn, $_POST["tov"]);
        if (!isset($_POST["ajax"]) || !$_POST["ajax"]) {
            if (isset($_POST['put-buy'])) {
                header('Location: /bask/');
            } else {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
        } else {
            echo "Информация|Товар добавлен в корзину";
        }
        break;
    case "delete":
        DelBasketPos($arg[0]);
        if (!isset($_POST["ajax"]) || !$_POST["ajax"]) {
            header('Location: /bask/');
        } else {
            $tcnt = 0;
            $bResult = basket_count();
            if($bResult !== false) {
                $tcnt = $bResult->s_cnt;
            }
            echo $tcnt;
            return;
        }
        break;
    case "update":
        if (!isset($_POST["ajax"]) || !$_POST["ajax"]) {
            foreach ($_POST as $K => $V)
                if (eregi("cnt_", $K)) {
                    UpdBasketPos(substr($K, 4), $V);
                }
            header('Location: /bask/');
        } else {
            UpdBasketPos($_POST["nomen"], $cn);
            $res = mysql_query("select tab1_id from total where total_id=" . $_POST["nomen"]);
            echo mysql_result($res, 0, "tab1_id");
            return;
        }
        break;
    case "clear":
        ClearBasket();
        if (!isset($_POST["ajax"]) || !$_POST["ajax"]) {
            header('Location: /bask/');
        }
        break;
}
$content .= '<div class="row"><ul class="breadcrumbs">
  <li><a href="/">Главная</a></li>
  <li class="current"><a href="/bask/">Корзина</a></li>
	</ul>
  <h2>Товары в Вашей корзине</h2>';
$res = CurBasket();
$num = mysql_num_rows($res);
if (!$num) {
    $content .= '<p class="empty-message">В корзине нет ни одного товара.</p>';
    if ($_POST["ajax"]) {
        echo $str;
    }
    return;
}
$content .= '<form method="post" id="cart-form" action="/bask/all/update/" name="tbl1">';
$row = 0;
$sum = 0;
$cnt = 0;
$h = 150;
$content .= '<table>
				  <thead>
					<tr>
					  <th width="200">Изображение</th>
					  <th>Товар</th>
					  <th width="100">Цена</th>
					  <th width="50"></th>
					</tr>
				  </thead>
				  <tbody>';
while ($curRows = mysql_fetch_object($res)) {

    $curRowRes = CurBasketRow($curRows->order_tmp_id, $curRows->id_tov);

    $bask = mysql_fetch_object($curRowRes);

    $link = '/card/' . $bask->tov . '/' . $bask->turl . '.html';
    $pic = ImageWork($bask->T4Pic, $bask->tab1_id, $bask->tab2_id, $bask->tab3_id,
        $bask->tab4_id, $bask->t2tr, $bask->T3Nm, $bask->T4Nm, $h);
    //$pic="/images/tovar/".($bask->tab1_id == 1?"tyres":"discs").$h."/".$pic;

    $content .= '<tr>
    <td><img src="' . $pic . '"></td>
	<td><h4>' . $bask->all_name . '</h4><span>Количество: ' . $bask->ord_cnt . '</span>
        <span>Цена: ' . $bask->price . '</span></td>
	<td><b>' . ($bask->ord_cnt * $bask->price) . ' руб.</b></td>
	<td><a href="/bask/' . $bask->total_id . '/delete/">Удалить</a></td>
	</tr>';  //  onclick="return DeleteNomen('.$bask->total_id.')" id="d_'.$bask->total_id.'"
    $sum_small += (int)$bask->all_cnt;
    $cnt += $bask->ord_cnt;
    $row++;
}

$content .= '</tbody></table></form>
<p><b>Общая стоимость (без доставки): <span id="allsum">' . $sum_small . '</span> Руб</b></p>';

$content .= '<h3>Оформление заказа:</h3>
    <form method="post" action="/buy/" name="ord">
      <div class="error-mes" id="checkout-error">Необходимо заполнить все обязательные поля</div>
      <fieldset>
				<legend>Заполните пожалуйста форму заказа</legend>

				<div class="row">
				  <div class="large-12 columns">
					<label>ФИО</label>
					<input type="text"  name="fio" placeholder="Введите имя и фамилию">
				  </div>
				</div>

				<div class="row">
				  <div class="large-4 columns">
					<label>Телефон</label>
					<input type="text" " name="tel" placeholder="Введите телефон">
				  </div>
				  <div class="large-4 columns">
					<label>E-mail</label>
					<input type="text"  name="e_mail"  placeholder="Введите email (опционально)">
				  </div>
				</div>
				<div class="row">
				  <div class="large-12 columns">
					<label>Примечание к заказу</label>
					<textarea placeholder="Введите дополнительную информацию (опционально)" name="info"></textarea>
				  </div>
				</div>
        <input class="button" value="Отправить" type="submit"/>
			  </fieldset>';
if ($_POST["ajax"]) {
    echo $content;
}