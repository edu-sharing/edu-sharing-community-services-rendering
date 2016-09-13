<?php

class ESRender_Metadata_Handler {
	
	private $esObject = null;
	private $valuesToShow = array(
			'{http://www.alfresco.org/model/content/1.0}creator',
			'{http://www.campuscontent.de/model/1.0}commonlicense_key',
			'{http://www.alfresco.org/model/content/1.0}versionLabel',
			'REPOSITORY_ID');
	
	public function __construct(ESObject $esObject) {
		$this -> esObject = $esObject;
	}
	
	public function render(Phools_Template_Interface $template, $tmpl = '/metadata/default', $valuesToShow = false) {
		
		if(!empty($valuesToShow))
			$this->valuesToShow = $valuesToShow;
		
		return $template -> render($tmpl, array(
            'title' => $this -> esObject -> getTitle(),
			'meta' => $this -> getMetadata()
		));
	}
		
	private function getLabels($key) {
		
		if(!empty($this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->labels->item)) {		
			foreach($this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->labels->item as $item) {
				if($item->key == $key)
					return $item->value;
			}
		}
		return $key;
	}
	
	
	private function getMetaData() {
		$return = array();
		foreach($this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->properties->item as $item) {
			if(in_array($item->key, $this->valuesToShow))
				$return[$this->getLabels($item->key)] = empty($item-> value) ? '-' : $item-> value;
		}
		return $return;
	}
	
}

