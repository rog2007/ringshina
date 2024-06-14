<?php

    function IdByName($nm, $tbl, $field_id, $field_name) {

        $sql = "select {$field_id} from {$tbl} where {$field_name}='{$nm}'";
        $result=mysql_query($sql);
        if (mysql_num_rows($result)==0){

            return 0;
        } else {

            return mysql_result($result, 0, $field_id);
        }
    }

    function updateUrlBrand($tov){

        global $dbcon;

        switch($tov){

            case 1:
                $tovName = 'шины';
                $sqlCheck = "select count(tb3_id) as cnt from tab3 where tb3_id <> :id and url=:url";
                $sqlUpdate = "update tab3 set url=:url where tb3_id = :id";
            break;
            case 2:
                $tovName = 'диски';
                $sqlCheck = "select count(tb3_id) as cnt from tab3 where tb3_id <> :id and url=:url";
                $sqlUpdate = "update tab3 set url=:url where tb3_id = :id";
            break;
            case 3:
                $tovName = 'АКБ';
                $sqlCheck = "select count(id) as cnt from akb_brand where id <> :id and url=:url";
                $sqlUpdate = "update akb_brand set url=:url where id = :id";
            break;
        }
        $str .= '<h2>Обновление URL для брендов (' + $tovName + ')</h2><ul><li>начало</li>';

        if($tov < 3){

            $selSeas = $dbcon->prepare('select tb3_id as id, tb3_nm as name from tab3 where url =\'\' AND tb3_tov_id = :tov');
            $selSeas->bindParam(':tov', $tov);
        } else {

            $selSeas = $dbcon->prepare('select id, name from akb_brand where url =\'\'');
        }

        if ($selSeas->execute() && $selSeas->rowCount() > 0) {

            $str .= '<li>Необходимо обновить ' . $selSeas->rowCount() . ' брендов</li>';

            while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {

                $id = $resObj->id;
                $url = str2url($resObj->name);

                $test = $dbcon->prepare($sqlCheck);
                $test->bindParam(':id',$id);
                $test->bindParam(':url',$url);

                if ($test->execute() && $test->rowCount() > 0) {

                    $testObj = $test->fetch(PDO::FETCH_OBJ);

                    if($testObj->cnt == 0){

                      $update = $dbcon->prepare($sqlUpdate);
                      $update->bindParam(':id',$resObj->id);
                      $update->bindParam(':url',$url);
                      $res = $update->execute();

                      $str .= '<li>Успешно обновлено - ' . $resObj->name . '(' . $res . ')</li>';

                    } else {

                      $str .= '<li>Url существует - ' . $resObj->name . '</li>';
                    }
                } else {

                    echo "\nPDOStatement::errorInfo():\n";
                    $arr = $test->errorInfo();
                    print_r($arr);
                }
            }
        } else {

            $str .= '<li>Все Url заполнены</li>';
        }
        $str .= '<li>Процесс обновления завершен</li></ul>';

        return $str;
    }

     function updateUrlModel($tov){

        global $dbcon;

        switch($tov){

            case 1:
                $tovName = 'шины';
                $sqlCheck = "select count(*) as cnt from tab4 where tb4_id <> :id and url=:url and brand_id=:br";
                $sqlUpdate = "update tab4 set url=:url where tb4_id = :id";
            break;
            case 2:
                $tovName = 'диски';
                $sqlCheck = "select count(*) as cnt from tab4 where tb4_id <> :id and url=:url and brand_id=:br";
                $sqlUpdate = "update tab4 set url=:url where tb4_id = :id";
            break;
            case 3:
                $tovName = 'АКБ';
                $sqlCheck = "SELECT COUNT(*) AS cnt FROM akb_model WHERE id <> :id and url = :url and akb_brand_id = :br";
                $sqlUpdate = "update akb_model set url=:url where id = :id";
            break;
        }
        $str .= '<h2>Обновление URL для моделей (' + $tovName + ')</h2><ul><li>начало</li>';

        if($tov < 3){

            $selSeas = $dbcon->prepare('select tb4_id as id, tb4_nm as name, brand_id from tab4 WHERE url =\'\' AND tb4_tov_id = :tov');
            $selSeas->bindParam(':tov', $tov);
        } else {

            $selSeas = $dbcon->prepare('select id, name, akb_brand_id as brand_id from akb_model where url =\'\'');
        }


        if ($selSeas->execute() && $selSeas->rowCount() > 0) {

            $str .= '<li>Необходимо обновить ' . $selSeas->rowCount() . ' моделей</li>';

            while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {

              $id = $resObj->id;
              $url = str2url($resObj->name);

              $test = $dbcon->prepare($sqlCheck);
              $test->bindParam(':id',$id);
              $test->bindParam(':url',$url);
              $test->bindParam(':br',$resObj->brand_id);

              if ($test->execute() && $test->rowCount() > 0) {

                  $testObj = $test->fetch(PDO::FETCH_OBJ);

                  if($testObj->cnt == 0){

                    $update = $dbcon->prepare($sqlUpdate);
                    $update->bindParam(':id',$resObj->id);
                    $update->bindParam(':url',$url);
                    $res = $update->execute();

                    $str .= '<li>Успешно обновлено - ' . $resObj->name . '(' . $res . ')</li>';

                  } else {

                    $str .= '<li>Url существует - ' . $resObj->name . '</li>';
                  }
              } else {

                echo "\n1 - PDOStatement::errorInfo():\n";
                $arr = $test->errorInfo();
                print_r($arr);
              }
            }
        } else {

            $str .= '<li>Все Url заполнены</li>';
        }
        $str .= '<li>Процесс обновления завершен</li></ul>';

        return $str;
     }

  $tov = IdByName($arg[0],"tab1","tb1_id","translit");

  if($arg[1] == 'brand'){

    $str .= updateUrlBrand($tov);
    $str .= '<p><a href="/adm/sp-tov/' . $arg[0] . '/">Вернуться</a></p>';
  }


  if($arg[1] == 'model'){

    $str .= updateUrlModel($tov);
    $str .= '<p><a href="/adm/sp-tov/' . $arg[0] . '/">Вернуться</a></p>';
  }
  if($arg[1] == 'tovar'){

    switch($tov){

        case 1:
            $tovName = 'шины';
            $sqlCheck = "select count(*) as cnt from total where total_id <> :id and url=:url";
            $sqlUpdate = "update total set url=:url where total_id = :id";
        break;
        case 2:
            $tovName = 'диски';
            $sqlCheck = "select count(*) as cnt from total where total_id <> :id and url=:url";
            $sqlUpdate = "update total set url=:url where total_id = :id";
        break;
        case 3:
            $tovName = 'АКБ';
            $sqlCheck = "SELECT COUNT(*) AS cnt FROM akb_tovar WHERE id <> :id and url = :url";
            $sqlUpdate = "update akb_tovar set url = :url where id = :id";
        break;
    }

    $str .= '<h2>Обновление URL для товаров (' . $tovName . ')</h2><ul><li>начало</li>';

    if($tov < 3){

        $selSeas = $dbcon->prepare("SELECT total_id, all_name as name, tab3.url as t3url, tab4.url as t4url, profw.name as wprof,
          profh.name as hprof, tb5_nm, tb6_nm, tb7_nm, tb8_nm, tb9_nm, tb12_nm, omolog, tab1_id, tb2_nm, rof
          FROM `total`
          left join tab2 on tb2_id = tab2_id
          left join tab3 on tb3_id = tab3_id
          left join tab4 on tb4_id = tab4_id
          left join tab5 on tb5_id = tab5_id
          left join profw on profw.id = w_id
          left join profh on profh.id = h_id
          left join tab6 on tb6_id = tab6_id
          left join tab7 on tb7_id = tab7_id
          left join tab8 on tb8_id = tab8_id
          left join tab9 on tb9_id = tab9_id
          left join tab12 on tb12_id = tab12_id
          WHERE total.url = '' AND tab1_id = :tov"); 
        $selSeas->bindParam(':tov', $tov);
    } else {
        $selSeas = $dbcon->prepare("SELECT at.id as total_id, full_name as name, ab.url as t3url, am.url as t4url, av.name as volname,
          avl.name as vname, ar.name as rname, 3 as tab1_id
          FROM `akb_tovar` as at
          left join akb_brand as ab on ab.id = id_brand
          left join akb_model as am on am.id = id_model
          left join akb_v as av on av.id = id_v
          left join akb_volt as avl on avl.id = id_volt
          left join akb_rvrt as ar on ar.id = rvrt
          WHERE at.url = '' OR at.url = '0'");
    }

    if ($selSeas->execute() && $selSeas->rowCount() > 0) {

      $str .= '<li>Необходимо обновить ' . $selSeas->rowCount() . ' товаров</li>';
      while ($resObj = $selSeas->fetch(PDO::FETCH_OBJ)) {

        $id = $resObj->total_id;
        if($resObj->tab1_id == 1){

          $url = $resObj->t3url . '_' . $resObj->t4url . '_' . ($resObj->omolog?'_' . strtolower($resObj->omolog):'') .
            $resObj->wprof . '_' . ($resObj->hprof?$resObj->hprof . '_':'') . strtolower($resObj->tb6_nm) . ($resObj->tb7_nm?'_' . strtolower(str_ireplace('/','_',$resObj->tb7_nm)):'') .
            ($resObj->tb8_nm?'_' . strtolower($resObj->tb8_nm):'') . ($resObj->rof ? '_rof' : '');
        }
        if($resObj->tab1_id == 2){

          $url = $resObj->t3url . '_' . $resObj->t4url . '_' . str2url($resObj->tb5_nm) . '_' . str2url($resObj->tb6_nm) . '_' .
            str2url($resObj->tb7_nm) . '_' . str2url($resObj->tb8_nm) . '_' . $resObj->tb9_nm . '_' .
            str2url($resObj->tb12_nm) . '_' . str2url($resObj->tb2_nm);            
        }

        if($resObj->tab1_id == 3){

          $url = $resObj->t3url . '_' . $resObj->t4url . '_' . str2url($resObj->vname) . '_' . str2url($resObj->volname) . '_' .
            str2url($resObj->rname);
        }

        $test = $dbcon->prepare($sqlCheck);
        $test->bindParam(':id',$id);
        $test->bindParam(':url',$url);


        if ($test->execute() && $test->rowCount() > 0) {

          $testObj = $test->fetch(PDO::FETCH_OBJ);

          if($testObj->cnt == 0){

            $update = $dbcon->prepare($sqlUpdate);
            $update->bindParam(':id',$resObj->total_id);
            $update->bindParam(':url',$url);
            $res = $update->execute();

            $str .= '<li>Успешно обновлено - ' . $resObj->name . '(' . $res . ')(' . $url . ')</li>';

          } else {

            $str .= '<li>Url существует - ' . $resObj->name . '</li>';
          }
        } else {

          echo "\nPDOStatement::errorInfo():\n";
          $arr = $test->errorInfo();
          print_r($arr);
        }
    }
  } else {

      $str .= '<li>Все Url заполнены</li>';
    }
    $str .= '<li>Процесс обновления завершен</li></ul>
    <p><a href="/adm/sp-tov/' . $arg[0] . '/">Вернуться</a></p>';
  }
  function rus2translit($string) {
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
function str2url($str) {
    // переводим в транслит
    $str = rus2translit($str);
    // в нижний регистр
    $str = strtolower($str);
    // заменям все ненужное нам на "-"
    $str = preg_replace('~[^\-a-z0-9_]+~u', '-', $str);
    // удаляем начальные и конечные '-'
    $str = trim($str, "-");
    return $str;
}