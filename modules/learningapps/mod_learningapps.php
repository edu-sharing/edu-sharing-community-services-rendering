<?php

/**
 * Module to handle learningapps.
 *
 *
 */

class mod_url
extends ESRender_Module_NonContentNode_Abstract {

    protected function display(array $requestData) {
        parent::display($requestData);
    }

    protected function dynamic(array $requestData) {
    	$Template = $this -> getTemplate();
    	$tempArray = array('url' => $this->getUrl());
    	if(Config::get('showMetadata'))
    		$tempArray['metadata'] = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');
    	$tempArray['title'] = $this->_ESOBJECT->getTitle();
    	echo $Template -> render('/module/learningapps/dynamic', $tempArray);
    	return true;
    }
    
    protected function inline(array $requestData) {
        $data = array();
        if(ENABLE_METADATA_INLINE_RENDERING) {
            $metadata = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/inline');
            $data['metadata'] = $metadata;
        }
        $license = $this->_ESOBJECT->ESOBJECT_LICENSE;
        if(!empty($license)) {
            $data['license'] = $license -> renderFooter($this -> getTemplate());
        }
        $data['url'] = $this->getUrl();
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/learningapps/inline', $data);
        return true;
    }

    protected function getUrl() {
        $urlProp = $this -> _ESOBJECT -> AlfrescoNode -> getProperty($this -> getUrlProperty());
        if(!empty($urlProp))
            return $urlProp;
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