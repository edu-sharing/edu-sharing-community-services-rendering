<?php

/**
 * Module to handle URL's.
 *
 *
 */

class mod_url
extends ESRender_Module_NonContentNode_Abstract {

    private function getEmbedding() {
        $remoteType = new RemoteObjectType($this->esObject);
        $type = $remoteType->getType();
        if(Config::get('urlEmbedding')) {
            $embedding = Config::get('urlEmbedding');
        }else if ($this -> esObject -> isLti13ToolObject()){
            $embedding = $this->getLti13ToolEmbedding();
        }else if ($type === RemoteObjectType::$TYPE_VIDEO) {
            $embedding = $this->getVideoEmbedding();
        }else if ($type === RemoteObjectType::$TYPE_AUDIO) {
            $embedding = $this->getAudioEmbedding();
        }else if($this -> isPixabayRemoteObject()) {
            $embedding = $this->getPixabayEmbedding();
        }else if ($type === RemoteObjectType::$TYPE_IMAGE) {
            $embedding = $this->getImageEmbedding();
        }else if ($type === RemoteObjectType::$TYPE_H5P) {
            $embedding = $this->getH5PEmbedding();
        }else if ($type === RemoteObjectType::$TYPE_PREZI) {
            $embedding = $this->getPreziEmbedding();
        }
        else{
            $embedding = '';
        }
        return $embedding;
    }

    protected function dynamic() {
    	if (!$this -> validate()) {
    		return false;
    	}
        $embedding = $this->getEmbedding();

    	$Template = $this -> getTemplate();
    	$tempArray = array(
    	    'embedding' => $embedding,
            'url' => $this->getUrl(),
            'previewUrl' => $this -> esObject->getPreviewUrl()
        );
    	if(Config::get('showMetadata'))
    		$tempArray['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');
    	
    	$tempArray['title'] = $this -> esObject->getTitle();
    	echo $Template -> render('/module/url/dynamic', $tempArray);
    
    	return true;
    }

    protected function embed() {
        if (!$this -> validate()) {
            return false;
        }
        $embedding = $this->getEmbedding();

        $license = $this -> esObject->getLicense();
        if(!empty($license)) {
            $license = $license -> renderFooter($this -> getTemplate(), $this->lmsInlineHelper());
        }

        $sequence = '';
        if($this -> esObject -> getSequenceHandler() -> isSequence())
            $sequence = $this -> esObject -> getSequenceHandler() -> render($this -> getTemplate(), '/sequence/inline', $this->lmsInlineHelper());

        $metadata = '';
        if(ENABLE_METADATA_INLINE_RENDERING) {
            $metadata = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/inline');
        }

        $footer = $this->getTemplate()->render('/footer/inline', array('license' => $license, 'metadata' => $metadata, 'sequence' => $sequence, 'title' => $this -> esObject -> getTitle()));
        $Template = $this -> getTemplate();
        $tempArray = array('embedding' => $embedding, 'url' => $this->getUrl(), 'previewUrl' => $this -> esObject->getPreviewUrl(), 'footer' => $footer, 'title' => $this -> esObject -> getTitle());

        echo $Template -> render('/module/url/embed', $tempArray);

        return true;
    }
    
    protected function inline() {
        if (!$this -> validate()) {
            return false;
        }
        $license = $this -> esObject->getLicense();
        if(!empty($license)) {
            $license = $license -> renderFooter($this -> getTemplate(), $this->lmsInlineHelper());
        }

        $sequence = '';
        if($this -> esObject -> getSequenceHandler() -> isSequence())
            $sequence = $this -> esObject -> getSequenceHandler() -> render($this -> getTemplate(), '/sequence/inline', $this->lmsInlineHelper());

        $metadata = '';
        if(ENABLE_METADATA_INLINE_RENDERING) {
            $metadata = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/inline');
        }

        $footer = $this->getTemplate()->render('/footer/inline', array('license' => $license, 'metadata' => $metadata, 'sequence' => $sequence, 'title' => $this -> esObject -> getTitle()));

        $remoteType = new RemoteObjectType($this->esObject);
        $type = $remoteType->getType();

        if(Config::get('urlEmbedding')) {
            $embedding = Config::get('urlEmbedding') . $footer;
        }else if ($this -> esObject -> isLti13ToolObject()){
            $embedding = $this->getLti13ToolEmbedding();
        }else if ($type === RemoteObjectType::$TYPE_VIDEO) {
            $embedding = $this -> getVideoEmbedding(mc_Request::fetch('width', 'INT', 600), $footer);
        }else if ($type === RemoteObjectType::$TYPE_VIDEO) {
            $embedding = $this->getAudioEmbedding($footer);
        } else if($this -> isPixabayRemoteObject()) {
            $embedding = $this->getPixabayEmbedding($footer, mc_Request::fetch('width', 'INT', 600));
        }else if ($type === RemoteObjectType::$TYPE_IMAGE) {
            $embedding = $this -> getImageEmbedding($footer);
        }else if ($type === RemoteObjectType::$TYPE_H5P) {
            $embedding = $this->getH5PEmbedding($footer);
        }else if ($type === RemoteObjectType::$TYPE_PREZI) {
            $embedding = $this->getPreziEmbedding($footer);
        }else {
            $license = $this -> esObject->getLicense();
            if (!empty($license))
                $license = $license->renderFooter($this->getTemplate(), $this->getUrl());
            $embedding = $this->getTemplate()->render('/footer/inline', array('license' => $license, 'metadata' => $metadata, 'sequence' => $sequence, 'title' => $this -> esObject->getTitle(), 'url' => $this->getUrl()));
        }

        $data = array('embedding' => $embedding);
                
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/url/inline', $data);
        
        return true;
    }

    private function validate() {
        if (!$this -> getUrl() && !$this -> isYoutubeRemoteObject() && !$this -> isPixabayRemoteObject()){
            error_log('validate: false');
            return false;
        }
        return true;
    }
    
    protected function isYoutubeRemoteObject() {
        if (!empty($this -> esObject -> getNode() -> remote)){
            if($this -> esObject -> getNode() -> remote -> repository -> repositoryType  == 'YOUTUBE'){
                return true;
            }
        }
        return false;
    }

    protected function isPixabayRemoteObject() {
        if (!empty($this -> esObject -> getNode() -> remote)){
            if($this -> esObject -> getNode() -> remote -> repository -> repositoryType  == 'PIXABAY'){
                return true;
            }
        }
        return false;
    }

    protected function getLinkEmbedding() {
        $htm =  '<script> if (typeof single != "undefined") location.href="'.$this -> getUrl().'";</script>';    
        $htm .= '<a href="' . $this -> getUrl() . '" target="_blank"><es:title xmlns:es="http://edu-sharing.net/object" >' . htmlspecialchars($this -> getUrl(), ENT_QUOTES, 'UTF-8') . '</es:title></a>';
        return $htm;         
    }

    protected function getH5PEmbedding($footer = '') {
        $htm = '<div style="max-width:100%"><iframe src="'.$this->getUrl().'" width="800px" height="500px" frameborder="0" allowfullscreen="allowfullscreen"></iframe>';
        $htm .= '<script src="https://h5p.org/sites/all/modules/h5p/library/js/h5p-resizer.js" charset="UTF-8"></script>'. $footer.'</div>';
        return $htm;         
    }

    protected function getPreziEmbedding($footer = '') {
        $preziUrl = $this->getUrl();
        if(substr($preziUrl , -1)!='/'){
            $preziUrl .= '/';
        }
        if(strpos($preziUrl, '/embed') === false) {
            $preziUrl .= 'embed';
        }
        $htm = '<div style="max-width:100%"><iframe width="800" height="580" src="'.$preziUrl.'" webkitallowfullscreen="1" mozallowfullscreen="1" allowfullscreen="1"></iframe>'. $footer.'</div>';
        return $htm;
    }

    protected function getPixabayEmbedding($footer = '', $width='auto') {
        if ($width != 'auto'){
            $width .= 'px';
        }
        $htm = '<div style="min-width: 350px; width:'.$width.';"><img class="edusharing_rendering_content" title="' . $this -> esObject->getTitle() . '" alt="' . $this -> esObject->getTitle() . '" src="'. $this->esObject->getPreviewUrl() .'" style="max-width: 100%; width:'.$width.';">' . $footer . '</div>';
        return $htm;
    }

    protected function getAudioEmbedding($footer = '')
    {
        $html = '<div class="edusharing_audio_wrapper">';
        $html .= '<img alt="" class="edusharing_audio_bg" src="'. $this->esObject->getNode()->preview->url.'">';
        $html .= '<div class="edusharing_audio_img"><img alt="" src="'. $this->esObject->getNode()->preview->url.'"></div>';
        $html .= '<video style="max-width:100%" src="' . $this->getUrl() . '" type="' . $this -> esObject->getMimeType() . '" controls="controls" oncontextmenu="return false;"></video>' . $footer . '</div>';
        return $html;
    }

    protected function getImageEmbedding($footer = '')
    {
        return '<div><img title="' . $this -> esObject->getTitle() . '" alt="' . $this -> esObject->getTitle() . '" src="' . $this->getUrl() . '" style="max-width: 100%">
            ' . $footer . '</div>';
    }

    protected function getLti13ToolEmbedding($footer = ''){
        return '<script>var ltiIFrame = document.getElementById("ltiframe");ltiIFrame.height=(window.innerHeight-ltiIFrame.getBoundingClientRect().top)+"px";</script><div>
            <iframe id="ltiframe" src="'. $this->getUrl().'&editMode=false&launchPresentation=iframe" style="border:none;;max-width: 100%;width:100%;"></iframe>
            ' . $footer . '</div>';

        /**
         *   protected function getLti13ToolEmbedding($footer = ''){
        return '<div> <iframe src="'. $this->getUrl().'&editMode=false" style="max-width: 100%;width:100%;height: 100%;" onLoad="this.style.height=contentWindow.document.documentElement.scrollHeigh>
        }

         */
    }

    protected function getVideoEmbedding($width = NULL, $footer = '') {

    global $MC_URL, $Locale;

    if(empty($width)) {
        $width = 800;
    }
    $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
    //16:9
    $height = $width * 0.5625;
    $objId = $this -> esObject -> getObjectID();
    $videoWrapperInnerStyle = 'position: relative; padding-bottom: 56.25%; padding-top: 25px; height: 0;';
    $type = new RemoteObjectType($this->esObject);
    //wrappers needed to handle max width
    if($this -> isYoutubeRemoteObject()){
        $vidId = $this -> esObject -> getNode() -> remote -> id;
        $src = '';
        $src = 'www.youtube-nocookie.com/embed/' . $vidId . '?modestbranding=1';
        return '<div class="videoWrapperOuter" style="max-width:' . $width . 'px;">
                    <div class="videoWrapperInner" style="'.$videoWrapperInnerStyle.'">
                        <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;' . '" id="' . $objId . '" src="//www.youtube-nocookie.com/embed/' . $vidId . '?modestbranding=1" src="'.$src.'" frameborder="0" allowfullscreen class="embedded_video"></iframe>
                    </div>
                    '.$footer.'
                </div>';
    }
    else if ($type->isYoutube()) {
        if (strpos($this -> getUrl(), 'youtu.be/') !== false) {
            $vidId = basename($this->getUrl());
        }else{
            $parsedUrl = parse_url($this -> getUrl());
            $paramsArr = explode('&', $parsedUrl['query']);
            foreach ($paramsArr as $param) {
                $item = explode('=', $param);
                $params[$item[0]] = $item[1];
            }
            $vidId = $params['v'];
        }
        return '<div class="videoWrapperOuter" style="max-width:' . $width . 'px;">
                    <div class="videoWrapperInner" style="'.($videoWrapperInnerStyle).'">
                        <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;' . '" id="' . $objId . '" src="//www.youtube-nocookie.com/embed/' . $vidId . '?modestbranding=1" frameborder="0" allowfullscreen class="embedded_video"></iframe>
                    </div>
                    '.$footer.'
                </div>';
    }
    else if ($type->isVimeo()) {
        $urlArr = explode('/', $this -> getUrl());
        if (substr_count($this -> getUrl(), '/') == 4 ){
            $token = end($urlArr);
            $vidId = prev($urlArr).'?h='.$token;
        }else{
            $vidId = end($urlArr);
        }
        $this->dataProtection = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($this->esObject, $objId, 'Vimeo', 'https://help.vimeo.com/hc/de/sections/203915088-Datenschutz', 'player.vimeo.com', 'VIMEO');
        return '<div class="videoWrapperOuter" style="max-width:'.$width.'px;">
                    <div class="videoWrapperInner" style="'.$videoWrapperInnerStyle.'">
                        <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;' . '" id="' . $objId . '" src="//player.vimeo.com/video/' . $vidId . '" src="" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="embedded_video"></iframe>
                    </div>
                    '.$footer.'
                </div>';
    } else {
        $type = $this -> esObject->getMimeType();
        if(pathinfo($this -> getUrl(), PATHINFO_EXTENSION) === 'mp4' || pathinfo($this -> getUrl(), PATHINFO_EXTENSION) === 'webm') {
            $type = 'video/' . pathinfo($this -> getUrl(), PATHINFO_EXTENSION);
        }
        $identifier = uniqid();
        return '<div class="videoWrapperOuter" style="max-width:'.$width.'px;">
                <div id="videoWrapperInner_'.$objId.'" class="videoWrapperInner" style="position: relative; padding-top: 25px;' . '">
                    <video id="'.$identifier.'" data-tap-disabled="true" controls style="max-width: 100%;background: transparent url(\''.$this->esObject->getPreviewUrl().'\') 50% 50% / cover no-repeat;" oncontextmenu="return false;" controlsList="nodownload">
                        <source src="' . $this -> getUrl() . '" type="' . $type . '"></source>
                    </video>
                </div>
                '.$footer.'
            </div>';
        }
    }

    protected function getUrl() {

        if($this -> esObject -> isLti13ToolObject()) {
            $result = html_entity_decode($this->esObject->getData() -> nodeUrls ->generateLtiResourceLink);
            Logger::getLogger('de.metaventis.esrender.index') -> info('ltitool_start_resourcelink:'.$result);
            return $result;
        }

        $urlProp = $this -> esObject -> getNodeProperty($this -> getUrlProperty());
       if(!empty($urlProp)){
           if (is_array($urlProp)){
               $urlProp = $urlProp[0];
           }
           return html_entity_decode($urlProp);
       }
       return false;
    }


    /**
     * The object's property containing the url.
     *
     * @var string
     */
    var $UrlProperty = 'ccm:wwwurl';
                        
     /**
     * Set the name of the property which should contain the url of interest.
     *
     * @param string $UrlProperty
     *
     * @return mod_url
     */
    public function setUrlProperty($UrlProperty) {
        assert(is_string($UrlProperty));
        $this -> UrlProperty = $UrlProperty;
        return $this;
    }

    /**
     * Get the name of the property which should contain the url of interest.
     *
     * @return string
     */
    protected function getUrlProperty() {
      return $this -> UrlProperty;
    }

}
