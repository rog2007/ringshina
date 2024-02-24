function getModels(firm, model){

  var selectBrand = document.getElementById(firm);
  var selectModel = document.getElementById(model);

  var sBody='step=1&firm=' + encodeURIComponent(selectBrand.options[selectBrand.selectedIndex].value);
  var oXmlHttp=zXmlHttp.createRequest();
  oXmlHttp.open("post","/adm/ajax.php",true);
  oXmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  oXmlHttp.onreadystatechange = function () {
    if (oXmlHttp.readyState == 4){

      if (oXmlHttp.status==200){

        ClearSelect(selectModel,0);
        var node = document.createElement ('option');
        node.value = TrimString("0");
        node.appendChild (document.createTextNode("все"));
        selectModel.appendChild (node);
        AddSelectPodb(selectModel,oXmlHttp.responseText,"0");
        selectModel.selectedIndex=0;
      }
    }
  }
  oXmlHttp.send(sBody);
  return false;
}

function getModelsColors(firm, model, color){

  var selectBrand = document.getElementById(firm);
  var selectModel = document.getElementById(model);
  var selectColor = document.getElementById(color);

  var sBody='step=1&firm=' + encodeURIComponent(selectBrand.options[selectBrand.selectedIndex].value);
  var oXmlHttp=zXmlHttp.createRequest();
  oXmlHttp.open("post","/adm/ajax.php",true);
  oXmlHttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  oXmlHttp.onreadystatechange = function () {
    if (oXmlHttp.readyState == 4){

      if (oXmlHttp.status==200){

        ClearSelect(selectModel,0);
        var node = document.createElement ('option');
        node.value = TrimString("0");
        node.appendChild (document.createTextNode("все"));
        selectModel.appendChild (node);
        AddSelectPodb(selectModel,oXmlHttp.responseText,"0");
        selectModel.selectedIndex=0;
      }
    }
  }
  oXmlHttp.send(sBody);
  var sBody1='step=2&firm=' + encodeURIComponent(selectBrand.options[selectBrand.selectedIndex].value);
  var oXmlHttp1=zXmlHttp.createRequest();
  oXmlHttp1.open("post","/adm/ajax.php",true);
  oXmlHttp1.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
  oXmlHttp1.onreadystatechange = function () {
    if (oXmlHttp1.readyState == 4){

      if (oXmlHttp1.status==200){

        ClearSelect(selectColor,0);
        var node = document.createElement ('option');
        node.value = TrimString("0");
        node.appendChild (document.createTextNode("все"));
        selectColor.appendChild (node);
        AddSelectPodb(selectColor,oXmlHttp1.responseText,"0");
        selectColor.selectedIndex=0;
      }
    }
  }
  oXmlHttp1.send(sBody1);
  return false;
}

function selectAllTov(el){

  if(jQuery(el).is(':checked')){

    jQuery('#nomen .chb input:checkbox').attr('checked','checked');
  } else {

    jQuery('#nomen .chb input:checkbox').removeAttr('checked');
  }
}

$(document).ready(function() {
    $("a.gallery").lightBox();
    $("#vilet_from").change(function(){
      if(Number.isNaN(parseInt($("#vilet_from option:selected").text())) ||
          Number.isNaN(parseInt($("#vilet_to option:selected").text())) ||
          parseInt($("#vilet_to option:selected").text()) < parseInt($("#vilet_from option:selected").text())) {
          $("#vilet_to").val(parseInt($("#vilet_from option:selected").val()));
      }
    });
    $("#vilet_to").change(function(){
        if(Number.isNaN(parseInt($("#vilet_from option:selected").text())) ||
            Number.isNaN(parseInt($("#vilet_to option:selected").text())) ||
            parseInt($("#vilet_to option:selected").text()) < parseInt($("#vilet_from option:selected").text())) {
            $("#vilet_from").val(parseInt($("#vilet_to option:selected").val()));
        }
    });
});