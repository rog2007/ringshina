<?php
session_start();
$ses = 0;
if (isset($_SESSION['name']) && isset($_SESSION['pass'])) {

    if (md5(crypt($_SESSION['name'], $_SESSION['pass'])) == $_SESSION['SID'])
        $ses = 1;
}
$content = '';
//ini_set('display_errors', 'on');
require ("cookies.php");
require ("connect.php");
require ("func.php");

/* vars */
$autoVend = 0;
$autoModel = 0;
$autoYear = 0;
$autoModif = 0;

$tyreDiamId = 0;
$tyreDiamName = 'все';
$tyreProfWId = 0;
$tyreProfWName = 'все';
$tyreProfHId = 0;
$tyreProfHName = 'все';
$tyreSeasId = 0;
$tyreSeasName = 'все';
$tyrePriceFrom = 0;
$tyrePriceTo = 0;
$tyreBrands = array();

$discDiamId = 0;
$discDiamName = 'все';
$discWidthId = 0;
$discWidthName = 'все';
$discOtvId = 0;
$discDCKOId = 0;
$discPcdId = 0;
$discPcdName = 'все';
$discViletId = 0;
$discViletName = '';
$discViletIdE = 0;
$discViletNameE = '';
$discStupId = 0;
$discStupName = 'все';
$discPriceFrom = 0;
$discPriceTo = 0;
$discBrands = array();

$volume = 0;
$volumeName = 'все';
$volumeFrom = 0;
$volumeFromName = 'не указан';
$volumeTo = 0;
$volumeToName = 'не указан';
$volt = 0;
$voltName = 'все';
$rvrt = 0;
$rvrtName = 'все';
$klem = 0;
$klemName = 'все';
$akbPriceFrom = 0;
$akbPriceTo = 0;

$REQUEST_URI1 = str_ireplace("index.php", "index.html", $_SERVER["REQUEST_URI"]);
$REQUEST_URI1 = str_ireplace(".html", "/", $REQUEST_URI1);
$var = explode("/", $REQUEST_URI1);

if ((trim($var[1]) != '') && ($var[1]))
    $page = $var[1];
else
    $page = 'index';

$res = mysql_query("select * from pages where pg='" . $var[1] . "'");
if ($curpage = mysql_fetch_object($res)) {

    $var[1] = 'static';
}
if ($page == "index")
    include_once("main.php");
if (!isset($var[2]))
    $var[2] = 0;
