<?php
  function AddInBasket($id_good,$tov_cnt,$skld,$uid1){
    $res=mysql_query("select order_tmp_id from order_tmp_sd where us_id=$uid1 and id_name=$id_good and sid=$skld and state=1");
    $num=@mysql_num_rows($res);
    if ($num>0) return mysql_query("update order_tmp_sd set cnt=cnt+$tov_cnt where us_id=$uid1 and id_name=$id_good and sid=$skld");
    else return mysql_query("insert into order_tmp_sd (cnt,us_id,id_name,sid,state) values($tov_cnt,$uid1,$id_good,$skld,1)");}
  function CurBasket(){global $uid;global $group; return mysql_query("select total_id,tab10_id,all_name,order_tmp_sd.cnt as ord_cnt,ROUND(priceb*(1+price".$group."/100)) as prss,ROUND(priceb*(1+price".$group."/100))*order_tmp_sd.cnt as all_cnt,tab1_id,tab3_id,tab2_id,tab4_id,id_sup,sid,us_id from order_tmp_sd left join total on total.total_id=order_tmp_sd.id_name left join total_suppl on total_id=id_tov AND sid = id_sup left join suppl on suppl.id=id_sup where us_id=".$uid);}
  function ClearBasket(){global $uid; return mysql_query("delete from order_tmp_sd where us_id=".$uid);}
  function DelBasketPos($id_good,$skld,$uid1){mysql_query("delete from order_tmp_sd where us_id=".$uid1." and id_name=".$id_good." and sid=".$skld);}
  function UpdBasketPos($id_g,$cnt,$sid){global $uid;mysql_query("update order_tmp_sd set cnt=$cnt where us_id=$uid and id_name=$id_g and sid=$sid");}
