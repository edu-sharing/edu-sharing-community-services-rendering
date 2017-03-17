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

require_once (dirname(__FILE__) . '/../../conf.inc.php');

/**
 *
 * @author shippeli
 * @version 1.0
 * @package core
 * @subpackage classes.new
 * 
 * @see https://github.com/tunapanda/h5p-standalone
 */
class mod_h5p
extends ESRender_Module_ContentNode_Abstract {


	protected function renderTemplate(array $requestData, $TemplateName, $getDefaultData = true) {
		$Logger = $this -> getLogger();
		if($getDefaultData)
			$template_data = parent::prepareRenderData($requestData);
			$template_data['title'] = (empty($title) ? $this -> _ESOBJECT -> getTitle() : $title);
			$template_data['content'] = $this -> _ESOBJECT -> getPath();// . '?' . session_name() . '=' . session_id().'&token=' . $requestData['token'];
			$Template = $this -> getTemplate();
			$rendered = $Template -> render($TemplateName, $template_data);

			return $rendered;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::createInstance()
	 */
	final public function createInstance(array $requestData) {
			
		if (!parent::createInstance($requestData)) {
			return false;
		}

		$path = str_replace('\\', '/', $this -> _ESOBJECT -> getFilePath());

		if ( ! rename($path, $path . '.zip') ) {
			return false;
		}
		
		if ( ! mkdir($path, 0744) ) {
			return false;
		}
		
		require_once(MC_LIB_PATH."File.class.php");
		if ( ! $l_zip = mc_File::factory($path.'.zip') ) {
			throw new Exception(__FILE__.'::'.__METHOD__.'('.__LINE__.')', '(failed : zip load)'.'<br>'.($zip_file));
		}
		
		$l_zip->extract($path.DIRECTORY_SEPARATOR);

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::display()
	 */
	final protected function display(array $requestData) {
		$Logger = $this -> getLogger();
		
		
		//header("Location: " . $this -> _ESOBJECT -> getPath() . '/index.html?' . session_name() . '=' . session_id().'&token=' . $requestData['token']);
		//exit();
		
		echo $this -> renderTemplate($requestData, '/module/h5p/display');

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::inline()
	 */
	protected function inline(array $requestData) {
		$Logger = $this -> getLogger();

		echo $this -> renderTemplate($requestData, '/module/h5p/inline');

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::dynamic()
	 */
	protected function dynamic(array $requestData) {
		
		die('no');
		/*$Logger = $this -> getLogger();
		$template_data['image_url'] = $this -> _ESOBJECT -> getPath() . '.jpg?' . session_name() . '=' . session_id().'&token=' . $requestData['token'];
		 
		if($requestData['dynMetadata'])
			$template_data['metadata'] = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');
			 
			$template_data['title'] = $this->_ESOBJECT->getTitle();
			echo $this -> getTemplate() -> render('/module/picture/dynamic', $template_data);
			return true;*/
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::getTimesOfUsage()
	 */
	public function getTimesOfUsage() {
		return 20;
	}


}
