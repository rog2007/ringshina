<?php
  $str = '';
  $orderId = $arg[0];

  if(isset($arg[1])){

    switch($arg[1]){

        case 'del':

             $selOrder = $dbcon->prepare("DELETE FROM order_move WHERE doc_id=:id");
             $selOrder->bindParam(':id', $orderId);
             $selOrder->execute();
             $selOrder = $dbcon->prepare("DELETE FROM order_doc WHERE big_id=:id");
             $selOrder->bindParam(':id', $orderId);
             $selOrder->execute();
             header('Location: ' . $_SERVER['HTTP_REFERER']);
        break;
    }
  }

  $selOrder = $dbcon->prepare("SELECT big_id, cust_name, cust_tel, cust_mail, inform, ord_time,
  ord_date, address, dost_cost, delivery.lable as dname, payment.lable as pname
  FROM order_doc left join delivery ON delivery.id = dost left join payment ON payment.id = opl
  WHERE big_id=:id limit 0, 1");
  $selOrder->bindParam(':id', $orderId);
  if ($selOrder->execute() && $selOrder->rowCount() > 0) {

    $str .= '<div id="order"><h1>Заказ - ' . $orderId . '</h1>';

    if ($objOrder = $selOrder->fetch(PDO::FETCH_OBJ)) {

      $str .= '<div class="order-info"><table>
      <tr>
        <td class="caption">Имя</td>
        <td class="value">' . $objOrder->cust_name . '</td>
      </tr>
      <tr>
        <td class="caption">Телефон</td>
        <td class="value">' . $objOrder->cust_tel . '</td>
      </tr>
      <tr>
        <td class="caption">Email</td>
        <td class="value">' . $objOrder->cust_mail . '</td>
      </tr>
      <!--tr>
        <td class="caption">Адрес доставки</td>
        <td class="value">' . $objOrder->address . '</td>
      </tr>
      <tr>
        <td class="caption">Доставка</td>
        <td class="value">' . $objOrder->dname . '(' . $objOrder->dost_cost . ' руб)</td>
      </tr>
      <tr>
        <td class="caption">Оплата</td>
        <td class="value">' . $objOrder->pname . '</td>
      </tr-->
      <tr>
        <td class="caption">Информация</td>
        <td class="value">' . $objOrder->inform . '</td>
      </tr>
      </table></div>';
    }
  }

  $selItems = $dbcon->prepare("SELECT id_name, tov_name, price, ord_cnt FROM order_move WHERE doc_id=:id order by tov_name");
  $selItems->bindParam(':id', $orderId);
  if ($selItems->execute() && $selItems->rowCount() > 0) {

    $str .= '<div class="order-items"><table><tr class="head"><td>Наименование</td>
    <td>Цена за шт.</td>
    <td>Количество</td>
    <td>Общая сумма</td></tr>';

    $sumCnt = 0;
    $sumPrice = 0;

    while ($objItem = $selItems->fetch(PDO::FETCH_OBJ)) {

      $str .= '
      <tr class="data">
        <td>' . $objItem->tov_name . '</td>
        <td>' . $objItem->price . '</td>
        <td>' . $objItem->ord_cnt . '</td>
        <td>' . $objItem->price * $objItem->ord_cnt . '</td>
      </tr>';

      $sumCnt += $objItem->ord_cnt;
      $sumPrice += ($objItem->price * $objItem->ord_cnt);
    }
    $str .= '<tr class="data">
        <td>Итого</td>
        <td></td>
        <td>' . $sumCnt . '</td>
        <td>' . $sumPrice . '</td>
      </tr></table></div>';
  }

  $str .= '</div>';

  mysql_query('update order_doc set status = 2 where big_id = ' . $orderId);

?>