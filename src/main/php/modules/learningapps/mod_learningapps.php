<?php

/**
 * Module to handle learningapps.
 *
 *
 */

class mod_learningapps
extends ESRender_Module_NonContentNode_Abstract {

    protected function dynamic() {
    	$Template = $this -> getTemplate();
    	$tempArray = array('url' => $this->getUrl());
    	if(Config::get('showMetadata'))
    		$tempArray['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');
    	$tempArray['title'] = $this -> esObject -> getTitle();
    	$uniqueId = uniqid('la_');
        $data['uniqueId'] = $uniqueId;
        $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
        $data['dataProtectionRegulationsDialog'] = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($this->esObject, $uniqueId, 'LearningApps.org', 'https://learningapps.org/rechtliches.php', 'LEARNINGAPP');
        echo $Template -> render('/module/learningapps/dynamic', $tempArray);
    	return true;
    }

    protected function embed() {
        $Template = $this -> getTemplate();
        $tempArray = array('url' => $this->getUrl());
        $uniqueId = uniqid('la_');
        $data['uniqueId'] = $uniqueId;
        echo $Template -> render('/module/learningapps/embed', $tempArray);
        return true;
    }
    
    protected function inline() {
        $data = array();
        if(ENABLE_METADATA_INLINE_RENDERING) {
            $metadata = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/inline');
            $data['metadata'] = $metadata;
        }
        $license = $this -> esObject -> getLicense();
        if(!empty($license)) {
            $data['license'] = $license -> renderFooter($this -> getTemplate());
        }
        $data['url'] = $this->getUrl();
        $data['originUrl'] = $this->getOriginUrl();
        $uniqueId = uniqid('la_');
        $data['uniqueId'] = $uniqueId;
        $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
        $data['dataProtectionRegulationsDialog'] = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($this->esObject, $uniqueId, 'LearningApps.org', 'https://learningapps.org/rechtliches.php', 'LEARNINGAPP');
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/learningapps/inline', $data);
        return true;
    }

    protected function getOriginUrl() {
        $urlProp = $this -> esObject -> getNodeProperty($this -> getUrlProperty());
        if(!empty($urlProp))
            return $urlProp;
        return false;
    }

    protected function getUrl() {
        $urlProp = $this -> esObject -> getNodeProperty($this -> getUrlProperty());
        if(!empty($urlProp))
            return str_replace('https://learningapps.org/', 'https://learningapps.org/view', $urlProp);
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
