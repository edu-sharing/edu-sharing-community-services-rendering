<?php

class providerEtherpad {

    private $token;

    public function __construct() {
        $this -> getConfig();
	$this -> setToken();
    }

    public function display(array $requestData) {

        $courseId = $requestData['app_id'] . '_';
        $courseId .= empty($requestData['course_id']) ? 'default' : $requestData['course_id'];
        $resource_link_id = empty($requestData['resource_id']) ? 'default' : $requestData['resource_id'];
        $userId = $requestData['user_name'];
        $fname = $requestData['user_name'];

        $params = '?fname=' . $fname . '&course_id=' . $courseId . '&resource_link_id=' . $resource_link_id . '&user_id=' . $userId .'&token=' . $this->token;

        header('HTTP/1.1 303 See other');
        header('Location: ' . ETHERPAD_PROVIDER . $params);
        return true;
        
    }
    
    public function inline(array $requestData) {
        
        $courseId = $requestData['app_id'] . '_' . $requestData['course_id'];
        $resource_link_id = $requestData['resource_id'];
        $userId = $requestData['user_name'];
        $fname = $requestData['user_name'];

        $params = '?fname=' . $fname . '&course_id=' . $courseId . '&resource_link_id=' . $resource_link_id . '&user_id=' . $userId . '&token=' . $this -> token;
        
        echo  '<iframe style="width: 98%; height: 300px; border: 1px solid #999;" src="' . ETHERPAD_PROVIDER . $params . '"></iframe>';
        
        return true;
        
    }
    
    private function getConfig() {
        include (dirname(__FILE__) . '/configEtherpad.php');
    }

    private function setToken() {
	$token = md5(microtime(true));
	$file = fopen(realpath(dirname(__FILE__)).'/'.$token, "w");
	fclose($file);
	$this -> token = $token;
    }


}
