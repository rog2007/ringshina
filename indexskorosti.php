<?php
  $tit="Индекс скорости";
  $desk="Индекс скорости для автомобильных шин";
  $kw="Индекс скорости";
  //$h1="Индекс скорости";
  $res=mysql_query("select txt from pages where pg='".$page."'");
  if($rs=mysql_fetch_object($res))
  $str.=$rs->txt;
  /*$str="<h1>".$h1."</h1>
  <div class=\"statbl\">
  <table class=\"bord\">
        <tr width=\"800\" height=\"25\">
            <td width=\"400\" class=\"bord\" style=\"background-color: rgb(198, 200, 193);\" align=\"center\"><strong>Индекс скорости</strong></td>
            <td width=\"400\" class=\"bord\" style=\"background-color: rgb(198, 200, 193);\" align=\"center\"><strong>Предельная скорость в км/ч</strong></td>
        </tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>B</strong></td><td class=\"bord\" align=\"center\"><strong>50</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>C</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>60</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>D</strong></td><td class=\"bord\" align=\"center\"><strong>65</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>E</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>70</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>F</strong></td><td class=\"bord\" align=\"center\"><strong>80</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>G</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>90</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>J</strong></td><td class=\"bord\" align=\"center\"><strong>100</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>K</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>110</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>L</strong></td><td class=\"bord\" align=\"center\"><strong>120</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>M</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>130</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>N</strong></td><td class=\"bord\" align=\"center\"><strong>140</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>P</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>150</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>Q</strong></td><td class=\"bord\" align=\"center\"><strong>160</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>R</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>170</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>S</strong></td><td class=\"bord\" align=\"center\"><strong>180</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>T</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>190</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>U</strong></td><td class=\"bord\" align=\"center\"><strong>200</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>H</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>210</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>VR</strong></td><td class=\"bord\" align=\"center\"><strong>>210</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>V</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>240</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>Z, ZR</strong></td><td class=\"bord\" align=\"center\"><strong>>240</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>W</strong></td><td class=\"bord\" style=\"background-color: rgb(238, 220, 197);\" align=\"center\"><strong>270</strong></td></tr>
        <tr height=\"25\"><td class=\"bord\" align=\"center\"><strong>Y</strong></td><td class=\"bord\" align=\"center\"><strong>300</strong></td></tr>

  </table>

  </div>";*/
?>