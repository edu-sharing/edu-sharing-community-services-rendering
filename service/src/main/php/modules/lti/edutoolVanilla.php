<?php

require_once ("ims-blti/blti_util.php");

class edutoolVanilla {

    private $esObject;
    
    public function __construct(ESObject $esObject , $template) {
        $this -> getConfig();
        $this -> esObject = $esObject;
    }

    public function display() {
        $parms = array();
        $parms['resource_link_id'] = '123';
        $parms['lis_person_name_full'] = $this -> esObject  -> getData() -> user -> authorityName;
        $parms['lis_person_contact_email_primary'] = $this -> esObject  -> getData() -> user -> authorityName.'@uni-weimar.de';
        $parms['context_label'] = 'medien'; // = thread        
        $parms['user_id'] = $this -> esObject -> getData() ->user -> authorityName;
        
        $parms['resource_link_title'] = '';
        $parms['resource_link_description'] = '';
        $parms['roles'] = 'Learner'; // Learner, instructor
        $parms['lis_person_sourcedid'] = '';
        $parms['context_id'] = '';
        $parms['context_title'] = '';
        $parms['tool_consumer_instance_guid'] = '';
        $parms['tool_consumer_instance_description'] = '';
        $parms['custom_gotocategory'] = '';

        // Cleanup parms before we sign
        foreach ($parms as $k => $val) {
            if (strlen(trim($parms[$k])) < 1) {
                unset($parms[$k]);
            }
        }

        // Add oauth_callback to be compliant with the 1.0A spec
        $parms["oauth_callback"] = "about:blank";

        $parms = signParameters($parms, VANILLA_ENDPOINT, "POST", VANILLA_KEY, VANILLA_SECRET, "Press to Launch", $parms['tool_consumer_instance_guid'], $parms['tool_consumer_instance_description']);

        $content = postLaunchHTML($parms, VANILLA_ENDPOINT, false, false);
        //"width=\"100%\" height=\"900\" scrolling=\"auto\" frameborder=\"1\" transparency");
        print($content);

        return true;
    }

    public function embed() {
        $parms = array();
        $parms['resource_link_id'] = '123';
        $parms['lis_person_name_full'] = $this -> esObject  -> getData() -> user -> authorityName;
        $parms['lis_person_contact_email_primary'] = $this -> esObject  -> getData() -> user -> authorityName.'@uni-weimar.de';
        $parms['context_label'] = 'medien'; // = thread
        $parms['user_id'] = $this -> esObject -> getData() ->user -> authorityName;

        $parms['resource_link_title'] = '';
        $parms['resource_link_description'] = '';
        $parms['roles'] = 'Learner'; // Learner, instructor
        $parms['lis_person_sourcedid'] = '';
        $parms['context_id'] = '';
        $parms['context_title'] = '';
        $parms['tool_consumer_instance_guid'] = '';
        $parms['tool_consumer_instance_description'] = '';
        $parms['custom_gotocategory'] = '';

        // Cleanup parms before we sign
        foreach ($parms as $k => $val) {
            if (strlen(trim($parms[$k])) < 1) {
                unset($parms[$k]);
            }
        }

        // Add oauth_callback to be compliant with the 1.0A spec
        $parms["oauth_callback"] = "about:blank";

        $parms = signParameters($parms, VANILLA_ENDPOINT, "POST", VANILLA_KEY, VANILLA_SECRET, "Press to Launch", $parms['tool_consumer_instance_guid'], $parms['tool_consumer_instance_description']);

        $content = postLaunchHTML($parms, VANILLA_ENDPOINT, false, false);
        //"width=\"100%\" height=\"900\" scrolling=\"auto\" frameborder=\"1\" transparency");
        print($content);

        return true;
    }

    private function getConfig() {
        include (dirname(__FILE__) . '/configVanilla.php');
    }


}
