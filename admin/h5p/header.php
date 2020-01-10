<?php

require_once (dirname(__FILE__) . '/../../conf.inc.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p.classes.php');
require_once (dirname(__FILE__) . '/../../modules/h5p/H5PFramework.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-file-storage.interface.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-default-storage.class.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-development.class.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-event-base.class.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-metadata.class.php');
require_once dirname(__FILE__) . '/../locale/lang.php';

session_start();
if ($_SESSION['loggedin'] !== 1){
    echo 'Not logged in! <a href="../index.php">Login</a>';
    exit;
}

global $H5PFramework, $H5PCore;
$H5PFramework = new H5PFramework();
$H5PCore = new H5PCore($H5PFramework, $H5PFramework->get_h5p_path(), $H5PFramework->get_h5p_url(), mc_Request::fetch('language', 'CHAR', 'de'), false);
$H5PStorage = new H5PStorage($H5PFramework, $H5PCore);

global $MC_URL;
global $db;
$db = new PDO('sqlite:' . $CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p'.DIRECTORY_SEPARATOR . 'db');
$db -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>H5P-Admin-Backend</title>

    <link rel="stylesheet" href="css/h5p.css" />
    <script src="js/sweetalert2.all.min.js"></script>
</head>
<body>

<div class="h5p-header">
    <h1>H5P-Admin-Backend</h1>
    <ul class="menu">
        <li><a href="index.php">H5P-Content</a></li>
        <li><a href="libraries.php">H5P-Libraries</a></li>
        <li><a href="../index.php">Rendering-Service-Admin</a></li>
    </ul>
</div>

