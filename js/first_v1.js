jQuery(document).ready(function(){

  if(jQuery('div.select select').length > 0){
    jQuery('div.select select').bind('change', changeLable);
  }

  /*if($('#tyre-tab').length > 0){
    $('#tyre-tab').bind('click', topTabClick);
    $('#disc-tab').bind('click', topTabClick);
    $('#mtyre-tab').bind('click', topTabClick);
  } */

  if(jQuery('.inc').length > 0){
    jQuery('.inc').on('click', changeQtyInc);
  }

  if(jQuery('.dec').length > 0){
    jQuery('.dec').on('click', changeQtyDec);
  }

  if(jQuery('#tyre-menu').length > 0){
    jQuery('#tyre-menu .menu-item-middle').on('click', showSubMenu);
  }

  if(jQuery('#disc-menu').length > 0){
    jQuery('#disc-menu .menu-item-middle').on('click', showSubMenu);
  }

  if(jQuery('#part-menu').length > 0){
    jQuery('#part-menu .menu-item-middle').on('click', showSubMenu);
  }

  if(jQuery('#info-menu').length > 0){
    jQuery('#info-menu .menu-item-middle').on('click', showSubMenu);
  }

});

function changeDostCost(cost){

  document.getElementById('itog').innerHTML = 'Общая стоимость, с учетом доставки: <span id="allsum">' + cost + '</span> Руб';
}

function showSubMenu(){

  var fl = 0;
  var name = jQuery(this).parent().attr('id');
  if(jQuery('#sub-'+name).css('display') == 'none'){
    fl = 1;
  }
  jQuery('.sub-menu-pp').css('display','none');
  jQuery('.menu-item').removeClass('sel');

  if(fl == 1){

    jQuery('#sub-'+name).css('display','block');
    jQuery(this).parent().addClass('sel');

  }
  return false;
}

function changeQtyInc(){

  var input = jQuery(this).parent().find('.input-qty');
  input.val(input.val() * 1 + 1);
  input.change();
  return false;
}

function changeQtyDec(){

  var input = jQuery(this).parent().find('.input-qty');
  if(input.val() * 1 == 0) return false;
  input.val(input.val() * 1 - 1);
  input.change();
  return false;
}

function changeLable(){

  var lableName = '#'+jQuery(this).attr('id')+'-lable';
  jQuery(lableName).text(this.options[this.selectedIndex].text);
}
function topTabClick(){
  var classNames = ['tyre-select', 'disc-select', 'mtyre-select'];
  var curTab = jQuery(this);
  var tabsTop = jQuery('#tabs-top');
  var rArray = curTab.attr('id').split('-');
  var newClassName = rArray[0]+'-select';
  if(tabsTop.hasClass(newClassName)) return false;
  for(var i=classNames.length-1; i>=0; i--) {
	  tabsTop.removeClass(classNames[i]);
	}
  tabsTop.addClass(rArray[0]+'-select');
  if('#tabs-tyre-tab' == '#tabs-'+jQuery(this).attr('id')){
    jQuery('#tyre-calc').css('display','block');
    jQuery('.left-tabs .clear').css('height','47px');
  }
  else{
    jQuery('#tyre-calc').css('display','none');
    jQuery('.left-tabs .clear').css('height','96px');
  }

  jQuery('#tabs-tyre-tab').css('display','none');
  jQuery('#tabs-disc-tab').css('display','none');
  jQuery('#tabs-mtyre-tab').css('display','none');
  if(jQuery('#tabs-pauto-tab:visible').length == 0){
    jQuery('#tabs-'+jQuery(this).attr('id')).css('display','block');
  }

  if(jQuery(this).attr('id') == 'tyre-tab'){
    jQuery('#catalog-tab').text('Каталог шин').attr('href','/catalog/shini.html');
    jQuery('#podborparam-tab').attr('href','/param/shini/');
  }
  if(jQuery(this).attr('id') == 'disc-tab'){
    jQuery('#catalog-tab').text('Каталог дисков').attr('href','/catalog/diski.html');
    jQuery('#podborparam-tab').attr('href','/param/diski/');
  }

}
