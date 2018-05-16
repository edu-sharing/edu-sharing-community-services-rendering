<?php
require_once ('../../conf.inc.php');
require_once ('../../admin/model/Version.php');

$version=new Version();
$info["version"]=$version->getInstalledVersion();

header('Content-Type','application/json');
echo json_encode($info);
?>