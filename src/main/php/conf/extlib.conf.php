<?php
/*
* $McLicense$
*
* $Id: extlib.conf.php 758 2011-07-27 13:53:47Z gross $
*
*/


# EXTERNAL LIBRARIES

// set include paths
$strIncludePath = implode(PATH_SEPARATOR, array(

	MC_BASE_DIR . 'func/extern/'

));
$strIncludePath = str_replace('/', DIRECTORY_SEPARATOR, $strIncludePath);

if ( ! @set_include_path($strIncludePath) )
{
	echo "unable to set include path!<br>";
	echo "1. please register path '{$strIncludePath}' manually as include path<br>";
	echo "2. disable <i>set_include_path</i> in file ".__FILE__;
	exit(0);
}

