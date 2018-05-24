<?php

class ESRender_Sequence_Handler {
    private $esObject = null;

    public function __construct(ESObject $esObject) {
        $this -> esObject = $esObject;
    }

    public function render(Phools_Template_Interface $template, $tmpl) {
        $templateData = array('sequence' => $this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->childs);
        return $template -> render($tmpl, $templateData);
    }

}