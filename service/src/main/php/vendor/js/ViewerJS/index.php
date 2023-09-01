<?php

require_once '../../../conf.inc.php';

// start session
if (empty($ESRENDER_SESSION_NAME)) {
    error_log('ESRENDER_SESSION_NAME not set in conf/system.conf.php');
    $ESRENDER_SESSION_NAME = 'ESSID';
}

session_name($ESRENDER_SESSION_NAME);

$sessid = mc_Request::fetch($ESRENDER_SESSION_NAME, 'CHAR', '');
if(!empty($sessid)){
    session_id($sessid);
}

if (!session_start()) {
    throw new Exception('Could not start session.');
}

$esrenderSessionId = session_id();
if (!$esrenderSessionId) {
    throw new Exception('Could not get current session_id().');
}
$LanguageCode = $_SESSION['languageCode'];
require_once '../../../application/esmain/init-language.php';



/**
 * custom file to override styles provided by ViewerJS
 */
$data = file_get_contents('index.html');


$data = str_replace('innerHTML="of "', 'innerHTML="/ "', $data);
$i18nStrings = [
    'Automatic',
    'Actual Size',
    'Full Width',
    'Next Page',
    'Previous Page',
    'Zoom In',
    'Zoom Out'
];
foreach($i18nStrings as $str) {
    $Message = new Phools_Message_Default('ViewerJS_' . $str);
    $data = str_replace($str, $Message->localize($Locale, $Translate), $data);
}
echo $data;

?>
<style>
    #titlebar, #toolbarRight, #pageNumberLabel {
        display: none;
    }
</style>
