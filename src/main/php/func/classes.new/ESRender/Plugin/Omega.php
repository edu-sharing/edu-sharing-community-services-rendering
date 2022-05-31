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
     * warning: new signature!
     * all has moved to an options object!
     * $proxy is not longer defined here, but defined globally via the proxy.conf using the regex matcher for the url
     */
    public function __construct($options = [
        "url" => '',
        "user" => 'dabiplus',
        "validateUrls" => true
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

        if ($esObject->getNodeProperty('ccm:replicationsource') == 'DE.FWU')  {

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