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

include_once "Plattform.php";
include_once "ESModule.php";

/**
 * handles
 *
 * @author steffen gross / matthias hupfer
 * @version 1.0
 * @package core
 * @subpackage classes.new
 */
class ESObject {

    /**
     * Conversion-constants
     *
     */
    const CONVERSION_STATUS_WAIT = 'CONVERSION_STATUS_WAIT';
    const CONVERSION_STATUS_PROCESSING = 'CONVERSION_STATUS_PROCESSING';
    const CONVERSION_STATUS_PROCESSED = 'CONVERSION_STATUS_PROCESSED';
    const CONVERSION_STATUS_ERROR = 'CONVERSION_STATUS_ERROR';
    const CONVERSION_STATUS_STUCK = 'CONVERSION_STATUS_STUCK';

    /**
     *
     * @var int
     */
    protected $id = null;

    /**
     *
     * @var int
     */
    protected $moduleId = null;

    /**
     *
     * @var string
     */
    protected $title = null;

    /**
     *
     * @var string
     */
    protected $repId = null;

    /**
     *
     * @var string
     */
    protected $objectId = '';

    /**
     *
     * @var string
     */
    protected $version = null;

    /**
     *
     * @var string
     */
    protected $versionedObjectId = null;

    /**
     *
     * @var
     */
    protected $mimetype = '';

    /**
     *
     * @var string
     */
    protected $path = null;

    /**
     *
     * @var string
     */
    protected $resourceType = null;

    /**
     *
     * @var string
     */
    protected $resourceVersion = null;

    /**
     *
     * @var string
     */
    protected $filePath;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $license = '';

    /**
     * @var ESModule
     */
    public $module = null;

    /**
     *
     * @var $string
     */
    protected $hash = null;

    /**
     *
     * @var string
     */
    public $metadataHandler = null;

    /**
     *
     * @var string
     */
    public $sequenceHandler = null;

    /**
     *
     * @var stdClass
     */
    protected $data= null;

    /**
     *
     */
    public function __construct($data) {
        $this -> data = $data;
        $this -> module = new ESModule();
        $this -> setDataByNode();
    }

    public function getUser() {
        return $this -> data -> user;
    }

    public function getData() {
        return $this -> data;
    }

    public function getNode() {
        if (isset($this -> data -> node)){
            return $this -> data -> node;
        }
        return false;
    }

    public function getNodeProperty($key) {
        if ( !empty ($this -> data -> node)){
            if(property_exists ($this -> data -> node -> properties, $key)) {
                if (is_array($this->data->node -> properties->$key) && count($this->data->node->properties->$key) == 1){
                    return $this->data->node -> properties->$key[0];
                }
                return $this->data->node -> properties->$key;
            }
        }
        return false;
    }

    /**
     * Cleanup.
     *
     */
    public function __destruct() {
        $this -> data = null;
        $this -> module = null;
        $this -> Logger = null;
    }

    /**
     * Removes this object from database.
     *
     * @return bool
     */
    final public function deleteFromDb() {

        try {
            $pdo = RsPDO::getInstance();
            $sql = 'DELETE FROM "ESOBJECT" WHERE "ESOBJECT_ID" = ? AND "ESOBJECT_REP_ID" = ?';
            $values = array((int)$this -> id, $this -> repId);

            if (!empty($this -> version)) {
                $sql .= ' AND "ESOBJECT_OBJECT_VERSION" = ?';
                $values[] = $this -> version;
            }

            $stmt = $pdo -> prepare($sql);
            $result = $stmt -> execute($values);

            if (!$result)
                throw new Exception('Error deleting entry from lock table. PDO error info ' . print_r($stmt -> errorInfo(), true));

        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }

        return true;
    }


