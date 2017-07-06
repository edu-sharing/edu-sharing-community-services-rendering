<?php

class ESRender_Metadata_Handler {
	
	private $esObject = null;
	private $valuesToShow = array();
	
	public function __construct(ESObject $esObject) {		
		$this -> esObject = $esObject;
	}

	public function render(Phools_Template_Interface $template, $tmpl) {
        $templateData = array('metadataHtml' => $this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->mdsTemplate, 'metadataRaw' => $this->getFullMetadata(), 'title' => $this->esObject->getTitle());
        return $template -> render($tmpl, $templateData);
	}

    private function getFullMetadata() {
        $return = array();
        foreach($this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->properties->item as $item) {
            $return[$item->key] = $item->value;
        }
        return $return;
    }
}

