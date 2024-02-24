<?php
  function generate_password($number)
  {$arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','r','s','t','u','v','x','y','z','A','B','C','D','E','F',
   'G','H','I','J','K','L','M','N','O','P','R','S','T','U','V','X','Y','Z','1','2','3','4','5','6','7','8','9','0');
   $pass = "";
   for($i = 0; $i < $number; $i++){ $index = rand(0, count($arr) - 1); $pass .= $arr[$index];}
   return $pass;}
  if(isset($_POST["add"]))
  {
    if(trim($_POST["nick"])=="") $err[1]=1;
    $nick = $_POST['nick'];
    $tmp=mysql_query("SELECT * FROM users WHERE nick='".strtolower($nick)."' and id<>".$arg[1]);
    if(mysql_num_rows($tmp)>0)  $err[2]=1;
    if(checkmail($_POST["email"])==-1) $err[3]=1;
    $email=$_POST['email'];
    $tmp=mysql_query("SELECT * FROM users WHERE email='".strtolower($email)."' and grid=".$_POST["gr"]);
    if(mysql_num_rows($tmp)>0)  $err[4]=1;
    if(checkmail($_POST["kemail"])==-1) $err[5]=1;
    $kemail=$_POST['kemail'];
    if(trim($_POST["comp"])=="") $err[6]=1;
    $comp = $_POST['comp'];
    if(trim($_POST["manager"])=="") $err[7]=1;
    $manager = $_POST['manager'];
    if(trim($_POST["tel"])=="") $err[8]=1;
    $tel = $_POST['tel'];
    if(trim($_POST["adres"])=="") $err[9]=1;
    $adres = $_POST['adres'];
    if(trim($_POST["group"])==0) $err[10]=1;
    $groupka_=$_POST["group"];
    if(trim($_POST["prdost"])==0) $err[11]=1;
    $prdost_=$_POST["prdost"];
    if(trim($_POST["sumdost"])=="") $err[12]=1;
    $sumdost = $_POST['sumdost'];
    $stat=$_POST["stat"];
    $dopinfo = $_POST["dopinfo"];
    if(count($err)==0)
    {
      if($arg[0]=="edit")
      {
        $res=mysql_query("select nick,status from users where users.id=".$arg[1]);
        $rs=mysql_fetch_object($res);
        $st_old=$rs->status;
        mysql_query("update users set nick='".$nick."',email='".$email."',status=".$stat.",grid=".$groupka_." where id=".$arg[1]);
        mysql_query("update opt_client set name='".$comp."',manager='".$manager."',tel='".$tel."',adrdst='".$adres."',dstpr=".$prdost_.",sumdost='".$sumdost."',email='".$kemail."' where usid=".$arg[1]);
        if($st_old!=$stat)
        {
          $headers  = "Content-type: text/html; charset=windows-1251 \r\n";
          $headers .= "From: robot@shinadisc.ru\r\n";
          $message ="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
          <html xmlns=\"http://www.w3.org/1999/xhtml\"><head><title>Изменение статуса пользователя на www.shinadisc.ru</title>
          <style type=\"text/css\">H1{font:bold 14px Arial;text-align: left} H2{font:bold 12px Arial;text-align: left} table{border-collapse:collapse} .block table td{border:1px solid #000066;padding:5px;width:150px}
          </style></head><body><h1>Изменение доступа</h1>
          <p>".($stat==1?"Вам открыт доступ к наличию на складе.":"Вам закрыт доступ к наличию на складе")."</p></body></html>";
          mail("dims@am-shina.ru","Доступ к сайту www.shinadisc.ru",$message,$headers);
          mail($email,"Доступ к сайту www.shinadisc.ru",$message,$headers);
        }
        header("Location: /addclient/edit/".$arg[1]."/");
        exit;
      }
      if($arg[0]=="add")
      {
        $new_pas=generate_password(8);
        $uniq_id = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].mktime());
        if(mysql_query("INSERT INTO users (password,email,nick,uniq_id,status,date,last_date,grid) VALUES('".
        md5($new_pas)."','".$email."','".$nick."','".$uniq_id."',".$stat.",'".date("dmY")."','".date("dmY")."',".$groupka_.")"))
        {
          $big_id=mysql_insert_id();
          mysql_query("INSERT INTO opt_client (name,manager,tel,adrdst,dstpr,prim,usid,sumdost,email) VALUES('".
          $comp."','".$manager."','".$tel."','".$adres."',".$prdost_.",'".$dopinfo."',".$big_id.",'".$sumdost."','".$kemail."')");
          $headers  = "Content-type: text/html; charset=windows-1251 \r\n";
          $headers .= "From: robot@shinadisc.ru\r\n";
          $res=mysql_query("select nick,users.email as umail,name,manager,tel,adrdst,prim,status from users left join opt_client on users.id=usid where users.id=".$big_id);
          $rs=mysql_fetch_object($res);
          $message ="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
          <html xmlns=\"http://www.w3.org/1999/xhtml\"><head><title>Данные по регистрации на сайте www.shinadisc.ru</title>
          <style type=\"text/css\">H1{font:bold 14px Arial;text-align: left} H2{font:bold 12px Arial;text-align: left} table{border-collapse:collapse} .block table td{border:1px solid #000066;padding:5px;width:150px}
          </style></head><body><h1>Регистрационные данные</h1>
          <div class=\"block\"><h2>Регистрационные данные</h2><table><tr><td>Логин (для входа)</td><td>".$rs->nick."</td></tr><tr><td>Пароль</td><td>".$new_pas."</td></tr>
          <tr><td>E-mail</td><td>".$email."</td></tr><tr><td>Компания</td><td>".$rs->name."</td></tr>
          <tr><td>Контактное лицо</td><td>".$rs->manager."</td></tr><tr><td>Контактный телефон</td><td>".$rs->tel."</td></tr>
          <tr><td>Фактический адрес</td><td>".$rs->adrdst."</td></tr><tr><td>Примечания</td><td>".$rs->prim."</td></tr></table></div>
          <p>".($rs->status==1?"Вы зарегестрированны на портале ShinaDisc.ru. Вам открыт доступ к наличию на складе.":"")."</p></body></html>";
          mail("dims@am-shina.ru","Доступ к сайту www.shinadisc.ru",$message,$headers);
          mail($email,"Доступ к сайту www.shinadisc.ru",$message,$headers);
          header("Location: /addclient/edit/".$big_id."/");
          exit;
        }
      }
    }
  }
  /*if(isset($_POST["add"]) && count($err)==0)
  {
    $uniq_id = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].mktime());
    $pass = $_POST['password'];
    if(mysql_query("INSERT INTO users (password,email,nick,uniq_id,status,date,last_date,grid) VALUES('".
    md5($pass)."','".$email."','".$nick."','".$uniq_id."',1,'".date("dmY")."','".date("dmY")."',0)"))
    {
      $big_id=mysql_insert_id();
      mysql_query("INSERT INTO opt_client (name,manager,tel,adrdst,prim,usid) VALUES('".$comp."','".$manager."','".$tel."','".$adres."','".$dopinfo."',".$big_id.")");
      $headers  = "Content-type: text/html; charset=windows-1251 \r\n";
      $headers .= "From: robot@shinadisc.ru\r\n";
      $res=mysql_query("select nick,users.email as umail,name,manager,tel,adrdst,prim from users left join opt_client on users.id=usid where users.id=".$big_id);
      $rs=mysql_fetch_object($res);
      $message ="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
      <html xmlns=\"http://www.w3.org/1999/xhtml\"><head><title>Данные по регистрации на сайте www.shinadisc.ru</title>
      <style type=\"text/css\">H1{font:bold 14px Arial;text-align: left} H2{font:bold 12px Arial;text-align: left} table{border-collapse:collapse} .block table td{border:1px solid #000066;padding:5px;width:150px}
      </style></head><body><h1>Регистрационные данные</h1>
      <div class=\"block\"><h2>Регистрационные данные</h2><table><tr><td>Логин (для входа)</td><td>".$rs->nick."</td></tr>
      <tr><td>Пароль</td><td>".$pass."</td></tr>
      <tr><td>E-mail</td><td>".$email."</td></tr><tr><td>Компания</td><td>".$rs->name."</td></tr>
      <tr><td>Контактное лицо</td><td>".$rs->manager."</td></tr><tr><td>Контактный телефон</td><td>".$rs->tel."</td></tr>
      <tr><td>Фактический адрес</td><td>".$rs->adrdst."</td></tr><tr><td>Примечания</td><td>".$rs->prim."</td></tr></table></div>
      <p>В ближайшее время с Вами свяжется оптовый менеджер обсудит условия сотрудничества и откроет Вам доступ к сайту.</p></body></html>";
      mail("dims@am-shina.ru","Доступ к сайту www.shinadisc.ru",$message,$headers);
      mail($email,"Доступ к сайту www.shinadisc.ru",$message,$headers);
      header("Location: /registration/ok.html");
    }
  }*/
  if($arg[0]=="edit")
  {
    $res=mysql_query("SELECT nick, users.email AS usml,`status`,grid, name, manager, tel, adrdst,prim, dstpr, sumdost, opt_client.email AS opml
    FROM users LEFT JOIN opt_client ON users.id = usid WHERE users.id =".$arg[1]);
    if($rscl=mysql_fetch_object($res))
    {
      $str.='<form name="addclient" action="/addclient/'.$arg[0].'/'.$arg[1].'/" method="post"><div class="log"><p>Редактирование данных о клиенте</p><br/>';
      if(count($err)>0)
        $str.="<p>Не верно введены данные о клиенте. Необходимо исправить данные в полях подсвеченных красным.</p><br/>";
      $str.='<table><tr><td class="name"'.($err[1]==1 || $err[2]==1?" style=\"color:#f00\"":"").'>Логин <span>*</span></td><td><input name="nick" type="text" value="'.(isset($_POST["add"])?$_POST["nick"]:$rscl->nick).'" class="text"></td></tr>
      <tr><td class="name"'.($err[3]==1 || $err[4]==1?" style=\"color:#f00\"":"").'>Email (регистрация)<span>*</span></td><td><input name="email" type="text" value="'.(isset($_POST["add"])?$_POST["email"]:$rscl->usml).'" class="text"></td></tr>
      <tr><td class="name"'.($err[6]==1?" style=\"color:#f00\"":"").'>Компания <span>*</span></td><td><input name="comp" type="text" value="'.(isset($_POST["add"])?$_POST["comp"]:$rscl->name).'" class="text"></td></tr>
      <tr><td class="name"'.($err[7]==1?" style=\"color:#f00\"":"").'>Контактное лицо <span>*</span></td><td><input name="manager" type="text" value="'.(isset($_POST["add"])?$_POST["manager"]:$rscl->manager).'" class="text"></td></tr>
      <tr><td class="name"'.($err[8]==1?" style=\"color:#f00\"":"").'>Контактный телефон <span>*</span></td><td><input name="tel" type="text" value="'.(isset($_POST["add"])?$_POST["tel"]:$rscl->tel).'" class="text"></td></tr>
      <tr><td class="name"'.($err[5]==1?" style=\"color:#f00\"":"").'>Контактный email <span>*</span></td><td><input name="kemail" type="text" value="'.(isset($_POST["add"])?$_POST["kemail"]:$rscl->opml).'" class="text"></td></tr>
      <tr><td class="name"'.($err[9]==1?" style=\"color:#f00\"":"").'>Фактический адрес <span>*</span></td><td><textarea name="adres">'.(isset($_POST["add"])?$_POST["adres"]:$rscl->adrdst).'</textarea></td></tr>
      <tr><td class="name">Дополнительная информация</td><td><textarea name="dopinfo">'.(isset($_POST["add"])?$_POST["dopinfo"]:$rscl->prim).'</textarea></td></tr>
      <tr><td class="name"'.($err[10]==1?" style=\"color:#f00\"":"").'>Тип контрагента <span>*</span></td><td><select name="group">
      <option value="0"'.(isset($_POST["add"])?($_POST["group"]==0?" selected=\"selected\"":""):($rscl->grid==0?" selected=\"selected\"":"")).'>не указан</option>';
      $resgr=mysql_query("select id,name from groups where tp=2");
      while($rgr=mysql_fetch_object($resgr))
        $str.="<option value=\"".$rgr->id."\"".(isset($_POST["add"])?($rgr->id==$_POST["group"]?" selected=\"selected\"":""):($rgr->id==$rscl->grid?" selected=\"selected\"":"")).">".$rgr->name."</option>";
      $str.='</select></td></tr>
      <tr><td class="name"'.($err[11]==1?" style=\"color:#f00\"":"").'>Приорететная доставка <span>*</span></td><td><select name="prdost">
      <option value="0"'.(isset($_POST["add"])?($_POST["prdost"]==0?" selected=\"selected\"":""):($rscl->dstpr==0?" selected=\"selected\"":"")).'>не указан</option>';
      $dst=mysql_query("select id,nm,desk from dost order by id");
      while($ds=mysql_fetch_object($dst))
        $str.="<option value=\"".$ds->id."\"".(isset($_POST["add"])?($_POST["prdost"]==$ds->id?" selected=\"selected\"":""):($ds->id==$rscl->dstpr?" selected=\"selected\"":"")).">".$ds->nm."</option>";
      $str.='</select></td></tr>
      <tr><td class="name"'.($err[12]==1?" style=\"color:#f00\"":"").'>Сумма доставки <span>*</span></td><td><input name="sumdost" type="text" value="'.(isset($_POST["add"])?$_POST["sumdost"]:$rscl->sumdost).'" class="text"></td></tr>
      <tr><td class="name">Статус <span>*</span></td><td><select name="stat"><option value="0"'.(isset($_POST["add"])?($_POST["stat"]==0?" selected=\"selected\"":""):($rscl->status==0?" selected=\"selected\"":"")).'>модерация</option>
      <option value="1"'.(isset($_POST["add"])?($_POST["stat"]==1?" selected=\"selected\"":""):($rscl->status==1?" selected=\"selected\"":"")).'>включен</option></select></td></tr>
      <tr><td colspan="2"><p><span style="color:#f00">*</span> - поля обязательные для заполнения</p></td></tr>
      <tr><td colspan="2" class="butt"><input class="but_1" type="submit" name="add" value="Сохранить"></td></tr></table></div></form>';
    }
  }
  if($arg[0]=="add")
  {
    $str.='<form name="addclient" action="/addclient/'.$arg[0].'/" method="post"><div class="log"><p>Добавление клиента</p><br/>';
    if(count($err)>0)
      $str.="<p>Не верно введены данные о клиенте. Необходимо исправить данные в полях подсвеченных красным.</p><br/>";
    $str.='<table><tr><td class="name"'.($err[1]==1 || $err[2]==1?" style=\"color:#f00\"":"").'>Логин <span>*</span></td><td><input name="nick" type="text" value="'.$_POST["nick"].'" class="text"></td></tr>
      <tr><td class="name"'.($err[3]==1 || $err[4]==1?" style=\"color:#f00\"":"").'>Email (регистрация)<span>*</span></td><td><input name="email" type="text" value="'.$_POST["email"].'" class="text"></td></tr>
      <tr><td class="name"'.($err[6]==1?" style=\"color:#f00\"":"").'>Компания <span>*</span></td><td><input name="comp" type="text" value="'.$_POST["comp"].'" class="text"></td></tr>
      <tr><td class="name"'.($err[7]==1?" style=\"color:#f00\"":"").'>Контактное лицо <span>*</span></td><td><input name="manager" type="text" value="'.$_POST["manager"].'" class="text"></td></tr>
      <tr><td class="name"'.($err[8]==1?" style=\"color:#f00\"":"").'>Контактный телефон <span>*</span></td><td><input name="tel" type="text" value="'.$_POST["tel"].'" class="text"></td></tr>
      <tr><td class="name"'.($err[5]==1?" style=\"color:#f00\"":"").'>Контактный email <span>*</span></td><td><input name="kemail" type="text" value="'.$_POST["kemail"].'" class="text"></td></tr>
      <tr><td class="name"'.($err[9]==1?" style=\"color:#f00\"":"").'>Фактический адрес <span>*</span></td><td><textarea name="adres">'.$_POST["adres"].'</textarea></td></tr>
      <tr><td class="name">Дополнительная информация</td><td><textarea name="dopinfo">'.$_POST["dopinfo"].'</textarea></td></tr>
      <tr><td class="name"'.($err[10]==1?" style=\"color:#f00\"":"").'>Тип контрагента <span>*</span></td><td><select name="group">
      <option value="0"'.($_POST["group"]==0?" selected=\"selected\"":"").'>не указан</option>';
    $resgr=mysql_query("select id,name from groups where tp=2");
    while($rgr=mysql_fetch_object($resgr))
      $str.="<option value=\"".$rgr->id."\"".($rgr->id==$_POST["group"]?" selected=\"selected\"":"").">".$rgr->name."</option>";
    $str.='</select></td></tr>
      <tr><td class="name"'.($err[11]==1?" style=\"color:#f00\"":"").'>Приорететная доставка <span>*</span></td><td><select name="prdost">
      <option value="0"'.($_POST["prdost"]==0?" selected=\"selected\"":"").'>не указан</option>';
    $dst=mysql_query("select id,nm,desk from dost order by id");
    while($ds=mysql_fetch_object($dst))
      $str.="<option value=\"".$ds->id."\"".($ds->id==$_POST["prdost"]?" selected=\"selected\"":"").">".$ds->nm."</option>";
    $str.='</select></td></tr>
      <tr><td class="name"'.($err[12]==1?" style=\"color:#f00\"":"").'>Сумма доставки <span>*</span></td><td><input name="sumdost" type="text" value="'.$_POST["sundost"].'" class="text"></td></tr>
      <tr><td class="name">Статус <span>*</span></td><td><select name="stat"><option value="0"'.($_POST["stat"]==0?" selected=\"selected\"":"").'>модерация</option>
      <option value="1"'.($_POST["stat"]==1?" selected=\"selected\"":"").'>включен</option></select></td></tr>
      <tr><td colspan="2"><p><span style="color:#f00">*</span> - поля обязательные для заполнения</p></td></tr>
      <tr><td colspan="2" class="butt"><input class="but_1" type="submit" name="add" value="Добавить"></td></tr></table></div></form>';
  }
?>