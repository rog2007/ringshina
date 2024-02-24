<?php       
if($arg[0]=="all")
{
  $str.="<h1>Новости</h1>";
  if(!$arg[1]) $crpg=1;
  else $crpg=$arg[1];
  $res=mysql_query("select count(*) as cn from news");
  if($rnews=mysql_fetch_object($res))
    $pgnc=ceil($rnews->cn/10);
  $strtmp="";
  if($pgnc>1)
  {
    $strtmp.="<div class=\"page\">";
    for($i=1;$i<=$pgnc;$i++)
      if ($i==$crpg) $strtmp.="<span>$i</span>";
      else $strtmp.="<a href='/news/all/$i.html'>$i</a>";
    $strtmp.="</div>";
  }
  $str.=$strtmp;
  $fst=($crpg-1)*10;
  $lim=" limit ".$fst.",10";
  $resT = mysql_query("select id,date,preview,title from news order by date desc".$lim);
  while($nw=mysql_fetch_object($resT))
    $str.="<div class=\"onebl\"><div class=\"dt\">".normalize_mysqldate($nw->date)."</div><a href=\"/news/".$nw->id.".html\" class=\"hd\">".$nw->title."</a><div class=\"prev\">".$nw->preview." <a href=\"/news/".$nw->id.".html\">Читать далее...</a></div></div>";
  $str.=$strtmp;
}
else
{
  $resT = mysql_query("select id,date,content,title,img from news where id=".$arg[0]);
  if($nw=mysql_fetch_object($resT))
  {
    $str.="<h1>".$nw->title."</h1><div class=\"onebl1\"><div class=\"dt\">".normalize_mysqldate($nw->date)."</div>";
    if($nw->img!="blank.gif" && trim($nw->img)!="")
      $str.="<img src=\"".$nw->img."\" class=\"newimg\" />";
    $str.="<div class=\"prev\">".$nw->content."</div></div>";
  }
  $str.="<div style=\"width:100%;float:left;overflow:hidden;position:relative;margin-top:15px\"><a href=\"/news/all/1.html\" style=\"font:14px Tahoma;color:#00618b\">Все новости</a></div>";
}


?>