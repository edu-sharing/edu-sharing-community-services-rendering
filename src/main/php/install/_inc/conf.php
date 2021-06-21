<?php
/*
* $McLicense$
*
* $Id$
*
*/


//ini_set('display_errors', 0);
//error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
$_REQUEST = array_merge($_GET, $_POST);

$supported_lang  = array();
$supported_lang[1] = 'DE';
$supported_lang[2] = 'EN';

$default_lang = 1;

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'defines.php');

// try to set include paths
require_once(MC_BASE_DIR . 'conf' . DIRECTORY_SEPARATOR . 'extlib.conf.php');

require_once(INST_PATH_LANG . "lang.txt.php");

// list of file extensions searched for token replacement
$scan_for_exTensions = array(
	"html",
	"htm",
	"inc",
	"php",
	"java",
	"lay",
	"wsdl",
	"xml",
	"",
);

// list of directories excluded from token replacement search
$scan_dir_exClusions = array(
	"install",
);

require_once(INST_PATH_LIB . "SysMsg.php");

