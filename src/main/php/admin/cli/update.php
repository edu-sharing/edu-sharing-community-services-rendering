<?php
define('CLI_MODE', true);
//error_reporting(E_ERROR);

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'conf.inc.php';

require_once MC_LIB_PATH . 'ESApp.php';
require_once MC_LIB_PATH . 'EsApplications.php';
require_once MC_LIB_PATH . 'EsApplication.php';
require_once MC_LIB_PATH . 'ESModule.php';
require_once MC_LIB_PATH . 'ESObject.php';

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR .'Version.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR .'Updater.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'locale' . DIRECTORY_SEPARATOR . 'lang.php';

ob_start();
$updater = new Updater();
ob_get_clean();

echo 'Installed version: ' . $updater -> installedVersion . PHP_EOL;

if(!$updater->isUpdatable()) {
    echo 'No update available';
} else {
    echo 'Type "y" to update to version ' . $updater -> updateVersion . PHP_EOL;
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (strtolower(trim($line)) != 'yes' && strtolower(trim($line)) != 'y') {
        echo 'Update cancelled';
    } else {
        $updateSuccess = $updater -> update();
        if(!$updateSuccess) {
            echo '[ERROR] Update failed. See logs';
        } else {
            echo '[OK] Update to version ' . $updater -> updateVersion . ' successfully completed';
        }
    }
}
