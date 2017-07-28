<?php

require_once dirname(__FILE__) . '/ltiProvider.php';

class mod_lti extends ESRender_Module_NonContentNode_Abstract {

    public function __construct(
        $Name,
        ESRender_Application_Interface $RenderApplication,
        ESObject $p_esobject,
        Logger $Logger,
        Phools_Template_Interface $Template) {

        parent::__construct($Name, $RenderApplication,$p_esobject, $Logger, $Template);
        $this -> instantiateProvider();
    }
        
    private function instantiateProvider() {
        switch($this->_ESOBJECT->ESOBJECT_RESOURCE_TYPE) {
            case 'edutool-vanilla':
                require_once(dirname(__FILE__) . '/providerVanilla.php');
                $this -> provider = new providerVanilla();
            break;
            case 'edutool-etherpad':
                require_once(dirname(__FILE__) . '/providerEtherpad.php');
                $this -> provider = new providerEtherpad();
            break;
            default:
                $this->provider = new ltiProvider($this->_ESOBJECT, $this->getTemplate());
        }
    }

    protected function dynamic(array $requestData) {
        return $this -> provider -> dynamic($requestData);
    }

    protected function display(array $requestData) {
        return $this -> provider -> display($requestData);
    }
    
    protected function inline(array $requestData) {
        return $this -> provider -> inline($requestData);
    }


}
