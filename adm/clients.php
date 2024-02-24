<?php
  if($grtp<>1)
  {
    Header("Location: /error/dostup/");
    exit;
  }
  $res=mysql_query("SELECT users.id as usid,nick, users.email AS umail,sumdost, opt_client.name AS opnm, manager, tel, adrdst, prim, groups.name as grnm,opt_client.email as opml,status FROM users LEFT JOIN opt_client ON users.id = usid LEFT JOIN groups ON groups.id = grid WHERE groups.tp =2 or groups.tp is null");
  $str="<div id=\"nomens\"><table><tr class=\"head\"><td class=\"login\">Логин</td><td class=\"email\">Email</td><td class=\"comp\">Компания</td><td class=\"manager\">Менеджер</td>
  <td class=\"comp\">Телефон</td><td class=\"email\">Email конт.</td><td class=\"adr\">Адрес</td><td class=\"dst\">Цена доставки</td><td class=\"group\">Группа</td><td class=\"status\">Статус</td><td class=\"edit\">Ред.</td></tr>";
  while($rs=mysql_fetch_object($res))
    $str.="<tr".($rs->status==0?" class=\"moder\"":"")."><td>".$rs->nick."</td><td>".$rs->umail."</td><td>".$rs->opnm."</td><td>".$rs->manager."</td><td>".$rs->tel."</td>
    <td>".$rs->opml."</td><td>".$rs->adrdst."</td><td>".$rs->sumdost."</td><td>".$rs->grnm."</td><td>".($rs->status==0?"модерация":"активен")."</td><td><a href=\"/addclient/edit/".$rs->usid."/\">Ред</a></td></tr>";
  $str.="</table></div>";
?>