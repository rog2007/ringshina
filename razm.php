<?php
  $tov=IdByName($arg[0],"tab1","tb1_id","translit");

  switch($tov) {

    case 1:
      $model = getModel($tov, $arg[2],$arg[1]);
      $frm_dsc = IdByName($model->brand_id,"tab3","tb3_dsc","tb3_id");
      $tovName = "шин";
      $title = "RingShina: размеры шин " . $model->tb3_nm . ' ' . $model->tb4_nm;
      $descr = "RingShina: размеры шин " . $model->tb3_nm . ' ' . $model->tb4_nm;
      $keywords = "RingShina: размеры шин " . $model->tb3_nm . ' ' . $model->tb4_nm;
    break;
    case 2:
      $model = getModel($tov, $arg[2],$arg[1]);
      $frm_dsc = IdByName($model->brand_id,"tab3","tb3_dsc","tb3_id");
      $tovName = "дисков";
      $title = "RingShina: размеры дисков " . $model->tb3_nm . ' ' . $model->tb4_nm;
      $descr = "RingShina: размеры дисков " . $model->tb3_nm . ' ' . $model->tb4_nm;
      $keywords = "RingShina: размеры дисков " . $model->tb3_nm . ' ' . $model->tb4_nm;
    break;
    case 3:
      $model = getModelAKB($arg[1]/*,$arg[1]*/);
      $frm_dsc = IdByName($model->brand_id, "akb_brand", "dsc", "id");
      $tovName = "АКБ";
      $title = "RingShina: варианты АКБ " . $model->tb4_nm;
      $descr = "RingShina: варианты АКБ " . $model->tb4_nm;
      $keywords = "RingShina: варианты АКБ " . $model->tb4_nm;
    break;
  }

  if($tov != 3){

    $content .= '<div class="row"><ul class="breadcrumbs">
  <li><a href="/">Главная</a></li>
  <li><a href="/catalog/' . $arg[0] . '.html">Каталог ' . $tovName . '</a></li>
  <li><a href="/modeli/' . $arg[0] . '/' . $arg[1] . '/1.html">' . $model->tb3_nm . '</a></li>
  <li class="current"><a href="/razm/'.$arg[0].'/'.$arg[1].'/'.$arg[2] . '.html">' . $model->tb4_nm  . '</a></li>
	</ul>';
  } else {

    $content .= '<div class="row"><ul class="breadcrumbs">
      <li><a href="/">Главная</a></li>
      <li><a href="/modeli/' . $arg[0] . '/1.html">Каталог ' . $tovName . '</a></li>
      <li class="current"><a href="/razm/'.$arg[0].'/'.$arg[1] . '.html">' . $model->tb4_nm  . '</a></li>
    	</ul>';
  }

  if( $tov == 2 ){
    $discImgs = getDiskModelImages($model->tb4_id);

    $pic = '/images/tovar/nofoto300.jpg';

    if ($discImgs->execute() && $discImgs->rowCount() > 0) {

      $i = 1;
      $dopImages = '';

      while($imgObj = $discImgs->fetch(PDO::FETCH_OBJ)){

        if($i == 1){

          $pic=ImageWork($imgObj->imgname, $tov, $imgObj->idcolor, $model->brand_id, $model->tb4_id,
            $imgObj->t2tr, $model->tb3_nm, $model->tb4_nm, '300');
          if(strpos($pic, 'nofoto')){

            $onclick = '';
            $style = 'style="cursor:default"';

          } else {

            /*$onclick = 'onclick="return ShowZoomWindow(true,\'' . addslashes($model->tb3_nm) . ' ' .
              addslashes($model->tb4_nm) . ($imgObj->tb2_nm ? ' ' . addslashes($imgObj->tb2_nm) : '') . '\',\'/images/tovar/' . ($tov==1?"tyres":"discs") . '/' . $imgObj->imgname . '\');"';
            */$style = '';
          }
          $i++;
        }
        $pic1=ImageWork($imgObj->imgname, $tov, $imgObj->idcolor, $model->brand_id, $model->tb4_id,
          $imgObj->t2tr, $model->tb3_nm, $model->tb4_nm, '80');
        if(strpos($pic1, 'nofoto')){

            $onclick1 = '';
            $style1 = 'style="cursor:default"';

        } else {

            $onclick1 = 'onclick="return setOtherImage(\'' . addslashes($imgObj->imgname) . '\',' . $imgObj->idcolor . ',' .
                $model->brand_id . ',' . $model->tb4_id . ',\'' . addslashes($imgObj->t2tr) . '\',\'' . addslashes($model->tb3_nm) .
                '\',\'' . addslashes($model->tb4_nm) . '\');"';
            $style1 = '';
        }

        $dopImages .= '<div class="small-dop-img"' . ($i%3 != 2 ? ' style="margin-left:10px"' : '') . '><img src="' . $pic1 . '" ' . $onclick1 . $style1 . ' /></div>';
        $i++;
      }
    }
  } else {

      $pic=ImageWork($model->image, $tov, $model->auto, $model->brand_id, $model->tb4_id, $model->t2tr, $model->tb3_nm, $model->tb4_nm, '300');
      if(strpos($pic, 'nofoto')){

      $onclick = '';
      $style = ';cursor:pointer';

      } else {

        /*$onclick = 'onclick="return ShowZoomWindow(true,\'' . addslashes($model->tb3_nm) . ' ' .
          addslashes($model->tb4_nm) . '\',\'/images/tovar/tyres/' . $model->image . '\');"';
        */$style = '';
      }
  }


