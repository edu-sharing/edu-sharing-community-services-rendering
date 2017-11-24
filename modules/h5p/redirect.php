<?php
require_once __DIR__ . '/../../conf/system.conf.php';

$pathArr = explode('modules/h5p', $_REQUEST['ID']);
$path = $CC_RENDER_PATH . '/h5p' . $pathArr[1];


$filesize = filesize($path);

$path_parts = pathinfo($path);
if($path_parts['extension'] === 'css') {
    $mime_type = 'text/css';
    header("Content-type: " . $mime_type);
}


header("Content-length: " . $filesize);
header('Access-Control-Allow-Origin: *');

@readfile($path);

exit(0);
