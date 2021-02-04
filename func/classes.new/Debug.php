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
 * handles debug
 *
 * @author steffen gross / matthias hupfer
 */
class mc_Debug
{

	/**
	 *
	 */
	public static function error($p_err_obj, $p_err_msg)
	{
		if (MC_DEBUG)
		{
			include_once(MC_LIB_PATH.'Debug/ErrorHandler.php');
			$l_ErrorHandler = new mc_ErrorHandler();
			$l_ErrorHandler->handleError(MC_DEBUG, $p_err_obj, $p_err_msg);
		}

//		return;
		return false;
	} // end method error



	/**
	 *
	 */
	public static function isError($p_err)
	{
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
	public static function check($p_debug, &$p_msg_prefix = '')
	{
		if ( empty($p_debug) )
		{
			return false;
		}

		if ($p_debug === true || empty($_SESSION['USER_IDENT']) )
		{
			$c = "ALL";
		}
		else
		{
			if ($_SESSION['USER_IDENT'] != $p_debug && $_SESSION['USER_IDENT'] != 1)
			{
				return false;
			}
			$c = "USER (IDENT:".$p_debug.")";
		}

		$p_msg_prefix = '<span style="font-size:10px;font-weight:bold;color:#0000A0;font-family:verdana,arial,helvetica,courier">msg visible to: '.$c.'</span>';

		return true;
	} // end method check



	/**
	 *
	 */
	public static function log($p_mixed = "debuglog(): missing parameter")
	{
		$logfile = $GLOBALS['ROOT_PATH']."error/debug/".session_id().".htm";
		if (@touch($logfile))
		{
			ob_start();
			var_dump($p_mixed);
			$dump = ob_get_contents();
			ob_end_clean();
			$log = "\n"."<pre>\n".htmlentities($dump)."</pre><p />\n";
			error_log($log, 3, $logfile);
		}

//		return;
		return $log;
	} // end method log



	/**
	 *
	 */
	public static function chat($param = "")
	{
		$l_debug = mc_Debug::check(MC_DEBUG);
		if ( $l_debug == false )
		{
			return '';
		}

		if (file_exists(MC_MODULES_PATH.'webchat/webchat.inc.php'))
		{
			$l_msg = 'admin debug ';

			$l_backtrace = debug_backtrace();
			$l_msg .= ' (from '.$l_backtrace[0]['file'].' : '.$l_backtrace[0]['line'].')<br>';

			if (is_array($param) == true)
			{
				$param = implode("<br>", $param);
			}
			$l_msg .= $param;

			require_once(MC_MODULES_PATH.'webchat/webchat.inc.php');
			require_once(MC_WEBCHAT_CLASSES."WebchatSend.php");
			$l_send = new WebchatSend();
			$l_send->saveMessage($l_msg, 1, 0, -2);

			return;
		}
	} // end method chat


} // end class mc_Debug







/**
 *
 */
function mc_dump(&$param)
{
	$l_debug = mc_Debug::check(MC_DEBUG, $l_msg_prefix);
	if ( $l_debug == false )
	{
		return '';
	}

	ob_start();

	$l_backtrace = debug_backtrace();
	echo $l_msg_prefix.' from '.$l_backtrace[0]['file'].':'.$l_backtrace[0]['line'].'<br />';

	var_dump($param);
	$content = ob_get_contents();
	ob_end_clean();

	echo '<pre style="padding:0px;margin:3px 0px;font-size:11px;">'.$content.'</pre><hr style="padding:0px;margin:0px;" />';

	return;
}



/**
 *
 */
function mc_out($param = "")
{
	$l_debug = mc_Debug::check(MC_DEBUG, $l_msg_prefix);
	if ( $l_debug == false )
	{
		return '';
	}

	ob_start();

	$l_backtrace = debug_backtrace();
	echo $l_msg_prefix.' from '.$l_backtrace[0]['file'].':'.$l_backtrace[0]['line'].'<br />';

	if (is_array($param) == true)
	{
		$param = implode("<br>", $param);
	}
	echo $param;

	$content = ob_get_contents();
	ob_end_clean();

	echo '<pre style="padding:0px;margin:3px 0px;font-size:11px;">'.$content.'</pre><hr style="padding:0px;margin:0px;" />';

	return;
}




/**
 *
 */
function show_time($t_start, $p_prefix = '')
{
	echo get_gentime($t_start, $p_prefix);
	return true;
}



/**
 *
 */
function get_gentime($t_start, $p_prefix = '')
{
$l_debug = mc_Debug::check(MC_DEBUG, $l_msg_prefix);
	if ( $l_debug == false )
	{
		return '';
	}
	$t_start = explode(" ", $t_start);
	$t_end   = explode(" ", microtime());

  return $l_msg_prefix.'<hr /><center style="font-family:verdana; font-size:9px;">'.$p_prefix.' code generated in '.str_pad(round((($t_end[1] - $t_start[1]) + ($t_end[0] - $t_start[0])), 4), 6, "0", STR_PAD_RIGHT).' sec</center>';
}


/**
 *
 */
function initTimer($name) { $GLOBALS['mc_timer_'.$name] = microtime(); }

/**
 *
 */
function getTimer($name) {
	$t_start = split(' ', $GLOBALS['mc_timer_'.$name]);
	$t_end   = split(' ', microtime());
	$t_sec  = (int)$t_end[1] - (int)$t_start[1];
	$t_msec = $t_end[0] - $t_start[0];
	return $name.' : '.($t_sec + $t_msec).'<br>from file '.__FILE__.'<br>';
}




?>