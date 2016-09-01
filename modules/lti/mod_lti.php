<?php

//@todo autoload
require_once(dirname(__FILE__) . '/providerEtherpad.php');
require_once(dirname(__FILE__) . '/providerVanilla.php');


class mod_lti extends ESRender_Module_NonContentNode_Abstract {
    
    private $providerType = '';

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
        
        //@todo factory pattern
        switch($this->_ESOBJECT->ESOBJECT_RESOURCE_TYPE) {
            case 'edutool-vanilla':
                $this -> provider = new providerVanilla();
            break;
            case 'edutool-etherpad':
                $this -> provider = new providerEtherpad();
            break;
            default:
                throw new Exception('provider could not be determined');
        }
    }

    protected function display(array $requestData) {
        return $this -> provider -> display($requestData);
    }
    
    protected function inline(array $requestData) {
        return $this -> provider -> inline($requestData);
    }


}
