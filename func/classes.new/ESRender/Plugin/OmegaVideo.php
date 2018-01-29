<?php

/**
 *
*
*
*/
class ESRender_Plugin_OmegaVideo
extends ESRender_Plugin_Abstract
{
	
	private $url = '';
	private $proxy = '';

	/**
	 *
	 * @param string $Url
	 */
	public function __construct($url, $proxy = '')
	{
		$this->url = $url;
		$this->proxy = $proxy;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
	 */
	public function postRetrieveObjectProperties(
			EsApplication &$remote_rep,
        &$app_id,
        ESContentNode &$contentNode,
        &$course_id,
        &$resource_id,
        &$username)
	{
	
       if ($contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsource') == 'DE.FWU') {
            $prop = new stdClass();
            $prop -> key = '{http://www.campuscontent.de/model/1.0}wwwurl';
			$prop -> value = $this->getWwwurl($contentNode);
			$contentNode -> setProperties(array($prop));
        }
		
	}
	
	protected function getWwwurl($contentNode) {
		$preUrl = $this->url . '?id=' . $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsourceid');
		$curlhandle = curl_init($preUrl);
		curl_setopt($curlhandle, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curlhandle, CURLOPT_HEADER, 0);
		curl_setopt($curlhandle, CURLOPT_PROXY, $this->proxy);
		curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlhandle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($curlhandle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curlhandle, CURLOPT_SSL_VERIFYHOST, false);
		$url = curl_exec($curlhandle);
		return $url;
	}

}
