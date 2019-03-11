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

// load plugin config
require_once(dirname(__FILE__).'/config.php');

include_once (dirname(__FILE__).'/../../conf.inc.php');
include_once (MC_ROOT_PATH.'func/classes.new/ESRender/Module/AudioVideo/Helper.php');

/**
 * handles
 *
 * @author steffen gross / matthias hupfer / steffen hippeli
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_video
extends ESRender_Module_AudioVideo_Abstract
{

    protected $filename;

    protected function prepareRenderData($getDefaultData = true, $showMetadata = true)
    {
    	global $MC_URL;

    	$template_data = array();
    	if($getDefaultData)
            $template_data = parent::prepareRenderData($showMetadata);

        $ext = $this -> getExtensionByFormat($this->getVideoFormatByRequestingDevice());
        $template_data['ext'] = $ext;
        $template_data['url'] = array();
        foreach(array(ESRender_Module_AudioVideo_Abstract::FORMAT_VIDEO_RESOLUTIONS_S, ESRender_Module_AudioVideo_Abstract::FORMAT_VIDEO_RESOLUTIONS_M, ESRender_Module_AudioVideo_Abstract::FORMAT_VIDEO_RESOLUTIONS_L) as $resolution) {
            if(file_exists($outputFilename = $this -> getOutputFilename($this->getExtensionByFormat($ext), $resolution)))
                $template_data['url'][$resolution] = dirname($this -> esObject->getPath()) . '/' . basename($this -> getOutputFilename($ext, $resolution)) . '?' . session_name() . '=' . session_id().'&token='.Config::get('token');
        }

        $template_data['width'] = 'width: ' . mc_Request::fetch('width', 'INT', 600) . 'px';
        $template_data['videoObjectIdentifier'] = uniqid('v_');
        $template_data['logger'] = $MC_URL . '/log/scr/clientlog.php';
        $template_data['cachePath'] = urlencode($this -> getOutputFilename($ext));
        $template_data['previewUrl'] = $this -> esObject->getPreviewUrl();
        return $template_data;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_AudioVideo_Abstract::getOutputFilename()
     */
    protected function getOutputFilename($ext, $resolution = NULL) {
        $filename = $this -> getCacheFileName();
        $filename = str_replace('\\','/', $filename);
        $filename .= '_'.$resolution.'.' . $ext;
        $filename = str_replace('/',DIRECTORY_SEPARATOR, $filename);
        return $filename;
    }
    
    
    protected function getExtensionByFormat($format) {
        switch($format) {
            case self::FORMAT_VIDEO_WEBM:
                return self::FORMAT_VIDEO_WEBM_EXT;
            break;
            default:
                return self::FORMAT_VIDEO_MP4_EXT;
            break;
        }
    }

    /**
     * Helper-method to allow re-using inline-templates in inline() and
     * display().
     *
     * @param array $template_data
     * @throws Exception
     */
    protected function renderInlineTemplate(array $template_data)
    {
        $inline_template = 'module/video/inline';
        $Template = $this->getTemplate();
        return $Template->render($inline_template, $template_data);
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

        if(Config::get('showMetadata'))
    		$template_data['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');
    	$template_data['title'] = $this -> esObject->getTitle();
    	echo $this->getTemplate()->render('/module/video/dynamic', $template_data);
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

        echo $this->getTemplate()->render('/module/video/embed', $template_data);
        return true;
    }
    

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::inline()
     */
    final public function inline()
    {
    	if($_REQUEST['displayoption'] == 'min') {
    		$template_data = $this->prepareRenderData( false);
    	} else {
    		$template_data = $this->prepareRenderData();
    	}
    	
    	echo $this->renderInlineTemplate($template_data);
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::locked()
     * 
     */
    final public function locked() {
    	    	
        $template = $this->getTemplate();
        $toolkitOutput = MC_ROOT_PATH . 'log/conversion/' . $this -> esObject -> getObjectID() . $this -> esObject->getObjectVersion()  . '_' . $this -> esObject->getId() . '_' . $this-> getVideoFormatByRequestingDevice() . '_' . ESRender_Module_AudioVideo_Abstract::FORMAT_VIDEO_RESOLUTIONS_S . '.log';
        $progress = ESRender_Module_AudioVideo_Helper::getConversionProgress($toolkitOutput);
        $positionInConversionQueue = $this -> esObject->getPositionInConversionQueue($this-> getVideoFormatByRequestingDevice(), ESRender_Module_AudioVideo_Abstract::FORMAT_VIDEO_RESOLUTIONS_S);
        if(empty($progress) || is_array($progress))
            $progress = '0';
        echo $template->render('/module/video/lock', array('callback' => mc_Request::fetch('callback', 'CHAR'),
        												'authString' => 'token='.Config::get('token').'&'.session_name().'='.session_id(),
        												'progress' => $progress,
        												'positionInConversionQueue' => $positionInConversionQueue));
        return true;
    }

}

