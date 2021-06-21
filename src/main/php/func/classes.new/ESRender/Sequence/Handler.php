<?php

class ESRender_Sequence_Handler {
    private $esObject = null;

    public function __construct(ESObject $esObject) {
        $this -> esObject = $esObject;
    }

    public function isSequence() {
        if(isset($this -> esObject -> getData() -> children) && sizeof($this -> esObject -> getData() -> children) > 0)
            return true;
        return false;
    }

    public function render(Phools_Template_Interface $template, $tmpl, $parentUrl) {
        $templateData = array('children' => $this->getChildren(), 'parentUrl' => $parentUrl);
        return $template -> render($tmpl, $templateData);
    }

    private function getChildren() {
        $children = array();
        $item = $this -> esObject -> getData() -> children;
        if(!is_array($item))
            $item = array($item);
        $i = 0;
        foreach($item as $child) {
            $children[$i]['iconUrl'] = $child -> iconURL;
            $children[$i]['name'] = $child -> name;
            $children[$i]['NodeID'] = $child -> ref -> id;
            $i++;
        }
        return $children;
    }

}