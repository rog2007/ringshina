<?php
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


?>