    public function setInstanceData(array $row) {
        $this -> id = $row['ESOBJECT_ID'];
        $this -> moduleId = $row['ESOBJECT_ESMODULE_ID'];
        $this -> title = $row['ESOBJECT_TITLE'];
        $this -> repId = $row['ESOBJECT_REP_ID'];
        $this -> objectId = $row['ESOBJECT_OBJECT_ID'];
        $this -> version = $row['ESOBJECT_OBJECT_VERSION'];
        $this -> mimetype = $row['ESOBJECT_MIMETYPE'];
        $this -> path = $row['ESOBJECT_PATH'];
        $this -> resourceType = $row['ESOBJECT_RESOURCE_TYPE'];
        $this -> resourceVersion = $row['ESOBJECT_RESOURCE_VERSION'];
        $this -> name = $row['ESOBJECT_ALF_FILENAME'];
        $this -> hash = $row['ESOBJECT_CONTENT_HASH'];

        $this -> module -> setModuleID($this -> moduleId);

        return $this;
    }

    /**
     *
     */
    final public function getId() {
        return $this -> id;
    }


    /**
     * Get object's mime-type.
     *
     * @return string
     */
    final public function getMimeType() {
        return $this -> mimetype;
    }

    /**
     * Get object's resource-type.
     *
     * @return string
     */
    final public function getResourceType() {
        return $this -> resourceType;
    }

    /**
     *
     */
    final public function getTitle() {
    	$title = $this -> getNode() -> title;
    	if(!empty($title))
    		return $title;
    	else
    		return $this -> getNode() -> name;
    }

    /**
     * Get object's title.
     *
     * @return string
     */
    final public function getFilename() {
        return $this -> name;
    }

    /**
     *
     */
    final public function getPath() {
        return MC_ROOT_URI . $this -> module -> getTmpFilepath() . '/' . $this -> getSubUri() . '/' . $this -> getObjectIdVersion();
    }

    /**
     *
     */
    final public function getFilePath()// deprecated
    {
    	global $CC_RENDER_PATH;
        return $CC_RENDER_PATH . DIRECTORY_SEPARATOR . $this -> module -> getName() . DIRECTORY_SEPARATOR . $this -> getSubPath() . DIRECTORY_SEPARATOR . $this -> getObjectIdVersion();
    }

    /**
     *
     */
    final public function getSubUri() {
        return $this -> path;
    }

    /**
     *
     */
    final public function setSubUri($path) {
        $this -> path = $path;
    }

    /**
     *
     */
    final public function getSubPath() {
        return str_replace('/', DIRECTORY_SEPARATOR, $this -> path);
    }

    /**
     *
     */
    final public function getObjectID() {
        return $this -> objectId;
    }

    /**
     *
     */
    final public function getModuleID() {
        return $this -> moduleId;
    }

    /**
     *
     */
    final public function getObjectVersion() {
        return $this -> version;
    }

    /**
     *
     */
    final public function getObjectIdVersion() {
        return $this -> objectId . '_' .  $this -> version;
    }

    /**
     *
     */
    final public function getModule() {
        include_once (dirname(__FILE__) . '/ESModule.php');
        $obj_module = new ESModule($this -> getModuleID());
        return $obj_module;
    }

    public function getLicense() {
        return $this -> license;
    }

    public function getContentHash()
    {
        return $this->hash;
    }

    public function setContentHash($ESOBJECT_CONTENT_HASH)
    {
        $this->hash = $ESOBJECT_CONTENT_HASH;
    }


