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

include_once(MC_LIB_PATH.'File.class.php');


/**
 * mc_File_Base
 *
 * @author [Autor]
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mc_File_Base
{
	protected $_basename;

	protected $_dirname;

	protected $_path;

	protected $_extension;

	protected $_content;

	protected $_handle;

	public function __construct ($p_fullname = null)
	{
		$this->_content = null;

		if (empty($p_fullname))
		{
			$this->_fullname  = null;
			$this->_basename  = null;
			$this->_dirname   = null;
			$this->_extension = null;

			return true;
		}

		$this->_fullname = trim($p_fullname);

		$info = mc_File::info($this->_fullname);

		$this->_basename  = $info['basename'];
		$this->_dirname   = $info['dirname'];
		$this->_extension = $info['extension'];
/*
		if ($this->exists())
		{
			$this->_content = file_get_contents($this->getFullName());
		}
		else
		{
			$this->_content = null;
		}
*/
		return true;
	} // end constructor



	public function getFullName()
	{
		return $this->_fullname;
	} // end method getFullName

	public function getName()
	{
		return $this->_basename;
	} // end method getName

	public function getPath()
	{
		return $this->_dirname;
	} // end method getPath

	public function getExtension()
	{
		return $this->_extension;
	} // end method getExtension



	public function fileExists()
	{
		return (@file_exists($this->getFullName()));
	} // end method fileExists



	public function pathExists()
	{
		return (@is_dir($this->getPath()));
	} // end method pathExists



	public function isWritable()
	{
		if ($this->fileExists())
		{
			return (@is_writable($this->getFullName()));
		}

		if ($this->pathExists())
		{
			return (@is_writable($this->getPath()));
		}

		return false;
	} // end method isWritable



	public function isReadable()
	{
		return (@is_readable($this->getFullName()));
	} // end method isReadable



/*
	private function createPath()
	{
	} // end method open

	public function open($p_mode)
	{
	} // end method open

	public function close()
	{
	} // end method open

	public function write()
	{
	} // end method open
*/




} // end class mc_File_Base

?>