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


	protected function renderTemplate(ESObject $ESObject, $TemplateName, $getDefaultData = true) {
		$Logger = $this -> getLogger();
		if($getDefaultData)
			$template_data = parent::prepareRenderData($ESObject);
			$template_data['title'] = (empty($title) ? $this -> _ESOBJECT -> getTitle() : $title);
			$template_data['content'] = $this -> _ESOBJECT -> getPath() . $this -> getContentPathSuffix();
           if($TemplateName == '/module/h5p/dynamic' && Config::get('showMetadata'))
                $template_data['metadata'] = $this -> _ESOBJECT -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');

            if($TemplateName == '/module/h5p/inline' && ENABLE_METADATA_INLINE_RENDERING) {
                $metadata = $this -> _ESOBJECT -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/inline');
                $data['metadata'] = $metadata;
            }
            $Template = $this -> getTemplate();
			$rendered = $Template -> render($TemplateName, $template_data);

			return $rendered;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::createInstance()
	 */
	final public function createInstance(ESObject $ESObject) {

        $logger = $this -> getLogger();
			
		if (!parent::createInstance($ESObject)) {
			return false;
		}

		$path = str_replace('\\', '/', $this -> _ESOBJECT -> getFilePath());

        try {
            if (!copy($path, $path . '.zip')) {
                throw new Exception('Error copying zip.');
            }
            if (!mkdir($path . $this -> getContentPathSuffix(), 0744) ) {
                throw new Exception('Error creating content folder.');
            }

            $zip = new ZipArchive;
            $res = $zip -> open($path . '.zip');
            if ($res !== true)
                throw new Exception('Error opening zip');
            $zip->extractTo($path . $this->getContentPathSuffix() . DIRECTORY_SEPARATOR);
            $zip->close();
        } catch (Exception $e) {
            $logger -> error('Error unzipping ' . $path . '.zip ');
            return false;
        }

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::inline()
	 */
	protected function inline(ESObject $ESObject) {
		echo $this -> renderTemplate($ESObject, '/module/h5p/inline');
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::dynamic()
	 */
	protected function dynamic(ESObject $ESObject) {
        echo $this -> renderTemplate($ESObject, '/module/h5p/dynamic');
        return true;
	}

	private function getContentPathSuffix() {
	    return '_content';
}


}
