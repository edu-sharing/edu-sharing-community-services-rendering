<?php

define("LTI_VERSION", "LTI-1p0");
define("LTI_MESSAGE_TYPE", "basic-lti-launch-request");
define("OAUTH_CALLBACK", "about:blank");
define("OAUTH_VERSION", "1.0");
define("OAUTH_SIGNATURE_METHOD", "HMAC-SHA1");

class ltiTool {

    private $esobject;
    private $template;

    public function __construct($esobject, $template) {
        $this -> esobject = $esobject;
        $this -> template = $template;
    }

    private function getLaunchForm($requestData) {

        $launch_data = array();
        $launch_data["roles"] = $this -> esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}tool_instance_roles');
        $launch_data["params"] = $this -> esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}tool_instance_params');
        $launch_data["lis_person_name_given"] = $requestData['user_givenname'];
        $launch_data["lis_person_name_family"] = $requestData['user_surname'];
        $launch_data["lis_person_contact_email_primary"] = $requestData['user_email'];
        $launch_data["user_id"] = $requestData['user_id'];
        $launch_data["lti_version"] = LTI_VERSION;
        $launch_data["lti_message_type"] = LTI_MESSAGE_TYPE;
        $launch_data["oauth_callback"] = OAUTH_CALLBACK;
        $launch_data["oauth_consumer_key"] = $this -> esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}tool_instance_key');
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

        $base_string = "POST&" . urlencode($this -> esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}wwwurl')) . "&" . rawurlencode(implode("&", $launch_params));
        $secret = urlencode($this -> esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}tool_instance_secret')) . '&';
        $signature = base64_encode(hash_hmac("sha1", $base_string, $secret, true));

        $form = '<html>
            <head>
            </head>
            <body onload="document.ltiLaunchForm.submit();">
                <form id="ltiLaunchForm_'.$this->esobject->getObjectID().'" name="ltiLaunchForm_'.$this->esobject->getObjectID().'" id="name="ltiLaunchForm_'.$this->esobject->getObjectID().'"
                method="POST" action="' . $this -> esobject -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}wwwurl') . '" target="lti_frame_'.$this->esobject->getObjectID().'">';
            foreach ($launch_data as $key => $value ) {
                $form .= '<input type="hidden" name="' . $key  . '" value="' . $value . '">';
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
        $template_data['launchForm'] = $this->getLaunchForm($requestData);
        $template_data['objectId'] = $this->esobject->getObjectID();
        $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
        $template_data['applyDataProtectionRegulationsDialog'] = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($template_data['objectId'], '', '', 'LTI_INLINE');
        echo $this -> template -> render('/module/lti/inline', $template_data);
        return true;
    }

    public function dynamic(array $requestData) {
        if(Config::get('showMetadata'))
            $template_data['metadata'] = $this -> esobject -> metadatahandler -> render($this -> template, '/metadata/dynamic');
        $template_data['title'] = $this->esobject->getTitle();
        $template_data['launchForm'] = $this->getLaunchForm($requestData);
        $template_data['objectId'] = $this->esobject->getObjectID();
        $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
        $template_data['applyDataProtectionRegulationsDialog'] = $dataProtectionRegulationHandler -> getApplyDataProtectionRegulationsDialog($template_data['objectId'], '', '', 'LTI_DYNAMIC');
        echo $this -> template -> render('/module/lti/dynamic', $template_data);
        return true;
    }

    public function display(array $requestData) {}

}