<?php
if(strpos($_REQUEST['ID'], 'cache/h5p/libraries') !== -1) {
    $_SESSION['esrender']['check'] = $_REQUEST['ID'];
    $skipToken = true;
    header('HTTP/1.0 200 Ok');
}
