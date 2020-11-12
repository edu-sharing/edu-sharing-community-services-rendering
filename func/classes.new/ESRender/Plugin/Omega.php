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

    /**
     *
     * @param string $Url
     */
    public function __construct($url, $proxy = '') {
        $this->url = $url;
        $this->proxy = $proxy;
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
        //if(Config::get('renderInfoLMSReturn') -> hasContentLicense === false) {
        //    $logger->info('hasContentLicense is false');
        //    return;
        //}
        /*
        if($esObject -> getContentHash() !== 0) {
            $logger->info('contentHash '.$esObject->getContentHash().' !== 0 handle as local object');
            return;
        }
        */

        $role = 'learner';
        if(Config::get('renderInfoLMSReturn') -> eduSchoolPrimaryAffiliation === 'teacher') {
            $role = 'teacher';
        }

        if ($esObject->getNodeProperty('ccm:replicationsource') == 'DE.FWU')  {

            if($esObject->getNodeProperty('cclom:format') == '')
                $logger->info('Format is empty!');

            $response = $this->callAPI($esObject, $role);
            $response = $this->evaluateResponse($response, $esObject);
            $logger->info('url is '.urldecode($response -> get -> streamURL));
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
        // @TODO: The user variable is not always the same and must be configurable!
        $url = $this->url . '?token_id=' . $replicationSourceId . '&role=' . $role . '&user=dabiplus';

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
