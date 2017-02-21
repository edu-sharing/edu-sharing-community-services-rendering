<?php

date_default_timezone_set('Etc/GMT+1');


# SYSTEM PATH SETTINGS
$MC_URL = '[[[TOKEN_URL]]]';
$MC_DOCROOT= '[[[TOKEN_DOCROOT]]]';

# RENDER PATH SETTINGS
# required ends with a slash
$CC_RENDER_PATH = '[[[TOKEN_DATA_DIR]]]';

DEFINE("ES_KEY","thetestkey");
DEFINE("ES_IV","initvect");
DEFINE("ENABLE_TRACK_OBJECT", true);
DEFINE("DISABLE_TRACK_ANONYMIZATION", false);
DEFINE("ENABLE_METADATA_RENDERING", false);
DEFINE("DISPLAY_DYNAMIC_METADATA_KEYS", serialize(array(
		'{http://www.alfresco.org/model/content/1.0}creator',
		'{http://www.campuscontent.de/model/1.0}commonlicense_key',
		'{http://www.alfresco.org/model/content/1.0}versionLabel',
		'REPOSITORY_ID'
)));

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