    /**
     * Set the appropriate module for this object.
     *
     * @return bool
     */
    public function setModule() {
        // runtime sanity
        if (empty($this -> getNode())) {
            throw new Exception('No Alfresco-properties set.');
        }

        if(false === Config::get('hasContentLicense')) {
            Logger::getLogger('de.metaventis.esrender.index') -> info('"hasContentLicense" is false, using module "doc".');
            $this -> module -> setName('doc');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        if($this -> getNode() -> isDirectory) {
            if(in_array('ccm:collection', $this -> getNode() -> aspects)) {
                Logger::getLogger('de.metaventis.esrender.index')->info('Property "collection" is true, using module "collection".');
                $this->module->setName('collection');
                $this->module->loadModuleData();
                $this->moduleId = $this->module->getModuleId();
                return true;
            } else {
                Logger::getLogger('de.metaventis.esrender.index')->info('Property "directory" is true, using module "directory".');
                $this->module->setName('directory');
                $this->module->loadModuleData();
                $this->moduleId = $this->module->getModuleId();
                return true;
            }
        }

        if ($this->getNode()->type == 'ccm:saved_search') {
            Logger::getLogger('de.metaventis.esrender.index')->info('Node type is "ccm:saved_search" is true, using module "saved_search".');
            $this->module->setName('saved_search');
            $this->moduleId = $this->module->getModuleId();
            return true;
        }

        $toolInstanceKey = $this -> getNodeProperty('ccm:tool_instance_key');
        if(!empty($toolInstanceKey)) {
            Logger::getLogger('de.metaventis.esrender.index') -> info('{http://www.campuscontent.de/model/1.0}tool_instance_ref equals set, using module "lti".');
            $this -> module -> setName('lti');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        if(!empty($this -> getNode() -> remote)) {
            if ($this->getNode()->remote->repository->repositoryType == 'YOUTUBE') {
                Logger::getLogger('de.metaventis.esrender.index')->info('Property {http://www.campuscontent.de/model/1.0}remoterepositorytype equals "YOUTUBE", using module "url".');
                $this->module->setName('url');
                $this->module->loadModuleData();
                $this->moduleId = $this->module->getModuleId();
                return true;
            }

            if ($this->getNode()->remote->repository->repositoryType == 'PIXABAY') {
                Logger::getLogger('de.metaventis.esrender.index')->info('Property {http://www.campuscontent.de/model/1.0}remoterepositorytype equals "PIXABAY", using module "url".');
                $this->module->setName('url');
                $this->module->loadModuleData();
                $this->moduleId = $this->module->getModuleId();
                return true;
            }

            if ($this->getNode()->remote->repository->repositoryType == 'LEARNINGAPPS') {
                Logger::getLogger('de.metaventis.esrender.index')->info('Property {http://www.campuscontent.de/model/1.0}remoterepositorytype equals "LEARNINGAPPS", using module "learningapps".');
                $this->module->setName('learningapps');
                $this->module->loadModuleData();
                $this->moduleId = $this->module->getModuleId();
                return true;
            }
        }

        if ($this -> getNodeProperty('ccm:replicationsource') == 'oai:dmglib.org') {
            Logger::getLogger('de.metaventis.esrender.index') -> info('Property {http://www.campuscontent.de/model/1.0}replicationsource equals "oai:dmglib.org", using module "url".');
            $this -> module -> setName('url');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        if($this -> isLti13ToolObject()){
            Logger::getLogger('de.metaventis.esrender.index') -> info('isLti13ToolObject, using module "url".');
            $this -> module -> setName('url');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        $wwwurl = $this -> getNodeProperty('ccm:wwwurl');

        if ($this -> getNodeProperty('ccm:replicationsource') == 'DE.FWU' && !empty($wwwurl)) {
            Logger::getLogger('de.metaventis.esrender.index') -> info('Property {http://www.campuscontent.de/model/1.0}replicationsource equals "DE.FWU" and {http://www.campuscontent.de/model/1.0}wwwurl set, using module "url".');
            $this -> module -> setName('url');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        if (!empty($wwwurl)) {
            Logger::getLogger('de.metaventis.esrender.index') -> info('Property {http://www.campuscontent.de/model/1.0}wwwurl found, using module "url".');
            $this -> module -> setName('url');
        } else if ($this -> mimetype == 'application/zip' || $this -> mimetype == 'application/vnd.moodle.backup') {
            if (!$this -> module -> setModuleByResource($this -> resourceType, $this -> resourceVersion)) {
                Logger::getLogger('de.metaventis.esrender.index') -> info('Could not set module by resource-type/-version, using default ("doc") module.');
                $this -> module -> setName('doc');
            }
        } else if ($this -> mimetype == 'audio/mp4') {
            $this->module->setName('audio');
        } else {
            if (!$this -> module -> setModuleByMimetype($this -> mimetype)) {
                Logger::getLogger('de.metaventis.esrender.index') -> info('Could not set module by mimetype "'.$this->mimetype.'" using default ("doc") module.');
                $this -> module -> setName('doc');
            }
        }

        // @TODO: May remove this for now and configure all mime types via database
        $modulePath = '../../modules/';
        foreach(scandir($modulePath) as $mod){
            $phpFile = $modulePath.$mod.'/mod_'.$mod.'.php';
            if(!file_exists($phpFile)) {
                continue;
            }
            require_once ($phpFile);
            $className='mod_'.$mod;
            if(method_exists($className, 'canProcess') && $className::canProcess($this)) {
                Logger::getLogger('de.metaventis.esrender.index') -> info('module canProcess() returned true');
                $this -> module -> setName($mod);
                break;
            }
        }

        $this -> module -> loadModuleData();

        $this -> moduleId = $this -> module -> getModuleId();

        return true;
    }

    public function isLti13ToolObject() {
        if(in_array('ccm:ltitool_node', $this->getNode() -> aspects)) {
            return true;
        }else{
            return false;
        }
    }

    final public function setDataByNode() {

        $this -> id = 0;
        $this -> moduleId = 0;
        if (!empty($this -> getNode())){
            $this -> title = $this -> getNode() -> title;
            $this -> name = $this -> getNode() -> name;
            $this -> repId = $this -> getNode() -> ref -> repo;
            $this -> objectId = $this -> getNode() -> ref -> id;
            $this -> version = $this -> getNode() -> content -> version;
            $this -> mimetype = $this -> getNode() -> mimetype;
            $this -> hash = $this -> getNode() -> content -> hash;
        }
        $this -> path = '';
        if(empty($this -> title)){
            $this -> title = $this -> name;
        }
        if(empty($this -> hash)){
            $this -> hash = 0;
        }

        $ressourcetype = $this -> getNodeProperty('ccm:ccressourcetype');
        if (!empty($ressourcetype)) {
            $this -> resourceType = $ressourcetype;
            if ($this -> resourceType == 'imsqti') {
                $this -> mimetype = 'application/zip';
            }
        }

        $ressourceversion = $this -> getNodeProperty('ccm:ccressourceversion');
        if (!empty($ressourceversion))
            $this -> resourceVersion = $ressourceversion;

        $commonlicense_key = $this -> getNodeProperty('ccm:commonlicense_key');
        if(!empty($commonlicense_key))
        	$this -> license = new ESRender_License($this);

        $this -> metadataHandler = new ESRender_Metadata_Handler($this);
        $this -> sequenceHandler = new ESRender_Sequence_Handler($this);

        return true;
    }

    final public function setData2Db() {

        $arrFields = array(
           'ESOBJECT_ESMODULE_ID' => intval($this -> module -> getModuleId()),
           'ESOBJECT_TITLE' => $this -> title,
           'ESOBJECT_REP_ID' => $this -> repId,
           'ESOBJECT_OBJECT_ID' => $this -> objectId,
           'ESOBJECT_OBJECT_VERSION' => $this -> version,
           'ESOBJECT_MIMETYPE' => $this -> mimetype,
           'ESOBJECT_RESOURCE_TYPE' => $this -> resourceType,
           'ESOBJECT_RESOURCE_VERSION' => $this -> resourceVersion,
           'ESOBJECT_PATH' => $this -> path,
           'ESOBJECT_ALF_FILENAME' => $this -> name,
           'ESOBJECT_CONTENT_HASH' => $this -> hash
        );

        $pdo = RsPDO::getInstance();
        try {
            $sql = "INSERT INTO \"ESOBJECT\" (\"";
            $sql .= implode('","', array_keys($arrFields));
            $sql .= "\") VALUES (:";
            $sql .= implode(',:', array_keys($arrFields));
            $sql .= ")";
            $stmt = $pdo -> prepare($sql);
            foreach($arrFields as $key => $value) {
                if($key === 'ESOBJECT_ESAPPLICATION_ID' || $key === 'ESOBJECT_ESMODULE_ID')
                    $type = PDO::PARAM_INT;
                else
                    $type = PDO::PARAM_STR;
                $stmt -> bindValue(':'.$key, $value, $type);
            }
            $result = $stmt -> execute();
            if(!$result)
                throw new Exception('Error storing object in DB. ' . print_r($stmt -> errorInfo(), true));
            //$this -> id = ($pdo->getDriver() === 'pgsql') ? $pdo->lastInsertId('h5p_contents_id_seq') : $pdo->lastInsertId();  //why?
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }
        return true;
    }

    public function addToConversionQueue($format, $filename, $outputFilename, $mimeType, $resolution = 0) {
        $arr = array(
            'ESOBJECT_CONVERSION_OBJECT_ID' => $this -> id,
            'ESOBJECT_CONVERSION_FORMAT' => $format,
            'ESOBJECT_CONVERSION_FILENAME' => $filename,
            'ESOBJECT_CONVERSION_OUTPUT_FILENAME' => $outputFilename,
            'ESOBJECT_CONVERSION_TIME' => time(),
            'ESOBJECT_CONVERSION_STATUS' => self::CONVERSION_STATUS_WAIT,
            'ESOBJECT_CONVERSION_MIMETYPE' => $mimeType,
            'ESOBJECT_CONVERSION_RESOLUTION' => $resolution
        );

        $pdo = RsPDO::getInstance();

        try {
            $sql = "INSERT INTO \"ESOBJECT_CONVERSION\" (\"";
            $sql .= implode('","',array_keys($arr));
            $sql .= "\") VALUES (:";
            $sql .= implode(',:', array_keys($arr));
            $sql .= ")";
            $stmt = $pdo -> prepare($sql);
            foreach($arr as $key => $value) {
                if($key === 'ESOBJECT_CONVERSION_OBJECT_ID'){
                    $type = PDO::PARAM_INT;
                }
                else{
                    $type = PDO::PARAM_STR;
                }
                $stmt -> bindValue(':'.$key, $value, $type);
            }
            $result = $stmt -> execute();
            if(!$result){
                throw new Exception('Error storing conversion entry in DB.' . print_r($pdo -> errorInfo(), true));
            }
            return true;
         } catch (PDOException $e) {
             throw new Exception($e -> getMessage());
         }
    }

    public function setConversionStateProcessed($objId, $format, $resolution = null) {
        $this -> setConversionState($objId, $format, $resolution, self::CONVERSION_STATUS_PROCESSED);
    }

    public function setConversionStateProcessing($objId, $format, $resolution = null) {
        $this -> setConversionState($objId, $format, $resolution, self::CONVERSION_STATUS_PROCESSING);
    }

    public function setConversionStateError($objId, $format, $errorcode, $resolution = null) {
        $this -> setConversionState($objId, $format, $resolution, self::CONVERSION_STATUS_ERROR . ' ' . $errorcode);
    }

    public function setConversionStateStuck($objId, $format, $resolution = null) {
        $this -> setConversionState($objId, $format, $resolution, self::CONVERSION_STATUS_STUCK);
    }

    protected function setConversionState($objId, $format, $resolution,  $state) {

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'UPDATE "ESOBJECT_CONVERSION" set "ESOBJECT_CONVERSION_STATUS" = :convstatus, "ESOBJECT_CONVERSION_TIME" = :time ' .
                'WHERE "ESOBJECT_CONVERSION_OBJECT_ID" = :objectid AND "ESOBJECT_CONVERSION_FORMAT" = :format';

            if($resolution){
                $sql .= '  AND "ESOBJECT_CONVERSION_RESOLUTION" = :resolution';
            }

            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':convstatus', $state);
            $stmt -> bindValue(':time', time(), PDO::PARAM_INT);
            $stmt -> bindValue(':objectid', $objId, PDO::PARAM_INT);
            $stmt -> bindValue(':format', $format);
            if($resolution){
                $stmt -> bindValue(':resolution', $resolution);
            }

            $result = $stmt -> execute();
            if(!$result)
                throw new Exception('Error setting conversion status ' . $state . '. ' . print_r($pdo -> errorInfo(), true));
            return true;
        } catch (PDOException $e) {
            throw new Exception($e -> getMessage());
        }
    }

    public function currentlyInConversion($format, $resolution = null) {
        try {
            $pdo = RsPDO::getInstance();
            $sql = 'SELECT "ESOBJECT_CONVERSION_OBJECT_ID" FROM "ESOBJECT_CONVERSION" WHERE "ESOBJECT_CONVERSION_OBJECT_ID" = :objectid AND "ESOBJECT_CONVERSION_FORMAT" = :format AND "ESOBJECT_CONVERSION_STATUS" = :inconversion  AND "ESOBJECT_CONVERSION_RESOLUTION" = :resolution';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindValue(':objectid', $this -> id, PDO::PARAM_INT);
            $stmt -> bindValue(':format', $format);
            $stmt -> bindValue(':inconversion', 'CONVERSION_STATUS_PROCESSING');
            $stmt -> bindValue(':resolution', $resolution);
            $stmt -> execute();
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);
            if(!$result){
                return false;
            }else{
                return true;
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return false;
        }
    }

    public function inConversionQueue($format, $resolution = null) {

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'SELECT "ESOBJECT_CONVERSION_OBJECT_ID" FROM "ESOBJECT_CONVERSION" WHERE "ESOBJECT_CONVERSION_OBJECT_ID" = :objectid AND "ESOBJECT_CONVERSION_FORMAT" = :format';
            if($resolution){
                $sql .= ' AND "ESOBJECT_CONVERSION_RESOLUTION" = :resolution';
            }

            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':objectid', $this -> id, PDO::PARAM_INT);
            $stmt -> bindValue(':format', $format, PDO::PARAM_STR);
            if($resolution)
                $stmt -> bindValue(':resolution', $resolution, PDO::PARAM_STR);

            $stmt -> execute();
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);

            if(!$result)
                return false;
            else
                return true;

        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }
    }

