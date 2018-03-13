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


		$logger = $this->getLogger();
		$logger->info('replicationsource: ' . $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsource') . ', format: ' .
		$contentNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}format') .', replicationsourceid: ' . $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsourceid'));

	if(Config::get('hasContentLicense') === false)
		return;


	/*Wahrscheinlich ist hier gar keine weitere Bedingung als ...replicationsource == 'DE.FWU' notwendig*/
    if ($contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsource') == 'DE.FWU'
		&& (strpos($contentNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}format'), 'video') !== false
		|| strpos($contentNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}format'), 'text/html') !== false
		|| strpos($contentNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}format'), 'application/zip') !== false
		|| $contentNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}format') == '')
		)  {

			$ret = $this->getTocenizedUrl($contentNode);
			if(strpos($ret, 'not found or not licensed') !== false) {
				Config::set('hasContentLicense', false);
			} else {
				$prop = new stdClass();
				$prop -> key = '{http://www.campuscontent.de/model/1.0}wwwurl';
				$prop -> value = $ret;
				$contentNode -> setProperties(array($prop));   
			}       
		}
	}
	
	protected function getTocenizedUrl($contentNode) {
          /* if ($contentNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}format')== "application/pdf")
             {
                 return $contentNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}wwwurl');
              }
*/
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
		$logger = $this->getLogger();
		$logger->info('called ' .$preUrl . ' got ' . $url);
		return $url;
	}

}
