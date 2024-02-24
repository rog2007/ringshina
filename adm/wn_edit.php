<?php
// функции =====================================================================
// функция генерация <select> ==================================================
  function SelectStr($query,$selname,$id,$fl)
  {
    $res=mysql_query($query);
    $tmpstr='<select id = "' . $selname . '" name="'.$selname . '"' . ($selname == 't3' ? ' onchange="return getModels(\'t3\', \'t4\')"' : '') . '>'.($fl==1?'<option value="0"'.(!$id?'selected="selected"':'').'>Не указан</option>':'');
    while($rs_sel=mysql_fetch_object($res))
      $tmpstr.='<option value="'.$rs_sel->id.'"'.($id==$rs_sel->id?'selected="selected"':'').'>'.$rs_sel->nm.'</option>';
    $tmpstr.='</select>';
    return $tmpstr;
  }
// /функция генерация <select> ==================================================
// /функции ====================================================================
  switch($arg[0])
  {
    case 1:
      $str_error='';
      if(isset($_POST["save"]))
      {
        mysql_query("update power set t3=".$_POST["t3"].",t4=".$_POST["t4"].",t5=".$_POST["t5"].",t71=".$_POST["t71"].",t6=".$_POST["t6"].",t7=".$_POST["t7"].",
          t8=".$_POST["t8"].",run=".($_POST["run"]?"1":"0").",xl=".($_POST["xl"]?"1":"0").",zr='".($_POST["zr"]?'Z':'')."',diam_c='".($_POST["diam_c"]?'C':'')."',om='".($_POST["om"]=='0'?'':$_POST["om"])."' where id_pow='".$arg[1]."'");
      }
      if(isset($_POST["work"]))
      {
        // обновление tid
        mysql_query("update power LEFT JOIN total ON total.tab3_id = power.t3 AND total.tab4_id = power.t4 AND total.w_id = power.t5  AND total.h_id = power.t71
        AND total.tab6_id = power.t6 AND total.tab7_id = power.t7 AND total.tab8_id = power.t8 AND total.rof = power.run AND total.omolog = power.om
        set tid=total_id WHERE id_pow=".$arg[1]." and t1=1 and t3>0 and t4>0 and t5>0 and t6>0 and ((t7>0 and t8>0) or power.zr='Z') and tid=0 and total_id IS NOT NULL;");
        if(mysql_affected_rows()==0)
        {
          mysql_query("insert into total (tab1_id,tab2_id,tab3_id,tab4_id,w_id,h_id,tab6_id,tab7_id,tab8_id,tab9_id,tab10_id,rof,omolog,xl,zr,dc)
          (select t1,t2,t3,t4,t5,t71,t6,t7,t8,t9,t10,run,om,xl,zr,diam_c from power WHERE id_pow='".$arg[1]."' and tid=0 and t1=1 and t3>0 and t4>0 and t5>0 and t6>0 and ((t7>0 and t8>0) or power.zr='Z'))");
          if(mysql_affected_rows()>0) {

            $big_id=mysql_insert_id();
            $mes.="<p>Вставил новую позицию в справочник - <b>".$big_id."</b></p>";
            mysql_query("update total left join (SELECT total_id as tid,tb3_nm as t3nm, tb4_nm as t4nm, IF(omolog>'',concat(' ',omolog),'') as om, profw.name as t5nm,if(ifnull(profh.name,'')>'',concat('/',profh.name),'') as t5nmh, zr, tb6_nm as t6_nm, dc, ifnull(t7.tb7_nm,'') AS mn7, ifnull(t8.tb8_nm,'') AS mn8, IF(rof=1,concat(' ',ifnull(run_flat.var,'run flat')),'') as run
            FROM total LEFT JOIN tab3 ON tab3_id = tb3_id LEFT JOIN tab4 ON tab4_id = tb4_id LEFT JOIN profw ON w_id = profw.id LEFT JOIN profh ON h_id = profh.id LEFT JOIN tab6 ON tab6_id = tb6_id
            LEFT JOIN tab7 AS t7 ON tab7_id = t7.tb7_id LEFT JOIN tab8 AS t8 ON tab8_id = t8.tb8_id LEFT JOIN tab9 ON tab9_id = tb9_id
            left join run_flat on run_flat.br=tab3_id WHERE total_id=".$big_id.") as tb1 on total_id=tb1.tid set all_name = concat(t3nm,' ',t4nm,om,' ',t5nm,t5nmh,' ',tb1.zr,t6_nm,tb1.dc,' ',mn7,mn8,IF(xl=1,' XL',''),run)
            where total_id=".$big_id);
            $mes.="<p>Обновил AllName (".mysql_affected_rows().")</p>";
            mysql_query("update power set tid=".$big_id." WHERE id_pow='".$arg[1]."'");
            $mes.="<p>Обновил tid (".mysql_affected_rows().")</p>";
          }
        }
        else $mes.="<p>Обновил tid по параметрам</p>";

        $res = mysql_query("SELECT COUNT(*) AS cn from power left join total_suppl on id_sup=sspid and tid=id_tov and power.id = id_tov_sup
          where tid>0 and total_suppl.id_tov is not null and id_pow='" . $arg[1] . "'");
        $rs = mysql_fetch_object($res);
        if($rs->cn == 0) {

          mysql_query("INSERT INTO total_suppl (id_tov, id_sup, cnt_sup, prs_sup, prsb_sup, id_tov_sup, suppl_name)
          (SELECT tid, sspid, power.cnt, power.price, power.priceb, power.id, price_name
          from power left join total_suppl on id_sup=sspid and tid=id_tov and power.id = id_tov_sup
          where tid>0 and total_suppl.id_tov is null and id_pow='".$arg[1]."')");
          $mes .= "<p>Вставил в total_suppl (" . mysql_affected_rows() . ")</p>";
        } else {

          mysql_query("UPDATE total_suppl LEFT JOIN power ON id_sup = sspid AND tid = id_tov AND power.id = id_tov_sup
          SET cnt_sup = power.cnt, prs_sup = power.price, prsb_sup = power.priceb, suppl_name = price_name
          WHERE tid>0 AND id_pow='" . $arg[1] . "'");
          $mes .= "<p>Обновил запись в total_suppl (" . mysql_affected_rows() . ")</p>";
        }


      }
      $res=mysql_query("select price_name,t1,t3,t4,t5,t71,t6,t7,t8,t9,run,xl,om,zr,diam_c from power where id_pow='".$arg[1]."'");
      $rs=mysql_fetch_object($res);
      $str.='<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control1">'.$rs->price_name.'</td></tr>
      <tr><td class="title">Тип товара</td><td class="control1">'.SelectStr('select tb1_id as id,tb1_nm as nm from tab1 order by tb1_id','t1',$rs->t1,0).'</td></tr>
      <tr><td class="title">Бренд</td><td class="control1">'.SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id='.$rs->t1.' order by tb3_nm','t3',$rs->t3,1).'</td></tr>
      <tr><td class="title">Модель</td><td class="control1">'.SelectStr('select tb4_id as id,tb4_nm as nm from tab4 where tb4_tov_id='.$rs->t1 . ($rs->t3 ? ' and brand_id=' . $rs->t3 : '') . ' order by tb4_nm','t4',$rs->t4,1).'</td></tr>
      <tr><td class="title">Ширина пр.</td><td class="control1">'.SelectStr('select id,name as nm from profw order by name*1','t5',$rs->t5,1).'</td></tr>
      <tr><td class="title">Высота пр.</td><td class="control1">'.SelectStr('select id,name as nm from profh order by name*1','t71',$rs->t71,1).'</td></tr>
      <tr><td class="title">Диаметр</td><td class="control1">'.SelectStr('select tb6_id as id,tb6_nm as nm from tab6 where tb6_tov_id='.$rs->t1.' order by tb6_nm','t6',$rs->t6,1).'</td></tr>
      <tr><td class="title">И. Нагрузки</td><td class="control1">'.SelectStr('select tb7_id as id,tb7_nm as nm from tab7 where tb7_tov_id='.$rs->t1.' order by tb7_nm','t7',$rs->t7,1).'</td></tr>
      <tr><td class="title">И. Скорости</td><td class="control1">'.SelectStr('select tb8_id as id,tb8_nm as nm from tab8 where tb8_tov_id='.$rs->t1.' order by tb8_nm','t8',$rs->t8,1).'</td></tr>
      <tr><td class="title">XL</td><td class="control1"><input type="checkbox" name="xl" '.($rs->xl==1?'checked="checked"':'').' /></td></tr>
      <tr><td class="title">RunFlat</td><td class="control1"><input type="checkbox" name="run" '.($rs->run?'checked="checked"':'').' /></td></tr>
      <tr><td class="title">Омологация</td><td class="control1">'.SelectStr('select om as id,om as nm from omolog where omvis=1 order by om','om',$rs->om,1).'</td></tr>
      <tr><td class="title">ZR</td><td class="control1"><input type="checkbox" name="zr" '.($rs->zr?'checked="checked"':'').' /></td></tr>
      <tr><td class="title">C</td><td class="control1"><input type="checkbox" name="diam_c" '.($rs->diam_c?'checked="checked"':'').' /></td></tr>
      <tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /><input name="work" type="submit" value="обработать" /></td></tr>
      <tr><td colspan="2">'.$mes.'</td></tr></table></form>';
    break;
    case 2:
      if(isset($_POST["save"]))
      {
        mysql_query("update power set t2=".$_POST["t2"].",t3=".$_POST["t3"].",
          t4=".$_POST["t4"].",t5=".$_POST["t5"].",t6=".$_POST["t6"].",t7=".$_POST["t7"].",
          t8=".$_POST["t8"].",t9=".$_POST["t9"].",t71=".$_POST["t71"]." where id_pow='".$arg[1]."'");
      }
      if(isset($_POST["work"])) {

        // обновление tid
        mysql_query("update power LEFT JOIN total ON total.tab3_id = power.t3 AND total.tab4_id = power.t4 AND total.tab5_id = power.t5
          AND total.tab6_id = power.t6 AND total.tab7_id = power.t7 AND total.tab8_id = power.t8  AND total.tab9_id = power.t9 AND total.tab12_id = power.t71
          AND total.tab2_id = power.t2 set tid=total_id WHERE id_pow='" . $arg[1] .
          "' and total_id IS NOT NULL and tid=0 and t2>0 and t3>0 and t4>0 and t5>0 and t6>0 and t7>0 and t8>0 and t9>0 and t71>0;");
        if(mysql_affected_rows()==0)
        {
          mysql_query("insert into total (tab1_id,tab2_id,tab3_id,tab4_id,tab5_id,tab6_id,tab7_id,tab8_id,tab9_id,tab12_id)
          (select t1,t2,t3,t4,t5,t6,t7,t8,t9,t71 from power WHERE id_pow='".$arg[1]."' and tid=0 and t1=2 and t3>0 and t4>0 and t5>0 and t6>0 and t7>0 and t8>0 and t9>0 and t71>0 group by t1,t2,t3,t4,t5,t6,t7,t8,t9,t71)");
          if(mysql_affected_rows()>0)
          {
            $big_id = mysql_insert_id();
            $mes.="<p>Вставил новую позицию в справочник - <b>" . $big_id . "</b></p>";
            mysql_query("update total LEFT JOIN(SELECT total_id AS tid, t4ses, ifnull( concat( ' ', tab2.translit ) , '' ) t2mn, " .
                "tb3_nm AS t3nm, tb4_nm AS t4nm, tb5_nm AS t5nm, tb6_nm AS t6_nm, t7.tb7_nm AS mn7, t8.tb8_nm AS mn8,
            ifnull( concat( ' ET', tb9_nm ) , '' ) AS mn9, ifnull( concat( ' D', tb12_nm ) , '' ) AS mn12
            FROM total LEFT JOIN tab2 ON tab2_id = tb2_id LEFT JOIN tab3 ON tab3_id = tb3_id LEFT JOIN tab4 ON tab4_id = tb4_id
            LEFT JOIN tab5 ON tab5_id = tb5_id
            LEFT JOIN tab6 ON tab6_id = tb6_id LEFT JOIN tab7 AS t7 ON tab7_id = t7.tb7_id LEFT JOIN tab8 AS t8 ON tab8_id = t8.tb8_id
            LEFT JOIN tab9 ON tab9_id = tb9_id
            LEFT JOIN tab12 ON tab12_id = tb12_id WHERE total_id=".$big_id.") AS tb1
            ON total_id = tb1.tid set all_name=concat( t3nm, ' ', t4nm, ' ', t5nm, 'x', t6_nm, ' ', mn7, '/', mn8, mn9, mn12, t2mn ), tab10_id = t4ses
            WHERE total_id=".$big_id);
            $mes.="<p>Обновил AllName (".mysql_affected_rows().")</p>";
            mysql_query("update power set tid=".$big_id." WHERE id_pow='".$arg[1]."'");
            $mes.="<p>Обновил tid (".mysql_affected_rows().")</p>";
          }
        } else {
          $mes.="<p>Обновил tid по параметрам</p>";
        }

         $res = mysql_query("SELECT count(*) AS cn FROM power LEFT JOIN total_suppl ON total_suppl.id_tov = power.tid " .
            "AND total_suppl.id_sup = power.sspid AND total_suppl.id_tov_sup = power.id " .
            "WHERE power.id_pow='" . $arg[1] . "' and total_suppl.id_tov IS NOT NULL;");

         $rs = mysql_fetch_object($res);
         if($rs->cn == 0) {

            mysql_query("INSERT INTO total_suppl (id_tov,id_sup,cnt_sup,prs_sup,prsb_sup,id_tov_sup, suppl_name)
        (select tid,sspid,power.cnt,power.price,power.priceb,power.id, price_name
        from power left join total_suppl on id_sup=sspid and tid=id_tov and power.id = id_tov_sup
        where tid>0 and total_suppl.id_tov is null and id_pow='".$arg[1]."')");
        $mes.="<p>Вставил в total_suppl (".mysql_affected_rows().")</p>";
         } else {

            mysql_query("UPDATE total_suppl LEFT JOIN power ON total_suppl.id_tov = power.tid " .
            "AND total_suppl.id_sup = power.sspid AND total_suppl.id_tov_sup = power.id " .
            "SET total_suppl.cnt_sup = power.cnt, total_suppl.prs_sup = power.price, total_suppl.prsb_sup = power.priceb, suppl_name = price_name ".
            "WHERE power.id_pow='" . $arg[1] . "' and total_suppl.id_tov IS NOT NULL;");
            $mes.="<p>Обновил в total_suppl (" . mysql_affected_rows() . ")</p>";
         }
      }
      $res=mysql_query("select price_name,t1,t2,t3,t4,t5,t6,t7,t8,t9,t71 from power where id_pow='".$arg[1]."'");
      $rs=mysql_fetch_object($res);
      $str.='<form enctype="multipart/form-data" action="" method="post"><table class="ed">
      <tr><td class="title">Наименование</td><td class="control1">'.$rs->price_name.'</td></tr>
      <tr><td class="title">Тип товара</td><td class="control1">'.SelectStr('select tb1_id as id,tb1_nm as nm from tab1 order by tb1_id','t1',$rs->t1,0).'</td></tr>
      <tr><td class="title">Бренд</td><td class="control1">'.SelectStr('select tb3_id as id,tb3_nm as nm from tab3 where tb3_tov_id='.$rs->t1.' order by tb3_nm','t3',$rs->t3,1).'</td></tr>
      <tr><td class="title">Модель</td><td class="control1">'.SelectStr('select tb4_id as id,tb4_nm as nm from tab4 where tb4_tov_id='.$rs->t1.' and brand_id='.$rs->t3.' order by tb4_nm','t4',$rs->t4,1).'</td></tr>
      <tr><td class="title">Ширина</td><td class="control1">'.SelectStr('select tb5_id as id,tb5_nm as nm from tab5 where tb5_tov_id='.$rs->t1.' order by tb5_nm','t5',$rs->t5,1).'</td></tr>
      <tr><td class="title">Диаметр</td><td class="control1">'.SelectStr('select tb6_id as id,tb6_nm as nm from tab6 where tb6_tov_id='.$rs->t1.' order by tb6_nm','t6',$rs->t6,1).'</td></tr>
      <tr><td class="title">Отверстия</td><td class="control1">'.SelectStr('select tb7_id as id,tb7_nm as nm from tab7 where tb7_tov_id='.$rs->t1.' order by tb7_nm','t7',$rs->t7,1).'</td></tr>
      <tr><td class="title">PCD</td><td class="control1">'.SelectStr('select tb8_id as id,tb8_nm as nm from tab8 where tb8_tov_id='.$rs->t1.' order by tb8_nm','t8',$rs->t8,1).'</td></tr>
      <tr><td class="title">Выслет</td><td class="control1">'.SelectStr('select tb9_id as id,tb9_nm as nm from tab9 where tb9_tov_id='.$rs->t1.' order by tb9_nm','t9',$rs->t9,1).'</td></tr>
      <tr><td class="title">Ступица</td><td class="control1">'.SelectStr('select tb12_id as id,tb12_nm as nm from tab12 where tb12_tov_id='.$rs->t1.' order by tb12_nm','t71',$rs->t71,1).'</td></tr>
      <tr><td class="title">цвет</td><td class="control1">'.SelectStr('select tb2_id as id,tb2_nm as nm from tab2 where tb2_tov_id='.$rs->t1.' and brid='.$rs->t3.' order by tb2_nm','t2',$rs->t2,1).'</td></tr>
      <tr><td colspan="2" class="but"><input name="save" type="submit" value="сохранить" /><input name="work" type="submit" value="обработать" /></td></tr>
       <tr><td colspan="2">'.$mes.'</td></tr></table></form>';
    break;
  }
?>