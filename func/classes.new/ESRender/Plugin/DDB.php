<?php

/**
 *Handle DDB Materials
 *
 *
 */
class ESRender_Plugin_DDB
    extends ESRender_Plugin_Abstract
{

    private $url = '';
    private $proxy = '';
    private $apiKey = '';

    /**
     *
     * @param string $Url
     */
    public function __construct($url, $proxy = '', $apiKey) {
        $this->url = $url;
        $this->proxy = $proxy;
        $this->apiKey = $apiKey;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
     */
    public function postRetrieveObjectProperties(EsApplication &$remote_rep, &$app_id,ESContentNode &$contentNode, &$course_id, &$resource_id, &$username) {
        $logger = $this->getLogger();
        $logger->info('DEV --------------------------------------------------------------------------- DDB');

        if($contentNode->getProperty('{http://www.campuscontent.de/model/1.0}remoterepositorytype') === 'DDB') {
            $id = $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}remotenodeid');
            $view = $this->callApi($contentNode, '/items/'.$id.'/view');
            //$response = $this->evaluateResponse($response, $contentNode);
            $prop = new stdClass();
            $prop -> key = '{http://www.campuscontent.de/model/1.0}wwwurl';
            $prop -> value = $view -> item -> origin;
            $contentNode -> setProperties(array($prop));

            $binaries = $this->callApi($contentNode, '/items/'.$id.'/binaries');
            $binary = $this->url . $binaries -> binary[0] -> {'@path'};
            $b64image = base64_encode(file_get_contents($binary . '?oauth_consumer_key=' . $this->apiKey));
            Config::set('base64Preview', 'data:image/jpg;base64,'.$b64image);
        }
    }

    protected function evaluateResponse($response = null, $contentNode) {
        $logger = $this->getLogger();

       // var_dump($response);


        //if($response -> get -> downloadURL) {
         //   Config::set('downladUrl', $response -> get -> downloadURL);
       // }

        return $response;
    }

    protected function callApi($contentNode, $path) {
        $logger = $this->getLogger();
        $url = $this->url . $path . '?oauth_consumer_key=' . $this->apiKey;
        $curlhandle = curl_init($url);
        curl_setopt($curlhandle, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curlhandle, CURLOPT_HEADER, 0);
        curl_setopt($curlhandle, CURLOPT_PROXY, $this->proxy);
        curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlhandle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($curlhandle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlhandle, CURLOPT_SSL_VERIFYHOST, false);
        $resp = curl_exec($curlhandle);
        return json_decode($resp);
    }
}
