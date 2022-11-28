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

include_once ('../../conf.inc.php');
require_once ('../doc/mod_doc.php');
require_once ('../../vendor/autoload.php');


/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_office
	extends mod_doc {
    static $CONVERTED_POSTFIX_PDF = '_converted.pdf';
    static $CONVERTED_POSTFIX_ODP = '_converted.odp';

    public function __construct($Name, ESRender_Application_Interface $RenderApplication, ESObject $p_esobject, Logger $Logger, Phools_Template_Interface $Template) {
        parent::__construct($Name, $RenderApplication, $p_esobject, $Logger, $Template);
        $this->doctype = DOCTYPE_PDF;
    }

    public function instanceExists() {
        return file_exists($this -> esObject -> getPath(). mod_office::$CONVERTED_POSTFIX_PDF) || file_exists($this -> esObject -> getPath(). mod_office::$CONVERTED_POSTFIX_ODP);
    }

    final public function createInstance() {
        $this->getLogger()->info('Creating office instance');

        if (!parent::createInstance()) {
            return false;
        }

        $mimetype = $this->esObject->getMimetype();
        if( $mimetype == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || $mimetype == 'application/vnd.oasis.opendocument.text') {
            $this->convertPDF($this->getCacheFileName(), $this->getCacheFileName() . mod_office::$CONVERTED_POSTFIX_PDF);
            $this->convertedPath = $this -> esObject -> getPath() . mod_office::$CONVERTED_POSTFIX_PDF;
        } else if ($mimetype == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
            $this->convertPresentation($this->getCacheFileName(), $this->getCacheFileName() . mod_office::$CONVERTED_POSTFIX_ODP);
            $this->convertedPath = $this -> esObject -> getPath() . mod_office::$CONVERTED_POSTFIX_ODP;
        } else if ($mimetype == 'application/vnd.ms-powerpoint') {
            // we can't convert them
            $this->doctype = DOCTYPE_UNKNOWN;
            return true;
        } else if ($mimetype == 'application/vnd.oasis.opendocument.presentation') {
            rename($this->getCacheFileName(), $this->getCacheFileName() . mod_office::$CONVERTED_POSTFIX_ODP);
            $this->convertedPath = $this -> esObject -> getPath() . mod_office::$CONVERTED_POSTFIX_ODP;
        } else {
            throw new \Exception('No office document processing steps found for mimetype ' . $mimetype);
        }
        @unlink($this->getCacheFileName());
        return true;
    }

    public function convertPresentation($src, $dest) {
        $reader = $this->getReader($src);
        $objWriter = $this->getWriter($reader);
        $objWriter->save($dest);
    }

    public function convertPDF($src, $dest) {
        $this->getLogger()->info('Converting to pdf: ' . $src. ' -> ' . $dest);
        $rendererLibraryPath = realpath('../../vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRenderer(\PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF, $rendererLibraryPath);

        $reader = $this->getReader($src);
        $objWriter = $this->getWriter($reader);
        $objWriter->save($dest);
    }

    private function getWriter($reader) {
        $mimetype = $this->esObject->getMimetype();
        if(
            $mimetype == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ||
            $mimetype == 'application/vnd.oasis.opendocument.text') {
            return \PhpOffice\PhpWord\IOFactory::createWriter($reader, 'PDF');
        } else if(
            $mimetype == 'application/vnd.openxmlformats-officedocument.presentationml.presentation' ||
            $mimetype == 'application/vnd.ms-powerpoint'
        ) {
            return \PhpOffice\PhpPresentation\IOFactory::createWriter($reader, 'ODPresentation');
        }
        throw new \Exception('No office document writer found for mimetype ' . $mimetype);
    }

    private function getReader($src) {
        $mimetype = $this->esObject->getMimetype();
        if($mimetype == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            return \PhpOffice\PhpWord\IOFactory::load($src, 'Word2007');
        } else if($mimetype == 'application/vnd.oasis.opendocument.text') {
            return \PhpOffice\PhpWord\IOFactory::load($src, 'ODText');
        } else if($mimetype == 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
            return \PhpOffice\PhpPresentation\IOFactory::createReader('PowerPoint2007')->load($src);
        }
        // seems to be not very robust or stable
        /* else if($mimetype == 'application/vnd.ms-powerpoint') {
            return \PhpOffice\PhpPresentation\IOFactory::createReader('PowerPoint97')->load($src);
        }*/
        throw new \Exception('No office document reader found for mimetype ' . $mimetype);
    }

    public static function canProcess($esObject) {
        if (ENABLE_VIEWER_JS){
            // echo $esObject->getMimetype();
            $supported = [
                'application/vnd.oasis.opendocument.text',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                // 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                // 'application/vnd.oasis.opendocument.presentation',
                // 'application/vnd.ms-powerpoint',
            ];
            if(in_array($esObject->getMimetype(), $supported)) {
                return true;
            }
        }

        return parent::canProcess($esObject);
    }

}

