<?php

class ESRender_Sequence_Handler {
    private $esObject = null;

    public function __construct(ESObject $esObject) {
        $this -> esObject = $esObject;
    }

    public function isSequence() {
        if(isset($this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->children->item))
            return true;
        return false;
    }

    public function render(Phools_Template_Interface $template, $tmpl, $parentUrl) {
        $templateData = array('children' => $this->getChildren(), 'parentUrl' => $parentUrl);
        return $template -> render($tmpl, $templateData);
    }

    private function getChildren() {
        $children = array();
        $item = $this->esObject->renderInfoLMSReturn->getRenderInfoLMSReturn->children->item;
        if(!is_array($item))
            $item = array($item);
        $i = 0;
        foreach($item as $child) {
            $children[$i]['iconUrl'] = $child->iconUrl;
            foreach($child->properties->item as $property) {
                $children[$i][$property->key]=$property->value;
            }
            $i++;
        }
        return $children;
    }

}