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

require_once ('Interface.php');
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
    protected $esObject = null;

    /**
     * This module's name
     *
     * @var string
     */
    protected $name = '';

    /**
     *
     * @var string
     */
    protected $renderPath = '';

    /**
     *
     * @param string $name
     * @param ESRender_Application_Interface $RenderApplication
     * @param ESObject $pesObject
     * @param Logger $Logger
     * @param Phools_Template_Interface $Template
     */
    public function __construct($name, ESRender_Application_Interface $RenderApplication, ESObject $esObject, Logger $Logger, Phools_Template_Interface $Template) {
        //  parent::__construct();

        $this -> setname($name) -> setRenderApplication($RenderApplication) -> setLogger($Logger) -> setTemplate($Template);

        $this -> esObject = $esObject;
    }

    public function __destruct() {
        $this -> esObject = null;
        $this -> Logger = null;
        $this -> Template = null;
        $this -> RenderApplication = null;
    }

    /**
     * Prepare data to be used in templates.
     *
     * @return array
     */
    protected function prepareRenderData($showMetadata = true) {
        global $Locale, $Translate;
        $msg = array();
        $msg['hasNoContentLicense'] = new Phools_Message_Default('hasNoContentLicense');

        $data = array('title' => $this -> esObject -> getTitle(),
                    'width' => mc_Request::fetch('width', 'INT', 0),
                    'height' => mc_Request::fetch('height', 'INT', 0),
                    'backLink' => mc_Request::fetch('backLink', 'CHAR', ''));

        if(false === Config::get('hasContentLicense')) {
            $license = '<span class="edusharing_warning">' . htmlentities($msg['hasNoContentLicense']->localize($Locale, $Translate), ENT_COMPAT, 'utf-8') . '</span>';
        } else {
            if($this -> esObject -> getLicense()) {
                $license = $this -> esObject -> getLicense() -> renderFooter($this -> getTemplate(), $this->lmsInlineHelper());
            } else {
                $license = '<a class="license_permalink" href="'.$this->lmsInlineHelper().'&closeOnBack=true" target="_blank" title="'.htmlentities($this->esObject -> getTitle()).'"><es:title xmlns:es="http://edu-sharing.net/object" >'
                    . htmlentities($this->esObject -> getTitle())
                    . '</es:title></a>';
            }
        }

        $sequence = '';
        if($this -> esObject -> getSequenceHandler() -> isSequence())
            $sequence = $this -> esObject -> getSequenceHandler() -> render($this -> getTemplate(), '/sequence/inline', $this->lmsInlineHelper());

        $metadata = '';
        if(ENABLE_METADATA_INLINE_RENDERING && $showMetadata) {
	       	$metadata = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/inline');
        }

        $data['footer'] = $this->getTemplate()->render('/footer/inline', array('license' => $license, 'metadata' => $metadata, 'sequence' => $sequence, 'title' => $this -> esObject -> getTitle()));

        return $data;
    }

    /**
     * Download current ESObject.
     *
     * @param ESObject $ESObject
     */
    abstract protected function download();

    /**
     * Render inline-portion for current ESObject.
     *
     * @param ESObject $ESObject
     */
    abstract protected function inline();

    /**
     *
     * @param string $Sql
     */
    protected function refineInstanceConstraints($Sql) {
        // nothing to refine by default.
        return $Sql;
    }

    public function instanceLocked() {
        $Logger = $this -> getLogger();
        $pdo = RsPDO::getInstance();
        
        try {
            $sql = 'SELECT "ESOBJECT_LOCK_OBJECT_ID" FROM "ESOBJECT_LOCK" '.
                'WHERE "ESOBJECT_LOCK_REP_ID" = :repid '.
                'AND "ESOBJECT_LOCK_CONTENT_HASH" = :contenthash '
                .'AND "ESOBJECT_LOCK_OBJECT_ID" = :objectid '.
                'AND "ESOBJECT_LOCK_OBJECT_VERSION" = :version';
                
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $this -> esObject -> getRepId());
            $stmt -> bindValue(':contenthash', $this -> esObject -> getContentHash());
            $stmt -> bindValue(':objectid', $this -> esObject -> getObjectID());
            $stmt -> bindValue(':version', $this -> esObject -> getObjectVersion());

            $stmt -> execute();
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }

        if ($result) {
            $Logger -> debug('Instance is locked! Cannot process...');
            return true;
        }
        return false;
    }

    public function instanceUnlock() {
        $Logger = $this -> getLogger();

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'DELETE FROM "ESOBJECT_LOCK" WHERE "ESOBJECT_LOCK_REP_ID" = :repid AND "ESOBJECT_LOCK_OBJECT_ID" = :objectid AND "ESOBJECT_LOCK_OBJECT_VERSION" = :version';
    
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $this -> esObject -> getRepId());
            $stmt -> bindValue(':objectid', $this -> esObject -> getObjectID());
            $stmt -> bindValue(':version', $this -> esObject -> getObjectVersion());
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

    public function instanceLock() {
        $Logger = $this -> getLogger();

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'INSERT INTO "ESOBJECT_LOCK" ("ESOBJECT_LOCK_REP_ID","ESOBJECT_LOCK_OBJECT_ID","ESOBJECT_LOCK_OBJECT_VERSION","ESOBJECT_LOCK_CONTENT_HASH") VALUES (:repid, :objectid, :objectversion, :contenthash)';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $this -> esObject -> getRepId());
            $stmt -> bindValue(':objectid', $this -> esObject -> getObjectID());
            $stmt -> bindValue(':objectversion', $this -> esObject -> getObjectVersion());
            $stmt -> bindValue(':contenthash', $this -> esObject -> getContentHash());
            $result = $stmt -> execute();

            if (!$result) {
                throw new Exception('Error storing entry to lock table. PDO error info ' . print_r($stmt->errorInfo(), true));
            }

            $Logger -> debug('Locking instance...');
            return true;
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
     * From version 5.1 object version is no longer regarded. If an object's content do not change in different versions
     * we use the existing cache object.
     * In this case the object version will be overridden in this function and must be interpreted as content version!
     *
     * (non-PHPdoc)
     * @see ESRender_Module_Interface::instanceExists()
     */
    public function instanceExists() {
        $Logger = $this -> getLogger();

        $pdo = RsPDO::getInstance();

        try {
            $sql = 'SELECT * FROM "ESOBJECT" ' .
                'WHERE "ESOBJECT_REP_ID" = :repid ' .
                'AND "ESOBJECT_CONTENT_HASH" = :contenthash ' .
                'AND "ESOBJECT_OBJECT_ID" = :objectid ' .
                'AND "ESOBJECT_OBJECT_VERSION" = :version';

            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':repid', $this -> esObject -> getRepId());
            $stmt -> bindValue(':contenthash', $this -> esObject -> getContentHash());
            $stmt -> bindValue(':objectid', $this -> esObject -> getObjectID());
            $stmt -> bindValue(':version', $this->esObject -> getVersion());
            $stmt -> execute();
            
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $this -> esObject -> setInstanceData($result);

                // check if cache exists
                global $CC_RENDER_PATH;
                $module = $this -> esObject -> getModule();
                $src_file =  $CC_RENDER_PATH . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . $this->esObject->getSubUri_file();
                $src_file .= DIRECTORY_SEPARATOR . $this->esObject->getObjectIdVersion();
                if ((is_file($src_file)) || (is_readable($src_file))) {
                    $Logger -> debug('Instance exists.');
                    return true;
                }else{
                    $Logger -> debug('No cache, deleting from DB...');
                    try {
                        $this->esObject->deleteFromDb();
                    } catch (Exception $e) {
                        $Logger -> debug('Could not delete from DB: ' . $e);
                    }
                    return false;
                }
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
    public function createInstance() {
        return true;
    }

    /**
     * (non-PHPdoc)
     * @param $p_kind
     * @param $locked
     * @return bool
     * @throws Exception
     * @see ESRender_Module_Interface::process()
     */
    public function process($p_kind, $locked=null) {
        $Logger = $this -> getLogger();

        switch( strtolower($p_kind) ) {
            case ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD :
                $Logger -> debug('Calling Module::download()');
                return $this -> download();
                break;

            case ESRender_Application_Interface::DISPLAY_MODE_INLINE :
                $Logger -> debug('Calling Module::inline()');
                return $this -> inline();
                break;
                
            case ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC :
                $Logger -> debug('Calling Module::dynamic()');
                return $this -> dynamic();
                break;

            case ESRender_Application_Interface::DISPLAY_MODE_EMBED :
                $Logger -> debug('Calling Module::embed()');
                return $this -> embed();
                break;

            case ESRender_Application_Interface::DISPLAY_MODE_PRERENDER :
                $Logger -> debug('Calling Module::prerender()');
                return $this -> prerender();
                break;

            case ESRender_Application_Interface::DISPLAY_MODE_LOCKED :
                //this method is only implemented in video and audio module
                Config::set('locked', true);
                $Logger -> debug('Calling Module::locked()');
                return $this -> locked();
                break;

            default :
                throw new Exception('Unhandled display-kind "' . $p_kind . '".');
        }

        return true;
    }

    /**
     *
     * @param string $name
     *
     * @return ESRender_Module_Base
     */
    protected function setname($name) {
        $this -> name = (string)$name;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getname() {
        return $this -> name;
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
