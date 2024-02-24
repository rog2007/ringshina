<?php
function PagesCreate($link,$all_cn,$crpg)
{
  $strtmp="<div class=\"sp-pages\">";
  if($all_cn<8)
  {
    for($i=1;$i<=$all_cn;$i++)
    {
      if ($i==$crpg) $strtmp.="<span>$i</span>";
      else $strtmp.="<a href='".$link."&curpage=$i'>$i</a>";
    }
  }
  if($all_cn>=8)
  {
    if($crpg<4)
    {
      for($i=1;$i<=5;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."&curpage=$i'>$i</a>";
      }
      $strtmp.="<span>...</span><a href='".$link."&curpage=$all_cn'>$all_cn</a>";
    }
    if($crpg==4)
    {
      for($i=1;$i<=6;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."&curpage=$i'>$i</a>";
      }
      $strtmp.="<span>...</span><a href='".$link."&curpage=$all_cn'>$all_cn</a>";
    }
    if($crpg>$all_cn-3)
    {
      $strtmp.="<a href='".$link."&curpage=1'>1</a><span>...</span>";
      for($i=$all_cn-4;$i<=$all_cn;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."&curpage=$i'>$i</a>";
      }
    }
    if($crpg==$all_cn-3)
    {
      $strtmp.="<a href='".$link."&curpage=1'>1</a><span>...</span>";
      for($i=$all_cn-5;$i<=$all_cn;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."&curpage=$i'>$i</a>";
      }
    }
    if($crpg<$all_cn-3 && $crpg>4)
    {
      $strtmp.="<a href='".$link."&curpage=1'>1</a><span>...</span>";
      for($i=$crpg-2;$i<=$crpg+2;$i++)
      {
        if ($i==$crpg) $strtmp.="<span>$i</span>";
        else $strtmp.="<a href='".$link."&curpage=$i'>$i</a>";
      }
      $strtmp.="<span>...</span><a href='".$link."&curpage=$all_cn'>$all_cn</a>";
    }
  }
  $strtmp.="</div>";
  return $strtmp;
}

function IdByName($nm,$tbl,$field_id,$field_name)
  {
    $sql="select {$field_id} from {$tbl} where {$field_name}='{$nm}'";
    //echo $sql;
    $result=mysql_query($sql);
    //echo "\n {$nm}, ".@mysql_result($result,0,$field_id);
    if (@mysql_num_rows($result)==0)
      return 0;
    else
      return @mysql_result($result,0,$field_id);
  }
// /константы ==================================================================
//if($grtp!=1) {Header("Location: /error/dostup/"); exit;}
$sql="";
$tov_nm="";
$h1="";
$h1=$tov_nm;
$ftip_nm="";
// функция генерация <select> ==================================================
  function SelectStr($query,$selname,$id,$fl)
  {
    $res=mysql_query($query);
    $tmpstr='<select name="'.$selname.'">'.($fl==1?'<option value="0"'.(!$id?'selected="selected"':'').'>Не указан</option>':'');
    while($rs_sel=mysql_fetch_object($res))
      $tmpstr.='<option value="'.$rs_sel->id.'"'.($id==$rs_sel->id?'selected="selected"':'').'>'.$rs_sel->nm.'</option>';
    $tmpstr.='</select>';
    return $tmpstr;
  }
// /функция генерация <select> ==================================================

if (!isset($_GET["curpage"]) || !trim($_GET["curpage"])){

  $_GET['curpage'] = 1;
}

$sqlArray = array();
if (isset($_GET["cname"]) && trim($_GET["cname"])){

  array_push($sqlArray, "cust_name like '%" . $_GET["cname"] . "%'");
}

if (isset($_GET["ctel"]) && trim($_GET["ctel"])){

  array_push($sqlArray, "cust_tel like '%" . $_GET["ctel"] . "%'");
}

if (isset($_GET["cmail"]) && trim($_GET["cmail"])){

  array_push($sqlArray, "cust_mail like '%" . $_GET["cmail"] . "%'");
}


$str .= '<table><tr><td style="width:140px;vertical-align:top"></td><td style="vertical-align:top">
<div id="order-filter">
  <form method="get" name="order" action="/adm/orders/">
    <div>
      <table>
        <tr>
          <td><div>Имя</div><input type="text" value="' . $_GET['cname'] . '" name="cname" /></td>
          <td><div>Телефон</div><input type="text" value="' . $_GET['ctel'] . '" name="ctel" /></td>
          <td><div>Email</div><input type="text" value="' . $_GET['cmail'] . '" name="cmail" /></td>
        </tr>
        <tr>
          <td><div>Дата от (день/месяц/год)</div><input type="text" value="' . $_GET['fdate'] . '" name="fdate" /></td>
          <td><div>Дата до (день/месяц/год)</div><input type="text" value="' . $_GET['tdate'] . '" name="tdate" /></td>
          <td></td>
        </tr>
        <tr>
          <td></td>
          <td></td>
          <td class="button"><input type="submit" name="pst" value="Фильтровать" /></td>
        </tr>
      </table>
    </div>
  </form></div>';
$str .= ' <h1>Заказы</h1>';

$where = '';
if(count($sqlArray) > 0){

  $where = ' WHERE ' . implode(' and ', $sqlArray);
}

$result=mysql_query("SELECT count(*) as cn FROM order_doc" . $where);

$allcn=0;
if($rs=mysql_fetch_object($result)) $allcn=ceil($rs->cn/100);
if($allcn>1)
{
  $lnkk = '/adm/orders/?cname=' . $_GET['cname'] . '&ctel=' . $_GET['ctel'] .
      '&cmail=' . $_GET['cmail'] . '&fdate=' . $_GET['fdate'] . '&tdate=' . $_GET['tdate'] .
      '&pst=' . $_GET['pst'];
  $strZak=PagesCreate($lnkk, $allcn, $_GET["curpage"]);
}

$str .=  $strZak . '<div id="orders-result">
  <table>
    <tr class="head">
      <td>Доб</td>
      <td class="idtov">ID</td>
      <td class="name">Имя</td>
      <td>Телефон</td>
      <td>Email</td>
      <td>Дата</td>
      <td>Время</td>
      <td>Статус</td>
      <td>Удалить</td>
    </tr>
    ';

  $sql = "SELECT * from order_doc " . $where ." order by ord_date desc, ord_time desc limit " . (($_GET["curpage"]-1)*100) . ",100";
  $result=mysql_query($sql);
  $nomid=0;
while($nom = mysql_fetch_object($result)){

  $str.="<tr class=\"skld\"><td style='width:30px'><a href=\"/adm/order-edit/".$nom->big_id."/\">Просмотр</a></td>
    <td>".$nom->big_id."</td>
    <td>".$nom->cust_name."</td>
    <td>".$nom->cust_tel."</td>
    <td>".$nom->cust_mail."</td>
    <td>".normalize_mysqldate_new($nom->ord_date). "</td>
    <td>".$nom->ord_time."</td>
    <td>" . ($nom->status == 1 ? 'новый' : '' ) . ($nom->status == 2 ? 'просмотрен' : '' ) . "</td>
    <td><a href=\"/adm/order-edit/".$nom->big_id."/del/\">Уд</a>
    </tr>";
} //<td><a href=\"/adm/sp-tov-edit/".$tov."/".$nom->big_id."/del/\">Уд</a></td>

$str.='</table>';
$str.="</div>" . $strZak;

$str.='</td></tr></table>';
?>