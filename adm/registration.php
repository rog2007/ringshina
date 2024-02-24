<?php
  if(isset($_POST["add"]))
  {
    if(trim($_POST["nick"])=="") $err[1]=1;
    if(checkmail($_POST["email"])==-1) $err[2]=1;
    if($_POST['password'] =='' || $_POST['password2'] =='' || $_POST['password'] != $_POST['password2'])  $err[3]=1;
    $email=$_POST['email'];
    $tmp=mysql_query("SELECT * FROM users WHERE email='".strtolower($email)."' and grid=".$_POST["gr"]);
    if(mysql_num_rows($tmp)>0)  $err[4]=1;
    $nick = $_POST['nick'];
    if(trim($_POST["comp"])=="") $err[9]=1;
    $comp = $_POST['comp'];
    if(trim($_POST["manager"])=="") $err[6]=1;
    $manager = $_POST['manager'];
    if(trim($_POST["tel"])=="") $err[7]=1;
    $tel = $_POST['tel'];
    if(trim($_POST["adres"])=="") $err[8]=1;
    $adres = $_POST['adres'];
    $dopinfo = $_POST["dopinfo"];
    $tmp=mysql_query("SELECT * FROM users WHERE nick='".strtolower($nick)."'");
    if(mysql_num_rows($tmp)>0)  $err[5]=1;
  }
  if(isset($_POST["add"]) && $err[1]==0 && $err[2]==0 && $err[3]==0 && $err[4]==0 && $err[5]==0 && $err[6]==0 && $err[7]==0 && $err[8]==0 && $err[9]==0)
  {
    $uniq_id = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].mktime());
    $pass = $_POST['password'];
    if(mysql_query("INSERT INTO users (password,email,nick,uniq_id,status,date,last_date,grid) VALUES('".
    md5($pass)."','".$email."','".$nick."','".$uniq_id."',0,'".date("dmY")."','".date("dmY")."',0)"))
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
      <div class=\"block\"><h2>Регистрационные данные</h2><table><tr><td>Логин (для входа)</td><td>".$rs->nick."</td></tr><tr><td>Пароль</td><td>".$pass."</td></tr>
      <tr><td>E-mail</td><td>".$email."</td></tr><tr><td>Компания</td><td>".$rs->name."</td></tr>
      <tr><td>Контактное лицо</td><td>".$rs->manager."</td></tr><tr><td>Контактный телефон</td><td>".$rs->tel."</td></tr>
      <tr><td>Фактический адрес</td><td>".$rs->adrdst."</td></tr><tr><td>Примечания</td><td>".$rs->prim."</td></tr></table></div>
      <p>В ближайшее время с Вами свяжется оптовый менеджер обсудит условия сотрудничества и откроет Вам доступ к сайту.</p></body></html>";
      mail("dims@am-shina.ru","Доступ к сайту www.shinadisc.ru",$message,$headers);
      mail($email,"Доступ к сайту www.shinadisc.ru",$message,$headers);
      header("Location: /registration/ok.html");
    }
  }
  if($arg[0]=="ok")
    $str.="<div class=\"log\"><h2>Информация</h2><p style=\"text-align:left\">Пользователь успешно добавлен, и на указанный адрес отправленно письмо с регистрационными данными. В ближайшее время наш менеджер свяжется с Вами для уточнения деталей сотрудничества.</p></div>";
  else
  {
    $str='<form name="addclient" action="/registration/" method="post"><div class="log"><p>Регистрация в системе</p><br>
    <table><tr><td class="name"'.($err[1]==1 || $err[5]==1?" style=\"color:#f00\"":"").'>Логин <span>*</span></td><td><input name="nick" type="text" value="'.$_POST["nick"].'" class="text"></td></tr>
    <tr><td class="name"'.($err[3]==1?" style=\"color:#f00\"":"").'>Пароль <span>*</span></td><td><input name="password" type="password" value="'.$_POST["password"].'" class="text"></td></tr>
    <tr><td class="name"'.($err[3]==1?" style=\"color:#f00\"":"").'>Повтор пароля <span>*</span></td><td><input name="password2" type="password" value="'.$_POST["password2"].'" class="text"></td></tr>
    <tr><td class="name"'.($err[2]==1 || $err[4]==1?" style=\"color:#f00\"":"").'>Email <span>*</span></td><td><input name="email" type="text" value="'.$_POST["email"].'" class="text"></td></tr>
    <tr><td class="name"'.($err[9]==1?" style=\"color:#f00\"":"").'>Компания <span>*</span></td><td><input name="comp" type="text" value="'.$_POST["comp"].'" class="text"></td></tr>
    <tr><td class="name"'.($err[6]==1?" style=\"color:#f00\"":"").'>Контактное лицо <span>*</span></td><td><input name="manager" type="text" value="'.$_POST["manager"].'" class="text"></td></tr>
    <tr><td class="name"'.($err[7]==1?" style=\"color:#f00\"":"").'>Контактный телефон <span>*</span></td><td><input name="tel" type="text" value="'.$_POST["tel"].'" class="text"></td></tr>
    <tr><td class="name"'.($err[8]==1?" style=\"color:#f00\"":"").'>Фактический адрес <span>*</span></td><td><textarea name="adres">'.$_POST["adres"].'</textarea></td></tr>
    <tr><td class="name">Дополнительная информация</td><td><textarea name="dopinfo">'.$_POST["dopinfo"].'</textarea></td></tr>
    <tr><td colspan="2"><p><span style="color:#f00">*</span> - поля обязательные для заполнения</p></td></tr>
    <tr><td colspan="2" class="butt"><input class="but_1" type="submit" name="add" value="Добавить"></td></tr></table></div></form>';
    if($err[1]==1 || $err[2]==1 || $err[3]==1 || $err[4]==1 || $err[5]==1 || $err[6]==1 || $err[7]==1 || $err[8]==1 || $err[9]==1)
      $str.="<div class=\"log\"><h3>Ошибка</h3><p>Не верно введены данные о клиенте. Необходимо исправить данные в полях подсвеченных красным.</p>
      <ul>".($err[1]==1?"<li>Необходимо ввести логин</li>":"").($err[2]==1?"<li>Неверный адрес электронной почты</li>":"").($err[3]==1?"<li>Не совпадают пароли</li>":"")
      .($err[4]==1?"<li>Человек с данным электронным адресом зарегистрирован на сайте</li>":"").($err[5]==1?"<li>Данный логин уже присутствует в базе</li>":"")
      .($err[6]==1?"<li>Не указано контактное лицо</li>":"").($err[7]==1?"<li>Не указан контактный телефон</li>":"")
      .($err[8]==1?"<li>Не указан адрес</li>":"").($err[9]==1?"<li>Не указано название компании</li>":"")."</ul></div>";
  }
?>