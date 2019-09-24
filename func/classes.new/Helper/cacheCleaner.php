<?php

define('RATIO_MAX', 0.08);

error_reporting(0);

require_once (dirname(__FILE__) . '/../../../conf.inc.php');
require_once (dirname(__FILE__) . '/../RsPDO.php');

class cacheCleaner {

    private $logger;
    private $pass = 0;
    public $renderPath = '';
    public $renderPathSave = '';

    public function __construct() {
        $this -> initLogger();
        if (!ENABLE_TRACK_OBJECT) {
            $this -> logger -> info('ENABLE_TRACK_OBJECT is disabled. Enable to use this script.');
            exit(0);
        }
        $this -> logger -> info('######## cacheCleaner initialized ########');
    }

    private function dirSize($directory) {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if ($file -> getFileName() != '..' && $file -> getFileName() != '.')
                $size += $file -> getSize();
        }
        return $size;
    }

    private function initLogger() {
        require_once (dirname(__FILE__) . '/../../extern/apache-log4php-2.0.0-incubating/src/main/php/Logger.php');
        Logger::configure(dirname(__FILE__) . '/../../../conf/de.metaventis.esrender.log4php.cachecleaner.properties');
        $this -> logger = Logger::getLogger('de.metaventis.esrender.cachecleaner');
    }

    private function deleteUndemandedObject() {
        try {
            $pdo = RsPDO::getInstance();
            if ($pdo -> getDriver() == 'pgsql') {
                $sql = 'SELECT `ESTRACK_ESOBJECT_ID`, MAX(`ESTRACK_TIME`) FROM `ESTRACK` WHERE `STATE` = :state GROUP BY `ESTRACK_ESOBJECT_ID` ORDER BY MAX(`ESTRACK_TIME`) ASC LIMIT 1 OFFSET 0';
            } else if ($pdo -> getDriver() == 'mysql') {
                $sql = 'SELECT ESTRACK_ESOBJECT_ID, MAX(ESTRACK_TIME) AS TIME FROM ESTRACK WHERE STATE = :state GROUP BY ESTRACK_OBJECT_ID ORDER BY TIME ASC LIMIT 0,1';
            } else {
                throw new Exception('Query not implemented for current db driver');
            }
            $sql = $pdo -> formatQuery($sql);
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':state', 'Y');
            $stmt -> execute();
            $esObjectId = $stmt -> fetchObject() -> ESTRACK_ESOBJECT_ID;

            $this -> logger -> info('esObjectId: '.$esObjectId);
            
            if(empty($esObjectId)) {
                $this -> logger -> info('Could not get result from ESTRACK.');
                return false;
            }
            
            $sql = 'UPDATE `ESTRACK` set `STATE` = :state WHERE `ESTRACK_ESOBJECT_ID` = :esobjectid';
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            $stmt -> bindValue(':state', 'N');
            $stmt -> bindValue(':esobjectid', $esObjectId);
            $result = $stmt -> execute();

            $sql = "SELECT * FROM `ESOBJECT` WHERE `ESOBJECT_ID` = :esobject_id";
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
            $stmt -> bindValue(':esobject_id', $esObjectId);
            $stmt -> execute();
            $result = $stmt -> fetch();

        } catch(PDOException $e) {
            print_r($e -> getMessage());
        }

        if(is_array($result)){
            $esobject = new ESObject(0);
            $esobject -> setInstanceData($result);

            //delete from db
            if (!$esobject -> deleteFromDb())
                $this -> logger -> info('could not delete db record with id ' . $esobject -> ESOBJECT_ID);
            else {
                $this -> logger -> info('deleted db record with id ' . $esobject -> ESOBJECT_ID);
            }

            $module = $esobject -> getModule();

            if($module->getName() == 'h5p'){

                $dbFile = $this->renderPath . DIRECTORY_SEPARATOR . 'h5p'.DIRECTORY_SEPARATOR . 'db';
                if(file_exists($dbFile)){
                    $h5p_db = new PDO('sqlite:' . $dbFile);
                    $h5p_db -> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

                    //get h5p-ID for the directory name
                    $query = "SELECT id FROM h5p_contents WHERE title='".$esobject->getObjectID()."'";
                    $statement = $h5p_db -> query($query);
                    $h5pID = $statement->fetchAll(\PDO::FETCH_OBJ)[0]->id;

                    //delete h5p sqlite entry
                    $query = "DELETE FROM h5p_contents WHERE title='".$esobject->getObjectID()."'";
                    $statement = $h5p_db -> query($query);
                    $result = $statement->execute();
                    $this -> logger -> info('deleted h5p-'.$h5pID.' from sqlite.');

                    //delete cache folder
                    $dirPath = $this->renderPath . DIRECTORY_SEPARATOR . $module -> getName() . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . $h5pID;
                    if (!$this -> removeDir($dirPath)){
                        $this -> logger -> info('could not delete ' . $dirPath);
                    }else{
                        $this -> logger -> info('deleted ' . $dirPath );
                    }

                }else{
                    $this -> logger -> info('could not find h5p-sqlite-db at ' . $dbFile);
                }

            }

            //delete cache folder
            $dirPath = $this->renderPath . DIRECTORY_SEPARATOR . $module -> getName() . DIRECTORY_SEPARATOR . $esobject -> getSubUri_file();
            if (!$this -> removeDir($dirPath)){
                $this -> logger -> info('could not delete ' . $dirPath);
            }else{
                $this -> logger -> info('deleted ' . $dirPath . ' ########### ' . $esobject -> getFilename());
            }

            if(!empty($this->renderPathSave)) {
                $dirPath = $this->renderPathSave . DIRECTORY_SEPARATOR . $module -> getName() . DIRECTORY_SEPARATOR . $esobject -> getSubUri_file();
                if (!$this -> removeDir($dirPath)){
                    $this -> logger -> info('could not delete ' . $dirPath);
                }else{
                    $this -> logger -> info('deleted ' . $dirPath . ' ########### ' . $esobject -> getFilename());
                }
            }
        }

        return true;
    }

    private function removeDir($dirPath) {

        if ($dirPath == '/' || empty($dirPath) || $dirPath == './')
            return false;
        if (!is_dir($dirPath))
            return false;
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/')
            $dirPath .= '/';
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            unlink($file);
        }
        if (!rmdir($dirPath))
            return false;
        return true;
    }

    public function cleanUp($forceDelete = false) {

        try {
            $availableSpace = disk_total_space($this->renderPath);
            $cacheSize = $this -> dirSize($this->renderPath);
            if(!empty($this->renderPathSave)) {
            	$availableSpace += disk_total_space($this->renderPathSave);
            	$cacheSize += $this -> dirSize($this->renderPathSave);
            }
            $diskUsageRatio = $cacheSize / $availableSpace;
            $this -> logger -> info('#### cleanup (pass ' . ++$this -> pass . ')');
            $this -> logger -> info('available disk space: ' . round($availableSpace / pow(1024, 3), 2) . 'GiB');
            $this -> logger -> info('size of cache: ' . round($cacheSize / pow(1024, 3), 2) . 'GiB');
            $this -> logger -> info('disk usage: ' . round($diskUsageRatio * 100, 2) . '%');

            if ($diskUsageRatio > RATIO_MAX || $forceDelete) {
                if($this -> deleteUndemandedObject())
                    $this -> cleanUp($forceDelete);
            }

        } catch(Exception $e) {
            $this -> logger -> error($e -> getMessage());
        }

    }

}

$cleaner = new cacheCleaner();
$cleaner -> renderPath = $CC_RENDER_PATH;
if(!empty($CC_RENDER_PATH_SAFE))
	$cleaner -> renderPathSave = $CC_RENDER_PATH_SAFE;
$cleaner -> cleanUp();
