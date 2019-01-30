<?php

if(basename($_REQUEST['ID']) === 'index.html') {
    $_SESSION['esrender']['mod']['html']['allow'] = dirname($_REQUEST['ID']);
    header('HTTP/1.0 200 Ok');
} else if(strpos($_REQUEST['ID'], $_SESSION['esrender']['mod']['html']['allow']) !== -1) {
    $skipToken = true;
    header('HTTP/1.0 200 Ok');
}
