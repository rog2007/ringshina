<?php
function ImageResSave1($infile,$path_s,$path_d,$newh,$neww) {

    if(!file_exists($_SERVER["DOCUMENT_ROOT"]."/images/tovar/".$path_s."/".$infile)) return 0;
    $im=imagecreatefromjpeg($_SERVER["DOCUMENT_ROOT"]."/images/tovar/".$path_s."/".$infile);
    $old_h=imagesy($im);
    $old_w=imagesx($im);
    $per_h=$old_h/$newh;
    $per_w=$old_w/$neww;
    if($per_h>=$per_w)
    {
      $new_w=$old_w/$per_h;
      $im1=imagecreatetruecolor($neww,$newh);
      $ink = imagecolorallocate($im1,255,255,255);
      imagefilledrectangle($im1,0,0,$neww,$newh,$ink);
      imagecopyresampled($im1,$im,($neww-$new_w)/2,0,0,0,$new_w,$newh,$old_w,$old_h);
    }
    else
    {
      $new_h=$old_h/$per_w;
      $im1=imagecreatetruecolor($neww,$newh);
      $ink = imagecolorallocate($im1,255,255,255);
      imagefilledrectangle ($im1,0,0,$neww,$newh,$ink);
      imagecopyresampled($im1,$im,0,($newh-$new_h)/2,0,0,$neww,$new_h,$old_w,$old_h);
    }
    imagedestroy($im);

    imagejpeg($im1,$_SERVER["DOCUMENT_ROOT"]."/images/tovar/".$path_d."/".$infile);
    imagedestroy($im1);
    return 1;
  }
        
function normalize_mysqldate_new($mysqldate) {
    return $mysqldate[8] . $mysqldate[9] . "." . $mysqldate[5] . $mysqldate[6] . "." . $mysqldate[0] . $mysqldate[1] . $mysqldate[2] . $mysqldate[3];
}

function to_mysqldate_new($normaldate) {
    $normaldate = trim($normaldate);
    return "20" . $normaldate[6] . $normaldate[7] . "-" . $normaldate[3] . $normaldate[4] . "-" . $normaldate[0] . $normaldate[1];
}

function deleteImages($dirrect, $path1, $name12) {

    if (is_dir($dirrect)) {

        if ($dir = opendir($dirrect)) {

            while (false !== ($file = readdir($dir))) {

                if ($file != "." && $file != "..") {

                    if (strpos($file, $path1) !== false && $file != $path1 && is_dir($dirrect . $file)) {

                        if (file_exists($dirrect . $file . "/" . $name12))
                            unlink($dirrect . $file . "/" . $name12);
                    }
                }
            }
            closedir($dir);
        }
    }
}

function getDiskModelImages_new($modelId, $colorId, $limit = 0) {
    global $dbcon;

    $selectNomen = $dbcon->prepare('select imgname, idcolor, tab2.translit as t2tr, tb2_nm from imgs
    LEFT JOIN tab2 ON tb2_id = idcolor
    LEFT JOIN tab4 ON tb4_id = idmodel
    where idmodel=:mid AND idcolor=:cid order by if(auto = idcolor, 1, 0) desc, idcolor' . 
        ($limit ? ' limit 0, ' . $limit : ''));
    $selectNomen->bindParam(':mid', $modelId);
    $selectNomen->bindParam(':cid', $colorId);
    return $selectNomen;
}

function ImageWork_new($imgname, $t1, $t2, $t3, $t4, $t2tr, $t3nm, $t4nm, $pth) {

    switch ($t1) {
        case 1:
            $t2 = 0;
            $fold = 'tyres';
            break;
        case 2:
            $fold = 'discs';
            break;
        case 3:
            $t2 = 0;
            $fold = 'akb';
            break;
    }
    $pic = '/images/tovar/nofoto' . $pth . '.jpg';
    if ($imgname) {

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $fold . "/" . $imgname)) {

            $pic = "/images/tovar/" . $fold . $pth . "/" . $imgname;
            if (!is_dir($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $fold . $pth)) {
                mkdir($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $fold . $pth, 0777);
            }
            if (!file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . $fold . $pth . "/" . $imgname)) {
                ImageResSave1($imgname, $fold, $fold . $pth, $pth, $pth);
            }
        }
    }
    return $pic;
}

