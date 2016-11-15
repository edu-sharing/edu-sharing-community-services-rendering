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
     * "Display" the url by presentung a intermediate page announcing the
     * redirect.
     *
     * (non-PHPdoc)
     * @see ESRender_Module_Base::display()
     */
    protected function display(array $requestData) {

        if (!$this -> validate()) {
      
         error_log('URL-property is empty.');
            return false;
        }

        if ($this -> detectVideo())
            $embedding = $this -> getVideoEmbedding();
        else
            $embedding = $this -> getLinkEmbedding();

        $Template = $this -> getTemplate();
        echo $Template -> render('/module/url/display', array('embedding' => $embedding, 'title' => $this->_ESOBJECT->getTitle()));

        return true;
    }
    
    protected function dynamic(array $requestData) {
    
    	if (!$this -> validate()) {
    		error_log('URL-property is empty.');
    		return false;
    	}
    
    	if ($this -> detectVideo())
    		$embedding = $this -> getVideoEmbedding();
    	else
    		$embedding = '';
    		
    	$metadata = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');
    	$Template = $this -> getTemplate();
    	echo $Template -> render('/module/url/dynamic', array('embedding' => $embedding, 'metadata' => $metadata, 'url' => $this->getUrl(), 'previewUrl' => $this->_ESOBJECT->renderInfoLMSReturn->getRenderInfoLMSReturn->previewUrl));
    
    	return true;
    }
    
    protected function inline(array $requestData) {
        if (!$this -> validate()) {
            error_log('URL-property is empty.');
            return false;
        }
        
        if(ENABLE_METADATA_RENDERING) {
	        $metadata = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate());
	        $data['metadata'] = $metadata;
        }

        if ($this -> detectVideo()) {
        	$factory = new ESRender_LicenseFactory_Implementation();
        	$lic = $factory -> getLicense(ESRender_License_Interface::CC_BY, 'Youtube', '', $this->_ESOBJECT->ESOBJECT_TITLE);
        	$license = $lic -> renderFooter($this -> getTemplate());
            $embedding = $this -> getVideoEmbedding($requestData['width']) .  utf8_encode($license) . utf8_encode($metadata);
        	
        } else {
        	$license = $this->_ESOBJECT->ESOBJECT_LICENSE;
        	if(!empty($license)) {
        		$license = $license -> renderFooter($this -> getTemplate());
        	}
            $embedding = $this -> getLinkEmbedding();
            if(!empty($license || !empty($metadata))) {
            	$embedding .= ' (';
            	$embedding .= '<span style="display: inline-block">' . utf8_encode($license) . '</span>';
            	if(!empty($license && !empty($metadata)))
            		$embedding .= '&nbsp|&nbsp';
            	$embedding .= '<span style="display: inline-block">' . utf8_encode($metadata) . '</span>';
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
    
    protected function getVideoEmbedding($width = NULL) {

        if(empty($width)) {
            $width = 800;
        }
        //16:9
        $height = $width * 0.5625;
        

        $objId = $this -> _ESOBJECT -> getObjectID();
        //wrappers needed to handle max width
        if($this -> isYoutubeRemoteObject()){
            $vidId = $this->_ESOBJECT->AlfrescoNode->getProperty('{http://www.campuscontent.de/model/1.0}remotenodeid');
            return '<div class="videoWrapperOuter" style="max-width:' . $width . 'px;"><div class="videoWrapperInner" style="position: relative; padding-bottom: 56.25%; padding-top: 25px; height: 0;"><iframe id="' . $objId . '" width="' . $width . '" height="' . $height . '" src="//www.youtube-nocookie.com/embed/' . $vidId . '?modestbranding=1" frameborder="0" allowfullscreen class="embedded_video" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe></div></div><p class="caption"><es:title></es:title></p>';
         }
        else if (strpos($this -> getUrl(), VIDEO_TOKEN_YOUTUBE) !== false) {
                    $parsedUrl = parse_url($this -> getUrl());
                    $paramsArr = explode('&', $parsedUrl['query']);
                    foreach ($paramsArr as $param) {
                        $item = explode('=', $param);
                        $params[$item[0]] = $item[1];
                    }
                    $vidId = $params['v'];
                    return '<div class="videoWrapperOuter" style="max-width:' . $width . 'px;"><div class="videoWrapperInner" style="position: relative; padding-bottom: 56.25%; padding-top: 25px; height: 0;"><iframe id="' . $objId . '" width="' . $width . '" height="'.$height.'" src="//www.youtube-nocookie.com/embed/' . $vidId . '?modestbranding=1" frameborder="0" allowfullscreen class="embedded_video" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe></div></div><p class="caption"><es:title></es:title></p>';
                }
        else if (strpos($this -> getUrl(), VIDEO_TOKEN_VIMEO) !== false) {
            $urlArr = explode('/', $this -> getUrl());
            $vidId = end($urlArr);
            return '<div class="videoWrapperOuter" style="max-width:'.$width.'px;"><div class="videoWrapperInner" style="position: relative; padding-bottom: 56.25%; padding-top: 25px; height: 0;"><iframe id="' . $objId . '" width="'.$width.'" height="'.$height.'" src="//player.vimeo.com/video/' . $vidId . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen class="embedded_video" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></iframe></div></div><p class="caption"><es:title></es:title></p>';
        }
        return '';
    }

    protected function getUrl() {
        $urlProp = $this -> _ESOBJECT -> AlfrescoNode -> getProperty($this -> getUrlProperty());
        if(!empty($urlProp))
            return $this -> _ESOBJECT -> AlfrescoNode -> getProperty($this -> getUrlProperty());
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
