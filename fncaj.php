<?php

  require ("func.php");
  header("Content-type: text/plain; charset=windows-1251");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);

  if($_POST["fl"]==2) {

    $im=imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"].$_POST["img"]);
    $str = imagesx($im)."|".imagesy($im);
    imagedestroy($im);
  }
  if($_POST["fl"] == 3){


    $str = ImageWork($_POST["imgName"], 2, $_POST["idColor"], $_POST["idBrand"], $_POST["idModel"],
        $_POST["t2tr"], $_POST["nameModel"], $_POST["nameBrand"], '300');
    /*$str = 'ImageWork(' . $_POST["imgName"] . ', 2, ' . $_POST["idColor"] . ', ' . $_POST["idBrand"] . ', ' . $_POST["idModel"] . ', ' .
        $_POST["t2tr"] . ', ' . $_POST["nameModel"] . ', ' . $_POST["nameBrand"] . ', ' . '300)';*/
  }
  echo $str;

?>