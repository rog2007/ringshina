<?php
$db_host     = "127.0.0.1";
$db_user     = "root";
$db_password = "";
$db_name     = 'test_podbor';
$table_name     = 'podbor_shini_i_diski';

#отправлять на удаленный сервер разработчика базы данные по новым позициям, изменения вносимые в старые данные (исправление ошибок, дополнения) для изменения/дополнения последующих выпусков базы, 1 - отправлять, 0 - не отправлять
$send_data = 1;

#имя пользователя
$send_email= "anonymous";

$send_data_url ="http://basecontent.info/stats/podbor_shini_i_diski/q.php";


#количество пустых полей добавляемых при редактировании
$add_new_block = 6;  


#имя скрипта
$script_file = "edit_base.php";



$xajaxargs = @$_POST['xajaxargs'];
$xajax = @$_POST['xajax'] ;
$show = @$_POST['show'] ;
$model_id = @$_POST['modification'];
$add = @$_GET['add'];




function SQL($query) {
   global $db_host, $db_user, $db_password, $db_name, $dbh;
   if (! $dbh) {
      $dbh = mysql_connect($db_host, $db_user, $db_password);
      mysql_select_db($db_name);
   }
   mysql_query ("SET NAMES `cp1251`");   
   $sth = mysql_query($query); #mysql_query $db_name
   if (mysql_errno()>0) {

		echo(mysql_errno()." : ".mysql_error()."<BR>\r\n $query<br>");
		die("Error in sql");
		exit;

   }

   return $sth;
}


$vendors  = array ();

#получение списка вендоров
$result= SQL("SELECT DISTINCT vendor FROM `$db_name`.`$table_name` ORDER BY `$table_name`.`vendor` ASC;");
	if(mysql_num_rows($result))  { 
		while($row = mysql_fetch_array($result)) {
		$vendors[]  = $row['vendor'];
	}
}









function SendData ($model_id, $vendor, $car, $year, $modification, $pcd, $diametr, $gaika, $zavod_shini, $zamen_shini,$tuning_shini, $zavod_diskov, $zamen_diskov, $tuning_diski, $coments) {
global $send_data_url,$send_email;

$post_data = "model_id=$model_id&vendor=$vendor&car=$car&year=$year&modification=$modification".
"&pcd=$pcd&diametr=$diametr&gaika=$gaika&zavod_shini=$zavod_shini&zamen_shini=$zamen_shini".
"&tuning_shini=$tuning_shini&zavod_diskov=$zavod_diskov&zamen_diskov=$zamen_diskov".
"&tuning_diski=$tuning_diski&type_base=1&send_email=$send_email&coments=$coments";

$ch = curl_init ();

curl_setopt ( $ch , CURLOPT_URL, $send_data_url); 
curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , 1 );
curl_setopt ( $ch , CURLOPT_USERAGENT , "Script edit base v_0.1" );
curl_setopt ( $ch , CURLOPT_FOLLOWLOCATION , 1 );
curl_setopt ( $ch , CURLOPT_ENCODING , 1 );
curl_setopt ( $ch , CURLOPT_POSTFIELDS, $post_data);
curl_setopt ( $ch , CURLOPT_TIMEOUT, 7);

$tmp = @curl_exec ( $ch );
$cur_er_n = curl_errno($ch);
$cur_er     = curl_error($ch);
curl_close ( $ch ); 

if ($cur_er_n != 0 ) return "ERROR, не полностью получены данные с сервера - $cur_er ($cur_er_n)";

return $tmp;
}



















