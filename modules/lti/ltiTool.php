<?php

define("LTI_VERSION", "LTI-1p0");
define("LTI_MESSAGE_TYPE", "basic-lti-launch-request");
define("OAUTH_CALLBACK", "about:blank");
define("OAUTH_VERSION", "1.0");
define("OAUTH_SIGNATURE_METHOD", "HMAC-SHA1");

class ltiTool {

    private $config = array();
    private $template;
    private $esobject;

    private $testUrl = 'http://ltiapps.net/test/tp.php';
    private $testKey = 'jisc.ac.uk';
    private $testSecret = 'secret';

    public function __construct(ESObject $esobject, Phools_Template_Interface $template) {
        $this->esobject = $esobject;
        //$properties = $esobject->renderInfoLMSReturn->getRenderInfoLMSReturn->propertiesToolInstance->item;
        $properties = array(array('key' => 'key1', 'value' => 'value1'));
        foreach($properties as $property) {
            $this->config[$property->key] = $property->value;
        }
        $this->template = $template;
    }

    private function getLaunchForm() {

        $launch_data = array(
            //"user_id" => "292832126", //nö
            //"roles" => "Instructor", // nö
            "lis_person_name_given" => "Administrator",
            "lis_person_name_family" => "",
            "lis_person_contact_email_primary" => "admin@alfresco.com",
            "resource_link_id" => $this->esobject->getObjectID(), //chatroom id / pad id etc.,
            "context_id" => "edu-sharing",
            "user_id" => "admin"
        );

        $launch_data["lti_version"] = LTI_VERSION;
        $launch_data["lti_message_type"] = LTI_MESSAGE_TYPE;

        # Basic LTI uses OAuth to sign requests
        $launch_data["oauth_callback"] = OAUTH_CALLBACK;
        $launch_data["oauth_consumer_key"] = $this->testKey;
        $launch_data["oauth_version"] = OAUTH_VERSION;
        $launch_data["oauth_nonce"] = uniqid('', true);
        $now = new \DateTime();
        $launch_data["oauth_timestamp"] = $now->getTimestamp();
        $launch_data["oauth_signature_method"] = OAUTH_SIGNATURE_METHOD;
        $launch_data_keys = array_keys($launch_data);
        sort($launch_data_keys);
        $launch_params = array();

        foreach ($launch_data_keys as $key) {
            array_push($launch_params, $key . "=" . rawurlencode($launch_data[$key]));
        }
        $base_string = "POST&" . urlencode($this->testUrl) . "&" . rawurlencode(implode("&", $launch_params));
        $secret = urlencode($this->testSecret) . '&';//urlencode($this->config['{http://www.campuscontent.de/model/1.0}tool_instance_secret']) . "&";
        $signature = base64_encode(hash_hmac("sha1", $base_string, $secret, true));

        $form = '<html>
            <head>
            </head>
            <body onload="document.ltiLaunchForm.submit();">
                <form id="ltiLaunchForm_'.$this->esobject->getObjectID().'" name="ltiLaunchForm_'.$this->esobject->getObjectID().'" id="name="ltiLaunchForm_'.$this->esobject->getObjectID().'"
                method="POST" action="'.$this->testUrl.'" target="lti_frame_'.$this->esobject->getObjectID().'">';
            foreach ($launch_data as $k => $v ) {
                $form .= '<input type="hidden" name="' . $k  . '" value="' . $v . '">';
            }
            $form .= '<input type="hidden" name="oauth_signature" value="' . $signature . '">';
            $form .= '</form>
            <body>
        </html>';
        return $form;
    }
    
    public function inline(array $requestData) {
        if(ENABLE_METADATA_INLINE_RENDERING) {
            $metadata = $this -> esobject -> metadatahandler -> render($this -> template, '/metadata/inline');
            $template_data['metadata'] = $metadata;
        }

        $license = $this->esobject->ESOBJECT_LICENSE;
        if(!empty($license)) {
            $template_data['license'] = $license -> renderFooter($this -> template);
        }

        $template_data['title'] = $this -> esobject -> getTitle();
        $template_data['launchForm'] = $this->getLaunchForm();
        $template_data['objectId'] = $this->esobject->getObjectID();
        echo $this->template -> render('/module/lti/inline', $template_data);
        return true;
    }

    public function dynamic(array $requestData) {
        if(Config::get('showMetadata'))
            $template_data['metadata'] = $this -> esobject -> metadatahandler -> render($this->template, '/metadata/dynamic');
        $template_data['title'] = $this->esobject->getTitle();
        $template_data['launchForm'] = $this->getLaunchForm();
        $template_data['objectId'] = $this->esobject->getObjectID();
        echo $this->template -> render('/module/lti/dynamic', $template_data);
        return true;
    }
}