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

    protected function prepareRenderData(
        array $requestData, $getDefaultData = true)
    {
    	global $MC_URL;

    	
    	$template_data = array();
    	
    	if($getDefaultData)
        	$template_data = parent::prepareRenderData($requestData);
        
        $ext = $this -> getExtensionByFormat($this->getVideoFormatByRequestingDevice());
        $object_url = dirname($this -> _ESOBJECT->getPath()) . '/' . basename($this -> getOutputFilename($ext)) . '?' . session_name() . '=' . session_id();
        $template_data['ext'] = $ext;
        $template_data['url'] = $object_url;
        if(!empty($requestData['width']))
            $template_data['width'] = 'width: ' . $requestData['width'] . 'px';
        $template_data['videoObjectIdentifier'] = uniqid('v_');
        $template_data['logger'] = $MC_URL . '/log/scr/clientlog.php';
        $template_data['cachePath'] = urlencode($this -> getOutputFilename($ext));
        return $template_data;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_AudioVideo_Abstract::getOutputFilename()
     */
    protected function getOutputFilename($ext) {
        $Logger = $this->getLogger();
        $filename = $this->getCacheFileName();
        $filename = str_replace('\\','/', $filename);
        $filename .= '.' . $ext;
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
        header('Access-Control-Allow-Origin: *');
        return $Template->render($inline_template, $template_data);
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::display()
     */
    final public function display(
        array $requestData)
    {
        global $Locale, $ROOT_URI;
        $template_data = $this->prepareRenderData($requestData);

        //load resource asynchr. with display mode inline!
        $template_data['ajax_url'] = $ROOT_URI . 'application/esmain/index.php?'.'app_id='
            .$requestData['app_id'].'&session='.$requestData['session']
            .'&rep_id='.$requestData['rep_id'].'&obj_id='.$requestData['object_id'].'&resource_id='
            .$requestData['resource_id'].'&course_id='.$requestData['course_id'].'&version='.$requestData['version']
            .'&display=inline&language='.$Locale->getLanguageTwoLetters().'&u='.urlencode($requestData['user_name_encr']).'&antiCache=' . mt_rand();
		//could be achieved with jquery ajax option, but in this way we can influence, for example allow caching if resource is in conversion cue
        $Template = $this->getTemplate();
        echo $Template->render('/module/video/display', $template_data);

        return true;
    }
    
    
    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::dynamic()
     */
    final public function dynamic(
    		array $requestData)
    {
    	global $Locale, $ROOT_URI;
    	$template_data = $this->prepareRenderData($requestData);
    
    	//load resource asynchr. with display mode inline!
    	$template_data['ajax_url'] = $ROOT_URI . 'application/esmain/index.php?'.'app_id='
    			.$requestData['app_id'].'&session='.$requestData['session']
    			.'&rep_id='.$requestData['rep_id'].'&obj_id='.$requestData['object_id'].'&resource_id='
    					.$requestData['resource_id'].'&course_id='.$requestData['course_id'].'&version='.$requestData['version']
    					.'&display=inline&displayoption=min&language='.$Locale->getLanguageTwoLetters().'&u='.urlencode($requestData['user_name_encr']).'&antiCache=' . mt_rand();
    					//could be achieved with jquery ajax option, but in this way we can influence, for example allow caching if resource is in conversion cue
    					$Template = $this->getTemplate();
    					header('Access-Control-Allow-Origin: *');
    					echo $Template->render('/module/video/dynamic', $template_data);
    
    					return true;
    }
    

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::inline()
     */
    final public function inline(
        array $requestData)
    {
    	
    	if($_REQUEST['displayoption'] == 'min') {
    		$template_data = $this->prepareRenderData($requestData, false);
    	} else {
    		$template_data = $this->prepareRenderData($requestData);
    	}
    	
    	echo $this->renderInlineTemplate($template_data);

        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::locked()
     * 
     */
    final public function locked(array $requestData) {
    	    	
        $template = $this->getTemplate();
        $toolkitOutput = MC_ROOT_PATH . 'log/conversion/' . $this -> _ESOBJECT -> getObjectID() . $this->_ESOBJECT->getObjectVersion() . $this-> getVideoFormatByRequestingDevice(). '.log';
        $progress = ESRender_Module_AudioVideo_Helper::getConversionProgress($toolkitOutput);
        $positionInConversionQueue = $this->_ESOBJECT->getPositionInConversionQueue($this-> getVideoFormatByRequestingDevice());
        if(empty($progress) || is_array($progress))
            $progress = '0';
        echo $template->render('/module/video/lock', array('callback' => $requestData['callback'], 'progress' => $progress, 'positionInConversionQueue' => $positionInConversionQueue));
        return true;
    }

}

