<?php
header('Content-Type: application/json');
require_once ('../../conf.inc.php');
require_once ('../../admin/model/Version.php');

$CurrentDirectoryName = basename(dirname(__FILE__));
$application = new ESApp();
$application->getApp($CurrentDirectoryName);
$homeRepId = $application->getHomeConf()->prop_array['homerepid'];
$homeRep = $application->getAppByID($homeRepId);

require_once "validate_signature.php";

$version=new Version();
$info["version"]=$version->getInstalledVersion();
$info["details"]=file_get_contents($CurrentDirectoryName . '/../../../version.json');
$packages = json_decode(file_get_contents($CurrentDirectoryName . '/../../../composer.lock'))->packages;
$info["licenses"]=[
    "core" => "Lists of " . count($packages) . " third-party dependencies.\n"
];
foreach($packages as $package) {
    $licenses = implode(", ", $package->license);
    $info["licenses"]["core"] .= "     ($licenses) $package->name (" . $package->name . "@" . $package->version;
    if($package->homepage) {
        $info["licenses"]["core"] .= " - $package->homepage";
    }
    $info["licenses"]["core"] .= ")\n";
}

echo json_encode($info);
