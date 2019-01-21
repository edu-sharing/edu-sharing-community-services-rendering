<?php

class edutoolEtherpad {

    private $token;
    private $esObject;
    private $template;

    public function __construct(ESObject $esObject, $template) {
        $this -> getConfig();
        $this -> esObject = $esObject;
        $this -> template = $template;
	    $this -> setToken();
    }

    public function display() {
        $courseId = mc_Request::fetch('app_id', 'CHAR') . '_';
        $courseId .= empty(mc_Request::fetch('course_id', 'CHAR')) ? 'default' : mc_Request::fetch('course_id', 'CHAR');
        $resource_link_id = empty(mc_Request::fetch('resource_id', 'CHAR')) ? 'default' : mc_Request::fetch('resource_id', 'CHAR');
        $userId = $this -> esObject -> getData() -> user -> authorityName;
        $fname = $this -> esObject -> getData() -> user -> authorityName;
        $params = '?fname=' . $fname . '&course_id=' . $courseId . '&resource_link_id=' . $resource_link_id . '&user_id=' . $userId .'&token=' . $this->token;
        header('HTTP/1.1 303 See other');
        header('Location: ' . ETHERPAD_PROVIDER . $params);
        return true;
    }

    public function dynamic() {
        $courseId = mc_Request::fetch('app_id', 'CHAR') . '_';
        $courseId .= empty(mc_Request::fetch('course_id', 'CHAR')) ? 'default' : mc_Request::fetch('course_id', 'CHAR');
        $resource_link_id = empty(mc_Request::fetch('resource_id', 'CHAR')) ? 'default' : mc_Request::fetch('resource_id', 'CHAR');
        $userId = $this -> esObject -> getData() -> user -> authorityName;
        $fname = $this -> esObject -> getData() -> user -> authorityName;
        $params = '?fname=' . $fname . '&course_id=' . $courseId . '&resource_link_id=' . $resource_link_id . '&user_id=' . $userId .'&token=' . $this->token;
        if(Config::get('showMetadata'))
            $template_data['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> template, '/metadata/dynamic');
        $template_data['title'] = $this->esObject->getTitle();
        $template_data['url'] = ETHERPAD_PROVIDER . $params;
        echo $this -> template -> render('/module/lti/etherpad/dynamic', $template_data);
        return true;
    }
    
    public function inline() {
        $courseId = mc_Request::fetch('app_id', 'CHAR') . '_' . mc_Request::fetch('course_id', 'CHAR');
        $resource_link_id = mc_Request::fetch('resource_id', 'CHAR');
        $userId = $this -> esObject -> getData() -> user -> authorityName;
        $fname = $this -> esObject -> getData() -> user -> authorityName;
        $params = '?fname=' . $fname . '&course_id=' . $courseId . '&resource_link_id=' . $resource_link_id . '&user_id=' . $userId . '&token=' . $this -> token;
        echo  '<iframe style="width: 98%; height: 300px; border: 1px solid #999;" src="' . ETHERPAD_PROVIDER . $params . '"></iframe>';
        return true;
    }
    
    private function getConfig() {
        include (dirname(__FILE__) . '/configEtherpad.php');
    }

    private function setToken() {
        global $CC_RENDER_PATH;
        $tokenPath = $CC_RENDER_PATH . '/etherpad/token/';
        if ( ! file_exists($tokenPath) )  {
            if ( ! mkdir($tokenPath, 0777, true) ) {
                throw new Exception('Error creating path "'.$tokenPath.'".');
            }

            if ( ! chmod($tokenPath, 0777) )  {
                throw new Exception('Error changing permissions on "'.$tokenPath.'".');
            }
        }
        $token = md5(microtime(true));
        $file = fopen($tokenPath . $token, "w");
        fclose($file);
        $this -> token = $token;
    }


}
