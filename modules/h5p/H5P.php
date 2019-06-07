<?php
namespace connector\tools\h5p;

define('MODE_NEW', 'mode_new');

class H5P extends \connector\lib\Tool {

    protected static $instance = null;
    public $H5PFramework;
    public $H5PCore;
    public $H5PValidator;
    public $H5PStorage;
    public $H5PContentValidator;
    public $H5PEditorAjaxImpl;
    private $mode;
    private $library;
    private $parameters;
    private $h5pLang;
    private $language;

    public function __construct($apiClient = NULL, $log = NULL, $connectorId = NULL) {
        if($apiClient && $log && $connectorId)
            parent::__construct($apiClient, $log, $connectorId);
        global $db;
        $this -> h5pLang = isset($_SESSION[$connectorId]['language'])? $_SESSION[$connectorId]['language'] : 'de';
	$this -> language = str_replace('/', DIRECTORY_SEPARATOR, include __DIR__ . '/../../../lang/' . $this -> h5pLang . '.php');
        $db = new \PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME, DBUSER, DBPASSWORD);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->H5PFramework = new H5PFramework();
        $this->H5PCore = new \H5PCore($this->H5PFramework, $this->H5PFramework->get_h5p_path(), $this->H5PFramework->get_h5p_url(), $this -> h5pLang, true);
        $this->H5PCore->aggregateAssets = TRUE; // why not?

        $this->H5PCore->disableFileCheck = TRUE; // @needs approval