$kw2 = "";
$kw3 = "";
$tit2 = "";
$desk2 = "";
$leftTabIndex = 1;
if ($page != "index") {

    if ($var[1] == 'podborauto') {
        $leftTabIndex = 4;
    }
    if ($var[1] == 'param') {
        $leftTabIndex = 1;
    }
    $var[1].=".php";
    for ($i = 2; $i < sizeof($var); $i++)
        if ($var[$i])
            $arg[$i - 2] = $var[$i];
    include_once($var[1]);
} else {

    $leftTabIndex = 4;
}
?><!DOCTYPE html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="ru"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="ru"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="ru"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="ru"> <!--<![endif]-->
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width">
        <title><?php echo $title; ?></title>
        <meta name="description" content="<?php echo $descr; ?>" />
        <meta name="keywords" content="<?php echo $keywords; ?>" />
        <link rel="icon" href="/images/favicons/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="/css/normalize.css">
        <link rel="stylesheet" href="/css/foundation.min.css">
        <link rel="stylesheet" href="/css/style-v4.css">
        <link rel="stylesheet" href="/css/style-add-v3.css">
        <script src="/js/vendor/custom.modernizr.js"></script>
        <script language="javascript" type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>        
        <script language="javascript" type="text/javascript" src="/js/sourse-v2.js"></script>
        <script language="javascript" type="text/javascript" src="/js/zxml.js"></script>
        <!--[if lt IE 9]>
                <link rel='stylesheet' href='/css/ie8-grid-foundation-4.css' type='text/css' media='all' />
        <![endif]-->
        <!-- IE Fix for HTML5 Tags -->
        <!--[if lt IE 9]>
                <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <!--Start of Zopim Live Chat Script-->
        <script type="text/javascript">
            window.$zopim || (function (d, s) {
                var z = $zopim = function (c) {
                    z._.push(c)
                }, $ = z.s =
                        d.createElement(s), e = d.getElementsByTagName(s)[0];
                z.set = function (o) {
                    z.set.
                            _.push(o)
                };
                z._ = [];
                z.set._ = [];
                $.async = !0;
                $.setAttribute('charset', 'utf-8');
                $.src = '//v2.zopim.com/?1ekuR29OORXagrUFIUNZRn0vYILvPYdV';
                z.t = +new Date;
                $.
                        type = 'text/javascript';
                e.parentNode.insertBefore($, e)
            })(document, 'script');
        </script>
        <!--End of Zopim Live Chat Script-->
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    <body>
        <?php
        $cnt = 0;
        $sum = 0;
        $bResult = basket_count();
        if ($bResult !== false) {
            $cnt = $bResult->s_cnt;
            $sum = $bResult->s_tot;
        }
        ?>
        <!-- Зафиксированный блок с иконками -->
        <div id="fix">
            <p><a href="/index.php"><img src="/img/search.png" data-tooltip class="tip-left" title="Перейти к форме поиска товаров" alt="иконка поиска"></a></p>
            <p style="text-align:center;"><a href="/bask/" style="color:#ffffff;"><img src="/img/shop.png" data-tooltip class="tip-left" title="Перейти в корзину покупок" alt="иконка корзины"><br /><?php echo ($cnt ? '(' . $cnt . ')' : '') ?></a></p>
            <!--p><a href="#" data-reveal-id="myModal"><img src="/img/telephone.png" data-tooltip class="tip-left" title="Заказать обратный звонок" alt="иконка телефона"></a></p-->
        </div>
        <!-- Конец зафиксированного блока с иконками -->
        <div id="page">
            <!-- Начало хедера и навигации -->
            <div class="header row">				
                <div class="large-3 columns text-left">
                    <ul class="button-group even-2 showme">
                        <li><a href="/bask/" style="color:#fff" class="button alert small"><img src="/img/bask.png" alt="иконка"> Корзина покупок <?php echo ($cnt ? '(' . $cnt . ')' : '') ?></a></li>
                    </ul>
<?php                    
    echo getBlock('address');
?>
                </div>
                <div class="large-6 columns text-center">
                    <h1 class="descr wh">Продажа шин и дисков в городе Омске!</h1>
                    <a href="/"><img src="/img/logotip.png" class="logotip" alt="Логотип компании"/></a>
                </div>
                <div class="large-3 columns vr text-right">
<?php                    
    echo getBlock('phone');
