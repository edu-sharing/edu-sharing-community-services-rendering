<?php

// load audio-video config
$configFile = dirname(__FILE__).'/../../../../../conf/audio-video.conf.php';
// first install -> file might not exists, init it with the example
if(!file_exists($configFile)) {
    copy(dirname(__FILE__).'/../../../../../conf/audio-video.conf.php.example', $configFile);
}
require_once($configFile);

/**
 *
 *
 *
 */
abstract class ESRender_Module_AudioVideo_Abstract
extends ESRender_Module_ContentNode_Abstract {

    /**
     *
     * @param string $format
     *
     * @throws Exception
     *
     * @return string
     */
    abstract protected function getOutputFilename($ext, $resolution = NULL);

    protected function getVideoFormatByRequestingDevice() {
        foreach (VIDEO_FORMATS as $format){
            if(isset($_REQUEST['videoFormat']) && $_REQUEST['videoFormat'] == $format) {
                return $format;
            }
        }
        return VIDEO_FORMATS[0];
    }


    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::createInstance()
     */
    final public function createInstance() {
        if (!parent::createInstance()) {
            return false;
        }
        return true;
    }

    /**
     * Checking for HTTP-header "User-Agent" allows conversion to a supported
     * audio/video-format "on-the-fly".
     *
     * (non-PHPdoc)
     * @see ESRender_Module_Base::process()
     */
    public function process($p_kind, $objectLocked = false) {

    	global $CC_RENDER_PATH;

        if ($objectLocked) {
            return parent::process(ESRender_Application_Interface::DISPLAY_MODE_LOCKED);
        }
        
        if ($p_kind == ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD) {
            return parent::process($p_kind);
        }
        
        $arr = explode("/", $this -> esObject -> getMimeType(), 2);
        $type = $arr[0];

        switch($type) {
            case 'audio':
                //$formats = array(self::FORMAT_AUDIO_MP3);
                foreach (AUDIO_FORMATS as $format) {
                    /*
                     * if there is no output file add object to conversion queue if needed
                     * for failed conversions skip this
                     * */
                    $outputFilename = $this -> getOutputFilename($format);
                    if (!file_exists($outputFilename) && !$this->esObject->conversionFailed($format)) {
                        if (!$this->esObject->inConversionQueue($format) && !$this->esObject->conversionFailed($format)) {
                            $this->esObject->addToConversionQueue($format, $this->getCacheFileName(), $outputFilename, $this->esObject->getMimeType());
                        }
                        //show lock screen (progress bar) but not in display mode 'window' and 'dynamic'
                        if (AUDIO_FORMATS[0] == $format && ($p_kind != ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC && $p_kind != ESRender_Application_Interface::DISPLAY_MODE_EMBED))
                            $p_kind = ESRender_Application_Interface::DISPLAY_MODE_LOCKED;
                    }
                }
            break;
            case 'video':
                //$formats = $this->getVideoFormats();
                foreach (VIDEO_FORMATS as $format) {
                    /*
                     * if there is no output file add object to conversion queue if needed
                     * for failed conversions skip this
                     * */
                    foreach (VIDEO_RESOLUTIONS as $resolution) {
                        $outputFilename = $this -> getOutputFilename($format, $resolution);
                        if (!file_exists($outputFilename) && !$this->esObject->conversionFailed($format)) {
                            if (!$this->esObject->inConversionQueue($format, $resolution) && $this->esObject->getId() > 0) {
                                $this->esObject->addToConversionQueue($format, $this->getCacheFileName(), $outputFilename, $this->esObject->getMimeType(),$resolution);
                            }
                            //show lock screen (progress bar) but not in display mode 'window' and 'dynamic'
                            if (VIDEO_FORMATS[0] == $format && $resolution == VIDEO_RESOLUTIONS[0] && ($p_kind != ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC && $p_kind != ESRender_Application_Interface::DISPLAY_MODE_EMBED)){
                                $p_kind = ESRender_Application_Interface::DISPLAY_MODE_LOCKED;
                            }

                        }
                    }
                }
        }


        //make it possible to start the converter in background in windows
        $cmd = "php -d display_errors=on " . dirname(__FILE__) . "/Converter.php";
        $logfile = dirname(__FILE__) . '/../../../../../log/conversion/converter.log';

        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r"));
        }
        else {
            exec($cmd . ">> ". $logfile . " 2>&1 &");
        }



        return parent::process($p_kind);
    }


    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::getTimesOfUsage()
     */
    public function getTimesOfUsage() {
        return PHP_INT_MAX;
    }

}
