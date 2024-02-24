<?php
  if($group>4)
  {
    Header("Location: /error/dostup/");
    exit;
  }
  if(isset($_POST["add"]))
  {
    $res=mysql_query("select count(*) as cnt from deskr where idmod=".$_POST["model"]." and idbrnd=".$_POST["brand"]);
    $rs=mysql_fetch_object($res);
    if($rs->cnt>0)
      mysql_query("update deskr set deskr_text='".$_POST["desc"]."' where idmod=".$_POST["model"]." and idbrnd=".$_POST["brand"]);
    else
      mysql_query("insert into deskr (deskr_text,deskr_tov_id,idmod,idbrnd) values ('".$_POST["desc"]."',".$_POST["tvid"].",".$_POST["model"].",".$_POST["brand"].")");
    //$big_id=mysql_insert_id();
    //mysql_query("update total set id_text=".$big_id." where tab4_id=".$_POST["model"]);
    $str.="<p>Новое описание добавленно.</p>";
  }
  $tov=IdByName($arg[0],"tab1","tb1_id","translit");
  $str.="<form action=\"\" method=\"post\" name=\"adddesk\" id=\"deskr\"><table><tr><td><select name=\"brand\"><option value=\"0\">не указан</option>";
  $res=mysql_query("select tb3_id,tb3_nm from tab3 where tb3_tov_id=".$tov." and wrk3=1 order by tb3_nm");
  while($mod=mysql_fetch_object($res))
    $str.="<option value=\"".$mod->tb3_id."\">".$mod->tb3_nm."</option>";
  $str.="</select><select name=\"model\"><option value=\"0\">не указана</option>";
  $res=mysql_query("select tb4_id,tb4_nm from tab4 where tb4_tov_id=".$tov." and wrk4=1 order by tb4_nm");
  while($mod=mysql_fetch_object($res))
    $str.="<option value=\"".$mod->tb4_id."\">".$mod->tb4_nm."</option>";
  $str.="</select></td></tr><tr><td><input type=\"hidden\" name=\"tvid\" value=\"".$tov."\" /><textarea name=\"desc\"></textarea></td></tr><tr><td><input type=\"submit\" name=\"add\" value=\"Добавить\" /></td></tr></table></form>";
?>