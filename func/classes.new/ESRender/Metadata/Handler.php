<?php

class ESRender_Metadata_Handler {
	
	private $esObject = null;

	public function __construct(ESObject $esObject) {		
		$this -> esObject = $esObject;
	}

	public function render(Phools_Template_Interface $template, $tmpl) {
        $previewUrl = $this->esObject->getPreviewUrl();
        if(!in_array('ReadAll', Config::get('permissions'))) {
            $previewUrl = '';
        }
        $templateData = array('metadataHtml' => $this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->mdsTemplate, 'title' => $this->esObject->getTitle(), 'previewUrl' => $previewUrl);
        return $template -> render($tmpl, $templateData);
	}
}

