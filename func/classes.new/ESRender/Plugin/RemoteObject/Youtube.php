<?php

require_once (dirname(__FILE__) . '/../Abstract.php');

class ESRender_Plugin_RemoteObject_Youtube extends ESRender_Plugin_Abstract {

    public function __construct() {

    }

    public function postRetrieveObjectProperties(EsApplication &$remote_rep, &$app_id, Node &$contentNode, &$course_id, &$resource_id, &$username) {
        
         if ($contentNode -> properties['{http://www.campuscontent.de/model/1.0}remoterepositorytype'] == 'YOUTUBE') {

            echo '<iframe id="'.$contentNode -> properties['{http://www.alfresco.org/model/content/1.0}name'].'" width="400px" height="300px" src="//www.youtube.com/embed/' . $contentNode -> properties['{http://www.campuscontent.de/model/1.0}remotenodeid'] . '" frameborder="0" allowfullscreen></iframe>';die();
            

        }
        
    }

}
