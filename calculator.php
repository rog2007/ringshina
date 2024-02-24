<section>
			<p class="title" data-section-title><a href="#">Шинный калькулятор</a></p>
			<div class="content" data-section-content>
			  <div class="row">
				<div class="large-5 columns">
					<h5>Шинный калькулятор</h5>
					<img src="/img/calc.png" alt="Схема колеса"/>
					<h6>Как пользоваться калькулятором шин?</h6>
					<p>Введите сначала типоразмер установленный на вашем автомобиле, а затем тот, который вы хотите установить и нажмите «расчитать». В таблице снизу будут показаны результаты расчетов калькулятора.</p>
				</div>
				<div class="large-7 columns">
					<div class="row">
					  <div class="large-6 columns">
						<form class="custom">
						  <label for="prfwc"><b>Настоящий типоразмер</b></label>
						  <select id="prfwc" name="prfw" class="no-custom small">
<?php
  $selProfWidth = $dbcon->prepare('select id, name from profw where vis=1 and (name*1) > 100 order by name*1');
  if ($selProfWidth->execute() && $selProfWidth->rowCount() > 0) {
    while ($resObj = $selProfWidth->fetch(PDO::FETCH_OBJ)) {
      echo '<option value="'.$resObj->name.'"'.(205==$resObj->name?' selected="selected"':'').'>'.$resObj->name.'</option>';
    }
  }
?>
              </select>
						  <label for="prfhc"><b>/</b></label>
						  <select id="prfhc" name="prfh" class="no-custom small">
<?php
  $selProfHeight = $dbcon->prepare('select id, name from profh where vis=1 and (name*1) > 20 order by name*1');
  if ($selProfHeight->execute() && $selProfHeight->rowCount() > 0) {
    while ($resObj = $selProfHeight->fetch(PDO::FETCH_OBJ)) {
     echo '<option value="'.$resObj->name.'"'.(55==$resObj->name?' selected="selected"':'').'>'.$resObj->name.'</option>';
    }
  }
?>
                  </select>
						  <label for="customDropdown"><b>R</b></label>
						  <select id="diamc" name="diam" class="no-custom small">
<?php
  $selDiam = $dbcon->prepare('select tb6_id as id, tb6_nm as name from tab6 where tb6_tov_id=1 and tb6_vis=1 order by tb6_nm');
  if ($selDiam->execute() && $selDiam->rowCount() > 0) {
    while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
      if(strpos($resObj->name,'C')) continue;
      echo '<option value="'.str_ireplace('R','',$resObj->name).'"'.('R16' == $resObj->name ? ' selected="selected"' : '').'>'.str_ireplace('R','',$resObj->name).'</option>';
    }
  }
?>
                  </select>
						</form>
					  </div>
					  <div class="large-6 columns">
						<form class="custom">
						  <label for="prfwc2"><b>Предполагаемый тип размера</b></label>
						  <select id="prfwc2" name="prfw2" class="no-custom small">
<?php
  $selProfWidth = $dbcon->prepare('select id, name from profw where vis=1 and (name*1) > 100 order by name*1');
  if ($selProfWidth->execute() && $selProfWidth->rowCount() > 0) {
    while ($resObj = $selProfWidth->fetch(PDO::FETCH_OBJ)) {
      echo '<option value="'.$resObj->name.'"'.(205==$resObj->name?' selected="selected"':'').'>'.$resObj->name.'</option>';
    }
  }
?>
              </select>
						  <label for="prfhc2"><b>/</b></label>
						  <select id="prfhc2" name="prfh2" class="no-custom small">
<?php
  $selProfHeight = $dbcon->prepare('select id, name from profh where vis=1 and (name*1) > 20 order by name*1');
  if ($selProfHeight->execute() && $selProfHeight->rowCount() > 0) {
    while ($resObj = $selProfHeight->fetch(PDO::FETCH_OBJ)) {
     echo '<option value="'.$resObj->name.'"'.(55==$resObj->name?' selected="selected"':'').'>'.$resObj->name.'</option>';
    }
  }
?>
						  </select>
						  <label for="diamc2"><b>R</b></label>
						   <select id="diamc2" name="diam2" class="no-custom small">
<?php
  $selDiam = $dbcon->prepare('select tb6_id as id, tb6_nm as name from tab6 where tb6_tov_id=1 and tb6_vis=1 order by tb6_nm');
  if ($selDiam->execute() && $selDiam->rowCount() > 0) {
    while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
      if(strpos($resObj->name,'C')) continue;
      echo '<option value="'.str_ireplace('R','',$resObj->name).'"'.('R16' == $resObj->name ? ' selected="selected"' : '').'>'.str_ireplace('R','',$resObj->name).'</option>';
    }
  }
