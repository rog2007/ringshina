<?php
  header("Content-type: text/plain; charset=windows-1251");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  require ("connect.php");
  function normalize_mysqldate ($mysqldate){return $mysqldate[8].$mysqldate[9].".".$mysqldate[5].$mysqldate[6].".".$mysqldate[2].$mysqldate[3];}
  function to_mysqldate ($normaldate){$normaldate = trim($normaldate);return "20".$normaldate[6].$normaldate[7]."-".$normaldate[3].$normaldate[4]."-".$normaldate[0].$normaldate[1];}
  if($_POST["tran"]==9)
  {
    $tbl=mysql_query("select nm,query from adm_veiws where id=".$_POST["tbid"]);
    if($r_tbl=mysql_fetch_object($tbl))
    {
      $view_nm=$r_tbl->nm;
      $view_qw=$r_tbl->query;
    }
    $tbl=mysql_query("SELECT adm_fields_veiw.id AS afvid, adm_fields_veiw.nm AS afvnm, adm_fields_veiw.w AS afvw, adm_fields_veiw.edit AS afve, adm_fields_veiw.vtype AS afvvt, adm_fields.id_type AS fit, adm_fields.iskey as afik, adm_fields.nm, af1.nm AS keyy, af2.nm AS txt,adm_tables.nm AS stbl,`id_field`,adm_fields.id_tbl AS idtbl
    FROM adm_fields_veiw LEFT JOIN adm_fields ON adm_fields.id = adm_fields_veiw.id_field LEFT JOIN adm_sel ON adm_fields_veiw.id = adm_sel.id_fld
    LEFT JOIN adm_fields AS af1 ON af1.id = id_key LEFT JOIN adm_fields AS af2 ON af2.id = id_text LEFT JOIN adm_tables ON adm_tables.id = adm_sel.id_tbl
    WHERE adm_fields_veiw.id_view =".$_POST["tbid"]." order by sort");
    $big_id="<table id=\"tbl200".$_POST["tbid"]."\"><tr class=\"head-row\"><td class=\"add\"><input type=\"button\" onfocus=\"this.blur()\" value=\"\" onclick=\"AddRowR(this)\"/></td>";
    $colcnt=0;
    while($r_tbl=mysql_fetch_object($tbl))
    {
      $colcnt++;
      $flds[$r_tbl->nm]["index"]=($r_tbl->afik?1:0);
      $flds[$r_tbl->nm]["vtype"]=$r_tbl->afvvt;
      $flds[$r_tbl->nm]["keyy"]=($r_tbl->keyy?$r_tbl->keyy:0);
      $flds[$r_tbl->nm]["txt"]=($r_tbl->txt?$r_tbl->txt:0);
      $flds[$r_tbl->nm]["tp"]=$r_tbl->afvvt;
      $flds[$r_tbl->nm]["sel"]="";
      $flds[$r_tbl->nm]["stbl"]=$r_tbl->stbl;
      $flds[$r_tbl->nm]["id_field"]=$r_tbl->id_field;
      $flds[$r_tbl->nm]["idtbl"]=$r_tbl->idtbl;
      if($r_tbl->stbl)
      {
        $rsel=mysql_query("select ".$r_tbl->txt." as name,".$r_tbl->keyy." as id from ".$r_tbl->stbl." order by ".$r_tbl->txt);
        $numb=mysql_num_rows($rsel);
        $flds[$r_tbl->nm]["sel"]="<select onfocus=\"FocusElem(this)\" onchange=\"EditElem(this)\" name=\"f_".$flds[$r_tbl->nm]["idtbl"]."_".$flds[$r_tbl->nm]["id_field"]."\"><option value=\"0\">не указано</option>";
        if($numb)
          while ($r_sel=mysql_fetch_object($rsel))
            $flds[$r_tbl->nm]["sel"].="<option value=\"".$r_sel->id."\">".$r_sel->name."</option>";
        $flds[$r_tbl->nm]["sel"].="</select>";
      }
      $big_id.="<td style=\"width:".$r_tbl->afvw."px\">".$r_tbl->afvnm."</td>"; //".($r_tbl->tp==6?" colspan=\"2\"":"")."
    }
    $colcnt+=2;
  /*$file = $_SERVER["DOCUMENT_ROOT"]."/admin/log.txt";
  $fh = fopen($file, "a+");
  fwrite($fh,$sql);
  fclose($fh);*/
  $tbl=mysql_query("SELECT af.id AS afid, af.id_tbl AS afidt,af.nm as afn FROM adm_fields_veiw LEFT JOIN adm_fields ON adm_fields.id = adm_fields_veiw.id_field
    LEFT JOIN adm_fields AS af ON af.id_tbl = adm_fields.id_tbl WHERE adm_fields_veiw.id_view =".$_POST["tbid"]." AND af.iskey <> '' GROUP BY af.id, af.id_tbl");
  $j=0;
  while($in=mysql_fetch_object($tbl))
  {
    $ind[$j][0]=$in->afid;
    $ind[$j][1]=$in->afidt;
    $ind[$j][2]=$in->afn;
    $j++;
  }
  $big_id.="<td class=\"marker\">".($val?"<input type=\"hidden\" id=\"{$nsf}\" value=\"$val\"/>":"")."</td><td class=\"empty\"></td></tr>";
  $result = mysql_query($view_qw);
  $num=mysql_num_rows( $result );
  if ($num)
    while ($r_test=mysql_fetch_array($result))
    {
      $big_id.="<tr><td class=\"plus\">".($pl?"<input type=\"button\" onfocus=\"this.blur()\" onclick=\"OpenRowR(this);\" value=\"+\" />":"");
      $j=0;
      while($ind[$j][0])
      {
        $big_id.="<input type=\"hidden\" value=\"".$r_test[$ind[$j][2]]."\" name=\"i_".$ind[$j][1]."_".$ind[$j][0]."\" />";
        $j++;
      }
      $big_id.="</td>";
      foreach($flds as $K=>$V)
      {
        $big_id.="<td>";
        switch($flds[$K]["tp"])
        {
          case 1: case 2: case 3: case 4:
            if($flds[$K]["stbl"])
              $big_id.=str_replace("value=\"".$r_test[$K]."\"","value=\"".$r_test[$K]."\" selected=\"selected\"",$flds[$K]["sel"]);    // onchange=\"EditElem(this)\"
            else
              $big_id.="<input type=\"text\" value=\"".$r_test[$K]."\" ".($flds[$K]["index"]?"disabled=\"disabled\"":" onfocus=\"FocusElem(this)\" onkeypress=\"EditElem(this)\"")." name=\"f_".$flds[$K]["idtbl"]."_".$flds[$K]["id_field"]."\" />";
          break;
          case 5:
            $big_id.="<input type=\"button\" class=\"".($r_test[$K]?"full":"empt")."\" onclick=\"EditTextR(this)\" name=\"f_".$flds[$K]["idtbl"]."_".$flds[$K]["id_field"]."\" />";
          break;
          case 6:
            $dt=normalize_mysqldate($r_test[$K]);
            $big_id.="<input type=\"text\" value=\"$dt\" onfocus=\"FocusElem(this)\" onkeypress=\"EditElem(this)\" name=\"f_".$flds[$K]["idtbl"]."_".$flds[$K]["id_field"]."\" />";
          break;
        }
        $big_id=$big_id."</td>";
      }
      $big_id=$big_id."<td class=\"marker\"><input type=\"button\" value=\"\" class=\"delete-row\" onclick=\"DeleteDBRowR(this);\"/></td><td class=\"empty\"></td></tr>";
    }
    if($pcount>1)
     for($i=1;$i<=$pcount;$i++)
      $pagestr.=($_POST["pg"]==$i?"<span>{$i}</span>":"<a href=\"#\" onclick=\"return OpenMenu('{$tbName}',".($_POST["cond"]?$_POST["cond"]:"0").",$i)\">{$i}</a>");
    $big_id="<h1>{$tbl_long_nm}</h1>".$big_id.($pcount>1?"<tr><td colspan=\"{$colcnt}\" class=\"pages\">{$pagestr}</td></tr>":"")."</table>";
  }
  if($_POST["tran"]==2)
  {
    $tbid=str_ireplace('tbl200','',$_POST["tbid"]);
    $whe="";
    foreach($_POST as $K=>$V)
    {
      preg_match_all("#i_(\d*)_(\d*)?#i",$K,$ind,PREG_SET_ORDER);
      if($ind[0][2]) $tblind[$ind[0][1]][$ind[0][2]]=$V;
      preg_match_all("#f_(\d*)_(\d*)?#i",$K,$fld,PREG_SET_ORDER);
      if($fld[0][2]) $tblfld[$fld[0][1]][$fld[0][2]]=$V;
    }
    $rsel=mysql_query("SELECT adm_tables.nm as atnm,edit,id_tbl,adm_fields.nm as afvnm,istxt,id_field FROM `adm_fields_veiw` LEFT JOIN adm_fields ON adm_fields.id = `adm_fields_veiw`.id_field LEFT JOIN adm_tables ON adm_tables.id = id_tbl LEFT JOIN adm_types ON id_type = adm_types.id WHERE `id_view` =".$tbid." ORDER BY id_tbl");
    $idtable=0;
    while($rs=mysql_fetch_object($rsel))
    {
      if($idtable!=$rs->id_tbl)
      {
        if($idtable!=0)
        {
          $sqlupd=substr($sqlupd,0,strlen($sqlupd)-1);
          $sqlupd.=" where ";
          $rind=mysql_query("select adm_fields.id as afid,id_type,iskey,adm_fields.nm AS afnm,istxt from adm_fields LEFT JOIN adm_types ON id_type = adm_types.id where id_tbl=".$rs->id_tbl." and iskey!=''");
          while($ri=mysql_fetch_object($rind))
            $sqlupd.=$ri->afnm."=".($ri->istxt?"'":"").iconv('UTF-8', 'windows-1251',$tblind[$rs->id_tbl][$ri->afid]).($ri->istxt?"'":"")." and ";
          $sqlupd=substr($sqlupd,0,strlen($sqlupd)-4);
        }
        $sqlupd="update ".$rs->atnm." set ";
        $idtable=$rs->id_tbl;
      }
      if($rs->edit)
        $sqlupd.=$rs->afvnm."=".($rs->istxt?"'":"").iconv('UTF-8', 'windows-1251',$tblfld[$rs->id_tbl][$rs->id_field]).($rs->istxt?"'":"").",";
    }
    $sqlupd=substr($sqlupd,0,strlen($sqlupd)-1);
    $sqlupd.=" where ";
    $rind=mysql_query("select adm_fields.id as afid,id_type,iskey,adm_fields.nm AS afnm,istxt from adm_fields LEFT JOIN adm_types ON id_type = adm_types.id where id_tbl=".$idtable." and iskey!=''");
    while($ri=mysql_fetch_object($rind))
      $sqlupd.=$ri->afnm."=".($ri->istxt?"'":"").$tblind[$idtable][$ri->afid].($ri->istxt?"'":"")." and ";
    $sqlupd=substr($sqlupd,0,strlen($sqlupd)-4);
    $big_id=$sqlupd;
  }
  echo $big_id;
?>