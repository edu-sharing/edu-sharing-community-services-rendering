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
define('DIR', $pUrl['path']);


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

        if (!file_exists($CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p'.DIRECTORY_SEPARATOR)) {
            mkdir($CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p'.DIRECTORY_SEPARATOR, 0755);
        }

        $this ->dbFile = $CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'h5p'.DIRECTORY_SEPARATOR . 'db';
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


	protected function renderTemplate($TemplateName, $getDefaultData = true, $showMetadata = true) {

        global $db;
        $Logger = $this -> getLogger();
        $contentHash = $this->esObject->getContentHash();

        //check if Content already exists in db & cache
        $query = "SELECT id, created_at FROM h5p_contents WHERE title='".$this->esObject->getObjectID()."-".$contentHash."'";
        $statement = $db -> query($query);
        $results = $statement->fetchAll(\PDO::FETCH_OBJ);

        if(!$results[0]->id){// only create new folder if we dont already have the object
            @mkdir($this->H5PFramework->get_h5p_path());

            //if dir exits -> somebody else is building the h5p-object. Abort and let the user try again.
            if(@mkdir($this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->esObject->getObjectID()) )){
                copy($this->esObject->getFilePath(), $this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->esObject->getObjectID()) . DIRECTORY_SEPARATOR . $this->esObject->getObjectID() . '.h5p');
                $this->H5PFramework->uploadedH5pFolderPath = $this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->esObject->getObjectID());
                $this->H5PFramework->uploadedH5pPath = $this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->esObject->getObjectID()) . DIRECTORY_SEPARATOR . $this->esObject->getObjectID() . '.h5p';
                $this->H5PCore->disableFileCheck = true;

                if($this->H5PValidator->isValidPackage()){
                    $this->H5PStorage->savePackage(array('title' => $this->esObject->getObjectID()."-".$contentHash, 'disable' => 0));

                    $query = "UPDATE h5p_contents SET description='".$this->esObject->getTitle()."' WHERE id=".$this->H5PCore->loadContent($this->H5PFramework->id)['id'];
                    $statement = $db -> query($query);
                    $results = $statement->execute();
                    $Logger -> debug('h5p saved: '.$this->esObject->getTitle());
                }else{
                    $messagesArray = array_values($this->H5PFramework->getMessages('error'));
                    $h5p_error = end($messagesArray);
                    $Logger -> debug('There was a problem with the H5P-file ('.$this->esObject->getObjectID().'): '.$h5p_error->code);
                    $template_data['h5p_new'] = 'There was a problem with the H5P-file: '.$h5p_error->code.'<br>'.$h5p_error->message;
                    echo $this -> getTemplate() -> render($TemplateName, $template_data);
                    return;
                }

            }else{
                $Logger -> debug('This file is being worked on: '.$this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->esObject->getObjectID()));
                $template_data['h5p_new'] = 'This file is being worked on. Please try again in a few moments.';

                date_default_timezone_set('Europe/Berlin');
                if ( $results[0]->created_at < date("Y-m-d H:i:s", strtotime("-10 minutes")) ){
                    @rmdir($this->H5PFramework->get_h5p_path() . DIRECTORY_SEPARATOR . md5($this->esObject->getObjectID()));
                    $Logger -> debug('H5P is at least 10 minutes old. Build Folder deleted...');
                }

                echo $this -> getTemplate() -> render($TemplateName, $template_data);
                return;
            }

        }else{
            $this->H5PFramework->id = $results[0]->id;
        }

        try {
            $content = $this->H5PCore->loadContent($this->H5PFramework->id);
            $this->add_assets($content);

            $template_data = array();

            $filename = $this->esObject->getFilePath() . '.html';
            file_put_contents($filename, $this->render($content['id']));

            $m_path = $this -> esObject -> getPath();

            if($getDefaultData)
                $template_data = parent::prepareRenderData($showMetadata);

            if(Config::get('showMetadata'))
                $template_data['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');

            $template_data['iframeurl'] = $m_path . '.html?' . session_name() . '=' . session_id().'&token=' . $requestData['token'];
            $template_data['title'] = $this->esObject->getTitle();
            $template_data['h5pId'] = $content['id'];
            $template_data['h5pApi'] = $content['id'];
            echo $this -> getTemplate() -> render($TemplateName, $template_data);

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
        //error_log('Render H5P');
        global $MC_URL;

        $html  = '<!doctype html>';
        $html .= '<html lang="en" class="h5p-iframe">';
        $html .= '<head>';
        $html .= '<meta charset="utf-8">';
        $html .= '<title>'.'H5P-iframe'.'</title>';

        foreach (self::$settings['core']['scripts'] as $script) {
            $html .= '<script src="'. DOMAIN. $script.'"></script> ';
        }
        foreach (self::$settings['contents']['cid-'.$contentId]['scripts'] as $script) {
            $html .= '<script src="'.$script.'"></script> ';
        }

        //neccessary to render latex
        $html .= '<script src="'.$MC_URL.'/vendor/js/mathdisplay.js"></script> ';

        foreach (self::$settings['core']['styles'] as $style) {
            $html .= '<link rel="stylesheet" href="' . DOMAIN . $style.'" type="text/css">';
        }
        foreach (self::$settings['contents']['cid-'.$contentId]['styles'] as $style) {
            $html .= '<link rel="stylesheet" href="'. $style.'" type="text/css">';
        }
        $html .= '</head>';
        $html .= '<body>';
        $html .= '<div class="h5p-content" data-content-id="' . $contentId . '"></div>';
        $html .= '</body>';

        //xApi-Connection
        $html .= '<script>
            const xapi = false; //turn LRS on or off
            function onXapi(event) {
                var data = {
                                action: "xapi_event"
                            };
                data.statement = JSON.stringify(event.data.statement);
                //console.log("Sending xApi-Event to Repo");
                event.data.statement.object.id = "'.$this -> esObject -> getPath().'";
                event.data.statement.object.definition.name = {"en-US": "'.$this->esObject->getTitle().'"};
                const nodeID = "'.$this->esObject->getObjectID().'";
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "'.Config::get('baseUrl').'/rest/node/v1/nodes/-home-/"+nodeID+"/xapi", true);
                xhr.setRequestHeader("Content-type", "application/json");
                xhr.setRequestHeader("Accept", "application/json");
                xhr.crossDomain = true;
                xhr.withCredentials = true;
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status === 200) {
                        let response = JSON.parse(xhr.response);
                        //console.log(response);
                    }
                }
                xhr.send(JSON.stringify(event.data.statement));
             }
            if (typeof H5P !== "undefined" && H5P.externalDispatcher && xapi){
                H5P.externalDispatcher.on("xAPI", onXapi);
                console.log("h5p xapi ready");
            }
                </script>';

        $html .= '<script>H5PIntegration='. json_encode(self::$settings).'</script>';

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

        // Use relative URL to support both http and https.basename($MC_URL)
        $lib_url =  DOMAIN . DIR .'/vendor/lib/h5p-core/';
        $rel_path = '/' . preg_replace('/^[^:]+:\/\/[^\/]+\//', '', $lib_url);
        // Add core stylesheets
        foreach (H5PCore::$styles as $style) {
            self::$settings['core']['styles'][] = $rel_path . $style . $cache_buster;
        }

        // Add core JavaScript
        foreach (H5PCore::$scripts as $script) {
            self::$settings['core']['scripts'][] = $rel_path . $script . $cache_buster;
        }
    }

    public function get_content_settings($content)
    {
        $core = $this->H5PCore;
        $safe_parameters = $core->filterParameters($content);

        // Add JavaScript settings for this content
        //@see https://h5p.org/creating-your-own-h5p-plugin
        $settings = array(
            'library' => H5PCore::libraryToString($content['library']),
            'jsonContent' => $safe_parameters,
            'fullScreen' => $content['library']['fullscreen'],
            //'resizeCode' => '<script src="' . DOMAIN . DIR . '/vendor/lib/h5p-core/js/h5p-resizer.js' . '" charset="UTF-8"></script>',
            'title' => $content['title'],
            //'displayOptions' => array(), //$core->getDisplayOptionsForView($content['disable'], 0) // not needed here
            'displayOptions' => [
                                    "frame" => true, // Show frame and buttons below H5P
                                    "export"=> false, // Display download button
                                    "embed"=> false, // Display embed button
                                    "copyright"=> true, // Display copyright button
                                    "copyright"=> true, // Display copyright button
                                    "icon"=> true // Display H5P icon
                                ],
            'metadata' => $content['metadata'],
            'contentUserData' => array(
                0 => array(
                    'state' => '{}'
                )
            )
        );

        return $settings;
    }

    //@see https://h5p.org/creating-your-own-h5p-plugin
    public function get_core_settings() {
        $settings = array(
            'baseUrl' =>  DOMAIN,
            'url' => PATH,
            'postUserStatistics' => false,
            'ajaxPath' => '',     // Only used by older Content Types
            'ajax' => array(
                    'setFinished' => '',
                    'contentUserData' => ''
                ),
            'saveFreq' => false,
            'siteUrl'=> DOMAIN,
            'l10n' => array(
                'H5P' => [
                      "fullscreen"=> "Fullscreen",
                      "disableFullscreen"=> "Disable fullscreen",
                      "download"=> "Download",
                      "copyrights"=> "Rights of use",
                      "embed"=> "Embed",
                      "size"=> "Size",
                      "showAdvanced"=> "Show advanced",
                      "hideAdvanced"=> "Hide advanced",
                      "advancedHelp"=> "Include this script on your website if you want dynamic sizing of the embedded content:",
                      "copyrightInformation"=> "Rights of use",
                      "close"=> "Close",
                      "title"=> "Title",
                      "author"=> "Author",
                      "year"=> "Year",
                      "source"=> "Source",
                      "license"=> "License",
                      "thumbnail"=> "Thumbnail",
                      "noCopyrights"=> "No copyright information available for this content.",
                      "downloadDescription"=> "Download this content as a H5P file.",
                      "copyrightsDescription"=> "View copyright information for this content.",
                      "embedDescription"=> "View the embed code for this content.",
                      "h5pDescription"=> "Visit H5P.org to check out more cool content.",
                      "contentChanged"=> "This content has changed since you last used it.",
                      "startingOver"=> "You'll be starting over.",
                      "by"=> "by",
                      "showMore"=> "Show more",
                      "showLess"=> "Show less",
                      "subLevel"=> "Sublevel",
                      "reuse"=> "Reuse",
                      "reuseContent"=> "Reuse Content",
                      "contentType"=> "Content Type"
                ],
            ),
            'hubIsEnabled' => false,
            'libraryUrl' => DOMAIN . '/rendering-service/vendor/lib/h5p-core/js',
        );
        return $settings;
    }

    public function add_settings() {
        if (self::$settings !== null) {
            return $this->print_settings(self::$settings);
        }
    }

    /**
     * JSON encode and print the given H5P JavaScript settings.
     *
     * @since 1.0.0
     * @param array $settings
     */
    public function print_settings(&$settings, $obj_name = 'H5PIntegration') {
        static $printed;
        if (!empty($printed[$obj_name])) {
            return; // Avoid re-printing settings
        }

        $json_settings = json_encode($settings);
        if ($json_settings !== FALSE) {
            $printed[$obj_name] = TRUE;
            return '<script>' . $obj_name . ' = ' . $json_settings . ';</script>';
        }
    }

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::inline()
	 */
	protected function inline() {
		echo $this -> renderTemplate('/module/h5p/inline');
		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see ESRender_Module_ContentNode_Abstract::dynamic()
	 */
	protected function dynamic() {
        echo $this -> renderTemplate('/module/h5p/dynamic');
        return true;
	}

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_ContentNode_Abstract::embed()
     */
    protected function embed() {
        echo $this -> renderTemplate('/module/h5p/embed');
        return true;
    }


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
        $Logger = $this -> getLogger();

        $pdo = RsPDO::getInstance();

        try {
            $sql = 'SELECT * FROM `ESOBJECT` ' . 'WHERE `ESOBJECT_REP_ID` = :repid ' . 'AND `ESOBJECT_CONTENT_HASH` = :contenthash ' . 'AND `ESOBJECT_OBJECT_ID` = :objectid ';

            $stmt = $pdo -> prepare($pdo->formatQuery($sql));
            $stmt -> bindValue(':repid', $this -> esObject -> getRepId());
            $stmt -> bindValue(':contenthash', $this -> esObject -> getContentHash());
            $stmt -> bindValue(':objectid', $this -> esObject -> getObjectID());
            $stmt -> execute();

            $result = $stmt -> fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $Logger -> debug('Instance exists.');
                $this -> esObject -> setInstanceData($result);
                return true;
            }

            $Logger -> debug('Instance does not exist.');
            return false;
        } catch (PDOException $e) {
            throw new Exception($e -> getMessage());
        }
    }

}
