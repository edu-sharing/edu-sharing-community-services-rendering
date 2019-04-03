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
    public function postRetrieveObjectProperties(EsApplication &$remote_rep, &$app_id,ESContentNode &$contentNode, &$course_id, &$resource_id, &$username) {
        $logger = $this->getLogger();
        $logger->info('Replicationsource: ' . $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsource') . ', format: ' .
            $contentNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}format') .', replicationsourceid: ' . $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsourceid'));

        if(Config::get('renderInfoLMSReturn') -> hasContentLicense === false) {
            $logger->info('hasContentLicense is false');
            return;
        }

        if(Config::get('renderInfoLMSReturn') -> contentHash > -1) {
            $logger->info('contentHash > -1 handle as local object');
            return;
        }

        $role = 'learner';
        if(Config::get('renderInfoLMSReturn') -> eduSchoolPrimaryAffiliation === 'teacher') {
            $role = 'teacher';
        }

        if ($contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsource') == 'DE.FWU')  {

            if($contentNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}format') == '')
                $logger->info('Format is empty!');

            $response = $this->callAPI($contentNode, $role);
            $response = $this->evaluateResponse($response, $contentNode);
            $prop = new stdClass();
            $prop -> key = '{http://www.campuscontent.de/model/1.0}wwwurl';
            $prop -> value = urldecode($response -> get -> streamURL);
            $contentNode -> setProperties(array($prop));
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

    protected function evaluateResponse($response = null, $contentNode) {

        global $MC_URL;

        if(empty($response))
            throw new ESRender_Exception_Omega('API respsonse is empty');

        $response = json_decode($response);

        if($response->get->identifier !== $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsourceid'))
            throw new ESRender_Exception_Omega('Wrong identifier');

        if(!empty($response->get->error)) {
            throw new ESRender_Exception_Omega($response->get->error);
        }

        if(empty($response->get->streamURL) && empty($response -> get -> downloadURL)) {
            throw new ESRender_Exception_Omega('urls empty');
        }

        if(empty($response->get->streamURL) && !empty($response -> get -> downloadURL))
            $response->get->streamURL = $response->get->downloadURL;

        $status = $this->checkStatus($response->get->streamURL);
        if($status > 299)
            throw new ESRender_Exception_Omega('given streamURL is invalid', $status);

        if($response -> get -> downloadURL) {
            Config::set('downloadUrl', $MC_URL . '/theme/redirect.php?downloadUrl='.urlencode($response -> get -> downloadURL).
                '&filename=' . urlencode($contentNode -> getProperty('{http://www.alfresco.org/model/content/1.0}name'))
            );
        }

        return $response;
    }

    protected function callAPI($contentNode, $role) {
        $logger = $this->getLogger();
        $replicationSourceId = $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsourceid');
        if(empty($replicationSourceId)) {
            throw new ESRender_Exception_Omega('Property replicationsourceid is empty');
        }
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
        $logger->debug('Called ' . $url . ' got ' . $resp);
        return $resp;
    }
}
