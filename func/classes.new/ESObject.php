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
    protected $ESOBJECT_ID = null;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_ESAPPLICATION_ID = null;

    /**
     *
     * @var int
     */
    protected $ESOBJECT_ESMODULE_ID = null;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_TITLE = null;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_REP_ID = null;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_OBJECT_ID = '';

    /**
     *
     * @var string
     */
    protected $ESOBJECT_OBJECT_VERSION = null;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_VERSIONED_OBJECT_ID = null;

    /**
     *
     * @var
     */
    protected $ESOBJECT_MIMETYPE = '';

    /**
     *
     * @var string
     */
    protected $ESOBJECT_PATH = null;

    /**
     * @var the remote's lms-identifier, e.g. 'alf' or 'moodle197'
     */
    protected $ESOBJECT_LMS_ID = null;

    /**
     * @var the remote's lms course-identifier this resource belongs to
     */
    protected $ESOBJECT_COURSE_ID = null;

    /**
     * Unique resource-identifier from used LMS.
     * @var int
     */
    protected $ESOBJECT_RESOURCE_ID = 0;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_RESOURCE_TYPE = null;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_RESOURCE_VERSION = null;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_FILE_PATH;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_ALF_FILENAME;

    /**
     *
     * @var string
     */
    protected $ESOBJECT_LICENSE = '';

    /**
     * @var ESModule
     */
    public $ESModule = null;

    /**
     *
     * @var Node
     */
    public $AlfrescoNode = null;
    
    /**
     *
     * @var $string
     */
    public $ESOBJECT_CONTENT_HASH = null;
    
    
    /**
     * 
     * @var stdObj
     */
    public $renderInfoLMSReturn = null;
    
    /**
     *
     * @var string
     */
    public $metadatahandler = null;

    /**
     *
     * @param string $ObjectId The initial object-id (from SpacesStore)
     */
    public function __construct($ObjectId, $ObjectVersion = null) {
        //parent::__construct();

        $this -> ESOBJECT_ID = 0;
        $this -> ESOBJECT_ESAPPLICATION_ID = 0;
        $this -> ESOBJECT_ESMODULE_ID = 0;
        $this -> ESOBJECT_TITLE = '';
        $this -> ESOBJECT_REP_ID = '';
        $this -> ESOBJECT_OBJECT_ID = $ObjectId;
        $this -> ESOBJECT_OBJECT_VERSION = $ObjectVersion;
        $this -> ESOBJECT_MIMETYPE = '';
        $this -> ESOBJECT_PATH = '';
        $this -> ESOBJECT_RESOURCE_ID = 0;
        $this -> ESOBJECT_RESOURCE_TYPE = '';
        $this -> ESOBJECT_RESOURCE_VERSION = '';
        $this -> ESOBJECT_FILE_PATH = '';
        $this -> ESOBJECT_ALF_FILENAME = '';
        $this -> ESOBJECT_CONTENT_HASH = '';

        $this -> ESModule = new ESModule();

        return true;
    }

    /**
     * Cleanup.
     *
     */
    public function __destruct() {
        $this -> AlfrescoNode = null;
        $this -> ESModule = null;
        $this -> Logger = null;
    }

    /**
     *
     */
    public function __get($name) {
        if (!property_exists($this, $name)) {
            throw new Exception('Accessing non-existing property "' . $name . '".');
        }

        return $this -> $name;
    }

    /**
     * Get full name from vCard-string.
     *
     * @param string $vcard
     *
     * @return string
     */
    protected function getFNfromVcard($vcard) {
        $regex = '/\nFN:(?<fn>[^\n]+)\n?/iu';

        $matches = array();
        if (!preg_match($regex, $vcard, $matches)) {
            return false;
        }

        return $matches['fn'];
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
            $values = array((int)$this -> ESOBJECT_ID, $this -> ESOBJECT_REP_ID);
            
            if (!empty($this -> ESOBJECT_LMS_ID)) {
                $sql .= ' AND `ESOBJECT_LMS_ID` = ? ';
                $values[] = $this -> ESOBJECT_LMS_ID;
                if(!empty($this -> ESOBJECT_COURSE_ID)) {
                    $sql .= ' AND `ESOBJECT_COURSE_ID` = ?';
                    $values[] = $this -> ESOBJECT_COURSE_ID;
                }
            }
            if (!empty($this -> ESOBJECT_OBJECT_VERSION)) {
                $sql .= ' AND `ESOBJECT_OBJECT_VERSION` = ?';
                $values[] = $this -> ESOBJECT_OBJECT_VERSION;
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
        $this -> ESOBJECT_ID = $row['ESOBJECT_ID'];
        $this -> ESOBJECT_ESAPPLICATION_ID = $row['ESOBJECT_ESAPPLICATION_ID'];
        $this -> ESOBJECT_ESMODULE_ID = $row['ESOBJECT_ESMODULE_ID'];
        $this -> ESOBJECT_TITLE = $row['ESOBJECT_TITLE'];
        $this -> ESOBJECT_REP_ID = $row['ESOBJECT_REP_ID'];
        $this -> ESOBJECT_OBJECT_ID = $row['ESOBJECT_OBJECT_ID'];
        $this -> ESOBJECT_OBJECT_VERSION = $row['ESOBJECT_OBJECT_VERSION'];
        $this -> ESOBJECT_VERSIONED_OBJECT_ID = $row['ESOBJECT_VERSIONED_OBJECT_ID'];
        $this -> ESOBJECT_MIMETYPE = $row['ESOBJECT_MIMETYPE'];
        $this -> ESOBJECT_PATH = $row['ESOBJECT_PATH'];
        $this -> ESOBJECT_LMS_ID = $row['ESOBJECT_LMS_ID'];
        $this -> ESOBJECT_COURSE_ID = $row['ESOBJECT_COURSE_ID'];
        $this -> ESOBJECT_RESOURCE_ID = $row['ESOBJECT_RESOURCE_ID'];
        $this -> ESOBJECT_RESOURCE_TYPE = $row['ESOBJECT_RESOURCE_TYPE'];
        $this -> ESOBJECT_RESOURCE_VERSION = $row['ESOBJECT_RESOURCE_VERSION'];
        $this -> ESOBJECT_FILE_PATH = $row['ESOBJECT_FILE_PATH'];
        $this -> ESOBJECT_ALF_FILENAME = $row['ESOBJECT_ALF_FILENAME'];
        $this -> ESOBJECT_CONTENT_HASH = $row['ESOBJECT_CONTENT_HASH'];

        $this -> ESModule -> setModuleID($this -> ESOBJECT_ESMODULE_ID);

        return $this;
    }

    /**
     *
     */
    final public function getId() {
        return $this -> ESOBJECT_ID;
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
    final public function setAlfrescoNode(ESContentNode $p_node) {
        $this -> AlfrescoNode = $p_node;
    }

    /**
     * Get object's mime-type.
     *
     * @return string
     */
    final public function getMimeType() {
        return $this -> ESOBJECT_MIMETYPE;
    }

    /**
     * Get object's resource-type.
     *
     * @return string
     */
    final public function getResourceType() {
        return $this -> ESOBJECT_RESOURCE_TYPE;
    }

    /**
     * Get object's resource-version.
     *
     * @return string
     */
    final public function getResourceVersion() {
        return $this -> ESOBJECT_RESOURCE_VERSION;
    }

    /**
     *
     */
    final public function getTitle() {
    	$title = $this->AlfrescoNode->getProperty('{http://www.campuscontent.de/model/lom/1.0}title');
    	if(!empty($title))
    		return $title;
    	else
    		return $this->AlfrescoNode->getProperty('{http://www.alfresco.org/model/content/1.0}name');
    }

    /**
     * Get object's title.
     *
     * @return string
     */
    final public function getFilename() {
        return $this -> ESOBJECT_ALF_FILENAME;
    }

    /**
     *
     */
    final public function getPath() {
        return MC_ROOT_URI . $this -> ESModule -> getTmpFilepath() . '/' . $this -> getSubUri() . '/' . $this -> getObjectIdVersion();
    }

    /**
     *
     */
    final public function getFilePath()// deprecated
    {
    	global $CC_RENDER_PATH;
        return $CC_RENDER_PATH . DIRECTORY_SEPARATOR . $this -> ESModule -> getName() . DIRECTORY_SEPARATOR . $this -> getSubPath() . DIRECTORY_SEPARATOR . $this -> getObjectIdVersion();
    }

    /**
     *
     * @return string
     */
    final public function getEsobjectFilePath() {
        return $this -> ESOBJECT_FILE_PATH;
    }

    /**
     *
     */
    final public function setFilePath($path)// deprecated
    {
        $this -> ESOBJECT_FILE_PATH = $path;
    }

    /**
     *
     */
    final public function getSubUri() {
        return $this -> ESOBJECT_PATH;
    }

    /**
     *
     */
    final public function setSubUri($sub_uri) {
        $this -> ESOBJECT_PATH = $sub_uri;
    }

    /**
     *
     */
    final public function getSubPath() {
        return str_replace('/', DIRECTORY_SEPARATOR, $this -> ESOBJECT_PATH);
    }

    /**
     *
     */
    final public function getPreviewPath() {
        return $this -> ESOBJECT_PATH;
    }

    /**
     *
     */
    final public function getObjectID() {
        return $this -> ESOBJECT_OBJECT_ID;
    }

    /**
     *
     */
    final public function getModuleID() {
        return $this -> ESOBJECT_ESMODULE_ID;
    }

    /**
     *
     */
    final public function getObjectVersion() {
        return $this -> ESOBJECT_OBJECT_VERSION;
    }

    /**
     *
     */
    final public function getVersionedObjectId() {
        return $this -> ESOBJECT_VERSIONED_OBJECT_ID;
    }

    /**
     *
     */
    final public function getObjectIdVersion() {
        return $this -> ESOBJECT_OBJECT_ID . $this -> ESOBJECT_OBJECT_VERSION;
    }

    /**
     *
     */
    final public function getModule() {
        include_once (dirname(__FILE__) . '/ESModule.php');
        $obj_module = new ESModule($this -> getModuleID());
        return $obj_module;
    }

    /**
     *
     * @return string
     */
    public function getLicense() {
        return $this -> ESOBJECT_LICENSE;
    }


    /**
     * Set the appropriate module for this object.
     *
     * @return bool
     */
    public function setModule() {
        // runtime sanity
        if (empty($this -> AlfrescoNode)) {
            throw new Exception('No Alfresco-properties set.');
        }

        if ($this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}remoterepositorytype') == 'YOUTUBE') {
            error_log('Property {http://www.campuscontent.de/model/1.0}remoterepositorytype equals "YOUTUBE", using module "url".');
            $this -> ESModule -> setName('url');
            $this -> ESModule -> loadModuleData();
            $this -> ESOBJECT_ESMODULE_ID = $this -> ESModule -> getModuleId();
            return true;
        }

        if ($this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}replicationsource') == 'oai:dmglib.org') {
            error_log('Property {http://www.campuscontent.de/model/1.0}replicationsource equals "oai:dmglib.org", using module "url".');
            $this -> ESModule -> setName('url');
            $this -> ESModule -> loadModuleData();
            $this -> ESOBJECT_ESMODULE_ID = $this -> ESModule -> getModuleId();
            return true;
        }
        
        if ($this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}replicationsource') == 'DE.FWU') {
        	error_log('Property {http://www.campuscontent.de/model/1.0}replicationsource equals "DE.FWU", using module "url".');
        	$this -> ESModule -> setName('url');
        	$this -> ESModule -> loadModuleData();
        	$this -> ESOBJECT_ESMODULE_ID = $this -> ESModule -> getModuleId();
        	return true;
        }

        $wwwurl = $this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}wwwurl');
        $tool_instance_ref = ($this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}tool_instance_ref'));
        if (!empty($wwwurl)) {
            error_log('Property {http://www.campuscontent.de/model/1.0}wwwurl found, using module "url".');
            $this -> ESModule -> setName('url');
        } else if (!empty($tool_instance_ref)) {
            error_log('Property{http://www.campuscontent.de/model/1.0}tool_instance_ref found, using module "lti".');
            $this -> ESModule -> setName('lti');
        } else if ($this -> ESOBJECT_MIMETYPE == 'application/zip') {
            if (!$this -> ESModule -> setModuleByResource($this -> ESOBJECT_RESOURCE_TYPE, $this -> ESOBJECT_RESOURCE_VERSION)) {
                error_log('Could not set module by resource-type/-version, using default ("doc") module.');
                $this -> ESModule -> setName('doc');
            }
        } else {
            if (!$this -> ESModule -> setModuleByMimetype($this -> ESOBJECT_MIMETYPE)) {
                error_log('Could not set module by mimetype using default ("doc") module.');
                $this -> ESModule -> setName('doc');
            }
        }

        $this -> ESModule -> loadModuleData();

        $this -> ESOBJECT_ESMODULE_ID = $this -> ESModule -> getModuleId();

        return true;
    }

    /**
     *
     */
    final public function setData($p_DataArray) {
        if (!empty($p_DataArray['ESOBJECT_ESAPPLICATION_ID'])) {
            $this -> ESOBJECT_ESAPPLICATION_ID = $p_DataArray['ESOBJECT_ESAPPLICATION_ID'];
        }

        if (!empty($p_DataArray['ESOBJECT_ESMODULE_ID'])) {
            $this -> ESOBJECT_ESMODULE_ID = $p_DataArray['ESOBJECT_ESMODULE_ID'];
        }

        if (!empty($p_DataArray['ESOBJECT_TITLE'])) {
            $this -> ESOBJECT_TITLE = $p_DataArray['ESOBJECT_TITLE'];
        }
        if (!empty($p_DataArray['ESOBJECT_REP_ID'])) {
            $this -> ESOBJECT_REP_ID = $p_DataArray['ESOBJECT_REP_ID'];
        }
        if (!empty($p_DataArray['ESOBJECT_MIMETYPE'])) {
            $this -> ESOBJECT_MIMETYPE = $p_DataArray['ESOBJECT_MIMETYPE'];
        }
        if (!empty($p_DataArray['ESOBJECT_PATH'])) {
            $this -> ESOBJECT_PATH = $p_DataArray['ESOBJECT_PATH'];
        }
        if (!empty($p_DataArray['ESOBJECT_LMS_ID'])) {
            $this -> ESOBJECT_LMS_ID = $p_DataArray['ESOBJECT_LMS_ID'];
        }
        if (!empty($p_DataArray['ESOBJECT_COURSE_ID'])) {
            $this -> ESOBJECT_COURSE_ID = $p_DataArray['ESOBJECT_COURSE_ID'];
        }
        if (!empty($p_DataArray['ESOBJECT_RESOURCE_ID'])) {
            $this -> ESOBJECT_RESOURCE_ID = $p_DataArray['ESOBJECT_RESOURCE_ID'];
        }
        if (!empty($p_DataArray['ESOBJECT_RESOURCE_TYPE'])) {
            $this -> ESOBJECT_RESOURCE_TYPE = $p_DataArray['ESOBJECT_RESOURCE_TYPE'];
        }
        if (!empty($p_DataArray['ESOBJECT_RESOURCE_VERSION'])) {
            $this -> ESOBJECT_RESOURCE_VERSION = $p_DataArray['ESOBJECT_RESOURCE_VERSION'];
        }
        if (!empty($p_DataArray['ESOBJECT_FILE_PATH'])) {
            $this -> ESOBJECT_FILE_PATH = $p_DataArray['ESOBJECT_FILE_PATH'];
        }
        if (!empty($p_DataArray['ESOBJECT_CONTENT_HASH'])) {
            $this -> ESOBJECT_CONTENT_HASH = $p_DataArray['ESOBJECT_CONTENT_HASH'];
        }
        return true;
    }


    final public function setDataByNode() {

        $this -> ESOBJECT_ESAPPLICATION_ID = 1;
        $this -> ESOBJECT_RESOURCE_TYPE = '';
        $this -> ESOBJECT_RESOURCE_VERSION = '';

        $title = $this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/lom/1.0}title');
        if(empty($title))
        	$title = $this -> AlfrescoNode -> getProperty('{http://www.alfresco.org/model/content/1.0}name');
        $this -> ESOBJECT_TITLE = $title;

        $mimetype = $this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/lom/1.0}format');
        if(!empty($mimetype))
            $this -> ESOBJECT_MIMETYPE = $mimetype;
            
        $ts = $this -> AlfrescoNode -> getProperty('{http://www.alfresco.org/model/content/1.0}modified');
        if(empty($ts))
            throw new Exception('No timestamp found in properties.');

        $name = $this -> AlfrescoNode -> getProperty('{http://www.alfresco.org/model/content/1.0}name');
        if(!empty($name))
            $this -> ESOBJECT_ALF_FILENAME = $name;

        $ressourcetype = $this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}ccressourcetype');
        if (!empty($ressourcetype)) {
            $this -> ESOBJECT_RESOURCE_TYPE = $ressourcetype;

            if ($this -> ESOBJECT_RESOURCE_TYPE == 'imsqti') {
                $this -> ESOBJECT_MIMETYPE = 'application/zip';
            }
        }

        $ressourceversion = $this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}ccressourceversion');
        if (!empty($ressourceversion))
            $this -> ESOBJECT_RESOURCE_VERSION = $ressourceversion;
        
        $commonlicense_key = $this -> AlfrescoNode -> getProperty('{http://www.campuscontent.de/model/1.0}commonlicense_key');
        if(!empty($commonlicense_key))
        	$this -> ESOBJECT_LICENSE = new ESRender_License($this);
        
        $this -> metadatahandler = new ESRender_Metadata_Handler($this);

        return true;
    }

    final public function setData2Db() {

        $arrFields = array(
           'ESOBJECT_ESAPPLICATION_ID' => intval($this -> ESOBJECT_ESAPPLICATION_ID),
           'ESOBJECT_ESMODULE_ID' => intval($this -> ESModule -> getModuleId()),
           'ESOBJECT_TITLE' => $this -> ESOBJECT_TITLE,
           'ESOBJECT_REP_ID' => $this -> ESOBJECT_REP_ID,
           'ESOBJECT_OBJECT_ID' => $this -> ESOBJECT_OBJECT_ID,
           'ESOBJECT_OBJECT_VERSION' => $this -> ESOBJECT_OBJECT_VERSION,
           'ESOBJECT_MIMETYPE' => $this -> ESOBJECT_MIMETYPE,
           'ESOBJECT_LMS_ID' => $this -> ESOBJECT_LMS_ID,
           'ESOBJECT_COURSE_ID' => $this -> ESOBJECT_COURSE_ID,
           'ESOBJECT_RESOURCE_ID' => $this -> ESOBJECT_RESOURCE_ID,
           'ESOBJECT_RESOURCE_TYPE' => $this -> ESOBJECT_RESOURCE_TYPE,
           'ESOBJECT_RESOURCE_VERSION' => $this -> ESOBJECT_RESOURCE_VERSION,
           'ESOBJECT_PATH' => $this -> ESOBJECT_PATH,
           'ESOBJECT_FILE_PATH' => $this -> ESOBJECT_FILE_PATH,
           'ESOBJECT_ALF_FILENAME' => $this -> ESOBJECT_ALF_FILENAME,
           'ESOBJECT_CONTENT_HASH' => $this -> ESOBJECT_CONTENT_HASH
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
            $this -> ESOBJECT_ID = $pdo -> lastInsertId();
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }
        return true;
    }

    public function addToConversionQueue($format, $dirSep, $filename, $outputFilename, $renderPath, $mimeType) {


        $arr = array(
            'ESOBJECT_CONVERSION_OBJECT_ID' => $this -> ESOBJECT_ID,
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
            $stmt -> bindValue(':objectid', $this -> ESOBJECT_ID, PDO::PARAM_INT);
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
            $stmt -> bindValue(':objectid', $this -> ESOBJECT_ID, PDO::PARAM_INT);
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
        return MC_ROOT_URI . $this -> ESModule -> getTmpFilepath() . '/' . $this -> getSubUri_file() . '/' . $this -> getObjectIdVersion();
    }

    final public function getSubUri_file() {
        return $this -> ESOBJECT_FILE_PATH;
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
            $stmt -> bindValue(':objectid', $this->ESOBJECT_ID, PDO::PARAM_INT);
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
    
    public function setInfoLmsData($renderInfoLMSReturn) {
        $this -> renderInfoLMSReturn = $renderInfoLMSReturn;
    	
    }

    public function renderOriginalDeleted($requestData, $display_kind, $template) {
        if($display_kind == 'dynamic') {
            $tempArray['title'] = $this->getTitle();
            echo $template -> render('/special/originaldeleted/dynamic', $tempArray);
        } else if($display_kind == 'inline') {
            $tempArray['title'] = $this->getTitle();
            echo $template -> render('/special/originaldeleted/inline', $tempArray);
        } else {
            throw new ESRender_Exception_CorruptVersion($this->getTitle());
        }
        exit();
    }
}
