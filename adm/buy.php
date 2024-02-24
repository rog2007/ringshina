<?php
  function CurBasket(){global $uid;global $group; return mysql_query("select total_id,tab10_id,all_name,order_tmp_sd.cnt as ord_cnt,ROUND(priceb*(1+price".$group."/100)) as prss,ROUND(priceb*(1+price".$group."/100))*order_tmp_sd.cnt as all_cnt,tab1_id,tab3_id,tab2_id,tab4_id,id_sup,sid,us_id from order_tmp_sd left join total on total.total_id=order_tmp_sd.id_name left join total_suppl on total_id=id_tov AND sid = id_sup left join suppl on suppl.id=id_sup where us_id=".$uid);}
  function ClearBasket(){global $uid; return mysql_query("delete from order_tmp_sd where us_id=$uid");}
  $date=date("Y.m.d");
  $date_dost=$_POST["dty"].".".$_POST["dtm"].".".$_POST["dtd"];
  switch($_POST["tpds"])
  {
    case 1:
      mysql_query("insert into order_doc_sd (us_id,man_name,man_tel,man_email,inform,ord_date,dost_date,dost_type,state)
      values ($uid,'".$_POST["mfio"]."','".$_POST["mtel"]."','".$_POST["me_mail"]."','".$_POST["info"]."','".$date."','".$date_dost."',".$_POST["tpds"].",1)");
    break;
    case 2:
      mysql_query("insert into order_doc_sd (us_id,man_name,man_tel,man_email,inform,ord_date,dost_date,dost_type,cust_name,cust_tel,cust_mail,state)
      values ($uid,'".$_POST["mfio"]."','".$_POST["mtel"]."','".$_POST["me_mail"]."','".$_POST["info"]."','".$date."','".$date_dost."',".$_POST["tpds"].",'".$_POST["cfio"]."','".$_POST["ctel"]."','".$_POST["ce_mail"]."',1)");
    break;
    case 3:
      mysql_query("insert into order_doc_sd (us_id,man_name,man_tel,man_email,inform,ord_date,dost_date,dost_type,man_adr,dost_pr,state)
      values ($uid,'".$_POST["mfio"]."','".$_POST["mtel"]."','".$_POST["me_mail"]."','".$_POST["info"]."','".$date."','".$date_dost."',".$_POST["tpds"].",'".$_POST["madr"]."',".$_POST["mk2"].",1)");
    break;
    case 4:
     mysql_query("insert into order_doc_sd (us_id,man_name,man_tel,man_email,inform,ord_date,dost_date,dost_type,cust_name,cust_tel,cust_mail,cust_adr,dost_pr,state)
     values ($uid,'".$_POST["mfio"]."','".$_POST["mtel"]."','".$_POST["me_mail"]."','".$_POST["info"]."','".$date."','".$date_dost."',".$_POST["tpds"].",'".$_POST["cfio"]."','".$_POST["ctel"]."','".$_POST["ce_mail"]."','".$_POST["cadr"]."',".$_POST["mk2"].",1)");
    break;
  }
  $big_id=mysql_insert_id();
  mysql_query("insert into order_move_sd (doc_id,id_name,price,ord_cnt,idsp)
    (select ".$big_id.",order_tmp_sd.id_name,ROUND(priceb*(1+price".$group."/100)),order_tmp_sd.cnt,sid from order_tmp_sd
    left join total_suppl on order_tmp_sd.id_name=id_tov and sid=id_sup where us_id=$uid)");
  $doc=mysql_query("select cust_name,cust_tel,cust_mail,inform,dost_date,ord_date,dost_pr,cust_adr,man_name,man_tel,man_email,man_adr,dost_type,dost.nm as dnm
  from order_doc_sd left join dost on dost.id=dost_type where big_id=".$big_id);
  if($rs=mysql_fetch_object($doc))
  {
    $message="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
    <html xmlns=\"http://www.w3.org/1999/xhtml\"><head><title>Новый оптовый заказ № ".$big_id." от ".$date."</title>
    <style type=\"text/css\">H1{font:bold 14px Arial;text-align: left} H2{font:bold 12px Arial;text-align: left} table{border-collapse:collapse} .block table td{border:1px solid #000066;padding:5px;width:150px}
    </style></head><body><h1>Новый оптовый заказ № ".$big_id." от ".$date."</h1>
    <div class=\"block\"><h2>Заказ</h2><table>
    <tr><td>Тип доставки</td><td>".$rs->dnm."</td></tr>
    <tr><td>Дата доставки (самовывоза)</td><td>".$rs->dost_date."</td></tr>".
    ($rs->cust_name?"<tr><td>Имя клиента</td><td>".$rs->cust_name."</td></tr>":"").
    ($rs->cust_tel?"<tr><td>Телефон клиента</td><td>".$rs->cust_tel."</td></tr>":"").
    ($rs->cust_mail?"<tr><td>Email клинта</td><td>".$rs->cust_mail."</td></tr>":"").
    ($rs->cust_adr?"<tr><td>Адрес клиента</td><td>".$rs->cust_adr."</td></tr>":"").
    ($rs->man_name?"<tr><td>Имя менеджера</td><td>".$rs->man_name."</td></tr>":"").
    ($rs->man_tel?"<tr><td>Телефон менеджера</td><td>".$rs->man_tel."</td></tr>":"").
    ($rs->man_email?"<tr><td>Телефон email</td><td>".$rs->man_email."</td></tr>":"").
    ($rs->man_adr?"<tr><td>Адрес контрагента для доставки</td><td>".$rs->man_adr."</td></tr>":"").
    ($rs->dost_pr?"<tr><td>Стоимость доставки</td><td>".$rs->dost_pr."</td></tr>":"").
    "</table></div><div class=\"block\"><h2>Содержимое заказа</h2><table>";
    $nom=mysql_query("select all_name,order_move_sd.price,ord_cnt,suppl.name as spnm from order_move_sd left join total on total_id=id_name left join suppl on suppl.id=idsp where doc_id=".$big_id);
    $message.="<tr><td>Наименование</td><td>Кол-во</td><td>Цена</td><td>Всего</td><td>Склад</td></tr>";
    $all_cnt=0;
    $all_sum=0;
    while($nomen=mysql_fetch_object($nom))
    {
      $message.="<tr><td>".$nomen->all_name."</td><td>".$nomen->ord_cnt."</td><td>".$nomen->price."</td><td>".$nomen->ord_cnt*$nomen->price."</td><td>".$nomen->spnm."</td></tr>";
      $all_cnt+=$nomen->ord_cnt;
      $all_sum+=$nomen->ord_cnt*$nomen->price;
    }
    $message.="<tr><td>Всего:</td><td>".$all_cnt."</td><td></td><td>".$all_sum."</td><td></td></tr></table>";
  }
  $message.="</body></html>";
  if (!$num)
  {
    $str="Карзина пуста. Заказ оформлен не был.";
  }
  $headers  = "Content-type: text/html; charset=windows-1251 \r\n";
  $headers .= "From: zakaz@shinadisc.ru\r\n";
  //$message=convert_cyr_string($message,"windows-1251","koi-8");
  mail("dims@am-shina.ru","Оптовый заказ",$message,$headers);
  //mail("rogov@am-shina.ru","Оптовый заказ",$message,$headers);
  ClearBasket();
$str="<p align=center>&nbsp</p>\n
<p align=center> <font color=#FF0000><strong>Уважаемый, $fio</strong></font></p>\n
<p align=center> <strong>Ваш заказ принят</strong></p>\n
<p align=center> <strong>Наш менеджер свяжется с Вами в ближайшее время для уточнения деталей</strong></p>\n
<p align=center> <strong>Спасибо за посещение нашего сайта!</strong></</p>\n";
?>