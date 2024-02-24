<?php
  $title="Контактная информация";
  $descr="Адрес и телефон магазина buy-tyres.ru";
  $keywords="контакты";
  $content .= '<div id="bread">
    <a href="/">Главная</a>
    <span>&gt;</span>
    <a href="/contacts.html">Контакты</a>
  </div>';
  $res=mysql_query("select txt from pages where pg='".$page."'");
  if($rs=mysql_fetch_object($res))
  $content .= $rs->txt;
 /* $h1="Наш адрес";
  $str="<h1>".$h1."</h1>
  <div class=\"statbl\">ИП Бакун И.Ю.<br/>ОГРН-311501823700050<br/>Фактический адрес: <b>141070</b>, Московская обл. г. Королёв, ул. Фрунзе, д.1Д., Корпус 2.<br/><b>Тел.</b> (495) 589-99-79<br/></div>

  <div class=\"statbl\"><div class=\"bord\">

<script src=\"http://api-maps.yandex.ru/1.1/?key=AN97Q04BAAAAbMRAEgIA4ApEQZrLafzeGttDyBKexf3eXRsAAAAAAAAAAAD19U16nx06ZHJJOPR2cjUhwaULBA==&modules=pmap&wizard=constructor\" type=\"text/javascript\"></script>
<script type=\"text/javascript\">
    YMaps.jQuery(window).load(function () {
        var map = new YMaps.Map(YMaps.jQuery(\"#YMapsID-4939\")[0]);
        map.setCenter(new YMaps.GeoPoint(37.824382,55.924931), 15, YMaps.MapType.MAP);
        map.addControl(new YMaps.Zoom());
        map.addControl(new YMaps.ToolBar());
        YMaps.MapType.PMAP.getName = function () { return \"Народная\"; };
        map.addControl(new YMaps.TypeControl([
            YMaps.MapType.MAP,
            YMaps.MapType.SATELLITE,
            YMaps.MapType.HYBRID,
            YMaps.MapType.PMAP
        ], [0, 1, 2, 3]));

        YMaps.Styles.add(\"constructor#pmlbmPlacemark\", {
            iconStyle : {
                href : \"http://api-maps.yandex.ru/i/0.3/placemarks/pmlbm.png\",
                size : new YMaps.Point(28,29),
                offset: new YMaps.Point(-8,-27)
            }
        });

       map.addOverlay(createObject(\"Placemark\", new YMaps.GeoPoint(37.823974,55.923835), \"constructor#pmlbmPlacemark\", \"Buy-Tyres.ru\"));

        function createObject (type, point, style, description) {
            var allowObjects = [\"Placemark\", \"Polyline\", \"Polygon\"],
                index = YMaps.jQuery.inArray( type, allowObjects),
                constructor = allowObjects[(index == -1) ? 0 : index];
                description = description || \"\";

            var object = new YMaps[constructor](point, {style: style, hasBalloon : !!description});
            object.description = description;

            return object;
        }
    });
</script>

<div id=\"YMapsID-4939\" style=\"width:640px;height:450px\"></div>


  </div></div>";*/
?>