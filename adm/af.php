<?php
  header("Content-type: text/plain; charset=windows-1251");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  require ("connect.php");
  function IdByName($nm,$tbl,$field_id,$field_name)
  {
    $sql="select {$field_id} from {$tbl} where {$field_name}='{$nm}'";
    $result=mysql_query($sql);
    if (@mysql_num_rows($result)==0) return 0;
    else return @mysql_result($result,0,$field_id);
  }
  $new = iconv('UTF-8', 'windows-1251', $_POST['new']);
  if ($_POST["old"]==0)
  {
    switch($_POST["type"])
    {
      case 3:
        mysql_query("insert into tab3(tb3_nm,tb3_tov_id) values ('".$new."',1)");
        $new_id=mysql_insert_id();
        mysql_query("update power set t3=".$new_id." where brand='".$new."'");
      case 4:
        mysql_query("insert into tab4(tb4_nm,tb4_tov_id) values ('".$new."',1)");
        $new_id=mysql_insert_id();
        mysql_query("update power set t4=".$new_id." where model='".$new."'");
    }
    echo $new_id;
  }
  else
  {
    $oldname=IdByName($_POST["old"],"tab".$_POST["type"],"tb".$_POST["type"]."_nm","tb".$_POST["type"]."_id");
    switch($_POST["type"])
    {
      case 3:
        mysql_query("insert into synonims(nm,snm,tp,t4_id) values ('".$oldname."','".$new."',3,".$_POST["old"].")");
        mysql_query("update power set t3=".$_POST["old"]." where brand='".$new."'");
      case 4:
        mysql_query("insert into synonims(nm,snm,tp,t4_id) values ('".$oldname."','".$new."',1,".$_POST["old"].")");
        mysql_query("update power set t4=".$_POST["old"]." where model='".$new."'");
    }
    echo $_POST["old"];
  }

?>