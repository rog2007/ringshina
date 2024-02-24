<?php
function query($sql, $params = [])
{
    global $dbcon;
    $res = ['result' => false, 'error' => [], 'data' => []];
    $stmt = $dbcon->prepare($sql);
    if (!$stmt) {
        $res['error'] = $dbcon->errorInfo();
        return $res;
    }
    if ($stmt->execute($params)) {
        $res['result'] = true;
        $res['data'] = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
        $res['error'] = $stmt->errorInfo();
    }
    return $res;
}