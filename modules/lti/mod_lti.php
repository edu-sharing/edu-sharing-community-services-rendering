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
        ESObject $p_esObject ,
        Logger $Logger,
        Phools_Template_Interface $template) {
        parent::__construct($Name, $RenderApplication,$p_esObject , $Logger, $template);
        $this -> instantiateTool($p_esObject , $template);
    }
        
    private function instantiateTool($esObject , $template) {

        //@todo factory pattern
        switch($this -> esObject -> getResourceType()) {
            case 'edutool-vanilla':
                $this -> tool = new edutoolVanilla($esObject , $template);
            break;
            case 'edutool-etherpad':
                $this -> tool = new edutoolEtherpad($esObject , $template);
            break;
            default:
                $this -> tool = new ltiTool($esObject , $template);
        }
    }

    protected function dynamic() {
        return $this -> tool -> dynamic();
    }

    protected function embed() {
        return $this -> tool -> embed();
    }
    
    protected function inline() {
        return $this -> tool -> inline();
    }
}
