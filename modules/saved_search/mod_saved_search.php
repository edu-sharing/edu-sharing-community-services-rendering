<?php

require_once '../../conf.inc.php';

class mod_saved_search
extends ESRender_Module_ContentNode_Abstract {
    protected function inline() {
        $metaData = $this->esObject->getMetadataHandler()
            ->render($this->getTemplate(), '/metadata/inline');
        echo $this->getTemplate()->render('/module/saved_search/inline', [
            'title' => htmlentities($this->esObject->getTitle()),
            'homeRepoUrl' => Config::get('homeRepository')->url,
            'objectId' => $this->esObject->getObjectID(),
            'children' => $this->esObject->getData()->children,
            'metaData' => $metaData,
        ]);
        return true;
    }
}
