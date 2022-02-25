<?php

define('RATIO_MAX', 0.8);

//error_reporting(0);

require_once(__DIR__ . '/../../../conf.inc.php');
require_once(__DIR__ . '/../RsPDO.php');

class cacheCleaner
{

    private $logger;
    private $pass = 0;
    public $renderPath = '';
    public $renderPathSave = '';
    private $pdo = null;

    public function __construct()
    {
        $this->initLogger();
        $this->pdo = RsPDO::getInstance();
        $this->logger->info('######## cacheCleaner initialized ########');

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

    private function initLogger()
    {
        $this->logger = require_once(MC_LIB_PATH . 'Log/init.php');
    }

    private function deleteUndemandedObject()
    {
        try {
            $sql = 'SELECT "ESTRACK_ESOBJECT_ID", MAX("ESTRACK_TIME") FROM "ESTRACK" GROUP BY "ESTRACK_ESOBJECT_ID" ORDER BY MAX("ESTRACK_TIME") ASC LIMIT 1 OFFSET 0';
            $stmt = $this->pdo->query($sql);
            $esObjectId = $stmt->fetchObject()->ESTRACK_ESOBJECT_ID;

            $this->logger->info('esObjectId: ' . $esObjectId);

            if (empty($esObjectId)) {
                $this->logger->info('Could not get result from ESTRACK.');
                return false;
            }

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
                    $query = "SELECT id FROM h5p_contents WHERE title='" . $esobject->getObjectID() . "-v" . $esobject->getContentHash() . "'";
                    $statement = $this->pdo->query($query);
                    $h5pID = $statement->fetchAll(\PDO::FETCH_OBJ)[0]->id;
                } catch (PDOException $e) {
                    $this->logger->info($e->getMessage());
                }

                $this->logger->info('h5pID: ' . $h5pID);

                //delete h5p entry
                try {
                    $query_libraries = "DELETE FROM h5p_contents_libraries WHERE content_id = " . $h5pID;
                    $statement_libraries = $this->pdo->query($query_libraries);
                    $results_libraries = $statement_libraries->execute();

                    $query = "DELETE FROM h5p_contents WHERE title='" . $esobject->getObjectID() . "-v" . $esobject->getContentHash() . "'";
                    $statement = $this->pdo->query($query);
                    $result = $statement->execute();
                    $this->logger->info('deleted h5p-' . $h5pID . ' from db.');
                } catch (PDOException $e) {
                    $this->logger->info($e->getMessage());
                }

                //delete cache folder
                if ($h5pID) {
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

$cleaner = new cacheCleaner();
$cleaner->renderPath = $CC_RENDER_PATH;
if (!empty($CC_RENDER_PATH_SAFE))
    $cleaner->renderPathSave = $CC_RENDER_PATH_SAFE;
$cleaner->cleanUp();
