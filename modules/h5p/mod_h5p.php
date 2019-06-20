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

require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p.classes.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-file-storage.interface.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-default-storage.class.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-development.class.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-event-base.class.php');
require_once (dirname(__FILE__) . '/../../vendor/lib/h5p-core/h5p-metadata.class.php');

require_once (dirname(__FILE__) . '/H5PFramework.php');
require_once (dirname(__FILE__) . '/H5PContentHandler.php');

$pUrl = parse_url($MC_URL);
define('DOMAIN', $pUrl['scheme'] . '://' . $pUrl['host']); // port only if specified!!!!!!
define('PATH', $pUrl['path'] . '/modules/cache/h5p');


class mod_h5p
extends ESRender_Module_ContentNode_Abstract {

    private $H5PFramework;
    private $H5PCore;
    private $H5PValidator;
    private $H5PStorage;
    private static $settings = array();
    private $dbFile;

    public function __construct($Name, ESRender_Application_Interface $RenderApplication, ESObject $p_esobject, Logger $Logger, Phools_Template_Interface $Template) {
        global $CC_RENDER_PATH;

        parent::__construct($Name, $RenderApplication,$p_esobject, $Logger, $Template);

        $this ->dbFile = $CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p'.DIRECTORY_SEPARATOR . uniqid();
        if(!file_exists($this ->dbFile))
            copy(__DIR__ . DIRECTORY_SEPARATOR . 'empty.sqlite', $this ->dbFile);

        global $db;
        $db = new PDO('sqlite:' . $this ->dbFile);
        $db -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

        $this->H5PFramework = new H5PFramework();
        $this->H5PCore = new H5PCore($this->H5PFramework, $this->H5PFramework->get_h5p_path(), $this->H5PFramework->get_h5p_url(), mc_Request::fetch('language', 'CHAR', 'de'), false);
        $this->H5PValidator = new H5PValidator($this->H5PFramework, $this->H5PCore);
        $this->H5PStorage = new H5PStorage($this->H5PFramework, $this->H5PCore);
    }


	protected function renderTemplate(array $requestData, $TemplateName, $getDefaultData = true) {
        @mkdir($this->H5PFramework->get_h5p_path());
        @mkdir($this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->_ESOBJECT->getObjectID()));
        copy($this->_ESOBJECT->getFilePath(), $this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->_ESOBJECT->getObjectID()) . DIRECTORY_SEPARATOR . $this->_ESOBJECT->getObjectID() . '.h5p');

        $this->H5PFramework->uploadedH5pFolderPath = $this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->_ESOBJECT->getObjectID());
        $this->H5PFramework->uploadedH5pPath = $this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->_ESOBJECT->getObjectID()) . DIRECTORY_SEPARATOR . $this->_ESOBJECT->getObjectID() . '.h5p';
        $this->H5PCore->disableFileCheck = true;

        try {
            $this->H5PValidator->isValidPackage();
            $this->H5PStorage->savePackage(array('title' => 'ein titel', 'disable' => 0));
            $content = $this->H5PCore->loadContent($this->H5PFramework->id);
            $this->add_assets($content);

            $template_data = array();

            $filename = $this->_ESOBJECT->getFilePath() . '.html';
            file_put_contents($filename, $this->render($content['id']));

            $m_path = $this -> _ESOBJECT -> getPath();

            if($getDefaultData)
                $template_data = parent::prepareRenderData($requestData);

            if(Config::get('showMetadata'))
                $template_data['metadata'] = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic');

            $template_data['iframeurl'] = $m_path . '.html?' . session_name() . '=' . session_id().'&token=' . $requestData['token'];
            $template_data['title'] = $this->_ESOBJECT->getTitle();
            echo $this -> getTemplate() -> render($TemplateName, $template_data);

            global $db;
            $db = null;
            unlink($this->dbFile);
        } catch(Exception $e) {
            var_dump($e);
        }
	}

    private function add_assets($content) {

        // Add core assets
        $this->add_core_assets();

        $cid = 'cid-' . $this->H5PFramework->id;
        if (!isset(self::$settings['contents'][$cid])) {
            self::$settings['contents'][$cid] = $this->get_content_settings($content);

            // Get assets for this content
            $preloaded_dependencies = $this -> H5PCore ->loadContentDependencies($content['id'], 'preloaded');

            $files = $this -> H5PCore -> getDependenciesFiles($preloaded_dependencies);

            self::$settings['contents'][$cid]['scripts'] = $this -> H5PCore->getAssetsUrls($files['scripts']);
            self::$settings['contents'][$cid]['styles'] = $this -> H5PCore->getAssetsUrls($files['styles']);
        }
    }

    private function render($contentId) {

        $html = '<html><head>';

        $html .= '<script>window.H5PIntegration='. json_encode(self::$settings).'</script>';

        foreach (self::$settings['core']['styles'] as $style) {
            $html .= '<link rel="stylesheet" href="' . DOMAIN . $style.'"> ';
        }
        foreach (self::$settings['contents']['cid-'.$contentId]['styles'] as $style) {
            $html .= '<link rel="stylesheet" href="'. $style.'"> ';
        }

        foreach (self::$settings['core']['scripts'] as $script) {
            $html .= '<script src="'. DOMAIN. $script.'"></script> ';
        }

        foreach (self::$settings['contents']['cid-'.$contentId]['scripts'] as $script) {
            $html .= '<script src="'.$script.'"></script> ';
        }

        $html .= '</head><body>';


      //$html .= '<div class="h5p-iframe-wrapper"><iframe id="h5p-iframe-' . $contentId . '" class="h5p-iframe" data-content-id="' . $contentId . '" style="height:1px" src="about:blank" frameBorder="0" scrolling="no"></iframe></div>';

        $html .= '<div class="h5p-content" data-content-id="' . $contentId . '"></div>';

        $html .= '</body>';

        //post message send height to parent to adjust iframe height
        $html .= '<script>var lastHeight = 0; function resize() {
                    var height = document.getElementsByTagName("html")[0].scrollHeight;
                    if(lastHeight != height) {
                        window.parent.postMessage(["setHeight", height], "*"); 
                    lastHeight = height;
                  }
                }
                setInterval(resize, 100);
            </script>';

        $html .= '</html>';

        return $html;
    }


    private function add_core_assets() {

        if (self::$settings !== null) {
            //return; // Already added
        }

        self::$settings = $this->get_core_settings();

        self::$settings['core'] = array(
            'styles' => array(),
            'scripts' => array()
        );
        self::$settings['loadedJs'] = array();
        self::$settings['loadedCss'] = array();
        $cache_buster = '?ver=' . time();

        // Use relative URL to support both http and https.
        $lib_url =  DOMAIN . '/rendering-service/vendor/lib/h5p-core/';
        $rel_path = '/' . preg_replace('/^[^:]+:\/\/[^\/]+\//', '', $lib_url);
        // Add core stylesheets
        foreach (H5PCore::$styles as $style) {
            self::$settings['core']['styles'][] = $rel_path . $style . $cache_buster;
        }

        // Add core JavaScript
        foreach (H5PCore::$scripts as $script) {
            self::$settings['core']['scripts'][] = $rel_path . $script . $cache_buster;
        }


        self::$settings['core']['scripts'][] = $rel_path . 'js/h5p-resizer.js';

    }

    public function get_content_settings($content)
    {
        global $wpdb;
        $core = $this->H5PCore;

        $safe_parameters = $core->filterParameters($content);

        // Add JavaScript settings for this content
        //@see https://h5p.org/creating-your-own-h5p-plugin
        $settings = array(
            'library' => H5PCore::libraryToString($content['library']),
            'jsonContent' => $safe_parameters,
            'fullScreen' => $content['library']['fullscreen'],
            'resizeCode' => '<script src="' . DOMAIN . '/rendering-service/vendor/lib/h5p-core/js/h5p-resizer.js' . '" charset="UTF-8"></script>',
            'title' => $content['title'],
            'displayOptions' => array(), //$core->getDisplayOptionsForView($content['disable'], 0) // not needed here
        );

        return $settings;

    }

    //@see https://h5p.org/creating-your-own-h5p-plugin
    public function get_core_settings() {
        $settings = array(
            'baseUrl' =>  DOMAIN,
            'url' => PATH,
            'l10n' => array(
                'H5P' => '',
            ),
        );
        return $settings;
    }

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::inline()
	 */
	protected function inline(array $requestData) {
		echo $this -> renderTemplate($requestData, '/module/h5p/inline');
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::dynamic()
	 */
	protected function dynamic(array $requestData) {
        echo $this -> renderTemplate($requestData, '/module/h5p/dynamic');
        return true;
	}

}
