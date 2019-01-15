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

require_once (MC_LIB_PATH . 'Plattform.php');

/**
 * Abstract base-class for esrender-modules.
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
abstract class ESRender_Module_Base implements ESRender_Module_Interface {

    /**
     * @var ESObject
     */
    protected $_ESOBJECT = null;

    /**
     * This module's name
     *
     * @var string
     */
    protected $Name = '';

    /**
     *
     * @var string
     */
    protected $render_path = '';

    /**
     *
     * @param string $Name
     * @param ESRender_Application_Interface $RenderApplication
     * @param ESObject $p_esobject
     * @param Logger $Logger
     * @param Phools_Template_Interface $Template
     */
    public function __construct($Name, ESRender_Application_Interface $RenderApplication, ESObject $p_esobject, Logger $Logger, Phools_Template_Interface $Template) {
        //  parent::__construct();

        $this -> setName($Name) -> setRenderApplication($RenderApplication) -> setLogger($Logger) -> setTemplate($Template);

        $this -> _ESOBJECT = $p_esobject;
    }

    public function __destruct() {
        $this -> _ESOBJECT = null;
        $this -> Logger = null;
        $this -> Template = null;
        $this -> RenderApplication = null;
    }

    /**
     * Prepare data to be used in templates.
     *
     * @param ESObject $ESObject
     *
     * @return array
     */
    protected function prepareRenderData(ESObject $ESObject) {
        global $Locale, $Translate;
        $msg = array();
        $msg['hasNoContentLicense'] = new Phools_Message_Default('hasNoContentLicense');

        $data = array('title' => $this -> _ESOBJECT -> getTitle(),
                    'width' => mc_Request::fetch('width', 'INT', 0),
                    'height' => mc_Request::fetch('height', 'INT', 0),
                    'backLink' => mc_Request::fetch('backLink', 'CHAR', ''));

        if(false === Config::get('hasContentLicense')) {
            $license = '<span class="edusharing_warning">' . htmlentities($msg['hasNoContentLicense']->localize($Locale, $Translate), ENT_COMPAT, 'utf-8') . '</span>';
        } else {
            if($this -> _ESOBJECT -> getLicense()) {
                $license = $this -> _ESOBJECT -> getLicense() -> renderFooter($this -> getTemplate(), $this->lmsInlineHelper());
            } else {
                $license = '<a class="license_permalink" href="'.$this->lmsInlineHelper().'?closeOnBack=true" target="_blank" title="'.htmlentities($this->_ESOBJECT->getTitle()).'"><es:title xmlns:es="http://edu-sharing.net/object" >'
                    . htmlentities($this->_ESOBJECT->getTitle())
                    . '</es:title></a>';
            }
        }

        $sequence = '';
        if($this -> _ESOBJECT -> getSequenceHandler() -> isSequence())
            $sequence = $this -> _ESOBJECT -> getSequenceHandler -> render($this -> getTemplate(), '/sequence/inline', $this->lmsInlineHelper());

        $metadata = '';
        if(ENABLE_METADATA_INLINE_RENDERING) {
	       	$metadata = $this -> _ESOBJECT -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/inline');
        }

        $data['footer'] = $this->getTemplate()->render('/footer/inline', array('license' => $license, 'metadata' => $metadata, 'sequence' => $sequence, 'title' => $this -> _ESOBJECT -> getTitle()));

        return $data;
    }
    
    /**
     * Display current ESObject.
     *
     * @param ESObject $ESObject
     */
    abstract protected function display(ESObject $ESObject);

    /**
     * Download current ESObject.
     *
     * @param ESObject $ESObject
     */
    abstract protected function download(ESObject $ESObject);

    /**
     * Render inline-portion for current ESObject.
     *
     * @param ESObject $ESObject
     */
    abstract protected function inline(ESObject $ESObject);

    /**
     *
     * @param string $Sql
     * @param ESObject $ESObject
     */
    protected function refineInstanceConstraints($Sql, ESObject $ESObject) {
        // nothing to refine by default.
        return $Sql;
    }

    public function instanceLocked(ESObject $ESObject) {
        
        $Logger = $this -> getLogger();
        $pdo = RsPDO::getInstance();
        
        try {
            $sql = $pdo -> formatQuery('SELECT `ESOBJECT_LOCK_OBJECT_ID` FROM `ESOBJECT_LOCK` '.
                'WHERE `ESOBJECT_LOCK_REP_ID` = :repid '.
                'AND `ESOBJECT_LOCK_CONTENT_HASH` = :contenthash '
                .'AND `ESOBJECT_LOCK_OBJECT_ID` = :objectid '.
                'AND `ESOBJECT_LOCK_OBJECT_VERSION` = :version');
                
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $ESObject->getRepId());
            $stmt -> bindValue(':contenthash', $ESObject->getContentHash());
            $stmt -> bindValue(':objectid', $ESObject -> getObjectID());
            $stmt -> bindValue(':version', $ESObject->getObjectVersion());
    
            $stmt -> execute();
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }

        if ($result) {
            $Logger -> debug('Instance locked.');
            return true;
        }
        
        return false;
    }

    public function instanceUnlock(ESObject $ESObject) {
        $Logger = $this -> getLogger();

        $pdo = RsPDO::getInstance();
        try {
            $sql = $pdo -> formatQuery('DELETE FROM `ESOBJECT_LOCK` WHERE `ESOBJECT_LOCK_REP_ID` = :repid AND `ESOBJECT_LOCK_OBJECT_ID` = :objectid AND `ESOBJECT_LOCK_OBJECT_VERSION` = :version');
    
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $ESObject -> getRepId());
            $stmt -> bindValue(':objectid', $ESObject -> getObjectID());
            $stmt -> bindValue(':version', $ESObject->getObjectVersion());
            $result = $stmt -> execute();
    
            if (!$result) {
                throw new Exception('Instance could not be unlocked. ' . print_r($pdo -> errorInfo(), true));
            }
    
            $Logger -> debug('Instance unlocked.');
            return true;
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }
    }

    public function instanceLock(ESObject $ESObject) {

        $pdo = RsPDO::getInstance();
        try {
            $sql = $pdo -> formatQuery('INSERT INTO `ESOBJECT_LOCK` (`ESOBJECT_LOCK_REP_ID`,`ESOBJECT_LOCK_OBJECT_ID`,`ESOBJECT_LOCK_OBJECT_VERSION`,`ESOBJECT_LOCK_CONTENT_HASH`) VALUES (:repid, :objectid, :objectversion, :contenthash)');
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $ESObject->getRepId());
            $stmt -> bindValue(':objectid', $ESObject->getObjectID());
            $stmt -> bindValue(':objectversion', $ESObject->getObjectVersion());
            $stmt -> bindValue(':contenthash', $ESObject->getContentHash());
            $result = $stmt -> execute();
            if (!$result) {
                throw new Exception('Error storing entry to lock table. PDO error info ' . print_r($stmt -> errorInfo(), true));
            return true;
        }
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }

        return false;
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
    public function instanceExists(ESObject $ESObject) {
        $Logger = $this -> getLogger();

        $pdo = RsPDO::getInstance();

        try {
            $sql = 'SELECT * FROM `ESOBJECT` ' . 'WHERE `ESOBJECT_REP_ID` = :repid ' . 'AND `ESOBJECT_CONTENT_HASH` = :contenthash ' . 'AND `ESOBJECT_OBJECT_ID` = :objectid ' . 'AND `ESOBJECT_LMS_ID` = :appid ' . 'AND `ESOBJECT_OBJECT_VERSION` = :version ' . 'AND `ESOBJECT_RESOURCE_ID` = :resourceid';

            $stmt = $pdo -> prepare($pdo->formatQuery($sql));
            $stmt -> bindValue(':repid', $ESObject->getRepId());
            $stmt -> bindValue(':contenthash', $ESObject->getContentHash());
            $stmt -> bindValue(':objectid', $ESObject -> getObjectID());
            $stmt -> bindValue(':appid', $ESObject -> getLmsId());
            $stmt -> bindValue(':version', $ESObject->getObjectVersion());
            $stmt -> bindValue(':resourceid', $ESObject->getResourceId());
            $stmt -> execute();
            
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $Logger -> debug('Instance exists.');
                $ESObject -> setInstanceData($result);
                return true;
            }
    
            $Logger -> debug('Instance does not exist.');
            return false;
        } catch (PDOException $e) {
            throw new Exception($e -> getMessage());
        }
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Interface::createInstance()
     */
    public function createInstance(ESObject $ESObject) {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Interface::process()
     */
    public function process($p_kind, ESObject $ESObject) {

        $Logger = $this -> getLogger();

        switch( strtolower($p_kind) ) {
            case ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD :
                $Logger -> debug('Calling Module::download()');
                return $this -> download($ESObject);
                break;

            case ESRender_Application_Interface::DISPLAY_MODE_INLINE :
                $Logger -> debug('Calling Module::inline()');
                return $this -> inline($ESObject);
                break;
                
                
                case ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC :
                	$Logger -> debug('Calling Module::dynamic()');
                	return $this -> dynamic($ESObject);
                	break;


            case ESRender_Application_Interface::DISPLAY_MODE_WINDOW :
                $Logger -> debug('Calling Module::display()');
                return $this -> display($ESObject);
                break;

            case ESRender_Application_Interface::DISPLAY_MODE_LOCKED :
                //this method is only implemented in video and audio module
                Config::set('locked', true);
                $Logger -> debug('Calling Module::locked()');
                return $this -> locked($ESObject);
                break;

            default :
                throw new Exception('Unhandled display-kind "' . $p_kind . '".');
        }

        return true;
    }

    /**
     * setCache
     */
    public function setCache() {
        return true;
    }

    /**
     *
     * @param string $Name
     *
     * @return ESRender_Module_Base
     */
    protected function setName($Name) {
        $this -> Name = (string)$Name;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getName() {
        return $this -> Name;
    }

    /**
     *
     *
     * @var ESRender_Application_Interface
     */
    private $RenderApplication = null;

    /**
     *
     *
     * @param ESRender_Application_Interface $RenderApplication
     * @return ESRender_Module_Base
     */
    protected function setRenderApplication(ESRender_Application_Interface $RenderApplication) {
        $this -> RenderApplication = $RenderApplication;
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Interface::getTimesOfUsage()
     */
    public function getTimesOfUsage() {
        return 2;
    }

    /**
     *
     * @var Logger
     */
    protected $Logger = null;

    /**
     * Set the logger-instance to use
     *
     * @param Logger $Logger
     */
    protected function setLogger(Logger $Logger) {
        $this -> Logger = $Logger;
        return $this;
    }

    /**
     *
     * @return Logger
     */
    protected function getLogger() {
        return $this -> Logger;
    }

    /**
     * Used to render templates.
     *
     * @var Phools_Template_Interface
     */
    private $Template = null;

    /**
     * Set the template-renderer to use
     *
     * @param Phools_Template_Interface $Template
     */
    protected function setTemplate(Phools_Template_Interface $Template) {
        $this -> Template = $Template;
        return $this;
    }

    /**
     *
     * @return Phools_Template_Interface
     */
    protected function getTemplate() {
        return $this -> Template;
    }

    protected function lmsInlineHelper()
    {
        return '{{{LMS_INLINE_HELPER_SCRIPT}}}';
    }


}
