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

/**
 * Möglichkeiten Zugriff auf Exception Message:
 * 1. $e->detail->{"de.cc.ws.authentication.AuthenticationException"}->message
 * 2. $prop = "de.cc.ws.authentication.AuthenticationException";
 *    $e->detail->$prop->message
 * 3. $e->detail->{$e->detail->exceptionName}->message (zu empfehlen!!!)
 */


class ESApp
{

	/**
	 *
	 * @var unknown_type
	 */
	private $basename;

	/**
	 *
	 * @var array
	 */
	private $Conf = array();

	public function __destruct()
	{
		$this->Conf = null;
	}

	public function getApp($basename)
	{
		$configFilename = CC_CONF_PATH.$basename.'/'.CC_CONF_APPFILE;
		$applications = new EsApplications($configFilename);
		$fileList = $applications->getFileList();
		if ( ! empty($fileList) )
		{
			$_cnf_path = CC_CONF_PATH.$basename.'/';
			foreach ($fileList as $key => $val)
			{
				$application = new EsApplication($_cnf_path.$val);
				$application->readProperties();

				$this->Conf[$val]= $application;
			}
		}

		return $this->Conf;
	}

	/**
	 *
	 * @param string $app_id
	 */
	public function getAppByID($app_id)
	{
        return $this->Conf['app-' . $app_id . '.properties.xml'] ?? false;
    }

	/**
	 *
	 * Enter description here ...
	 */
	public function getHomeConf()
	{
        return $this->Conf['homeApplication.properties.xml'] ?? false;
    }

	public function setApp2Cache()
	{
		return false;
	}

	public function GetTicketbyConf($p_alf_conf,$p_home_conf) {
        throw new Exception('GetTicketbyConf() is deprecated!');
		try {

			$repository = new AlfrescoRepository($p_alf_conf->prop_array['alfresco_webservice_url']);

			$ticket = $repository->authenticate($p_alf_conf->prop_array['username'], $p_alf_conf->prop_array['password']);

			return $ticket;

		} catch (Exception $e) {
			return $e;
		}
	}

	public function GetTicketByUser($username,$useremail) {
        throw new Exception('GetTicketByUser() is deprecated!');
		$hc = $this->getHomeConf();

		$wsdl = $hc->prop_array['authenticationwebservice_wsdl'];
		try {
			$alfservice = new AlfrescoWebService($wsdl, array());

			$params = array(
				"applicationId" => $hc->prop_array['appid'],
				"username" => $username,
				"email" => $useremail,
				"ticket" => session_id(),
				"createUser" => false);

			$alfReturn = $alfservice->authenticateByApp($params);

			// got ticket... put into session and return it
			$ticket = $alfReturn->authenticateByAppReturn->ticket;
			return $ticket;

		} catch (Exception $e) {
			return $e;
		}
	}

}//eof class CCWebServiceFactory