function show_form($vendor,$car,$year,$modification,$zavod_shini,$zamen_shini,$tuning_shini ,$zavod_diskov,$zamen_diskov,$tuning_diski, $pcd,$diametr,$gaika,$coments) {
global $add_new_block;



	echo "<TABLE BORDER=0>\r\n";

	echo "<TR><TD>производитель: </TD><TD><input type=\"text\" size=\"38\" name=\"vendor\" value=\"$vendor\" /> </TD></TR>";
	echo "<TR><TD>название: </TD><TD><input type=\"text\" size=\"38\" name=\"car\" value=\"$car\" />  </TD></TR>";
	echo "<TR><TD>год: </TD><TD><input type=\"text\" size=\"38\" name=\"year\" value=\"$year\" />  </TD><TD>*</TD></TR>";
	echo "<TR><TD>модификация: </TD><TD><input type=\"text\" size=\"38\" name=\"modification\" value=\"$modification\" /> </TD></TR>";
	echo "<TR><TD>коментарий: </TD><TD><input type=\"text\" size=\"38\" name=\"coments\" value=\"$coments\" /> </TD></TR></TABLE>";


	echo "PCD: <input type=\"text\" size=\"8\" name=\"pcd\" value=\"$pcd\" /> ";
	echo "диаметр: <input type=\"text\" size=\"12\" name=\"diametr\" value=\"$diametr\" /> ";
	echo "болт/гайка: <input type=\"text\" size=\"12\" name=\"gaika\" value=\"$gaika\" /> <br><br>\r\n";


	echo "<TABLE BORDER=0>\r\n";
 	echo "<TR><TD><b><center>Параметры шин</center></b></TD></TR>\r\n";

	echo "<TR><TD><center>заводская комплектация</center></TD></TR>\r\n";
	$zavod_shini_ = explode('|',$zavod_shini);
	echo "<TR><TD>передняя ось: </TD><TD> задняя ось:</TD></TR>\r\n";

	for ($j=0; $j<=count($zavod_shini_); $j++) {
		if ($zavod_shini_[$j] != "")  {
			$pered_os = "";
			$zad_os = "";
			@list($pered_os,$zad_os) = explode ('#' ,$zavod_shini_[$j]);
			echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"shini_zavod_koml_pered_os_$j\" value=\"$pered_os\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"shini_zavod_koml_zad_os_$j\" value=\"$zad_os\" />" . "" .  "</TD></TR>\r\n";
		}
	}	
	$i = $j;
	for ($i=$j; $i<$add_new_block+$j; $i++) 
		echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"shini_zavod_koml_pered_os_$i\" value=\"\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"shini_zavod_koml_zad_os_$i\" value=\"\" />" . "" .  "</TD></TR>\r\n";

	

	echo "<TR><TD><center>варианты замены</center></TD></TR>\r\n";
	$zamen_shini_ = explode('|',$zamen_shini);
	echo "<TR><TD>передняя ось: </TD><TD> задняя ось:</TD></TR>\r\n";

	for ($j=0; $j<=count($zamen_shini_); $j++) {
		if ($zamen_shini_[$j] != "")  {
			$pered_os = "";
			$zad_os = "";
			@list($pered_os,$zad_os) = explode ('#' ,$zamen_shini_[$j]);
			echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"zamen_shini_pered_os_$j\" value=\"$pered_os\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"zamen_shini_zad_os_$j\" value=\"$zad_os\" />" . "" .  "</TD></TR>\r\n";
		}
	}
	$i = $j;
	for ($i=$j; $i<$add_new_block+$j; $i++) 
		echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"zamen_shini_pered_os_$i\" value=\"\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"zamen_shini_zad_os_$i\" value=\"\" />" . "" .  "</TD></TR>\r\n";

	




	echo "<TR><TD><center>тюнинг</center></TD></TR>\r\n";
	$tuning_shini_ = explode('|',$tuning_shini);
	echo "<TR><TD>передняя ось: </TD><TD> задняя ось:</TD></TR>\r\n";
	for ($j=0; $j<=count($tuning_shini_); $j++) {
		if ($tuning_shini_[$j] != "")  {
			$pered_os = "";
			$zad_os = "";
			@list($pered_os,$zad_os) = explode ('#' ,$tuning_shini_[$j]);
			echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"tuning_shini_pered_os_$j\" value=\"$pered_os\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"tuning_shini_zad_os_$j\" value=\"$zad_os\" />" . "" .  "</TD></TR>\r\n";
		}
	}
	$i = $j;
	for ($i=$j; $i<$add_new_block+$j; $i++) 
		echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"tuning_shini_pered_os_$i\" value=\"\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"tuning_shini_zad_os_$i\" value=\"\" />" . "" .  "</TD></TR>\r\n";
	



 	echo "<TR><TD><br></TD></TR><TR><TD><b><center>Параметры дисков</center></b></TD></TR>\r\n";

	echo "<TR><TD><center>заводская комплектация</center></TD></TR>\r\n";
	$zavod_diskov_ = explode('|',$zavod_diskov);
	echo "<TR><TD>передняя ось: </TD><TD> задняя ось:</TD></TR>\r\n";


	for ($j=0; $j<=count($zavod_diskov_); $j++) {
		if ($zavod_diskov_[$j] != "")  {
			$pered_os = "";
			$zad_os = "";
			@list($pered_os,$zad_os) = explode ('#' ,$zavod_diskov_[$j]);
			echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"zavod_diskov_pered_os_$j\" value=\"$pered_os\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"zavod_diskov_zad_os_$j\" value=\"$zad_os\" />" . "" .  "</TD></TR>\r\n";
		}
	}	
	$i = $j;
	for ($i=$j; $i<$add_new_block+$j; $i++) 
		echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"zavod_diskov_pered_os_$i\" value=\"\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"zavod_diskov_zad_os_$i\" value=\"\" />" . "" .  "</TD></TR>\r\n";
	


	echo "<TR><TD><center>варианты замены</center></TD></TR>\r\n";
	$zamen_diskov_ = explode('|',$zamen_diskov);
	echo "<TR><TD>передняя ось: </TD><TD> задняя ось:</TD></TR>\r\n";

	for ($j=0; $j<=count($zamen_diskov_); $j++) {
		if ($zamen_diskov_[$j] != "")  {
			$pered_os = "";
			$zad_os = "";
			@list($pered_os,$zad_os) = explode ('#' ,$zamen_diskov_[$j]);
			echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"zamen_diskov_pered_os_$j\" value=\"$pered_os\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"zamen_diskov_zad_os_$j\" value=\"$zad_os\" />" . "" .  "</TD></TR>\r\n";
		}
	}	
	$i = $j;
	for ($i=$j; $i<$add_new_block+$j; $i++) 
		echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"zamen_diskov_pered_os_$i\" value=\"\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"zamen_diskov_zad_os_$i\" value=\"\" />" . "" .  "</TD></TR>\r\n";

		

	echo "<TR><TD><center>тюнинг</center></TD></TR>\r\n";
	$tuning_diski_ = explode('|',$tuning_diski);
	echo "<TR><TD>передняя ось: </TD><TD> задняя ось:</TD></TR>\r\n";

	for ($j=0; $j<=count($tuning_diski_); $j++) {
		if ($tuning_diski_[$j] != "")  {
			$pered_os = "";
			$zad_os = "";
			@list($pered_os,$zad_os) = explode ('#' ,$tuning_diski_[$j]);
			echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"tuning_diski_pered_os_$j\" value=\"$pered_os\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"tuning_diski_zad_os_$j\" value=\"$zad_os\" />" . "" .  "</TD></TR>\r\n";

		}
	}

	$i = $j;
	for ($i=$j; $i<$add_new_block+$j; $i++) 
		echo "<TR><TD>" . "<input type=\"text\" size=\"12\" name=\"tuning_diski_pered_os_$i\" value=\"\" />" . "</TD><TD>" . "<input type=\"text\" size=\"12\" name=\"tuning_diski_zad_os_$i\" value=\"\" />" . "" .  "</TD></TR>\r\n";

		




}

















