<?php

class mod_directory extends ESRender_Module_NonContentNode_Abstract {


    public function inline(array $requestData) {
        $children = array();
        $i = 0;
        foreach($this->_ESOBJECT->renderInfoLMSReturn->getRenderInfoLMSReturn->children->item as $child) {
            $children[$i]['iconUrl'] = $child->iconUrl;
            foreach($child->properties->item as $property) {
                $children[$i][$property->key]=$property->value;
            }
            $i++;
        }

        $creator = $this->_ESOBJECT->AlfrescoNode->getProperty('NodeCreator_FirstName') . ' ' . $this->_ESOBJECT->AlfrescoNode->getProperty('NodeCreator_LastName');
        if(strpos(strtolower($creator), 'administrator') !== false || strpos(strtolower($creator), 'unknown') !== false)
            $creator = '';
        $data = array('title' => htmlentities($this->_ESOBJECT->getTitle()), 'children' => $children, 'parentUrl'=> $this->lmsInlineHelper($requestData), 'folderUrl' => Config::get('homeRepository')->url . '/components/workspace?' . $requestData['object_id'], 'creator' => $creator);
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/directory/inline', $data);
        return true;
    }

    public function display(array $requestData) {
        return;
    }


}