//. ($tov == 2 && $model->auto ? ' ' . $model->tb2_nm : '' )
  $content .= '
  <div id="razmer">
  <div class="head"><h1>' . $model->tb3_nm . ' ' . $model->tb4_nm . ($model->auto_brand ? ' (' . $model->t_auto_nm . ')' : '')  . '</h1></div>
  <div class="description">' . $model->description . '</div>
    <div class="left">
      <div class="image">
        <img id="bimage" src="' . $pic . '" ' . $onclick . $style . ' />
      </div>
      ' . ($tov == 2 && $dopImages ? '<div class="dop-imgs">' . $dopImages . '</div>' : '') . '
      <div class="info">
        <div class="line"><span>Производитель</span><span>'.$model->tb3_nm.'</span></div>' .
        ($tov == 3 ? '' :'<div class="line"><span>'. ($tov == 1 ? 'Сезон' : 'Тип') . '</span>' .
        ($model->t4ses == 3 ? '<img src="/img/soln.png" alt="">' : ($model->t4ses == 5 ? '<img src="/img/sneg.png" alt="">' : ''))
        . '<span>'.$model->tb10_nm.'</span></div>') .
        ($model->t4sh == 3?'<div class="line"><span>Шипы</span>' . ($model->t4sh == 3 ? '<img src="/img/ship.png" alt="">' : '') . '<span>есть</span></div>':'').
        ($tov == 1 && $model->tb2_nm ? '<div class="line"><span>Автомобиль</span><span>'.$model->tb2_nm.'</span></div>' : '').
      '</div>
    </div>
    <div class="right">
      <div class="top">
        <div class="razmer">' . ($tov == 3 ? 'Параметры' : 'Типоразмер') . '</div>'.
        ($tov == 2 ? '<div class="color">Цвет</div>' : '')
        .'<div class="price">Цена, руб</div>
        <div class="qty">Количество</div>
        <div class="inbask"></div>
      </div>';

  if($tov == 1)
    $razmer = getRazmerTyre($model->tb4_id);
  if($tov == 2)
    $razmer = getRazmerDisc($model->tb4_id);
  if($tov == 3)
    $razmer = getRazmerAKB($model->tb4_id);

  if ($razmer->execute() && $razmer->rowCount() > 0) {

      $i = 1;
      while($razObj = $razmer->fetch(PDO::FETCH_OBJ)){

        if($tov == 1) $razmerName = $razObj->wname . ($razObj->hname?'/' . $razObj->hname : '') .
          ' ' . $razObj->tb6_nm . ' ' . $razObj->tb7_nm . $razObj->tb8_nm;
        if($tov == 2) $razmerName = $razObj->tb5_nm . '*' . $razObj->tb6_nm . ' ' .
          $razObj->tb7_nm . '/' . $razObj->tb8_nm . ' ET' . $razObj->tb9_nm .
          ($razObj->tb12_nm ? ' d' . $razObj->tb12_nm : '');
        if($tov == 3) $razmerName = $model->tb4_nm . ' ' . $razObj->vname . 'Ач ' . $razObj->volname . 'В ' . $razObj->arnm;;
        $content .= '<form class="line' . ($i%2 ? '' : ' grey' ) . '" method="post" action="/bask/' . $razObj->total_id . '/add.html">
        <a class="razmer" href="/card/'.$razObj->turl.'.html"' . ($razObj->spid == 21 ? ' style="color:#f00"' : '') . '>'. $razmerName .'</a>' .
        ($tov == 2 ? '<div class="color">' . $razObj->tb2_nm . '</div>' : '').
        '<div class="price">' . (int)$razObj->price . '</div>
        <div class="qty">
          <input type="text" name="cnt" class="input-qty" value="'.($razObj->cnt >= 4 ? '4' : $razObj->cnt).'" />
          <input type="hidden" name="tov" value="' . $tov . '" />
        </div>
        <div class="inbask"><input type="submit" value="В корзину" name="add" class="button small" /></div>
        </form>';
        $i++;
      }
    } else {

      $arr = $razmer->errorInfo();
      print_r($arr);
    }
     $content .=  '</div>
    </div>';

    if($frm_dsc){
      $content .= '<div class="brand-dsc-razm">' . $frm_dsc . '</div>';
    }
  $content .=  '</div>';
?>
