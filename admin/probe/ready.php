<?php

require_once('../../conf.inc.php');
require_once('../model/Version.php');
require_once('../../func/classes.new/RsPDO.php');

header('Content-Type: application/json');

$version=new Version();
$status["version"] = $version->getInstalledVersion();

//Server-load linux
if( function_exists('sys_getloadavg')) {
    $load = sys_getloadavg();
    if($load[0] > 80) {
        $status["LOAD"] = 'Error - '.$load[0].'%';
        header("HTTP/1.1 503 Too busy, try again later");
    }else{
        $status["LOAD"] = 'OK - '.$load[0].'%';
    }
}else {
    $status["LOAD"] = 'Warning - Server-Load not supported. Try Linux.';
}

//db-status
try {
    $pdo = RsPDO::getInstance();
    $status["DB"] = 'OK';
}catch(PDOException $e) {
    $status["DB"] = 'Error - '.$e -> getMessage();
    header("HTTP/1.1 500 Internal Server Error");
}

echo json_encode($status);
