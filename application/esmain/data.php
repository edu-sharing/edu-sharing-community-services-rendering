<?php
$data = json_decode(file_get_contents('php://input'));
$data->dummy = array();

$data->dummy['backLink'] = mc_Request::fetch('backLink', 'CHAR');
$data->dummy['baseUrl'] = mc_Request::fetch('baseUrl', 'CHAR');
$data->dummy['displayKind'] = mc_Request::fetch('display', 'CHAR', 'window');

$data->dummy['showMetadata'] = mc_Request::fetch('showMetadata', 'CHAR');
$data->dummy['showDownloadButton'] = mc_Request::fetch('showDownloadButton', 'CHAR');
$data->dummy['showDownloadAdvice'] = mc_Request::fetch('showDownloadAdvice', 'CHAR');
$data->dummy['forcePreview'] = mc_Request::fetch('forcePreview', 'CHAR');