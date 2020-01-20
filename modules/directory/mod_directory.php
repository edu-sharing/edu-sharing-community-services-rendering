<?php

class mod_directory extends ESRender_Module_NonContentNode_Abstract {


    public function inline() {
        $children = array();
        $i = 0;
        $childrenItems = $this -> esObject->getData()->children;
        if($childrenItems) {
            if(!is_array($childrenItems))
                $childrenItems = array($childrenItems);
                foreach($childrenItems as $child) {
                    $children[$i]['iconUrl'] = $child -> iconURL;
                    $children[$i]['name'] = $child -> name;
                    $children[$i]['NodeID'] = $child -> ref -> id;
                    $i++;
                }
        }

        $creator = $this -> esObject -> getNodeProperty('createdBy') -> firstname . ' ' . $this -> esObject -> getNodeProperty('createdBy') -> lastName;
        if(strpos(strtolower($creator), 'administrator') !== false || strpos(strtolower($creator), 'unknown') !== false)
            $creator = '';
        $data = array('title' => htmlentities($this -> esObject->getTitle()), 'children' => $children, 'parentUrl'=> $this->lmsInlineHelper(), 'folderUrl' => Config::get('homeRepository')->url . '/components/workspace?' . $this -> esObject -> getObjectID(), 'creator' => $creator);
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/directory/inline', $data);
        return true;
    }

}