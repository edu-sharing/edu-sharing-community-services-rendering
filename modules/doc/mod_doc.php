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
define('DOCTYPE_ODT', 'DOCTYPE_ODT');
define('DOCTYPE_ODP', 'DOCTYPE_ODP');
define('DOCTYPE_ODS', 'DOCTYPE_ODS');
define('DOCTYPE_HTML', 'DOCTYPE_HTML');
define('DOCTYPE_TEXT', 'DOCTYPE_TEXT');
define('DOCTYPE_UNKNOWN', 'DOCTYPE_UNKNOWN');
define('VIEWER_JS_PATH', 'vendor/viewerjs/ViewerJS/index.html#');

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

    protected function renderTemplate($TemplateName, $showMetadata = true) {

        $template_data = parent::prepareRenderData($showMetadata);
        $template_data['previewUrl'] = $this -> esObject->getPreviewUrl();

        if(Config::get('hasContentLicense') === true) {

            if($this->getDoctype() == DOCTYPE_PDF) {
                $template_data['content'] = $this -> esObject -> getPath() . '?' . session_name() . '=' . session_id().'&token=' . Config::get('token');
                $template_data['url'] = $this -> esObject->getPath() . '?' . session_name() . '=' . session_id() . '&token=' . Config::get('token');
            }
            if($this->getDoctype() == DOCTYPE_ODT || $this->getDoctype() == DOCTYPE_ODS || $this->getDoctype() == DOCTYPE_ODP) {
                $urlFile=$this->_ESOBJECT->getPath() . '?' . session_name() . '=' . session_id() . '&token=' . $requestData['token'];
                $template_data['title'] =$this->_ESOBJECT->getTitle();
                $template_data['url'] = $urlFile;
                $template_data['urlEmbeded'] = $MC_URL.DIRECTORY_SEPARATOR.VIEWER_JS_PATH.$urlFile."ssss";
                $template_data['objectId'] = $this -> _ESOBJECT ->getObjectID();
            }
            if($this->getDoctype() == DOCTYPE_HTML) {
                $template_data['content'] = file_get_contents($this->getCacheFileName() . '_purified.html');
            }

            if($this->getDoctype() === DOCTYPE_TEXT) {
                $template_data['content'] = nl2br(htmlentities(file_get_contents($this->getCacheFileName())));
            }
        }

        if(Config::get('showMetadata'))
        	$template_data['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');

        $Template = $this -> getTemplate();
        $rendered = $Template -> render($TemplateName, $template_data);
        return $rendered;
    }

    public function createInstance() {
        if(Config::get('hasContentLicense') === false)
            return true;

    	if (!parent::createInstance()) {
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
			   	$Logger->info('Stored content in file "'.$this->getCacheFileName().'"_purified.html.');
	    	} catch(Exception $e) {
	    		$Logger->info('Error storing content in file "'.$this->getCacheFileName().'"_purified.html.');
	    		return false;
	    	}
    	}
        return true;
    }

    protected function getOutputFilename() {
        $Logger = $this -> getLogger();
        $filename = $this -> getCacheFileName();
        $filename = str_replace('\\', '/', $filename);
        return $filename;
    }

    final protected function dynamic() {
        if($this->getDoctype() === DOCTYPE_HTML || $this->getDoctype() === DOCTYPE_TEXT) {
            echo $this -> renderTemplate($this -> getThemeByDoctype().'dynamic');
            return true;
        }
        else if($this->getDoctype() === DOCTYPE_PDF) {
            echo $this -> renderTemplate($this -> getThemeByDoctype().'dynamic');
            return true;
        }
        else if($this->getDoctype() === DOCTYPE_ODT || $this->getDoctype() == DOCTYPE_ODS || $this->getDoctype() == DOCTYPE_ODP) {
            echo $this -> renderTemplate($requestData, $this -> getThemeByDoctype().'dynamic');
            return true;
        }else {
            return parent::dynamic();
        }
    }

    final protected function embed() {
        if($this->getDoctype() === DOCTYPE_HTML || $this->getDoctype() === DOCTYPE_TEXT) {
            echo $this -> renderTemplate($this -> getThemeByDoctype().'embed', false);
            return true;
        }
        else if($this->getDoctype() === DOCTYPE_PDF) {
            echo $this -> renderTemplate($this -> getThemeByDoctype().'embed', false);
            return true;
        }
        else return parent::embed();
    }

    /**
     * Load theme according to current doctype
     */
    protected function getThemeByDoctype() {
        if(Config::get('hasContentLicense') === false)
            return '/module/default/';
        switch($this->getDoctype()) {
            case DOCTYPE_HTML :
            case DOCTYPE_TEXT :
                return '/module/doc/html/';
                break;
            case DOCTYPE_PDF :
                return '/module/doc/pdf/';
                break;
            case DOCTYPE_ODT :
                return '/module/doc/odt/';
                break;
            case DOCTYPE_ODP :
                return '/module/doc/odp/';
                break;
            case DOCTYPE_ODS :
                return '/module/doc/ods/';
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
    	else if(strpos($this -> _ESOBJECT -> getMimeType(), 'text/plain') !== false)
            $this->doctype = DOCTYPE_TEXT;
        else if(strpos($this -> _ESOBJECT -> getMimeType(), 'application/pdf') !== false)
            $this->doctype = DOCTYPE_PDF;
        else if(strpos($this -> _ESOBJECT -> getMimeType(), 'application/vnd.sun.xml.writer') !== false || 
                strpos($this -> _ESOBJECT -> getMimeType(), 'application/vnd.oasis.opendocument.text') !== false)
            $this->doctype = DOCTYPE_ODT;
         else if(strpos($this -> _ESOBJECT -> getMimeType(), 'application/vnd.sun.xml.impress') !== false || 
                strpos($this -> _ESOBJECT -> getMimeType(), 'application/vnd.oasis.opendocument.presentation') !== false)
             $this->doctype = DOCTYPE_ODP;
        else if(strpos($this -> _ESOBJECT -> getMimeType(), 'application/vnd.sun.xml.calc') !== false || 
                strpos($this -> _ESOBJECT -> getMimeType(), 'pplication/vnd.oasis.opendocument.spreadsheet') !== false)
            $this->doctype = DOCTYPE_ODS;
        else
        	$this -> doctype = DOCTYPE_UNKNOWN;
        
        return;
    }

    /**
     * Doctype getter
     */
    protected function getDoctype() {
        if (!$this -> doctype)
            $this -> setDoctype();
        return $this -> doctype;
    }


    public function process($p_kind, $locked=null) {
        global $requestingDevice;
        $Logger = $this -> getLogger();
        if (($p_kind == ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC || $p_kind == ESRender_Application_Interface::DISPLAY_MODE_EMBED) && !$this->requestingDeviceCanRenderContent()) {
            $Logger -> debug('Set display mode to ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD as requesting device will not render ' . $this->getDoctype());
        }

        parent::process($p_kind);
        return true;
    }
    
    public function requestingDeviceCanRenderContent() {
        switch($this->getDoctype()) {
            case DOCTYPE_PDF :
                return true;
                break;
            case DOCTYPE_ODF :
                return true;
                break;
            case DOCTYPE_HTML:
            	return true;
            	break;
            case DOCTYPE_ODT:
            case DOCTYPE_ODP:
            case DOCTYPE_ODS:
                return true;
                break;
            default :
                return false;
        }
    }    

}
