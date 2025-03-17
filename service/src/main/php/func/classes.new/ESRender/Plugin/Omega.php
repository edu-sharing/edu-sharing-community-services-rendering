<?php

/**
 *Handle Omega Materials. Not only Videos as the plugin name suggests.
 *
 *
 */
class ESRender_Plugin_Omega
    extends ESRender_Plugin_Abstract
{
    protected $url;
    protected $user;
    protected $validateUrls;
    /**
     * comma seperated list of whitelisted prefix
     */
    protected $identifierPrefixWhitelist;

    /**
     * warning: new signature!
     * all has moved to an options object!
     * $proxy is not longer defined here, but defined globally via the proxy.conf using the regex matcher for the url
     */
    public function __construct($options = [
        "url" => '',
        "user" => 'dabiplus',
        "validateUrls" => true,
        "identifierPrefixWhitelist" => ''
    ]) {
        parent::__construct($options);
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
     */
    public function postRetrieveObjectProperties(&$data) {
        $logger = $this->getLogger();
        $esObject = new ESObject($data);

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
        $hasWhitelist = isset($this->identifierPrefixWhitelist) && strlen($this->identifierPrefixWhitelist) > 0;

        if($hasWhitelist) {
            $repId = $esObject->getNodeProperty('ccm:replicationsourceid');
            $inWhitelist = false;
            foreach (explode(',', $this->identifierPrefixWhitelist) as $prefix) {
                $prefix = trim($prefix);
                if($prefix == substr($repId, 0, strlen($prefix))) {
                    $inWhitelist = true;
                    break;
                }
            }
            if(!$inWhitelist) {
                if($repId) {
                    $logger->info("Object $repId not in whitelist array, will not trigger omega api!");
                }
                return;
            }
        }

        if ($esObject->getNodeProperty('ccm:replicationsource') == 'DE.FWU' && !$hasLocalContent)  {
            $logger->info('Replicationsource: ' . $esObject->getNodeProperty('ccm:replicationsource') . ', format: ' .
                $esObject->getNodeProperty('cclom:format') .', replicationsourceid: ' . $esObject->getNodeProperty('ccm:replicationsourceid'));

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
        $client = GuzzleHelper::getClient();
        return $client->head($streamUrl, [
            'http_errors' => false
        ])->getStatusCode();
    }

    protected function evaluateResponse($responseString = null, $esObject) {

        if(empty($responseString)){
            throw new ESRender_Exception_Omega('API respsonse is empty');
        }

        $response = json_decode($responseString);
        if(!$response) {
            throw new ESRender_Exception_Omega('Invalid JSON-Data from Sodis API: ' . $responseString);
        }

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
        if($this->validateUrls) {
            $preExec = microtime(true);
            $status = $this->checkStatus($response->get->streamURL);
            $postExec = microtime(true);
            $this->getLogger()->info("Checking if Sodis result url is valid took " . ($postExec - $preExec) . " seconds");
            if ($status > 299)
                throw new ESRender_Exception_Omega('given streamURL is invalid', $status);
        }
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
        $client = GuzzleHelper::getClient();
        $preExec = microtime(true);
        $response = $client->get($url, [
            'headers' => [
                'User-Agent' => $_SERVER['HTTP_USER_AGENT']
            ],
            'http_errors' => false
        ]);
        $postExec = microtime(true);
        $diff = $postExec - $preExec;
        $logger->debug('Omega API request took '. $diff .' seconds');
        $logger->info('Called ' . $url . ' got ' . $response->getBody());
        return $response->getBody();
    }
}
