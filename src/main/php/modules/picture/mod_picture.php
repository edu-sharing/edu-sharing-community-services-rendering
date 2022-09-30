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

            list($origWidth, $origHeight, $type) = getimagesize($SourceFile);

            switch ($type) {
                case IMAGETYPE_GIF:
                    $nGif = new GIF_eXG($SourceFile,0);
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
                    //php gd image creation from bmp is not implemented
                    $tmpFile = $this -> ImageCreateFromBMP($SourceFile);
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

                /*if gif, use separate rescale function and continue*/
                if($type == IMAGETYPE_GIF) {
                    $conversionSuccess = $nGif->resize($DestinationFile . '_' . $l . '.' . $this -> getFileExtension($type), $width, $height, 1, 1);
                    if (!$conversionSuccess)
                        throw new Exception('Cannot resize gif');
                    $Logger->debug('Resized gif');
                    continue;
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

        if ($this -> esObject -> getNode() -> remote -> repository -> repositoryType == 'PIXABAY' && filesize($ObjectFilename) == 0) {
            $ObjectFilename = $this -> esObject -> getNode() -> properties -> {'ccm:thumbnailurl'}[0];
        }
        return $this -> convertImage($ObjectFilename, $this -> getImageFilename());
    }

    public function isSvg($filePath) {
        return strpos(@mime_content_type($filePath), 'image/svg') !== false;
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

        if($fileExtension === self::EXTENSION_SVG)
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
                 $flavours[] = intval(end($array));
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


    private function ImageCreateFromBMP($filename)
    # ========================================================================#
    #
    #  This work is licensed under the Creative Commons Attribution 3.0 Unported
    #  License. To view a copy of this license,
    #  visit http://creativecommons.org/licenses/by/3.0/ or send a letter to
    #  Creative Commons, 444 Castro Street, Suite 900, Mountain View, California,
    #  94041, USA.
    #
    #  All rights reserved.
    #
    #  Author:    Jarrod Oberto
    #  Version:   1.5.1
    #  Date:      10-05-11
    #  Purpose:   Provide tools for image manipulation using GD
    #  Param In:  See functions.
    #  Param Out: Produces a resized image
    #  Requires : Requires PHP GD library.
    #  Usage Example:
    #                     include("lib/php_image_magician.php");
    #                     $magicianObj = new resize('images/car.jpg');
    #                     $magicianObj -> resizeImage(150, 100, 0);
    #                     $magicianObj -> saveImage('images/car_small.jpg', 100);
    #
    #        - See end of doc for more examples -
    #
    #  Supported file types include: jpg, png, gif, bmp, psd (read)
    #
    #
    #
    #  The following functions are taken from phpThumb() [available from
    #    http://phpthumb.sourceforge.net], and are used with written permission
    #  from James Heinrich.
    #    - GD2BMPstring
    #      - GetPixelColor
    #      - LittleEndian2String
    #
    #  The following functions are from Marc Hibbins and are used with written
    #  permission (are also under the Attribution-ShareAlike
    #  [http://creativecommons.org/licenses/by-sa/3.0/] license.
    #    -
    #
    #  PhpPsdReader is used with written permission from Tim de Koning.
    #  [http://www.kingsquare.nl/phppsdreader]
    #
    #
    #
    #  Modificatoin history
    #  Date      Initials  Ver Description
    #  10-05-11  J.C.O   0.0 Initial build
    #  01-06-11  J.C.O   0.1.1   * Added reflections
    #              * Added Rounded corners
    #              * You can now use PNG interlacing
    #              * Added shadow
    #              * Added caption box
    #              * Added vintage filter
    #              * Added dynamic image resizing (resize on the fly)
    #              * minor bug fixes
    #  05-06-11  J.C.O   0.1.1.1 * Fixed undefined variables
    #  17-06-11  J.C.O   0.1.2   * Added image_batch_class.php class
    #              * Minor bug fixes
    #  26-07-11  J.C.O   0.1.4 * Added support for external images
    #              * Can now set the crop poisition
    #  03-08-11  J.C.O   0.1.5 * Added reset() method to reset resource to
    #                original input file.
    #              * Added method addTextToCaptionBox() to
    #                simplify adding text to a caption box.
    #              * Added experimental writeIPTC. (not finished)
    #              * Added experimental readIPTC. (not finished)
    #  11-08-11  J.C.O     * Added initial border presets.
    #  30-08-11  J.C.O     * Added 'auto' crop option to crop portrait
    #                images near the top.
    #  08-09-11  J.C.O     * Added cropImage() method to allow standalone
    #                cropping.
    #  17-09-11  J.C.O     * Added setCropFromTop() set method - set the
    #                percentage to crop from the top when using
    #                crop 'auto' option.
    #              * Added setTransparency() set method - allows you
    #                to turn transparency off (like when saving
    #                as a jpg).
    #              * Added setFillColor() set method - set the
    #                background color to use instead of transparency.
    #  05-11-11  J.C.O   0.1.5.1 * Fixed interlacing option
    #  0-07-12  J.C.O   1.0
    #
    #  Known issues & Limitations:
    # -------------------------------
    #  Not so much an issue, the image is destroyed on the deconstruct rather than
    #  when we have finished with it. The reason for this is that we don't know
    #  when we're finished with it as you can both save the image and display
    #  it directly to the screen (imagedestroy($this->imageResized))
    #
    #  Opening BMP files is slow. A test with 884 bmp files processed in a loop
    #  takes forever - over 5 min. This test inlcuded opening the file, then
    #  getting and displaying its width and height.
    #
    #  $forceStretch:
    # -------------------------------
    #  On by default.
    #  $forceStretch can be disabled by calling method setForceStretch with false
    #  parameter. If disabled, if an images original size is smaller than the size
    #  specified by the user, the original size will be used. This is useful when
    #  dealing with small images.
    #
    #  If enabled, images smaller than the size specified will be stretched to
    #  that size.
    #
    #  Tips:
    # -------------------------------
    #  * If you're resizing a transparent png and saving it as a jpg, set
    #  $keepTransparency to false with: $magicianObj->setTransparency(false);
    #
    #  FEATURES:
    #    * EASY TO USE
    #    * BMP SUPPORT (read & write)
    #    * PSD (photoshop) support (read)
    #    * RESIZE IMAGES
    #      - Preserve transparency (png, gif)
    #      - Apply sharpening (jpg) (requires PHP >= 5.1.0)
    #      - Set image quality (jpg, png)
    #      - Resize modes:
    #        - exact size
    #        - resize by width (auto height)
    #        - resize by height (auto width)
    #        - auto (automatically determine the best of the above modes to use)
    #        - crop - resize as best as it can then crop the rest
    #      - Force stretching of smaller images (upscale)
    #    * APPLY FILTERS
    #      - Convert to grey scale
    #      - Convert to black and white
    #      - Convert to sepia
    #      - Convert to negative
    #    * ROTATE IMAGES
    #      - Rotate using predefined "left", "right", or "180"; or any custom degree amount
    #    * EXTRACT EXIF DATA (requires exif module)
    #      - make
    #      - model
    #      - date
    #      - exposure
    #      - aperture
    #      - f-stop
    #      - iso
    #      - focal length
    #      - exposure program
    #      - metering mode
    #      - flash status
    #      - creator
    #      - copyright
    #    * ADD WATERMARK
    #      - Specify exact x, y placement
    #      - Or, specify using one of the 9 pre-defined placements such as "tl"
    #        (for top left), "m" (for middle), "br" (for bottom right)
    #        - also specify padding from edge amount (optional).
    #      - Set opacity of watermark (png).
    #    * ADD BORDER
    #    * USE HEX WHEN SPECIFYING COLORS (eg: #ffffff)
    #    * SAVE IMAGE OR OUTPUT TO SCREEN
    #
    #
    # ========================================================================#
    # Author:     DHKold
    # Date:     The 15th of June 2005
    # Version:    2.0B
    # Purpose:    To create an image from a BMP file.
    # Param in:   BMP file to open.
    # Param out:  Return a resource like the other ImageCreateFrom functions
    # Reference:  http://us3.php.net/manual/en/function.imagecreate.php#53879
    # Bug fix:    Author:   domelca at terra dot es
    #       Date:   06 March 2008
    #       Fix:    Correct 16bit BMP support
    # Notes:
    #
    {

        //Ouverture du fichier en mode binaire
        if (!$f1 = fopen($filename, "rb"))
            return FALSE;

        //1 : Chargement des ent�tes FICHIER
        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
        if ($FILE['file_type'] != 19778)
            return FALSE;

        //2 : Chargement des ent�tes BMP
        $BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
        $BMP['colors'] = pow(2, $BMP['bits_per_pixel']);

        if ($BMP['size_bitmap'] == 0)
            $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];

        $BMP['bytes_per_pixel'] = $BMP['bits_per_pixel'] / 8;
        $BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
        $BMP['decal'] = ($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] -= floor($BMP['width'] * $BMP['bytes_per_pixel'] / 4);
        $BMP['decal'] = 4 - (4 * $BMP['decal']);

        if ($BMP['decal'] == 4)
            $BMP['decal'] = 0;

        //3 : Chargement des couleurs de la palette
        $PALETTE = array();
        if ($BMP['colors'] < 16777216) {
            $PALETTE = unpack('V' . $BMP['colors'], fread($f1, $BMP['colors'] * 4));
        }

        //4 : Cr�ation de l'image
        $IMG = fread($f1, $BMP['size_bitmap']);
        $VIDE = chr(0);

        $res = imagecreatetruecolor($BMP['width'], $BMP['height']);
        $P = 0;
        $Y = $BMP['height'] - 1;
        while ($Y >= 0) {
            $X = 0;
            while ($X < $BMP['width']) {
                if ($BMP['bits_per_pixel'] == 24)
                    $COLOR = unpack("V", substr($IMG, $P, 3) . $VIDE);
                elseif ($BMP['bits_per_pixel'] == 16) {

                    /*
                     * BMP 16bit fix
                     * =================
                     *
                     * Ref: http://us3.php.net/manual/en/function.imagecreate.php#81604
                     *
                     * Notes:
                     * "don't work with bmp 16 bits_per_pixel. change pixel
                     * generator for this."
                     *
                     */

                    // *** Original code (don't work)
                    //$COLOR = unpack("n",substr($IMG,$P,2));
                    //$COLOR[1] = $PALETTE[$COLOR[1]+1];

                    $COLOR = unpack("v", substr($IMG, $P, 2));
                    $blue = ($COLOR[1] & 0x001f)<<3;
                    $green = ($COLOR[1] & 0x07e0)>>3;
                    $red = ($COLOR[1] & 0xf800)>>8;
                    $COLOR[1] = $red * 65536 + $green * 256 + $blue;

                } elseif ($BMP['bits_per_pixel'] == 8) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, $P, 1));
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 4) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 2) % 2 == 0)
                        $COLOR[1] = ($COLOR[1]>>4);
                    else
                        $COLOR[1] = ($COLOR[1] & 0x0F);
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } elseif ($BMP['bits_per_pixel'] == 1) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 8) % 8 == 0)
                        $COLOR[1] = $COLOR[1]>>7;
                    elseif (($P * 8) % 8 == 1)
                        $COLOR[1] = ($COLOR[1] & 0x40)>>6;
                    elseif (($P * 8) % 8 == 2)
                        $COLOR[1] = ($COLOR[1] & 0x20)>>5;
                    elseif (($P * 8) % 8 == 3)
                        $COLOR[1] = ($COLOR[1] & 0x10)>>4;
                    elseif (($P * 8) % 8 == 4)
                        $COLOR[1] = ($COLOR[1] & 0x8)>>3;
                    elseif (($P * 8) % 8 == 5)
                        $COLOR[1] = ($COLOR[1] & 0x4)>>2;
                    elseif (($P * 8) % 8 == 6)
                        $COLOR[1] = ($COLOR[1] & 0x2)>>1;
                    elseif (($P * 8) % 8 == 7)
                        $COLOR[1] = ($COLOR[1] & 0x1);
                    $COLOR[1] = $PALETTE[$COLOR[1] + 1];
                } else
                    return FALSE;

                imagesetpixel($res, $X, $Y, $COLOR[1]);
                $X++;
                $P += $BMP['bytes_per_pixel'];
            }

            $Y--;
            $P += $BMP['decal'];
        }

        //Fermeture du fichier
        fclose($f1);

        return $res;
    }
}


