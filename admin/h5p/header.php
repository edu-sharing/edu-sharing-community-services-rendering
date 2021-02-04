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
if(!empty($_SESSION['expire']) && time() > $_SESSION['expire']) {
    $_SESSION['loggedin'] = 0;
    $showTimeout = true;
}


global $H5PFramework, $H5PCore;
$H5PFramework = new H5PFramework();
$H5PCore = new H5PCore($H5PFramework, $H5PFramework->get_h5p_path(), $H5PFramework->get_h5p_url(), mc_Request::fetch('language', 'CHAR', 'de'), false);
$H5PStorage = new H5PStorage($H5PFramework, $H5PCore);

global $MC_URL;
global $db;
$db = RsPDO::getInstance();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>H5P-Admin-Backend</title>

    <link rel="stylesheet" href="css/h5p.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap" rel="stylesheet">
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
<?php
    if ($_SESSION['loggedin'] !== 1){
    if ($showTimeout){
        echo "
            <script>
                Swal.fire({
                    title: 'Not logged in!',
                    text: 'Your session expired. Please Login again:',
                    position: 'center',
                    icon: 'error',
                    showCancelButton: false,
                  confirmButtonColor: '#3085d6',
                  confirmButtonText: 'Login'
                }).then((result) => {
                  if (result.value) {
                    window.open('../index.php', '_self');
                  }
                })
            </script>
        ";
    }else{
        echo "
            <script>
                Swal.fire({
                    title: 'Not logged in!',
                    text: 'Please Login:',
                    position: 'center',
                    icon: 'error',
                    showCancelButton: false,
                  confirmButtonColor: '#3085d6',
                  confirmButtonText: 'Login'
                }).then((result) => {
                  if (result.value) {
                    window.open('../index.php', '_self');
                  }
                })
            </script>
        ";
    }
    exit;
    }
?>
