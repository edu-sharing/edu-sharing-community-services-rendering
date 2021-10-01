<?php

class RemoteObjectType {
    static $TYPE_VIDEO = 'video';
    static $TYPE_AUDIO = 'audio';
    static $TYPE_IMAGE = 'image';
    static $TYPE_H5P = 'h5p';
    static $TYPE_PREZI = 'prezi';
    static $TYPE_GENERIC = 'generic';
    public function __construct(ESObject $esObject)
    {
        $this->esObject = $esObject;
        $this->remoteRepository = $esObject -> getNode() -> remote -> repository -> repositoryType;
        $this->url = $esObject -> getNodeProperty('ccm:wwwurl');
    }
    public function isYoutube() {
        return strpos($this->url, '.youtube.com/watch?') !== FALSE ||
            strpos($this->url, 'youtu.be/') !== FALSE ||
            $this->isYoutubeRemoteObject();
    }
    public function isVimeo() {
            return strpos($this->url, 'vimeo.com') !== FALSE;
    }
    public function getType() {
        if($this->detectVideo()) {
            return RemoteObjectType::$TYPE_VIDEO;
        }
        if($this->detectAudio()) {
            return RemoteObjectType::$TYPE_AUDIO;
        }
        if($this->detectImage()) {
            return RemoteObjectType::$TYPE_IMAGE;
        }
        if($this->isH5P()) {
            return RemoteObjectType::$TYPE_H5P;
        }
        if($this->isPrezi()) {
            return RemoteObjectType::$TYPE_PREZI;
        }
        return RemoteObjectType::$TYPE_GENERIC;
    }
    private function detectAudio() {
        if(strpos($this -> esObject->getMimeType(), 'audio') !== false)
            return true;
    }

    private function detectImage() {
        if((strpos($this -> esObject->getMimeType(), '/png') !== false ||
                strpos($this -> esObject->getMimeType(), '/jpg') !== false ||
                strpos($this -> esObject->getMimeType(), '/jpeg') !== false ||
                strpos($this -> esObject->getMimeType(), '/gif') !== false) &&
            $this -> esObject -> getNodeProperty('ccm:remoterepositorytype') !== 'DDB')
            return true;
        return false;
    }
    public function isH5P(){
        return strpos($this -> url, 'h5p.org/h5p/embed') !== false;
    }

    public function isPrezi() {
        if(strpos($this -> url, 'prezi.com/view/') !== false || strpos($this -> url, 'prezi.com/embed/') !== false){
            return true;
        }
        return false;
    }

    private function detectVideo() {
        if($this->isYoutube() || $this->isVimeo()) {
            return true;
        }
        if(pathinfo($this -> url, PATHINFO_EXTENSION) === 'mp4' || pathinfo($this -> url, PATHINFO_EXTENSION) === 'webm')
            return true;

        //filter videos that are embedded in html
        if(strpos($this -> esObject->getMimeType(), 'video') !== false && strpos($this -> url, '.htm') === false && strpos($this -> url, '.php') === false)
            return true;

        return false;
    }

    private function isYoutubeRemoteObject()
    {
        return $this->remoteRepository === 'YOUTUBE';
    }


}

class DataProtectionHandler
{
    /**
     * @var Phools_Template_Script
     */
    private $Template;

    static function getConfig() {
        global $Locale;
        global $DATAPROTECTIONREGULATION_CONFIG;
        if(!$DATAPROTECTIONREGULATION_CONFIG) {
            $DATAPROTECTIONREGULATION_CONFIG = [];
        }
        return $DATAPROTECTIONREGULATION_CONFIG;
    }
    public function __construct(\Phools_Template_Script $Template, ESObject $node)
    {
        $this->template = $Template;
        $this->node = $node;
    }

    /**
     * Returns the handler details for the given node
     * May return null if no handler is needed or the privacy function is disabled
     * @param ESObject $node
     * @return null|string[]
     */
    static public function getHandlerDetails(ESObject $node) {
        global $Locale;
        if(!DataProtectionHandler::getConfig()['enabled']) {
            return null;
        }
        $url = $node->getNodeProperty('ccm:wwwurl');
        if(!$url/* || substr($url,0,5) === 'ccrep'*/) {
            return null;
        }
        $mods = DataProtectionHandler::getConfig()['modules'];

        // print_r($this->node);
        if(count($mods) !== 0 && in_array($node->getModule()->getName(), $mods) === FALSE) {
            return null;
        }
        $remoteRepository = $node -> getNode() -> remote -> repository -> repositoryType;#
        $type = new RemoteObjectType($node);
        if($type->isH5P()) {
            return [
                "name" => "H5P",
                "url" => "https://h5p.org/privacy"
            ];
        }
        if($type->isPrezi()) {
            return [
                "name" => "Prezi",
                "url" => "https://prezi.com/privacy-policy"
            ];
        }
        if($type->isYoutube()) {
            return [
                "name" => "YouTube",
                "url" => "https://policies.google.com/privacy?hl=" . $Locale->getLanguageTwoLetters()
            ];
        }
        if($type->isVimeo()) {
            return [
                "name" => "Vimeo",
                "url" => "https://vimeo.com/privacy"
            ];
        }
        foreach (DataProtectionHandler::getConfig()['urls'] as $k => $v) {
            if (preg_match($k, $url)) {
                return $v;
            }
        }
        return null;
    }
    public function handle()
    {
        $this->privacy = DataProtectionHandler::getHandlerDetails($this->node);
        if($this->privacy) {
            $type = new RemoteObjectType($this->node);
            ob_start();
            render(["skipDataProtection" => true]);
            $content = ob_get_clean();
            return $this->template->render('/data_protection/dynamic', [
                "node" => $this->node,
                "url" => $this->node -> getNodeProperty('ccm:wwwurl'),
                "content" => $content,
                "type" => $type->getType(),
                "handler" => $this->privacy
            ]);
        }
        return null;
    }

    static function detectRemoteObjectType() {

    }

}