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

    /**
     * Deprecated
     *
     * (non-PHPdoc)
     * @see ESRender_Module_Base::display()
     */
    protected function display(array $requestData) {
        return true;
    }
    
    protected function dynamic(array $requestData) {
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

    	$Template = $this -> getTemplate();
    	$tempArray = array('embedding' => $embedding, 'url' => $this->getUrl(), 'previewUrl' => $this->_ESOBJECT->getPreviewUrl());
    	if(Config::get('showMetadata'))
    		$tempArray['metadata'] = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');
    	
    	$tempArray['title'] = $this->_ESOBJECT->getTitle();
    	echo $Template -> render('/module/url/dynamic', $tempArray);
    
    	return true;
    }
    
    protected function inline(array $requestData) {
        if (!$this -> validate()) {
            return false;
        }
        
        if(ENABLE_METADATA_INLINE_RENDERING) {
	        $metadata = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/inline');
	        $data['metadata'] = $metadata;
        }

        $license = $this->_ESOBJECT->ESOBJECT_LICENSE;
        if(!empty($license)) {
            $license = $license -> renderFooter($this -> getTemplate());
        }

        if(Config::get('urlEmbedding')) {
            $embedding = Config::get('urlEmbedding') . $license . $metadata;;
        } else if ($this -> detectVideo()) {
            $embedding = $this -> getVideoEmbedding($requestData['width']) . $license . $metadata;
        } else if($this -> detectAudio()) {
            $embedding = $this->getAudioEmbedding() . $license . $metadata;
        } else if($this -> detectImage()) {
            $embedding = $this -> getImageEmbedding() . $license . $metadata;
        } else {
            $embedding = $this -> getLinkEmbedding();
            if(!empty($license) || !empty($metadata)) {
            	$embedding .= ' (';
            	$embedding .= '<span style="display: inline-block">' . utf8_encode($license) . '</span>';
            	if(!empty($license) && !empty($metadata))
            		$embedding .= '&nbsp|&nbsp';
            	$embedding .= '<span style="display: inline-block">' . $metadata . '</span>';
            	$embedding .= ')';
        
            }
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
        if($this -> _ESOBJECT -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}remoterepositorytype') == 'YOUTUBE')
            return true;
        return false;
    }

    protected function getLinkEmbedding() {
        $htm =  '<script> if (typeof single != "undefined") location.href="'.$this -> getUrl().'";</script>';    
        $htm .= '<a href="' . $this -> getUrl() . '" target="_blank"><es:title xmlns:es="http://edu-sharing.net/object" >' . htmlspecialchars($this -> getUrl(), ENT_QUOTES, 'UTF-8') . '</es:title></a>';
        return $htm;         
    }
    
    protected function getAudioEmbedding() {
    	return '<audio style="max-width:100%" src="'.$this -> getUrl().'" type="'. $this->_ESOBJECT->getMimeType() .'" controls="controls" oncontextmenu="return false;"></audio>
        		<p class="caption"><es:title></es:title></p>';
    }

    protected function getImageEmbedding() {
        return '<img title="'.$this->_ESOBJECT->getTitle().'" alt="'.$this->_ESOBJECT->getTitle().'" src="'.$this -> getUrl().'" style="max-width: 100%">
        		<p class="caption"><es:title></es:title></p>';
    }

    protected function getVideoEmbedding($width = NULL) {
		
		global $MC_URL, $Locale;

        if(empty($width)) {
            $width = 800;
        }
        //16:9
        $height = $width * 0.5625;
        $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();

        $objId = $this -> _ESOBJECT -> getObjectID();
        //wrappers needed to handle max width
        if($this -> isYoutubeRemoteObject()){
            $vidId = $this->_ESOBJECT->AlfrescoNode->getProperty('{http://www.campuscontent.de/model/1.0}remotenodeid');
            return '<div class="videoWrapperOuter" style="max-width:' . $width . 'px;">
            			<div class="videoWrapperInner" style="position: relative; padding-bottom: 56.25%; padding-top: 25px; height: 0;">
			                '.$dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($objId, 'Youtube', 'https://policies.google.com/privacy?hl='.$Locale->getLanguageTwoLetters(), 'YOUTUBE').'
            				<iframe style="none" id="' . $objId . '" width="' . $width . '" height="' . $height . '" data-src="//www.youtube-nocookie.com/embed/' . $vidId . '?modestbranding=1" src="" frameborder="0" allowfullscreen class="embedded_video" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
            			</div>
            		</div>
            		<p class="caption"><es:title></es:title></p>';
         }
        else if (strpos($this -> getUrl(), VIDEO_TOKEN_YOUTUBE) !== false) {
            $parsedUrl = parse_url($this -> getUrl());
            $paramsArr = explode('&', $parsedUrl['query']);
            foreach ($paramsArr as $param) {
                $item = explode('=', $param);
                $params[$item[0]] = $item[1];
            }
            $vidId = $params['v'];
            return '<div class="videoWrapperOuter" style="max-width:' . $width . 'px;">
                        <div class="videoWrapperInner" style="position: relative; padding-bottom: 56.25%; padding-top: 25px; height: 0;">
                           '.$dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($objId, 'Youtube', 'https://policies.google.com/privacy?hl='.$Locale->getLanguageTwoLetters(), 'YOUTUBE').'
                            <iframe style="display:none" id="' . $objId . '" width="' . $width . '" height="'.$height.'" data-src="//www.youtube-nocookie.com/embed/' . $vidId . '?modestbranding=1" src="" frameborder="0" allowfullscreen class="embedded_video" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
                        </div>
                    </div>
                    <p class="caption"><es:title></es:title></p>';
        }
        else if (strpos($this -> getUrl(), VIDEO_TOKEN_VIMEO) !== false) {
            $urlArr = explode('/', $this -> getUrl());
            $vidId = end($urlArr);
            return '<div class="videoWrapperOuter" style="max-width:'.$width.'px;">
            			<div class="videoWrapperInner" style="position: relative; padding-bottom: 56.25%; padding-top: 25px; height: 0;">
            			    '.$dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($objId, 'Vimeo', 'https://help.vimeo.com/hc/de/sections/203915088-Datenschutz', 'VIMEO').'
            				<iframe style="display:none" id="' . $objId . '" width="'.$width.'" height="'.$height.'" data-src="//player.vimeo.com/video/' . $vidId . '" src="" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="embedded_video" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe>
            			</div>
            		</div>
            		<p class="caption"><es:title></es:title></p>';
        } else {
            $type = $this->_ESOBJECT->getMimeType();
            if(pathinfo($this -> getUrl(), PATHINFO_EXTENSION) === 'mp4' || pathinfo($this -> getUrl(), PATHINFO_EXTENSION) === 'webm') {
                $type = 'video/' . pathinfo($this -> getUrl(), PATHINFO_EXTENSION);
            }
            $identifier = uniqid();
            return '<div class="videoWrapperOuter" style="max-width:'.$width.'px;">
                        '.$dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($objId, '', '', 'VIDEO_DEFAULT').'
                    <div id="videoWrapperInner_'.$objId.'" class="videoWrapperInner" style="display: none;position: relative; padding-top: 25px; ">
                        <video poster="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="'.$identifier.'" data-tap-disabled="true" controls style="max-width: 100%;background: transparent url(\''.$this->_ESOBJECT->getPreviewUrl().'\') 50% 50% / cover no-repeat;" oncontextmenu="return false;" controlsList="nodownload">
                            <source src="' . $this -> getUrl() . '" type="' . $type . '"></source>
                        </video>
                        <div class="playButton" id="b_'.$identifier.'"></div>
                    </div>
                </div>
                <p class="caption"><es:title></es:title></p>
                <style>.playButton{background: transparent url(\''.$MC_URL.'/theme/default/img/play.svg\') 50% 50% / cover no-repeat;height: 100px;position: absolute;width: 100px;margin: auto;top:0;bottom:0;right:0;left:0;}</style>
                <script>var video_'.$identifier.' = document.getElementById(\''.$identifier.'\');
                video_'.$identifier.'.addEventListener(\'play\',function(){video_'.$identifier.'.play();document.getElementById(\'b_'.$identifier.'\').style.display = \'none\';},false);
                video_'.$identifier.'.addEventListener(\'ended\',function(){document.getElementById(\'b_'.$identifier.'\').style.display = \'block\';},false);
                video_'.$identifier.'.addEventListener(\'pause\',function(){document.getElementById(\'b_'.$identifier.'\').style.display = \'block\';},false);
                b_'.$identifier.'.onclick = function(){video_'.$identifier.'.click();};
                video_'.$identifier.'.onclick = function(){if (video_'.$identifier.'.paused){video_'.$identifier.'.play();}else{video_'.$identifier.'.pause();}return false;};</script>';
        }
    }

    protected function getUrl() {
        $urlProp = $this -> _ESOBJECT -> AlfrescoNode -> getProperty($this -> getUrlProperty());
       if(!empty($urlProp))
            return $urlProp;
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
        if(strpos($this->_ESOBJECT->getMimeType(), 'video') !== false && strpos($this -> getUrl(), '.htm') === false && strpos($this -> getUrl(), '.php') === false)
        	return true;
        
        return false;
    }
    
    protected function detectAudio() {
    	if(strpos($this->_ESOBJECT->getMimeType(), 'audio') !== false)
    		return true;
    }

    protected function detectImage() {
        if((strpos($this->_ESOBJECT->getMimeType(), '/png') !== false ||
            strpos($this->_ESOBJECT->getMimeType(), '/jpg') !== false ||
            strpos($this->_ESOBJECT->getMimeType(), '/jpeg') !== false ||
            strpos($this->_ESOBJECT->getMimeType(), '/gif') !== false) &&
            $this->_ESOBJECT->AlfrescoNode->getProperty('{http://www.campuscontent.de/model/1.0}remoterepositorytype') !== 'DDB')
            return true;
        return false;
    }

    /**
     * The object's property containing the url.
     *
     * @var string
     */
    var $UrlProperty = '{http://www.campuscontent.de/model/1.0}wwwurl';
                        
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
