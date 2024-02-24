<?php

  $_imagePath = $_SERVER["DOCUMENT_ROOT"] . '/img/';
  $_imageUrl = '/img/';
  $_imageExtensions = array('.png', '.jpg', '.jpeg', '.gif');

  if(isset($_POST["load"])) {

        $image = '';
        if(!empty($_FILES)){

          $fileArray['name'] = $_FILES['image']['name'];
          $fileArray['tempname'] = $_FILES['image']['tmp_name'];
          /*var_dump($_FILES);
          var_dump($fileArray);
          echo $_imagePath;
          die;*/
          $image = loadImageFileToServer($fileArray, $_imagePath);
        }

      }
  if(isset($_POST['delete'])){

    unlink($_imagePath . $_POST['filename']);
  }

  $imagesArray = array();
  if(is_dir($_imagePath)){

    $imageDir = opendir($_imagePath);

    while($file = readdir($imageDir)){

      if(in_array(substr($file, strpos($file, '.')), $_imageExtensions)){

        array_push($imagesArray, $file);

      }

    }
    closedir($imageDir);

  } else {

    echo $_imagePath. ' - Не верный путь к папке с изображениями. Обратитесь к разработчику.';
    die;
  }
  $str .= '<div id="img-buttons">
  <form enctype="multipart/form-data" action="" method="post">
    <input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
    <input name="image" type="file" class="file"/>
    <input name="load" type="submit" value="загрузить" />
  </form>
  </div><div id="imagespath">';
  if(!empty($imagesArray)){

    sort($imagesArray);
    clearTempPath();
    $urlFirstImage = '';

    foreach ($imagesArray as $imageName){

      if($fileName = imageChange($imageName, $_imagePath, 100, 100)){

        $str .= '<div class="image' . ($urlFirstImage == '' ? ' select' : '') . '">
          <img src="' . $fileName . '" onclick="return selectImage(this, \'' . $_imageUrl . $imageName . '\')"/>
          <div class="name">' . $imageName . '</div>
          <div class="del">
            <form enctype="multipart/form-data" action="" method="post">
              <input type="hidden" name="filename" value="' . $imageName . '" />
              <input name="delete" type="submit" value="удалить" />
            </form>
          </div>
        </div>';

        if($urlFirstImage == ''){

          $urlFirstImage .= $_imageUrl . $imageName;
        }
      } 
    }
  }

  $str .= '</div>';

  $str .= '<div id="curimg"><div class="caption">Текущее изображение</div><div id="url"><span>URL: </span>' . $urlFirstImage . '</div><div id="imagefull">
    <img id="imagebig" src="' . $urlFirstImage . '" />
  </div></div>';

  function clearTempPath(){
    global $_imagePath, $_imageExtensions;

    if(is_dir($_imagePath . '/tmp/')){

      $imageDir = opendir($_imagePath . '/tmp/');

      while($file = readdir($_imagePath . '/tmp/')){

        if(in_array(substr($file, strpos($file, '.')), $_imageExtensions)){
          // echo $_imagePath . '/tmp/' . $file;
          unlink($_imagePath . '/tmp/' . $file);
        }

      }

      closedir($imageDir);

    } else {

      mkdir($_imagePath . '/tmp/');
    }

    return true;
  }

  function imageChange($fName, $path, $newh, $neww, $dop = ''){
    global $_imageUrl;

    $fileName = $path . $fName;

    if(!file_exists($fileName))
      return false;

    if(file_exists($path . '/tmp/' . $fName)){

      return $_imageUrl . ($dop ? $dop . '/' : '') . 'tmp/' . $fName;
    }

    $type = substr($fileName, strrpos($fileName, '.') + 1);

    switch ($type){

      case 'png':
        $im = imagecreatefrompng($fileName);
      break;
      case 'jpg': case 'jpeg':
        $im = imagecreatefromjpeg($fileName);
      break;
      case 'gif':
        $im = imagecreatefromgif($fileName);
      break;
      default:
        return false;
      break;
    }


    $old_h = imagesy($im);
    $old_w = imagesx($im);
    $per_h = $old_h/$newh;
    $per_w = $old_w/$neww;
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

    $newFileName = $path . '/tmp/' . $fName;

    switch ($type){

      case 'png':
        imagepng($im1, $newFileName);
      break;
      case 'jpg': case 'jpeg':
        imagejpeg($im1, $newFileName);
      break;
      case 'gif':
        imagegif($im1, $newFileName);
      break;
      default:
        return false;
      break;
    }
    return $_imageUrl . ($dop ? $dop . '/' : '') . 'tmp/' . $fName;
  }

  function loadImageFileToServer($fileArray, $path){

    if (move_uploaded_file($fileArray['tempname'], $path . $fileArray['name'])){

      return $fileArray['name'];
    } else {
      return false;
    }
  }

?>