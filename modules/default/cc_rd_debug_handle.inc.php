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

function cc_rd_log_die($err_msg)
{
	$arr = array('GET' => &$_GET, 'POST' => &$_POST, 'COOKIE' => &$_COOKIE);
	if ( !empty($_SESSION) )
	{
		$arr['SESSION'] = &$_SESSION;
	}
	cc_rd_write_log(print_r($arr, true)."\n".$err_msg);
  
	die(ob_get_clean().$err_msg);
}

function cc_rd_log_buffer($buffer)
{
	if (empty($buffer)) { return; }
	cc_rd_write_log("BUFFER:\n".$buffer);
}

function cc_rd_write_log($data)
{
	if ($h = @fopen('../log/redirect.log', 'a+'))
	{
		// we have to suppress warnings and errors from date as they'll be screwing our download 
		fwrite($h, "\n//+++++ ".@date('Y-m-d H:i:s')."\n".$data."\n\n//-----\n");
		fclose($h);
	}
}
