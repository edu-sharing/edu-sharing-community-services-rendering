<?php

// provided parameters :
// $sub_file     : requestes file path inside of each modules directory 'files'
// $src_file     : path to real file
// $mime_type    : mime type of real file
// $file_name    : original file name
// $display_kind : type of display

header("Content-type: ".$mime_type);

switch( strtolower($display_kind) )
{
	case ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD:
		header('Content-Disposition: attachment; filename="' . $file_name . '"');
		break;

	case ESRender_Application_Interface::DISPLAY_MODE_INLINE:
	case ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC:
	case ESRender_Application_Interface::DISPLAY_MODE_EMBED:
		header('Content-Disposition: inline; filename="' . $file_name . '"');
		break;

	default:
		header('HTTP/1.1 500 Internal Server Error');
		throw new Exception('Invalid "display_kind" provided.');
}

header("Pragma: no-cache");
header("Expires: 0");
