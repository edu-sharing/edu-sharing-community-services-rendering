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
class mod_qti21
extends ESRender_Module_ContentNode_Abstract
{

	private $p_kind;
	
	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::display()
	 */
	final protected function display(array $requestData)
	{
	    
        global $LanguageCode;

		$Logger = $this->getLogger();

        if (!file_exists(dirname(__FILE__).'/config.php')) {
            echo parent::display($requestData);
            return true;
            $Logger -> error('Error opening ' . dirname(__FILE__).'/config.php');
        }


		try {
            include_once __DIR__ . '/config.php';
			$m_path = $this->_ESOBJECT->getFilePath();
			$m_name = $this->_ESOBJECT->getTitle();

			$SoapClientParams = array();
			if ( defined('USE_HTTP_PROXY') && USE_HTTP_PROXY )
			{
                require_once(dirname(__FILE__) . '../../func/classes.new/Helper/ProxyHelper.php');
                $proxyHelper = new ProxyHelper($remote_rep->prop_array['renderinfoservice_wsdl']);
                $SoapClientParams = $proxyHelper -> getSoapClientParams();
			}

			$client = new SoapClient(cfg_onyx_service, $SoapClientParams);
			$file = $m_path;

			$fh = fopen($file, 'r+');
			if ( ! $fh )
			{
				$Logger->error('Error opening file "'.$file.'".');
				return false;
			}

			$contents = file_get_contents($file, false);
			if ( ! $contents )
			{
				$Logger->error('Error reading file-contents "'.$file.'".');
				return false;
			}

			$wrappedParams = new stdClass();
			$wrappedParams->uniqueId = $requestData["rep_id"]."-".$requestData["app_id"]."-".$requestData["course_id"]."-".$requestData["user_id"]."-".$requestData["tracking_id"]."-".$requestData['object_id'];

            $wrappedParams->uniqueId .= '-' . $requestData['user_name'];

			$wrappedParams->contentPackage = $contents;
			$wrappedParams->language = $LanguageCode;
			$wrappedParams->instructions = '<html><body><h1>ONYX</h1></body></html>';
			$wrappedParams->tempalteId  = 'onyxdefault';
			// @todo move to config
			$wrappedParams->serviceName =  'esrender';
			$wrappedParams->allowShowSolution = 'true';

			$result = $client->run(
				$wrappedParams->uniqueId,
				$wrappedParams->contentPackage,
				$wrappedParams->language,
				$wrappedParams->instructions,
				$wrappedParams->tempalteId,
				$wrappedParams->serviceName,
				$wrappedParams->allowShowSolution);

			$oru = cfg_onyx_runurl."?id=".urlencode($wrappedParams->uniqueId);
			$oru .= '&' . session_name() . '=' . session_id();

			if ( $Logger ) {
				$Logger->debug('Redirecting to url: "'.$oru.'"');
			}

			$Template = $this->getTemplate();
			if($this->p_kind == ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC) {
			    if(Config::get('showMetadata'))
					$metadata = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');
				
				echo $Template->render('/module/qti21/dynamic', array('oru' => $oru, 'title' => $m_name, 'metadata' => $metadata, 'previewUrl' => $this->_ESOBJECT->getPreviewUrl()));
				
			}else if ( $_SESSION['esrender']['display_kind']=='inline')
 			{
                     echo $Template->render('/module/qti21/inline', array('oru' => $oru, 'title' => $m_name));

			}else {
                     echo $Template->render('/module/qti21/display', array('oru' => $oru, 'title' => $m_name));
			}



		} catch(Exception $e){
			echo "<pre>";
			throw new SoapFault("Server", " error:".$e);
		}

		return true;
	}


	protected function inline(array $requestData)
	{
        $Logger = $this->getLogger();

        if (!file_exists(dirname(__FILE__).'/config.php')) {
            echo parent::inline($requestData);
            return true;
            $Logger -> error('Error opening QTI config');
        }

		echo $this->renderTemplate(
			$requestData,
			'/module/qti21/inline');

		return true;
	}


	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::createInstance()
	 */
	final public function createInstance(array $requestData)
	{
		if ( ! parent::createInstance($requestData) )
		{
			return false;
		}

	  	$qti_zip_path = $this->render_path.DIRECTORY_SEPARATOR.$this->filename;

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_Base::process()
	 */
	final public function process($p_kind, array $requestData) {
		$obj_module = $this -> _ESOBJECT -> getModule();
		if(empty($p_kind))
		  $p_kind = $obj_module -> getConf('defaultdisplay', $p_kind);

		  $this->p_kind = $p_kind;
		  
		switch($p_kind)
		{
			case ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD:
                $this -> download($requestData);
			break;

            case ESRender_Application_Interface::DISPLAY_MODE_INLINE:
			case ESRender_Application_Interface::DISPLAY_MODE_WINDOW:
			case ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC:
				return $this -> display($requestData);
			break;


			default:
				return false;
		}

		return true;
	}

}

