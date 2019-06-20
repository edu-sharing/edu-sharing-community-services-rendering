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

ob_start();
set_time_limit(0);
include_once ('../conf.inc.php');

$skipToken = false;

if (empty($ESRENDER_SESSION_NAME)) {
    error_log('ESRENDER_SESSION_NAME not set in conf/system.conf.php');
    $ESRENDER_SESSION_NAME = 'ESSID';
}

define('CC_DEBUG_REDIRECT', true);

/**
 * Remove double-slashes from given $Path.
 *
 * @param string $Path
 *
 * @return string
 */
function sanitizePath($Path) {
    $SanitizedPath = $Path;
    do {
        $Path = $SanitizedPath;
        $SanitizedPath = str_replace('//', '/', $Path);
    } while( $SanitizedPath != $Path );

    return $SanitizedPath;
}

function cc_rd_debug($err_msg) {
    if (CC_DEBUG_REDIRECT) {
        // suppressing all warnings from included file to not disrupt download
        @include ('./default/cc_rd_debug_handle.inc.php');
        cc_rd_log_die($err_msg);
    }
}

/*
 * h5p stuff
 * */
if(strpos($_REQUEST['ID'], 'cache/h5p/libraries') !== false && strpos($_REQUEST['ID'], '..') === false) {

    $_SESSION['esrender']['check'] = $_REQUEST['ID'];

    $src_file = str_replace('/rendering-service/modules/cache', 'C:\xampp\cache\rendering-service', $_REQUEST['ID']);

    $filesize = filesize($src_file);

    $path_parts = pathinfo($src_file);
    if($path_parts['extension'] === 'css')
        $mime_type = 'text/css';
    else
        $mime_type = 'application/javascript';

    $mimetype = mime_content_type($src_file);

    header("Content-type: ".$mimetype);


    header("Content-length: " . $filesize);
    header('Access-Control-Allow-Origin: *');

    if($filesize <= 2048) {
        @readfile($src_file);
    } else {
        $fd = fopen($src_file, 'rb');
        while(!feof($fd)) {
            $buffer = fread($fd, 2048);
            echo $buffer;
            flush();
        }
        fclose($fd);
    }
    exit();

}







// start session to read object-data
if (!empty($_GET[$ESRENDER_SESSION_NAME])) {
    $l_sid = $_GET[$ESRENDER_SESSION_NAME];
} else if(!empty($_COOKIE[$ESRENDER_SESSION_NAME])) {
    header('HTTP/1.0 400 Bad Request');
    $l_sid = $_COOKIE[$ESRENDER_SESSION_NAME];
} else {
    header('HTTP/1.0 400 Bad Request');
    $l_sid = cc_rd_debug('esrender session missing');
}

// the object
if (empty($_GET['ID'])) {
    header('HTTP/1.0 400 Bad Request');
    $l_dest = cc_rd_debug('esrender destination missing');
} else {
    $l_dest = (string)$_GET['ID'];
}
$l_dest = sanitizePath($l_dest);

session_name($ESRENDER_SESSION_NAME);
session_id($l_sid);
session_start();

if (empty($_SESSION['esrender'])) {
    error_log('Missing "esrender"-session-data.');
    header('HTTP/1.0 500 Internal Server Error');
    cc_rd_debug('missing session render data');
}

@include ('./' . $_SESSION['esrender']['mod_name'] . '/redirect.inc.php');

if(!$skipToken) {

    if (empty($_SESSION['esrender']['token'])) {
        error_log('Missing token (session)');
        cc_rd_debug('Missing token (session)');
        header('HTTP/1.0 500 Internal Server Error');
    }
    if (empty($_REQUEST['token'])) {
        error_log('Missing token (request)');
        cc_rd_debug('Missing token (request)');
        header('HTTP/1.0 500 Internal Server Error');
    }
    if (($_SESSION['esrender']['token'] !== $_REQUEST['token']) && ($_SESSION['esrender']['token']) !== $_COOKIE['ESSEC']) {
        error_log('Invalid token');
        cc_rd_debug('Invalid token');
        header('HTTP/1.0 500 Internal Server Error');
    } else {
        $token = md5(uniqid());
        $_SESSION['esrender']['token'] = $token;
        setcookie('ESSEC', $token, time() + 300);
    }

}

session_write_close();

// check path access
if (empty($_SESSION['esrender']['check'])) {
    $l_check = cc_rd_debug('esrender check missing');
} else {
    $l_check = $_SESSION['esrender']['check'];
}
$l_check = sanitizePath($l_check);

$dest_path = parse_url($l_dest, PHP_URL_PATH);

if (strpos($dest_path, $l_check) !== 0) {
    header('HTTP/1.0 400 Bad Request');
    cc_rd_debug('Permission denied (path access check failed)');
}

$dest_path = sanitizePath($dest_path);

if (empty($_SESSION['esrender']['file_name'])) {
    $file_name = basename($_SESSION['esrender']['mod_path']);
} else {
    $file_name = $_SESSION['esrender']['file_name'];
}

if (empty($_SESSION['esrender']['display_kind'])) {
    $display_kind = ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD;
} else {
    $display_kind = $_SESSION['esrender']['display_kind'];
}

// preparing $src_file here to send any 404-header before the included
$_SESSION['esrender']['mod_path'] = sanitizePath($_SESSION['esrender']['mod_path']);
$sub_file = substr($l_dest, strlen($_SESSION['esrender']['mod_path']));
$src_file = $_SESSION['esrender']['src_root'] . $sub_file;

$src_file = strtok($src_file, '?');

if ((!is_file($src_file)) || (!is_readable($src_file))) {
    header("HTTP/1.0 404 Not Found");
    trigger_error('Source-file "' . $src_file . '" not found.', E_USER_ERROR);
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $src_file);

finfo_close($finfo);

$buffer = ob_get_clean();
if (!empty($buffer)) {
    // suppressing all warnings from included file to not disrupt download
    @include ('./default/cc_rd_debug_handle.inc.php');
    cc_rd_log_buffer($buffer);
}

// fetching customized module header
if (!@include ('./' . $_SESSION['esrender']['mod_name'] . '/redirect_header.inc.php')) {
    // suppressing all warnings from included file to not disrupt download
    @include ('./default/redirect_header.inc.php');
}

$filesize = filesize($src_file);

header("Content-length: " . $filesize);
header('Access-Control-Allow-Origin: *');

if($filesize <= 2048) {
    @readfile($src_file);
} else {
    $fd = fopen($src_file, 'rb');
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
        flush();
    }
    fclose($fd);
}

exit(0);

