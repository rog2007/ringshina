<?php
header("Content-type: text/xml");


$proxy = new SoapClient(rtrim($_REQUEST['w']));
$sessionId = $proxy->login(rtrim($_REQUEST['l']), rtrim($_REQUEST['p']));
$field = array(array('status'=>array( 'pending', 'processing' )));


$arr = $proxy->call($sessionId, 'sales_order.list', $field);

//the following line sets the the status.  valid statuses are:
//pending, processing, holded, complete, closed, canceled, paid, incomplete

echo '<MagentoOrders status="pending">';

$ordcount = 0;

foreach($arr as $key => $value){
$ordcount += 1;

if ($ordcount < 50) {
$arr2 = $proxy->call($sessionId, 'sales_order.info', $value["increment_id"]);

//*************************
echo "<MagentoOrder>";
$arr2['created_at'] = date('Y-m-d H:i:s', strtotime($arr2['created_at']) - 21600);
writeelements($arr2);
echo "<shipping_address>";
$arr2["shipping_address"]["region"] = getStateAbbreviation($arr2["shipping_address"]["region"]);
writeelements($arr2["shipping_address"]);
echo "</shipping_address>\n";
echo "<billing_address>";
$arr2["billing_address"]["region"] = getStateAbbreviation($arr2["billing_address"]["region"]);
writeelements($arr2["billing_address"]);
echo "</billing_address>\n";
echo "<payment>";
writeelements($arr2["payment"]);
echo "<auth_net_payment_profile_id>";
echo $arr2["payment"]["additional_information"]["paymentprofileid"];
echo "</auth_net_payment_profile_id>";
echo "</payment>\n";
echo "<items>";


//modify the parent/child relationship
$parent = "";
$childsku = "";
$childname = "";
$count = count($arr2["items"]);
for ($i = $count-1; $i >= 0; $i--) {
    if ($parent != "" && $parent == $arr2["items"][$i]["item_id"]){
        $arr2["items"][$i]["parent_sku"] = $arr2["items"][$i]["sku"];
        $arr2["items"][$i]["sku"] = $childsku;
        $arr2["items"][$i]["name"] = $childname;
    }
    $parent = $arr2["items"][$i]["parent_item_id"];
    $childsku = $arr2["items"][$i]["sku"];
    $childname = $arr2["items"][$i]["name"];
    if ($parent != "") unset($arr2["items"][$i]); //remove the child item
}

$samplecount=0;
$count = count($arr2["items"]);
for ($i = 0; $i < $count; $i++){

        if (substr($arr2["items"][$i]["sku"],0,13) == "sample-bundle"){
                $arr2["items"][$i]["kit_flag"] = "V";
                $options = $arr2["items"][$i]["product_options"];
                $pos1 = strpos($options,'"options"',1);
                if ($pos1 > 0) {
                        $pos2 = strpos($options,"{",$pos1);
                        if ($pos2 > 0) {
                                $posend = strpos($options,"}",$pos2+1);
                                $pos3 = strpos($options,'"',$pos2);
                                $pos4 = strpos($options,'"',$pos3+1);
                                while ($pos3 > 0 && $pos4 > 0 and $pos3 < $posend){
                                        $samplecount = $samplecount+1;
                                        $samplesku = substr($options,$pos3+1,$pos4-$pos3-1);
//echo "<samplesku>";
//echo $samplesku;
//echo "</samplesku>";
                                        $arr2["items"][$i]["samples"][$samplecount]["sku"] = $samplesku;
                                        $arr2["items"][$i]["samples"][$samplecount]["qty_ordered"] = 1;
                                        $arr2["items"][$i]["samples"][$samplecount]["kit_flag"] = "K";
                                        $pos3 = strpos($options,'"',$pos4+1);
                                        $pos4 = strpos($options,'"',$pos3+1);
                                }
                        }

                }
        }
        $options = $arr2["items"][$i]["product_options"];
        $pos1 = strpos($options,'"Cut"',0);
        if ($pos1 > 0) {
                $pos2 = strpos($options,'"',$pos1+20);
                if ($pos2 > 0) {
                        $pos3 = strpos($options,'"',$pos2+1);
                        if ($pos3 > 0){
                                $smessage = substr($options,$pos2+1,$pos3-$pos2-1);
                                $cutqty =  number_format( (float) $arr2["items"][$i]["qty_ordered"],0);
                                $cutqty = ltrim($cutqty);
                                $arr2["items"][$i]["line_message"] = "Cut ".$cutqty." Tiles ".$smessage;
                                $cutid = "";
                                if ($smessage == "1/2 Against The Grain") $cutid = "41-1348-01";
                                if ($smessage == "1/2 With The Grain") $cutid = "41-1337-01";
                                if ($smessage == "1/3 Against The Grain") $cutid = "41-1349-01";
                                if ($smessage == "1/3 With The Grain") $cutid = "41-1338-01";
                                if ($smessage == "1/4 Against The Grain") $cutid = "41-1350-01";
                                if ($smessage == "1/4 With The Grain") $cutid = "41-1339-01";
                                $arr2["items"][$i]["cuts"][1]["sku"] = $cutid;
                                $arr2["items"][$i]["cuts"][1]["qty_ordered"] = $cutqty;

                        }
                }
        }
        $arr2["items"][$i]["product_options"] = "";

}



foreach($arr2["items"] as $key2 => $value2){

echo "<item>";
writeelements($value2);
echo "</item>\n";

foreach($value2["samples"] as $key3 => $value3){
echo "<item>";
writeelements($value3);
echo "</item>\n";
}

//foreach($value2["cuts"] as $key3 => $value3){
//echo "<item>";
//writeelements($value3);
//echo "</item>\n";
//}

}

echo "</items>";
echo "</MagentoOrder>";

//*************************
//echo $value["increment_id"];
//writeelements($value);

}  //if ordcount
}  //foreach

