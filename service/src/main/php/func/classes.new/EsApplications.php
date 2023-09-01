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
 * handles the current  session
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class EsApplications
{

	/**
	 *
	 * @var string
	 */
	protected $conf_file;

	/**
	 *
	 * @var array
	 */
	protected $_list;

	/**
	 * @param string $p_conf_file
	 */
	public function __construct($p_conf_file)
	{
	    $this->conf_file = $p_conf_file;
	}

	/**
	 *
	 * @param string $p_filename
	 */
	final public function addFile($p_filename)
	{
		$li   = $this->getFileList();

		$li[] = $p_filename;
		$this->updateList($li);

		return true;
	}

	/**
	 *
	 * @param string $p_filename
	 */
	final public function deleteFile($p_filename)
	{
		$li = $this->getFileList();
		if ( $pos = array_search($p_filename, $li) )
		{
			unset($li[$pos]);
		}

		$this->updateList($li);

		return true;
	}

	/**
	 * @param array $p_filearray
	 * @throws Exception
	 */
	final public function updateList(array $p_filearray)
	{
		$app_str = implode(',',$p_filearray);

		$l_DOMDocument = new DOMDocument();
		if ( ! $l_DOMDocument->load($this->conf_file) )
		{
			throw new Exception('Error loading config file.');
		}

		$list = $l_DOMDocument->getElementsByTagName('entry');
		if ( 0 < $nodeList->length )
		{
			foreach ($list as $entry)
			{
				if ($entry->getAttribute("key")=="applicationfiles")
				{
					$entry->nodeValue = $app_str;
					break;
				}
			}
	    }

		$l_DOMDocument->save($this->conf_file);

		return true;
	}


	/**
	 *
	 * @return array
	 * @throws Exception
	 */
	final public function getFileList()
	{
		$l_DOMDocument = new DOMDocument();
		if ( ! $l_DOMDocument->load($this->conf_file) )
		{
			throw new Exception('Error loading config file.');
		}

		$nodeList = $l_DOMDocument->getElementsByTagName('entry');
		if ( 0 < $nodeList->length )
		{
			foreach ($nodeList as $entry)
			{
				if ($entry->getAttribute("key")=="applicationfiles" )
				{
					$app_str = $entry->nodeValue;
					break;
				}
		    }
		}

		$app_array = explode(',',$app_str);

		return $app_array;
	}

	/**
	 *
	 * @param string $path
	 * @param string $target
	 */
	final public function getHtmlList( $path, $target)
	{
		$list = $this->getFileList();

		$htmllist = '<SELECT NAME="esappconflist" onchange="var s = this.options[this.selectedIndex].text;parent.'.$target.'.location.href=\''.$path.'?sel=\'+s">';
		$htmllist .= '<option value="">-- select --</option>';

		foreach ($list as $key => $val)
		{
			$htmllist.='<option value="'.$key.'">'.$val.'</option>';
	    }

		$htmllist.='</SELECT >';

		return $htmllist;
	}

}