    public function conversionFailed($format, $resolution = null) {

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'SELECT "ESOBJECT_CONVERSION_OBJECT_ID" FROM "ESOBJECT_CONVERSION" WHERE "ESOBJECT_CONVERSION_OBJECT_ID" = :objectid AND "ESOBJECT_CONVERSION_FORMAT" = :format AND "ESOBJECT_CONVERSION_STATUS" like :error';
            if($resolution)
                $sql .= ' AND "ESOBJECT_CONVERSION_RESOLUTION" = :resolution';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':objectid', $this -> id, PDO::PARAM_INT);
            $stmt -> bindValue(':format', $format);
            $stmt -> bindValue(':error', '%ERROR%');

            if($resolution){
                $stmt -> bindValue(':resolution', $resolution);
            }

            $stmt -> execute();
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);
            if(!$result){
                return false;
            }else{
                return true;
            }

        } catch(Exception $e) {
            throw new Exception($e -> getMessage());
        }
    }

    final public function getPathfile() {
        return MC_ROOT_URI . $this -> module -> getTmpFilepath() . '/' . $this -> getSubUri_file() . '/' . $this -> getObjectIdVersion();
    }

    final public function getSubUri_file() {
        return $this -> path;
    }

    public function getPositionInConversionQueue($format, $resolution = null) {
        $pdo = RsPDO::getInstance();
        try {
            $sql = 'SELECT COUNT("ESOBJECT_CONVERSION_OBJECT_ID") AS "SUM" FROM "ESOBJECT_CONVERSION" WHERE "ESOBJECT_CONVERSION_STATUS" = :state';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':state', self::CONVERSION_STATUS_WAIT);
            $stmt -> execute();
            $sum = $stmt -> fetchObject() -> SUM;

            $sql = 'SELECT COUNT("ESOBJECT_CONVERSION_ID") AS "POS" FROM "ESOBJECT_CONVERSION"
                        WHERE "ESOBJECT_CONVERSION_STATUS" = :status
                          AND "ESOBJECT_CONVERSION_ID" < (
                              SELECT "ESOBJECT_CONVERSION_ID" FROM "ESOBJECT_CONVERSION"
                              WHERE "ESOBJECT_CONVERSION_OBJECT_ID" = :objectid
                                AND "ESOBJECT_CONVERSION_FORMAT" = :format' . (($resolution) ? ' AND "ESOBJECT_CONVERSION_RESOLUTION" = :resolution' : '') . ')';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':objectid', $this->id, PDO::PARAM_INT);
            $stmt -> bindValue(':format', $format);
            $stmt -> bindValue(':status', self::CONVERSION_STATUS_WAIT);
            if($resolution)
                $stmt -> bindValue(':resolution', $resolution);
            $stmt -> execute();
            $pos = $stmt -> fetchObject() -> POS;
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }
        $pos = $pos + 1;
        if($sum < $pos)
            $sum = $pos;

        return $pos . ' / ' . $sum;
    }

    public function getPreviewUrl() {
        if(!empty(Config::get('base64Preview')))
            return Config::get('base64Preview');
        $previewUrl = $this -> getNode() -> preview -> url ;
        return $previewUrl;
    }

    public function renderOriginalDeleted($display_kind, $template) {
        if($display_kind == 'dynamic') {
            $tempArray['title'] = $this->getTitle();
            if(Config::get('showMetadata'))
                $tempArray['metadata'] = $this -> metadataHandler -> render($template, '/metadata/dynamic');
            echo $template -> render('/special/originaldeleted/dynamic', $tempArray);
        } else if($display_kind == 'inline') {
            $tempArray['title'] = $this->getTitle();
            if(ENABLE_METADATA_INLINE_RENDERING) {
                $tempArray['metadata'] = $this -> metadataHandler -> render($template, '/metadata/inline');
            }
            echo $template -> render('/special/originaldeleted/inline', $tempArray);
        }
        exit(0);
    }

    public function update() {
        if($this->getTitle() !== $this->title) {
            try {
                $pdo = RsPDO::getInstance();
                $sql = 'UPDATE "ESOBJECT" SET "ESOBJECT_TITLE" = :title WHERE "ESOBJECT_ID" = :id';
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindValue(':title', $this->getTitle());
                $stmt -> bindValue(':id', $this->id, PDO::PARAM_INT);
                $result = $stmt -> execute();
                if(!$result)
                    throw new Exception('Error updating title ' . print_r($pdo -> errorInfo(), true));
                $this->title = $this->getTitle();
            } catch(PDOException $e) {
                throw new Exception($e -> getMessage());
            }
        }

        if($this -> getNodeProperty('cm:name') !== $this->name) {
            try {
                $pdo = RsPDO::getInstance();
                $sql = 'UPDATE "ESOBJECT" SET "ESOBJECT_ALF_FILENAME" = :name WHERE "ESOBJECT_ID" = :id';
                $stmt = $pdo -> prepare($sql);
                $stmt -> bindValue(':name', $this -> getNodeProperty('cm:name'));
                $stmt -> bindValue(':id', $this->id, PDO::PARAM_INT);
                $result = $stmt -> execute();
                if(!$result)
                    throw new Exception('Error updating name ' . print_r($pdo -> errorInfo(), true));
                $this->name = $this -> getNodeProperty('cm:name');
            } catch(PDOException $e) {
                throw new Exception($e -> getMessage());
            }
        }

    }

    public function getRepId(): string
    {
        return $this->repId;
    }

    public function getVersion(): string
    {
        return $this->version ?? '';
    }

    public function getResourceVersion(): string
    {
        return $this->resourceVersion;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getMetadataHandler()
    {
        return $this->metadataHandler;
    }


    public function getSequenceHandler()
    {
        return $this->sequenceHandler;
    }


    /**
     * returns true if the user has the given permission on the node
     * will obey license check for objects inside collections
     */
    public function hasPermission(string $permission) {
        if (in_array('ccm:collection_io_reference', $this->getNode()->aspects)) {
            // is it a licensed node? check the original for access (new since 5.1)
            // accessEffective is introduced in 9.1!
            if ($this->getNode()->accessEffective) {
                return in_array($permission, $this->getNode()->accessEffective);
            }
        }
        return in_array($permission, $this->getNode()->access);
    }
}
