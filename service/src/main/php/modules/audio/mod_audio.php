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
require_once ('../../vendor/autoload.php');

/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_audio extends ESRender_Module_AudioVideo_Abstract {

    protected string $filename;

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_AudioVideo_Abstract::getOutputFilename()
     */
    protected function getOutputFilename($format = null, $resolution = NULL) {
        $filename = $this->getCacheFileName();
        return $filename .= '.' . AUDIO_FORMATS[0];
    }

    protected function prepareRenderData($getDefaultData = true, $showMetadata = true) {
    	
    	$data = array();

        if($getDefaultData){
            $data = parent::prepareRenderData($showMetadata);
            $data['css'] = true;
        }else{
            $data['css'] = false;
        }
        
        $object_url = dirname($this -> esObject->getPath()) . '/' . basename($this->getOutputFilename()) . '?' . session_name() . '=' . session_id(). '&token=' . Config::get('token');
        $data['audio_url'] = $object_url;
        $data['preview_resource_url'] = $this->esObject->getPreviewUrl() ?? "";

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
    final public function dynamic()
    {
        global $Locale, $ROOT_URI;
        $template_data = $this->prepareRenderData( false);

        $template_data['ajax_url'] =
            $ROOT_URI . 'application/esmain/index.php?'.
            'app_id=' . mc_Request::fetch('app_id', 'CHAR') .
            '&rep_id=' . mc_Request::fetch('app_id', 'CHAR').
            '&resource_id='. mc_Request::fetch('resource_id', 'CHAR').
            '&course_id='.mc_Request::fetch('course_id', 'CHAR') .
            '&display=inline' .
            '&displayoption=min' .
            '&language='.$Locale->getLanguageTwoLetters().
            '&antiCache=' . mt_rand();
        //could be achieved with jquery ajax option, but in this way we can influence, for example allow caching if resource is in conversion cue

        $template_data['authString'] = 'token='.Config::get('token').'&'.session_name().'='.session_id();

        if(Config::get('showMetadata')){
            $template_data['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');
        }
        if ($this->esObject->conversionFailed(AUDIO_FORMATS[0])){
            $template_data['error'] = 'error';
        }
        $template_data['title'] = $this -> esObject->getTitle();
        echo $this->getTemplate()->render('/module/audio/dynamic', $template_data);
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::dynamic()
     */
    final public function embed()
    {
        global $Locale, $ROOT_URI;
        $template_data = $this->prepareRenderData(true, false);
        $template_data['ajax_url'] =
            $ROOT_URI . 'application/esmain/index.php?'.
            'app_id=' . mc_Request::fetch('app_id', 'CHAR') .
            '&rep_id=' . mc_Request::fetch('app_id', 'CHAR').
            '&resource_id='. mc_Request::fetch('resource_id', 'CHAR').
            '&course_id='.mc_Request::fetch('course_id', 'CHAR') .
            '&display=inline' .
            '&displayoption=min' .
            '&language='.$Locale->getLanguageTwoLetters().
            '&antiCache=' . mt_rand();
        //could be achieved with jquery ajax option, but in this way we can influence, for example allow caching if resource is in conversion cue

        $template_data['authString'] = 'token='.Config::get('token').'&'.session_name().'='.session_id();

        echo $this->getTemplate()->render('/module/audio/embed', $template_data);
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::inline()
     */
    protected function inline() {
    	if($_REQUEST['displayoption'] == 'min') {
    		$data = $this->prepareRenderData(false);
    	} else {
    		$data = $this->prepareRenderData();
    	}

    	echo $this->renderInlineTemplate($data);
        return true;
    }
    
        /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::locked()
     * 
     */
    final public function locked() {
        $template = $this->getTemplate();
        $toolkitOutput = MC_ROOT_PATH . 'log/conversion/' . $this -> esObject -> getObjectID() . $this -> esObject->getObjectVersion() . AUDIO_FORMATS[0] .'.log';
        $progress = ESRender_Module_AudioVideo_Helper::getConversionProgress($toolkitOutput);
        $positionInConversionQueue = $this -> esObject->getPositionInConversionQueue(AUDIO_FORMATS[0]);
        if(empty($progress) || is_array($progress))
            $progress = '0';
        $id = uniqid();
        $callback = mc_Request::fetch('callback', 'CHAR');
        $_SESSION["mod_audio"][$id]=[
            "callback"   => $callback,
            "authString" => 'token='.Config::get('token').'&'.session_name().'='.session_id(),
            "timeOut"    => 5000
        ];
        echo $template->render('/module/audio/lock',
            [
                "callback" => true,
                'progress' => $progress,
                'positionInConversionQueue' => $positionInConversionQueue,
                'customId' => $id
            ]
        );
        return true;
    }

}
