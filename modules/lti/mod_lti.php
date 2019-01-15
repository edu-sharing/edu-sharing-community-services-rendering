<?php

//@todo autoload
require_once(dirname(__FILE__) . '/edutoolEtherpad.php');
require_once(dirname(__FILE__) . '/edutoolVanilla.php');
require_once(dirname(__FILE__) . '/ltiTool.php');


class mod_lti extends ESRender_Module_NonContentNode_Abstract {

    private $tool;

    public function __construct(
        $Name,
        ESRender_Application_Interface $RenderApplication,
        ESObject $p_esobject,
        Logger $Logger,
        Phools_Template_Interface $template) {
        parent::__construct($Name, $RenderApplication,$p_esobject, $Logger, $template);
        $this -> instantiateTool($p_esobject, $template);
    }
        
    private function instantiateTool($esobject, $template) {

        //@todo factory pattern
        switch($this->_ESOBJECT->resourceType) {
            case 'edutool-vanilla':
                $this -> tool = new edutoolVanilla($esobject, $template);
            break;
            case 'edutool-etherpad':
                $this -> tool = new edutoolEtherpad($esobject, $template);
            break;
            default:
                $this -> tool = new ltiTool($esobject, $template);
        }
    }

    protected function dynamic(ESObject $ESObject) {
        return $this -> tool -> dynamic($ESObject);
    }

    protected function display(ESObject $ESObject) {
        return $this -> tool -> display($ESObject);
    }
    
    protected function inline(ESObject $ESObject) {
        return $this -> tool -> inline($ESObject);
    }
}