?>
						  </select>
						</form>
					  </div>
					</div>
					<a href="#" class="button"  onclick="return td__(1);">Расчитать</a>
					<!-- Таблица расчета -->
					<h6>Результаты расчета размеров.</h6>
					<table>
					  <thead>
						<tr>
						  <th width="200">Размеры</th>
						  <th width="150">Старый</th>
						  <th width="150">Новый</th>
						  <th>Разница</th>
						</tr>
					  </thead>
					  <tbody>
						<tr>
						  <td>Ширина шины A</td>
						  <td id="w_old"></td>
						  <td id="w_new"></td>
						  <td id="w_raz"></td>
						</tr>
						<tr>
						  <td>Высота шины H</td>
						  <td id="h_old"></td>
						  <td id="h_new"></td>
						  <td id="h_raz"></td>
						</tr>
						<tr>
						  <td>Внутренний диаметр R</td>
						  <td id="r_old"></td>
						  <td id="r_new"></td>
						  <td id="r_raz"></td>
						</tr>
						<tr>
						  <td>Внешний диаметр D</td>
						  <td id="rv_old"></td>
						  <td id="rv_new"></td>
						  <td id="rv_raz"></td>
						</tr>
						<tr>
						  <td>Расчет изменения клиренса</td>
						  <td id="klir"></td>
						</tr>
					  </tbody>
					</table>
					<h6>Результаты расчета изменений показания спидометра.</h6>
					<table>
					  <thead>
						<tr>
						  <th width="200">Показания спидометра</th>
						  <th width="150">Реальная</th>
						  <th width="150">Разница</th>
						</tr>
					  </thead>
					  <tbody>
						<tr>
						  <td><input type="text" value="100" id="speed" style="width:80px"
           onkeypress="if ( event.keyCode == 13 ) return td__(0);" onfocus="return focs('speed');" onchange="return td__(0);" /></td>
						  <td id="new_speed"></td>
						  <td id="raz_speed"></td>
						</tr>
					  </tbody>
					</table>
				</div>
			  </div>
			  <div class="row">
				<div class="large-5 columns">
					<h5>Калькулятор дюймов</h5>
					<h6>Как пользоваться калькулятором шины-дюймы?</h6>
					<p>Для пересчета американсих размеров шин в дюймах в европеские типоразмеры в мм, Вы можете воспользоваться шинным калькулятором для дюймовых размеров. К примеру, Вам нужно пересчитать американский типоразмер шины с маркировкой 31Х10.5 R15 - в европейский. Мы вводим 31 х 10.5 х 15 и получаем результат расчета: 267/76 R15, а чуть ниже вы будет показан округленный до европейского стандарта типоразмер 265/75 R15.</p>
				</div>
				<div class="large-7 columns">
					<div class="row">
					  <div class="large-4 columns">
						<form class="custom">
						  <label for="prfwc4"><b>Укажите типоразмер</b></label>
						  <select id="prfwc4" name="prfw4" class="no-custom">
