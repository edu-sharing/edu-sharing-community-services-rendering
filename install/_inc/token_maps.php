<?php
/*
* $McLicense$
*
* $Id$
*
*/


$TOKEN_SBASE_DIR      = addslashes($TOKEN_BASE_DIR);
$TOKEN_DOCROOT        = $_SERVER['DOCUMENT_ROOT'];

// set values depending on context (platform or configuration script)
$arrToken2Values = array(

	// e.g. for ./_tmpl/conf/db.conf.tpl => ../conf/db.conf.php
	'[[[TOKEN_DBHOST]]]' => $TOKEN_DBHOST,
	'[[[TOKEN_DBUSER]]]' => $TOKEN_DBUSER,
	'[[[TOKEN_DBPASS]]]' => $TOKEN_DBPASS,
	'[[[TOKEN_DBNAME]]]' => $TOKEN_DBNAME,
	'[[[TOKEN_DBPORT]]]' => $TOKEN_DBPORT,
	'[[[TOKEN_DBDRIVER]]]' => $TOKEN_DBDRIVER,

	// e.g. for ./_tmpl/conf/system.conf.tpl => ../conf/system.conf.php
	'[[[TOKEN_URL]]]'          => $TOKEN_URL,          // "http[s]://<host>/<base_uri>"
	'[[[TOKEN_BASE_DIR]]]'     => $TOKEN_BASE_DIR,     // e.g. /srv/www/htdocs/elearning/mc/
	'[[[TOKEN_SBASE_DIR]]]'    => $TOKEN_SBASE_DIR,
	'[[[TOKEN_DEFAULT_LANG]]]' => $TOKEN_DEFAULT_LANG,
	'[[[TOKEN_REPO_URL]]]'     => $TOKEN_REPO_URL,
	'[[[TOKEN_REPO_HOST]]]'    => $TOKEN_REPO_HOST,
	'[[[TOKEN_REPO_PORT]]]'    => $TOKEN_REPO_PORT,
	'[[[TOKEN_REPO_SCHEME]]]'  => $TOKEN_REPO_SCHEME,
	'[[[TOKEN_DATA_DIR]]]'     => $TOKEN_DATA_DIR,
	'[[[TOKEN_FFMPEG_EXEC]]]'    => $TOKEN_BASE_DIR . 'vendor/lib/converter/ffmpeg',
	'[[[TOKEN_DOCROOT]]]'      => $TOKEN_DOCROOT,
	'[[[TOKEN_PRIVATE_KEY]]]' => $TOKEN_PRIVATE_KEY,
	'[[[TOKEN_PUBLIC_KEY]]]' => $TOKEN_PUBLIC_KEY,
	'[[[TOKEN_SCHEME]]]' => $TOKEN_SCHEME,
	'[[[TOKEN_HOST]]]' => $TOKEN_HOST,
	'[[[TOKEN_PORT]]]' => $TOKEN_PORT,
	
);


$arrValues2Token = array(
	$TOKEN_URL           => '[[[TOKEN_URL]]]',          // "http[s]://<host>/<base_uri>"
	$TOKEN_BASE_DIR      => '[[[TOKEN_BASE_DIR]]]',     // e.g. /srv/www/htdocs/elearning/mc/
	$TOKEN_SBASE_DIR     => '[[[TOKEN_SBASE_DIR]]]',
	$TOKEN_DBNAME        => '[[[TOKEN_DBNAME]]]',
	$TOKEN_DBDRIVER      => '[[[TOKEN_DBDRIVER]]]',
	$TOKEN_DBPORT        => '[[[TOKEN_DBPORT]]]',
	$TOKEN_FFMPEG_EXEC   => '[[[TOKEN_SBASE_DIR]]]' . 'vendor/lib/converter/ffmpeg',
	);

