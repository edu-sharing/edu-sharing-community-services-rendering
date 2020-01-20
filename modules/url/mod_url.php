<?php

/**
 * Module to handle URL's.
 *
 *
 */

define("VIDEO_TOKEN_YOUTUBE", "youtube.com/watch?");
define("VIDEO_TOKEN_VIMEO", "vimeo.com");
define("VIDEO_TOKENS", serialize(array(VIDEO_TOKEN_YOUTUBE, VIDEO_TOKEN_VIMEO)));

class mod_url
extends ESRender_Module_NonContentNode_Abstract {

    protected function dynamic() {
    	if (!$this -> validate()) {
    		return false;
    	}

    	if(Config::get('urlEmbedding')) {
            $embedding = Config::get('urlEmbedding');
        }else if ($this -> detectVideo()) {
            $embedding = $this->getVideoEmbedding();
        }else if($this -> detectAudio()) {
            $embedding = $this->getAudioEmbedding();
        }else if($this -> detectImage()) {
            $embedding = $this->getImageEmbedding();
        }else if($this -> detectH5P()) {
            $embedding = $this->getH5PEmbedding();
        }else if($this -> detectPrezi()) {
            $embedding = $this->getPreziEmbedding();
        }else
    		$embedding = '';

    	$Template = $this -> getTemplate();
    	$tempArray = array('embedding' => $embedding, 'url' => $this->getUrl(), 'previewUrl' => $this -> esObject->getPreviewUrl());
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

        if(Config::get('urlEmbedding'))
            $embedding = Config::get('urlEmbedding');
        else if ($this -> detectVideo())
            $embedding = $this -> getVideoEmbedding();
        else if($this -> detectAudio())
            $embedding = $this -> getAudioEmbedding();
        else if($this -> detectImage())
            $embedding = $this -> getImageEmbedding();
        else
            $embedding = '';


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

        $footer = $this->getTemplate()->render('/footer/inline', array('license' => $license, 'metadata' => utf8_decode($metadata), 'sequence' => $sequence, 'title' => $this -> esObject -> getTitle()));
        $Template = $this -> getTemplate();
        $tempArray = array('embedding' => $embedding, 'url' => $this->getUrl(), 'previewUrl' => $this -> esObject->getPreviewUrl(), 'footer' => $footer);

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

        $footer = $this->getTemplate()->render('/footer/inline', array('license' => $license, 'metadata' => utf8_decode($metadata), 'sequence' => $sequence, 'title' => $this -> esObject -> getTitle()));


        if(Config::get('urlEmbedding')) {
            $embedding = Config::get('urlEmbedding') . $footer;
        } else if ($this -> detectVideo()) {
            $embedding = $this -> getVideoEmbedding(mc_Request::fetch('width', 'INT', 600), $footer);
        } else if($this -> detectAudio()) {
            $embedding = $this->getAudioEmbedding($footer);
        } else if($this -> detectImage()) {
            $embedding = $this -> getImageEmbedding($footer);
        } else if($this -> detectH5P()) {
            $embedding = $this->getH5PEmbedding($footer);
        }else if($this -> detectPrezi()) {
            $embedding = $this->getPreziEmbedding($footer);
        }else {
            $license = $this -> esObject->getLicense();
            if (!empty($license))
                $license = $license->renderFooter($this->getTemplate(), $this->getUrl());
            $embedding = $this->getTemplate()->render('/footer/inline', array('license' => $license, 'metadata' => utf8_decode($metadata), 'sequence' => $sequence, 'title' => $this -> esObject->getTitle(), 'url' => $this->getUrl()));
        }

        $data = array('embedding' => $embedding);
                
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/url/inline', $data);
        
        return true;
    }

    private function validate() {
        if (!$this -> getUrl() && !$this -> isYoutubeRemoteObject())
              return false;
        return true;
    }
    
    protected function isYoutubeRemoteObject() {
        if($this -> esObject -> getNode() -> remote -> repository -> repositoryType  == 'YOUTUBE')
            return true;
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
        $htm = '<div style="max-width:100%"><iframe width="800" height="580" src="'.$preziUrl.'embed" webkitallowfullscreen="1" mozallowfullscreen="1" allowfullscreen="1"></iframe>'. $footer.'</div>';
        return $htm;
    }

    protected function getAudioEmbedding($footer = '')
    {
        return '<div><audio style="max-width:100%" src="' . $this->getUrl() . '" type="' . $this -> esObject->getMimeType() . '" controls="controls" oncontextmenu="return false;"></audio>' . $footer . '</div>';
    }

        protected function getImageEmbedding($footer = '')
        {
            return '<div><img title="' . $this -> esObject->getTitle() . '" alt="' . $this -> esObject->getTitle() . '" src="' . $this->getUrl() . '" style="max-width: 100%">
                ' . $footer . '</div>';
        }

        protected function getVideoEmbedding($width = NULL, $footer = '') {
		
		global $MC_URL, $Locale;

        if(empty($width)) {
            $width = 800;
        }
        //16:9
        $height = $width * 0.5625;
        $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
        $objId = $this -> esObject -> getObjectID();
        $videoWrapperInnerStyle = 'position: relative; padding-bottom: 56.25%; padding-top: 25px; height: 0;';

        //wrappers needed to handle max width
        if($this -> isYoutubeRemoteObject()){
            $vidId = $this -> esObject -> getNode() -> remote -> id;
            $applyDataProtectionRegulationsDialog = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($objId, 'Youtube', 'https://policies.google.com/privacy?hl='.$Locale->getLanguageTwoLetters(), 'www.youtube-nocookie.com', 'YOUTUBE');
            return '<div class="videoWrapperOuter" style="max-width:' . $width . 'px;">
            			<div class="videoWrapperInner" style="'.($applyDataProtectionRegulationsDialog?'':$videoWrapperInnerStyle).'">
			                '.$applyDataProtectionRegulationsDialog.'
            				<iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;' . ($applyDataProtectionRegulationsDialog?'display:none':'') . '" id="' . $objId . '" data-src="//www.youtube-nocookie.com/embed/' . $vidId . '?modestbranding=1" src="" frameborder="0" allowfullscreen class="embedded_video"></iframe>
            			</div>
            			'.$footer.'
            		</div>';
        }
        else if (strpos($this -> getUrl(), VIDEO_TOKEN_YOUTUBE) !== false) {
            $parsedUrl = parse_url($this -> getUrl());
            $paramsArr = explode('&', $parsedUrl['query']);
            foreach ($paramsArr as $param) {
                $item = explode('=', $param);
                $params[$item[0]] = $item[1];
            }
            $vidId = $params['v'];
            $applyDataProtectionRegulationsDialog = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($objId, 'Youtube', 'https://policies.google.com/privacy?hl='.$Locale->getLanguageTwoLetters(), 'www.youtube-nocookie.com', 'YOUTUBE');
            return '<div class="videoWrapperOuter" style="max-width:' . $width . 'px;">
                        <div class="videoWrapperInner" style="'.($applyDataProtectionRegulationsDialog?'':$videoWrapperInnerStyle).'">
                           '.$applyDataProtectionRegulationsDialog.'
                            <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;' . ($applyDataProtectionRegulationsDialog?'display:none':'') . '" id="' . $objId . '" data-src="//www.youtube-nocookie.com/embed/' . $vidId . '?modestbranding=1" src="" frameborder="0" allowfullscreen class="embedded_video"></iframe>
                        </div>
                        '.$footer.'
                    </div>';
        }
        else if (strpos($this -> getUrl(), VIDEO_TOKEN_VIMEO) !== false) {
            $urlArr = explode('/', $this -> getUrl());
            $vidId = end($urlArr);
            $applyDataProtectionRegulationsDialog = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($objId, 'Vimeo', 'https://help.vimeo.com/hc/de/sections/203915088-Datenschutz', 'player.vimeo.com', 'VIMEO');
            return '<div class="videoWrapperOuter" style="max-width:'.$width.'px;">
            			<div class="videoWrapperInner" style="'.($applyDataProtectionRegulationsDialog?'':$videoWrapperInnerStyle).'">
            			    '.$applyDataProtectionRegulationsDialog.'
            				<iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;' . ($applyDataProtectionRegulationsDialog?'display:none':'') . '" id="' . $objId . '" data-src="//player.vimeo.com/video/' . $vidId . '" src="" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="embedded_video"></iframe>
            			</div>
            			'.$footer.'
            		</div>';
        } else {
            $type = $this -> esObject->getMimeType();
            if(pathinfo($this -> getUrl(), PATHINFO_EXTENSION) === 'mp4' || pathinfo($this -> getUrl(), PATHINFO_EXTENSION) === 'webm') {
                $type = 'video/' . pathinfo($this -> getUrl(), PATHINFO_EXTENSION);
            }
            $identifier = uniqid();
            $applyDataProtectionRegulationsDialog = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($objId, '', '', $this -> getUrl(),'VIDEO_DEFAULT');
            return '<div class="videoWrapperOuter" style="max-width:'.$width.'px;">
                        '.$applyDataProtectionRegulationsDialog.'
                    <div id="videoWrapperInner_'.$objId.'" class="videoWrapperInner" style="position: relative; padding-top: 25px;' . ($applyDataProtectionRegulationsDialog?'display:none':'') . '">
                        <video id="'.$identifier.'" data-tap-disabled="true" controls style="max-width: 100%;background: transparent url(\''.$this->esObject->getPreviewUrl().'\') 50% 50% / cover no-repeat;" oncontextmenu="return false;" controlsList="nodownload">
                            <source src="' . $this -> getUrl() . '" type="' . $type . '"></source>
                        </video>
                    </div>
                    '.$footer.'
                </div>';
        }
    }

    protected function getUrl() {
        $urlProp = $this -> esObject -> getNodeProperty($this -> getUrlProperty());
       if(!empty($urlProp))
            return html_entity_decode($urlProp);
        return false;
    }

    protected function detectVideo() {

        if($this -> isYoutubeRemoteObject())
            return true;
        
        $needles = unserialize(VIDEO_TOKENS);
        foreach ($needles as $needle) {
            if (strpos($this -> getUrl(), $needle) !== false)
                return true;
        }

        if(pathinfo($this -> getUrl(), PATHINFO_EXTENSION) === 'mp4' || pathinfo($this -> getUrl(), PATHINFO_EXTENSION) === 'webm')
            return true;
        
        //filter videos that are embedded in html
        if(strpos($this -> esObject->getMimeType(), 'video') !== false && strpos($this -> getUrl(), '.htm') === false && strpos($this -> getUrl(), '.php') === false)
        	return true;
        
        return false;
    }
    
    protected function detectAudio() {
    	if(strpos($this -> esObject->getMimeType(), 'audio') !== false)
    		return true;
    }

    protected function detectImage() {
        if((strpos($this -> esObject->getMimeType(), '/png') !== false ||
            strpos($this -> esObject->getMimeType(), '/jpg') !== false ||
            strpos($this -> esObject->getMimeType(), '/jpeg') !== false ||
            strpos($this -> esObject->getMimeType(), '/gif') !== false) &&
            $this -> esObject -> getNodeProperty('ccm:remoterepositorytype') !== 'DDB')
            return true;
        return false;
    }

    protected function detectH5P() {
    	if(strpos($this -> getUrl(), 'h5p.org/h5p/embed') !== false){
            return true;
        }
        return false;
    }

    protected function detectPrezi() {
        if(strpos($this -> getUrl(), 'prezi.com/view/') !== false){
            return true;
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
