<?php
require_once __DIR__ . '/../../conf/system.conf.php';

$pathArr = explode('modules/h5p', $_REQUEST['ID']);
$path = $CC_RENDER_PATH . '/h5p' . $pathArr[1];


$filesize = filesize($path);

header("Content-length: " . $filesize);
header('Access-Control-Allow-Origin: *');

@readfile($path);

exit(0);
