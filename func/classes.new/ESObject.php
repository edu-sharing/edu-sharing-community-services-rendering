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
     * @var string
     */
    protected $appId = null;

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
     * @var the remote's lms-identifier, e.g. 'alf' or 'moodle197'
     */
    protected $lmsId = null;

    /**
     * @var the remote's lms course-identifier this resource belongs to
     */
    protected $courseId = null;

    /**
     * Unique resource-identifier from used LMS.
     * @var int
     */
    protected $resourceId = 0;

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
     * @var Node
     */
    protected $contentNode = null;

    /**
     * @return mixed
     */

    /**
     *
     * @var $string
     */
    protected $hash = null;

    /**
     *
     * @var string
     */
    public $metadatahHandler = null;

    /**
     *
     * @var string
     */
    public $sequenceHandler = null;

    /**
     *
     */
    public function __construct(ESContentNode $ESContentNode) {
        $this->contentNode = $ESContentNode;
        $this -> module = new ESModule();
        $this -> setDataByNode();
        return true;
    }

    /**
     * Cleanup.
     *
     */
    public function __destruct() {
        $this -> contentNode = null;
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
            $sql = 'DELETE FROM `ESOBJECT` WHERE `ESOBJECT_ID` = ? AND `ESOBJECT_REP_ID` = ?';
            $values = array((int)$this -> id, $this -> repId);
            
            if (!empty($this -> lmsId)) {
                $sql .= ' AND `ESOBJECT_LMS_ID` = ? ';
                $values[] = $this -> lmsId;
                if(!empty($this -> courseId)) {
                    $sql .= ' AND `ESOBJECT_COURSE_ID` = ?';
                    $values[] = $this -> courseId;
                }
            }
            if (!empty($this -> version)) {
                $sql .= ' AND `ESOBJECT_OBJECT_VERSION` = ?';
                $values[] = $this -> version;
            }
            
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            $result = $stmt -> execute($values);
            
            if (!$result)
                throw new Exception('Error deleting entry from lock table. PDO error info ' . print_r($stmt -> errorInfo(), true));
            
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
            return false;
        }

        return true;
    }


    public function setInstanceData(array $row) {
        $this -> id = $row['ESOBJECT_ID'];
        $this -> appId = $row['ESOBJECT_ESAPPLICATION_ID'];
        $this -> moduleId = $row['ESOBJECT_ESMODULE_ID'];
        $this -> title = $row['ESOBJECT_TITLE'];
        $this -> repId = $row['ESOBJECT_REP_ID'];
        $this -> objectId = $row['ESOBJECT_OBJECT_ID'];
        $this -> version = $row['ESOBJECT_OBJECT_VERSION'];
        $this -> versionedObjectId = $row['ESOBJECT_VERSIONED_OBJECT_ID'];
        $this -> mimetype = $row['ESOBJECT_MIMETYPE'];
        $this -> path = $row['ESOBJECT_PATH'];
        $this -> lmsId = $row['ESOBJECT_LMS_ID'];
        $this -> courseId = $row['ESOBJECT_COURSE_ID'];
        $this -> resourceId = $row['ESOBJECT_RESOURCE_ID'];
        $this -> resourceType = $row['ESOBJECT_RESOURCE_TYPE'];
        $this -> resourceVersion = $row['ESOBJECT_RESOURCE_VERSION'];
        $this -> filePath = $row['ESOBJECT_FILE_PATH'];
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
     *
     */
    final public function setCache() {
        return true;
    }

    /**
     * @param Node $p_node
     */
    final public function setContentNode(ESContentNode $p_node) {
        $this -> contentNode = $p_node;
    }

    /*
     * @return ESContentNode
     */
    final public function getContentNode() {
        return $this -> contentNode;
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
    	$title = $this -> contentNode -> getNode() -> title;
    	if(!empty($title))
    		return $title;
    	else
    		return $this -> contentNode -> getNode() -> name;
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
     * @return string
     */
    final public function getEsobjectFilePath() {
        return $this -> filePath;
    }

    /**
     *
     */
    final public function setFilePath($path)// deprecated
    {
        $this -> filePath = $path;
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
    final public function setSubUri($sub_uri) {
        $this -> path = $sub_uri;
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
    final public function getPreviewPath() {
        return $this -> path;
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
    final public function getVersionedObjectId() {
        return $this -> versionedObjectId;
    }

    /**
     *
     */
    final public function getObjectIdVersion() {
        return $this -> objectId . $this -> version;
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
        if (empty($this -> contentNode)) {
            throw new Exception('No Alfresco-properties set.');
        }

        if(false === Config::get('hasContentLicense')) {
            error_log('"hasContentLicense" is false!');
            $this -> module -> setName('doc');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        if($this -> contentNode -> node -> directory) {
            error_log('Property "directory" is true, using module "directory".');
            $this -> module -> setName('directory');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        $toolInstanceKey = $this -> contentNode -> getNodeProperty('ccm:tool_instance_key');
        if(!empty($toolInstanceKey)) {
            error_log('ccm:tool_instance_ref equals set, using module "lti".');
            $this -> module -> setName('lti');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        if ($this -> contentNode -> getNodeProperty('ccm:remoterepositorytype') == 'YOUTUBE') {
            error_log('Property ccm:remoterepositorytype equals "YOUTUBE", using module "url".');
            $this -> module -> setName('url');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        if ($this -> contentNode -> getNodeProperty('ccm:remoterepositorytype') == 'LEARNINGAPPS') {
            error_log('Property ccm:remoterepositorytype equals "LEARNINGAPPS", using module "learningapps".');
            $this -> module -> setName('learningapps');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        if ($this -> contentNode -> getNodeProperty('ccm:replicationsource') == 'oai:dmglib.org') {
            error_log('Property ccm:replicationsource equals "oai:dmglib.org", using module "url".');
            $this -> module -> setName('url');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        $wwwurl = $this -> contentNode -> getNodeProperty('ccm:wwwurl');

        if ($this -> contentNode -> getNodeProperty('ccm:replicationsource') == 'DE.FWU' && !empty($wwwurl)) {
            error_log('Property ccm:replicationsource equals "DE.FWU" and ccm:wwwurl set, using module "url".');
            $this -> module -> setName('url');
            $this -> module -> loadModuleData();
            $this -> moduleId = $this -> module -> getModuleId();
            return true;
        }

        // load appropriate module
        if (!empty($wwwurl)) {
            error_log('Property ccm:wwwurl found, using module "url".');
            $this -> module -> setName('url');
        } else if ($this -> mimetype == 'application/zip' || $this -> mimetype == 'application/vnd.moodle.backup') {
            if (!$this -> module -> setModuleByResource($this -> resourceType, $this -> resourceVersion)) {
                error_log('Could not set module by resource-type/-version, using default ("doc") module.');
                $this -> module -> setName('doc');
            }
        } else {
            if (!$this -> module -> setModuleByMimetype($this -> mimetype)) {
                error_log('Could not set module by mimetype using default ("doc") module.');
                $this -> module -> setName('doc');
            }
        }

        $this -> module -> loadModuleData();

        $this -> moduleId = $this -> module -> getModuleId();

        return true;
    }

    final public function setDataByNode() {

        $this -> id = 0;
        $this -> moduleId = 0;
        $this -> title = $this -> contentNode -> getNode() -> title;
        $this -> name = $this -> contentNode -> getNode() -> name;
        if(empty($this -> title))
            $this -> title = $this -> name;
        $this -> repId = $this -> contentNode -> getNode() -> ref -> repo;
        $this -> objectId = $this -> contentNode->getnode() -> ref -> id;
        $this -> version = $this -> contentNode->getNode() -> content -> version;
        $this -> mimetype = $this -> contentNode -> getNode() -> mimetype;
        $this -> path = '';
        $this -> resourceId = mc_Request::fetch('resource_id', 'INT', 0);
        $this -> filePath = '';
        $this -> hash = $this -> contentNode -> getNode()->content -> hash;
        $this -> lmsId = mc_Request::fetch('app_id', 'CHAR', $this -> contentNode -> getNode() -> ref -> repo);

        $ressourcetype = $this -> contentNode -> getNodeProperty('ccm:ccressourcetype');
        if (!empty($ressourcetype)) {
            $this -> resourceType = $ressourcetype;
            if ($this -> resourceType == 'imsqti') {
                $this -> mimetype = 'application/zip';
            }
        }

        $ressourceversion = $this -> contentNode -> getNodeProperty('ccm:ccressourceversion');
        if (!empty($ressourceversion))
            $this -> resourceVersion = $ressourceversion;
        
        $commonlicense_key = $this -> contentNode -> getNodeProperty('ccm:commonlicense_key');
        if(!empty($commonlicense_key))
        	$this -> license = new ESRender_License($this);

        $this -> metadataHandler = new ESRender_Metadata_Handler($this);
        $this -> sequenceHandler = new ESRender_Sequence_Handler($this);

        return true;
    }

    final public function setData2Db() {

        $arrFields = array(
           'ESOBJECT_ESAPPLICATION_ID' => intval($this -> appId),
           'ESOBJECT_ESMODULE_ID' => intval($this -> module -> getModuleId()),
           'ESOBJECT_TITLE' => $this -> title,
           'ESOBJECT_REP_ID' => $this -> repId,
           'ESOBJECT_OBJECT_ID' => $this -> objectId,
           'ESOBJECT_OBJECT_VERSION' => $this -> version,
           'ESOBJECT_MIMETYPE' => $this -> mimetype,
           'ESOBJECT_LMS_ID' => $this -> lmsId,
           'ESOBJECT_COURSE_ID' => $this -> courseId,
           'ESOBJECT_RESOURCE_ID' => $this -> resourceId,
           'ESOBJECT_RESOURCE_TYPE' => $this -> resourceType,
           'ESOBJECT_RESOURCE_VERSION' => $this -> resourceVersion,
           'ESOBJECT_PATH' => $this -> path,
           'ESOBJECT_FILE_PATH' => $this -> filePath,
           'ESOBJECT_ALF_FILENAME' => $this -> name,
           'ESOBJECT_CONTENT_HASH' => $this -> hash
        );
        
        $pdo =RsPDO::getInstance();
        try {
            $sql = 'INSERT INTO `ESOBJECT` (`';
            $sql .= implode('`,`', array_keys($arrFields));
            $sql .= '`) VALUES (:';
            $sql .= implode(',:', array_keys($arrFields));
            $sql .= ')';
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            foreach($arrFields as $key => $value) {
                if($key == 'ESOBJECT_ESAPPLICATION_ID' || $key == 'ESOBJECT_ESMODULE_ID')
                    $type = PDO::PARAM_INT;
                else
                    $type = PDO::PARAM_STR;
                $stmt -> bindValue(':'.$key, $value, $type);
            }
            $result = $stmt -> execute();
            if(!$result)
                throw new Exception('Error storing object in DB. ' . print_r($stmt -> errorInfo(), true));
            $this -> id = $pdo -> lastInsertId();
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }
        return true;
    }

    public function addToConversionQueue($format, $dirSep, $filename, $outputFilename, $renderPath, $mimeType) {
        $arr = array(
            'ESOBJECT_CONVERSION_OBJECT_ID' => $this -> id,
            'ESOBJECT_CONVERSION_FORMAT' => $format,
            'ESOBJECT_CONVERSION_DIR_SEPERATOR' => $dirSep,
            'ESOBJECT_CONVERSION_FILENAME' => $filename,
            'ESOBJECT_CONVERSION_OUTPUT_FILENAME' => $outputFilename,
            'ESOBJECT_CONVERSION_RENDER_PATH' => $renderPath,
            'ESOBJECT_CONVERSION_TIME' => time(),
            'ESOBJECT_CONVERSION_STATUS' => self::CONVERSION_STATUS_WAIT,
            'ESOBJECT_CONVERSION_MIMETYPE' => $mimeType
        );
    
        $pdo = RsPDO::getInstance();
        
        try {
            $sql = 'INSERT INTO `ESOBJECT_CONVERSION` (`';
            $sql .= implode('`,`', array_keys($arr));
            $sql .= '`) VALUES (:';
            $sql .= implode(',:', array_keys($arr));
            $sql .= ')';
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            foreach($arr as $key => $value) {
                if($key == 'ESOBJECT_CONVERSION_OBJECT_ID')
                    $type = PDO::PARAM_INT;
                else
                    $type = PDO::PARAM_STR;
                $stmt -> bindValue(':'.$key, $value, $type);
            }
            $result = $stmt -> execute();
            if(!$result)
                throw new Exception('Error storing conversion entry in DB.' . print_r($pdo -> errorInfo(), true));
            return true;
         } catch (PDOException $e) {
             throw new Exception($e -> getMessage());
         }
    }

    public function setConversionStateProcessed($objId, $format) {
        $this -> setConversionState($objId, $format, self::CONVERSION_STATUS_PROCESSED);
    }

    public function setConversionStateProcessing($objId, $format) {
        $this -> setConversionState($objId, $format, self::CONVERSION_STATUS_PROCESSING);
    }
    
    public function setConversionStateError($objId, $format, $errorcode) {
        $this -> setConversionState($objId, $format, self::CONVERSION_STATUS_ERROR . ' ' . $errorcode);
    }
    
    public function setConversionStateStuck($objId, $format) {
        $this -> setConversionState($objId, $format, self::CONVERSION_STATUS_STUCK);
    }

    protected function setConversionState($objId, $format, $state) {

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'UPDATE `ESOBJECT_CONVERSION` set `ESOBJECT_CONVERSION_STATUS` = :convstatus, `ESOBJECT_CONVERSION_TIME` = :time ' .
                'WHERE `ESOBJECT_CONVERSION_OBJECT_ID` = :objectid AND `ESOBJECT_CONVERSION_FORMAT` = :format';
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            $stmt -> bindValue(':convstatus', $state);
            $stmt -> bindValue(':time', time(), PDO::PARAM_INT);
            $stmt -> bindValue(':objectid', $objId, PDO::PARAM_INT);
            $stmt -> bindValue(':format', $format);
            
            $result = $stmt -> execute();
            if(!$result)
                throw new Exception('Error setting conversion status ' . $state . '. ' . print_r($pdo -> errorInfo(), true));
            return true;
        } catch (PDOException $e) {
            throw new Exception($e -> getMessage());
        }
    }

    public function inConversionQueue($format) {

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'SELECT `ESOBJECT_CONVERSION_OBJECT_ID` FROM `ESOBJECT_CONVERSION` WHERE `ESOBJECT_CONVERSION_OBJECT_ID` = :objectid AND `ESOBJECT_CONVERSION_FORMAT` = :format';
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            $stmt -> bindValue(':objectid', $this -> id, PDO::PARAM_INT);
            $stmt -> bindValue(':format', $format, PDO::PARAM_STR);
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
    
    public function conversionFailed($format) {

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'SELECT `ESOBJECT_CONVERSION_OBJECT_ID` FROM `ESOBJECT_CONVERSION` WHERE `ESOBJECT_CONVERSION_OBJECT_ID` = :objectid AND `ESOBJECT_CONVERSION_FORMAT` = :format AND `ESOBJECT_CONVERSION_STATUS` like :error';
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            $stmt -> bindValue(':objectid', $this -> id, PDO::PARAM_INT);
            $stmt -> bindValue(':format', $format);
            $stmt -> bindValue(':error', '%ERROR%');
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);
            if(!$result)
                return false;
            else
                return true;
        } catch(Exception $e) {
            throw new Exception($e -> getMessage());
        }
    }

    final public function getPathfile() {
        return MC_ROOT_URI . $this -> module -> getTmpFilepath() . '/' . $this -> getSubUri_file() . '/' . $this -> getObjectIdVersion();
    }

    final public function getSubUri_file() {
        return $this -> filePath;
    }
    
    public function getPositionInConversionQueue($format) {
        $pdo = RsPDO::getInstance();
        try {
            $sql = 'SELECT COUNT(`ESOBJECT_CONVERSION_OBJECT_ID`) AS `SUM` FROM `ESOBJECT_CONVERSION` WHERE `ESOBJECT_CONVERSION_STATUS` = :state';
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            $stmt -> bindValue(':state', self::CONVERSION_STATUS_WAIT);
            $stmt -> execute();
            $sum = $stmt -> fetchObject() -> SUM;     
    
            $sql = 'SELECT COUNT(`ESOBJECT_CONVERSION_ID`) AS `POS` FROM `ESOBJECT_CONVERSION` WHERE `ESOBJECT_CONVERSION_ID` < (SELECT `ESOBJECT_CONVERSION_ID` FROM `ESOBJECT_CONVERSION` WHERE `ESOBJECT_CONVERSION_OBJECT_ID` = :objectid AND `ESOBJECT_CONVERSION_FORMAT` = :format) AND `ESOBJECT_CONVERSION_STATUS` = :status';
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            $stmt -> bindValue(':objectid', $this->id, PDO::PARAM_INT);
            $stmt -> bindValue(':format', $format);
            $stmt -> bindValue(':status', self::CONVERSION_STATUS_WAIT);
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
        $previewUrl = $this -> getContentNode() -> getNode() -> preview -> url ;
        return $previewUrl;
    }

    public function renderOriginalDeleted($requestData, $display_kind, $template) {
        if($display_kind == 'dynamic') {
            $tempArray['title'] = $this->getTitle();
            if(Config::get('showMetadata'))
                $tempArray['metadata'] = $this -> metadatahHandler -> render($template, '/metadata/dynamic');
            echo $template -> render('/special/originaldeleted/dynamic', $tempArray);
        } else if($display_kind == 'inline') {
            $tempArray['title'] = $this->getTitle();
            if(ENABLE_METADATA_INLINE_RENDERING) {
                $tempArray['metadata'] = $this -> metadatahHandler -> render($template, '/metadata/inline');
            }
            echo $template -> render('/special/originaldeleted/inline', $tempArray);
        }
        exit(0);
    }

    public function update() {
        if($this->getTitle() !== $this->title) {
            try {
                $pdo = RsPDO::getInstance();
                $sql = 'UPDATE `ESOBJECT` SET `ESOBJECT_TITLE` = :title WHERE `ESOBJECT_ID` = :id';
                $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
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

        if($this->contentNode->getNodeProperty('cm:name') !== $this->name) {
            try {
                $pdo = RsPDO::getInstance();
                $sql = 'UPDATE `ESOBJECT` SET `ESOBJECT_ALF_FILENAME` = :name WHERE `ESOBJECT_ID` = :id';
                $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
                $stmt -> bindValue(':name', $this->contentNode->getNodeProperty('cm:name'));
                $stmt -> bindValue(':id', $this->id, PDO::PARAM_INT);
                $result = $stmt -> execute();
                if(!$result)
                    throw new Exception('Error updating name ' . print_r($pdo -> errorInfo(), true));
                $this->name = $this->contentNode->getNodeProperty('cm:name');
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
        return $this->version;
    }

    public function getLmsId(): string
    {
        return $this->lmsId;
    }

    public function getCourseId(): int
    {
        return $this->courseId;
    }

    public function getResourceId(): int
    {
        return $this->resourceId;
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
        return $this->metadatahHandler;
    }


    public function getSequenceHandler()
    {
        return $this->sequenceHandler;
    }



}
