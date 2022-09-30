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
$logCacheRead = false;

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
function addContentHeaders($src_file){
    $filesize = filesize($src_file);
    $mimetype = mime_content_type($src_file);
    if(strpos($src_file, '.css')){
        $mimetype = 'text/css';
    }elseif(strpos($src_file, '.js')){
        $mimetype = 'text/javascript';
    }
    header("Content-type: ".$mimetype);
    header("Content-length: " . $filesize);
    header('Access-Control-Allow-Origin: *');
}


/*
 * h5p stuff
 * */
if(strpos($_REQUEST['ID'], 'cache/h5p/libraries') !== false && strpos($_REQUEST['ID'], '..') === false) {

    $_SESSION['esrender']['check'] = $_REQUEST['ID'];

    $rs_name = substr($MC_URL, strrpos($MC_URL, "/") + 1);
    $src_file = str_replace('/'.$rs_name.'/modules/cache', $CC_RENDER_PATH, $_REQUEST['ID']);

    $filesize = filesize($src_file);

    $path_parts = pathinfo($src_file);

    addContentHeaders($src_file);

    if($filesize <= 2048) {
        @readfile($src_file);
    } else {
        $fd = fopen($src_file, 'rb');
        if($fd != false){
            while(!feof($fd)) {
                $buffer = fread($fd, 2048);
                echo $buffer;
                flush();
            }
        }
        fclose($fd);
    }
    exit();

}

