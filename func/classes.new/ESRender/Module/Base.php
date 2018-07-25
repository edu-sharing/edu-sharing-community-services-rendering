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
     * @param array $requestData
     *
     * @return array
     */
    protected function prepareRenderData(array $requestData) {
        $license = '';
        if ($this -> _ESOBJECT -> getLicense()) {
            $license = $this -> _ESOBJECT -> getLicense() -> renderFooter($this -> getTemplate());
        }

        $data = array('title' => $this -> _ESOBJECT -> getTitle(),
                    'license' => $license,
                    'width' => $requestData['width'],
                    'height' => $requestData['height'],
                    'backLink' => $requestData['backLink']);

        if(ENABLE_METADATA_INLINE_RENDERING) {
	       	$metadata = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/inline');
			$data['metadata'] = $metadata;
        }
        
        return $data;
    }
    
    /**
     * Display current ESObject.
     *
     * @param array $requestData
     */
    abstract protected function display(array $requestData);

    /**
     * Download current ESObject.
     *
     * @param array $requestData
     */
    abstract protected function download(array $requestData);

    /**
     * Render inline-portion for current ESObject.
     *
     * @param array $requestData
     */
    abstract protected function inline(array $requestData);

    /**
     *
     */
    protected function _buildUsername(array $requestData) {
        $username = $requestData['user_name'];
        $username .= '@' . $requestData['app_id'];

        return $username;
    }

    /**
     *
     * @param string $Sql
     * @param array $requestData
     */
    protected function refineInstanceConstraints($Sql, array $requestData) {
        // nothing to refine by default.
        return $Sql;
    }

    public function instanceLocked(ESObject $ESObject, array $requestData, $contentHash) {
        
        $Logger = $this -> getLogger();

        $version = $requestData['version'];
        if (empty($version))
            $version = 0;

        $pdo = RsPDO::getInstance();
        
        try {
            $sql = $pdo -> formatQuery('SELECT `ESOBJECT_LOCK_OBJECT_ID` FROM `ESOBJECT_LOCK` '.
                'WHERE `ESOBJECT_LOCK_REP_ID` = :repid '.
                'AND `ESOBJECT_LOCK_CONTENT_HASH` = :contenthash '
                .'AND `ESOBJECT_LOCK_OBJECT_ID` = :objectid '.
                'AND `ESOBJECT_LOCK_OBJECT_VERSION` = :version');
                
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $requestData['rep_id']);
            $stmt -> bindValue(':contenthash', $contentHash);
            $stmt -> bindValue(':objectid', $ESObject -> getObjectID());
            $stmt -> bindValue(':version', $version);
    
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

    public function instanceUnlock(ESObject $ESObject, array $instanceParams, $contentHash) {
        $Logger = $this -> getLogger();

        $version = $instanceParams['version'];
        if (empty($version))
            $version = 0;

        $pdo = RsPDO::getInstance();
        try {
            $sql = $pdo -> formatQuery('DELETE FROM `ESOBJECT_LOCK` WHERE `ESOBJECT_LOCK_REP_ID` = :repid AND `ESOBJECT_LOCK_OBJECT_ID` = :objectid AND `ESOBJECT_LOCK_OBJECT_VERSION` = :version');
    
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $instanceParams['rep_id']);
            $stmt -> bindValue(':objectid', $ESObject -> getObjectID());
            $stmt -> bindValue(':version', $version);
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

    public function instanceLock($ESObject, $instanceParams, $contentHash) {

        $pdo = RsPDO::getInstance();
        try {
            $sql = $pdo -> formatQuery('INSERT INTO `ESOBJECT_LOCK` (`ESOBJECT_LOCK_REP_ID`,`ESOBJECT_LOCK_OBJECT_ID`,`ESOBJECT_LOCK_OBJECT_VERSION`,`ESOBJECT_LOCK_CONTENT_HASH`) VALUES (:repid, :objectid, :objectversion, :contenthash)');
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $instanceParams['rep_id']);
            $stmt -> bindValue(':objectid', $instanceParams['object_id']);
            $stmt -> bindValue(':objectversion', $instanceParams['version']);
            $stmt -> bindValue(':contenthash', $contentHash);
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
    public function instanceExists(ESObject $ESObject, array $requestData, $contentHash) {
        $Logger = $this -> getLogger();

        $version = $requestData['version'];
        if (empty($version))
            $version = 0;


        $resource_id = $requestData['resource_id'];
        if (empty($resource_id))
            $resource_id = 0;

        $pdo = RsPDO::getInstance();

        try {
            $sql = 'SELECT * FROM `ESOBJECT` ' . 'WHERE `ESOBJECT_REP_ID` = :repid ' . 'AND `ESOBJECT_CONTENT_HASH` = :contenthash ' . 'AND `ESOBJECT_OBJECT_ID` = :objectid ' . 'AND `ESOBJECT_LMS_ID` = :appid ' . 'AND `ESOBJECT_OBJECT_VERSION` = :version ' . 'AND `ESOBJECT_RESOURCE_ID` = :resourceid';

            $stmt = $pdo -> prepare($pdo->formatQuery($sql));
            $stmt -> bindValue(':repid', $requestData['rep_id']);
            $stmt -> bindValue(':contenthash', $contentHash);
            $stmt -> bindValue(':objectid', $ESObject -> getObjectID());
            $stmt -> bindValue(':appid', $requestData['app_id']);
            $stmt -> bindValue(':version', $version);
            $stmt -> bindValue(':resourceid', $resource_id);
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
    public function createInstance(array $requestData) {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Interface::process()
     */
    public function process($p_kind, array $requestData) {

        $Logger = $this -> getLogger();

        switch( strtolower($p_kind) ) {
            case ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD :
                $Logger -> debug('Calling Module::download()');
                return $this -> download($requestData);
                break;

            case ESRender_Application_Interface::DISPLAY_MODE_INLINE :
                $Logger -> debug('Calling Module::inline()');
                return $this -> inline($requestData);
                break;
                
                
                case ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC :
                	$Logger -> debug('Calling Module::dynamic()');
                	return $this -> dynamic($requestData);
                	break;


            case ESRender_Application_Interface::DISPLAY_MODE_WINDOW :
                $Logger -> debug('Calling Module::display()');
                return $this -> display($requestData);
                break;

            case ESRender_Application_Interface::DISPLAY_MODE_LOCKED :
                //this method is only implemented in video and audio module
                $Logger -> debug('Calling Module::locked()');
                return $this -> locked($requestData);
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
     *
     * @return ESRender_Application_Interface
     */
    public function getRenderApplication() {
        return $this -> RenderApplication;
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

    public function getRequestingDevice() {
        return $this -> requestingDevice;
    }
}
