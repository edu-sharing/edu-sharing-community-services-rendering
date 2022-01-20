<?php

class RemoteAppPropertyHandler {

    private $propertiesXml;
    private $exc;

    public function __construct($exc) {
        $this -> exc = $exc;
    }

    private function setHomeRepProperties() {
        try {
            $xml = $this -> getHomeRepProperties();
            $this -> propertiesXml = simplexml_load_string($xml);
        } catch(Exception $e) {
            $this -> exc -> error(install_err_fetch_props_homerep);
            return false;
        }
        
        return true;
    }
    
    public function getHomeRepProperties() {
        $url = $this -> exc -> getRepoUrl() . '/metadata?format=render';
        $context = stream_context_create(array($this -> exc -> getRepoScheme() => array('header' => 'Accept: application/xml')));
        $xml = file_get_contents($url, false, $context);
        if (!$xml) {
            //$this -> exc -> error('Error fetching ' . $url);
            return false;
        }
        return $xml;
    }

    public function setHomeRep() {

        $this -> setHomeRepProperties();

        $appId = '';
        
        foreach ($this -> propertiesXml -> entry as $entry) {
            if ($entry['key'] == 'appid')
                $appId = (string)$entry;
        }

        if (empty($appId)) {
            $this -> exc -> error('No appid found');
        }

        // save properties
        $fileName = 'app-' . $appId . '.properties.xml';
        if (!($this -> propertiesXml -> asXml(MC_BASE_DIR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . $fileName)))
            $this -> exc -> error(install_err_save_props_homerep);
       // else
          //  $this -> exc -> info(install_msg_save_props_homerep);

        //add to ccrep
        try {
            $appRegFile = MC_BASE_DIR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . 'ccapp-registry.properties.xml';
            $appReg = new DOMDocument();
            $appReg -> load($appRegFile);
            $appReg -> preserveWhiteSpace = false;
    
            $entries = $appReg -> getElementsByTagName('entry');
            foreach ($entries as $entry) {
                if ($entry -> getAttribute('key') == 'applicationfiles') {
                    $appReg -> save($appRegFile . '_' . time() . '.bak');
                    $entry -> nodeValue = $entry -> nodeValue . ',' . $fileName;
                    $appReg -> save($appRegFile);
                }
            }
            if(defined('CLI_MODE') && CLI_MODE)
                echo '[OK] Import repository properties' . PHP_EOL;
            else
                $this -> exc -> info(install_msg_add_registry_repo);
        } catch (Exception $e) {
            $this -> exc -> error(install_err_add_registry_repo);
        }

        //add to homeconf
        try {
            $xmlTmp = simplexml_load_file(MC_BASE_DIR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . 'homeApplication.properties.xml');
            $entry = $xmlTmp -> addChild("entry");
            $entry -> addAttribute("key", 'homerepid');
            $entry[0] = $appId;
            $xmlTmp -> asXML(MC_BASE_DIR . 'conf' . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . 'homeApplication.properties.xml');
        } catch(Exception $e) {
            $this -> exc -> error(install_err_add_repotohomeconfig);
        }
        
    }

}
