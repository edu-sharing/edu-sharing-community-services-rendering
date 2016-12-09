<?php

include_once ('../../conf.inc.php');

if (!export_metadata)
    die('no metadaservice');

require_once (MC_LIB_PATH . 'ESApp.php');
require_once (MC_LIB_PATH . 'EsApplications.php');
require_once (MC_LIB_PATH . 'EsApplication.php');

function getEntryElement($dom, $key, $val) {

    $entry = $dom -> createElement('entry', $val);
    $entry -> setAttribute("key", $key);
    return $entry;
};

$impl = new DOMImplementation();
$dtd = $impl -> createDocumentType('properties', '', 'http://java.sun.com/dtd/properties.dtd');

$dom = $impl -> createDocument('1.0', '', $dtd);
$dom -> encoding = 'UTF-8';
$dom -> preserveWhiteSpace = false;
$dom -> formatOutput = true;
$element = $dom -> createElement('properties');
$dom -> appendChild($element);

$application = new ESApp();
$application -> getApp('esmain');
$hc = $application -> getHomeConf();

$port = '';
if(!empty($hc -> prop_array['port']))
	$port = ':' . $hc -> prop_array['port'];

$cep_url = $hc -> prop_array['scheme'] . '://' . $hc -> prop_array['host'] . $port . dirname($_SERVER['SCRIPT_NAME']) . '/' . 'index.php';

if (empty($hc -> prop_array['public_key'])) {
    require_once (dirname(__FILE__) . '/../../func/classes.new/Helper/AppPropertyHelper.php');
    $appPropertyHelper = new AppPropertyHelper($hc);
    $appPropertyHelper -> addSslKeypairToHomeConfig();
    $application = new ESApp();
    $application -> getApp('esmain');
    $hc = $application -> getHomeConf();
}

$entry = getEntryElement($dom, 'appid', $hc -> prop_array['appid']);
$element -> appendChild($entry);

$entry = getEntryElement($dom, 'appcaption', $hc -> prop_array['appcaption']);
$element -> appendChild($entry);

$entry = getEntryElement($dom, 'type', $hc -> prop_array['type']);
$element -> appendChild($entry);

$entry = getEntryElement($dom, 'host', $hc -> prop_array['host']);
$element -> appendChild($entry);

$entry = getEntryElement($dom, 'port', $hc -> prop_array['port']);
$element -> appendChild($entry);

$entry = getEntryElement($dom, 'trustedclient', $hc -> prop_array['trustedclient']);
$element -> appendChild($entry);

$entry = getEntryElement($dom, 'contenturl', $cep_url);
$element -> appendChild($entry);

$entry = getEntryElement($dom, 'public_key', $hc -> prop_array['public_key']);
$element -> appendChild($entry);

header("Content-Type: application/xhtml+xml; charset=utf-8");
print $dom -> saveXML();