?>                    
                </div>
                <div id="myModal" class="reveal-modal">
                    <h2>Здравствуйте!</h2>
                    <p class="lead">Заказать обратный звонок</p>
                    <p>Поля со звездочкой (*) обязательны к заполнению.</p>
                    <form method="post" action="/callback/" name="callback">
                        <fieldset>
                            <legend>Мы обязательно перезвоним!</legend>
                            <?php
                            echo '<div class="large-6 columns" id="checkout-error"' . ($fio_er || $tel_er ? ' style="display:block"' : ' style="display:none"') . '>
    Необходимо заполнить все обязательные поля</div>';
                            ?>
                            <div class="row">
                                <div class="large-6 columns">
                                    <label>Ваше имя *</label>
                                    <input type="text" placeholder="укажите ваше имя" name="fio" style="width:200px" value="" />
                                </div>
                                <div class="large-6 columns">
                                    <label>Ваш телефон *</label>
                                    <input type="text" placeholder="укажите телефон" name="tel" style="width:200px" value="" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-12 columns">
                                    <label>Примечание</label>
                                    <textarea placeholder="Вопрос или совет" type="text" name="info"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-6 columns">
                                    <label>Введите проверочный код</label>
                                    <input name="sid" maxlength="8" type="text" value="" />
                                    <img src="/kcaptcha/index.php" title="обновить рисунок" alt="обновить рисунок" onclick="return CaptchaUpdate(this);" />
                                </div>
                            </div>
                            <input  class="button" type="submit" value="Отправить" onclick="return CheckCallBack()"/>
                        </fieldset>
                    </form>
                    <a class="close-reveal-modal">&#215;</a>
                </div>
            </div> 
            <nav class="row">
                <ul class="button-group even-6">
                    <li><a href="/" class="button">Главная</a></li>
                    <li><a href="/akcii.html" class="button">Акции</a></li>
                    <li><a href="/catalog/shini.html" class="button drop-down-menu" id="tyre-menu">Шины<div class="row-bottom"></div></a></li>
                    <li><a href="/catalog/diski.html" class="button drop-down-menu" id="disc-menu">Диски<div class="row-bottom"></div></a></li>
                    <li><a href="/modeli/akb.html" class="button drop-down-menu" id="akb-menu"><span class="big">Аккумуляторы</span><span class="small">АКБ</span><div class="row-bottom"></div></a></li>
                    <li><a href="/contacts.html" class="button">Контакты</a></li>
                </ul>                
            </nav>
            <div id="sub-tyre-menu" class="sub-menu-pp">
                <a href="/param/shini/">Подбор шин</a>
                <a href="/podborauto/">Подбор по авто</a>
                <a href="/catalog/shini.html">Каталог шин</a>
            </div>
            <div id="sub-disc-menu" class="sub-menu-pp">
                <a href="/param/diski/">Подбор дисков</a>
                <a href="/podborauto/">Подбор по авто</a>
                <a href="/catalog/diski.html">Каталог дисков</a>
            </div>
            <div id="sub-akb-menu" class="sub-menu-pp">
                <a href="/param/akb/">Подбор АКБ</a>                
                <a href="/modeli/akb.html">Каталог АКБ</a>
            </div>
            <!-- Конец хедера и навигации -->

            <?php
            $horTab = 1;
            if ($page != "index") {

                $horTab = 2;
            }

            if ($var[1] == 'podborauto.php' && !isset($_GET["modif"])) {

                $horTab = 1;
            }

            if ($var[1] == 'param.php' && !isset($_GET["paramsmb"])) {

                $leftTabIndex = 1;
                $horTab = 1;
            }
            ?>

            <!-- Начало табов поиска -->
            <div class="section-container auto" data-section>
                <section <?php echo ($horTab == 1 ? '' : ' class="active"') ?>>
                    <p class="title specpr"><a href="#section2">Специальные предложения</a></p>
                    <div class="content text-center" data-slug="section2">
                        <a href="/akcii.html#bdost"><img src="/img/1.jpg" /></a>
                        <a href="/akcii.html#bshin"><img src="/img/2.jpg" /></a>
                        <a href="/akcii.html#bhran"><img src="/img/3.jpg" /></a>
                    </div>
                </section>
                <section<?php echo ($horTab == 2 ? '' : ' class="active"') ?>>
                    <p class="title"><a href="#section1">Подбор товара</a></p>
                    <div class="content" data-slug="section1">
                        <div class="row">
                            <div class="section-container vertical-tabs" data-section="vertical-tabs">
                                <section<?php echo ($leftTabIndex == 4 ? ' class="active"' : '') ?>>
                                    <p class="title" data-section-title><a href="#">Подбор по авто</a></p>
                                    <div class="content" data-section-content>
                                        <form method="get" class="custom" name="pauto" action="/podborauto/">
                                            <!--legend><b>Что будем искать?</b></legend><br />
                                              <label for="radio1"><input name="radio1" type="radio" id="radio1" style="display:none;" CHECKED><span class="custom radio checked"></span> Все результаты</label>
                                              <label for="radio1"><input name="radio1" type="radio" id="radio1" style="display:none;"><span class="custom radio"></span> Только шины</label>
                                              <label for="radio1"><input name="radio1" type="radio" id="radio1" style="display:none;"><span class="custom radio"></span> Только диски</label><br /
                                              -->
                                            <label for="vend"><b>Производитель авто</b></label>
                                            <select id="vend" name="vend" onchange="return podbor_ajax(1)" class="no-custom medium">
                                                <option value="0"<?php echo ($autoVend == 0 ? ' selected="selected"' : ''); ?>>все</option>
                                                <?php
                                                $selProfWidth = $dbcon->prepare('select vendor from podbor_shini_i_diski group by vendor order by vendor');
                                                if ($selProfWidth->execute() && $selProfWidth->rowCount() > 0) {
                                                    while ($resObj = $selProfWidth->fetch(PDO::FETCH_OBJ)) {
                                                        echo '<option value="' . $resObj->vendor . '"' . ($autoVend === $resObj->vendor ? ' selected="selected"' : '') . '>' . $resObj->vendor . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <label for="model"><b>Марка авто</b></label>
                                            <select id="model" name="model" class="no-custom medium" onchange="return podbor_ajax(2)"<?php echo ($autoVend ? '' : ' disabled="disabled"') ?>>
                                                <option value="0"<?php echo ($autoModel == 0 ? ' selected="selected"' : ''); ?>>все</option>
                                                <?php
                                                if ($autoVend) {

                                                    $selProfHeight = $dbcon->prepare("select car from podbor_shini_i_diski where vendor='" . $autoVend . "' group by car order by car");
                                                    if ($selProfHeight->execute() && $selProfHeight->rowCount() > 0) {
                                                        while ($resObj = $selProfHeight->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $resObj->car . '"' . ($autoModel === $resObj->car ? ' selected="selected"' : '') . '>' . $resObj->car . '</option>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <label for="year"><b>Год выпуска</b></label>
                                            <select id="year" name="year" class="no-custom medium" onchange="return podbor_ajax(3)"<?php echo ($autoModel ? "" : ' disabled="disabled"') ?>>
                                                <option value="0"<?php echo ($autoYear == 0 ? ' selected="selected"' : ''); ?>>все</option>
                                                <?php
                                                if (isset($_GET["model"])) {

                                                    $selDiam = $dbcon->prepare("select year from podbor_shini_i_diski where vendor='" . $autoVend . "' and car='" . $autoModel . "' group by year order by year");
                                                    if ($selDiam->execute() && $selDiam->rowCount() > 0) {
                                                        while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $resObj->year . '"' . ($autoYear === $resObj->year ? ' selected="selected"' : '') . '>' . $resObj->year . '</option>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <label for="modif"><b>Модификация</b></label>
                                            <select id="modif" name="modif" class="no-custom medium" onchange="document.pauto.submit();"<?php echo ($autoYear ? "" : ' disabled="disabled"') ?>>
                                                <option value="0"<?php echo ($autoModif === 0 ? ' selected="selected"' : ''); ?>>все</option>
                                                <?php
                                                if ($autoYear) {

                                                    $selSeas = $dbcon->prepare("select modification from podbor_shini_i_diski where vendor='" . $autoVend . "' and car='" . $autoModel . "' and year='" . $autoYear . "' group by modification order by modification");
                                                    if ($selSeas->execute() && $selSeas->rowCount() > 0) {

                                                        while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $resObj->modification . '"' . ($autoModif == $resObj->modification ? ' selected="selected"' : '') . '>' . $resObj->modification . '</option>';
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </form>
                                    </div>
                                </section>
                                <section<?php echo ($leftTabIndex == 1 && $tov == 1 ? ' class="active"' : '') ?>>
                                    <p class="title" data-section-title><a href="#">Шины</a></p>
                                    <div class="content" data-section-content>
                                        <div class="row">
                                            <form method="get" id="tyres" name="tyres" action="/param/shini/"  class="custom">
                                                <div class="small-5 columns">
                                                    <label for="prfw"><b>Ширина</b></label>
                                                    <select id="prfw" class="no-custom medium" name="prfw">
                                                        <option value="0"<?php echo ($tyreProfWId == 0 ? ' selected="selected"' : ''); ?>>Все</option>
                                                        <?php
                                                        $selProfWidth = $dbcon->prepare('select id, name from profw where vis=1' .
                                                                ($tyreProfWId ? ' OR id = ' . $tyreProfWId : '') . ' order by name*1');
                                                        if ($selProfWidth->execute() && $selProfWidth->rowCount() > 0) {
                                                            while ($resObj = $selProfWidth->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="' . $resObj->id . '"' . ($tyreProfWId == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <label for="prfh"><b>Высота</b></label>
                                                    <select id="prfh" class="no-custom medium" name="prfh">
                                                        <option value="0"<?php echo ($tyreProfHId == 0 ? ' selected="selected"' : ''); ?>>Все</option>
                                                        <?php
                                                        $selProfHeight = $dbcon->prepare('select id, name from profh where vis=1' .
                                                                ($tyreProfHId ? ' OR id = ' . $tyreProfHId : '') . ' order by name*1');
                                                        if ($selProfHeight->execute() && $selProfHeight->rowCount() > 0) {
                                                            while ($resObj = $selProfHeight->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="' . $resObj->id . '"' . ($tyreProfHId == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <label for="diam"><b>Диаметр</b></label>
                                                    <select id="diam" class="no-custom medium" name="diam">
                                                        <option value="0"<?php echo ($tyreDiamId == 0 ? ' selected="selected"' : ''); ?>>Все</option>
                                                        <?php
                                                        $selDiam = $dbcon->prepare('select tb6_id as id, tb6_nm as name from tab6 where tb6_tov_id=1 and tb6_vis=1 order by tb6_nm');
                                                        if ($selDiam->execute() && $selDiam->rowCount() > 0) {
                                                            while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="' . $resObj->id . '"' . ($tyreDiamId == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <label for="seas"><b>Сезонность</b></label>
                                                    <select id="seas" class="no-custom medium" name="seas">
                                                        <option value="0"<?php echo ($tyreSeasId == 0 ? ' selected="selected"' : ''); ?>>Все</option>
                                                        <?php
                                                        $selSeas = $dbcon->prepare('select tb10_id as id, tb10_nm as name from tab10 where tb10_tov_id=1 and tb10_vis=1 order by tb10_nm');
                                                        if ($selSeas->execute() && $selSeas->rowCount() > 0) {

                                                            while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {
                                                                echo '<option value="' . $resObj->id . '"' . ($tyreSeasId == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                            }
                                                            echo '<option value="50"' . ($tyreSeasId == 50 ? ' selected="selected"' : '') . '>Зима не шипованные</option>';
                                                            echo '<option value="53"' . ($tyreSeasId == 53 ? ' selected="selected"' : '') . '>Зима шипованные</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <fieldset>
                                                        <legend>Цена</legend>
                                                        <div class="row">
                                                            <div class="large-6 columns">
                                                                <label>От</label>
                                                                <input type="text" placeholder="Минимум" id="tyre_price_from" name="tyre_price_from" value="<?php echo ($tyrePriceFrom ? $tyrePriceFrom : '') ?>" >
                                                            </div>
                                                            <div class="large-6 columns">
                                                                <label>До</label>
                                                                <input type="text" placeholder="Максимум" id="tyre_price_to" name="tyre_price_to" value="<?php echo ($tyrePriceTo ? $tyrePriceTo : '') ?>" >
                                                            </div>
                                                        </div>
                                                        <input name="paramsmb"  class="button button-podbor" value=" Подобрать " type="submit"/>
                                                        <input name="paramsmb"  class="button button-podbor" value=" Очистить " onclick="return clearTyres()" type="button"/>
                                                    </fieldset>
                                                </div>
                                                <div class="small-7 columns">
                                                    <p><b>Бренды</b></p>
                                                    <?php
                                                    echo getBrandsPodbor(1, $tyreBrands);
                                                    ?>
                                            </form>
                                        </div>
                                    </div>
                            </div>
                            </section>
                            <section<?php echo ($leftTabIndex == 1 && $tov == 2 ? ' class="active"' : '') ?>>
                                <p class="title" data-section-title><a href="#">Диски</a></p>
                                <div class="content" data-section-content>
                                    <div class="row">
                                        <form method="get" name="discs" id="discs"  class="custom" action="/param/diski/" id="tabs-disc-tab">
                                            <div class="small-5 columns">
                                                <label for="diamd"><b>Диаметр диска</b></label>
                                                <select id="diamd" class="no-custom medium" name="diamd">
                                                    <option value="0"<?php echo ($discDiamId == 0 ? ' selected="selected"' : ''); ?>>Все</option>
                                                    <?php
                                                    $selDiam = $dbcon->prepare('select tb6_id as id, tb6_nm as name from tab6 where tb6_tov_id=2 and tb6_vis=1 order by tb6_nm');
                                                    if ($selDiam->execute() && $selDiam->rowCount() > 0) {
                                                        while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $resObj->id . '"' . ($discDiamId == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <label for="widthd"><b>Ширина обода</b></label>
                                                <select id="widthd" class="no-custom medium" name="widthd">
                                                    <option value="0"<?php echo ($discWidthId == 0 ? ' selected="selected"' : ''); ?>>Все</option>
                                                    <?php
                                                    $selDiam = $dbcon->prepare('select tb5_id as id, tb5_nm as name from tab5 where tb5_tov_id=2 and tb5_vis=1 order by tb5_nm * 1');
                                                    if ($selDiam->execute() && $selDiam->rowCount() > 0) {
                                                        while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $resObj->id . '"' . ($discWidthId == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <label for="pcd"><b>Крепеж (PCD)</b></label>
                                                <select id="pcd" class="no-custom medium" name="pcd">
                                                    <option value="0"<?php echo ($discPcdId == 0 ? ' selected="selected"' : ''); ?>>все</option>
                                                    <?php
                                                    $selDiam = $dbcon->prepare('select id, pcd_name as name from tab_pcd where pcd_vis=1 order by pcd_name');
                                                    if ($selDiam->execute() && $selDiam->rowCount() > 0) {
                                                        while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $resObj->id . '"' . ($discPcdId == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>


                                                <label for="vilb"><b>Вылет (ET), мм</b></label>
                                                <div class="row">
                                                    <div class="large-3 small-5 columns">
                                                        <span><b>от</b></span><select id="vilb" class="no-custom small" name="vilb">
                                                            <option value="0"<?php echo ($discViletId == 0 ? ' selected="selected"' : ''); ?>>все</option>
                                                            <?php
                                                            $selDiam = $dbcon->prepare('select tb9_id as id, tb9_nm as name from tab9 where tb9_tov_id=2 order by tb9_nm*1');
                                                            if ($selDiam->execute() && $selDiam->rowCount() > 0) {
                                                                while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="' . $resObj->id . '"' . ($discViletId == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>

                                                    </div>
                                                    <div class="large-3 small-5 columns" style="margin-left: 23px">
                                                        <span><b>до</b></span><select id="vile" class="no-custom small dme" name="vile">
                                                            <option value="0"<?php echo ($discViletIdE == 0 ? ' selected="selected"' : ''); ?>>все</option>
                                                            <?php
                                                            $selDiam = $dbcon->prepare('select tb9_id as id, tb9_nm as name from tab9 where tb9_tov_id=2 order by tb9_nm*1');
                                                            if ($selDiam->execute() && $selDiam->rowCount() > 0) {
                                                                while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
                                                                    echo '<option value="' . $resObj->id . '"' . ($discViletIdE == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>

                                                    </div>
                                                    <div class="large-4 small-2 columns">
                                                    </div>
                                                </div>
                                                <label for="stup"><b>Центральные отверстия</b></label>
                                                <select id="stup" class="no-custom medium" name="stup">
                                                    <option value="0"<?php echo ($discStupId == 0 ? ' selected="selected"' : ''); ?>>все</option>
                                                    <?php
                                                    $selDiam = $dbcon->prepare('select tb12_id as id, tb12_nm as name from tab12 where tb12_tov_id=2 and (tb12_vis=1' .
                                                            ($discStupId ? ' OR tb12_id = ' . $discStupId : '') . ') order by tb12_nm*1');
                                                    if ($selDiam->execute() && $selDiam->rowCount() > 0) {
                                                        while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $resObj->id . '"' . ($discStupId == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <fieldset>
                                                    <legend>Цена (в руб.)</legend>
                                                    <div class="row">
                                                        <div class="large-6 columns">
                                                            <label>От</label>
                                                            <input type="text" placeholder="Минимум" id="disc_price_from" name="disc_price_from" value="<?php echo ($discPriceFrom ? $discPriceFrom : '') ?>" />
                                                        </div>
                                                        <div class="large-6 columns">
                                                            <label>До</label>
                                                            <input type="text" placeholder="Максимум" id="disc_price_to" name="disc_price_to" value="<?php echo ($discPriceTo ? $discPriceTo : '') ?>" />
                                                        </div>
                                                    </div>
                                                    <input name="paramsmb"  class="button button-podbor" value=" Подобрать " type="submit"/>
                                                    <input name="paramsmb"  class="button button-podbor" value=" Очистить " onclick="return clearDiscs()" type="button"/>
                                                </fieldset>
                                            </div>
                                            <div class="small-7 columns">
                                                <p><b>Бренды</b></p>
                                                <form class="custom">
                                                    <?php
                                                    echo getBrandsPodbor(2, $discBrands);
                                                    ?>
                                                </form>
                                            </div>
                                    </div>
                                </div>
                            </section>
                            <section<?php echo ($leftTabIndex == 1 && $tov == 3 ? ' class="active"' : '') ?>>
                                <p class="title" data-section-title><a href="#">Аккумуляторы</a></p>
                                <div class="content" data-section-content>
                                    <form method="get" id="akb" name="akb" class="custom" action="/param/akb/" id="tabs-akb-tab">
                                        <div class="row">
                                            <div class="large-3 columns">
                                                <p><b>Ёмкость (Ач) :</b></p>
                                                <label for="volume">Выбрать</label>
                                                <select id="volume" name="volume" class="no-custom small">
                                                    <option value="0"<?php echo ($volume == 0 ? ' selected="selected"' : ''); ?>>все</option>
                                                    <?php
                                                    $selVol = $dbcon->prepare('select id, name from akb_v WHERE vis = 1 order by name*1');
                                                    if ($selVol->execute() && $selVol->rowCount() > 0) {

                                                        while ($resObj = $selVol->fetch(PDO::FETCH_OBJ)) {

                                                            echo '<option value="' . $resObj->id . '"' . ($volume == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <br />
                                                <label for="volumeFrom">От</label>
                                                <select id="volumeFrom" name="volumeFrom" class="no-custom small">
                                                    <option value="0" <?php echo ($volumeFrom == 0 ? ' selected="selected"' : ''); ?>>не указано</option>
                                                    <?php
                                                    $selVol = $dbcon->prepare('select id, name from akb_v WHERE vis = 1 order by name*1');
                                                    if ($selVol->execute() && $selVol->rowCount() > 0) {

                                                        while ($resObj = $selVol->fetch(PDO::FETCH_OBJ)) {

                                                            echo '<option value="' . $resObj->id . '"' . ($volumeFrom == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <label for="volumeTo">До</label>
                                                <select id="volumeTo" name="volumeTo" class="no-custom small">
                                                    <option value="0" <?php echo ($volumeTo == 0 ? ' selected="selected"' : ''); ?>>не указано</option>
                                                    <?php
                                                    $selVol = $dbcon->prepare('select id, name from akb_v WHERE vis = 1 order by name*1');
                                                    if ($selVol->execute() && $selVol->rowCount() > 0) {

                                                        while ($resObj = $selVol->fetch(PDO::FETCH_OBJ)) {

                                                            echo '<option value="' . $resObj->id . '"' . ($volumeTo == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="large-3 columns">
                                                <p><b>Напряжение (В) :</b></p>
                                                <label for="volt">Выбрать</label>
                                                <select id="volt" name="volt" class="no-custom small">
                                                    <option value="0" <?php echo ($volt == 0 ? ' selected="selected"' : ''); ?>>не указано</option>
                                                    <?php
                                                    $selVolt = $dbcon->prepare('select id, name from akb_volt WHERE vis = 1 order by name*1');
                                                    if ($selVolt->execute() && $selVolt->rowCount() > 0) {
                                                        while ($resObj = $selVolt->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value="' . $resObj->id . '"' . ($volt == $resObj->id ? ' selected="selected"' : '') . '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="large-3 columns">
                                                <p><b>Полярность :</b></p>
                                                <label for="rvrt">Выберите</label>
                                                <select id="rvrt" name="rvrt" class="no-custom small">
                                                    <option value="0" <?php echo ($rvrt == 0 ? ' selected="selected"' : ''); ?>>любая</option>
                                                    <?php
                                                    $selRvrt = $dbcon->prepare('select id, name from akb_rvrt WHERE vis = 1 order by id DESC');
                                                    if ($selRvrt->execute() && $selRvrt->rowCount() > 0) {
                                                        while ($resObj = $selRvrt->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value = "' . $resObj->id . '" ' . ($rvrt == $resObj->id ? ' selected="selected"' : '') .
                                                            '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="large-3 columns">
                                                <p><b>Клеммы :</b></p>
                                                <label for="klem">Выберите</label>
                                                <select id="klem" name="klem" class="no-custom small">
                                                    <option value="0" <?php echo ($klem == 0 ? ' selected="selected"' : ''); ?>>любая</option>
                                                    <?php
                                                    $selRvrt = $dbcon->prepare('select id, name from akb_klemy WHERE vis = 1 order by id');
                                                    if ($selRvrt->execute() && $selRvrt->rowCount() > 0) {
                                                        while ($resObj = $selRvrt->fetch(PDO::FETCH_OBJ)) {
                                                            echo '<option value = "' . $resObj->id . '" ' . ($klem == $resObj->id ? ' selected="selected"' : '') .
                                                            '>' . $resObj->name . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <fieldset>
                                                <legend>Цена (в руб.)</legend>
                                                <div class="row">
                                                    <div class="large-6 columns">
                                                        <label>От</label>
                                                        <input type="text" placeholder="Минимум" name="akb_price_from" id="akb_price_from" value="<?php echo ($akbPriceFrom ? $akbPriceFrom : '') ?>" />
                                                    </div>
                                                    <div class="large-6 columns">
                                                        <label>До</label>
                                                        <input type="text" placeholder="Максимум" name="akb_price_to" id="akb_price_to" value="<?php echo ($akbPriceTo ? $akbPriceTo : '') ?>" />
                                                    </div>
                                                </div>
                                                <input name="paramsmb" class="button button-podbor" value=" Подобрать " type="submit"/>
                                                <input class="button button-podbor" value=" Очистить " onclick="return clearAkb()" type="button"/>
                                            </fieldset>
                                        </div>
                                    </form>
                                </div>
                            </section>
                            <?php include_once("calculator.php"); ?>                            
                        </div>	
                    </div>
                    <!-- Конец табов поиска -->
            </div>
        </section>
    </div>
    <?php
    echo $content;
    ?>

    <!-- Футер сайта -->

    <footer class="row">
        <div class="large-12 columns">
            <hr />
            <div class="row">
                <div class="large-4 columns">
                    <p>&copy; Ринг-Шина, 2013</p>
                    <img src="/img/logo_min.png">
                </div>
                <div class="large-8 columns">
                    <ul class="inline-list right">
                        <li><a href="/">Главная</a></li>
                        <li><a href="/akcii.html">Акции</a></li>
                        <li><a href="/contacts.html">Контакты</a></li>
                    </ul>
<?php
    echo getBlock('footer_contacts');
?>
                </div>
            </div>
        </div> 
    </footer>
</div>
<script>
    document.write('<script src=/js/vendor/'
            + ('__proto__' in {} ? 'zepto' : 'jquery')
            + '.js><\/script>');
</script>
<script src="/js/foundation.min.js"></script>
<script>
    $(document).foundation();
</script>
<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-46205279-1', 'ringshina.ru');
    ga('send', 'pageview');

</script>
</body>
</html>
