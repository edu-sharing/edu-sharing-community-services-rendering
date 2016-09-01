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

require_once(MC_LIB_PATH.'File/File_Base.class.php');
require_once(MC_ROOT_PATH."func/extern/pclZip/pclzip.lib.php");


/**
 * mc_File_Zip
 *
 * @author [Autor]
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mc_File_Zip
	extends mc_File_Base
{

	private $archive;

	public function __construct ($p_fullname)
	{
		if (empty($p_fullname))
		{
			mc_Debug::error($p_fullname, 'parameter is empty! zip file objects must have a filesource.');
			return false;
		}

		parent::__construct($p_fullname);

		$this->archive = new PclZip($this->getFullName());

		return true;
	} // end constructor



	/**
	 * creates an archive with the name specified by the constructor
	 *
	 * returns a list of the files added to the archive
	 */
	public function add($p_path, $l_add_dir = '', $l_rem_dir = '')
	{
		if (!$this->isWritable())
		{
			mc_Debug::error($this->getFullName(), 'path or file is not writable or does not exist');
			return false;
		}

		if (empty($l_rem_dir))
		{
			$l_rem_dir = dirname($p_path);
		}

		if ($this->fileExists())
		{
			return $this->archive->add($p_path, $l_add_dir, $l_rem_dir);
		}

		// archive does not exist yet: create archive

		return $this->archive->create($p_path, $l_add_dir, $l_rem_dir);
	} // end method add



	/**
	 * reads and returns an object of the archive
	 */
	public function read()
	{
/*
		if (!$this->fileExists())
		{
			mc_Debug::error($this->getFullName(), 'path or file is not writable or does not exist');
			return false;
		}

		if (!$this->isReadable())
		{
			mc_Debug::error($this->getFullName(), 'path is not readable');
			return false;
		}
		$this->archive = new PclZip($this->getFullName());
*/
		return $this->archive;
	} // end method read



	/**
	 * returns a list of files contained by the archive
	 */
	public function getFileList()
	{
/*
		if (empty($this->archive))
		{
			$this->read();
		}
*/
/* PclZip
array() {
  array(10) {
    ["filename"]=>
    string(12) "compact.html"
    ["stored_filename"]=>
    string(12) "compact.html"
    ["size"]=>
    int(1715)
    ["compressed_size"]=>
    int(613)
    ["mtime"]=>
    int(1222700936)
    ["comment"]=>
    string(0) ""
    ["folder"]=>
   bool(false)
    ["index"]=>
    int(0)
    ["status"]=>
    string(2) "ok"
    ["crc"]=>
    int(1412574582)
  },
}
*/

		return $this->archive->listContent();
	} // end method getFileList



	/**
	 * extracts the archive to the specified path
	 *
	 * returns list of extracted files or false
	 */
	public function extract($p_target_path = null, $p_file_index = null)
	{
/*
		if (empty($this->archive))
		{
			$this->read();
		}
*/
		// use path of file itself if no other path is specified
		if (empty($p_target_path))
		{
			if (!$this->isWritable())
			{
				mc_Debug::error($this->getFullName(), 'extract path is not writable');
				return false;
			}
			$p_target_path = $this->getPath();
		}
		else if (!@is_writable($p_target_path))
		{
			mc_Debug::error($p_target_path, 'extract path is not writable');
			return false;
		}

		if ($p_file_index === null)
		{
			return $this->archive->extract($p_target_path);
		}

		return $this->archive->extractByIndex($p_file_index, $p_target_path);
	} // end method extract




} // end class mc_File_Zip

?>