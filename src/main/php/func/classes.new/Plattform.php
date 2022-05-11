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


/**
 * Plattform
 *
 * @author hupfer

 */
class Plattform
{

	/**
	 * Laufmodus der Plattform (false - normal, true - Debugmodus)
	 * @var	boolean
	 */
	public $DEBUG;


	/**
	 * Initialisiert werden die der Klasse App zugehörige Membervariablen
	 */
	public function __construct()
	{
		// DEBUG (siehe dblog.inc.php)
		$this->DEBUG = MC_DEBUG;
		return true;
	} // Ende Konstruktor


	/**
	 * plattform error handling for BWC
	 */
	public static function error($p_err, $p_query, $p_file = null, $p_line = null)
	{
		return mc_Debug::error($p_err, $p_query);
	} // end function error



	/**
	 *
	 */
	public static function isError($p_err)
	{
		/*
		 * @FIXME PHP Strict standards:  Non-static method MDB2::isError() should not be called statically -> Plattform::isError() must be dynamic too.
		 */
		if (DBMC::isError($p_err))
		{
			mc_Debug::error($p_err, $p_err->getUserInfo());
			return true;
		}

		return false;
	} // end function isError



	/**
	 *
	 */
	public static function isOk($p_err)
	{
		if (Plattform::isError($p_err))
		{
			return false;
		}

		return true;
	} // end function isOk



	/**
	 *
	 */
	public static function showUserMsg($p_msg, $p_send_header = false)
	{
		SysMsg::showMsg($p_msg, $p_send_header);
	} // end function showUserInfo


	/**
	 *
	 */
	public static function showUserInfo($p_msg, $p_send_header = false)
	{
		SysMsg::showInfo($p_msg, $p_send_header);
	} // end function showUserInfo


	/**
	 *
	 */
	public static function showUserWarning($p_msg, $p_send_header = false)
	{
		SysMsg::showWarning($p_msg, $p_send_header);
	} // end function showUserWarning




	/**
	 *
	 */
	public static function parseMultiLang($p_string, $p_str_lang)
	{

		if (substr($p_string, 0, 2) != 'a:' || substr($p_string, -2) != ';}')
		{
			return $p_string;
		}

		if (empty($p_str_lang))
		{
			mc_Debug::error($p_str_lang, 'missing language parameter');
		}

		$l_ser = @unserialize($p_string);

		if ( $l_ser === false )
		{
			mc_Debug::error($p_string, 'unserialize failed');
		}

		if ( empty($l_ser) )
		{
			return '';
		}

		if (isSet($l_ser[$p_str_lang]))
		{
			return $l_ser[$p_str_lang];
		}

		if (isSet($l_ser[$GLOBALS['DEFAULT_LANG']]))
		{
			return $l_ser[$GLOBALS['DEFAULT_LANG']];
		}

		return reset($l_ser);

	}


	/**
	 *
	 */
	public static function fetchParam($p_param_name, $p_default = null, $p_regmatch = null, $p_source = null)
	{
		if ($p_source === null)
		{
			if ( isset($_POST[$p_param_name]) )
			{
				$l_value = $_POST[$p_param_name];
			}
			else if ( isset($_GET[$p_param_name]) )
			{
				$l_value = $_GET[$p_param_name];
			}
			else
			{
				$l_value = $p_default;
			}
		}
		else
		{
			if ( isset($p_source[$p_param_name]) )
			{
				$l_value = $p_source[$p_param_name];
			}
			else
			{
				$l_value = $p_default;
			}
		}

		if (empty($p_regmatch) == false)
		{
			if ( !preg_match($p_regmatch, $l_value) )
			{
				return $p_default;
			}
		}

		return $l_value;
	}



	/**
	 *
	 */
	public static function fetchParamIntoArray(&$p_param_base, $p_param_name, $p_default = null, $p_regmatch = null)
	{

		if ( isset($_POST[$p_param_name]) )
		{
			$p_param_base[$p_param_name] = $_POST[$p_param_name];
		}
		else if ( isset($_GET[$p_param_name]) )
		{
			$p_param_base[$p_param_name] = $_GET[$p_param_name];
		}
		else
		{
			if ( !isset($p_param_base[$p_param_name]) )
			{
				$p_param_base[$p_param_name] = $p_default;
			}
		}

		if (empty($p_regmatch) == false)
		{
			if ( !preg_match($p_regmatch, $p_param_base[$p_param_name]) )
			{
				$p_param_base[$p_param_name] = $p_default;
			}
		}

		return $p_param_base[$p_param_name];

	}



} // end class Plattform

?>
