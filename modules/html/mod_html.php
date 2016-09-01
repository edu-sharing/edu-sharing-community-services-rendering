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
include_once ('../../conf.inc.php');


/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_html
extends ESRender_Module_ContentNode_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::createInstance()
	 */
	public function createInstance(array $requestData)
	{
		if ( ! parent::createInstance($requestData) )
		{
			return false;
		}

		$extraction_path = $this->getCacheFileName();
		$zip_file = $extraction_path.'.zip';

		if ( ! rename($extraction_path, $zip_file) )
		{
			return false;
		}

		if ( ! mkdir($extraction_path, 0777) )
		{
			return false;
		}

		require_once(MC_LIB_PATH."File.class.php");
		if ( ! $l_zip = mc_File::factory($zip_file) )
		{
			throw new SoapFault(__FILE__.'::'.__METHOD__.'('.__LINE__.')', '(failed : zip load)'.'<br>'.($zip_file));
		}

		$l_list = $l_zip->extract($extraction_path.DIRECTORY_SEPARATOR);

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::display()
	 */
	protected function display(array $requestData)
	{
		$Logger = $this->getLogger();

		header('HTTP/1.1 303 See other');
		header('Location: '.$this->_ESOBJECT->getPath().'/index.html');

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::download()
	 */
	protected function download(array $requestData)
	{
		$Logger = $this->getLogger();

		header('HTTP/1.1 303 See other');
		header('Location: '.$this->_ESOBJECT->getPath().'.zip');

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::getTimesOfUsage()
	 */
	public function getTimesOfUsage()
	{
		return PHP_INT_MAX;
	}

}