        $this->H5PValidator = new \H5PValidator($this->H5PFramework, $this->H5PCore);
        $this->H5PStorage = new \H5PStorage($this->H5PFramework, $this->H5PCore);
        $this->H5PContentValidator = new \H5PContentValidator($this->H5PFramework, $this->H5PCore);
        self::$instance = $this;
    }

    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function run() {
        $this->H5PCore->disableFileCheck = true;
        $this->H5PValidator->isValidPackage();
        $content['language'] = $this -> h5pLang;
        if($this->mode === MODE_NEW) {
            $content['id'] = '';
        } else {
            $titleShow = $_SESSION[$this->connectorId]['node']->node->title;
            if(empty($titleShow))
                $titleShow = $_SESSION[$this->connectorId]['node']->node->name;
             $this->H5PStorage->savePackage(array('title' => $titleShow, 'disable' => 0));
             $content = $this->H5PCore->loadContent($this->H5PStorage->contentId);
             $this->library = \H5PCore::libraryToString($content['library']);
             $this->parameters = htmlentities($content['params']); // metadata missing !!!!!!!!!!!!!!!!!!!!!! check if needed => //htmlentities($this->H5PCore->filterParameters($content));
            //copy media to editor
            $this->copyr($this->H5PFramework->get_h5p_path().'/content/'.$content['id'], $this->H5PFramework->get_h5p_path().'/editor/');
            $_SESSION[$this->connectorId]['viewContentId'] = $content['id'];
        }
        $this->showEditor();
    }

    private function copyr($source, $dest)
    {
        if(is_file($source) && basename($source) == 'content.json')
            return true;

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            if ($dest !== "$source/$entry") {
                $this->copyr("$source/$entry", "$dest/$entry");
            }
        }

        // Clean up
        $dir->close();
        return true;
    }


    public function rrmdir($dir) {
       if (is_dir($dir)) {
         $objects = scandir($dir);
         foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
             if (is_dir($dir."/".$object))
               $this -> rrmdir($dir."/".$object);
             else
               unlink($dir."/".$object);
            }
            }
            rmdir($dir);
        }
    }


    public function showEditor() {
        echo '<html><head><meta name="viewport" content="width=device-width, initial-scale=1.0">';
        $integration = array();
        $integration['baseUrl'] = WWWURL;
        $integration['url'] = '/eduConnector/src/tools/h5p';
        $integration['siteUrl'] = WWWURL;
        $integration['postUserStatistics'] = '';
        $integration['ajax'] = array();
        $integration['saveFreq'] = false;
        $integration['l10n'] = array('H5P' => $this->H5PCore->getLocalization());
        $integration['hubIsEnabled'] = falsw;
        $integration['user'] = array();
        $integration['core'] = array('style'=>\H5PCore::$styles, 'scripts'=>\H5PCore::$scripts);
        $integration['loadedJs'] = '';
        $integration['loadedCss'] = '';
        $integration['editor']['filesPath'] = WWWURL . '/src/tools/h5p/editor';
        $integration['editor']['fileIcon'] = '';
        $integration['editor']['ajaxPath'] = WWWURL . '/ajax/ajax.php?action=h5p_';
        //$integration['editor']['libraryUrl'] = WWWURL . '/vendor/h5p/h5p-editor/';
        $integration['editor']['copyrightSemantics'] = $this->H5PContentValidator ->getCopyrightSemantics();
        foreach(\H5PCore::$styles as $b) {
            $integration['editor']['assets']['css'][] = WWWURL . '/vendor/h5p/h5p-core/' . $b;
        }
        foreach(\H5PCore::$scripts as $b) {
            $integration['editor']['assets']['js'][] = WWWURL . '/vendor/h5p/h5p-core/' . $b;
        }
     /*   foreach(\H5PEditor::$styles as $b) {
            $integration['editor']['assets']['css'][] = WWWURL . '/vendor/h5p/h5p-editor/' . $b;
        }

        foreach(\H5PEditor::$scripts as $b) {
            $integration['editor']['assets']['js'][] = WWWURL . '/vendor/h5p/h5p-editor/' . $b;
        }
        $integration['editor']['assets']['js'][] = WWWURL . '/vendor/h5p/h5p-editor/language/'.$this -> h5pLang.'.js';
*/
        $integration['editor']['deleteMessage'] = 'soll das echt geloescht werden?';
        $integration['editor']['apiVersion'] = \H5PCore::$coreApi;
        $integration['editor']['nodeVersionId'] = $this->H5PStorage->contentId;
        $integration['editor']['metadataSemantics'] = $this->H5PContentValidator->getMetadataSemantics();

        echo '<link rel="stylesheet" href="' . WWWURL . '/css/h5p.css"> ';

        echo '<script>'.
            'window.H5PIntegration='. json_encode($integration).
            '</script>';
        foreach(\H5PCore::$styles as $style) {
            echo '<link rel="stylesheet" href="' . WWWURL . '/vendor/h5p/h5p-core/' . $style . '"> ';
        }
        foreach (\H5PCore::$scripts as $script) {
            echo '<script src="' . WWWURL . '/vendor/h5p/h5p-core/' . $script . '"></script> ';
        }/*
        foreach(\H5PEditor::$styles as $style) {
            echo '<link rel="stylesheet" href="' . WWWURL . '/vendor/h5p/h5p-editor/' . $style . '"> ';
        }

        foreach (\H5PEditor::$scripts as $script) {
            echo '<script src="' . WWWURL . '/vendor/h5p/h5p-editor/' . $script . '"></script> ';
        }

                echo '<script src="'.WWWURL.'/src/tools/h5p/js/editor.js"></script>';*/
        echo '</head><body>';

        $titleShow = $_SESSION[$this->connectorId]['node']->node->title;
        if(empty($titleShow))
            $titleShow = $_SESSION[$this->connectorId]['node']->node->name;

        echo '<form method="post" enctype="multipart/form-data" id="h5p-content-form" action="'.WWWURL.'/ajax/ajax.php?title='.$_SESSION[$this->connectorId]['node']->node->ref->id.'&action=h5p_create&id='.$this->connectorId.'">';
      //  echo '<div class="h5pSaveBtnWrapper"><h1 class="h5pTitle">'.$titleShow.'</h1><input type="submit" name="submit" value="' . $this -> language['save'] . '" class="h5pSaveBtn btn button button-primary button-large"/></div>';
        echo '<div class="h5p-create"><div class="h5p-editor"></div></div>';
        echo '<input type="hidden" name="library" value="'.$this->library.'">';
        echo '<input type="hidden" name="parameters" value="'.$this->parameters.'">';
        echo '<div class="h5pSaveBtnWrapper"><input type="submit" name="submit" value="' . $this -> language['save'] . '" class="h5pSaveBtn btn button button-primary button-large"/></div>';
        echo '</form>';
        echo '</body></html>';
    }

    public function setNode() {
        $node = $this->getNode();

            if(defined('FORCE_INTERN_COM') && FORCE_INTERN_COM) {
                $arrApiUrl = parse_url($_SESSION[$this->connectorId]['api_url']);
                $arrContentUrl = parse_url($node->node->contentUrl);
                $contentUrl = $arrApiUrl['scheme'].'://'.$arrApiUrl['host'].':'.$arrApiUrl['port'].$arrContentUrl['path'].'?'.$arrContentUrl['query'] . '&com=internal';
                $curlHeader = array('Cookie:JSESSIONID=' . $_SESSION[$this->connectorId]['sessionId']);
                $url = $contentUrl . '&params=display%3Ddownload';
            } else {
                $contentUrl = $node->node->contentUrl;
                $curlHeader = array();
                $url = $contentUrl . '&ticket=' . $_SESSION[$this->connectorId]['ticket'] . '&params=display%3Ddownload';
            }
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeader);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            $data = curl_exec($curl);
            curl_close($curl);

            $fp = fopen($this->H5PFramework->getUploadedH5pPath(), 'w');
            fwrite($fp, $data);
            fclose($fp);

        $node = $this->getNode();
        $_SESSION[$this->connectorId]['node'] = $node;
    }
}
