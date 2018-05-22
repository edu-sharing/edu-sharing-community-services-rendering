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
    private $responseView;
    private $responseBinaries;

    /**
     *
     * @param string $Url
     */
    public function __construct($url, $proxy = '', $apiKey) {
        $this->url = $url;
        $this->proxy = $proxy;
        $this->apiKey = $apiKey;
        $this->repoUrl = '';
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
     */
    public function postRetrieveObjectProperties(EsApplication &$remote_rep, &$app_id,ESContentNode &$contentNode, &$course_id, &$resource_id, &$username) {
        $this -> repoUrl = str_replace('services/usage2?wsdl', '', $remote_rep->prop_array['usagewebservice_wsdl']);
        $logger = $this->getLogger();
        if($contentNode->getProperty('{http://www.campuscontent.de/model/1.0}remoterepositorytype') === 'DDB') {
            $logger->info('remoterepositorytype = DDB, start using plugin');
            $id = $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}remotenodeid');
            $this->responseView = $this->callApi($contentNode, '/items/'.$id.'/view');
            $prop = new stdClass();
            $prop -> key = '{http://www.campuscontent.de/model/1.0}wwwurl';
            $prop -> value = $this->responseView  -> item -> origin;
            $contentNode -> setProperties(array($prop));
            $this->responseBinaries = $this->callApi($contentNode, '/items/'.$id.'/binaries');
            $binary = $this->url . $this->responseBinaries -> binary[0] -> {'@path'};
            $b64image = base64_encode(file_get_contents($binary . '?oauth_consumer_key=' . $this->apiKey));
            Config::set('base64Preview', 'data:image/jpg;base64,'.$b64image);
            Config::set('urlEmbedding', $this->getEmbedding($contentNode));
        }
    }

    public function getEmbedding($contentNode) {

        global $Locale, $Translate;

        $wwwUrl = $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}wwwurl');

        $Message = new Phools_Message_Default('jumpToDataProvider :dataProvider', array(new Phools_Message_Param_String(':dataProvider', $this->responseView ->item->institution->name)));

        if(strpos($wwwUrl, 'av.getinfo.de') !== false) {
            if($_REQUEST['display'] === 'inline')
                return '<div style="display: inline-block"><iframe width="800" height="450" scrolling="no" src="//av.tib.eu/player/'.array_pop(explode('/', $wwwUrl)).'" frameborder="0" allowfullscreen></iframe>
                    <br/><img src="'.$this->repoUrl.'assets/images/sources/ddb.png"><span class="ddb_title">'.utf8_encode($this->responseView ->item->title).'</span>
                    <br/><a target="_blank" href="'.$wwwUrl.'"> ' . utf8_encode($Message -> localize($Locale, $Translate)) .'</a></div>';
            else
                return '<iframe style="display: block; margin: auto;" width="800" height="450" scrolling="no" src="//av.tib.eu/player/'.array_pop(explode('/', $wwwUrl)).'" frameborder="0" allowfullscreen></iframe>';

        }


        if($_REQUEST['display'] === 'inline')
            return '<div style="display: inline-block"><img src="'.Config::get('base64Preview').'">
                    <br/><img src="'.$this->repoUrl.'assets/images/sources/ddb.png"><span class="ddb_title">'.utf8_encode($this->responseView ->item->title).'</span>
                    <br/><a target="_blank" href="'.$wwwUrl.'"> ' . utf8_encode($Message -> localize($Locale, $Translate)).'</a></div>';

        return '';
    }

    protected function callApi($contentNode, $path) {
        $logger = $this->getLogger();
        try {
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
            $httpcode = curl_getinfo($curlhandle, CURLINFO_HTTP_CODE);
            if($resp === false || $httpcode > 200) {
                $logger -> error(serialize($resp));
                throw new \Exception('API request error');
            }
        } catch(\Exception $e) {
            throw new Exception($e -> getMessage());
        }
        return json_decode($resp);
    }
}
