<?php

require_once (__DIR__ . '/../../conf.inc.php');
session_id($_GET["PHPSESSID"]);
session_start();
$data = $_SESSION["mod_audio"][$_GET["ID"]];
$callback = $data['callback'] ?? 'get_resource';
$authString = $data['authString'] ?? null;
if ($authString === null) {
    error_log("Missing auth info.");
    echo "";
    exit(0);
}

header('Content-Type: text/javascript');

$script = <<<JS
setTimeout(() => $callback("{$authString}"), 5000)
JS;

echo $script;
