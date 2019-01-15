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

include_once (dirname(__FILE__) . '/config.php');
include_once ('../../conf.inc.php');


/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_scenario
	extends ESRender_Module_ContentNode_Abstract
{

	/**
	 *
	 */
	final protected function display(ESObject $ESObject)
	{
		include_once('config.php');
/*
	  array(11) { ["estrack_app_id"]=>  string(10) "demomoodle"
				  ["estrack_rep_id"]=>  string(9) "elcontent"
				  ["estrack_lms_course_id"]=>  string(2) "47"
				  ["estrack_object_id"]=>  string(36) "acbb4307-73b8-4f9f-a307-b58243c1dc52"
				  ["estrack_name"]=>  string(9) "Test1.zip"
				  ["estrack_modul_id"]=>  string(1) "5"
				  ["estrack_modul_name"]=>  string(5) "qti21"
				  ["estrack_version"]=>  string(1) "0"
				  ["estrack_user_name"]=>  string(17) "demo@metacoon.net"
				  ["estrack_user_id"]=>  string(2) "28"
				  ["estrack_points"]=>  int(0) }
				  ["estrack_id"]=>  int(0) }

*/
		try
		{
			$m_mimeType = $this->_ESOBJECT->getMimeType();
			$m_path = $this->_ESOBJECT->getFilePath();
			$m_name = $this->_ESOBJECT->getTitle();
			$m_objectID = $this->_ESOBJECT->getObjectID();

			$SoapClientParams = array(
					'trace' => 1,
				);
			if ( defined('USE_HTTP_PROXY') && USE_HTTP_PROXY )
			{
                require_once(dirname(__FILE__) . '../../func/classes.new/Helper/ProxyHelper.php');
                $proxyHelper = new ProxyHelper(Config::get('homeRepository')->prop_array['renderinfoservice_wsdl']);
                $SoapClientParams = $proxyHelper -> getSoapClientParams();
			}

			$client = new SoapClient(cfg_scenario_auth_service, $SoapClientParams);

			unset($wrappedParams);
			global $user_data;
			global $hc;
            $wrappedParams = new stdClass();
			//$wrappedParams->courseid = $COURSE->id;
			$wrappedParams->applicationId = $hc->prop_array['appid'];
			$wrappedParams->username   = (empty($user_data->authenticateByAppReturn->userid) ? $user_data->authenticateByAppReturn->username : $user_data->authenticateByAppReturn->userid);
			$wrappedParams->email	  = $user_data->authenticateByAppReturn->username;
			$wrappedParams->ticket = session_id(); // render session
			$wrappedParams->createUser = true;
			$result = $client->authenticateByApp($wrappedParams);
			$virt_sess = $result->authenticateByAppReturn->sessionid;

		}
		catch(SoapFault $e){
			echo "<pre>";
			var_dump($e);
			die();
		}
		catch(Exception $e){
//			throw new SoapFault("Server", " error:".$e);
			echo "<pre>";
			var_dump($e);
			die();
		}

		header('Location: '.$this->_ESOBJECT->getPath().'&SID='.$virt_sess);

		return true;
	} // end method display



	/**
	 *
	 */
	final public function createInstance(ESObject $ESObject)
	{
		if ( ! parent::createInstance($ESObject) )
		{
			return false;
		}

		// pfad lesen
		// verzeichniss anlegen /jahr/monat/tag/minute/sek
		// kopieren
		//	 echo $this->_ESOBJECT->ESModule->getTmpFilepath();
		$date = date('Y:m:d:H:i:s');
		$date2 = date('Y:m:d:H:i');
		$datepath = explode(':',$date);
		$datepath2 = explode(':',$date2);

		$l_add='';

		foreach ($datepath as $path)
		{
			$l_path = getenv("DOCUMENT_ROOT").'/esrender/'.$this->_ESOBJECT->module->getTmpFilepath().$l_add;
			@mkdir($l_path);
			$l_add .= '/'.$path;
		}

		$content = $this->_ESOBJECT->getContentNode()->getNodeProperty('cm:content');
		$content->readContentToFile($l_path.'/'.$this->_ESOBJECT->getContentNode()->getNodeProperty('{http://www.alfresco.org/model/system/1.0}node-uuid'));

		$this->filename = $this->_ESOBJECT->getContentNode()->getNodeProperty('{http://www.alfresco.org/model/system/1.0}node-uuid');

		$DataArray['ESOBJECT_FILE_PATH']  = $l_path.'/'.$this->_ESOBJECT->getContentNode()->getNodeProperty('{http://www.alfresco.org/model/system/1.0}node-uuid');
//		$DataArray['ESOBJECT_PATH']	   = MC_ROOT_URI.$this->_ESOBJECT->ESModule->getTmpFilepath().'/'.implode('/',$datepath2).'/'.$this->filename;
		$DataArray['ESOBJECT_ESMODULE_ID']=  $this->_ESOBJECT->module->getModuleId();

		// get virt. session
		try
		{
			$SoapClientParams = array(
					'trace' => 1,
				);
			if ( defined('USE_HTTP_PROXY') && USE_HTTP_PROXY )
			{
                require_once(dirname(__FILE__) . '../../func/classes.new/Helper/ProxyHelper.php');
                $proxyHelper = new ProxyHelper(Config::get('homeRepository')->prop_array['renderinfoservice_wsdl']);
                $SoapClientParams = $proxyHelper -> getSoapClientParams();
			}

			$client = new SoapClient(cfg_scenario_auth_service, $SoapClientParams);

			unset($wrappedParams);
			global $user_data;
			global $hc;
            $wrappedParams = new stdClass();
			//$wrappedParams->courseid = $COURSE->id;
			$wrappedParams->applicationId = $hc->prop_array['appid'];
			$wrappedParams->username   = (empty($user_data->authenticateByAppReturn->userid) ? $user_data->authenticateByAppReturn->username : $user_data->authenticateByAppReturn->userid);
			$wrappedParams->email	  = $user_data->authenticateByAppReturn->username;
			$wrappedParams->ticket = session_id(); // render session
			$wrappedParams->createUser = true;
			$result = $client->authenticateByApp($wrappedParams);
			$virt_sess = $result->authenticateByAppReturn->sessionid;
		}
		catch(SoapFault $e){
			echo "mod_sceanrio authenticateByApp<br><pre>";
			var_dump($client);
			var_dump($e);
			die();
		}
		catch(Exception $e){
			echo "mod_sceanrio authenticateByApp<br><pre>";
//			throw new SoapFault("Server", " error:".$e);
			var_dump($client);
			var_dump($e);
			die();
		}

		// get new scenario id
		try
		{
			$SoapClientParams = array(
					'trace' => 1,
				);
			if ( defined('USE_HTTP_PROXY') && USE_HTTP_PROXY )
			{
                require_once(dirname(__FILE__) . '../../func/classes.new/Helper/ProxyHelper.php');
                $proxyHelper = new ProxyHelper(Config::get('homeRepository')->prop_array['renderinfoservice_wsdl']);
                $SoapClientParams = $proxyHelper -> getSoapClientParams();
			}

			$client2 = new SoapClient(cfg_scenarion_service, $SoapClientParams);
			unset($u);

			$fh = fopen($DataArray['ESOBJECT_FILE_PATH'], 'r+');
			if ( ! $fh )
			{
				return false;
			}

            $u = new stdClass();
			$u->content   = fread($fh, filesize($DataArray['ESOBJECT_FILE_PATH']));
			$u->courseid  = $user_data->authenticateByAppReturn->username;
			$u->sessionid = $result->authenticateByAppReturn->sessionid;
			$u->filename  = $this->filename;

			fclose($fh);

			$result = $client2->createScenario($u);

			$DataArray['ESOBJECT_PATH'] = cfg_scenario_player.'?'.$result->createScenarioReturn;
		}
		catch(SoapFault $e){
			echo "<pre>";
			var_dump($e);
			die();
		}
		catch(Exception $e){
			echo "<pre>";
//			throw new SoapFault("Server", " error:".$e);
			var_dump($e);
			die();
		}

		$this->_ESOBJECT->setData($DataArray);

		return true;
	}


	final public function process(
		$p_kind,
		ESObject $ESObject)
	{
		$m_mimeType = $this->_ESOBJECT->getMimeType();
		$m_path = $this->_ESOBJECT->getPath();
		$m_name = $this->_ESOBJECT->getTitle();

		$obj_module = $this->_ESOBJECT->getModule();
		$p_kind = $obj_module->getConf('defaultdisplay', $p_kind);

		switch( strtolower($p_kind) )
		{
			case ESRender_Application_Interface::DISPLAY_MODE_INLINE:
				return $this->inline($ESObject);
			break;

			case ESRender_Application_Interface::DISPLAY_MODE_WINDOW:
				return $this->display($ESObject);
			break;

			case ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD:
				header('Content-type: '.$m_mimeType);
				header('Content-Disposition: attachment; filename="'.$m_name.'"');
				readfile($m_path);
			break;

			default:
				$content = "news.php";
			break;
		}

		return true;
	}



	/**
	 *
	 */
	final public function setCache()
	{
		return true;
	}

}
