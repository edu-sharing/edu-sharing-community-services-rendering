<?php

define('RATIO_MAX', 0.8);

//error_reporting(0);

require_once(__DIR__ . '/../../../conf.inc.php');
require_once(__DIR__ . '/../RsPDO.php');

class CacheCleanerClass
{
    private $logger;
    private $pass = 0;
    public $renderPath = '';
    public $renderPathSave = '';
    private $pdo = null;
    private $module;

    public function __construct($module, $logger = null)
    {
        $this->initLogger($logger);
        $this->pdo = RsPDO::getInstance();
        $this->logger->info('######## cacheCleaner initialized ########');
        $this->module = $module;
        if ($this->module !== null) {
            $this->logger->info('Module is set to ' . $module . '. All content of this type will be cleared from the cache');
        }
    }

    private function dirSize($directory)
    {
        $size = 0;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if ($file->getFileName() !== '..' && $file->getFileName() !== '.')
                $size += $file->getSize();
        }
        return $size;
    }

    private function initLogger($logger)
    {
        $this->logger = $logger === null ? require_once(MC_LIB_PATH . 'Log/init.php') : $logger;
    }

    public function deleteUndemandedObject($esObjectId = null)
    {
        try {
            if ($esObjectId === null) {
                $sql = 'SELECT "ESTRACK_ESOBJECT_ID", MAX("ESTRACK_TIME") FROM "ESTRACK" GROUP BY "ESTRACK_ESOBJECT_ID" ORDER BY MAX("ESTRACK_TIME") ASC LIMIT 1 OFFSET 0';
                $stmt = $this->pdo->query($sql);
                if($stmt){
                    $esObjectId = $stmt->fetchObject()->ESTRACK_ESOBJECT_ID;
                }else{
                    $this->logger->error('query error: '.print_r($stmt, true));
                }

                if (empty($esObjectId)) {
                    $this->logger->info('Could not get result from ESTRACK.');
                    return false;
                }
            }
            $this->logger->info('esObjectId: ' . $esObjectId);

            $sql = 'SELECT * FROM "ESOBJECT" WHERE "ESOBJECT_ID" = :esobject_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':esobject_id', $esObjectId);
            $stmt->execute();
            $result = $stmt->fetch();

        } catch (PDOException $e) {
            print_r($e->getMessage());
        }

        if (is_array($result)) {
            $esobject = new ESObject(0);
            $esobject->setInstanceData($result);

            //delete from db
            if (!$esobject->deleteFromDb()) {
                $this->logger->info('could not delete db record with id ' . $esobject->getId());
                return false;
            }
            $this->logger->info('deleted db record with id ' . $esobject->getId());

            $module = $esobject->getModule();
            if(!$module) {
                $this->logger->warn('Could not determine module for esObjectId ' . $esObjectId);
                return false;
            }
            if ($module->getName() === 'h5p') {

                //get h5p-ID for the directory name
                try {
                    $query = "SELECT id FROM h5p_contents WHERE title='" . $esobject->getObjectID() . "-" . $esobject->getContentHash() . "'";
                    $statement = $this->pdo->query($query);
                    $contentResult = $statement->fetchAll(\PDO::FETCH_OBJ);
                    $h5pID = ! empty($contentResult) ? $contentResult[0]->id : null;
                } catch (PDOException $e) {
                    $this->logger->info($e->getMessage());
                }
                if ($h5pID === null) {
                    $this->logger->info('No entry found in h5p_contents for object:' . $esobject->getObjectID() . "-" . $esobject->getContentHash());
                } else {
                    $this->logger->info('h5pID: ' . $h5pID);
                    try {
                        $query_libraries = "DELETE FROM h5p_contents_libraries WHERE content_id = " . $h5pID;
                        $statement_libraries = $this->pdo->query($query_libraries);
                        $statement_libraries->execute();
                        $query = "DELETE FROM h5p_contents WHERE title='" . $esobject->getObjectID() . "-" . $esobject->getContentHash() . "'";
                        $statement = $this->pdo->query($query);
                        $statement->execute();
                        $this->logger->info('deleted h5p-' . $h5pID . ' from db.');
                    } catch (PDOException $e) {
                        $this->logger->info($e->getMessage());
                    }
                    //delete cache folder
                    $dirPath = $this->renderPath . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . $h5pID;
                    if (!$this->removeDir($dirPath)) {
                        $this->logger->info('could not delete ' . $dirPath);
                    } else {
                        $this->logger->info('deleted ' . $dirPath);
                    }
                }
            }

            //delete cache folder
            if ($esobject->getSubUri_file()) {
                $dirPath = $this->renderPath . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . $esobject->getSubUri_file();
                if (!$this->removeDir($dirPath)) {
                    $this->logger->info('could not delete ' . $dirPath);
                } else {
                    $this->logger->info('deleted ' . $dirPath . ' ########### ' . $esobject->getFilename());
                }
            }

            if (!empty($this->renderPathSave)) {
                $dirPath = $this->renderPathSave . DIRECTORY_SEPARATOR . $module->getName() . DIRECTORY_SEPARATOR . $esobject->getSubUri_file();
                if (!$this->removeDir($dirPath)) {
                    $this->logger->info('could not delete ' . $dirPath);
                } else {
                    $this->logger->info('deleted ' . $dirPath . ' ########### ' . $esobject->getFilename());
                }
            }
        }

        // clean up database
        $sql = 'DELETE FROM "ESTRACK" WHERE "ESTRACK_ESOBJECT_ID" = :esobject_id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':esobject_id', $esObjectId);
        $stmt->execute();

        return true;
    }

    private function removeDir($dirPath)
    {
        try {
            $it = new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,
                RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getPathname());
                } else {
                    unlink($file->getPathname());
                }
            }
        } catch (Exception $e) {
            $this->logger->info('deleting error');
        }

        if (!rmdir($dirPath)) {
            return false;
        }
        return true;
    }

    public function cleanUp($forceDelete = false)
    {
        if ($this->module !== null) {
            $this->cleanUpByModule();
        } else {
            try {
                $availableSpace = disk_total_space($this->renderPath);
                $cacheSize = $this->dirSize($this->renderPath);
                if (!empty($this->renderPathSave)) {
                    $availableSpace += disk_total_space($this->renderPathSave);
                    $cacheSize += $this->dirSize($this->renderPathSave);
                }
                $diskUsageRatio = $cacheSize / $availableSpace;
                $this->logger->info('#### cleanup (pass ' . ++$this->pass . ')');
                $this->logger->info('available disk space: ' . round($availableSpace / pow(1024, 3), 2) . 'GiB');
                $this->logger->info('size of cache: ' . round($cacheSize / pow(1024, 3), 2) . 'GiB');
                $this->logger->info('disk usage: ' . round($diskUsageRatio * 100, 2) . '%');

                if ($diskUsageRatio > RATIO_MAX || $forceDelete) {
                    if ($this->deleteUndemandedObject())
                        $this->cleanUp($forceDelete);
                } else {
                    echo "Current Disk Usage Ratio < Configured Ratio (" . $diskUsageRatio . " < " . RATIO_MAX . "). Stopping...";
                }

            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    private function cleanUpByModule() {
        // Get module ID
        $sql = 'SELECT "ESMODULE_ID" FROM "ESMODULE" WHERE "ESMODULE_NAME" = :name';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $this->module);
        $stmt->execute();
        $result = $stmt->fetchObject();
        $moduleId = $result->ESMODULE_ID;

        //retrieve all objects with the given module id
        $sql = 'SELECT * FROM "ESOBJECT" WHERE "ESOBJECT_ESMODULE_ID" = ' . $moduleId;
        $results = $this->pdo->query($sql)->fetchAll();
        if (count($results) === 0) {
            $this->logger->info('No entries in ESOBJECT found for module ' . $this->module . '. If applicable, invalid data remnants will be deleted.');
        }
        foreach ($results as $esObject) {
            $this->deleteUndemandedObject($esObject["ESOBJECT_ID"]);
        }
        // Clean up all remaining data
        $cachePath = $this->renderPath . DIRECTORY_SEPARATOR . $this->module;
        is_dir($cachePath) && $this->removeDir($cachePath);
        $truncateLibs = $this->pdo->prepare('TRUNCATE TABLE "h5p_contents_libraries"');
        $truncateLibs->execute();
        $truncateContent = $this->pdo->prepare('TRUNCATE TABLE "h5p_contents"');
        $truncateContent->execute();
        $truncateAllLibs = $this->pdo->prepare('TRUNCATE TABLE "h5p_libraries"');
        $truncateAllLibs->execute();
        $truncateLibsLibs = $this->pdo->prepare('TRUNCATE TABLE "h5p_libraries_libraries"');
        $truncateLibsLibs->execute();
        $truncateLibsLangs = $this->pdo->prepare('TRUNCATE TABLE "h5p_libraries_languages"');
        $truncateLibsLangs->execute();
        $truncateHubCache = $this->pdo->prepare('TRUNCATE TABLE "h5p_libraries_hub_cache"');
        $truncateHubCache->execute();
    }
}