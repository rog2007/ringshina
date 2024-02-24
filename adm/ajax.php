<?php
header("Content-type: text/plain; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
require("connect.php");
require("func_new.php");
  if ($_POST["step"] == 1) {
      $res = query("SELECT tb4_id, tb4_nm FROM tab4 WHERE brand_id=:id ORDER BY tb4_nm", [':id' => $_POST["firm"]]);
      $str_pod = '';
      if($res['result']) {
          foreach ($res['data'] as $rs) {
              if(!empty($str_pod)) {
                  $str_pod .= '$';
              }
              $str_pod .= $rs->tb4_id . '|' . $rs->tb4_nm;
          }
      }
  }
  if ($_POST["step"] == 2) {
      $res = query("SELECT tb2_id, tb2_nm FROM tab3 WHERE brid=:id ORDER BY tb2_nm", [':id' => $_POST["firm"]]);
      $str_pod = '';
      if($res['result']) {
          foreach ($res['data'] as $rs) {
              if(!empty($str_pod)) {
                  $str_pod .= '$';
              }
              $str_pod .= $rs->tb2_id . '|' . $rs->tb2_nm;
          }
      }
  }
  echo $str_pod;