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

    /**
     *
     * @param string $SourceFile
     * @param string $DestinationFile
     *
     * @return bool
     */
    protected function convertImage($SourceFile, $DestinationFile, $width, $height) {
        $Logger = $this -> getLogger();
        try {

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
                case IMAGETYPE_BMP:
                    //php gd image creation from bmp is not implemented
                    $tmpFile = $this -> ImageCreateFromBMP($SourceFile);
                    break;
                default :
                    throw new Exception('Cannot create temporary image file');
            }

            if (!empty($width) && !empty($height)) {
                $newImage = imagecreatetruecolor($width, $height);
                if (!imagecopyresampled($newImage, $tmpFile, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight))
                    throw new Exception('Cannot resample image');
                $Logger -> debug('Resampled picture (' . $width . ' px x ' . $height . ' px).');
                if (!imagejpeg($newImage, $DestinationFile))
                    throw new Exception('Cannot convert image');
                imagedestroy($newImage);
            } else {
                if (!imagejpeg($tmpFile, $DestinationFile))
                    throw new Exception('Cannot convert image');
                imagedestroy($tmpFile);
            }
            $Logger -> debug('Converted picture to jpg.');

        } catch (Exception $e) {
            $Logger -> debug($e -> getMessage());
            return false;
        }
        return true;
    }

    /**
     *
     * @return string
     */
    protected function getImageFilename() {
        return $this -> _ESOBJECT -> getFilePath() . '.jpg';
    }

    protected function renderTemplate(array $requestData, $TemplateName) {
        $Logger = $this -> getLogger();

        $m_mimeType = $this -> _ESOBJECT -> getMimeType();
        $m_path = $this -> _ESOBJECT -> getPath();
        $m_name = $this -> _ESOBJECT -> getTitle();
        $f_path = $this -> _ESOBJECT -> getFilePath();

        $imageUrl = $m_path . '.jpg?' . session_name() . '=' . session_id();

        $template_data = parent::prepareRenderData($requestData);

        $template_data['title'] = (empty($title) ? $this -> _ESOBJECT -> getTitle() : $title);
        $template_data['image_url'] = $imageUrl;
        $Template = $this -> getTemplate();
        $rendered = $Template -> render($TemplateName, $template_data);

        return $rendered;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::createInstance()
     */
    final public function createInstance(array $requestData) {
        if (!parent::createInstance($requestData)) {
            return false;
        }

        $Logger = $this -> getLogger();

        $m_mimeType = $this -> _ESOBJECT -> getMimeType();
        $m_path = $this -> _ESOBJECT -> getPath();
        $m_name = $this -> _ESOBJECT -> getTitle();
        $f_path = $this -> _ESOBJECT -> getFilePath();

        $ObjectFilename = str_replace('\\', '/', $f_path);
        $this -> convertImage($ObjectFilename, $this -> getImageFilename(), $requestData['width'], $requestData['height']);

        return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::display()
     */
    final protected function display(array $requestData) {
        $Logger = $this -> getLogger();

        echo $this -> renderTemplate($requestData, '/module/picture/display');

        return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::inline()
     */
    protected function inline(array $requestData) {
        $Logger = $this -> getLogger();

        echo $this -> renderTemplate($requestData, '/module/picture/inline');

        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::dynamic()
     */
    protected function dynamic(array $requestData) {
    	$Logger = $this -> getLogger();
    
    	echo $this -> renderTemplate($requestData, '/module/picture/dynamic');
    
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
