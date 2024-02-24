<?php
  $html=file_get_contents('http://www.am-shina.ru/');
  preg_match_all("#<link(.*)/>#i",$html,$posit,PREG_SET_ORDER);
  $ln=sizeof($posit);
  $i=0;
  echo "<table border=1>";
  while($i<$ln)
  {
    echo "<tr>";
    if($posit[$i][1])
    {

      preg_match_all("#rel=\"?([\w\/\.]*)\"?#i",$posit[$i][1],$rel,PREG_SET_ORDER);
      echo "<td>".$rel[0][1]."</td>";
      preg_match_all("#type=\"?([\w\/\.]*)\"?#i",$posit[$i][1],$type,PREG_SET_ORDER);
      echo "<td>".$type[0][1]."</td>";
      preg_match_all("#href=\"?([\w\/\.]*)\"?#i",$posit[$i][1],$href,PREG_SET_ORDER);
      echo "<td>".$href[0][1]."</td>";
      if($type[0][1]=="text/css") $csshref=$href[0][1];
    }
    echo "</tr>";
    $i++;
  }
  echo "</table>";
  preg_match_all("#\sid=\"?([a-zA-Z0-9_\-]+)\"?[\s>]#i",$html,$id,PREG_SET_ORDER);
  $ln=sizeof($id);
  $i=0;
  echo "<table border=1>";
  while($i<$ln)
  {
    echo "<tr>";
    if($id[$i][1])
      echo "<td>".$id[$i][1]."</td>";
    echo "</tr>";
    $i++;
  }
  echo "</table>";
  preg_match_all("#\sclass=\"?([a-zA-Z0-9_\-\s]+)\"?[\s>]#i",$html,$classes,PREG_SET_ORDER);
  $ln=sizeof($classes);
  $i=0;
  echo "<table border=1>";
  while($i<$ln)
  {
    echo "<tr>";
    if($classes[$i][1])
      echo "<td>".$classes[$i][1]."</td>";
    echo "</tr>";
    $i++;
  }
  echo "</table>";
  $css=file_get_contents('http://www.am-shina.ru'.$csshref);
  //$css="html{text-align: center;background:#d9eafa}";
  preg_match_all("#(([a-zA-Z0-9\.\#\s_\,]+)\s?({[a-zA-Z0-9\.\#:;\/\s\(\)-]+}))#i",$css,$css_ar,PREG_SET_ORDER);
  $ln=sizeof($css_ar);
  $i=0;
  echo "<table border=1>";
  while($i<$ln)
  {
    echo "<tr>";
    if($css_ar[$i][1])
      echo "<td>".$css_ar[$i][2]."</td><td>".$css_ar[$i][3]."</td>";
    echo "</tr>";
    $i++;
  }
  echo "</table>";
?>