echo '</MagentoOrders>';


//$xml = new XmlWriter();
//$xml->openMemory();
//$xml->startDocument('1.0', 'UTF-8');
//$xml->startElement('MagentoOrder');



function write($data, $indent)
{
    foreach($data as $key => $value)
	{
if (is_numeric($key)) $key = 'item';

echo str_repeat('&nbsp;',$indent*2),$key,'=',$value,'<br>';

        if(is_array($value)){
            write($value, $indent+1);
		continue;
        }
else
{
//echo str_repeat('&nbsp;',$indent*2),$key,'=',$value,'<br>';
}
    }
}

function writeelements($data)
{
    foreach($data as $key => $value)
	{
	if (is_numeric($key)) $key = 'Item';

//	echo str_repeat('&nbsp;',$indent*2),$key,'=',$value,'<br>';

        if(is_array($value)){
        }
	else
	{
		echo '<',$key,'>',$value,'</',$key,'>';
//		echo '&nbsp;&nbsp;',$key,'=',$value,'<br>';
	}
    }
}

function getStateAbbreviation( $stateIn ) {
  $stateArray = array (
    "ALABAMA"=>"AL",
    "ALASKA"=>"AK",
    "AMERICAN SAMOA"=>"AS",
    "ARIZONA"=>"AZ",
    "ARKANSAS"=>"AR",
    "ARMED FORCES AFRICA"=>"AE",
    "ARMED FORCES AMERICAS"=>"AA",
    "ARMED FORCES CANADA"=>"AE",
    "ARMED FORCES EUROPE"=>"AE",
    "ARMED FORCES MIDDLE EAST"=>"AE",
    "ARMED FORCES PACIFIC"=>"AP",
    "CALIFORNIA"=>"CA",
    "COLORADO"=>"CO",
    "CONNECTICUT"=>"CT",
    "DELAWARE"=>"DE",
    "DISTRICT OF COLUMBIA"=>"DC",
    "FEDERATED STATES OF MICRONESIA"=>"FM",
    "FLORIDA"=>"FL",
    "GEORGIA"=>"GA",
    "GUAM"=>"GU",
    "HAWAII"=>"HI",
    "IDAHO"=>"ID",
    "ILLINOIS"=>"IL",
    "INDIANA"=>"IN",
    "IOWA"=>"IA",
    "KANSAS"=>"KS",
    "KENTUCKY"=>"KY",
    "LOUISIANA"=>"LA",
    "MAINE"=>"ME",
    "MARSHALL ISLANDS"=>"MH",
    "MARYLAND"=>"MD",
    "MASSACHUSETTS"=>"MA",
    "MICHIGAN"=>"MI",
    "MINNESOTA"=>"MN",
    "MISSISSIPPI"=>"MS",
    "MISSOURI"=>"MO",
    "MONTANA"=>"MT",
    "NEBRASKA"=>"NE",
    "NEVADA"=>"NV",
    "NEW HAMPSHIRE"=>"NH",
    "NEW JERSEY"=>"NJ",
    "NEW MEXICO"=>"NM",
    "NEW YORK"=>"NY",
    "NORTH CAROLINA"=>"NC",
    "NORTH DAKOTA"=>"ND",
    "NORTHERN MARIANA ISLANDS"=>"MP",
    "OHIO"=>"OH",
    "OKLAHOMA"=>"OK",
    "OREGON"=>"OR",
    "PALAU"=>"PW",
    "PENNSYLVANIA"=>"PA",
    "PUERTO RICO"=>"PR",
    "RHODE ISLAND"=>"RI",
    "SOUTH CAROLINA"=>"SC",
    "SOUTH DAKOTA"=>"SD",
    "TENNESSEE"=>"TN",
    "TEXAS"=>"TX",
    "UTAH"=>"UT",
    "VERMONT"=>"VT",
    "VIRGINIA"=>"VA",
    "VIRGIN ISLANDS"=>"VI",
    "WASHINGTON"=>"WA",
    "WEST VIRGINIA"=>"WV",
    "WISCONSIN"=>"WI",
    "WYOMING"=>"WY",
    "ALBERTA"=>"AB",
    "BRITISH COLUMBIA"=>"BC",
    "MANITOBA"=>"MB",
    "NEW BRUNSWICK"=>"NB",
    "NEWFOUNDLAND"=>"NL",
    "LABRADOR"=>"NL",
    "NORTHWEST TERRITORIES"=>"NT",
    "NOVA SCOTIA"=>"NS",
    "NUNAVUT"=>"NU",
    "ONTARIO"=>"ON",
    "PRINCE EDWARD ISLAND"=>"PE",
    "QUEBEC"=>"QC",
    "SASKATCHEWAN"=>"SK",
    "YUKON"=>"YT"
);

    $key = strtoupper($stateIn);
	$sAbb = $stateArray[$key];
	if ($sAbb == "") $sAbb = $stateIn;

    return( $sAbb );
}




//echo "<MagentoOrder>";
//writeelements($arr);
//echo "<shipping_address>";
//writeelements($arr["shipping_address"]);
//echo "</shipping_address>";
//echo "<billing_address>";
//writeelements($arr["billing_address"]);
//echo "</billing_address>";
//echo "<payment>";
//writeelements($arr["payment"]);
//echo "</payment>";
//echo "<items>";
//foreach($arr["items"] as $key => $value){
//echo "<item>";
//writeelements($value);
//echo "</item>";
//}
//echo "</items>";
//echo "</MagentoOrder>";
//write($arr,0);

//$xml->endElement();

//echo $xml->outputMemory(true);
//echo $str;
//print_r(ArrayToXML::toXML($proxy->call($sessionId, 'sales_order.info', '100000023')));
?>
