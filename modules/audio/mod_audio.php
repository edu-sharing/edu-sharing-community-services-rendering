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
include_once (MC_ROOT_PATH.'func/classes.new/ESRender/Module/AudioVideo/Helper.php');

/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_audio extends ESRender_Module_AudioVideo_Abstract {

    protected $filename;

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_AudioVideo_Abstract::getOutputFilename()
     */
    protected function getOutputFilename($format = self::FORMAT_AUDIO_MP3) {
        $filename = $this->getCacheFileName();
        return $filename .= '.' . $this->getExtensionByFormat($format);
    }

    protected function getExtensionByFormat($format = self::FORMAT_AUDIO_MP3) {
        return self::FORMAT_AUDIO_MP3_EXT;
    }

    protected function prepareRenderData(ESObject $ESObject, $getDefaultData = true) {
    	
    	$data = array();
    	
    	if($getDefaultData)
        	$data = parent::prepareRenderData($ESObject);
        
        $object_url = dirname($this->_ESOBJECT->getPath()) . '/' . basename($this->getOutputFilename($this)) . '?' . session_name() . '=' . session_id(). '&token=' . Config::get('token');
        $data['audio_url'] = $object_url;
        return $data;
    }

    /**
     * Helper-method to allow re-using inline-templates in inline() and
     * display().
     *
     * @param array $data
     * @throws Exception
     */
    protected function renderInlineTemplate(array $data) {
        $Template = $this->getTemplate();
        return $Template->render('/module/audio/inline', $data);
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::dynamic()
     */
    final public function dynamic(
    		ESObject $ESObject)
    {
    	global $Locale, $ROOT_URI;
    	$data = $this->prepareRenderData($ESObject);
    	//$data['inline'] = $this->renderInlineTemplate($data);
    	$data['ajax_url'] = $ROOT_URI . 'application/esmain/index.php?'.'app_id='.$requestData['app_id']
    			.'&rep_id='.$requestData['rep_id'].'&obj_id='.$requestData['object_id'].'&resource_id='
    					.$requestData['resource_id'].'&course_id='.$requestData['course_id'].'&version='.$requestData['version']
    					.'&display=inline&displayoption=min&language='.$Locale->getLanguageTwoLetters().'&u='.urlencode($requestData['user_name_encr']).'&antiCache=' . mt_rand();
    					//could be achieved with jquery ajax option, but in this way we can influence, for example allow caching if resource is in conversion cue
    	$data['authString'] = 'token='.Config::get('token').'&'.session_name().'='.session_id();
    	if(Config::get('showMetadata'))
            $data['metadata'] = $this -> _ESOBJECT -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');

        $data['title'] = $this->_ESOBJECT->getTitle();
    	echo $this->getTemplate()->render('/module/audio/dynamic', $data);
    	
    	return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::inline()
     */
    protected function inline(ESObject $ESObject) {
    	if($_REQUEST['displayoption'] == 'min') {
    		$data = $this->prepareRenderData($ESObject, false);
    	} else {
    		$data = $this->prepareRenderData($ESObject);
    	}

    	echo $this->renderInlineTemplate($data);
        return true;
    }
    
        /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::locked()
     * 
     */
    final public function locked(ESObject $ESObject) {
        $template = $this->getTemplate();
        $toolkitOutput = MC_ROOT_PATH . 'log/conversion/' . $this -> _ESOBJECT -> getObjectID() . $this->_ESOBJECT->getObjectVersion() . self::FORMAT_AUDIO_MP3 .'.log';
        $progress = ESRender_Module_AudioVideo_Helper::getConversionProgress($toolkitOutput, self::FORMAT_AUDIO_MP3);
        $positionInConversionQueue = $this->_ESOBJECT->getPositionInConversionQueue(self::FORMAT_AUDIO_MP3);
        if(empty($progress) || is_array($progress))
            $progress = '0';
        echo $template->render('/module/audio/lock', array('callback' => mc_Request::fetch('callback', 'CHAR'),
        											'authString' => 'token='.Config::get('token').'&'.session_name().'='.session_id(),
        											'progress' => $progress,
        											'positionInConversionQueue' => $positionInConversionQueue));
        return true;
    }

}
