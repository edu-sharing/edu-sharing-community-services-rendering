<?php
/*
* $McLicense$
*
* $Id$
*
*/


//##################################
//#                                #
//#  do NOT change settings below  #
//#                                #
//##################################

define("INST_LANG_ID",  (isset($_REQUEST['LANG']) ? intval($_REQUEST['LANG']) : 1));
define("INST_LANG_ISO", $supported_lang[INST_LANG_ID]);

$rp = dirname(dirname(dirname(__FILE__)));
if (basename($rp) == 'maintenance')
{
	$rp = dirname($rp);
}
define("MC_BASE_DIR", $rp . DIRECTORY_SEPARATOR);

define("INST_PATH",  dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define("INST_PATH_INC",  INST_PATH . '_inc'     . DIRECTORY_SEPARATOR);
define("INST_PATH_LANG", INST_PATH . '_lang'    . DIRECTORY_SEPARATOR . INST_LANG_ISO . DIRECTORY_SEPARATOR);
define("INST_PATH_LAY",	 INST_PATH . '_layout'  . DIRECTORY_SEPARATOR);
define("INST_PATH_LIB",	 INST_PATH . '_classes' . DIRECTORY_SEPARATOR);
define("INST_PATH_TMPL", INST_PATH . '_tmpl'    . DIRECTORY_SEPARATOR);
define("INST_PATH_LOGS", INST_PATH . '_logs'    . DIRECTORY_SEPARATOR);

define('MC_DIE_ON_ERROR', true);

# HTML DEFAULT CHARACTER SET
if ( ! defined('MC_CHAR_SET') )
{
	define('MC_CHAR_SET', 'utf-8');
}

// from conf/defines.conf.php
define("MC_EXTLIB_PATH",  MC_BASE_DIR . '/func/extern/' );

