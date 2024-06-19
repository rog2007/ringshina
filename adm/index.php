<?php
session_start();

require("connect.php");
require("funcn.php");
require("func_new.php");
require("a.charset.php");

$REQUEST_URI2 = str_replace('index.php', 'index.html', $_SERVER['REQUEST_URI']);
$REQUEST_URI1 = str_replace('.html', '/', $REQUEST_URI2);

$do = filter_input(INPUT_POST, 'do');
$email = filter_input(INPUT_POST, 'email');
$password = filter_input(INPUT_POST, 'password');

if ($do !== null) {
    if ($email AND $password) {
        $res = query('SELECT * FROM users WHERE email=:email AND password=:password AND status=:status',
            array(':email' => $email, ':password' => md5($password), ':status' => 1));
        if ($res['result'] === false) {
            echo '<p>Проверка доступа. Ошибка обращения к БД. ' . dbLastErrorToString($res['error']) . '</a>';
            exit;
        }
        if (count($res['data']) > 0) {
            $_SESSION['name'] = $res['data'][0]->email;
            $_SESSION['pass'] = $res['data'][0]->password;
            $_SESSION['SID'] = md5(crypt($_SESSION['name'], $_SESSION['pass']));
            header("Location: /adm/");
            die;
        } else {
            $er = "Неверный логин/пароль. Возможно Ваш аккаунт не активирован.";
        }
    } else {
        header("Location: /adm/");
        die;
    }
}

$var = explode("/", $REQUEST_URI1);
if ((trim($var[2]) != '') && ($var[2])) {
    $page = $var[2];
} else {
    $page = 'index';
}
if ($page == "index") {
    include_once("main.php");
}
if ($page != "index") {
    $var[2] .= ".php";
    for ($i = 3; $i < sizeof($var); $i++)
        if (isset($var[$i]) && $var[$i] != '') {
            $arg[$i - 3] = $var[$i];
        }
    include_once($var[2]);
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Администрирование RingShina</title>
    <link rel="stylesheet" type="text/css" href="/adm/css/style.css"/>
    <link rel="stylesheet" type="text/css" href="/adm/css/jquery.lightbox-0.5.css"/>
    <meta charset="utf-8"/>
    <script language="javascript" type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
    <script language="javascript" type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
    <script language="javascript" type="text/javascript" src="/adm/js/fancybox.js"></script>
    <script language="javascript" type="text/javascript" src="/js/zxml.js"></script>
    <script language="javascript" type="text/javascript" src="/js/sourse-v2.js"></script>
    <script language="javascript" type="text/javascript" src="/adm/adm.js"></script>
</head>
<body>
<div id="head"><a href="/adm/lprices/">Загрузка прайсов</a> | <a href="/adm/spravochnic.html">Справочники</a> | <a
            href="/adm/orders.html">Заказы</a> | <a href="/adm/work_new/">Обработка не отработанных позиций</a> | <a
            href="/adm/work_akb/">Обработка не отработанных позиций (АКБ)</a> | <a href="/">На сайт</a> | <a
            href="/adm/calculators/show/1/">Калькуляторы</a> | <a href="/adm/spravochnic_podbor/vendors/1">Побор авто</a> | <a href="/adm/exit.php">Выход</a></div>
<div id="main">
    <?php
    if (md5(crypt($_SESSION['name'], $_SESSION['pass'])) != $_SESSION['SID']) {
        echo "<h2 style=\"margin-left:20px\">Необходимо ввести логин и пароль для продолжения работы в админской части сайта</h2>";
        echo '<form name="form1" action="" method="post"><div class="log"  style="margin-left:20px"><table id="tb">
  <tr><td align="right"><strong>Логин</strong></td><td><input name="email" type="text" value="" class="text"></td><tr>
  <tr><td align="right"><strong>Пароль</strong></td><td><input name="password" type="password" value="" class="text"></td></tr>
  <tr><td colspan="2" align="center"><input class="but_1" type="submit" name="do" value="Войти"></td><td></td></tr></table></div></form>';
    } else {
        echo $str;
    }
    if (isset($_POST["upsp"])) {
        UpdSp();
    }

    /* if(isset($_POST["upd"]))
      {
      UpdateImgs();
      return;
      } */
    ?>
    <script type="text/javascript">

        function selectImage(el, url) {

            document.getElementById('url').innerHTML = url;
            document.getElementById('imagebig').src = url;
            for (var childItem in document.getElementById('imagespath').childNodes) {
                //alert(childItem.className);
                childItem.className = 'image';
            }
            el.parentNode.className += ' select';
        }

        function changeLable(el) {

            var lableName = el.id + '-lable';
            document.getElementById(lableName).innerHTML = el.options[el.selectedIndex].innerHTML;
        }

        tinyMCE.init({
            // General options
            mode: "exact",
            elements: "page-content,model_descr,tov_descr,brand_descr",
            theme: "advanced",
            plugins: "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
            // Theme options
            theme_advanced_buttons1: "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4: "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_resizing: true,
            // Skin options
            skin: "o2k7",
            skin_variant: "silver",
            // Example content CSS (should be your site CSS)
            content_css: "css/example.css",
            // Drop lists for link/image/media/template dialogs
            template_external_list_url: "js/template_list.js",
            external_link_list_url: "js/link_list.js",
            external_image_list_url: "js/image_list.js",
            media_external_list_url: "js/media_list.js",
            // Replace values for the template plugin
            template_replace_values: {
                username: "Some User",
                staffid: "991234"
            }
        });
    </script>
</div>
</body>
</html>