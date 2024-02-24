<?php
  function add_good_bask($id_good, $tov_cnt, $tov_id) {

    global $uid;
    $res = mysql_query("select order_tmp_id from order_tmp where us_id='$uid' and id_name=$id_good");
    $num = mysql_num_rows($res);
    if ($num>0)
      mysql_query("update order_tmp set cnt=cnt+$tov_cnt where us_id='$uid' and id_name=$id_good");
    else
      mysql_query("insert into order_tmp (cnt, us_id, id_name, id_tov) values($tov_cnt,'$uid',$id_good, $tov_id)");
  }

  function CurBasket(){

    global $uid;
    return $res = mysql_query("SELECT id_tov, id_name, order_tmp_id FROM order_tmp WHERE us_id = '$uid'");
  }

  function CurBasketRow($rowId, $tov) {

    global $uid;

   if($tov < 3){

    $sql = "select total_id, tab1.translit as tov, total.url as turl, tab1_id,
      all_name,order_tmp.cnt as ord_cnt,price,price*order_tmp.cnt as all_cnt,
      imgs.imgname as T4Pic, tab2_id, tab3_id, tab4_id, tab2.translit as t2tr, tb3_nm as T3Nm, tb4_nm as T4Nm
      from order_tmp left join total on total.total_id=order_tmp.id_name
      LEFT JOIN imgs ON tab4_id = imgs.idmodel AND imgs.idcolor=IF(tab1_id = 1, 0, tab2_id)
      LEFT JOIN tab1 on tab1_id = tb1_id LEFT JOIN tab2 on tab2_id = tb2_id
      LEFT JOIN tab3 on tab3_id = tb3_id LEFT JOIN tab4 on tab4_id = tb4_id
      where order_tmp_id = " . $rowId;
   } else {

    $sql = "select akb_tovar.id as total_id, 'akb' as tov, akb_tovar.url as turl,
      3 AS tab1_id, full_name as all_name, order_tmp.cnt as ord_cnt, price, 0 AS tab2_id, '' as t2tr,
      price*order_tmp.cnt as all_cnt, akb_model.pic as T4Pic, '' as tab2_id,
      akb_brand.id as tab3_id, akb_model.id as tab4_id, akb_brand.name as T3Nm,
      akb_model.name as T4Nm
      FROM order_tmp LEFT JOIN akb_tovar on akb_tovar.id = order_tmp.id_name
      LEFT JOIN akb_brand ON akb_brand.id = id_brand
      LEFT JOIN akb_model ON akb_model.id = id_model where order_tmp_id = " . $rowId;
   }

   return mysql_query($sql);
  }

  function ClearBasket()
  {
    global $uid;
    return mysql_query("delete from order_tmp where us_id='$uid'");
  }
  function DelBasketPos($id_good)
  {
    global $uid;
    mysql_query("delete from order_tmp where us_id='$uid' and id_name=$id_good");
  }
  function UpdBasketPos($id_g,$cnt)
  {
    global $uid;
    mysql_query("update order_tmp set cnt=$cnt where us_id='$uid' and id_name=$id_g");
  }
?>