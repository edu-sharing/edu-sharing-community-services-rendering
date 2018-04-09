<?php
require_once __DIR__ . '/../../conf/system.conf.php';
$pathArr = explode('modules/h5p', $_REQUEST['ID']);
$path = $CC_RENDER_PATH . '/h5p' . $pathArr[1];

//check if requested path is child of h5p cache directory
if(strpos(realpath($path), realpath($CC_RENDER_PATH . '/h5p') === false))
    throw new Exception('Access not allowed');

$filesize = filesize($path);

$path_parts = pathinfo($path);
if($path_parts['extension'] === 'css') {
    header("Content-type: text/css");
} else {
    header('Content-type: ' . mime_content_type($path));
}
header('Content-length: ' . $filesize);
header('Access-Control-Allow-Origin: *');
echo file_get_contents($path);
exit(0);
