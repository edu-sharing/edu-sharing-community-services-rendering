<?php
session_start();
require_once dirname(__FILE__) . '/../conf.inc.php';
require_once MC_LIB_PATH . 'ESApp.php';
require_once MC_LIB_PATH . 'EsApplications.php';
require_once MC_LIB_PATH . 'EsApplication.php';
require_once MC_LIB_PATH . 'ESModule.php';
require_once MC_LIB_PATH . 'ESObject.php';
require_once dirname(__FILE__) . '/model/Admin.php';
require_once dirname(__FILE__) . '/model/LoginManager.php';
require_once dirname(__FILE__) . '/model/Updater.php';
require_once dirname(__FILE__) . '/locale/lang.php';
$admin = new Admin();