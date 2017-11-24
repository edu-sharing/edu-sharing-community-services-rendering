<?php
/**
 * This product Copyright 2010 metaVentis GmbH.  For detailed notice,
 * see the "NOTICE" file with this distribution.
 *
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */
define('gen_time_start', microtime());

// dont display errors, but log all of them.
ini_set('error_reporting', E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('html_errors', 1);

//metadata import export
define('export_metadata',true);
define('import_metadata',true);

// AUTOLOADING
require_once(dirname(__FILE__).'/func/classes.new/ESRender/Autoload.php');
ESRender_Autoload::register();

require_once(dirname(__FILE__).'/func/extern/Phools/Autoload.php');
Phools_Autoload::register();

require_once (dirname(__FILE__) . '/conf/system.conf.php');

require_once ($ROOT_PATH."conf/db.conf.php");
require_once ($ROOT_PATH."conf/defines.conf.php");
require_once ($ROOT_PATH."conf/custom.conf.php");
require_once ($ROOT_PATH."conf/extlib.conf.php");

if (@phpversion() < '5.3.0') {
	die('This version of edu-sharing only supports PHP 5.3 or higher.');
}

$GLOBALS['_MCFORM'] = array_merge($_GET, $_POST);

require_once(MC_LIB_PATH."Debug.php");
require_once(MC_LIB_PATH."SysMsg.php");
require_once(MC_LIB_PATH."Request.class.php");

