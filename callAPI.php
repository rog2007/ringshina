<?php
function callAPI($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = json_decode(curl_exec($curl), $associative=true);
    if($result == null){
        return false;
    }
    $result = $result["data"];
    curl_close($curl);
    return $result;
}

?>
