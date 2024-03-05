$(document).ready(function () {
    $("#vilb").change(function(){
        if(Number.isNaN(parseInt($("#vilb option:selected").text())) ||
            Number.isNaN(parseInt($("#vile option:selected").text())) ||
            parseInt($("#vile option:selected").text()) < parseInt($("#vilb option:selected").text())) {
            $("#vile").val(parseInt($("#vilb option:selected").val()));
        }
    });
    $("#vile").change(function(){
        if(Number.isNaN(parseInt($("#vilb option:selected").text())) ||
            Number.isNaN(parseInt($("#vile option:selected").text())) ||
            parseInt($("#vile option:selected").text()) < parseInt($("#vilb option:selected").text())) {
            $("#vilb").val(parseInt($("#vile option:selected").val()));
        }
    });
    $(document).on('click', hidePopUp);
    if ($('#tyre-menu').length > 0) {
        $('#tyre-menu').on('click', showSubMenu);
    }
    if ($('#disc-menu').length > 0) {
        $('#disc-menu').on('click', showSubMenu);
    }
    if ($('#akb-menu').length > 0) {
        $('#akb-menu').on('click', showSubMenu);
    }
});

function hidePopUp() {
    $('.sub-menu-pp').css('display', 'none');
}

function showSubMenu() {

    var fl = 0;
    var name = $(this).attr('id');
    if ($('#sub-' + name).css('display') == 'none') {
        fl = 1;
    }

    hidePopUp();

    if (fl == 1) {

        var top = $(this).offset().top + $(this).innerHeight();
        var left = $(this).offset().left;
        var width = $(this).innerWidth();

        var subMenu = $('#sub-' + name);
        subMenu.css('display', 'block');
        subMenu.css('min-width', width + 'px');
        subMenu.css('top', top + 'px');
        subMenu.css('left', left + 'px');
    }
    return false;
}

function TrimString(sInString) {
    sInString = sInString.replace(/ /g, ' ');
    return sInString.replace(/(^\s+)|(\s+$)/g, "");
}
function seldelfirst(oSelf) {
    if (oSelf.options[0].value == 0)
        oSelf.options[0] = null;
    return false
}
function CaptchaUpdate(img)
{
    var str = img.src;
    str = str.substr(str.indexOf("d=") + 2);
    if (str == "1")
        str = "2";
    else
        str = "1";
    img.src = "/kcaptcha/index.php?d=" + str;
    return true;
}
function getSession(name) {

    var sBody = 'event=session&name=' + name + '&ajax=1';
    var oXmlHttp = zXmlHttp.createRequest();
    var result = 'false';
    oXmlHttp.open("post", "/callback.php", false);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {

        if (oXmlHttp.readyState == 4) {

            if (oXmlHttp.status == 200) {
                result = TrimString(oXmlHttp.responseText);
            }
        }
    }
    oXmlHttp.send(sBody);
    return result;
}