if ($add) {

$vendor = @$_GET['vendor'];
$car = @$_GET['car'];
$year = @$_GET['year'];



$zavod_shini = "";
$zamen_shini = "";
$tuning_shini = "";

$zavod_diskov = "";
$zamen_diskov = "";
$tuning_diski = "";

$pcd = "";
$diametr = "";
$gaika = "";
$coments = "";


echo "<form action='$script_file' method=\"POST\">";
echo "<input type=hidden name=show value='ADD'>";

echo "<h3>Добавление новой записи</h3>";

show_form($vendor,$car,$year,$modification,$zavod_shini,$zamen_shini,$tuning_shini ,$zavod_diskov,$zamen_diskov,$tuning_diski, $pcd,$diametr,$gaika,$coments);


echo "</TABLE> <br/><input type=\"submit\" value=\"Добавить\"></form>\r\n";
echo "<br>* допускается использование диапазона или списка, например: 2005-2010 или 2007,2008,2009";
exit;

}




















function get_some_data ($id) {
global $db_name, $table_name;
	$result= SQL("SELECT * FROM `$db_name`.`$table_name` WHERE id = $id;");
	if(mysql_num_rows($result))  { 
		$row = mysql_fetch_array($result);
		$vendor  = $row['vendor'];
		$car  = $row['car'];
		$year  = $row['year'];
		$modification = $row['modification'];
		return array($vendor, $car, $modification, $year);
	}
}




