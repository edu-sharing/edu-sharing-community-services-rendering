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


define('MC_REQUEST_POST',   1);
define('MC_REQUEST_GET',    2);
define('MC_REQUEST_FORM',   3); // default
define('MC_REQUEST_COOKIE', 4);
define('MC_REQUEST_ALL',    7);

/*
define('MC_REQMATCH_SESSION',   '/^[0-9a-f]{32}$/');
//define('MC_REQMATCH_VIEW',      '/^V[A-Z0-9_]*$/');
define('MC_REQMATCH_VIEW',      '/^[A-Z0-9_]*$/');
*/


/**
 * Request
 *
 * @author gross
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mc_Request
{

	/**
	 *
	 */
	public static function fetch($p_var, $p_type, $p_default = null, $p_regmatch = null, $p_source = null)
	{
		$p_var  = trim($p_var);
		$p_type = strtolower($p_type);

		if (empty($p_source)) { $p_source = $GLOBALS['_MCFORM']; }


		if (!is_array($p_source))
		{
			$l_source = array();
			if ($p_source & MC_REQUEST_GET)
			{
				$l_source = array_merge($l_source, $_GET);
			}
			if ($p_source & MC_REQUEST_POST)
			{
				$l_source = array_merge($l_source, $_POST);
			}
			if ($p_source & MC_REQUEST_COOKIE)
			{
				$l_source = array_merge($l_source, $_COOKIE);
			}
			$p_source = &$l_source;
		}

		if ( !isset($p_source[$p_var]) )
		{
			switch ($p_type)
			{
				case 'array':
					return array();

				default :
					return $p_default;
			}
		}


		// perform for regular expression match
		if ($p_regmatch)
		{
			if (preg_match($p_regmatch, $p_source[$p_var], $l_match))
			{
				if (sizeof($l_match) == 0)
				{
					return $p_default;
				}
			}
			$l_value = $l_match[0];
		}
		else
		{
			$l_value = $p_source[$p_var];
		}


		// parameter type check or cast
		switch ($p_type)
		{
			case 'int':
//				if (is_int($p_source[$p_var])) { mc_Debug::error($p_source[$p_var], 'parameter is not an integer!'); }
				$l_value = intval($l_value);
				break;

/*
			case 'float':
				// ???
				break;
*/

			case 'char':
			case 'string':
				if (!is_string($l_value)) { mc_Debug::error($p_source[$p_var], 'parameter is not a string!'); }
				$l_value = $l_value;
				break;

			case 'string_lower':
				if (!is_string($l_value)) { mc_Debug::error($p_source[$p_var], 'parameter is not a string!'); }
				$l_value = strtolower($l_value);
				break;

			case 'string_upper':
				if (!is_string($l_value)) { mc_Debug::error($p_source[$p_var], 'parameter is not a string!'); }
				$l_value = strtoupper($l_value);
				break;

			case 'date':
				if (!is_array($l_value)) { mc_Debug::error($p_source[$p_var], 'parameter is not an array!'); }
				return $l_value;
				break;

			case 'array':
				if (!is_array($l_value)) { mc_Debug::error($p_source[$p_var], 'parameter is not an array!'); }
				return $l_value;
				break;

			case 'bool':
			case 'boolean':
				return !empty($l_value);

			default :
				die(mc_Debug::error($p_type, 'variable type unknown'));
		}

		return $l_value;
	}


	/**
	 *
	 */
	public static function fetchMulti($p_arr_var, $p_arr_return)
	{
		foreach ($p_arr_var as $p_var)
		{
			$p_arr_return[$l_var] = Request::fetch(
				$p_var[0],
				$p_var[1],
				(isset($p_var[2]) ? $p_var[2] : null),
				(isset($p_var[3]) ? $p_var[3] : null),
				(isset($p_var[4]) ? $p_var[4] : null)
			);
		}
		return true;
	}



} // end class mc_Request

?>