<?php
/**
 * This product Copyright 2010 metaVentis GmbH.  For detailed notice,
 * see the "NOTICE" file with this distribution.
 *
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

require_once (dirname(__FILE__) . '/../../conf.inc.php');

/**
 *
 * @author steffen gross / matthias hupfer / steffen hippeli
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_picture
extends ESRender_Module_ContentNode_Abstract {

    const FORMAT_IMAGE_RESOLUTIONS_L = 1920;
    const FORMAT_IMAGE_RESOLUTIONS_M = 1280;
    const FORMAT_IMAGE_RESOLUTIONS_S = 640;
    const EXTENSION_GIF = 'gif';
    const EXTENSION_JPEG = 'jpeg';
    const EXTENSION_PNG = 'png';
    const EXTENSION_SVG = 'svg';
    const EXTENSION_WEBP = 'webp';

    /**
     *
     * @param string $SourceFile
     * @param string $DestinationFile
     *
     * @return bool
     */
    protected function convertImage($SourceFile, $DestinationFile) {
        $Logger = $this -> getLogger();
        try {

            if($this->isSvg($SourceFile)) {
                copy($SourceFile, $DestinationFile . '.' . self::EXTENSION_SVG);
                return true;
            }

            if($this->isGif($SourceFile)) {
                copy($SourceFile, $DestinationFile . '.' . self::EXTENSION_GIF);
                return true;
            }

            list($origWidth, $origHeight, $type) = getimagesize($SourceFile);

            switch ($type) {
                case IMAGETYPE_GIF:
                    $tmpFile = imagecreatefromgif($SourceFile);
                    break;
                case IMAGETYPE_JPEG:
                    $tmpFile = imagecreatefromjpeg($SourceFile);
                    break;
                case IMAGETYPE_PNG:
                    $tmpFile = imagecreatefrompng($SourceFile);
                    break;
                case IMAGETYPE_WEBP:
                    $tmpFile = imagecreatefromwebp($SourceFile);
                    break;
                case IMAGETYPE_BMP:
                    $tmpFile = imagecreatefrombmp($SourceFile);
                    break;
                default :
                    throw new Exception('Cannot create temporary image file');
            }


            $ratio = $origHeight / $origWidth;

            //detect longest side to work with
            $origLong = $origWidth;
            if($ratio > 1) {
                $origLong = $origHeight;
            }

            foreach(array(self::FORMAT_IMAGE_RESOLUTIONS_S, self::FORMAT_IMAGE_RESOLUTIONS_M, self::FORMAT_IMAGE_RESOLUTIONS_L, $origLong) as $l) {

                //do not upscale
                if($l > $origLong) {
                    continue;
                }

                //handle portrait and landscape format
                if($ratio > 1) {
                    $height = $l;
                    $width = round($height / $ratio);
                } else {
                    $width = $l;
                    $height = round($width * $ratio);
                }

                $newImage = imagecreatetruecolor($width, $height);
                imageAlphaBlending($newImage, false);
                imageSaveAlpha($newImage, true);
                if (!imagecopyresampled($newImage, $tmpFile, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight))
                    throw new Exception('Cannot resample image to ' . $width);
                $Logger->debug('Resampled picture (' . $width . ' px x ' . $height . ' px).');


                $exif = @exif_read_data($SourceFile);
                if (!empty($exif['IFD0'])) {
                    $orientation = $exif['IFD0']['Orientation'];
                }else{
                    $orientation = $exif['Orientation'];
                }

                switch ($orientation) {
                    case 3:
                        $newImage = imagerotate($newImage, 180, 0);
                        break;
                    case 6:
                        $newImage = imagerotate($newImage, -90, 0);
                        break;
                    case 8:
                        $newImage = imagerotate($newImage, 90, 0);
                        break;
                }


                $conversionSuccess = false;


                /*
                 * for max compatibility convert all formats to png except jpeg (svg skipped earlier)
                 */
                switch ($type) {
                    case IMAGETYPE_JPEG:
                        $conversionSuccess = imagejpeg($newImage, $DestinationFile . '_' . $l . '.' . $this -> getFileExtension($type));
                        $conversionSuccess = imagepng($newImage, $DestinationFile . '_' . $l . '.' . self::EXTENSION_PNG);
                        break;
                    default :
                        $conversionSuccess = imagepng($newImage, $DestinationFile . '_' . $l . '.' . $this -> getFileExtension($type));
                }

                if (!$conversionSuccess)
                    throw new Exception('Cannot resize/convert image');
                imagedestroy($newImage);

                $Logger->debug('Resized/converted image to ' . $this -> getFileExtension($type));
            }

        } catch (Exception $e) {
            $Logger -> debug($e -> getMessage());
            return false;
        }
        return true;
    }

    private function getFileExtension($type = null, $file = null) {

        if($type === null && $file === null){
            throw new Exception('Either a type or file must be provided.');
        }

        if($file) {
            if($this->isSvg($file)){
                return self::EXTENSION_SVG;
            }

            if($this->isGif($file)){
                return self::EXTENSION_GIF;
            }
            
            $type = exif_imagetype ($file);
        }

        switch ($type) {
            case IMAGETYPE_JPEG:
                return self::EXTENSION_JPEG;
            case IMAGETYPE_GIF:
                return self::EXTENSION_GIF;
            default:
                return self::EXTENSION_PNG;
        }
    }

    /**
     *
     * @return string
     */
    protected function getImageFilename() {
        return $this -> esObject -> getFilePath();
    }

    protected function renderTemplate($TemplateName, $getDefaultData = true, $showMetadata = true) {
        if($getDefaultData)
        	$template_data = parent::prepareRenderData($showMetadata);
        $template_data['title'] = $this -> esObject -> getTitle();
        $template_data['image_url'] = $this -> getImageUrl($_REQUEST['width']);
        $Template = $this -> getTemplate();
        $rendered = $Template -> render($TemplateName, $template_data);

        return $rendered;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::createInstance()
     */
    final public function createInstance() {
        if (!parent::createInstance()) {
            return false;
        }

        $f_path = $this -> esObject -> getFilePath();
        $ObjectFilename = str_replace('\\', '/', $f_path);

        if(!empty($this->esObject->getNode()->remote)) {
            if ($this->esObject->getNode()->remote->repository->repositoryType == 'PIXABAY' && filesize($ObjectFilename) == 0) {
                $ObjectFilename = $this->esObject->getNode()->properties->{'ccm:thumbnailurl'}[0];
            }
        }
        return $this -> convertImage($ObjectFilename, $this -> getImageFilename());
    }

    public function isSvg($filePath) {
        return strpos(@mime_content_type($filePath), 'image/svg') !== false;
    }

    public function isGif($filePath) {
        return strpos(@mime_content_type($filePath), 'image/gif') !== false;
    }

    private function getImageUrl($width = null) {
        $fileExtension = $this -> getFileExtension(null, $this -> esObject -> getFilePath());
        return $this -> esObject -> getPath() . $this -> getFlavour($width, $fileExtension) . '.' . $fileExtension . '?' . session_name() . '=' . session_id().'&token=' . Config::get('token');
    }

    /*
     * Detect which flavor to show
     * */
    private function getFlavour($width, $fileExtension) {
        global $CC_RENDER_PATH;

        if($fileExtension === self::EXTENSION_SVG || $fileExtension === self::EXTENSION_GIF)
            return '';

        $flavours = array();
        $tmpFile = '';

        //get all available flavours
         $files = scandir($CC_RENDER_PATH . DIRECTORY_SEPARATOR . $this -> getName() . DIRECTORY_SEPARATOR . $this -> esObject -> getSubPath());
         foreach($files as $file) {
             if(strpos($file, '.' . $fileExtension) !== false) {
                 if(empty($tmpFile))
                    $tmpFile = $file;
                $array = explode('_', str_replace('.' . $fileExtension, '', $file));
                $cacheVersion = $array[count($array) - 2];
                if($this -> esObject -> getVersion() == $cacheVersion) {
                    $flavours[] = intval(end($array));
                }
            }
        }

         if(empty($flavours))
             throw new Exception('No image binary found.');

        rsort($flavours);

         //default if width > available resolution
         $flavor = $flavours[0];

        /*
         * handle dynamic and embed display mode
         *
         * set default width
        */
         if(empty($width)) {
             list($width, $height) = getimagesize($CC_RENDER_PATH . DIRECTORY_SEPARATOR . $this -> getName() . DIRECTORY_SEPARATOR . $this -> esObject -> getSubPath() . DIRECTORY_SEPARATOR . $tmpFile);
             if($width > $height) {
                 $width = self::FORMAT_IMAGE_RESOLUTIONS_M;
             } else {
                 $width = self::FORMAT_IMAGE_RESOLUTIONS_S * $width / $height;
             }
         }

         //find best matching flavour
         foreach($flavours as $f) {
             if(intval($width) <= $f)
                 $flavor = $f;
         }

         return '_' . $flavor;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::inline()
     */
    protected function inline() {
        echo $this -> renderTemplate('/module/picture/inline');
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::dynamic()
     */
    protected function dynamic() {
    	$template_data['image_url'] = $this -> getImageUrl();

    	if(Config::get('showMetadata'))
	    	$template_data['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');

	    $template_data['title'] = $this -> esObject->getTitle();
    	echo $this -> getTemplate() -> render('/module/picture/dynamic', $template_data);
    	return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::dynamic()
     */
    protected function embed() {
        $template_data = parent::prepareRenderData(false);
        $template_data['image_url'] = $this -> getImageUrl();
        echo $this -> getTemplate() -> render('/module/picture/embed', $template_data);
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::getTimesOfUsage()
     */
    public function getTimesOfUsage() {
        return 20;
    }
}
