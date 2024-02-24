<?php
if ($arg[0] == "error") {

    $content = '<div id="buy-error">';
    if ($arg[1] == 1) {
        $content .= "<h2>Ошибка при оформлении заказа</h2><div>В корзине нет ни одного товара. Заказ оформлен не был. <a href='/bask/'>В корзину</a></div>";
    }
    if ($arg[1] == 2) {
        $content .= "<h2>Ошибка при оформлении заказа</h2><div>Не указано контактное лицо. Заказ оформлен не был. <a href='/bask/'>В корзину</a></div>";
    }
    if ($arg[1] == 3) {
        $content .= "<h2>Ошибка при оформлении заказа</h2><div>Не указан телефон. Заказ оформлен не был. <a href='/bask/'>В корзину</a></div>";
    }
    $content .= '</div>';
    return;
}
if ($_POST["ajax"]) {

    header('Content-type: text/html; charset=windows-1251');
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    require("connect.php");
    require("cookies.php");
    require("func.php");
    $fio = $_POST['fio']; //iconv('UTF-8', 'windows-1251', $_POST['fio']);
    $tel = $_POST['tel']; // iconv('UTF-8', 'windows-1251', $_POST['tel']);
    $email = $_POST["e_mail"];//iconv('UTF-8', 'windows-1251', $_POST["e_mail"]);
    $info = $_POST['info'];//iconv('UTF-8', 'windows-1251', $_POST['info']);
} else {

    $fio = $_POST['fio'];
    $tel = $_POST['tel'];
    $email = trim($_POST["e_mail"]);
    $info = trim($_POST['info']);
    //$city = trim($_POST['city']);
    //$street = trim($_POST['street']);
    //$adr_dop = trim($_POST['adr_dop']);
    /*if(isset($_POST['dost']) && $_POST['dost']){

      $dostCon = $dbcon->prepare('select * from delivery where id=:id');
      $dostCon->bindParam(':id',$_POST['dost']);
      if ($dostCon->execute() && $dostCon->rowCount() > 0) {

        $dostObj = $dostCon->fetch(PDO::FETCH_OBJ);
      }
    }
    if(isset($_POST['opl']) && $_POST['opl']){

      $payCon = $dbcon->prepare('select * from payment where id=:id');
      $payCon->bindParam(':id',$_POST['opl']);
      if ($payCon->execute() && $payCon->rowCount() > 0) {

        $payObj = $payCon->fetch(PDO::FETCH_OBJ);
      }
    }*/
}
require("func_basket.php");
$error = 0;
$res = CurBasket();
$num = @mysql_num_rows($res);
if (!$num) {
    if ($_POST["ajax"]) {
        $str = "Ошибка|В корзине нет ни одного товара. Заказ оформлен не был.";
    } else {
        Header("Location: /buy/error/1.html");
    }
    Exit;
}
if (trim($fio) == "") {
    if ($_POST["ajax"]) {
        $str = "Ошибка|Не указано контактное лицо. Заказ оформлен не был.";
    } else {
        Header("Location: /buy/error/2.html");
    }
    Exit;
}
if (trim($tel) == "") {
    if ($_POST["ajax"]) {
        $str = "Ошибка|Не указан телефон. Заказ оформлен не был.";
    } else {
        Header("Location: /buy/error/3.html");
    }
    Exit;
}
$date = date("Y.m.d");

mysql_query("INSERT INTO order_doc (us_id, cust_name, cust_tel, cust_mail, inform,
    ord_date, ord_time, status)
    values ('$uid', '$fio', '$tel', '$email', '$info', '$date', '" . date("H:i:s") . "',1)");
$big_id = mysql_insert_id();

$cnt_tm = 0;
$total = 0;
$bResult = basket_count();
if ($bResult !== false) {
    $cnt_tm = $bResult->s_cnt;
    $total = $bResult->s_tot;
}