// start session to read object-data
if (!empty($_GET[$ESRENDER_SESSION_NAME])) {
    $l_sid = $_GET[$ESRENDER_SESSION_NAME];
} else if(!empty($_SERVER['HTTP_REFERER'])) {
    $parts = parse_url($_SERVER['HTTP_REFERER']);
    parse_str($parts["query"], $query);
    if (!empty($query[$ESRENDER_SESSION_NAME])){
        $l_sid = $query[$ESRENDER_SESSION_NAME];
    }else{
        header('HTTP/1.0 400 Bad Request');
        $l_sid = cc_rd_debug('esrender session missing');
    }
} else if(!empty($_COOKIE[$ESRENDER_SESSION_NAME])) {
    //header('HTTP/1.0 400 Bad Request');
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

    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
        setcookie("ESSEC", "", time()-3600); // remove ESSEC-cookie
        session_unset();     // unset $_SESSION variable for the run-time
        session_destroy();   // destroy session data in storage
        error_log('Session to old');
    }

    if (empty($_SESSION['esrender']['token'])) {
        error_log('Missing token (session)');
        cc_rd_debug('Missing token (session)');
        header('HTTP/1.0 401 Unauthorized');
    }
    if (empty($_REQUEST['token']) && empty($_COOKIE['ESSEC'])) {
        error_log('Missing token (request)');
        cc_rd_debug('Missing token (request)');
        header('HTTP/1.0 401 Unauthorized');
    }
    if ( (isset($_REQUEST['token']) && $_SESSION['esrender']['token'] == $_REQUEST['token']) ||
        ( isset($_COOKIE['ESSEC'] ) && $_SESSION['esrender']['token'] == $_COOKIE['ESSEC'] ) ) {
        $token = $_SESSION['esrender']['token'];
        setcookie('ESSEC', $token, time() + 300);
        $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
    } else {
        cc_rd_debug('Invalid token');
        header('HTTP/1.0 401 Unauthorized');
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
if (substr($src_file, -3) == 'svg'){    //set correct type for svg
    $mime_type = 'image/svg+xml';
}

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

//partial content requests
if(isset($_SERVER['HTTP_RANGE'])) {
    //serveFilePartial($src_file, $file_name);
    partialContent($src_file);
}
addContentHeaders($src_file);

if ($logCacheRead){
    error_log('start readfile from cache...');
}

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

if ($logCacheRead){
    error_log('file finished.');
}

exit(0);




function serveFilePartial($fileName, $fileTitle = null, $contentType = 'application/octet-stream')
{
    if( !file_exists($fileName) )
        throw New \Exception(sprintf('File not found: %s', $fileName));
    if( !is_readable($fileName) )
        throw New \Exception(sprintf('File not readable: %s', $fileName));
    ### Remove headers that might unnecessarily clutter up the output
    header_remove('Cache-Control');
    header_remove('Pragma');
    ### Default to send entire file
    $byteOffset = 0;
    $byteLength = $fileSize = filesize($fileName);
    header('Accept-Ranges: bytes', true);
    header(sprintf('Content-Type: %s', $contentType), true);
    if( $fileTitle )
        header(sprintf('Content-Disposition: attachment; filename="%s"', $fileTitle));
    ### Parse Content-Range header for byte offsets, looks like "bytes=11525-" OR "bytes=11525-12451"
    if( isset($_SERVER['HTTP_RANGE']) && preg_match('%bytes=(\d+)-(\d+)?%i', $_SERVER['HTTP_RANGE'], $match) )
    {
        ### Offset signifies where we should begin to read the file
        $byteOffset = (int)$match[1];
        ### Length is for how long we should read the file according to the browser, and can never go beyond the file size
        if( isset($match[2]) ){
            $finishBytes = (int)$match[2];
            $byteLength = $finishBytes + 1;
        } else {
            $finishBytes = $fileSize - 1;
        }

        $cr_header = sprintf('Content-Range: bytes %d-%d/%d', $byteOffset, $finishBytes, $fileSize);

        header("HTTP/1.1 206 Partial content");
        header($cr_header);  ### Decrease by 1 on byte-length since this definition is zero-based index of bytes being sent
    }
    $byteRange = $byteLength - $byteOffset;
    header(sprintf('Content-Length: %d', $byteRange));
    header(sprintf('Expires: %s', date('D, d M Y H:i:s', time() + 60*60*24*90) . ' GMT'));
    $buffer = ''; 	### Variable containing the buffer
    $bufferSize = 512 * 16; ### Just a reasonable buffer size
    $bytePool = $byteRange; ### Contains how much is left to read of the byteRange
    if( !$handle = fopen($fileName, 'r') )
        throw New \Exception(sprintf("Could not get handle for file %s", $fileName));
    if( fseek($handle, $byteOffset, SEEK_SET) == -1 )
        throw New \Exception(sprintf("Could not seek to byte offset %d", $byteOffset));
    while( $bytePool > 0 )
    {
        $chunkSizeRequested = min($bufferSize, $bytePool); ### How many bytes we request on this iteration
        ### Try readin $chunkSizeRequested bytes from $handle and put data in $buffer
        $buffer = fread($handle, $chunkSizeRequested);
        ### Store how many bytes were actually read
        $chunkSizeActual = strlen($buffer);
        ### If we didn't get any bytes that means something unexpected has happened since $bytePool should be zero already
        if( $chunkSizeActual == 0 )
        {
            ### For production servers this should go in your php error log, since it will break the output
            trigger_error('Chunksize became 0', E_USER_WARNING);
            break;
        }
        ### Decrease byte pool with amount of bytes that were read during this iteration
        $bytePool -= $chunkSizeActual;
        ### Write the buffer to output
        print $buffer;
        ### Try to output the data to the client immediately
        flush();
    }
    exit();
}

function partialContent($src_file){
    $filesize = filesize($src_file);
    $ranges = array_map(
        'intval', // Parse the parts into integer
        explode(
            '-', // The range separator
            substr($_SERVER['HTTP_RANGE'], 6) // Skip the `bytes=` part of the header
        )
    );

    // If the last range param is empty, it means the EOF (End of File)
    if (!$ranges[1]) {
        $ranges[1] = $filesize - 1;
    }

    // Send the appropriate headers
    header('HTTP/1.1 206 Partial Content');
    header('Accept-Ranges: bytes');
    header('Content-Length: ' . ($ranges[1] - $ranges[0]+1)); // The size of the range

    // Send the ranges we offered
    header(
        sprintf(
            'Content-Range: bytes %d-%d/%d', // The header format
            $ranges[0], // The start range
            $ranges[1], // The end range
            $filesize // Total size of the file
        )
    );

    header('Access-Control-Allow-Origin: *');

    ob_clean();

    // It's time to output the file
    $f = fopen($src_file, 'rb'); // Open the file in binary mode
    $chunkSize = 8192; // The size of each chunk to output

    // Seek to the requested start range
    fseek($f, $ranges[0]);

    // Start outputting the data
    while (true) {
        // Check if we have outputted all the data requested
        if (ftell($f) >= $ranges[1]) {
            break;
        }

        // Output the data
        echo fread($f, $chunkSize);

        // Flush the buffer immediately
        @ob_flush();
        flush();
    }
    fclose($f);
    exit(0);
}
