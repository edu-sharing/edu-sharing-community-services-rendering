<?php

class ESRender_Metadata_Handler {
	
	private $esObject = null;

	public function __construct(ESObject $esObject) {		
		$this -> esObject = $esObject;
	}

	public function render(Phools_Template_Interface $template, $tmpl) {
        $templateData = array('metadataHtml' => $this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->mdsTemplate, 'title' => $this->esObject->getTitle(), 'previewUrl' => $this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->previewUrl);
        return $template -> render($tmpl, $templateData);
	}
}

