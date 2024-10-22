<?php

require_once '../../../../conf.inc.php';

global $MC_URL;

// start session
if (empty($ESRENDER_SESSION_NAME)) {
    error_log('ESRENDER_SESSION_NAME not set in conf/system.conf.php');
    $ESRENDER_SESSION_NAME = 'ESSID';
}

session_name($ESRENDER_SESSION_NAME);

$sessid = mc_Request::fetch($ESRENDER_SESSION_NAME, 'CHAR', '');
if (!empty($sessid)) {
    session_id($sessid);
}

$allowDownloadAndPrint = !isset($_GET['esObject']);

try {
    if (!session_start()) {
        throw new Exception('Could not start session.');
    }

    $esrenderSessionId = session_id();
    if (!$esrenderSessionId) {
        throw new Exception('Could not get current session_id().');
    }
} catch (Exception $exception) {
    error_log($exception->getMessage());
    echo 'error';
    exit();
}
//$LanguageCode = $_SESSION['languageCode'];
require_once '../../../../application/esmain/init-language.php';

$customCss = '/vendor/js/pdfJS/web/' . ($allowDownloadAndPrint ? 'esdefault.css' : 'esalternative.css');

/**
 * Adding styles and scripts
 */
$data = file_get_contents('viewer.html');
$data = str_replace('{{PDF_JS_PLACEHOLDER}}', $MC_URL . '/vendor/js/pdfJS/build/pdf.mjs', $data);
$data = str_replace('{{VIEWER_CSS_PLACEHOLDER}}', $MC_URL . '/vendor/js/pdfJS/web/viewer.css', $data);
$data = str_replace('{{VIEWER_JS_PLACEHOLDER}}', $MC_URL . '/vendor/js/pdfJS/web/viewer.mjs', $data);
$data = str_replace('{{ESPDF_JS_PLACEHOLDER}}', $allowDownloadAndPrint ? '' : $MC_URL . '/vendor/js/pdfJS/web/espdf.js', $data);
$data = str_replace('{{CUSTOM_CSS_PLACEHOLDER}}', $MC_URL . $customCss, $data);

echo $data;
