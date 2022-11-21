<?php

/**
 *Handle Omega Materials. Not only Videos as the plugin name suggests.
 *
 *
 */
class ESRender_Plugin_Omega
    extends ESRender_Plugin_Abstract
{

    private $url = '';
    private $proxy = '';
    private $user = '';

    /**
     *
     * @param string $Url
     */
    public function __construct($url, $proxy = '', $user = 'dabiplus') {
        $this->url = $url;
        $this->proxy = $proxy;
        $this->user = $user;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
     */
    public function postRetrieveObjectProperties(&$data) {
        $logger = $this->getLogger();
        $esObject = new ESObject($data);
        $logger->info('Replicationsource: ' . $esObject->getNodeProperty('ccm:replicationsource') . ', format: ' .
            $esObject->getNodeProperty('cclom:format') .', replicationsourceid: ' . $esObject->getNodeProperty('ccm:replicationsourceid'));

        //check!
        if(Config::get('hasContentLicense') !== true) {
            $logger->info('hasContentLicense is false');
            return;
        }

        /*
        if($esObject -> getContentHash() !== 0) {
            $logger->info('contentHash '.$esObject->getContentHash().' !== 0 handle as local object');
            return;
        }
        */

        $role = 'learner';
        if($esObject->getUser()->primaryAffiliation === 'teacher') {
            $role = 'teacher';
        }
        $hasLocalContent = !$esObject->getNodeProperty('cclom:location') && $esObject->getContentHash() != null;
        if($hasLocalContent) {
            $logger->info('Object has local content, will not trigger omega api!');
        }
        if ($esObject->getNodeProperty('ccm:replicationsource') == 'DE.FWU' && !$hasLocalContent)  {

            if($esObject->getNodeProperty('cclom:format') == ''){
                $logger->info('Format is empty!');
            }

            $response = $this->callAPI($esObject, $role);
            $response = $this->evaluateResponse($response, $esObject);
            $logger->info('url is '.urldecode($response -> get -> streamURL));

            Config::set('omega', $esObject->getNodeProperty('ccm:wwwurl')); // used for button if hasContentLicence == false

            $data->node->properties->{'ccm:wwwurl'} =  urldecode($response -> get -> streamURL);

            /*
            $prop = new stdClass();
            $prop -> key = 'ccm:wwwurl';
            $prop -> value =;
            $esObject -> setProperties(array($prop));
            */
        }
    }

    protected function checkStatus($streamUrl) {
        $curlhandle = curl_init();
        curl_setopt($curlhandle, CURLOPT_URL, $streamUrl);
        curl_setopt($curlhandle, CURLOPT_HEADER, true);
        curl_setopt($curlhandle, CURLOPT_NOBODY, true);
        curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlhandle, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlhandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlhandle, CURLOPT_SSL_VERIFYHOST, false);
        curl_exec($curlhandle);
        $response = curl_getinfo($curlhandle, CURLINFO_HTTP_CODE);
        curl_close($curlhandle);
        return $response;
    }

    protected function evaluateResponse($response = null, $esObject) {

        if(empty($response)){
            throw new ESRender_Exception_Omega('API respsonse is empty');
        }

        $response = json_decode($response);

        if($response->get->identifier !== $esObject->getNodeProperty('ccm:replicationsourceid')){
            throw new ESRender_Exception_Omega('Wrong identifier');
        }

        if(!empty($response->get->error)) {
            throw new ESRender_Exception_Omega($response->get->error);
        }
        if(substr($response->get->streamURL,0,8) == 'problem:' && substr($response->get->downloadURL,0,8) == 'problem:') {
            if(strpos($response->get->downloadURL, 'no right to download') !== false) {
                throw new ESRender_Exception_Generic(ESRender_Exception_Generic::$TYPE_PERMISSIONS_MISSING);
            }
        }
        if(substr($response->get->streamURL,0,8) == 'problem:') {
            $response->get->streamURL = '';
        }
        if(substr($response->get->downloadURL,0,8) == 'problem:') {
            $response->get->downloadURL = '';
        }
        if(empty($response->get->streamURL) && !empty($response -> get -> downloadURL))
            $response->get->streamURL = $response->get->downloadURL;

        if(empty($response->get->streamURL) && empty($response -> get -> downloadURL)) {
            throw new ESRender_Exception_Omega('urls empty');
        }

        if(empty($response->get->streamURL) && !empty($response -> get -> downloadURL))
            $response->get->streamURL = $response->get->downloadURL;

        $status = $this->checkStatus(urlencode($response->get->streamURL));
        if($status > 299)
            throw new ESRender_Exception_Omega('given streamURL is invalid', $status);

        if($response -> get -> downloadURL) {
            Config::set('downloadUrl', $response -> get -> downloadURL);
        }

        return $response;
    }

    protected function callAPI($esObject, $role) {
        $logger = $this->getLogger();
        $replicationSourceId = $esObject->getNodeProperty('ccm:replicationsourceid');
        if(empty($replicationSourceId)) {
            throw new ESRender_Exception_Omega('Property replicationsourceid is empty');
        }
        $url = $this->url . '?token_id=' . $replicationSourceId . '&role=' . $role . '&user=' . $this->user;

		$curlhandle = curl_init($url);
        curl_setopt($curlhandle, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlhandle, CURLOPT_HEADER, 0);
        curl_setopt($curlhandle, CURLOPT_PROXY, $this->proxy);
        curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlhandle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curlhandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlhandle, CURLOPT_SSL_VERIFYHOST, false);
		$preExec = microtime(true);
        $resp = curl_exec($curlhandle);
		$postExec = microtime(true);
		$diff = $postExec - $preExec;
		$logger->debug('API request took '. $diff .' seconds');
        $logger->info('Called ' . $url . ' got ' . $resp);
        return $resp;
    }
}
