<?php

  session_start();
  if(md5(crypt($_SESSION['name'],$_SESSION['pass'])) != $_SESSION['SID']){

    Header("Location: index.php");
  }

  require ("../func.php");
  require ("connect.php");
  require ("funcn.php");

  if($_POST) {

    $file_temp = $_FILES['userfile']['tmp_name'];
    if(isset($_POST["data"])){

      $post=explode('|',$_POST["data"]);
      $fl = 1;
    }
    if(isset($_POST["tovar"])){

      $post=explode('|',$_POST["tovar"]);
      $fl = 2;
    }

    if(isset($_POST["brand"])){

      $post=explode('|',$_POST["brand"]);
      $fl = 3;
      $res=mysql_query("select tb3_nm FROM tab3 where tb3_id=" . $post[0]);
      if($rs=mysql_fetch_object($res)) {

        $name12 = str_replace(" ", "_", rus4lat($rs->tb3_nm));
        $name12 = replaceSymbols($name12);
        $name12 .= ".png";
      }
    }

    if(isset($_POST["akbbrand"])){

      $post = explode('|', $_POST["akbbrand"]);
      $fl = 8;
      $res=mysql_query("select name FROM akb_brand where id=" . $post[0]);
      if($rs=mysql_fetch_object($res)) {

        $name12 = str_replace(" ", "_", rus4lat($rs->name));
        $name12 = replaceSymbols($name12);
        $name12 .= ".png";
      }
    }

    if(isset($_POST["akbmodel"])){

      $post = explode('|', $_POST["akbmodel"]);
      $fl = 9;
      $res=mysql_query("select akb_model.url as mname FROM akb_model where akb_model.id = " . $post[0]);

      if($rs=mysql_fetch_object($res)) {

        $name12 = str_replace(" ", "_", rus4lat(mb_strtolower($rs->mname)));
        $name12 = replaceSymbols($name12);
        $name12 .= ".jpg";
      }
    }

    if(isset($_POST["t_auto"])){

      $fl = 4;
      $res=mysql_query("select t_auto_nm FROM t_auto where t_auto_id=" . $_POST["t_auto"]);
      if($rs=mysql_fetch_object($res)) {

        $name12=str_replace(" ","_",rus4lat($rs->t_auto_nm));
        $name12 = replaceSymbols($name12);
        $name12 .= ".png";
      }
    }

    if($post[0] == 1 && $fl != 3 && $fl != 4 && $fl != 8 && $fl != 9) {

      $post[3]=0;
      if($fl == 1){

        $res=mysql_query("select tb3_nm,tb4_nm from tab4 left join tab3 on brand_id=tb3_id where brand_id=" . $post[1] . " and tb4_id=" . $post[2]);

        if($rs=mysql_fetch_object($res)) {

          $name12=str_replace(" ","_",rus4lat($rs->tb3_nm . "_" . $rs->tb4_nm));
          $name12 = replaceSymbols($name12);
          $name12 .= ".png";
        } else {

          echo "нет такой модели шин";
          return;
        }
      } else {

        $res=mysql_query("select url, tovimg from total where total_id=" . $post[1]);

        if($rs = mysql_fetch_object($res)) {

          if($rs->tovimg && trim($rs->tovimg) != ''){

            $name12 = $rs->tovimg;
          } elseif($rs->url && trim($rs->url) != '') {

            $name12 = $rs->url;
            $name12 .= ".jpg";
          } else {

            echo 'нет URL для формирования имени изображения';
            return;
          }
        } else {

          echo "нет такой позиции";
          return;
        }
      }
    }

    if($post[0]==2 && $fl != 3 && $fl != 4 && $fl != 8 && $fl != 9){

      if($fl == 1){

        if($post[3] == 0){
          if(isset($_POST['auto'])){
            $post[3] = $_POST['auto'];
          }
        }

        $res=mysql_query("select tb3_nm,tb4_nm from tab4 left join tab3 on brand_id=tb3_id
        where brand_id=" . $post[1] . " and tb4_id=" . $post[2]);

        if($rs=mysql_fetch_object($res))
        {
          $brName = $rs->tb3_nm;
          $modName = $rs->tb4_nm;
          if($post[3]){

            $colName = IdByName($post[3], "tab2", "tb2_nm", "tb2_id");

          }

        } else {

          echo 'нет такой модели диска';
          return;
        }

          if($post[3]>0)
          {
            $cols=str_replace("(","",$colName);
            $cols=str_replace(")","",$cols);
          }

          $name12=str_replace(" ","_",rus4lat($brName . "_" . $modName . ($post[3] > 0 ? "_" . rus4lat($cols) : "")));
          $name12 = replaceSymbols($name12);
          $name12 .= ".jpg";
        } else {

          $res=mysql_query("select url, tovimg from total where total_id=" . $post[1]);
          if($rs = mysql_fetch_object($res)) {

            if($rs->tovimg && trim($rs->tovimg) != ''){

              $name12 = $rs->tovimg;
            } elseif($rs->url && trim($rs->url) != '') {

              $name12 = $rs->url;
              $name12 .= ".jpg";
            } else {

              echo 'нет URL для формирования имени изображения';
              return;
            }
          } else {

            echo "нет такой позиции";
            return;
          }
        }
      }

    if($fl < 3){

      $uploaddir = $_SERVER["DOCUMENT_ROOT"]."/images/tovar/".($post[0]==1?"tyres":"discs")."/".$name12;
    }
    if($fl == 3 || $fl == 8){

      $uploaddir = $_SERVER["DOCUMENT_ROOT"]."/images/tovar/brands/" . $name12;
    }
    if($fl == 9){

      $uploaddir = $_SERVER["DOCUMENT_ROOT"]."/images/tovar/akb/" . $name12;
    }
    if($fl == 4){

      $uploaddir = $_SERVER["DOCUMENT_ROOT"]."/images/tovar/cars/" . $name12;
    }

    if (move_uploaded_file($_FILES['userfile']['tmp_name'],$uploaddir)) {

      if($fl == 1){

        $path1 = ($post[0]==1?"tyres":"discs");
        $res=mysql_query("select count(*) as cnt1 from imgs where idmodel=".$post[2]." and idcolor=".$post[3]." and idbrand=".$post[1]);
        $r=mysql_fetch_object($res);
        $cnt=$r->cnt1;

        if($cnt){

          mysql_query("update imgs set imgname='".$name12."' where idmodel=".$post[2]." and idcolor=".$post[3]." and idbrand=".$post[1]);
        }
        else

          mysql_query("insert into imgs (idmodel,idcolor,imgname,idbrand) values(".$post[2].",".$post[3].",'".$name12."',".$post[1].")");
      } elseif($fl == 2) {

        mysql_query("update total set tovimg='".$name12."' where total_id=".$post[1]);
      } elseif($fl ==3) {

        mysql_query("update tab3 set tb3_pic = '".$name12."' where tb3_id=" . $post[0]);
      } elseif($fl == 8) {

        mysql_query("update akb_brand set pic = '" . $name12 . "' where id=" . $post[0]);
      } elseif($fl == 9) {

        mysql_query("update akb_model set pic = '" . $name12 . "' where id=" . $post[0]);
      } elseif($fl ==4) {

        mysql_query("update t_auto set t_auto_pic = '".$name12."' where t_auto_id=" . $_POST["t_auto"]);
      }

      if($fl < 3){

        $dirrect = $_SERVER["DOCUMENT_ROOT"]."/images/tovar/";

        deleteImages($dirrect, $path1, $name12);
      }

      Header("Location: ".$_SERVER['HTTP_REFERER']);
    }
    else
    {
      echo $_POST["MAX_FILE_SIZE"]."<br/>".$file_temp."<br/>".$uploaddir."<br/>".$_FILES['userfile']['tmp_name']." ошибка при загрузке файла";
      print_r($_FILES);
    }
  }

function rus4translit($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'Y',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
        'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
        'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
    );
    return strtr($string, $converter);
}
function rus4lat($str) {
    // переводим в транслит
    $str = rus4translit($str);
    // в нижний регистр
    $str = strtolower($str);
    // заменям все ненужное нам на "-"
    $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
    // удаляем начальные и конечные '-'
    $str = trim($str, "-");
    return $str;
}