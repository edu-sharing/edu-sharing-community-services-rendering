<?php

/**
 *
 *
 *
 */
class ESRender_Plugin_Arix
extends ESRender_Plugin_Abstract
{

	/**
	 *
	 * @param string $Url
	 */
	public function __construct($Url)
	{
		$this->setUrl($Url);
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
	 */
	public function postRetrieveObjectProperties(
		EsApplication &$remote_rep,
        ESObject &$contentNode,
		&$course_id,
		&$resource_id,
		&$username)
	{
		if ( $contentNode->getNodeProperty('ccm:replicationsource') == 'DE.EDMOND' )
		{
    		$RemoteNodeId = $contentNode->getNodeProperty('ccm:replicationsourceid');

			$this->handleRemoteObject($RemoteNodeId);
		}
	}

	/**
	 *
	 * @param string $RemoteNodeId
	 */
	protected function handleRemoteObject($RemoteNodeId)
	{
		$id = explode('.', $RemoteNodeId);
		$RemoteNodeId = $id[0].'-'.$id[1];

		$postvars = "<record identifier='".$RemoteNodeId."' template='edmondlogin' />";
		$postvars = "xmlstatement=".$postvars;

		$result = $this->postRequest($this->getUrl(), $postvars);

		$result = str_replace('<record>','',$result);
		$result = str_replace('</record>','',$result);

		echo $result;
		die();
	}

	/**
	 * Execute POST request.
	 *
	 * @param string $url
	 * @param string|array $params
	 */
	protected function postRequest($url, $params){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST      ,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS    ,$params);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL

		return curl_exec($ch);
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Url = '';

	/**
	 *
	 *
	 * @param string $Url
	 * @return ESRender_Plugin_Edmond
	 */
	public function setUrl($Url)
	{
		$this->Url = (string) $Url;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUrl()
	{
		return $this->Url;
	}

}
