<?php
 require ("connect.php");

 //die;





/* update url field in tab3 table*/

/*echo 'update url field in tab3 table - begin....<br/>';
$selSeas = $dbcon->prepare('select tb3_id as id, tb3_nm as name from tab3');
if ($selSeas->execute() && $selSeas->rowCount() > 0) {
  while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {

    //echo $resObj->name . '<br/>';
    $id = $resObj->id;
    $url = rus3lat($resObj->name);
    //echo $url . '<br/>';
    $test = $dbcon->prepare("select count(tb3_id) as cnt from tab3 where tb3_id <> :id and url=:url");
    $test->bindParam(':id',$id);
    $test->bindParam(':url',$url);

    if ($test->execute() && $test->rowCount() > 0) {

      $testObj = $test->fetch(PDO::FETCH_OBJ);

      //echo $testObj->cnt . '<br/>';

      if($testObj->cnt == 0){

        $update = $dbcon->prepare("update tab3 set url=:url where tb3_id = :id");
        $update->bindParam(':id',$resObj->id);
        $update->bindParam(':url',$url);
        $res = $update->execute();

        echo '”спешно обновлено - ' . $resObj->name . '(' . $res . ')<br/>';

      } else {

        echo 'Url существует - ' . $resObj->name . '<br/>';
      }
    } else {
      echo "\nPDOStatement::errorInfo():\n";
$arr = $test->errorInfo();
print_r($arr);
    }
  }
}
echo 'update url field in tab3 table - end.<br/>';*/
/* update url field in tab3 table*/

/* update url field in tab4 table*/
/*echo 'update url field in tab4 table - begin....<br/>';
$selSeas = $dbcon->prepare('select tb4_id as id, tb4_nm as name, brand_id from tab4');
if ($selSeas->execute() && $selSeas->rowCount() > 0) {
  while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {

    //echo $resObj->name . '<br/>';
    $id = $resObj->id;
    $url = rus3lat($resObj->name);
    //echo $url . '<br/>';
    $test = $dbcon->prepare("select count(*) as cnt from tab4 where tb4_id <> :id and url=:url and brand_id=:br");
    $test->bindParam(':id',$id);
    $test->bindParam(':url',$url);
    $test->bindParam(':br',$resObj->brand_id);

    if ($test->execute() && $test->rowCount() > 0) {

      $testObj = $test->fetch(PDO::FETCH_OBJ);

      //echo $testObj->cnt . '<br/>';

      if($testObj->cnt == 0){

        $update = $dbcon->prepare("update tab4 set url=:url where tb4_id = :id");
        $update->bindParam(':id',$resObj->id);
        $update->bindParam(':url',$url);
        $res = $update->execute();

        echo '”спешно обновлено - ' . $resObj->name . '(' . $res . ')<br/>';

      } else {

        echo 'Url существует - ' . $resObj->name . '<br/>';
      }
    } else {
      echo "\nPDOStatement::errorInfo():\n";
$arr = $test->errorInfo();
print_r($arr);
    }
  }
}
echo 'update url field in tab4 table - end.<br/>'; */
/* update url field in tab4 table*/
/* update total*/  /*
echo 'update url field in total table - begin....<br/>';
$selSeas = $dbcon->prepare("SELECT total_id, all_name as name, tab3.url as t3url, tab4.url as t4url, profw.name as wprof,
profh.name as hprof, tb6_nm, tb7_nm, tb8_nm, omolog
FROM `total`
left join tab3 on tb3_id = tab3_id
left join tab4 on tb4_id = tab4_id
left join profw on profw.id = w_id
left join profh on profh.id = h_id
left join tab6 on tb6_id = tab6_id
left join tab7 on tb7_id = tab7_id
left join tab8 on tb8_id = tab8_id
WHERE tab1_id = 1 and total.url = ''");
if ($selSeas->execute() && $selSeas->rowCount() > 0) {
  while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {

    //echo $resObj->name . '<br/>';
    $id = $resObj->total_id;
    $url = $resObj->t3url . '_' . $resObj->t4url . '_' . ($resObj->omolog?'_' . strtolower($resObj->omolog):'') .
      $resObj->wprof . '_' . ($resObj->hprof?$resObj->hprof . '_':'') . strtolower($resObj->tb6_nm) . ($resObj->tb7_nm?'_' . strtolower(str_ireplace('/','_',$resObj->tb7_nm)):'') .
      ($resObj->tb8_nm?'_' . strtolower($resObj->tb8_nm):'');
    //echo $url . '<br/>';
    echo 'test - ' . $resObj->name . ' - ' . $url . '<br/>';
    $test = $dbcon->prepare("select count(*) as cnt from total where total_id <> :id and url=:url");
    $test->bindParam(':id',$id);
    $test->bindParam(':url',$url);

    if ($test->execute() && $test->rowCount() > 0) {

      $testObj = $test->fetch(PDO::FETCH_OBJ);

      //echo $testObj->cnt . '<br/>';

      if($testObj->cnt == 0){

      echo '”спешно обновлено - ' . $resObj->name . ' - ' . $url . '<br/>';
        $update = $dbcon->prepare("update total set url=:url where total_id = :id");
        $update->bindParam(':id',$id);
        $update->bindParam(':url',$url);
        $res = $update->execute();



      } else {

        echo 'Url существует - ' . $resObj->name . '<br/>';
      }
    } else {
      echo "\nPDOStatement::errorInfo():\n";
$arr = $test->errorInfo();
print_r($arr);
    }
  }
}
echo 'update url field in total table - end.<br/>';   */