// from https://github.com/Yuriy-Khomenko/GIF_eXG/blob/master/gif_exg.php

/**********************************************************************************
PASSPORT OF CLASS
Name: GIF_eXG
Current version: 1.081
Appointment: resize gif image file with support animation and transparency
Features: fast, stable and correct work with most files, ease of use

History of modification:
- 1.00 basic functionality
- 1.01 bag fix
- 1.02 fast resize, overall optization and first release
- 1.03 bag fix (thanks for council of aAotD)
- 1.04 small fix (support not standart file formats)
- 1.05 fix (added: support new not standart file formats; optization code, thanks for council of AvrGavr)
- 1.06 correct handling files with error sizes of local frame
- 1.07 correct resampled (on request MasterShredder)
- 1.08 timing fix
- 1.081 small code fix

Author: Yuriy Khomenko
Year of development: 2013
Country: Ukraine

Developed and test:
- PHP 5.3.13/5.5.4
- GD 2.0.34/2.1.0
- OS Windows/Linux

Attention and comment:
- class can be used for personal and commercial purposes
- class is allowed to change or modify
- i will be glad if you class will come in handy

How to use:
1) require_once "gif_exg.php";	- include a library file

2) $nGif = new GIF_eXG($source_file,$opt);	- create an instance of the class
- $source_file: full path to the source file
- $opt: "1" - use optization
"0" - not use optization (file will retain the internal structure)

3) $nGif->resize($dest_file,$new_width,$new_height,$symmetry,$resampled);	- public function for changing the size of (returns NULL on failure)
- $dest_file: full path to the destination file
- $new_width: new image width
- $new_height: new image height
- $symmetry:  "1" - preserve symmetry
"0" - not preserve symmetry
- $resampled: "1" - use resampled
"0" - not use resampled

Example:

require_once "gif_exg.php";
$nGif = new GIF_eXG("../image/src.gif",1);
$nGif->resize("../image/dst1.gif",180,180,1,1);
$nGif->resize("../image/dst2.gif",150,150,0,1);

 **********************************************************************************/

