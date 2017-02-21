<?php

class ESRender_Metadata_Handler {
	
	private $esObject = null;
	private $valuesToShow = array();
	
	public function __construct(ESObject $esObject) {		
		$this -> esObject = $esObject;
		$this -> valuesToShow = unserialize(DISPLAY_DYNAMIC_METADATA_KEYS);
	}
	
	public function render(Phools_Template_Interface $template, $tmpl = '/metadata/default') {
		if(strpos($tmpl, 'dynamic') !== false)
			return $template -> render($tmpl, array(
					'title' => $this -> esObject -> getTitle(),
					'meta' => $this -> getFullMetadata()
			));
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
	
	private function getFullMetadata() {
		$return = array();
		foreach($this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->properties->item as $item) {
				$return[$item->key]['label'] = $this->getLabels($item->key);
				$return[$item->key]['value'] = $item->value;
		}
		return $return;
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