function nomenTyresNew($data) {

    $sql = ' WHERE total_id IN (' . implode(', ', $data) . ')';
    $sql = 'SELECT total.url as turl, total_id,all_name,tb3_pic as T3Pic,imgs.imgname as T4Pic, ' .
            'tb4_nm as T4Nm, tb6_nm, tb7_nm, tb8_nm, price, tb3_nm as T3Nm, tovimg, t4ses, t4sh, ' .
            'CONCAT(profw.name, IF(ifnull(profh.name,\'\') > \'\', concat(\'/\', profh.name), \'\')) as prof, ' .
            'tb4_nm as T4Nm,tab3_id,tab4_id, rof, cnt FROM total LEFT JOIN imgs on imgs.idmodel = tab4_id ' .
            'LEFT JOIN tab3 ON tb3_id = tab3_id LEFT JOIN tab6 ON tb6_id = tab6_id ' .
            'LEFT JOIN tab4 ON tb4_id = tab4_id LEFT JOIN tab7 ON tb7_id=tab7_id ' .
            'LEFT JOIN tab8 ON tb8_id = tab8_id LEFT JOIN tab5 ON tb5_id=tab5_id ' .
            'LEFT JOIN tab2 ON tb2_id = tab2_id LEFT JOIN tab10 ON tb10_id = tab10_id ' .
            'LEFT JOIN tab9 ON tb9_id = t4sh LEFT JOIN tab12 ON tb12_id = tab12_id ' .
            'LEFT JOIN profw ON w_id = profw.id LEFT JOIN profh ON h_id = profh.id ' .
            $sql . ' ORDER BY tab10_id desc, all_name ';
    $result = mysql_query($sql);
    return $result;
}

function nomenDiscsNew($data) {

    $sql = ' WHERE total_id IN (' . implode(', ', $data) . ')';
    $sql = 'SELECT total.url as turl, total_id, all_name, tb3_pic as T3Pic, imgs.imgname as T4Pic, ' .
            'tb4_nm as T4Nm, tb6_nm, tb7_nm, tb8_nm, price, tb3_nm as T3Nm, tb9_nm, tb12_nm, tovimg, ' .
            'tb5_nm, tb4_nm as T4Nm, tab3_id, tab4_id, cnt, tb2_nm, tab2_id, auto_brand, t_auto_nm ' .
            'FROM total LEFT JOIN imgs ON imgs.idmodel = tab4_id AND imgs.idcolor = tab2_id ' .
            'LEFT JOIN tab3 ON tb3_id=tab3_id LEFT JOIN tab6 ON tb6_id=tab6_id ' .
            'LEFT JOIN tab4 ON tb4_id=tab4_id LEFT JOIN tab7 ON tb7_id=tab7_id ' .
            'LEFT JOIN tab8 ON tb8_id=tab8_id LEFT JOIN tab5 ON tb5_id=tab5_id ' .
            'LEFT JOIN tab2 ON tb2_id=tab2_id LEFT JOIN tab10 ON tb10_id=tab10_id ' .
            'LEFT JOIN tab9 ON tb9_id=tab9_id LEFT JOIN tab12 ON tb12_id=tab12_id ' .
            'LEFT JOIN t_auto ON t_auto_id = auto_brand ' . $sql . ' ORDER BY all_name';
    $result = mysql_query($sql);
    return $result;
}

function nomenAkbNew($data) {
    
    $sql = ' WHERE at.id IN (' . implode(', ', $data) . ')';
    $sql = 'SELECT at.url as turl, at.id as total_id, at.full_name as all_name, ' .
        'ab.pic as T3Pic, am.pic as T4Pic, am.name as T4Nm, avl.name as vlname, ar.name as rname, ' .
        'price, ab.name as T3Nm, ab.id as tab3_id, am.id as tab4_id, cnt, av.name as vname ' .
        'FROM akb_tovar AS at LEFT JOIN akb_brand as ab ON at.id_brand = ab.id ' .
        'LEFT JOIN akb_model AS am ON at.id_model = am.id LEFT JOIN akb_v as av ON at.id_v = av.id ' .
        'LEFT JOIN akb_rvrt AS ar ON at.rvrt = ar.id ' .
        'LEFT JOIN akb_volt AS avl ON at.id_volt = avl.id ' . $sql . ' ORDER BY full_name';
    $result = mysql_query($sql);
    return $result;
  }