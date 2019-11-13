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
// provided parameters :
// $sub_file   : requestes file path inside of each modules directory 'files'
// $src_file   : path to real file
// $mime_type  : mime type of real file
// $file_name  : original file name

// TAKEN FROM: http://mobiforge.com/developing/story/content-delivery-mobile-devices
function rangeDownload_old($file) {

    $fp = @fopen($file, 'rb');

    $size = filesize($file);
    // File size
    $length = $size;
    // Content length
    $start = 0;
    // Start byte
    $end = $size - 1;
    // End byte
    // Now that we've gotten so far without errors we send the accept range header
    /* At the moment we only support single ranges.
     * Multiple ranges requires some more work to ensure it works correctly
     * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
     *
     * Multirange support annouces itself with:
     * header('Accept-Ranges: bytes');
     *
     * Multirange content must be sent with multipart/byteranges mediatype,
     * (mediatype = mimetype)
     * as well as a boundry header to indicate the various chunks of data.
     */
    header("Accept-Ranges: 0-$length");
    // header('Accept-Ranges: bytes');
    // multipart/byteranges
    // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
    if (isset($_SERVER['HTTP_RANGE'])) {

        $c_start = $start;
        $c_end = $end;
        // Extract the range string
        list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
        // Make sure the client hasn't sent us a multibyte range
        if (strpos($range, ',') !== false) {

            // (?) Shoud this be issued here, or should the first
            // range be used? Or should the header be ignored and
            // we output the whole content?
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            // (?) Echo some info to the client?
            exit ;
        }
        // If the range starts with an '-' we start from the beginning
        // If not, we forward the file pointer
        // And make sure to get the end byte if spesified
        if ($range == '-') { //fix
            // The n-number of the last bytes is requested
            $c_start = $size - substr($range, 1);
        } else {

            $range = explode('-', $range);
            $c_start = $range[0];
            $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
        }
        /* Check the range and make sure it's treated according to the specs.
         * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
         */
        // End bytes can not be larger than $end.
        $c_end = ($c_end > $end) ? $end : $c_end;
        // Validate the requested range and return an error if it's not correct.
        if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {

            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            // (?) Echo some info to the client?
            exit ;
        }
        $start = $c_start;
        $end = $c_end;
        $length = $end - $start + 1;
        // Calculate new content length
        fseek($fp, $start);
        header('HTTP/1.1 206 Partial Content');
    }

    // Notify the client the byte range we'll be outputting
    header("Content-Range: bytes $start-$end/$size");
    header("Content-Length: $length");

    // Start buffered download
    $buffer = 1024 * 8;
    while (!feof($fp) && ($p = ftell($fp)) <= $end) {

        if ($p + $buffer > $end) {

            // In case we're only outputtin a chunk, make sure we don't
            // read past the length
            $buffer = $end - $p + 1;
        }
        set_time_limit(0);
        // Reset time limit for big files
        echo fread($fp, $buffer);
        flush();
        // Free up memory. Otherwise large files will trigger PHP's memory limit.
    }

    fclose($fp);
}

function rangeDownload($file){
    //$fp = @fopen($file, 'rb');

    $filesize = filesize($file);

    if(isset($_SERVER['HTTP_RANGE'])) {
        // Parse the range header to get the byte offset
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
        $f = fopen($file, 'rb'); // Open the file in binary mode
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
}

header("Content-type: " . $mime_type);
//header("Content-length: " . filesize($src_file));

if($display_kind == ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD){
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
}else {
    rangeDownload($src_file);
    exit();
}

