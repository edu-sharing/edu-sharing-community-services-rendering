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

    /**
     *
     * @param string $format
     *
     * @throws Exception
     *
     * @return string
     */
    abstract protected function getOutputFilename($ext);
   
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
    final public function createInstance(array $requestData) {
        if (!parent::createInstance($requestData)) {
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
    public function process($p_kind, array $requestData, $objectLocked = false) {

    	global $CC_RENDER_PATH;
    	
        if ($objectLocked) {
            return parent::process(ESRender_Application_Interface::DISPLAY_MODE_LOCKED, $requestData);
        }
        
        if ($p_kind == ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD) {
            return parent::process($p_kind, $requestData);
        }
        
        $arr = explode("/", $this -> _ESOBJECT -> getMimeType(), 2);
        $type = $arr[0];
        
        switch($type) {
            case 'audio':
                $formats = array(self::FORMAT_AUDIO_MP3);
            break;
            default:
                $formats = $this->getVideoFormats();    
        }

        
       	if(empty($formats))
        	return parent::process($p_kind, $requestData);

        foreach ($formats as $format) {
            $output_filename = $this -> getOutputFilename($this->getExtensionByFormat($format));

            /*
             * Throw an exception if conversion to the requested format failed earlier
             * */
            if ($formats[0] == $format && $this -> _ESOBJECT -> conversionFailed($format))
                throw new ESRender_Exception_Conversion('Could not convert Object ' . $this -> _ESOBJECT ->getId() . ' to ' . $format);

            /*
             * if there is no output file add object to conversion queue if needed
             * for failed conversions skip this
             * */
            if (!file_exists($output_filename) && !$this -> _ESOBJECT -> conversionFailed($format)) {
                if (!$this -> _ESOBJECT -> inConversionQueue($format)) {
                    $this -> _ESOBJECT -> addToConversionQueue($format, DIRECTORY_SEPARATOR, $this -> getCacheFileName(), $this -> getOutputFilename($this->getExtensionByFormat($format)), $CC_RENDER_PATH, $this -> _ESOBJECT -> getMimeType());
                }
                //show lock screen (progress bar) but not in display mode 'window' and 'dynamic'
                if ($formats[0] == $format && ($p_kind != ESRender_Application_Interface::DISPLAY_MODE_WINDOW && $p_kind != ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC))
                    $p_kind = ESRender_Application_Interface::DISPLAY_MODE_LOCKED;
            }
        }
        exec("php " . dirname(__FILE__) . "/Converter.php > /dev/null 2>/dev/null &");
        return parent::process($p_kind, $requestData);
    }


    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::getTimesOfUsage()
     */
    public function getTimesOfUsage() {
        return PHP_INT_MAX;
    }

}
