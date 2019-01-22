<?php

define("LTI_VERSION", "LTI-1p0");
define("LTI_MESSAGE_TYPE", "basic-lti-launch-request");
define("OAUTH_CALLBACK", "about:blank");
define("OAUTH_VERSION", "1.0");
define("OAUTH_SIGNATURE_METHOD", "HMAC-SHA1");

class ltiTool {

    private $esObject ;
    private $template;

    public function __construct(ESObject $esObject , $template) {
        $this -> esObject  = $esObject ;
        $this -> template = $template;
    }

    private function getLaunchForm() {

        $launch_data = array();
        $launch_data["roles"] = $this -> esObject  -> getNodeProperty('ccm:tool_instance_roles');
        $launch_data["params"] = $this -> esObject  -> getNodeProperty('ccm:tool_instance_params');
        $launch_data["lis_person_name_given"] = $this -> esobject -> getData() -> user -> profile -> givenName;
        $launch_data["lis_person_name_family"] = $this -> esobject -> getData() -> user -> profile -> lastName;
        $launch_data["lis_person_contact_email_primary"] = $this -> esobject -> getData() -> user -> profile -> email;
        $launch_data["user_id"] = $this -> esobject -> getData() -> user -> authorityName;
        $launch_data["lti_version"] = LTI_VERSION;
        $launch_data["lti_message_type"] = LTI_MESSAGE_TYPE;
        $launch_data["oauth_callback"] = OAUTH_CALLBACK;
        $launch_data["oauth_consumer_key"] = $this -> esObject  -> getNodeProperty('ccm:tool_instance_key');
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

        $base_string = "POST&" . urlencode($this -> esObject  -> getNodeProperty('ccm:wwwurl')) . "&" . rawurlencode(implode("&", $launch_params));
        $secret = urlencode($this -> esObject  -> getNodeProperty('ccm:tool_instance_secret')) . '&';
        $signature = base64_encode(hash_hmac("sha1", $base_string, $secret, true));

        $form = '<html>
            <head>
            </head>
            <body onload="document.ltiLaunchForm.submit();">
                <form id="ltiLaunchForm_'.$this->esObject ->getObjectID().'" name="ltiLaunchForm_'.$this->esObject ->getObjectID().'" id="name="ltiLaunchForm_'.$this->esObject ->getObjectID().'"
                method="POST" action="' . $this -> esObject  -> getNodeProperty('ccm:wwwurl') . '" target="lti_frame_'.$this->esObject ->getObjectID().'">';
            foreach ($launch_data as $key => $value ) {
                $form .= '<input type="hidden" name="' . $key  . '" value="' . $value . '">';
            }
            $form .= '<input type="hidden" name="oauth_signature" value="' . $signature . '">';
            $form .= '</form>
            <body>
        </html>';
        return $form;
    }
    
    public function inline() {
        if(ENABLE_METADATA_INLINE_RENDERING) {
            $metadata = $this -> esObject  -> getMetadatahandler() -> render($this -> template, '/metadata/inline');
            $template_data['metadata'] = $metadata;
        }
        $license = $this->esObject ->getLicense();
        if(!empty($license)) {
            $template_data['license'] = $license -> renderFooter($this -> template);
        }
        $template_data['title'] = $this -> esObject  -> getTitle();
        $template_data['launchForm'] = $this->getLaunchForm();
        $template_data['objectId'] = $this->esObject ->getObjectID();
        $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
        $template_data['applyDataProtectionRegulationsDialog'] = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($template_data['objectId'], '', '', 'LTI_INLINE');
        echo $this -> template -> render('/module/lti/inline', $template_data);
        return true;
    }

    public function dynamic() {
        if(Config::get('showMetadata'))
            $template_data['metadata'] = $this -> esObject  -> getMetadataHandler() -> render($this -> template, '/metadata/dynamic');
        $template_data['title'] = $this->esObject ->getTitle();
        $template_data['launchForm'] = $this->getLaunchForm();
        $template_data['objectId'] = $this->esObject ->getObjectID();
        $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
        $template_data['applyDataProtectionRegulationsDialog'] = $dataProtectionRegulationHandler -> getApplyDataProtectionRegulationsDialog($template_data['objectId'], '', '', 'LTI_DYNAMIC');
        echo $this -> template -> render('/module/lti/dynamic', $template_data);
        return true;
    }

    public function embed() {
        $template_data['launchForm'] = $this->getLaunchForm();
        $template_data['objectId'] = $this->esObject ->getObjectID();
        echo $this -> template -> render('/module/lti/embed', $template_data);
        return true;
    }

}