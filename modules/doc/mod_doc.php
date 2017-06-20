<?php
/**
 * This product Copyright 2013 metaVentis GmbH.  For detailed notice,
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
 * 
 * 
 * 
 * 
 * 
 */

include_once ('../../conf.inc.php');

define('DOCTYPE_PDF', 'DOCTYPE_PDF');
define('DOCTYPE_ODF', 'DOCTYPE_ODF');
define('DOCTYPE_HTML', 'DOCTYPE_HTML');
define('DOCTYPE_UNKNOWN', 'DOCTYPE_UNKNOWN');

/**
 * This module handles documents of type pdf and odf (the basic odf formats) assigned in db 
 *
 * @author steffen hippeli
 * @version 1.1
 * @package modules
 * @subpackage doc
 */
class mod_doc
extends ESRender_Module_ContentNode_Abstract {

    private $doctype;

    /**
     * Extension: set doctype
     */    
    public function __construct($Name, ESRender_Application_Interface $RenderApplication, ESObject $p_esobject, Logger $Logger, Phools_Template_Interface $Template) {
        parent::__construct($Name, $RenderApplication, $p_esobject, $Logger, $Template);
        $this -> setDoctype();
    }

    protected function renderTemplate(array $requestData, $TemplateName) {
        $Logger = $this -> getLogger();
        $template_data = parent::prepareRenderData($requestData);
        $object_url = dirname($this -> _ESOBJECT -> getPath()) . '/' . basename($this -> getOutputFilename());
        if($this->getDoctype() == DOCTYPE_HTML)
        	$object_url .= '_purified.html';
        $object_url .= '?' . session_name() . '=' . session_id(). '&token=' . $requestData['token'];
        $template_data['path'] = $object_url;
        if($requestData['dynMetadata'])
        	$template_data['metadata'] = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');
        $Template = $this -> getTemplate();
        $rendered = $Template -> render($TemplateName, $template_data);
        return $rendered;
    }
    
    public function createInstance(array $requestData) {
    	
    	if (!parent::createInstance($requestData)) {
    		return false;
    	}
    	
    	if($this->getDoctype() == DOCTYPE_HTML) {
	    	$Logger = $this->getLogger();
	    	try {
	    		require_once __dir__ . '/../../func/extern/htmlpurifier/HTMLPurifier.standalone.php';
			   	$htmlPurifier = new HTMLPurifier();
			   	$originalHTML = file_get_contents($this->getCacheFileName());
			   	$purified = $htmlPurifier->purify($originalHTML);
			   	file_put_contents($this->getCacheFileName().'_purified.html', $purified);
			   	$Logger->info('Stored content in file "'.$cacheFile.'"_purified.html.');
			   	return true;
	    	} catch(Exception $e) {
	    		$Logger->info('Error storing content in file "'.$cacheFile.'"_purified.html.');
	    		return false;
	    	}  
    	}
    }

    protected function getOutputFilename() {
        $Logger = $this -> getLogger();
        $filename = $this -> getCacheFileName();
        $filename = str_replace('\\', '/', $filename);
        return $filename;
    }

    final protected function display(array $requestData) {
        $Logger = $this -> getLogger();
        echo $this -> renderTemplate($requestData, $this -> getThemeByDoctype().'display');
        return true;
    }
    
    final protected function dynamic(array $requestData) {
    	$Logger = $this -> getLogger();
    	echo $this -> renderTemplate($requestData, $this -> getThemeByDoctype().'dynamic');
    	return true;
    }

   
    /**
     * Load theme according to current doctype
     */
    protected function getThemeByDoctype() {
        switch($this->getDoctype()) {
        	case DOCTYPE_HTML :
        		return '/module/doc/html/';
        		break;
            case DOCTYPE_PDF :
                return '/module/doc/pdf/';
                break;
            case DOCTYPE_ODF :
                return '/module/doc/odf/';
                break;
            default :
                return '';
        }
    }

    /**
     * Set doctype
     */
    protected function setDoctype() {
        
        
    	if (strpos($this -> _ESOBJECT -> getMimeType(), 'text/html') !== false)
    		$this->doctype = DOCTYPE_HTML;
    	else
        	$this -> doctype = DOCTYPE_UNKNOWN;
        return;
        /*
        
        if (strpos($this -> _ESOBJECT -> getMimeType(), 'opendocument') !== false) {
            $this -> doctype = DOCTYPE_UNKNOWN;
            $this -> doctype = DOCTYPE_ODF;
        } else if (strpos($this -> _ESOBJECT -> getMimeType(), 'pdf') !== false) {
            $this -> doctype = DOCTYPE_PDF;
        } else {
            $this -> doctype = DOCTYPE_UNKNOWN;
        }*/
    }

    /**
     * Doctype getter
     */
    protected function getDoctype() {
        if (!$this -> doctype)
            $this -> setDoctype();
        return $this -> doctype;
    }


    public function process($p_kind, array $requestData) {
        global $requestingDevice;
        $Logger = $this -> getLogger();
        if ($p_kind == ESRender_Application_Interface::DISPLAY_MODE_WINDOW && !$this->requestingDeviceCanRenderContent()) {
            $Logger -> debug('Set display mode to ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD as requesting device will not render ' . $this->getDoctype());
            $p_kind = ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD;
        }

        parent::process($p_kind, $requestData);
        return true;
    }
    
    public function requestingDeviceCanRenderContent() {
        switch($this->getDoctype()) {
            case DOCTYPE_PDF :
                return $this -> checkPdfUserAgents();
                break;
            case DOCTYPE_ODF :
                return true;
                break;
            case DOCTYPE_HTML:
            	return true;
            	break;
            default :
                return false;
        }
    }
    
   /* public function checkPdfUserAgents() {
        global $requestingDevice;
        switch($requestingDevice -> getCapability('model_name')) {
            case 'Chrome':
            case 'Firefox':
                return true;
            break;
            case 'Internet Explorer':
                if((float)$requestingDevice -> getCapability('mobile_browser_version') > 10)
                    return true;
                else
                    return false;
                break;
            default:
                return false;
        }
    }*/

}
