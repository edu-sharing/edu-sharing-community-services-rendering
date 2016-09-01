<?php

require_once (dirname(__FILE__) . '/../Abstract.php');

class ESRender_Plugin_RemoteObject_Serlo extends ESRender_Plugin_Abstract {

    public function __construct() {

    }

    public function postRetrieveObjectProperties(EsApplication &$remote_rep, &$app_id, EsContentNode &$contentNode, &$course_id, &$resource_id, &$username) {
        if ($contentNode -> getProperty('{http://www.campuscontent.de/model/1.0}replicationsource') == 'serlo') {
            header('Location: ' . $contentNode -> getProperty('{http://www.campuscontent.de/model/lom/1.0}location'));
            echo '<a href="' . $contentNode -> getProperty('{http://www.campuscontent.de/model/lom/1.0}location') . '" target="_blank">' . $contentNode -> getProperty('{http://www.campuscontent.de/model/lom/1.0}title') . '!! </a>';
            exit();
        }
    }
}
