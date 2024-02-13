<?php

# SYSTEM PATH SETTINGS
$MC_URL = '[[[TOKEN_URL]]]';
$INTERNAL_URL = '';
$MC_DOCROOT= '[[[TOKEN_DOCROOT]]]';
$CUSTOM_CONTENT_URL = '';

# RENDER PATH SETTINGS
# required ends with a slash
$CC_RENDER_PATH = '[[[TOKEN_DATA_DIR]]]';

DEFINE("ENABLE_METADATA_INLINE_RENDERING", true);
# Default config for dataprotection dialog (the key must be a regex matching the given url, and the value is an array of name + url. First index match will win)
$DATAPROTECTIONREGULATION_CONFIG = ["enabled" => false, "modules" => [], "urls" => ['/.*/' => ["name" => "Example", "url" => "http://example"]]];

DEFINE("ENABLE_VIEWER_JS", true); # toggle viewer.js for office documents
$VIEWER_JS_CONFIG = ["pdf"]; # office and spreadsheet also available

$ESRENDER_SESSION_NAME = 'ESSID';

# ERROR HANDLING
# toggle debug messages: TRUE = show | FALSE = hide | [0-9]+ = display messages to user with specified ident only
$DEBUG = false;
# toggle some developer tools & shortlinks. usage like $DEBUG. has NO effect if $DEBUG is turned off
$DEVMODE = false;

// 1 = german, 2 = english
$DEFAULT_LANG = 1;


$MC_URL = rtrim($MC_URL, '/');
$ROOT_PATH = current(explode("conf", dirname(__FILE__)));
$ROOT_URI  = $MC_URL . '/';
$H5P_DISABLE_CACHE_DELAY = 0;

