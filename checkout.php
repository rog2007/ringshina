<?php

  $content .= '<div id="checkout">
    <div class="head"><h1>Оформление заказа</h1></div>
    <form method="post" action="/buy/" name="ord">
      <div class="ch-line">
        <div class="caption">ФИО <span>*</span></div>
        <div class="input">
          <div class="input-left"></div>
          <input type="text" name="fio" style="width:200px" value="" />
          <div class="input-right"></div>
        </div>
      </div>
      <div class="ch-line">
        <div class="caption">Телефон <span>*</span></div>
        <div class="input">
          <div class="input-left"></div>
          <input type="text" name="tel" style="width:200px" value="" />
          <div class="input-right"></div>
        </div>
      </div>
      <div class="ch-line">
        <div class="caption">E-mail</div>
        <div class="input">
          <div class="input-left"></div>
          <input type="text" name="e_mail" style="width:200px" value="" />
          <div class="input-right"></div>
        </div>
      </div>
      <div class="ch-line" style="height:60px">
        <div class="caption">Примечание</div>
        <div class="textarea">
          <div class="textarea-left"></div>
          <textarea type="text" name="info"></textarea>
          <div class="textarea-right"></div>
        </div>
      </div>
      <div class="ch-line">
        <div class="caption">Адрес</div>
        <div class="input">
          <div class="caption" style="width:150px">Город</div>
          <div class="caption" style="width:150px">Улица</div>
          <div class="caption" style="width:110px">Дом, корп., Кв.</div>
        </div>
      </div>
      <div class="ch-line">
        <div class="caption"></div>
        <div class="input" style="width:150px">
          <div class="input-left"></div>
          <input type="text" name="city" style="width:130px" value="" />
          <div class="input-right"></div>
        </div>
        <div class="input" style="width:150px">
          <div class="input-left"></div>
          <input type="text" name="street" style="width:130px" value="" />
          <div class="input-right"></div>
        </div>
        <div class="input" style="width:110px">
          <div class="input-left"></div>
          <input type="text" name="adr_dop" style="width:90px" value="" />
          <div class="input-right"></div>
        </div>
      </div>
      <div class="ch-line" style="height:80px">
        <div class="caption" style="height:80px">Способ доставки</div>
        <div class="radio-set">
          <div class="radio">
            <input type="radio" id="dost1" name="dost" value="1" />
            <label for="dost1"><span>Самовывозом (г. Мытищи)</span></label>
          </div>
          <div class="dop-info">бесплатно в любой день. Адрес:</div>
          <div class="radio" style="margin-top:10px">
            <input type="radio" id="dost2" name="dost" value="2" />
            <label for="dost2"><span>Курьером (на следующий день)</span></label>
          </div>
          <div class="dop-info">500 руб. в любой день</div>
        </div>
      </div>
      <div class="ch-line" style="height:80px">
        <div class="caption" style="height:80px">Способ оплаты</div>
        <div class="radio-set">
          <div class="radio">
            <input type="radio" id="opl1" name="opl" value="1" />
            <label for="opl1"><span>Наличными</span></label>
          </div>
          <div class="radio" style="margin-top:10px">
            <input type="radio" id="opl2" name="opl" value="2" />
            <label for="opl2"><span>Безналичный расчет</span></label>
          </div>
          <div class="radio" style="margin-top:10px">
            <input type="radio" id="opl3" name="opl" value="3" />
            <label for="opl3"><span>Банковская карта</span></label>
          </div>
        </div>
      </div>
      <div class="ch-line" style="margin-top:20px">
        <input class="buy" type="submit" value="" />
      </div>
    </form>
  </div>';
?>