<?php
/*
* $McLicense$
*
* $Id$
*
*/


require_once(MCP_LIB_PATH."Conf.php");

class Log
{

	var $name;

	function Log($p_name)
	{
		$this->name = $p_name;

		return true;
	} // end Constructor


	function getLogName()
	{
		return $this->name;
	} //  end method getLogName



	function getLogFullUri()
	{
		return Conf::getLogUri().$this->name;
	} //  end method getLogFullUri

	function getLogFullPath()
	{
		return Conf::getLogPath().$this->name;
	} //  end method getLogFullPath


	function fileExists()
	{
		return ((file_exists($this->getLogFullPath())) ? true : false);
	} //  end method getLogFullPath

	function isWritable()
	{
		return ((is_writable($this->getLogFullPath())) ? true : false);
	} //  end method getLogFullPath

	function write($p_log_text)
	{
		$l_handle = fopen($this->getLogFullPath(), 'a');
		fwrite($l_handle, $p_log_text);
		fclose($l_handle);

		return true;
	} //  end method write


} // end class Log
