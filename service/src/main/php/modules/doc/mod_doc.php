<?php
/**
 * This product Copyright 2013 metaVentis GmbH.  For detailed notice,
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
 *
 *
 *
 *
 *
 */

include_once('../../conf.inc.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

define('DOCTYPE_PDF', 'DOCTYPE_PDF');
define('DOCTYPE_ODF', 'DOCTYPE_ODF');
define('DOCTYPE_HTML', 'DOCTYPE_HTML');
define('DOCTYPE_TEXT', 'DOCTYPE_TEXT');
define('DOCTYPE_UNKNOWN', 'DOCTYPE_UNKNOWN');

/**
 * This module handles documents of type pdf and odf (the basic odf formats) assigned in db
 *
 * @author steffen hippeli
 * @version 1.1
 * @package modules
 * @subpackage doc
 */
class mod_doc
    extends ESRender_Module_ContentNode_Abstract
{

    protected $doctype;
    // optional, converted path, if unset, the es content path will be used
    protected $convertedPath;

    /**
     * Extension: set doctype
     */
    public function __construct($Name, ESRender_Application_Interface $RenderApplication, ESObject $p_esobject, Logger $Logger, Phools_Template_Interface $Template) {
        parent::__construct($Name, $RenderApplication, $p_esobject, $Logger, $Template);
        $this->setDoctype();
    }

    protected function renderTemplate($TemplateName, $showMetadata = true) {

        global $VIEWER_JS_CONFIG;

        $template_data               = parent::prepareRenderData($showMetadata);
        $template_data['previewUrl'] = $this->esObject->getPreviewUrl();

        // get the rights from the es object
        $hasDownloadRight       = true;
        $hasPrintRight          = true;
        $removePrintAndDownload = !$hasDownloadRight || !$hasPrintRight;

        if (Config::get('hasContentLicense') === true) {

            if ($this->getDoctype() == DOCTYPE_PDF) {
                if (ENABLE_VIEWER_JS && isset($VIEWER_JS_CONFIG) && in_array('pdf', $VIEWER_JS_CONFIG)) {
                    $template_data['content'] = ($this->convertedPath ? $this->convertedPath : $this->esObject->getPath()) . '?' . session_name() . '=' . session_id() . '&token=' . Config::get('token');

                } else {
                    $template_data['content'] = $this->esObject->getPath() . '?' . session_name() . '=' . session_id() . '&token=' . Config::get('token');
                }
                $esOptions                = ['allowDownload' => $removePrintAndDownload ? 0 : 1];
                $template_data['content'] .= '&esOptions=' . base64_encode(json_encode($esOptions));
                $template_data['url'] = $this->esObject->getPath() . '?' . session_name() . '=' . session_id() . '&token=' . Config::get('token');
            }
            if ($this->getDoctype() == DOCTYPE_HTML) {
                $template_data['content'] = file_get_contents($this->getCacheFileName() . '_purified.html');
            }

            if ($this->getDoctype() === DOCTYPE_TEXT) {
                $template_data['content'] = nl2br(htmlentities(file_get_contents($this->getCacheFileName())));
            }
        }

        if (Config::get('showMetadata'))
            $template_data['metadata'] = $this->esObject->getMetadataHandler()->render($this->getTemplate(), '/metadata/dynamic');

        $Template = $this->getTemplate();
        $rendered = $Template->render($TemplateName, $template_data);
        return $rendered;
    }

    public function createInstance() {
        if (Config::get('hasContentLicense') === false)
            return true;

        if (!parent::createInstance()) {
            return false;
        }

        if ($this->getDoctype() == DOCTYPE_HTML) {
            $Logger = $this->getLogger();
            try {
                require_once __dir__ . '/../../func/extern/htmlpurifier/HTMLPurifier.standalone.php';
                $htmlPurifier = new HTMLPurifier();
                $originalHTML = file_get_contents($this->getCacheFileName());
                $purified     = $htmlPurifier->purify($originalHTML);
                file_put_contents($this->getCacheFileName() . '_purified.html', $purified);
                $Logger->info('Stored content in file "' . $this->getCacheFileName() . '"_purified.html.');
            } catch (Exception $e) {
                $Logger->info('Error storing content in file "' . $this->getCacheFileName() . '"_purified.html.');
                return false;
            }
        }
        return true;
    }

    protected function getOutputFilename() {
        $Logger   = $this->getLogger();
        $filename = $this->getCacheFileName();
        $filename = str_replace('\\', '/', $filename);
        return $filename;
    }

    protected function dynamic() {
        if ($this->getDoctype() === DOCTYPE_HTML || $this->getDoctype() === DOCTYPE_TEXT) {
            echo $this->renderTemplate($this->getThemeByDoctype() . 'dynamic');
            return true;
        } else if ($this->getDoctype() === DOCTYPE_PDF) {
            echo $this->renderTemplate($this->getThemeByDoctype() . 'dynamic');
            return true;
        } else return parent::dynamic();
    }

    final protected function embed() {
        if ($this->getDoctype() === DOCTYPE_HTML || $this->getDoctype() === DOCTYPE_TEXT) {
            echo $this->renderTemplate($this->getThemeByDoctype() . 'embed', false);
            return true;
        } else if ($this->getDoctype() === DOCTYPE_PDF) {
            echo $this->renderTemplate($this->getThemeByDoctype() . 'embed', false);
            return true;
        } else return parent::embed();
    }

    final protected function inline() {
        if ($this->getDoctype() === DOCTYPE_HTML || $this->getDoctype() === DOCTYPE_TEXT) {
            echo $this->renderTemplate($this->getThemeByDoctype() . 'inline', false);
            return true;
        } else if ($this->getDoctype() === DOCTYPE_PDF) {
            echo $this->renderTemplate($this->getThemeByDoctype() . 'inline', false);
            return true;
        } else return parent::embed();
    }

    /**
     * Load theme according to current doctype
     */
    protected function getThemeByDoctype() {
        if (Config::get('hasContentLicense') === false)
            return '/module/default/';
        switch ($this->getDoctype()) {
            case DOCTYPE_HTML :
            case DOCTYPE_TEXT :
                return '/module/doc/html/';
                break;
            case DOCTYPE_PDF :
                return '/module/doc/pdf/';
                break;
            case DOCTYPE_ODF :
                return '/module/doc/odf/';
                break;
            default :
                return '';
        }
    }

    /**
     * Set doctype
     */
    protected function setDoctype() {


        if (strpos($this->esObject->getMimeType(), 'text/html') !== false)
            $this->doctype = DOCTYPE_HTML;
        else if (strpos($this->esObject->getMimeType(), 'text/plain') !== false)
            $this->doctype = DOCTYPE_TEXT;
        else if (strpos($this->esObject->getMimeType(), 'application/pdf') !== false)
            $this->doctype = DOCTYPE_PDF;
        else
            $this->doctype = DOCTYPE_UNKNOWN;
        return;
        /*

        if (strpos($this -> esObject -> getMimeType(), 'opendocument') !== false) {
            $this -> doctype = DOCTYPE_UNKNOWN;
            $this -> doctype = DOCTYPE_ODF;
        } else if (strpos($this -> esObject -> getMimeType(), 'pdf') !== false) {
            $this -> doctype = DOCTYPE_PDF;
        } else {
            $this -> doctype = DOCTYPE_UNKNOWN;
        }*/
    }

    /**
     * Doctype getter
     */
    protected function getDoctype() {
        if (!$this->doctype)
            $this->setDoctype();
        return $this->doctype;
    }


    public function process($p_kind, $locked = null) {
        global $requestingDevice;
        $Logger = $this->getLogger();
        if (($p_kind == ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC || $p_kind == ESRender_Application_Interface::DISPLAY_MODE_EMBED) && !$this->requestingDeviceCanRenderContent()) {
            $Logger->debug('Set display mode to ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD as requesting device will not render ' . $this->getDoctype());
        }

        parent::process($p_kind);
        return true;
    }

    public function requestingDeviceCanRenderContent() {
        switch ($this->getDoctype()) {
            case DOCTYPE_PDF :
                return true;
                break;
            case DOCTYPE_ODF :
                return true;
                break;
            case DOCTYPE_HTML:
                return true;
                break;
            default :
                return false;
        }
    }

    /* public function checkPdfUserAgents() {
         global $requestingDevice;
         switch($requestingDevice -> getCapability('model_name')) {
             case 'Chrome':
             case 'Firefox':
                 return true;
             break;
             case 'Internet Explorer':
                 if((float)$requestingDevice -> getCapability('mobile_browser_version') > 10)
                     return true;
                 else
                     return false;
                 break;
             default:
                 return false;
         }
     }*/

    /**
     * Test if this object already exists for this module. This method
     * checks only ESRender's ESOBJECT-table to for existance of this
     * object. Override this method to implement module-specific behaviour
     * (@see modules/moodle/mod_moodle.php).
     *
     * (non-PHPdoc)
     * @see ESRender_Module_Interface::instanceExists()
     */
    public function instanceExists() {
        $Logger = $this->getLogger();

        $pdo = RsPDO::getInstance();

        try {
            $sql = 'SELECT * FROM "ESOBJECT" ' . 'WHERE "ESOBJECT_REP_ID" = :repid ' . 'AND "ESOBJECT_CONTENT_HASH" = :contenthash ' . 'AND "ESOBJECT_OBJECT_ID" = :objectid';

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':repid', $this->esObject->getRepId());
            $stmt->bindValue(':contenthash', $this->esObject->getContentHash());
            $stmt->bindValue(':objectid', $this->esObject->getObjectID());
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $this->esObject->setInstanceData($result);

                // check if cache exists
                global $CC_RENDER_PATH;
                $module   = $this->esObject->getModule();
                $src_file = $CC_RENDER_PATH . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . $this->esObject->getSubUri_file();
                $src_file .= DIRECTORY_SEPARATOR . $this->esObject->getObjectIdVersion();
                if ((is_file($src_file)) || (is_readable($src_file))) {
                    $Logger->debug('Instance exists.');
                    return true;
                } else {
                    $Logger->debug('No cache, deleting from DB...');
                    try {
                        $this->esObject->deleteFromDb();
                    } catch (Exception $e) {
                        $Logger->debug('Could not delete from DB: ' . $e);
                    }
                    return false;
                }
            }

            $Logger->debug('Instance does not exist.');
            return false;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

}
