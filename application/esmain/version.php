<?php
header('Content-Type: application/json');
require_once ('../../conf.inc.php');
require_once ('../../admin/model/Version.php');

$version=new Version();
$info["version"]=$version->getInstalledVersion();

echo json_encode($info);
