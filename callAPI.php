<?php
function callAPI($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $responce = curl_exec($curl);
    $result = json_decode($responce, $associative=true);
    if($result == null){
        return false;
    }
    $result = $result["data"];
    curl_close($curl);
    return $result;
}

?>
