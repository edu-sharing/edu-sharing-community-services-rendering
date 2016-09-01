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
 * FileTemplate
 *
 * @author [Autor]
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mc_FileTemplate
{

	var $l_content;

	function loadContentFromTemplate($p_source)
	{
		if (!file_exists($p_source))
		{
			return mc_Debug::error($p_source, "template not found");
		}

		$this->content = file_get_contents($p_source);

		return true;
	} // end method loadContentFromTemplate


	function replaceStringTokenInsideTemplate($p_replace)
	{
		if (!is_array($p_replace))
		{
			die(mc_Debug::error($p_replace, "parameter is not an array"));
		}

		if (sizeof($p_replace) == 0)
		{
			return true;
		}

		$this->content = strtr($this->content, $p_replace);

		return true;
	} // end method replaceStringTokenInsideTemplate



	function writeContentToFile($p_destination, $ignore_overwrite=true)
	{

		if (!is_dir(dirname($p_destination)))
		{
			return mc_Debug::error(dirname($p_destination), "destination directory not found");
		}

		if ($ignore_overwrite == false)
		{
			if (file_exists($p_destination))
			{
				return mc_Debug::error($p_destination, "file already exists");
			}
		}

		ob_start();
		touch($p_destination);
		$l_state = ob_get_contents();
		ob_end_clean();

		if (!empty($l_state))
		{
			return mc_Debug::error($l_state, "creating file '".$p_destination."' failed");
		}

		ob_start();
		chmod($p_destination, 0777);
		$l_state = ob_get_contents();
		ob_end_clean();

		if (!empty($l_state))
		{
			return mc_Debug::error($l_state, "setting file permissions for '".$p_destination."' failed");
		}

		$l_handle = fopen($p_destination, "w+");
		fwrite($l_handle, $this->content);
		fclose($l_handle);

		return true;

	} // end method writeContentToFile




	function createFileFromTemplate($p_source, $p_destination, $p_replace = false, $ignore_overwrite = true)
	{
		if (empty($p_source))
		{
			return mc_Debug::error(null, "source path empty");
		}

		if (empty($p_destination))
		{
			return mc_Debug::error(null, "destination path empty");
		}

		if (empty($p_replace))
		{
			$p_replace = array();
		}

		if ( !$this->loadContentFromTemplate($p_source) )
		{
			return false;
		}

		if ( !$this->replaceStringTokenInsideTemplate($p_replace) )
		{
			return false;
		}

		if ( !$this->writeContentToFile($p_destination, $ignore_overwrite) )
		{
			return false;
		}

		return true;
	} // end method createFileFromTemplate






} // end class mc_FileTemplate

?>