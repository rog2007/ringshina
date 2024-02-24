<?php

  $tovObj = getNomenId($arg[0]);
  $tov = $tovObj->tab1_id;

  if ($tov == 1) {

    $nomen = getTyre($tovObj->total_id);
    $fullTovName = $nomen->all_name;

    $title = 'Купить шины ' . $fullTovName;
    $keywords = 'Купить шины ' . $fullTovName;
    $descr = 'Купить шины ' . $fullTovName;

    $pic1="tyres";
    $strPar = '<table>
			  <thead>
				<tr>
				  <th width="300">Название</th>
				  <th width="200">Значение</th>
				</tr>
			  </thead>
			  <tbody>
				<tr>
				  <td>Производитель</td>
				  <td>'.$nomen->T3Nm.'</td>
				</tr>
				<tr>
				   <td>Модель</td>
				  <td>'.$nomen->T4Nm.'</td>
				</tr>
				<tr>
				  <td>Типоразмер</td>
				  <td>'.$nomen->prof." ".$nomen->T6Nm.'</td>
				</tr>
				<tr>
				  <td>Индекс нагрузки</td>
				  <td>'.$nomen->T7Nm.'</td>
				</tr>
				<tr>
				  <td>Индекс скорости</td>
				  <td>'.$nomen->T8Nm.'</td>
				</tr>' . ($nomen->t4sh == 3 ? '<tr>
				  <td>Шипованность</td>
				  <td><img src="/img/ship.png" alt=""></td>
				</tr>' : '') . '
				<tr>
				   <td>Сезонность</td>
				  <td>' . $nomen->T10Nm . ' ' . ($nomen->tab10_id == 3 ? '<img src="/img/soln.png" alt="">' : ($nomen->tab10_id == 5 ? '<img src="/img/sneg.png" alt="">' : '')) . '</td>
				</tr>
			  </tbody>
			</table>';

   if(trim($nomen->tovimg)){

    $pic = $nomen->tovimg;
   } else {

    $pic = $nomen->T4Pic;
   }
   $link = $nomen->prof." ".$nomen->T6Nm . ' ' . $nomen->T7Nm . $nomen->T8Nm;
   $onclick =  'onclick="return ShowZoomWindow(true,\'' . addslashes($nomen->T3Nm) . ' ' .
    addslashes($nomen->T4Nm) . '\',\'/images/tovar/tyres/' . $pic . '\');"';

   $h=350;
  }

  if ($tov == 2) {

    $nomen = getDisc($tovObj->total_id);
    $fullTovName = $nomen->T3Nm . ' ' . $nomen->T4Nm . ($nomen->auto_brand ? ' (' . $nomen->t_auto_nm . ')' : '') .
        ' ' . $nomen->tb5_nm."*".$nomen->T6Nm . ' ' . $nomen->T7Nm."/".$nomen->T8Nm . ' ET' . $nomen->T9Nm .
        ' D' . $nomen->tb12_nm . ' ' . $nomen->T2Nm;//$nomen->all_name;

    $title = 'Купить диски ' . $fullTovName;
    $keywords = 'Купить диски ' . $fullTovName;
    $descr = 'Купить диски ' . $fullTovName;

    $lst=2;
   $pic1="discs";
   $strPar = '<table>
			  <thead>
				<tr>
				  <th width="300">Название</th>
				  <th width="200">Значение</th>
				</tr>
			  </thead>
			  <tbody>
				<tr>
				  <td>Производитель</td>
				  <td>'.$nomen->T3Nm.'</td>
				</tr>
				<tr>
				   <td>Модель</td>
				  <td>'.$nomen->T4Nm . ($nomen->auto_brand ? ' (' . $nomen->t_auto_nm . ')' : '').'</td>
				</tr>
				<tr>
				  <td>Ширина * диаметр обода</td>
				  <td>'.$nomen->tb5_nm."*".$nomen->T6Nm.'</td>
				</tr>
				<tr>
				  <td>Количество крепежных отверстий</td>
				  <td>'.$nomen->T7Nm.'</td>
				</tr>
				<tr>
				  <td>PCD</td>
				  <td>'.$nomen->T8Nm.'</td>
				</tr>
				<tr>
				  <td>Вылет</td>
				  <td>'.$nomen->T9Nm.'</td>
				</tr>
				<tr>
				   <td>Ступица</td>
				  <td>'.$nomen->tb12_nm.'</td>
				</tr>
				<tr>
				   <td>Тип</td>
				  <td>'.$nomen->T10Nm.'</td>
				</tr>
				<tr>
				   <td>Цвет</td>
				  <td>'.$nomen->T2Nm.'</td>
				</tr>
			  </tbody>
			</table>';
   if(trim($nomen->tovimg)){

    $pic = $nomen->tovimg;
   } else {

    $pic = $nomen->T4Pic;
   }
   $link = $nomen->T5Nm."*".$nomen->T6Nm . ' ' . $nomen->T7Nm . '/' . $nomen->T8Nm . ' ET' . $nomen->T9Nm .
    ($nomen->tb12_nm ? ' D' . $nomen->tb12_nm : '') . ($nomen->T2Nm ? ' ' . $nomen->T2Nm : '');
   $onclick =  'onclick="return ShowZoomWindow(true,\'' . addslashes($nomen->T3Nm) . ' ' .
    addslashes($nomen->T4Nm) . ($nomen->T2Nm ? ' ' . addslashes($nomen->T2Nm) : '') . '\',\'/images/tovar/discs/' . $pic . '\');"';

   $h=350;
  }

   if ($tov == 3) {

    $nomen = getAKB($tovObj->total_id);
    $fullTovName = 'АКБ ' . $nomen->voltname . 'В ' . $nomen->volname . 'А/ч ' . $nomen->T4Nm . ' ' . ($nomen->rvrt == 1 ? 'обратная' : 'прямая' );

    $title = 'Купить ' . $fullTovName;
    $keywords = 'Купить ' . $fullTovName;
    $descr = 'Купить ' . $fullTovName;

    $pic1="akb";
    $strPar = '<table>
			  <thead>
				<tr>
				  <th width="300">Название</th>
				  <th width="200">Значение</th>
				</tr>
			  </thead>
			  <tbody>
				<tr>
				   <td>Модель</td>
				  <td>'.$nomen->T4Nm.'</td>
				</tr>
                <tr>
				  <td>Емкость (Ач)</td>
				  <td>'.$nomen->volname.'</td>
				</tr>
				<tr>
				  <td>Напряжение (В)</td>
				  <td>'.$nomen->voltname.'</td>
				</tr>
				<tr>
				  <td>Полярность</td>
				  <td>' . ($nomen->rvrt == 1 ? 'обратная' : 'прямая' ) . '</td>
				</tr>
			  </tbody>
			</table>';
           if(trim($nomen->tovimg)){

            $pic = $nomen->tovimg;
           } else {

            $pic = $nomen->T4Pic;
           }
   $link = $fullTovName;
   //$onclick =  'onclick="return ShowZoomWindow(true,\'' . addslashes($nomen->T3Nm) . ' ' .
   // addslashes($nomen->T4Nm) . ($nomen->T2Nm ? ' ' . addslashes($nomen->T2Nm) : '') . '\',\'/images/tovar/discs/' . $pic . '\');"';

   $h=350;
  }
  $pic=ImageWork($pic, $tov, $nomen->tab2_id, $nomen->tab3_id, $nomen->tab4_id, $nomen->t2tr, $nomen->T3Nm, $nomen->T4Nm, $h);
  if(strpos($pic, 'nofoto')){

    $onclick = '';
    $style = ' style="cursor:default"';
  }

  $content .= '<div class="row"><ul class="breadcrumbs">
  <li><a href="/">Главная</a></li>
  <li><a href="/catalog/' . $nomen->t1url . '.html">' . ($tov == 1 ? 'Шины' : ($tov == 3 ? 'АКБ' : 'Диски')) . '</a></li>'.
  ($tov == 3 ? '' : '<li><a href="/modeli/' . $nomen->t1url . '/' . $nomen->t3url . '.html">' . $nomen->T3Nm . '</a></li>').
  '<li><a href="/razm/'.$nomen->t1url.'/' . ($tov == 3 ? '' : $nomen->t3url . '/') . $nomen->t4url . '.html">' . $nomen->T4Nm . '</a></li>
  <li class="current"><a href="/card/'.$nomen->tturl.'.html">' . $link . '</a></li>
	</ul>';

  /*if($nomen->tb3_dsc){
      $content .= '<div class="brand-dsc-razm">' . $nomen->tb3_dsc . '</div>';
  } */

  $content .= '<div class="large-4 columns">
			<a class="th radius" href="' . $pic . '">
			<img src="' . $pic . '">
			</a>
		</div>
    <div class="large-8 columns">
			<h2>' . $fullTovName . '</h2>' . $strPar;
    if($nomen->cnt > 0) {

        $content .= '<table>
			  <thead>
				<tr>
				  <th width="200">Цена</th>
				  <th width="150">Количество</th>
				  <th width="200">Оплата товара</th>
				</tr>
			  </thead>
			  <tbody>';
    $content .= '<form method="post" action="/bask/' . $nomen->total_id . '/add.html">
        <tr>
		    <td><b class="cena">' . (int)$nomen->price . ' рублей</b></td>
			<td>
                <input type="text" name="cnt" class="input-qty" value="'.($nomen->cnt >= 4 ? '4' : $nomen->cnt).'" />
                <input type="hidden" name="tov" value="' . $tov . '" />
            </td>
			<td>
                <input type="submit"  class="button" value=" Положить в корзину " name="put-only" style="width:177px">
                <input type="submit"  class="button" value=" Купить в один клик " name="put-buy" style="width:177px">
            </td>
		</tr>
			  </tbody>
			</table>';
    } else {
        $content .= '<p style="color:#f00;font-weight:bold;margin-left:50px">К сожалению товара уже нет в наличии</p>';
    }
    $content .= '<p>' . ($tov == 1 ? $nomen->description : $nomen->tovdsc) . '</p>
		</div>
	</div>';
?>