echo 'update url field in total -d table - begin....<br/>';
$selSeas = $dbcon->prepare("SELECT total_id, all_name as name, tab3.url as t3url, tab4.url as t4url,
tb5_nm, tb6_nm, tb7_nm, tb8_nm,tb9_nm,tb2_nm,tb12_nm
FROM `total`
left join tab2 on tb2_id = tab2_id
left join tab3 on tb3_id = tab3_id
left join tab4 on tb4_id = tab4_id
left join tab5 on tb5_id = tab5_id
left join tab6 on tb6_id = tab6_id
left join tab7 on tb7_id = tab7_id
left join tab8 on tb8_id = tab8_id
left join tab9 on tb9_id = tab9_id
left join tab12 on tb12_id = tab12_id
WHERE tab1_id = 2 and total.url = ''");
if ($selSeas->execute() && $selSeas->rowCount() > 0) {
  while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {

    //echo $resObj->name . '<br/>';
    $id = $resObj->total_id;
    $url = $resObj->t3url . '_' . $resObj->t4url . '_' . rus3lat($resObj->tb5_nm) . '_' . rus3lat($resObj->tb6_nm) . '_' .
      rus3lat($resObj->tb7_nm) . '_' . rus3lat($resObj->tb8_nm) . '_' . rus3lat($resObj->tb9_nm) . '_' .
      rus3lat($resObj->tb12_nm) . '_' . rus3lat($resObj->tb2_nm);
    //echo $url . '<br/>';
    //echo 'test - ' . $resObj->name . ' - ' . $url . '<br/>';
    $test = $dbcon->prepare("select count(*) as cnt from total where total_id <> :id and url=:url");
    $test->bindParam(':id',$id);
    $test->bindParam(':url',$url);

    if ($test->execute() && $test->rowCount() > 0) {

      $testObj = $test->fetch(PDO::FETCH_OBJ);

      //echo $testObj->cnt . '<br/>';

      if($testObj->cnt == 0){

      echo '”спешно обновлено - ' . $resObj->name . ' - ' . $url . '<br/>';
        $update = $dbcon->prepare("update total set url=:url where total_id = :id");
        $update->bindParam(':id',$id);
        $update->bindParam(':url',$url);
        $res = $update->execute();



      } else {

        echo 'Url существует - ' . $resObj->name . '<br/>';
      }
    } else {
      echo "\nPDOStatement::errorInfo():\n";
$arr = $test->errorInfo();
print_r($arr);
    }
  }
}
echo 'update url field in tab4 table - end.<br/>';
/* update total*/
function rustolow($s)
  {
    $rus = "јЅ¬√ƒ≈∆«»… ЋћЌќѕ–—“”‘’÷„ЎўЁёя";
    $lat = "абвгдежзийклмнопрстуфхцчшщэю€";
    $s = strtr($s, $rus, $lat);
    return $s;
  }
function rus3lat($s){

    $s=strtolower($s);
    $s=rustolow($s);
    $s=str_ireplace("ыа","yha",$s);
    $s=str_ireplace("ыо","yho",$s);
    $s=str_ireplace("ыу","yhu",$s);
    $s=str_ireplace("Є","yo",$s);
    $s=str_ireplace("ж","zh",$s);
    $rus = "абвгдезийклмнопрстуфхц";
    $lat = "abvgdezijklmnoprstufxc";
    $s = strtr($s, $rus, $lat);
    $s=str_ireplace("ч","ch",$s);
    $s=str_ireplace("ш","sh",$s);
    $s=str_ireplace("щ","shh",$s);
    $s=str_ireplace("ъ","qh",$s);
    $s=str_ireplace("ы","y",$s);
    $s=str_ireplace("ь","q",$s);
    $s=str_ireplace("э","eh",$s);
    $s=str_ireplace("ю","yu",$s);
    $s=str_ireplace("€","ya",$s);
    $s=str_ireplace("(","_",$s);
    $s=str_ireplace(")","_",$s);
    $s=str_ireplace(" ","_",$s);
    $s=str_ireplace("/","_",$s);
    $s=str_ireplace("\\","_",$s);
    $s=str_ireplace(".","_",$s);
    $s=str_ireplace(",","_",$s);
    return $s;
  }
?>