if ( ($show == "EDIT") OR ($show == "ADD")) {

$zavod_shini = "";
$zamen_shini = "";
$tuning_shini = "";
$zavod_diskov = "";
$zamen_diskov = "";
$tuning_diski = "";
$pcd = "";
$diametr = "";
$gaika = "";
$coments = "";

$model_id = @$_POST['model_id'];

$vendor = @$_POST['vendor'];
$car = @$_POST['car'];
$year = trim(@$_POST['year']);
$modification = @$_POST['modification'];

$pcd = @$_POST['pcd'];
$diametr = @$_POST['diametr'];
$gaika = @$_POST['gaika'];
$coments = @$_POST['coments'];
$ad = @$_POST['ad'];
$ed = @$_POST['ed'];
$del = @$_POST['del'];

if (strpos($year,"-") > 0)   { 
$t_year = "";
list($year_start,$year_end) = explode ('-', $year);
$year_start = trim($year_start);
$year_end = trim($year_end);
for ($j = $year_start; $j <= $year_end; $j++) {
if ($t_year == "") $t_year = $j; else $t_year .= "," . $j;
}
$year = $t_year;
}



if ($del) {
$data_ = get_some_data ($model_id);
$vendor_ = $data_[0];
$car_ = $data_[1];
$modification_ = $data_[2];
$year__ = $data_[3];
sql("DELETE FROM `$db_name`.`$table_name`WHERE `$table_name`.`id` =  $model_id;");
echo "<b>запись успешно удалена ($vendor_ - $car_ - $year__ - $modification_)</b><br>\r\n";
return "";
}


if ($ad) {
$model_id = "new";
$show = "ADD";
}


for ($j=0; $j< 20; $j++) {
if (@$_POST["shini_zavod_koml_pered_os_$j"] != "")
$zavod_shini .= "|" . $_POST["shini_zavod_koml_pered_os_$j"] . "#" . @$_POST["shini_zavod_koml_zad_os_$j"];
if (@$_POST["zamen_shini_pered_os_$j"] != "")
$zamen_shini .= "|" . $_POST["zamen_shini_pered_os_$j"] . "#" . @$_POST["zamen_shini_zad_os_$j"];
if (@$_POST["tuning_shini_pered_os_$j"] != "")
$tuning_shini .= "|" . $_POST["tuning_shini_pered_os_$j"] . "#" . @$_POST["tuning_shini_zad_os_$j"];
if (@$_POST["zavod_diskov_pered_os_$j"] != "") 
$zavod_diskov .= "|" . $_POST["zavod_diskov_pered_os_$j"] . "#" . @$_POST["zavod_diskov_zad_os_$j"];
if (@$_POST["zamen_diskov_pered_os_$j"] != "") 
$zamen_diskov .= "|" . $_POST["zamen_diskov_pered_os_$j"] . "#" . @$_POST["zamen_diskov_zad_os_$j"];
if (@$_POST["tuning_diski_pered_os_$j"] != "") 
$tuning_diski .=  "|" . $_POST["tuning_diski_pered_os_$j"]  . "#" . $_POST["tuning_diski_zad_os_$j"] ;
}

if (substr($zavod_shini, 0, 1) == "|") $zavod_shini = substr($zavod_shini, 1);
if (substr($zamen_shini, 0, 1) == "|") $zamen_shini = substr($zamen_shini, 1);
if (substr($tuning_shini, 0, 1) == "|") $tuning_shini = substr($tuning_shini, 1);
if (substr($zavod_diskov, 0, 1) == "|") $zavod_diskov = substr($zavod_diskov, 1);
if (substr($zamen_diskov, 0, 1) == "|") $zamen_diskov = substr($zamen_diskov, 1);
if (substr($tuning_diski, 0, 1) == "|") $tuning_diski = substr($tuning_diski, 1);

$zavod_shini = str_replace("#|","|",$zavod_shini);
$zamen_shini = str_replace("#|","|",$zamen_shini);
$tuning_shini = str_replace("#|","|",$tuning_shini);
$zavod_diskov = str_replace("#|","|",$zavod_diskov);
$zamen_diskov = str_replace("#|","|",$zamen_diskov);
$tuning_diski = str_replace("#|","|",$tuning_diski);

if (substr($zavod_shini , strlen($zavod_shini) -1, 1) == "#") $zavod_shini = substr($zavod_shini , 0, strlen($zavod_shini) -1);
if (substr($zamen_shini , strlen($zamen_shini) -1, 1) == "#") $zamen_shini = substr($zamen_shini , 0, strlen($zamen_shini) -1);
if (substr($tuning_shini , strlen($tuning_shini) -1, 1) == "#") $tuning_shini = substr($tuning_shini , 0, strlen($tuning_shini) -1);
if (substr($zavod_diskov , strlen($zavod_diskov) -1, 1) == "#") $zavod_diskov = substr($zavod_diskov , 0, strlen($zavod_diskov) -1);
if (substr($zamen_diskov , strlen($zamen_diskov) -1, 1) == "#") $zamen_diskov = substr($zamen_diskov , 0, strlen($zamen_diskov) -1);
if (substr($tuning_diski , strlen($tuning_diski) -1, 1) == "#") $tuning_diski = substr($tuning_diski , 0, strlen($tuning_diski) -1);




if ($show == "EDIT") {

if (strpos($year,",") > 0)   { 

$data_ = get_some_data ($model_id);

$vendor_ = $data_[0];
$car_ = $data_[1];
$modification_ = $data_[2];

$year_ = explode(',', $year);
for ($j=0; $j<count($year_); $j++) {
if ($year_[$j] != "") {
echo "вносим изменения для года " . trim($year_[$j]) . " ($vendor_ - $car_ - $modification_)<br>\r\n";
sql("UPDATE `$db_name`.`$table_name` SET 
`vendor` = '".addslashes($vendor) ."',
`car` = '".addslashes($car) ."',
`year` = '".addslashes(trim($year_[$j])) ."',
`modification` = '".addslashes($modification) ."',

`pcd` = '".addslashes($pcd) ."',
`diametr` = '".addslashes($diametr) ."',
`gaika` = '".addslashes($gaika) ."',
`zavod_shini` = '".addslashes($zavod_shini) ."',
`zamen_shini` = '".addslashes($zamen_shini) ."',
`tuning_shini` = '".addslashes($tuning_shini) ."',
`zavod_diskov` = '".addslashes($zavod_diskov) ."',
`zamen_diskov` = '".addslashes($zamen_diskov) ."',
`tuning_diski` = '".addslashes($tuning_diski) ."',
`coments` = '".addslashes($coments) ."'
 WHERE `$table_name`.`vendor` =  '".addslashes($vendor_) ."' AND `$table_name`.`car` =  '".addslashes($car_) ."' 
 AND `$table_name`.`year` = '".addslashes(trim($year_[$j])) ."' AND `$table_name`.`modification` =  '".addslashes($modification_) ."' ;");
echo "<b>запись успешно обновлена</b><br>\r\n";
if ($send_data) echo SendData ($model_id, $vendor, $car, trim($year_[$j]), $modification, $pcd, $diametr, $gaika, $zavod_shini, $zamen_shini,$tuning_shini, $zavod_diskov, $zamen_diskov, $tuning_diski,$coments) . "<br>\r\n";
}
}

} else {

sql("UPDATE `$db_name`.`$table_name` SET 
`vendor` = '".addslashes($vendor) ."',
`car` = '".addslashes($car) ."',
`year` = '".addslashes(trim($year)) ."',
`modification` = '".addslashes($modification) ."',
`pcd` = '".addslashes($pcd) ."',
`diametr` = '".addslashes($diametr) ."',
`gaika` = '".addslashes($gaika) ."',
`zavod_shini` = '".addslashes($zavod_shini) ."',
`zamen_shini` = '".addslashes($zamen_shini) ."',
`tuning_shini` = '".addslashes($tuning_shini) ."',
`zavod_diskov` = '".addslashes($zavod_diskov) ."',
`zamen_diskov` = '".addslashes($zamen_diskov) ."',
`tuning_diski` = '".addslashes($tuning_diski) ."',
`coments` = '".addslashes($coments) ."'
 WHERE `$table_name`.`id` =  $model_id;");
echo "<b>запись успешно обновлена  ($vendor - $car - $year - $modification)</b><br>\r\n";
if ($send_data) echo SendData ($model_id, $vendor, $car, trim($year), $modification, $pcd, $diametr, $gaika, $zavod_shini, $zamen_shini,$tuning_shini, $zavod_diskov, $zamen_diskov, $tuning_diski,$coments) . "<br>\r\n";



}


      

}



if ($show == "ADD") {

$year_ = explode(',' ,$year);
for ($j=0; $j<count($year_); $j++) {
if ($year_[$j] != "") {

sql("INSERT INTO `$db_name`.`$table_name` ( `vendor`, `car`, `year`, `modification`, `pcd`, `diametr`, `gaika`, `zavod_shini`, `zamen_shini`, `tuning_shini`, `zavod_diskov`, `zamen_diskov`, `tuning_diski`,`coments`) VALUES (
 '".addslashes(trim($vendor)) ."',
 '".addslashes(trim($car)) ."',
 '".addslashes(trim($year_[$j])) ."',
 '".addslashes(trim($modification)) ."',
 '".addslashes(trim($pcd)) ."',
 '".addslashes(trim($diametr)) ."',
 '".addslashes(trim($gaika)) ."',
 '".addslashes(trim($zavod_shini)) ."',
 '".addslashes(trim($zamen_shini)) ."',
 '".addslashes(trim($tuning_shini)) ."',
 '".addslashes(trim($zavod_diskov)) ."',
 '".addslashes(trim($zamen_diskov)) ."',
 '".addslashes(trim($tuning_diski)) ."',
 '".addslashes(trim($coments)) ."');");
echo "<b>добавлена новая запись</b><br>\r\n";
if ($send_data) echo SendData ("new", $vendor, $car, $year_[$j], $modification, $pcd, $diametr, $gaika, $zavod_shini, $zamen_shini,$tuning_shini, $zavod_diskov, $zamen_diskov, $tuning_diski, $coments). "<br>\r\n";
}
}



}






#exit;
}





if ($show == "OK") {

	if ($model_id == "") die("<b><center>Ошибка, не полностью выбраны данные для подбора,<br> вернитесь назад и укажите все данные полностью (производитель, марка, год выпуска, модификация)");


	$result= SQL("SELECT * FROM `$db_name`.`$table_name` WHERE id = $model_id;");

	echo "<form action='$script_file' method=\"POST\">";
	echo "<input type=hidden name=show value='EDIT'>";
	echo "<input type=hidden name=model_id value=\"$model_id\">";

 	echo "<h3>Режим редактирования базы</h3>";

	if(mysql_num_rows($result))  { 
		$row = mysql_fetch_array($result);

		$vendor  = $row['vendor'];
		$car  = $row['car'];
		$year  = $row['year'];
		$modification = $row['modification'];

		$zavod_shini = $row['zavod_shini'];
		$zamen_shini = $row['zamen_shini'];
		$tuning_shini = $row['tuning_shini'];
 
		$pcd = $row['pcd'];
		$diametr = $row['diametr'];
		$gaika = $row['gaika'];

		$zavod_diskov = $row['zavod_diskov'];
		$zamen_diskov = $row['zamen_diskov'];
		$tuning_diski = $row['tuning_diski'];
		$coments = @$row['coments'];


 		#echo "автомобиль <b>$vendor $car $year $modification</b><br>\r\n";


		echo "<a href=\"$script_file?add=1&vendor=$vendor\">добавить новую запись для <b>$vendor</b></a><br>";
		echo "<a href=\"$script_file?add=1&vendor=$vendor&car=$car\">добавить новую запись для <b>$vendor $car</b></a><br>";
		echo "<a href=\"$script_file?add=1&vendor=$vendor&car=$car&year=$year\">добавить новую запись для <b>$vendor $car $year</b></a><br><br>";


		show_form($vendor,$car,$year,$modification,$zavod_shini,$zamen_shini,$tuning_shini ,$zavod_diskov,$zamen_diskov,$tuning_diski, $pcd,$diametr,$gaika,$coments);


	}

	echo "</TABLE> <br/><input type=\"submit\" name = \"ed\" value=\"Изменить старую запись\">  <input type=\"submit\" name = \"ad\" value=\"добавить новую запись с этими данными\">";



  	if ($model_id > 0) echo "  <input type=\"submit\" name = \"del\" value=\"удалить текущую запись\"><br>";

	echo "</form>\r\n";

echo "<br>* при редактировании допускается использование диапазона или списка, например: 2005-2010 или 2007,2008,2009 ";

echo "только при одинаковом значении полей (до редактирования) производитель, название, модификация (у уже существующих записей)";


	exit;



}


if ($xajax == "getmodels") {
	header("Content-type: text/xml; charset=utf-8"); 
	
	$vendor_id = $xajaxargs[0];



	$result= SQL("SELECT * FROM `$db_name`.`$table_name` WHERE vendor = '".$vendors[$vendor_id]."' ORDER BY `$table_name`.`car` ASC;") ;



	$i = 0;
	$out_ = "";
	$last_car = "";
	if(mysql_num_rows($result))  { 
		while($row = mysql_fetch_array($result)) {
			if ($last_car != $row['car']) {
				$i++;
				$out_ = $out_ . "<cmd n=\"as\" t=\"models\" p=\"options[$i].text\"><![CDATA[".$row['car']."]]></cmd>";
				$out_ = $out_ . "<cmd n=\"as\" t=\"models\" p=\"options[$i].value\"><![CDATA[".$row['id']."]]></cmd>";
				#$i++;
				$last_car = $row['car'];
			}
		}
	}

	$i++;
	$out_null = "<cmd n=\"as\" t=\"models\" p=\"options[0].text\"><![CDATA[Выберите марку]]></cmd>";
	$out_null = $out_null . "<cmd n=\"as\" t=\"models\" p=\"options[0].value\"><![CDATA[0]]></cmd>";


	$out__ =  "<cmd n=\"as\" t=\"modification\" p=\"options.length\"><![CDATA[0]]></cmd><cmd n=\"as\" t=\"modification\" p=\"options.length\"><![CDATA[0]]></cmd>";
	$out__ =  $out__ . "<cmd n=\"as\" t=\"year\" p=\"options.length\"><![CDATA[0]]></cmd><cmd n=\"as\" t=\"year\" p=\"options.length\"><![CDATA[0]]></cmd>";


	$out = "<?phpxml version=\"1.0\" encoding=\"utf-8\" ?><xjx><cmd n=\"as\" t=\"models\" p=\"options.length\"><![CDATA[0]]></cmd><cmd n=\"as\" t=\"models\" p=\"options.length\"><![CDATA[".$i."]]></cmd>";
	echo iconv("windows-1251","utf-8",$out . $out__ . $out_null . $out_ . "</xjx>");

	
}




if ($xajax == "getyear") {

	header("Content-type: text/xml; charset=utf-8"); 
	$year_id = $xajaxargs[0];

	if ($year_id == "") die("year_id is null in getyear");

	$result= SQL("SELECT * FROM `$db_name`.`$table_name` WHERE id = '$year_id'") ;
	if(mysql_num_rows($result))  { 
		$row = mysql_fetch_array($result);
		$_vendor = $row['vendor'];
		$_car = $row['car'];
		#$_year = $row['year'];
	}



	$result= SQL("SELECT * FROM `$db_name`.`$table_name` WHERE vendor = '$_vendor' AND car = '$_car' ORDER BY `$table_name`.`year` ASC;") ;
	$i = 0;
	$out_ = "";
	$last_year = "";
	if(mysql_num_rows($result))  { 
		while($row = mysql_fetch_array($result)) {
			if ($last_year != $row['year']) {
				$i++;
				$out_ = $out_ . "<cmd n=\"as\" t=\"year\" p=\"options[$i].text\"><![CDATA[".$row['year']."]]></cmd>";
				$out_ = $out_ . "<cmd n=\"as\" t=\"year\" p=\"options[$i].value\"><![CDATA[".$row['id']."]]></cmd>";
				#$i++;
				$last_year = $row['year'];
			}
		}
	}
	
	$i++;
	$out_null =  "<cmd n=\"as\" t=\"year\" p=\"options[0].text\"><![CDATA[Выберите год выпуска]]></cmd>";
	$out_null = $out_null . "<cmd n=\"as\" t=\"year\" p=\"options[0].value\"><![CDATA[0]]></cmd>";


	$out__ =  "<cmd n=\"as\" t=\"modification\" p=\"options.length\"><![CDATA[0]]></cmd><cmd n=\"as\" t=\"modification\" p=\"options.length\"><![CDATA[0]]></cmd>";

	$out = "<?phpxml version=\"1.0\" encoding=\"utf-8\" ?><xjx><cmd n=\"as\" t=\"year\" p=\"options.length\"><![CDATA[0]]></cmd><cmd n=\"as\" t=\"year\" p=\"options.length\"><![CDATA[".$i."]]></cmd>";
	echo iconv("windows-1251","utf-8",$out . $out__ . $out_null . $out_ . "</xjx>");      
                                                                                                                                                                                                                                                          
}









if ($xajax == "getmodification") {

	header("Content-type: text/xml; charset=utf-8"); 

	$modification_id = $xajaxargs[0];
	$year_id = $xajaxargs[0];
	$out_ = "";

	if ($modification_id == "") die("modification_id is null in getmodification");


	$result= SQL("SELECT * FROM `$db_name`.`$table_name` WHERE id = '$modification_id'") ;
	if(mysql_num_rows($result))  { 
		$row = mysql_fetch_array($result);
		$_vendor = $row['vendor'];
		$_car = $row['car'];
		$_year = $row['year'];
	}

	$result2= SQL("SELECT * FROM `$db_name`.`$table_name` WHERE vendor = '$_vendor' AND car = '$_car' AND year = '$_year'");


		$last_name = "";
		$i = 0;
		if(mysql_num_rows($result2))  { 
			while($row2 = mysql_fetch_array($result2)) {
				
				if ($last_name != $row2['modification']) {
					$last_name = $row2['modification'];
	
					$out_ = $out_ . "<cmd n=\"as\" t=\"modification\" p=\"options[$i].text\"><![CDATA[".$row2['modification']."]]></cmd>";
					$out_ = $out_ . "<cmd n=\"as\" t=\"modification\" p=\"options[$i].value\"><![CDATA[".$row2['id']."]]></cmd>";
					$i++;
		
				}

			}
		}

			


	$out =  "<?phpxml version=\"1.0\" encoding=\"utf-8\" ?><xjx><cmd n=\"as\" t=\"modification\" p=\"options.length\"><![CDATA[0]]></cmd><cmd n=\"as\" t=\"modification\" p=\"options.length\"><![CDATA[".$i."]]></cmd>";
	echo iconv("windows-1251","utf-8",$out . $out_ . "</xjx>");
	
}

if (($show == "OK") OR ($xajax != "")) exit;

?>


<h3>Режим редактирования базы</h3>

<script type="text/javascript">

var xajaxRequestUri="<?php echo $script_file?>";
var xajaxDebug=false;
var xajaxStatusMessages=false;
var xajaxWaitCursor=true;
var xajaxDefinedGet=0;
var xajaxDefinedPost=1;
var xajaxLoaded=false;
function xajax_getmodels(){return xajax.call("getmodels", arguments, 1);}
function xajax_getmodification(){return xajax.call("getmodification", arguments, 1);}
function xajax_getyear(){return xajax.call("getyear", arguments, 1);}

function xajax_list(){return xajax.call("list", arguments, 1);}


</script>



<script type="text/javascript" src="xajax.js"></script>


<script type="text/javascript">
window.setTimeout(function () { if (!xajaxLoaded) { alert('Error: the xajax Javascript file could not be included. Perhaps the URL is incorrect?\nURL: http://192.168.1.2/podbor_shini_diski/xajax.js'); } }, 6000);
</script>


<form action='<?php echo $script_file?>' method="POST">
<input type=hidden name=show value='OK'>

Производитель:<br>
<select name=auto id=auto size=1 onchange="xajax_getmodels(this.value)" style="width:200px">
<option value="-1">Выберите производителя</option>

<?php
	for ($j=0; $j<=count($vendors); $j++) {
		if ($vendors[$j] != "") {
			if ($vendor != $vendors[$j]) { 
				echo "<option value=\"$j\">".$vendors[$j]."</option>\r\n"; 
			} else {
				echo "<option value=\"$j\" selected=\"selected\">".$vendors[$j]."</option>\r\n";
			}
		}
	}
?>

</select><br/>

Марка:<br>
<select name=models id=models size=1  onchange="xajax_getyear(this.value)"  style="width:200px">
</select><br/>   

Год выпуска:<br>
<select name=year id=year  size=1 onchange="xajax_getmodification(this.value)"  style="width:200px">
</select><br/>

Модификация:<br>
<select name=modification id=modification  size=1  style="width:200px">
</select>



<br/>
<br/><input type="submit" value="Выбрать *">

<br/>* для подбора необходимо выбрать <b>все</b> параметры

</form>		


