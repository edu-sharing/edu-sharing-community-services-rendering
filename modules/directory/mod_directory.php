<?php

class mod_directory extends ESRender_Module_NonContentNode_Abstract {


    public function inline(ESObject $ESObject) {
        $children = array();
        $i = 0;
        $childrenItems = $this->_ESOBJECT->renderInfoLMSReturn->getRenderInfoLMSReturn->children->item;
	if($childrenItems) {
	    if(!is_array($childrenItems))
        	$childrenItems = array($childrenItems);
    	    foreach($childrenItems as $child) {
        	$children[$i]['iconUrl'] = $child->iconUrl;
        	foreach($child->properties->item as $property) {
            	    $children[$i][$property->key]=$property->value;
        	}
        	$i++;
    	    }
	}

        $creator = $this -> _ESOBJECT -> getNodeProperty('NodeCreator_FirstName') . ' ' . $this -> _ESOBJECT -> getNodeProperty('NodeCreator_LastName');
        if(strpos(strtolower($creator), 'administrator') !== false || strpos(strtolower($creator), 'unknown') !== false)
            $creator = '';
        $data = array('title' => htmlentities($this->_ESOBJECT->getTitle()), 'children' => $children, 'parentUrl'=> $this->lmsInlineHelper(), 'folderUrl' => Config::get('homeRepository')->url . '/components/workspace?' . $ESObject->getObjectID(), 'creator' => $creator);
        $Template = $this -> getTemplate();
        echo $Template -> render('/module/directory/inline', $data);
        return true;
    }

    public function display(ESObject $ESObject) {
        return;
    }


}