$tit="Ваша корзина";
$desk="Ваша корзина.";
$kw="ваша корзина";
if ($_POST["ajax"])
{
  header('Content-type: text/html; charset=windows-1251');
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  require ("connect.php");
  $arg[0]=$_POST["nomen"];
  $arg[1]=$_POST["sup"];
  $arg[2]=$_POST["event"];
  $uid=$_POST["sid"];
  //$arg[4]=$_POST["price"];
}
//echo "select total_id,tab10_id,all_name,order_tmp_sd.cnt as ord_cnt,ROUND(priceb*(1+price".$group."/100)) as prss,ROUND(priceb*(1+price".$group."/100))*order_tmp_sd.cnt as all_cnt,tab1_id,tab3_id,tab2_id,tab4_id,id_sup,sid,us_id from order_tmp_sd left join total on total.total_id=order_tmp_sd.id_name left join total_suppl on total_id=id_tov AND sid = id_sup left join suppl on suppl.id=id_sup where us_id=".$uid;
if (isset($_POST["cnt"])) $cn=$_POST["cnt"];
else $cn=4;
switch ($arg[2])
{
  case "dost":
    $res=mysql_query("select desk from dost where id=".$_POST["dost1"]);
    if ($rs=mysql_fetch_object($res))
      echo $rs->desk;
    return;
  break;
  case "add":
    $rs=AddInBasket($arg[0],$cn,$arg[1],$uid);
    if (!$_POST["ajax"]) header('Location: /bask/');
    else {echo $rs; return;}
  break;
  case "delete":
    DelBasketPos($arg[0],$arg[1],$uid);
    if (!$_POST["ajax"]) header('Location: /bask/');
  break;
  case "update":
    if ($_POST["ajax"])
    {
      UpdBasketPos($arg[0],$cn,$arg[1]);
      $res=mysql_query("select tab1_id from total where total_id=".$arg[0]);
      echo @mysql_result($res,0,"tab1_id");
      return;
    }
  break;
  case "clear":
    ClearBasket();
    if (!$_POST["ajax"])
      header('Location: /bask/');
  break;
}
$res=CurBasket();
$num = @mysql_num_rows($res);
if($num)
{
  $str.="<div id=\"bask\"><h1>Ваша корзина</h1><form method=\"post\" action='/bask/0/update/' name=\"fr_bas\"><table><tr class=\"hd\"><td class=\"nm\">Наименование</td><td>Цена</td><td>Количество</td><td>Сумма</td><td>Удалить</td></tr>";
  while($tov=mysql_fetch_object($res))
  {
    $str.="<tr><td class=\"nm\">".$tov->all_name."</td><td>".$tov->prss."</td><td><input type=\"hidden\" name=\"sid_".$tov->id_sup."\" value=\"".$tov->id_sup."\" /><input type=\"text\" name=\"cnt_".$tov->total_id."\" value=\"".$tov->ord_cnt."\" onkeypress='if(event.keyCode==13) return UpdateNomen(".$tov->total_id.",this,".$tov->id_sup.",".$uid.");' onfocus='return focs(this);' onchange='return UpdateNomen(".$tov->total_id.",this,".$tov->id_sup.",".$uid.");' /></td>
    <td id=\"sum".$tov->total_id."\">".$tov->all_cnt."</td><td><a href=\"/bask/".$tov->total_id."/".$tov->id_sup."/delete/\" onclick=\"return DeleteNomen(".$tov->total_id.",".$tov->id_sup.",".$uid.");\">удалить</a></td></tr>";
    $sum_small+=(int)$tov->all_cnt;
    $cnt+=$tov->ord_cnt;
  }
  if($dstpr==1) $mkad=0;
  else $mkad=500;
  $str.="<tr class=\"itog\"><td colspan=\"2\" style=\"text-align:left\" class=\"zg\">Всего (товар)</td><td id=\"allcount\">".$cnt."</td><td id=\"allsum\">".$sum_small."</td><td><a href=\"/bask/0/0/clear/\" onclick=\"return ClearNomen()\" onfocus=\"blur()\" class=\"clear\">Очистить</a></td></tr>
  <tr class=\"hd\"><td class=\"nm\">Доставка</td><td>МКАД</td><td colspan=\"2\">За МКАД</td><td>Итого</td></tr>
  <tr><td class=\"nm\"><select id=\"tdost\" onchange='return dosttp();'>";
  $dst=mysql_query("select id,nm,desk from dost order by id");
  while($ds=mysql_fetch_object($dst))
  {
    if($ds->id==$dstpr) $dstop=$ds->desk;
    $str.="<option value=\"".$ds->id."\"".($ds->id==$dstpr?" selected=\"selected\"":"").">".$ds->nm."</option>";
  }
  $day=date("d");
  $mon=date("m");
  $year=date("Y");
  $str.="</select><div class=\"opdst\" id=\"od\">".$dstop."</div></td><td id=\"mkad\">".$mkad."</td><td colspan=\"2\"><input type=\"text\" value=\"0\"".($dstpr<3?" disabled=\"disabled\"":"")." onkeypress='if(event.keyCode==13) return dost(1);' onfocus='return focs(this);' id=\"km\" onchange='return dost();'/>км = <span id=\"pr_dost\">0</span></td><td id=\"itogd\" style=\"font-weight:bold\">".$mkad."</td></tr>
  <tr class=\"hd\"><td colspan=\"4\" style=\"text-align:left\">Всего (товар+доставка)</td><td id=\"itog\">".($sum_small+$mkad)."</td></tr></table></form></div>
  <div id=\"resp\"><form method=\"post\" action='/buy/' name=\"ord\">
  <h2>Оформление заказа</h2><table><tr><td class=\"nm\">Дата доставки (самовывоза)</td><td class=\"calend\" style=\"width:210px\"><select name=\"dtd\">"; //<a href=\"\" onclick=\"return PickDisplayDay(this)\"></a>
  for($i=1;$i<=$monfs[(int)$mon][2];$i++)
    $str.="<option value=\"".$i."\"".($i==(int)$day?" selected=\"selected\"":"").">".$i."</option>";
  $str.="</select><select name=\"dtm\" class=\"monf\">";
  for($i=1;$i<=12;$i++)
    $str.="<option value=\"".$i."\"".($i==(int)$mon?" selected=\"selected\"":"").">".$monfs[$i][1]."</option>";
  $str.="</select><input type=\"text\" value=\"".$year."\" name=\"dty\"/></td></tr>";
  $str.="<tr><td colspan=\"2\" class=\"hd\">Данные о клиенте</td></tr><tr><td class=\"nm\">Имя клиента <span>*</span></td><td class=\"contrl\"><input type=\"text\" name=\"cfio\" ".($dstpr==1 || $dstpr==3?" disabled=\"disabled\"":"")." value=\"\" /></td></tr>
    <tr><td class=\"nm\">Телефон клиента <span>*</span></td><td class=\"contrl\"><input type=\"text\" name=\"ctel\"".($dstpr==1 || $dstpr==3?" disabled=\"disabled\"":"")." value=\"\" /></td></tr>
    <tr><td class=\"nm\">E-mail клиента</td><td class=\"contrl\"><input type=\"text\" name=\"ce_mail\"".($dstpr==1 || $dstpr==3?" disabled=\"disabled\"":"")." value=\"\" /></td></tr>";
  $str.="<tr><td class=\"nm\">Адрес доставки клиент<span>*</span></td><td class=\"contrl\"><input type=\"text\" name=\"cadr\"".($dstpr!=4?" disabled=\"disabled\"":"")." value=\"\" /></td></tr>";
  $str.="<tr><td colspan=\"2\" class=\"hd\">Данные о партнере</td></tr><tr><td class=\"nm\">Менеджер <span>*</span></td><td class=\"contrl\"><input type=\"text\" name=\"mfio\" value=\"".$manager."\" /></td></tr>
    <tr><td class=\"nm\">Телефон <span>*</span></td><td class=\"contrl\"><input type=\"text\" name=\"mtel\" value=\"".$tel."\" /></td></tr>
    <tr><td class=\"nm\">E-mail</td><td class=\"contrl\"><input type=\"text\" name=\"me_mail\" value=\"".$kmail."\" /></td></tr>";
  $str.="<tr><td class=\"nm\">Адрес доставки<span>*</span></td><td class=\"contrl\"><input type=\"text\" name=\"madr\"".($dstpr!=3?" disabled=\"disabled\"":"")." value=\"".$adrdst."\" /></td></tr>";
  $str.="<tr><td class=\"nm\">Комментарий</td><td class=\"contrl\"><textarea name=\"info\"></textarea></td></tr>
    <tr><td class=\"button\" colspan=\"2\"><input type=\"hidden\" value=\"0\" id=\"mk2\" name=\"mk2\"/><input type=\"hidden\" value=\"".$dstpr."\" id=\"tpds\" name=\"tpds\"/><input type=\"submit\" name=\"buy\" value=\"Отправить\"/></td></tr></table></form>";
  $str.="</div>";
}
else
  $str.="<div class=\"b_empt\">Ваша корзина пуста</div>";
if ($_POST["ajax"]) echo $str;
?>