function DeleteNomen(nomen)
{
    var sBody = 'event=delete&nomen=' + nomen + '&ajax=1';
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("post", "/bask.php", true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {
        if (oXmlHttp.readyState == 4) {
            if (oXmlHttp.status == 200) {

                var ind = oXmlHttp.responseText;
                ind *= 1;
                if (ind) {
                    jQuery('#tovar-' + nomen).css('display', 'none');
                    var allprice = parseInt(jQuery('#sum-' + nomen).text()) * 1;
                    jQuery('#allsum').text(parseInt(jQuery('#allsum').text()) - allprice);
                } else {

                    jQuery('#checkout').css('display', 'none');
                    jQuery('#cart-form').css('display', 'none');
                    jQuery('#itog').css('display', 'none');
                    jQuery('#cart .head a').css('display', 'none');
                    jQuery('#cart').append('<p class="empty-message">В корзине нет ни одного товара.</p>');
                }
                //ShowModalWindow(true,"Информация","Позиция удалена из Вашей корзины",1);
                //TopBasket();
            }
            else
                ShowModalWindow(true, "Обнаружена ошибка", oXmlHttp.statusText, 1);
        }
    }
    oXmlHttp.send(sBody);
    return false;
}
var isIE = window.navigator.userAgent.indexOf("MSIE") > -1;
var GlassWindow = null;
var Dialog = null;
function ShowGlassWindow(show, fl) {
    if (GlassWindow == null) {
        GlassWindow = document.createElement('DIV');
        with (GlassWindow.style) {
            display = 'none';
            position = 'absolute';
            height = 0;
            width = 0;
            zIndex = 1;
            if (isIE) {
                backgroundColor = '#FFFFFF';
                filter = "progid:DXImageTransform.Microsoft.Alpha(Opacity=40, Style=0)";
            }
            else
                backgroundImage = 'url(/images/des/alfa40-fon.png)';
        }
        if (fl == 1)
            GlassWindow.onclick = function () {
                ShowModalWindow(false, "", "", 1);
                return false;
            }
        document.body.appendChild(GlassWindow);
    }
    if (show) {
        var s = getdocumentize();
        with (GlassWindow.style) {
            left = top = 0;
            width = s[0] + 'px';
            height = s[1] + 'px';
        }
    }
    GlassWindow.style.display = show ? 'block' : 'none';
}
function ShowModalWindow(show, head, mes, fl)
{
    ShowGlassWindow(show, 1);
    Dialog = document.getElementById("mes");
    if (show) {
        var c = getClientCenter();
        document.getElementById("m_h").innerHTML = head;
        document.getElementById("m_dv").innerHTML = mes;
        Dialog.style.zIndex = 2;
        Dialog.style.display = 'block';
        var hw = Dialog.offsetWidth / 2;
        var hhe = Dialog.offsetHeight / 2;
        var lft = 0;
        if (c[0] - hw > 0)
            lft = c[0] - hw;
        var rg = 0;
        if (c[1] - hhe > 0)
            rg = c[1] - hhe;
        Dialog.style.left = (lft) + 'px';
        Dialog.style.top = (rg) + 'px';
        Dialog.focus();
    }
    else
        Dialog.style.display = 'none';
}
function ClearNomen()
{
    var sBody = 'event=clear' + '&ajax=1';
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("post", "/bask.php", true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {
        if (oXmlHttp.readyState == 4)
        {
            if (oXmlHttp.status == 200)
            {
                document.getElementById("main").innerHTML = oXmlHttp.responseText;
                TopBasket();
                ShowModalWindow(true, "Информация", "Корзина очищена", 1);
            }
            else
                ShowModalWindow(true, "Обнаружена ошибка", oXmlHttp.statusText, 1);
        }
    }
    oXmlHttp.send(sBody);
    return false;
}
function UpdateNomen(nomen, el)
{
    var cnt = el.value * 1;
    var sBody = 'event=update&nomen=' + nomen + '&cnt=' + cnt + '&ajax=1';
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("post", "/bask.php", true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {
        if (oXmlHttp.readyState == 4) {
            if (oXmlHttp.status == 200) {
                var ind = oXmlHttp.responseText;
                ind *= 1;
                if (ind) {

                    var price = parseInt(jQuery('#price-' + nomen).text()) * 1;
                    var allprice = parseInt(jQuery('#sum-' + nomen).text()) * 1;
                    var delt = (price * cnt) - allprice;
                    jQuery('#allsum').text(parseInt(jQuery('#allsum').text()) + delt);
                    jQuery('#sum-' + nomen).text(price * cnt);
                    //TopBasket();
                }
            }
        }
    }
    oXmlHttp.send(sBody);
    return false;
}
function TopBasket()
{
    var sBody = 'event=top&ajax=1';
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("post", "/bask.php", true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {
        if (oXmlHttp.readyState == 4)
        {
            if (oXmlHttp.status == 200)
            {
                var s = oXmlHttp.responseText;
                var a = s.split('|');
                document.getElementById("tovcnt").innerHTML = "Товаров: " + a[1] + " шт.";
                document.getElementById("tovsum").innerHTML = "На сумму: " + a[0] + " руб.";
            }
        }
    }
    oXmlHttp.send(sBody);
    return false;
}
function getdocumentize() {
    return [document.body.scrollWidth > document.body.offsetWidth ? document.body.scrollWidth : document.body.offsetWidth, document.body.scrollHeight > document.body.offsetHeight ? document.body.scrollHeight : document.body.offsetHeight];
}
function getClientSize() {
    if (document.compatMode == 'CSS1Compat'/* && !window.opera*/)
        return [document.documentElement.clientWidth, document.documentElement.clientHeight];
    else
        return [document.body.clientWidth, document.body.clientHeight];
}
function getdocumentcroll() {
    return [self.pageXOffset || (document.documentElement && document.documentElement.scrollLeft) || (document.body && document.body.scrollLeft), self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop)];
}
function getClientCenter() {
    var sizes = getClientSize();
    var scrl = getdocumentcroll();
    return [parseInt(sizes[0] / 2) + scrl[0], parseInt(sizes[1] / 2) + scrl[1]];
}
function CheckOrder(mark)
{
    var oForm = document.ord;
    var error = 0;
    if (TrimString(oForm.fio.value) == "")
    {
        document.getElementById("p_fio").style.color = "#f00";
        error++;
    }
    else
    {
        document.getElementById("p_fio").style.color = "#000";
    }
    if (TrimString(oForm.tel.value) == "")
    {
        document.getElementById("p_tel").style.color = "#f00";
        error++;
    }
    else
    {
        document.getElementById("p_tel").style.color = "#000";
    }
    if (error)
    {
        document.getElementById('checkout-error').style.display = 'block';
        return false;
    }
    return true;
}
function CheckCallBack() {

    var oForm = document.callback;
    var error = 0;
    var coma = '<br/>';
    document.getElementById('checkout-error').style.display = 'none';
    document.getElementById('checkout-error').innerHTML = 'Необходимо заполнить все обязательные поля: ';
    if (TrimString(oForm.fio.value) == "") {

        document.getElementById('checkout-error').innerHTML += coma + 'Имя.';
        error++;
    }
    if (TrimString(oForm.tel.value) == "") {
        document.getElementById('checkout-error').innerHTML += coma + 'Телефон.';
        error++;
    }
    if (TrimString(oForm.sid.value) == '') {

        document.getElementById('checkout-error').innerHTML += coma + 'Проверочный код.';
        error++;
    }
    if (TrimString(oForm.sid.value) != '' && TrimString(oForm.sid.value) != getSession('captcha_keystring')) {

        if (error > 0) {
            document.getElementById('checkout-error').innerHTML += coma + 'Проверочный код введен не верно.';
        } else {
            document.getElementById('checkout-error').innerHTML = 'Проверочный код введен не верно.';
        }
        error++;
    }
    var reTel = /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/;
    if (!reTel.test(oForm.tel.value)) {

        if (error > 0) {
            document.getElementById('checkout-error').innerHTML += coma + 'Телефон указан не верно.';
        } else {
            document.getElementById('checkout-error').innerHTML = 'Телефон указан не верно.';
        }
        error++;
    }
    var info = oForm.info.value;
    if (info.length > 500) {

        if (error > 0) {
            document.getElementById('checkout-error').innerHTML += coma + 'Ограничение на ввод "Примечания" - 500 символов.';
        } else {
            document.getElementById('checkout-error').innerHTML = 'Ограничение на ввод "Примечания" - 500 символов.';
        }
        error++;
    }

    if (error) {
        document.getElementById('checkout-error').style.display = 'block';
        return false;
    }

    return true;
}
function td__(fl) {
    // link_in_culc_ajax();
    var b1 = document.getElementById('diamc').value * 25.4;
    var b2 = document.getElementById('diamc2').value * 25.4;
    var wd_old = document.getElementById('prfwc').value;
    var a1 = (document.getElementById('prfwc').value * document.getElementById('prfhc').value) / 100;
    var wd_new = document.getElementById('prfwc2').value;
    var a2 = (document.getElementById('prfwc2').value * document.getElementById('prfhc2').value) / 100;

    document.getElementById('w_old').innerHTML = Math.round(wd_old * 10) / 10;
    document.getElementById('w_new').innerHTML = Math.round(wd_new * 10) / 10;
    var res = Math.round((wd_new - wd_old) * 10) / 10;
    if (res > 0)
        document.getElementById('w_raz').innerHTML = "+" + res;
    else
        document.getElementById('w_raz').innerHTML = res;

    document.getElementById('h_old').innerHTML = Math.round(a1 * 10) / 10;
    document.getElementById('h_new').innerHTML = Math.round(a2 * 10) / 10;
    res = Math.round((a2 - a1) * 10) / 10;
    if (res > 0)
        document.getElementById('h_raz').innerHTML = "+" + res;
    else
        document.getElementById('h_raz').innerHTML = res;

    document.getElementById('r_old').innerHTML = Math.round(b1 * 10) / 10;
    document.getElementById('r_new').innerHTML = Math.round(b2 * 10) / 10;
    res = Math.round((b2 - b1) * 10) / 10;
    if (res > 0)
        document.getElementById('r_raz').innerHTML = "+" + res;
    else
        document.getElementById('r_raz').innerHTML = res;

    var c1 = a1 * 2 + b1;
    var c2 = a2 * 2 + b2;
    document.getElementById('rv_old').innerHTML = Math.round(c1 * 10) / 10;
    document.getElementById('rv_new').innerHTML = Math.round(c2 * 10) / 10;
    res = Math.round((c2 - c1) * 10) / 10;
    if (res > 0)
        document.getElementById('rv_raz').innerHTML = "+" + res;
    else
        document.getElementById('rv_raz').innerHTML = res;

    var spd = document.getElementById('speed').value * (a2 * 2 + b2) / (a1 * 2 + b1);
    document.getElementById('new_speed').innerHTML = Math.round(spd * 10) / 10;
    spd = spd - document.getElementById('speed').value;
    res = Math.round(spd * 10) / 10;
    if (res > 0)
        document.getElementById('raz_speed').innerHTML = "+" + res;
    else
        document.getElementById('raz_speed').innerHTML = res;

    res = Math.round((((a2 * 2 + b2) - (a1 * 2 + b1)) / 2) * 10) / 10;
    if (res > 0)
        document.getElementById('klir').innerHTML = "+" + res;
    else
        document.getElementById('klir').innerHTML = res;
    return false;
}
function CheckPodbor() {
    var oForm = document.podbord;
    var error = 0;
    if (oForm.otv.value == 0)
        error++;
    if (oForm.dcko.value == 0)
        error++;
    if (oForm.diamd.value == 0)
        error++;
    if (error) {
        ShowModalWindow(true, "Некоректные данные", "Для подбора дисков необходимо указать параметры, отмеченные звездочкой: количество отверстий, PCD и диаметр.");
        return false;
    }
    return true;
}
function CheckPodborT() {
    var oForm = document.form1;
    var error = 0;
    if (oForm.prfw.value == 0)
        error++;
    if (oForm.prfh.value == 0)
        error++;
    if (oForm.diam.value == 0)
        error++;
    if (error) {
        ShowModalWindow(true, "Некоректные данные", "Для подбора шин необходимо указать параметры, отмеченные звездочкой: ширина, профиль и диаметр.");
        return false;
    }
    return true;
}

function ClearSelect(oSel, n) {

    var dataId = $(oSel).attr('data-id');
    var div1 = $("div[data-id=" + dataId + "]");
    div1.addClass('disabled');

    while (oSel.options.length > n) {

        oSel.options[oSel.options.length - 1] = null;
    }
    $("div[data-id=" + dataId + "] ul li").remove(); // .spec-for-del
    var nodeLi = document.createElement('li');
    nodeLi.appendChild(document.createTextNode("все"));
    $("div[data-id=" + dataId + "] ul").append(nodeLi);
    $("div[data-id=" + dataId + "] .current").text("все");
}

function AddSelectPodb(oSel, response, val) {

    var dataId = $(oSel).attr('data-id');
    var div1 = $("div[data-id=" + dataId + "]");
    div1.removeClass('disabled');
    var ul = $("div[data-id=" + dataId + "] ul");
    var a = response.split('$');
    var j = 0;
    for (var i = 0, l = a.length; i < l; i++) {

        var b = a[i].split('|');
        var node = document.createElement('option');
        var nodeLi = document.createElement('li');
        node.value = TrimString(b[0]);
        node.appendChild(document.createTextNode(TrimString(b[1])));
        nodeLi.appendChild(document.createTextNode(TrimString(b[1])));
        nodeLi.className = 'spec-for-del';
        oSel.appendChild(node);
        ul.append(nodeLi);
        if (TrimString(val) == TrimString(b[1]))
            oSel.selectedIndex = j;
        j++;
    }
}

function calc3() {

    var sBody = 'wid=' + encodeURIComponent(document.getElementById('prfwc3').options[document.getElementById('prfwc3').selectedIndex].value);
    sBody += '&hid=' + encodeURIComponent(document.getElementById('prfhc3').options[document.getElementById('prfhc3').selectedIndex].value);
    sBody += '&rid=' + encodeURIComponent(document.getElementById('diamc3').options[document.getElementById('diamc3').selectedIndex].value);
    sBody += '&clc=3';
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("post", "/calcfn.php", true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {
        if (oXmlHttp.readyState == 4) {

            if (oXmlHttp.status == 200) {

                var res = oXmlHttp.responseText;
                var resArray = res.split('|');
                document.getElementById('ddisc').innerHTML = document.getElementById('diamc3').options[document.getElementById('diamc3').selectedIndex].innerHTML;
                document.getElementById('dminw').innerHTML = resArray[0];
                document.getElementById('dmaxw').innerHTML = resArray[1];
            }
        }
    }
    oXmlHttp.send(sBody);

    return false;
}

function calc2() {

    var sBody = 'wid=' + encodeURIComponent(document.getElementById('prfwc4').options[document.getElementById('prfwc4').selectedIndex].value);
    sBody += '&hid=' + encodeURIComponent(document.getElementById('prfhc4').options[document.getElementById('prfhc4').selectedIndex].value);
    sBody += '&rid=' + encodeURIComponent(document.getElementById('diamc4').options[document.getElementById('diamc4').selectedIndex].value);
    sBody += '&clc=2';
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("post", "/calcfn.php", true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {
        if (oXmlHttp.readyState == 4) {

            if (oXmlHttp.status == 200) {

                var res = oXmlHttp.responseText;
                document.getElementById('usasz').innerHTML = document.getElementById('prfwc4').options[document.getElementById('prfwc4').selectedIndex].innerHTML + ' x ' +
                        document.getElementById('prfhc4').options[document.getElementById('prfhc4').selectedIndex].innerHTML + ' x ' +
                        document.getElementById('diamc4').options[document.getElementById('diamc4').selectedIndex].innerHTML;
                var euroW = parseFloat(document.getElementById('prfhc4').options[document.getElementById('prfhc4').selectedIndex].innerHTML.replace(',', '.')) * 25.4;
                var euroH = (parseFloat(document.getElementById('prfwc4').options[document.getElementById('prfwc4').selectedIndex].innerHTML.replace(',', '.')) -
                        parseFloat(document.getElementById('diamc4').options[document.getElementById('diamc4').selectedIndex].innerHTML)) / 2 / parseFloat(document.getElementById('prfhc4').options[document.getElementById('prfhc4').selectedIndex].innerHTML.replace(',', '.')) * 100;
                document.getElementById('calcsz').innerHTML = Math.round(euroW) + '/' + Math.round(euroH) + ' R' + document.getElementById('diamc4').options[document.getElementById('diamc4').selectedIndex].innerHTML;
                document.getElementById('eurosz').innerHTML = res;
            }
        }
    }
    oXmlHttp.send(sBody);
    return false;
}

function podborSubmit() {

    var frm = document.pauto;
    frm.modif_name.value = frm.modif.options[frm.modif.selectedIndex].text;
    frm.model_name.value = frm.model.options[frm.model.selectedIndex].text;
    frm.vend_name.value = frm.vend.options[frm.vend.selectedIndex].text;
    document.pauto.submit();
}

function podbor_ajax(st)
{
    var frm = document.pauto;
    var sBody = 'step=' + st;
    if (st >= 1)
        sBody += '&firm=' + encodeURIComponent(frm.vend.options[frm.vend.selectedIndex].value);
    if (st >= 2)
        sBody += '&model=' + encodeURIComponent(frm.model.options[frm.model.selectedIndex].value);
    if (st >= 3)
        sBody += '&year=' + encodeURIComponent(frm.year.options[frm.year.selectedIndex].value);
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("post", "/podborfn.php", true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {
        console.log(oXmlHttp.responseText);
        if (oXmlHttp.readyState == 4) {

            if (oXmlHttp.status == 200) {

                if (st == 1) {

                    ClearSelect(frm.model, 0);
                    var node = document.createElement('option');
                    node.value = TrimString("0");
                    node.appendChild(document.createTextNode("выберите модель"));
                    frm.model.appendChild(node);
                    AddSelectPodb(frm.model, oXmlHttp.responseText, "0");
                    frm.model.selectedIndex = 0;
                    frm.model.disabled = "";
                    //jQuery('#model-lable').removeClass('disable');
                    frm.year.disabled = "disabled";
                    //jQuery('#year-lable').appendClass('disable');
                    ClearSelect(frm.year, 0);
                    var node = document.createElement('option');
                    node.value = TrimString("0");
                    node.appendChild(document.createTextNode("выберите год"));
                    frm.year.appendChild(node);
                    frm.year.selectedIndex = 0;
                    frm.modif.disabled = "disabled";
                    //jQuery('#modif-lable').appendClass('disable');
                    ClearSelect(frm.modif, 0);
                    var node = document.createElement('option');
                    node.value = TrimString("0");
                    node.appendChild(document.createTextNode("выберите модификацию"));
                    frm.modif.appendChild(node);
                    frm.modif.selectedIndex = 0;
                }
                if (st == 2)
                {
                    ClearSelect(frm.year, 0);
                    var node = document.createElement('option');
                    node.value = TrimString("0");
                    node.appendChild(document.createTextNode("выберите год"));
                    frm.year.appendChild(node);
                    AddSelectPodb(frm.year, oXmlHttp.responseText, "0");
                    frm.year.disabled = "";
                    frm.year.selectedIndex = 0;
                    frm.modif.disabled = "disabled";
                    ClearSelect(frm.modif, 0);
                    var node = document.createElement('option');
                    node.value = TrimString("0");
                    node.appendChild(document.createTextNode("выберите модификацию"));
                    frm.modif.appendChild(node);
                    frm.modif.selectedIndex = 0;
                }
                if (st == 3)
                {
                    ClearSelect(frm.modif, 0);
                    var node = document.createElement('option');
                    node.value = TrimString("0");
                    node.appendChild(document.createTextNode("выберите модификацию"));
                    frm.modif.appendChild(node);
                    var s = oXmlHttp.responseText;
                    AddSelectPodb(frm.modif, s, "0");
                    frm.modif.disabled = "";
                }
            }
            else
                saveResult("Обнаружена ошибка " + oXmlHttp.statusText);
        }
    }
    oXmlHttp.send(sBody);
    return false;
}

function setOtherImage(imgName, idColor, idBrand, idModel, t2tr, nameModel, nameBrand) {

    var sBody = 'fl=3&imgName=' + imgName + '&idColor=' + idColor + '&idBrand=' + idBrand +
            '&idModel=' + idModel + '&t2tr=' + t2tr + '&nameModel=' + nameModel + '&nameBrand=' + nameBrand;
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("post", "/fncaj.php", true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {

        if (oXmlHttp.readyState == 4) {

            if (oXmlHttp.status == 200) {

                document.getElementById('bimage').src = oXmlHttp.responseText;
            }
        }
    }
    oXmlHttp.send(sBody);
    return false;
}

function ShowZoomWindow(show, head, mes)
{
    ShowGlassWindowNew(show, 2);
    if (!show) {
        Dialog.style.display = 'none';
        document.body.removeChild(Dialog);
        return false;
    }
    var sBody = 'fl=2' + '&img=' + mes;
    var oXmlHttp = zXmlHttp.createRequest();
    oXmlHttp.open("post", "/fncaj.php", true);
    oXmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    oXmlHttp.onreadystatechange = function () {
        if (oXmlHttp.readyState == 4)
        {
            if (oXmlHttp.status == 200)
            {
                var max = getClientSize();
                var a = oXmlHttp.responseText.split('|');
                Dialog = document.createElement('DIV');
                Dialog.id = "zoommes";
                a[0] *= 1;
                a[0] += 10;
                var wd = a[0] * 1 + 6 * 2;
                a[1] *= 1;
                a[1] += 10;
                var he = a[1] * 1 + 38 + 51;
                if (max[1] < he)
                {
                    wd = ((max[1] - 10) * wd) / he;
                    he = max[1] - 10;

                }
                with (Dialog.style) {
                    width = wd + 'px';
                    height = he + 'px';
                }
                var htop = document.createElement('DIV');
                htop.className = "top";
                htop.style.width = wd + 'px';
                var htopr = document.createElement('DIV');
                htopr.className = "left";
                htop.appendChild(htopr);
                htopr = document.createElement('DIV');
                htopr.className = "mid1";
                var wd1 = wd - 76;
                htopr.style.width = wd1 + 'px';
                htop.appendChild(htopr);
                htopr = document.createElement('DIV');
                htopr.className = "right";
                var htopa = document.createElement('A');
                htopa.href = "#";
                htopa.onclick = function () {
                    return ShowZoomWindow(false, '', '')
                }
                htopr.appendChild(htopa);
                htop.appendChild(htopr);
                Dialog.appendChild(htop);
                htop = document.createElement('DIV');
                htop.className = "leftbord";
                htop.style.height = (he - 38 - 51) + 'px';
                Dialog.appendChild(htop);
                htop = document.createElement('DIV');
                htop.className = "mid";
                htopr.id = "zoomimg";
                htop.style.width = (wd - 12) + 'px';
                htop.style.height = (he - 38 - 51) + 'px';
                if (he >= (a[1] * 1 + 38 + 51))
                    htop.style.background = "url(" + mes + ") no-repeat center center #fff";
                else
                    htop.style.background = "url(/img_resize.php?newh=" + (he - 38 - 51 - 10) + "&infile=" + encodeURIComponent(mes) + ") no-repeat center center #fff";
                Dialog.appendChild(htop);
                htop = document.createElement('DIV');
                htop.className = "rightbord";
                htop.style.height = (he - 38 - 51) + 'px';
                Dialog.appendChild(htop);
                htop = document.createElement('DIV');
                htop.className = "bot";
                htop.style.width = wd + 'px';
                htopr = document.createElement('DIV');
                htopr.className = "left";
                htop.appendChild(htopr);
                htopr = document.createElement('DIV');
                htopr.className = "mid1";
                htopr.id = "zoomhd";
                htopr.innerHTML = head;
                wd1 = wd - 30;
                htopr.style.width = wd1 + 'px';
                htop.appendChild(htopr);
                htopr = document.createElement('DIV');
                htopr.className = "right";
                htop.appendChild(htopr);
                Dialog.appendChild(htop);
                Dialog.style.display = 'block';
                document.body.appendChild(Dialog);
                var c = getClientCenter();
                var hw = Dialog.offsetWidth / 2;
                var hhe = Dialog.offsetHeight / 2;
                var lft = 0;
                if (c[0] - hw > 0)
                    lft = c[0] - hw;
                var rg = 0;
                if (c[1] - hhe > 0)
                    rg = c[1] - hhe;
                Dialog.style.left = (lft) + 'px';
                Dialog.style.top = (rg) + 'px';
                Dialog.focus();
            }
            else
                ShowModalWindow(true, "Обнаружена ошибка", oXmlHttp.statusText);
        }
    }
    oXmlHttp.send(sBody);
    return false;
}
function ShowGlassWindowNew(show, fl) {
    if (GlassWindow == null) {
        GlassWindow = document.createElement('DIV');
        with (GlassWindow.style) {
            display = 'none';
            position = 'absolute';
            height = 0;
            width = 0;
            zIndex = 1;
            if (isIE) {
                backgroundColor = '#FFF';
                filter = "progid:DXImageTransform.Microsoft.Alpha(Opacity=40, Style=0)";
            }
            else
                backgroundImage = 'url(/images/des/alfa40-fon.png)';
        }
        if (fl == 1)
            GlassWindow.onclick = function () {
                ShowModalWindow(false, "", "");
                return false;
            }
        if (fl == 2)
            GlassWindow.onclick = function () {
                ShowZoomWindow(false, "", "");
                return false;
            }
        document.body.appendChild(GlassWindow);
    }
    if (show) {
        var s = getdocumentize();
        with (GlassWindow.style) {
            left = top = 0;
            width = s[0] + 'px';
            height = s[1] + 'px';
        }
    }
    GlassWindow.style.display = show ? 'block' : 'none';
}

function clearTyres() {

    clearSelect1("prfw");
    clearSelect1("prfh");
    clearSelect1("diam");
    clearSelect1("seas");
    $("#tyre_price_from").val("");
    $("#tyre_price_to").val("");
    $("#tyres input:checked").parent().find('span').removeClass('checked');
    $("#tyres input:checked").removeAttr('checked');
    return false;
}

function clearDiscs() {

    clearSelect1("diamd");
    clearSelect1("widthd");
    clearSelect1("pcd");
    clearSelect1("vilb");
    clearSelect1("stup");
    $("#disc_price_from").val("");
    $("#disc_price_to").val("");
    $("#discs input:checked").parent().find('span').removeClass('checked');
    $("#discs input:checked").removeAttr('checked');
    return false;
}

function clearAkb() {

    clearSelect1("volume");
    clearSelect1("volumeFrom");
    clearSelect1("volumeTo");
    clearSelect1("volt");
    clearSelect1("rvrt");
    clearSelect1("klem");
    $("#akb_price_from").val("");
    $("#akb_price_to").val("");
    return false;
}

function clearSelect1(id) {

    var select = $("#" + id);
    select.val("0");
    var dataId = select.attr('data-id');
    $("div[data-id=" + dataId + "] a.current").text($("#" + id + " :selected").text());
    $("div[data-id=" + dataId + "] ul li").removeClass();
    $("div[data-id=" + dataId + "] ul li:contains('" + $("#" + id + " :selected").text() + "')").addClass('selected');
}