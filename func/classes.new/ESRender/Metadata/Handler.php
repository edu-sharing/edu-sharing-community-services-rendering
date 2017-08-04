<?php

class ESRender_Metadata_Handler {
	
	private $esObject = null;

	public function __construct(ESObject $esObject) {		
		$this -> esObject = $esObject;
	}

	public function render(Phools_Template_Interface $template, $tmpl) {
        $templateData = array('metadataHtml' => utf8_encode($this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->mdsTemplate), 'title' => $this->esObject->getTitle());
        return $template -> render($tmpl, $templateData);
	}
}

