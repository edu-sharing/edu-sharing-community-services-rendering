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



// location of installation file
$index = 'install/install.php';

if (!file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.$index))
{
	
  include_once ('conf.inc.php');
  if (!import_metadata) 	die('edu-sharing renderService');
      header('Location: application/esmain/import_metadata.php'); exit(0);
}

die( header('Location: '.$index) );


$index_label_DE = ' Installation';
$index_label_EN = ' installation';
$label_redir_DE = 'Sie wurden nicht automatisch weitergeleitet?';
$label_redir_EN = 'You weren\'t redirected?';
$label_click_DE = 'Bitte klicken Sie hier: ';
$label_click_EN = 'Please click here: ';

echo strtr('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="expires" content="0">
<!--		<meta http-equiv="refresh" content="0;URL={index}"> -->
	</head>
	<body>
		<h2>{redir_DE}</h2>
		{click_DE}<a href="{index}">{label_DE}</a><br>
		<br>
		<hr>
		<br>
		<br>
		<h2>{redir_EN}</h2>
		{click_EN}<a href="{index}">{label_EN}</a>
	</body>
</html>', array(
	'{index}' => $index,
	'{redir_DE}' => $label_redir_DE,
	'{redir_EN}' => $label_redir_EN,
	'{click_DE}' => $label_click_DE,
	'{click_EN}' => $label_click_EN,
	'{label_DE}' => $index_label_DE,
	'{label_EN}' => $index_label_EN,
));

?>
