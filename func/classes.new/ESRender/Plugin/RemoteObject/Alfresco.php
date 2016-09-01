<?php

require_once(dirname(__FILE__).'/Abstract.php');

/**
 *
 *
 *
 */
class ESRender_Plugin_RemoteObject_Alfresco
extends ESRender_Plugin_RemoteObject_Abstract
{

	/**
	 *
	 * @param string $RemoteRepositoryType
	 */
	public function __construct($RepositoryType = 'ALFRESCO')
	{
		parent::__construct($RepositoryType);
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Plugin_RemoteObject_Abstract::handleRemoteObject()
	 */
	protected function handleRemoteObject(
		$NodeId,
		$RepositoryId)
	{
		/* username decrypten */
		$handler = mcrypt_module_open('blowfish', '', 'cbc', '');
		$secretKey = ES_KEY;
		$iv= ES_IV;
		mcrypt_generic_init($handler, $secretKey, $iv);
		$decrypted = mdecrypt_generic($handler, base64_decode($username));
		mcrypt_generic_deinit($handler);
		$username = trim($decrypted);
		mcrypt_module_close($handler);

		/**
		 * Authentifizierung
		 */
		$basename ='esmain';
		$n = new EsApp();
		$n->getApp($basename);
		$hc = $n->getHomeConf();
		$remote_app = $n->getAppByID($this->repositoryId);

		try
		{
			$SoapClientParams = array();
			if ( defined('USE_HTTP_PROXY') && USE_HTTP_PROXY )
			{
				$SoapClientParams = array(
                'proxy_host' => HTTP_PROXY_HOST,
                'proxy_port' => HTTP_PROXY_PORT,
                'proxy_login' => HTTP_PROXY_USER,
                'proxy_password' => HTTP_PROXY_PASS,
				);
			}

			$client = new SoapClient(
				$remote_app->prop_array['authenticationwebservice_wsdl'],
				$SoapClientParams);

			$params = array(
						"applicationId"	=> $hc->prop_array['appid'],
						"username"		=> $username,
						"email"			=> $username,
						"ticket"		=> session_id(),
						"createUser"	=> false
			);

			$user_data = $client->authenticateByApp($params);

			$redirectUrl = $remote_app->prop_array['contenturl'].'?obj_id='.$RemoteNodeId.'&rep_id='.$RepositoryId.'&session='.$user_data->authenticateByAppReturn->ticket.'&u='.urlencode($username_enc);

			header( 'Location: '.$redirectUrl ) ;

		}
		catch (Exception $e) {
			echo "<pre>";
			var_dump($e);
			return $e;
		}

	}

}
