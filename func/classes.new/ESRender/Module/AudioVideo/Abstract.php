<?php

/**
 *
 *
 *
 */
abstract class ESRender_Module_AudioVideo_Abstract
extends ESRender_Module_ContentNode_Abstract {

    /**
     * Format constants
     *
     * @var string
     */
    const FORMAT_VIDEO_MP4 = 'FORMAT_VIDEO_MP4';
    const FORMAT_VIDEO_MP4_EXT = 'mp4';
    const FORMAT_VIDEO_WEBM = 'FORMAT_VIDEO_WEBM';
    const FORMAT_VIDEO_WEBM_EXT = 'webm';
    const FORMAT_AUDIO_MP3 = 'FORMAT_AUDIO_MP3';
    const FORMAT_AUDIO_MP3_EXT = 'mp3';
    const FORMAT_VIDEO_RESOLUTIONS = ['640', '1280', '1920'];

    /**
     *
     * @param string $format
     *
     * @throws Exception
     *
     * @return string
     */
    abstract protected function getOutputFilename($ext, $resolution = NULL);
   
    protected function getVideoFormats() {  
    	    	
    	switch($this->getVideoFormatByRequestingDevice()) {
    		case self::FORMAT_VIDEO_MP4:
    			return array(self::FORMAT_VIDEO_MP4, self::FORMAT_VIDEO_WEBM);
    		break;
    		case self::FORMAT_VIDEO_WEBM:
    			return array(self::FORMAT_VIDEO_WEBM, self::FORMAT_VIDEO_MP4);
    		break;
    		default:
    			return array();
    	}
    }
    
    protected function getVideoFormatByRequestingDevice() {
    	if(isset($_REQUEST['videoFormat']) && $_REQUEST['videoFormat'] == self::FORMAT_VIDEO_WEBM_EXT) {
    		return self::FORMAT_VIDEO_WEBM;
    	}
    	return self::FORMAT_VIDEO_MP4;
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
                $formats = array(self::FORMAT_AUDIO_MP3);
            break;
            default:
                $formats = $this->getVideoFormats();
        }


       	if(empty($formats))
        	return parent::process($p_kind);

        foreach ($formats as $format) {
            /*
             * if there is no output file add object to conversion queue if needed
             * for failed conversions skip this
             * */

            if($type != 'audio') {
                foreach (self::FORMAT_VIDEO_RESOLUTIONS as $resolution) {
                    $outputFilename = $this -> getOutputFilename($this->getExtensionByFormat($format), $resolution);
                    if (!file_exists($outputFilename) && !$this->esObject->conversionFailed($format)) {
                        if (!$this->esObject->inConversionQueue($format, $resolution)) {
                            $this->esObject->addToConversionQueue($format, $this->getCacheFileName(), $outputFilename, $CC_RENDER_PATH, $this->esObject->getMimeType(),$resolution);
                        }
                        //show lock screen (progress bar) but not in display mode 'window' and 'dynamic'
                        if ($formats[0] == $format && $resolution == '640' && ($p_kind != ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC && $p_kind != ESRender_Application_Interface::DISPLAY_MODE_EMBED))
                            $p_kind = ESRender_Application_Interface::DISPLAY_MODE_LOCKED;
                    }
                }
            } else {
                $outputFilename = $this -> getOutputFilename($this->getExtensionByFormat($format));
                if (!file_exists($outputFilename) && !$this->esObject->conversionFailed($format)) {
                    if (!$this->esObject->inConversionQueue($format)) {
                        $this->esObject->addToConversionQueue($format, $this->getCacheFileName(), $outputFilename, $CC_RENDER_PATH, $this->esObject->getMimeType());
                    }
                    //show lock screen (progress bar) but not in display mode 'window' and 'dynamic'
                    if ($formats[0] == $format && ($p_kind != ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC && $p_kind != ESRender_Application_Interface::DISPLAY_MODE_EMBED))
                        $p_kind = ESRender_Application_Interface::DISPLAY_MODE_LOCKED;
                }
            }
        }
        exec("php " . dirname(__FILE__) . "/Converter.php > /dev/null 2>/dev/null &");

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