// составление и отправка писем
$body_2 = "<table class=\"tov\"><tr><td>Наименование товара</td><td>Цена (руб)</td><td>Кол-во (шт)</td><td>Всего (руб)</td></tr>";
for ($i = 0; $i < $num; $i++) {

    $curRowRes = CurBasketRow(mysql_result($res, $i, "order_tmp_id"), mysql_result($res, $i, "id_tov"));

    $bask = mysql_fetch_object($curRowRes);

    mysql_query("INSERT INTO order_move (doc_id, id_name, price,ord_cnt, tov_name)
        VALUES (" . $big_id . ", " . $bask->total_id . ", " . $bask->price . ",
        " . $bask->ord_cnt . ", '" . mysql_real_escape_string($bask->all_name) . "')");

    $body_2 .= "<tr><td>" . $bask->all_name . "</td><td>" . $bask->price . "</td>
        <td class=\"tov\">" . $bask->ord_cnt . "</td><td>" . $bask->all_cnt . "</td></tr>";
}

$body_2 .= "<tr><td>Итого</td><td></td><td class=\"tov\">" . $cnt_tm . "</td>
      <td>" . $total . "</td></tr></table>";
$message = "<html>
    <head>
        <title>Номер $big_id от " . normalize_mysqldate($date) . "</title>
        <style type=\"text/css\">
          H2{font-size: 14pt;text-align: center;font-family: Verdana, Arial, Helvetica, sans-serif}
          table{width: auto;padding: 0px;border: 0px}
          table.tov{border: 1 #000000 solid;border-collapse:collapse}
          table.tov td{border: 1 #000000 solid}
          td.tov{border: 1 #000000 solid;text-align: center}
          td{text-align: left;padding-left: 10px}
          td.inf{font-weight: bold}
          p{text-align: left}
        </style>
    </head>
    <body><h2>Номер $big_id от " . normalize_mysqldate($date) . "</h2>
    <p>Сведения о покупателе:</p>
    <table>
        <tr>
          <td>ФИО: </td>
          <td class=\"inf\">{$fio}</td>
        </tr>
        <tr>
          <td>Электронная почта: </td>
          <td><a href='mailto:{$email}'>{$email}</a></td>
        </tr>
        <tr>
          <td>Телефоны: </td>
          <td class=\"inf\">{$tel}</td>
        </tr>
        <tr>
          <td>Дополнительно:</td>
          <td class=\"inf\">{$info}</td>
        </tr>
      </table>
      <p>Содержание заказа:
      $body_2 </p>
      </body>
    </html>";

$message_1 = "<html>
    <head>
        <title>Заказ на сайте www.ringshina.ru</title>
        <style type=\"text/css\">
          H2{font-size: 14pt;text-align: center;font-family: Verdana, Arial, Helvetica, sans-serif}
          img{border:0}
          table{width: auto;padding: 0px;border: 0px}
          table.tov{border: 1 #000000 solid;border-collapse:collapse}
          table.tov td{border: 1 #000000 solid}
          td.tov{border: 1 #000000 solid;text-align: center}
          td{text-align: left;padding-left: 10px}
          td.inf{font-weight: bold}
          p{text-align: left}
          p.numb{text-align: center;font-weight: bold}
        </style>
    </head>
    <body><h2>Заказ на сайте www.ringshina.ru</h2>
    <p>Здравствуйте {$fio}!</p>
    <p>Ваш заказ поступил в интернет-магазин <a href=\"https://www.ringshina.ru\">www.ringshina.ru</a> Наши менеджеры обработают его в ближайшее время и свяжутся с Вами для уточнения
    деталей.</p>
    <p class=\"numb\">Номер $big_id от " . normalize_mysqldate($date) . "</p>
    $body_2
    <p></p>
    <p>Спасибо что выбрали нас. С уважением, администрация сайта <a href=\"https://www.ringshina.ru\">www.ringshina.ru</a>.
Если у Вас позникли какие либо вопросы или дополнения к Вашему заказу, свяжитесь
пожалуйста с нами по телефону (3812) 51-39-41, (3812) 51-39-44 либо дождитесь звонка
менеджера.</p>
    </body>
    </html>";
$headers = "Content-type: text/html; charset=utf-8 \r\n";
$headers .= "From: mail@ringshina.ru\r\n";
if ($email == "pismorogu@gmail.com") {
    if (mail("pismorogu@gmail.com", "Новый заказ с сайта www.ringshina.ru", $message, $headers)) {
        echo "11111111111111";
    } else {
        echo "2222222222222";
    }
}
mail("ring_omsk@mail.ru", "Новый заказ с сайта www.ringshina.ru", $message, $headers);
if (trim($email) != "") {
    mail($email, "Новый заказ на сайте www.ringshina.ru", $message_1, $headers);
}
if ($email != "pismorogu@gmail.com") {
    ClearBasket();
}
if ($_POST["ajax"]) {
    echo $str = "Сообщение|Заказ оформлен. Наши менеджеры в ближайшее время свяжутся с Вами.";
} else {
    $content = "<div id='buy'><div class='head'><h1>Заказ оформлен</h1></div>
    <div class='success'><p style=\"text-align:left;margin:10px 0\"><strong>Уважаемый, " . $_POST["fio"] .
        "!</strong></p><p style=\"text-align:left;margin:10px 0\">Ваш заказ номер " . $big_id . " от " . normalize_mysqldate($date) . " принят. Наш менеджер свяжется с Вами в ближайшее время для уточнения деталей.</p>
      <p style=\"text-align:left;margin:10px 0\"><strong>Спасибо за посещение нашего сайта!</strong></p><p style=\"text-align:right;margin:10px 0\"><a href=\"/\"><< вернуться на главную</a></p>
      </div></div>";
}

?>