class FRM {
    var $pos_x, $pos_y, $width_f, $height_f, $tr_frm = 0, $lc_mod, $gr_mod, $off_xy, $head, $lc_palet, $image;
    function FRM($lc_mod, $lc_palet, $image, $head, $pzs_xy, $gr_mod) {
        $this->lc_mod = $lc_mod;
        $this->lc_palet = $lc_palet;
        $this->image = $image;
        $this->head = $head;
        $this->pos_x = $pzs_xy[0];
        $this->pos_y = $pzs_xy[1];
        $this->width_f = $pzs_xy[2];
        $this->height_f = $pzs_xy[3];
        $this->gr_mod = $gr_mod;
        $this->tr_frm = ord($gr_mod[3]) & 1 ? 1 : 0;
    }
}

class GIF_eXG {
    private $gif, $pnt = 0, $gl_mn, $gl_palet, $gl_mod, $gl_mode, $int_w, $int_h, $au = 0, $er = 0, $nt = 0, $lp_frm = 0, $ar_frm = Array(), $gn_fld = Array(), $dl_frmf = Array(), $dl_frms = Array();
    function GIF_eXG($file_src, $opt) {
        $this->gif = file_get_contents($file_src);
        $this->gl_mn = $this->gtb(13);
        if (substr($this->gl_mn, 0, 3) != "GIF") {
            $this->er = 1;
            return 0;
        }$this->int_w = $this->rl_int($this->gl_mn[6] . $this->gl_mn[7]);
        $this->int_h = $this->rl_int($this->gl_mn[8] . $this->gl_mn[9]);
        if (($vt = ord($this->gl_mn[10])) & 128 ? 1 : 0) {
            $this->gl_palet = $this->gtb(pow(2, ($vt & 7) + 1) * 3);
        }$buffer_add = "";
        if($this->gif[$this->pnt] == "\x21"){
            while ($this->gif[$this->pnt + 1] != "\xF9" && $this->gif[$this->pnt] != "\x2C") {
                switch ( $this->gif[$this->pnt + 1] ) {
                    case "\xFE":
                        $sum = 2;
                        while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                            $sum+=$lc_i + 1;
                        }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
                        break;
                    case "\xFF":
                        $sum = 14;
                        while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                            $sum+=$lc_i + 1;
                        }$buffer_add.=$this->gtb($sum + 1);
                        break;
                    case "\x01":
                        $sum = 15;
                        while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                            $sum+=$lc_i + 1;
                        }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);

                }}$this->gl_mod = $buffer_add;
        }
        while ($this->gif[$this->pnt] != "\x3B" && $this->gif[$this->pnt + 1] != "\xFE" && $this->gif[$this->pnt + 1] != "\xFF" && $this->gif[$this->pnt + 1] != "\x01") {
            $lc_mod;
            $lc_palet;
            $pzs_xy = Array();
            $head;
            $gr_mod;
            $this->lp_frm++;
            while ($this->gif[$this->pnt] != "\x2C") {
                switch ($this->gif[$this->pnt + 1]) {
                    case "\xF9":
                        $this->gn_fld[] = $this->gif[$this->pnt + 3];
                        $this->dl_frmf[] = $this->gif[$this->pnt + 4];
                        $this->dl_frms[] = $this->gif[$this->pnt + 5];
                        $gr_mod = $buffer_add = $this->gtb(8);
                        break;
                    case "\xFE":
                        $sum = 2;
                        while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                            $sum+=$lc_i + 1;
                        }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
                        break;
                    case "\xFF":
                        $sum = 14;
                        while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                            $sum+=$lc_i + 1;
                        }if (substr($tmp_buf = $this->gtb($sum + 1), 3, 8) == "NETSCAPE") {
                        if (!$this->nt) {
                            $this->nt = 1;
                            $this->gl_mod.=$tmp_buf;
                        }
                    } else {
                        $buffer_add.=$tmp_buf;
                    }
                        break;
                    case "\x01":
                        $sum = 15;
                        while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                            $sum+=$lc_i + 1;
                        }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
                }
            }$lc_mod = $buffer_add;
            $pzs_xy[] = $this->ms_int(1, 2);
            $pzs_xy[] = $this->ms_int(3, 2);
            $pzs_xy[] = $this->ms_int(5, 2);
            $pzs_xy[] = $this->ms_int(7, 2);
            $head = $this->gtb(10);
            if((($pzs_xy[0] + $pzs_xy[2])-$this->int_w)>0){
                $head[1]= "\x00";
                $head[2]= "\x00";
                $head[5]= $this->int_raw($this->int_w);
                $head[6]= "\x00";

                $pzs_xy[0]=0;
                $pzs_xy[2]=$this->int_w;
            }
            if((($pzs_xy[1] + $pzs_xy[3])-$this->int_h)>0){
                $head[3]= "\x00";
                $head[4]= "\x00";
                $head[7]= $this->int_raw($this->int_h);
                $head[8]= "\x00";
                $pzs_xy[1]=0;
                $pzs_xy[3]=$this->int_h;
            }
            if ((ord($this->gif[$this->pnt - 1]) & 128 ? 1 : 0)) {
                $lc_i = pow(2, (ord($this->gif[$this->pnt - 1]) & 7) + 1) * 3;
                $lc_palet = $this->gtb($lc_i);
            }$sum = 0;
            $this->pnt++;
            while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                $sum+=$lc_i + 1;
            }$this->pnt--;
            $this->ar_frm[] = new FRM($lc_mod, $lc_palet, $this->gtb($sum + 2), $head, $pzs_xy, $gr_mod);
        }$buffer_add = "";
        while ($this->gif[$this->pnt] != "\x3B") {
            switch ($this->gif[$this->pnt + 1]){
                case "\xFE":
                    $sum = 2;
                    while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                        $sum+=$lc_i + 1;
                    }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
                    if ($sum == 17) {
                        $this->au = 1;
                    }
                    break;
                case "\xFF":
                    $sum = 14;
                    while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                        $sum+=$lc_i + 1;
                    }$buffer_add.=$this->gtb($sum + 1);
                    break;
                case "\x01":
                    $sum = 15;
                    while (($lc_i = ord($this->gif[$this->pnt + $sum])) != 0x00) {
                        $sum+=$lc_i + 1;
                    }$opt ? $this->gtb($sum + 1) : $buffer_add.=$this->gtb($sum + 1);
            }
        }$this->gl_mode = $buffer_add;
        $this->gif = "";
    }
    private function gtb($n) {
        $b = substr($this->gif, $this->pnt, $n);
        $this->pnt+=$n;
        return $b;
    }
    private function rl_int($hw) {
        $z = ord($hw[1]) << 8;
        $c = ord($hw[0]);
        $x = $z | $c;
        return $x;
    }
    private function ms_int($g_f, $g_s) {
        return $this->rl_int(substr($this->gif, $this->pnt + $g_f, $g_s));
    }
    private function int_raw($t) {
        return chr($t & 255) . chr(($t & 0xFF00) >> 8);
    }
    private function cr_img($i) {
        return $this->gl_mn . $this->gl_palet . $this->gl_mod . $this->ar_frm[$i]->lc_mod . $this->ar_frm[$i]->head . $this->ar_frm[$i]->lc_palet . $this->ar_frm[$i]->image . "\x3B";
    }
    private function resize_img($b, $ind_f, $des) {
        $buf_n = round($this->ar_frm[$ind_f]->width_f * $des[0]);
        $n_width = $buf_n ? $buf_n : 1;
        $buf_n = round($this->ar_frm[$ind_f]->height_f * $des[1]);
        $n_height = $buf_n ? $buf_n : 1;
        $n_pos_x = round($this->ar_frm[$ind_f]->pos_x * $des[0]);
        $n_pos_y = round($this->ar_frm[$ind_f]->pos_y * $des[1]);
        $this->ar_frm[$ind_f]->off_xy = $this->int_raw($n_pos_x) . $this->int_raw($n_pos_y);
        $str_img = @imagecreatefromstring($b);
        if ($this->lp_frm == 1 || $des[3]) {
            $img_s = @imagecreatetruecolor($n_width, $n_height);
        } else {
            $img_s = @imagecreate($n_width, $n_height);
        }if ($this->ar_frm[$ind_f]->tr_frm) {
            $in_trans = @imagecolortransparent($str_img);
            if ($in_trans >= 0 && $in_trans < @imagecolorstotal($img_s)) {
                $tr_clr = @imagecolorsforindex($str_img, $in_trans);
            }if ($this->lp_frm == 1 || $des[3]) {
                $n_trans = @imagecolorallocatealpha($img_s, 255, 255, 255, 127);
            } else {
                $n_trans = @imagecolorallocate($img_s, $tr_clr['red'], $tr_clr['green'], $tr_clr['blue']);
            }@imagecolortransparent($img_s, $n_trans);
            @imagefill($img_s, 0, 0, $n_trans);
        }@imagecopyresampled($img_s, $str_img, 0, 0, 0, 0, $n_width, $n_height, $this->ar_frm[$ind_f]->width_f, $this->ar_frm[$ind_f]->height_f);
        @ob_start();
        @imagegif($img_s);
        $t_img = ob_get_clean();
        @ob_end_clean();
        @imagedestroy($str_img);
        @imagedestroy($img_s);

        return $t_img;
    }
    private function rm_fld($str_img, $gr_i) {
        $hd = $offset = 13 + pow(2, (ord($str_img[10]) & 7) + 1) * 3;
        $palet="";
        $i_hd = 0;
        $m_off = 0;
        for ($i = 13; $i < $offset; $i++) {
            $palet.=$str_img[$i];
        }if ($this->ar_frm[$gr_i]->tr_frm) {
            while ($str_img[$offset + $m_off] != "\xF9") {
                $m_off++;
            }$str_img[$offset + $m_off + 2] = $this->gn_fld[$gr_i];
            $str_img[$offset + $m_off + 3] = $this->dl_frmf[$gr_i];
            $str_img[$offset + $m_off + 4] = $this->dl_frms[$gr_i];
        }
        while($str_img[$offset] != "\x2C"){
            $offset = $offset + $this->rl_int($str_img[$offset+2]) + 4;
            $i_hd = $i_hd + $this->rl_int($str_img[$offset+2]) + 8;
        }
        $str_img[$offset + 1] = $this->ar_frm[$gr_i]->off_xy[0];
        $str_img[$offset + 2] = $this->ar_frm[$gr_i]->off_xy[1];
        $str_img[$offset + 3] = $this->ar_frm[$gr_i]->off_xy[2];
        $str_img[$offset + 4] = $this->ar_frm[$gr_i]->off_xy[3];
        @$str_img[$offset + 9] = chr($str_img[$offset + 9] | 0x80 | (ord($str_img[10]) & 0x7));
        $ms1 = substr($str_img, $hd, $i_hd + 10);
        if (!$this->ar_frm[$gr_i]->tr_frm) {
            $ms1 = $this->ar_frm[$gr_i]->gr_mod . $ms1;
        }return $ms1 . $palet . substr(substr($str_img, $offset + 10), 0, -1);
    }
    function resize($file_dst, $new_x, $new_y, $pr, $sm) {
        if ($this->er) {
            printf("ERROR: signature file is incorrectly");
            return 0;
        }if ($new_x == 0 || $new_y == 0) {
            printf("ERROR: size height or width can not be equal to zero");
            return 0;
        }$des = Array(0, 0, 0);
        $f_buf = "";
        $con;
        $des[3] = $sm;
        $des[0] = $new_x / $this->int_w;
        $des[1] = $new_y / $this->int_h;
        if ($pr) {
            $rt = min($des[0], $des[1]);
            $des[0] == $rt ? $des[1] = $rt : $des[0] = $rt;
        }for ($i = 0; $i < $this->lp_frm; $i++) {
            $f_buf.=$this->rm_fld($this->resize_img($this->cr_img($i), $i, $des), $i);
        }$gm = $this->gl_mn;
        @$gm[10] = $gm[10] & 0x7F;
        $bf_t = round($this->int_w * $des[0]);
        $t = $this->int_raw($bf_t ? $bf_t : 1);
        $gm[6] = $t[0];
        $gm[7] = $t[1];
        $bf_t = round($this->int_h * $des[1]);
        $t = $this->int_raw($bf_t ? $bf_t : 1);
        $gm[8] = $t[0];
        $gm[9] = $t[1];
        if (strlen($this->gl_mode)) {
            $con = $this->gl_mode . "\x3B";
        } else {
            $con = "\x3B";
        }
        file_put_contents($file_dst, $gm . $this->gl_mod . $f_buf . (iconv_strlen($con) >= 19 ? $con : "\x21"));
        return 1;
    }
}
