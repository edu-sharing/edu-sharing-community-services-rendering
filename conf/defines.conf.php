<?php


/*
 * 
 * NOTHING TO CHANGE HERE!
 * 
 * */












$MC_INCLUDE_PATH = explode(':', ini_get("include_path"));

# LANGUAGE MAP
$_LANG = array();
$_LANG[1] = 'DE';
$_LANG[2] = 'EN';
$_LANG[3] = 'ZH';
$_LANG[4] = 'FR';
$_LANG['DE'] = 1;
$_LANG['EN'] = 2;
$_LANG['ZH'] = 3;
$_LANG['FR'] = 4;

# ERROR HANDLING
define('MC_DIE_ON_ERROR', false);
define('MC_DEBUG',   $DEBUG);
define('MC_DEVMODE', $DEVMODE);

# HTML DEFAULT CHARACTER SET
define('MC_CHAR_SET', 'utf-8');

# INTERNAL PATH DEFINITIONS
$parsedUrl = parse_url($MC_URL);
define("MC_SCHEME",  $parsedUrl['scheme']);
define("MC_HOST",    $parsedUrl['host']);
define("MC_PORT",   $parsedUrl['scheme']);
define("MC_PATH",    $parsedUrl['path'] . '/');

define("MC_DOCROOT", $MC_DOCROOT);
define("MC_ROOT_PATH", $ROOT_PATH);
define("MC_BASE_DIR", MC_ROOT_PATH);
define("MC_URL",  $MC_URL);
define("MC_MODULES_PATH", MC_ROOT_PATH."modules/");
define("MC_MODULES_URI",  MC_URL."modules/");
define("MC_LIB_PATH",     MC_ROOT_PATH."func/classes.new/");
define("CC_CONF_PATH",     MC_ROOT_PATH."conf/");
define("CC_CONF_APPFILE",  "ccapp-registry.properties.xml");
define("CC_LOCALE_PATH",   MC_ROOT_PATH."locale/");
define("CC_LOCALE_FILE",  "lang.common.php");
define('CC_RENDER_PATH',   $CC_RENDER_PATH);
define("MC_ROOT_URI",  $ROOT_URI);
define("INTERNAL_URL", $INTERNAL_URL);

// declaring include paths

$MC_INCLUDE_PATH[] = MC_ROOT_PATH."func/extern/";
array_unshift($MC_INCLUDE_PATH, '.');
ini_set("include_path", implode(';', array_unique($MC_INCLUDE_PATH)));
unset($MC_INCLUDE_PATH);
