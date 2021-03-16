<?php
/*
* $McLicense$
*
* $Id$
*
*/


class Step
{
	private $msg = array();

	function getMsg()         { return (implode('', $this->msg)); }

	function msg($p_msg)      { ob_start(); SysMsg::showMsg($p_msg);     $this->msg[] = ob_get_clean(); return false; }
	function error($p_msg)    { ob_start(); SysMsg::showError($p_msg);   $this->msg[] = ob_get_clean(); return false; }
	function warning($p_msg)  { ob_start(); SysMsg::showWarning($p_msg); $this->msg[] = ob_get_clean(); return false; }
	function info($p_msg)     { ob_start(); SysMsg::showInfo($p_msg);    $this->msg[] = ob_get_clean(); return false; }

	function check($p_post)   { return true; }
	function process($p_post) { return true; }

	function getPage($p_post, $p_step) { return ''; }

	function writelog($param = null, $value = null)
	{
		$log = array();
		$logfile = INST_PATH_TMPL.'log.php';

		if (file_exists($logfile))
		{
			include($logfile); // this should contain a new declaration of parameter $log !
			$log = unserialize($log);
		}

		if (!empty($param))
		{
			$log[$param] = $value;
		}

		$h = fopen($logfile, 'w+');
		if ( !$h )
		{
			die('can not write into directory '.INST_PATH_TMPL);
		}

		// create context check
		$deny = 'if ( ! defined("INST_PATH_TMPL") ) { die("access denied"); }';

		// create log statement
		$ser = '$log = "'.addcslashes(serialize($log), '\\$"').'";';

        // create log statement
        $dump = var_export($log, true);
        fwrite($h, "<"."?php\n\n{$deny}\n\n"."{$ser}\n\n/*{$dump}*/\n\n?".">");
        fclose($h);

		return true;
	}



	function readlog($param, $p_default = null)
	{
		$logfile = INST_PATH_TMPL.'log.php';
		if ( !file_exists($logfile) )
		{
			$this->writelog();
			return $p_default;
		}

		include($logfile);
		$log = unserialize($log);

		if (empty($log[$param]))
		{
			return $p_default;
		}

		return $log[$param];
	}



	function normalizeUri($uri)  {

		$uri = trim($uri);

		if (empty($uri) || $uri == '/') {
			return '/';
		}

		$uri = '/' . $uri . '/';

		while ( strpos($uri, '//') !== false ) {
			$uri = str_replace('//', '/', $uri);
		}

		return $uri;
	}



	function normalizeDir($dir)  {

		$dir = trim($dir);

		if ( substr($dir, -1) == DIRECTORY_SEPARATOR ) {
			return $dir;
		}

		$dir = $dir . DIRECTORY_SEPARATOR;
		return $dir;
	}



	function replace($string, $arrReplace)  {
		$pattern = array_keys($arrReplace);
		return str_replace($pattern, $arrReplace, $string);
	}


} // end class Step
