<?php

require_once '../../../conf.inc.php';

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

$allowDownloadAndPrint = true;
if (isset($_GET['esOptions']) && is_string($_GET['esOptions'])) {
    try {
        $options = json_decode(base64_decode($_GET['esOptions']), true, 512, JSON_THROW_ON_ERROR);
        error_log(json_encode($options));
        if (isset($options['allowDownload']) && $options['allowDownload'] === 0) {
            $allowDownloadAndPrint = false;
        }
    } catch (Exception $exception) {
        error_log('decoding esOptions failed with exception: ' . $exception->getMessage());
        unset($exception);
    }
}

if (!session_start()) {
    throw new Exception('Could not start session.');
}

$esrenderSessionId = session_id();
if (!$esrenderSessionId) {
    throw new Exception('Could not get current session_id().');
}
//$LanguageCode = $_SESSION['languageCode'];
require_once '../../../application/esmain/init-language.php';


/**
 * Adding styles and scripts
 */
$data = file_get_contents('viewer.html');
$data = str_replace('{{PDF_JS_PLACEHOLDER}}', $MC_URL . '/vendor/js/pdfJS/build/pdf.js', $data);
$data = str_replace('{{VIEWER_CSS_PLACEHOLDER}}', $MC_URL . '/vendor/js/pdfJS/viewer.css', $data);
$data = str_replace('{{VIEWER_JS_PLACEHOLDER}}', $MC_URL . '/vendor/js/pdfJS/viewer.js', $data);
if (! $allowDownloadAndPrint) {
    $data = str_replace('id="toolbarViewerRight"', 'id="toolbarViewerRight" style="visibility:hidden"', $data);
}

echo $data;
