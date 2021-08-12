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
include_once (__DIR__ . '/config.php');
include_once ('../../conf.inc.php');
define('DOCTYPE_DOCX', 'DOCTYPE_DOCX');
define('DOCTYPE_XLSX', 'DOCTYPE_XLSX');
define('DOCTYPE_PPTX', 'DOCTYPE_PPTX');
define('DOCTYPE_UNKNOWN', 'DOCTYPE_UNKNOWN');

/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class mod_office extends ESRender_Module_ContentNode_Abstract
{
    private $doctype;

    /**
     * Extension: set doctype
     */
    public function __construct($Name, ESRender_Application_Interface $RenderApplication, ESObject $p_esobject, Logger $Logger, Phools_Template_Interface $Template)
    {
        parent::__construct($Name, $RenderApplication, $p_esobject, $Logger, $Template);
        $this->setDoctype();
    }

    public function process($p_kind, $locked = null)
    {
        global $requestingDevice;
        // It will be alway embeded mode, since we will will just embeded those files
        $p_kind = ESRender_Application_Interface::DISPLAY_MODE_EMBED;
        parent::process($p_kind);
        return true;
    }

    // final protected function dynamic()
    // {
    //     if ($this->getDoctype() === DOCTYPE_DOCX)
    //     {
    //         echo $this->renderTemplate($this->getThemeByDoctype() . 'dynamic');
    //         return true;
    //     }
    //     else if ($this->getDoctype() === DOCTYPE_XLSX)
    //     {
    //         echo $this->renderTemplate($this->getThemeByDoctype() . 'dynamic');
    //         return true;
    //     }
    //     else if ($this->getDoctype() === DOCTYPE_PPTX)
    //     {
    //         echo $this->renderTemplate($this->getThemeByDoctype() . 'dynamic');
    //         return true;
    //     }
    //     else return parent::dynamic();
    // }
    final protected function embed()
    {
        if ($this->getDoctype() === DOCTYPE_DOCX || $this->getDoctype() === DOCTYPE_XLSX || $this->getDoctype() === DOCTYPE_PPTX)
        {
            echo $this->renderTemplate($this->getThemeByDoctype() . 'embed');
            return true;
        }
        else return parent::embed();
    }

    /**
     * Set doctype
     */
    protected function setDoctype()
    {
        if (strpos($this
            ->esObject
            ->getMimeType() , 'application/msword') !== false || strpos($this
            ->esObject
            ->getMimeType() , 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') !== false) $this->doctype = DOCTYPE_DOCX;
        else if (strpos($this
            ->esObject
            ->getMimeType() , 'application/vnd.ms-excel') !== false || strpos($this
            ->esObject
            ->getMimeType() , 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') !== false) $this->doctype = DOCTYPE_XLSX;
        else if (strpos($this
            ->esObject
            ->getMimeType() , 'application/vnd.ms-powerpoint') !== false || strpos($this
            ->esObject
            ->getMimeType() , 'application/vnd.openxmlformats-officedocument.presentationml.presentation') !== false) $this->doctype = DOCTYPE_PPTX;
        else $this->doctype = DOCTYPE_UNKNOWN;

        return;
    }

    /** 
     * Doctype getter
     */
    protected function getDoctype()
    {
        if (!$this->doctype) $this->setDoctype();
        return $this->doctype;
    }

    /**
     * Load theme according to current doctype
     */
    protected function getThemeByDoctype()
    {
        if (Config::get('hasContentLicense') === false) return '/module/default/';
        switch ($this->getDoctype())
        {
            case DOCTYPE_DOCX:
                return '/module/office/docx/';
            break;
            case DOCTYPE_PPTX:
                return '/module/office/pptx/';
            break;
            case DOCTYPE_XLSX:
                return '/module/office/xlsx/';
            break;
            default:
                return '';
        }
    }

    private function getFileType()
    {
        switch ($this->getDoctype())
        {
            case DOCTYPE_DOCX:
                return 'docx';
            break;
            case DOCTYPE_PPTX:
                return 'pptx';
            break;
            case DOCTYPE_XLSX:
                return 'xlsx';
            break;
            default:
                return 'docx';
        }
    }

    /**
     * Function for rendering Templates in base of MIME-TYPES
     */
    protected function renderTemplate($TemplateName, $showMetadata = true)
    {
        global $MC_URL;
        $template_data = parent::prepareRenderData($showMetadata);
        $template_data['previewUrl'] = $this
            ->esObject
            ->getPreviewUrl();
        $template_data['logoImageUrl'] = $this->getConfiguration(constant('LOGO_IMAGE_URL'), $MC_URL . "/admin/img/edulogo.svg");
        $template_data['logoLinkUrl'] = $this->getConfiguration(constant('LOGO_LINK_URL'), "https://edu-sharing.com/");

        if (Config::get('hasContentLicense') === true)
        {

            if ($this->getDoctype() == DOCTYPE_DOCX || $this->getDoctype() == DOCTYPE_PPTX || $this->getDoctype() == DOCTYPE_XLSX)
            {

                $urlFile = $this
                    ->esObject
                    ->getPath() . '?' . session_name() . '=' . session_id() . '&token=' . Config::get('token');
                $template_data['url'] = $urlFile;
                $template_data['title'] = $this
                    ->esObject
                    ->getTitle();
                $template_data['objectId'] = $this
                    ->esObject
                    ->getObjectID();
                $template_data['fileType'] = $this->getFileType();
                $template_data['downloadAdvice'] = $this->downloadAdvice($template_data);

            }

        }

        if (Config::get('showMetadata')) $template_data['metadata'] = $this
            ->esObject
            ->getMetadataHandler()
            ->render($this->getTemplate() , '/metadata/dynamic');

        $Template = $this->getTemplate();
        $rendered = $Template->render($TemplateName, $template_data);
        return $rendered;
    }

    /**
     * This function will check for a specific configuration
     * @param Type $configName configuration name
     * @param Type $defaultValue a default value as fallback
     * @return Type configuration values if exist
     * @return Type default value if not exist
     */
    private function getConfiguration($configName, $defaultValue)
    {
        if ($configName || $configName!="") return $configName;
        else return $defaultValue;
    }

    /**
     * function that return a html if something goes wrong with preview
     * @param Array $dataArray keep a list of pair key:value
     * @return a html string
     */
    private function downloadAdvice($dataArray)
    {
        global $Locale, $Translate;
        $toDownload = new Phools_Message_Default('toDownload');
        $cannotOpenObject = new Phools_Message_Default('cannotOpenObject');
        $cannotOpenObjectText = new Phools_Message_Default('cannotOpenObjectText');
        $downloadbutton = "";

        if (Config::get('showDownloadButton') && Config::get('renderInfoLMSReturn')->hasContentLicense === true)
        {
            $downloadbutton = '<a href="' . $dataArray['url'] . '" download="' . $dataArray['title'] . '" class="edusharing_rendering_content" id="edusharing_rendering_content_href">' . $toDownload->localize($Locale, $Translate) . '</a>';
        }

        return '<div id="edusharing_downloadadvice">' . '<img class="edusharing_rendering_content_preview" src="' . $dataArray['previewUrl'] . '">' . '<h3>' . $cannotOpenObject->localize($Locale, $Translate) . '</h3>' . '<h4>' . $cannotOpenObjectText->localize($Locale, $Translate) . '</h4>' . '</div>' . $downloadbutton;

    }

}