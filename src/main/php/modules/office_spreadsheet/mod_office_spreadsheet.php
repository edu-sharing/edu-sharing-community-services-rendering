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

include_once (__DIR__ . '/../../conf.inc.php');
require_once (__DIR__ . '/../doc/mod_doc.php');
require_once (__DIR__ . '/../../vendor/autoload.php');


/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_office_spreadsheet
	extends ESRender_Module_ContentNode_Abstract
{
    static $CONVERTED_POSTFIX = '_converted.html';
    public function __construct($Name, ESRender_Application_Interface $RenderApplication, ESObject $p_esobject, Logger $Logger, Phools_Template_Interface $Template) {
        parent::__construct($Name, $RenderApplication, $p_esobject, $Logger, $Template);

    }
    public function instanceExists() {
        return file_exists($this -> esObject -> getPath(). mod_office_spreadsheet::$CONVERTED_POSTFIX);
    }


    // @TODO: Also use inline features
    protected function dynamic()
    {
        $template_data['url'] = $this -> esObject->getPath() . mod_office_spreadsheet::$CONVERTED_POSTFIX.'?' . session_name() . '=' . session_id(). '&token=' . Config::get('token');
        if(Config::get('showMetadata'))
            $template_data['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');
        $template_data['title'] = $this -> esObject->getTitle();
        $template_data['previewUrl'] = $this -> esObject->getPreviewUrl();
        echo $this -> getTemplate() -> render('/module/html/dynamic', $template_data);
        return true;
    }


    final public function createInstance() {
        $this->getLogger()->info('Creating office_spreadsheet instance');
        if (!parent::createInstance()) {
            return false;
        }
        $this -> convertHTML( $this -> getCacheFileName(), $this -> getCacheFileName(). mod_office_spreadsheet::$CONVERTED_POSTFIX);
        $this->convertedPath = $this -> esObject -> getPath() . mod_office_spreadsheet::$CONVERTED_POSTFIX;
        return true;
    }
    public function convertHTML($src, $dest) {
        $this->getLogger()->info('Converting to pdf: ' . $src. ' -> ' . $dest);
        $reader = $this->getReader($src);
        $objWriter = $this->getWriter($reader);
        $objWriter->save($dest);
    }
    private function getWriter($reader) {
        return \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($reader, 'Html');
    }
    private function getReader($src) {
        $mimetype = $this->esObject->getMimetype();
        if($mimetype == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            return \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx')->load($src);
        } else if($mimetype == 'application/vnd.ms-excel') {
            return \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xls')->load($src);
        } else if($mimetype == 'application/vnd.oasis.opendocument.spreadsheet') {
            return \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Ods')->load($src);
        } else if($mimetype == 'text/csv') {
            return \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Csv')->load($src);
        }
        throw new \Exception('No office document reader found for mimetype ' . $mimetype);
    }

    public static function canProcess($esObject) {
        if (ENABLE_VIEWER_JS){
            $supported = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
                'application/vnd.oasis.opendocument.spreadsheet',
                'text/csv',
            ];
            if(in_array($esObject->getMimetype(), $supported)) {
                return true;
            }
        }
        return parent::canProcess($esObject);
    }
}

