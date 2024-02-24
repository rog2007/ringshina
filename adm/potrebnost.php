<?php
  if (!$myFile = fopen($_FILES["r"]["tmp_name"],"r"))
  {
    echo "Не могу открыть файл csv: ".$_FILES["r"]["tmp_name"];
    Exit;
  }
  $razmer='';
  while($data = fgetcsv($myFile, 1024,';'))
  {
    if($data[0]=='' && $data[2]<>'')
    {
      $razmer=$data[2];
      continue;
    }
    $cnt=$data[1];
    if($cnt=='') $cnt=$data[3];
    if($cnt=='') $cnt=0;
    $sql="insert into potrebnost (brand,razmer,cnt) values ('".$data[0]."','".$razmer."',".$cnt.");";
    mysql_query($sql);
  }
  echo "выполнено";
?>