<?php
  $selProfWidth = $dbcon->prepare('select profw.id as id, name from profw left join calc2 on profw.id = calc2.w_id
  where calc2.id is not null group by name order by name*1');
  if ($selProfWidth->execute() && $selProfWidth->rowCount() > 0) {
    while ($resObj = $selProfWidth->fetch(PDO::FETCH_OBJ)) {
      echo '<option value="'.$resObj->id.'"'.(31==$resObj->name?' selected="selected"':'').'>'.$resObj->name.'</option>';
    }
  }
?>
              </select>
						  <label for="prfhc4"><b>/</b></label>
						  <select id="prfhc4" name="prfh4" class="no-custom">
<?php
  $selProfHeight = $dbcon->prepare('select profh.id as id, name from profh left join calc2 on profh.id = calc2.h_id
  where calc2.id is not null group by name order by name*1');
  if ($selProfHeight->execute() && $selProfHeight->rowCount() > 0) {
    while ($resObj = $selProfHeight->fetch(PDO::FETCH_OBJ)) {
      echo '<option value="'.$resObj->id.'"'.('10,5' == $resObj->name?' selected="selected"':'').'>'.$resObj->name.'</option>';
    }
  }
?>
                  </select>
						  <label for="diamc4"><b>R</b></label>
						  <select id="diamc4" name="diam4" class="no-custom">
<?php
  $selDiam = $dbcon->prepare('select tb6_id as id, tb6_nm as name from tab6 left join calc2 on tb6_id = calc2.r_id
  where calc2.id is not null group by tb6_nm order by tb6_nm');
  if ($selDiam->execute() && $selDiam->rowCount() > 0) {
    while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
      if(strpos($resObj->name,'C')) continue;
      echo '<option value="'.$resObj->id.'"'.('R15' == $resObj->name ? ' selected="selected"' : '').'>'.str_ireplace('R','',$resObj->name).'</option>';
    }
  }
?>
                  </select>
						</form>
						<a href="#" class="button"  onclick="return calc2()">Расчитать</a>
					  </div>
					  <div class="large-8 columns">
						<table>
						  <thead>
							<tr>
							  <th width="300">Пересчет дюймового типоразмера шин</th>
							  <th width="150"></th>
							</tr>
						  </thead>
						  <tbody>
							<tr>
							  <td>Американский типоразмер</td>
							  <td id="usasz">31 х 10.5 х 15</td>
							</tr>
							<tr>
							  <td>Метрический размер</td>
							  <td id="calcsz">267/76 R15</td>
							</tr>
							<tr>
							  <td>Округленный до европейского </td>
							  <td id="eurosz">265/75 R15</td>
							</tr>
						  </tbody>
						</table>
					  </div>
					</div>
				</div>
			  </div>
			  <div class="row">
				<div class="large-5 columns">
					<h5>Расчет ширины диска</h5>
					<h6>Как пользоваться калькулятором расчета ширины диска?</h6>
					<p>При подборе дисков для вашего автомобиля необходимо точно знать минимальную и максимальную ширину диска подходящую для данного типоразмера шины. Для расчета ширины диска вы можете воспользоваться этим разделом шинного калькулятора.</p>

				</div>
				<div class="large-7 columns">
					<div class="row">
					  <div class="large-4 columns">
						<form class="custom">
						  <label for="prfwc3"><b>Укажите типоразмер</b></label>
						  <select id="prfwc3" name="prfw3" class="no-custom">
<?php
  $selProfWidth = $dbcon->prepare('select profw.id as id, name from profw left join calc3 on profw.id = calc3.w_id
  where calc3.id is not null group by name order by name*1');
  if ($selProfWidth->execute() && $selProfWidth->rowCount() > 0) {
    while ($resObj = $selProfWidth->fetch(PDO::FETCH_OBJ)) {
      echo '<option value="'.$resObj->id.'"'.(205==$resObj->name?' selected="selected"':'').'>'.$resObj->name.'</option>';
    }
  }
?>
              </select>
						  <label for="customDropdown"><b>/</b></label>
						 <select id="prfhc3" name="prfh3" class="no-custom">
<?php
  $selProfHeight = $dbcon->prepare('select profh.id as id, name from profh left join calc3 on profh.id = calc3.h_id
  where calc3.id is not null group by name order by name*1');
  if ($selProfHeight->execute() && $selProfHeight->rowCount() > 0) {
    while ($resObj = $selProfHeight->fetch(PDO::FETCH_OBJ)) {
      echo '<option value="'.$resObj->id.'"'.(55==$resObj->name?' selected="selected"':'').'>'.$resObj->name.'</option>';
    }
  }
?>
                  </select>
						  <label for="customDropdown"><b>R</b></label>
						  <select id="diamc3" name="diam" class="no-custom">
<?php
  $selDiam = $dbcon->prepare('select tb6_id as id, tb6_nm as name from tab6 left join calc3 on tb6_id = calc3.r_id
  where calc3.id is not null group by tb6_nm order by tb6_nm');
  if ($selDiam->execute() && $selDiam->rowCount() > 0) {
    while ($resObj = $selDiam->fetch(PDO::FETCH_OBJ)) {
      if(strpos($resObj->name,'C')) continue;
        echo '<option value="'.$resObj->id.'"'.('R16' == $resObj->name ? ' selected="selected"' : '').'>'.str_ireplace('R','',$resObj->name).'</option>';
    }
  }
?>
                  </select>
						</form>
						<a href="#" class="button"  onclick="return calc3()">Расчитать</a>
					  </div>
					  <div class="large-8 columns">
						<table>
						  <thead>
							<tr>
							  <th width="300">Параметры диска</th>
							  <th width="150"></th>
							</tr>
						  </thead>
						  <tbody>
							<tr>
							  <td>Диаметр диска, дюймы</td>
							  <td id="ddisc"></td>
							</tr>
							<tr>
							  <td>Минимальная ширина диска, дюймы</td>
							  <td id="dminw"></td>
							</tr>
							<tr>
							  <td>Максимальная ширина диска, дюймы</td>
							  <td id="dmaxw"></td>
							</tr>
						  </tbody>
						</table>
					  </div>
					</div>
				</div>
			  </div>
			</div>
		  </section>