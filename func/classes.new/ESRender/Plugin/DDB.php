<?php

define('API_URL_1', 'https://api.deutsche-digitale-bibliothek.de');
define('API_URL_2', 'https://iiif.deutsche-digitale-bibliothek.de/image/2');

/**
 *Handle DDB Materials
 *
 *
 */
class ESRender_Plugin_DDB
    extends ESRender_Plugin_Abstract
{

    private $apiKey = '';
    private $node;

    /**
     *
     * @param string $Url
     */
    public function __construct($apiKey) {
        $this -> apiKey = $apiKey;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
     */
    public function postRetrieveObjectProperties(EsApplication &$remote_rep, &$app_id,ESContentNode &$contentNode, &$course_id, &$resource_id, &$username) {
        $this -> iconUrl = $remote_rep->prop_array['clientprotocol'] .'://' . $remote_rep->prop_array['domain'] . ':' . $remote_rep->prop_array['clientport'] . '/edu-sharing/assets/images/sources/ddb.png';
        $logger = $this->getLogger();
        if($contentNode->getNodeProperty('ccm:remoterepositorytype') === 'DDB') {
            $logger->info('remoterepositorytype = DDB, start using plugin');
            $id = $contentNode->getNodeProperty('ccm:remotenodeid');
            
            $this -> node = $this->callApi(API_URL_1 . '/items/' . $id);
            $prop = new stdClass();
            $prop -> key = 'ccm:wwwurl';
            $prop -> value = $this -> node -> view -> item -> origin;
            $contentNode -> setProperties(array($prop));
            $ref = $this -> node -> binaries -> binary -> {'@ref'};
            $info = $this->callApi(API_URL_2 . '/' . $ref . '/info.json');
            $sizes = $info -> sizes;

            $width = $sizes[0] -> width;
            $height = $sizes[0] -> height;
            foreach($sizes as $size) {
                if($size -> width < 500 && $size -> width > 300) {
                    $width = $size -> width;
                    $height = $size -> height;
                }
            }

            $imgSrc = API_URL_2 . '/' .  $ref . '/full/!' . $width . ',' . $height . '/0/default.jpg';
            $this->getEmbedding($contentNode, $imgSrc);
        }
    }

    public function getEmbedding($contentNode, $imgSrc) {

        global $Locale, $Translate;

        $wwwUrl = $contentNode -> getNodeProperty('ccm:wwwurl');

        $Message = new Phools_Message_Default('jumpToDataProvider :dataProvider', array(new Phools_Message_Param_String(':dataProvider', utf8_decode($this -> node -> view -> item -> institution -> name))));

        /* @todo check this with current api state
        if(strpos($wwwUrl, 'av.getinfo.de') !== false) {
            if($_REQUEST['display'] === 'inline') {
                Config::set('urlEmbedding', '<iframe width="800" height="450" style="margin-bottom:-6px" scrolling="no" src="//av.tib.eu/player/' . array_pop(explode('/', $wwwUrl)) . '" frameborder="0" allowfullscreen></iframe>');
                Config::set('urlEmbeddingLicense', '<div><img src="' . $this->iconUrl . '"><span class="ddb_title">' . utf8_encode($this -> node -> view -> item -> title) . '</span>
                    <br/><a target="_blank" href="' . $wwwUrl . '"> ' . utf8_encode($Message->localize($Locale, $Translate)) . '</a></div>');
            } else {
                Config::set('urlEmbedding', '<iframe style="display: block; margin: auto;" width="800" height="450" scrolling="no" src="//av.tib.eu/player/' . array_pop(explode('/', $wwwUrl)) . '" frameborder="0" allowfullscreen></iframe>');
            }
            return;
        }*/

        if($_REQUEST['display'] === 'inline') {
            Config::set('urlEmbedding', '<div style="display: inline-block; max-width: 400px;"><img style="float:left; margin-right: 10px; margin-bottom: 5px;" src="'.$imgSrc.'"></div>');
            Config::set('licensePre', '<img style="display:inline-style;margin-right:10px;" src="'.$this->iconUrl.'">');
            Config::set('licensePost', '<br/><a target="_blank" href="'.$wwwUrl.'"> ' . utf8_encode($Message -> localize($Locale, $Translate)).'</a>');
        }
    }

    protected function callApi($path) {
        $logger = $this->getLogger();
        try {
            $url = $path . '?oauth_consumer_key=' . $this->apiKey;
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
