<?php
//error_reporting(E_ERROR);
require_once (__DIR__ . '/../../../../../conf.inc.php');
require_once (__DIR__ . '/../../../../../conf/audio-video.conf.php');
require_once (__DIR__ . '/../../../../../modules/video/mod_video.php');


/*
 *
 * Loads conversion queue from DB and converts video files
 *
 *
 * */
class Converter {

    private $timeout = '';
    private $threads = '';
    private $logger;

    public function __construct() {
        $this -> initLogger();
        $this -> setTimeout();
        $this -> setThreads();

        $tmp_path = CC_RENDER_PATH . DIRECTORY_SEPARATOR . 'tmp_conversion';

        if (!file_exists($tmp_path)) {
            if (!mkdir($tmp_path, 0770, true) && !is_dir($tmp_path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $tmp_path));
            }
        }

    }

    private function initLogger() {
        // init LOGGER
        require_once (__DIR__ . '/../../../../../func/extern/apache-log4php-2.0.0-incubating/src/main/php/Logger.php');
        Logger::configure(__DIR__ . '/../../../../../conf/de.metaventis.esrender.log4php.properties');
        $this -> logger = Logger::getLogger('de.metaventis.esrender.converter');
        $this -> logger -> info('Converter: Starting up.');
    }
    private function setThreads() {
        if(defined('FFMPEG_THREADS'))
            $this -> threads = "-threads " . FFMPEG_THREADS;
        else
            $this -> threads = "-threads 1";
    }
    
    private function setTimeout() {
        if(defined('FFMPEG_EXEC_TIMEOUT') && strpos(strtolower(PHP_OS), 'win') === false)
            $this -> timeout = "timeout " . FFMPEG_EXEC_TIMEOUT . " ";
    }

    public function startup() {
        global $argv;
        if(count($argv) > 1) {
            $param = $argv[1];
            $pdo = RsPDO::getInstance();
            $sql = 'DELETE FROM "ESOBJECT_CONVERSION" WHERE "ESOBJECT_CONVERSION_STATUS" LIKE :status';
            $stmt = $pdo -> prepare($sql);
            if($param == '--restart') {
                $stmt->bindValue(':status', ESObject::CONVERSION_STATUS_PROCESSING);
            } else if($param == '--retry-failed') {
                $stmt->bindValue(':status', ESObject::CONVERSION_STATUS_ERROR . '%');
            } else if($param == '--retry-stuck') {
                $stmt->bindValue(':status', ESObject::CONVERSION_STATUS_STUCK);
            } else {
                die('Invalid argument(s) detected: ' . $param);
            }
            $stmt->execute();
            echo 'Reset ' . $stmt->rowCount() . ' elements in queue';
        }
        $this->convert();
    }
    /*
     *
     * Load queue, lock and convert items.
     *
     * @return boolean
     *
     */
    public function convert() {
        if($this->converterIsOccupied()){
            die('Converter is still running. Use --restart to remove any current conversions and restart them');
        }

        if (!$conv = $this -> getNextFromConversionQueue()){
            return;
        }

        $object = new ESObject($conv -> ESOBJECT_CONVERSION_OBJECT_ID);
        $object -> setConversionStateProcessing($conv -> ESOBJECT_CONVERSION_OBJECT_ID, $conv -> ESOBJECT_CONVERSION_FORMAT, $conv -> ESOBJECT_CONVERSION_RESOLUTION);
        $this -> convertObject($conv);

        $this -> convert();
        
        return true;
    }

    /*
     * Convert item
     *
     * @param $conv the to convert
     *
     * @return boolean
     */
    public function convertObject($conv) {

        $arr = explode("/", $conv -> ESOBJECT_CONVERSION_MIMETYPE, 2);
        $type = $arr[0];

        switch($type) {
            case 'audio' :
                $conv -> ESOBJECT_CONVERSION_FILENAME = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, $conv -> ESOBJECT_CONVERSION_FILENAME);
                $filename = explode(DIRECTORY_SEPARATOR, $conv->ESOBJECT_CONVERSION_FILENAME);
                $logfile = dirname(__FILE__) . '/../../../../../log/conversion/' . end($filename) . '_' . $conv->ESOBJECT_CONVERSION_OBJECT_ID . '_' . AUDIO_FORMATS[0] . '.log';
                $tmpName = CC_RENDER_PATH . '/tmp_conversion/' . uniqid($conv->ESOBJECT_CONVERSION_OBJECT_ID, true) . '.mp3';
                exec($this -> timeout  . FFMPEG_BINARY . " " . "-i" . " " . $conv -> ESOBJECT_CONVERSION_FILENAME . " " . "-f mp3 -y" . " " .  $tmpName . " " ."2>>" . $logfile, $whatever, $code);
                //exec($this -> timeout  . FFMPEG_BINARY . " " . "-i" . " " . $conv -> ESOBJECT_CONVERSION_FILENAME . " " . "-f mp3 -y" . " " . $tmpName, $output, $code);
                $this->setConversionStatus($code, $conv, $tmpName, $output);

                break; 
            case 'video' :
                $conv -> ESOBJECT_CONVERSION_FILENAME = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, $conv -> ESOBJECT_CONVERSION_FILENAME);
                $filename = explode(DIRECTORY_SEPARATOR, $conv->ESOBJECT_CONVERSION_FILENAME);
                $logfile = dirname(__FILE__) . '/../../../../../log/conversion/' . end($filename) . '_' . $conv->ESOBJECT_CONVERSION_OBJECT_ID . '_' . $conv -> ESOBJECT_CONVERSION_FORMAT . '_' . $conv -> ESOBJECT_CONVERSION_RESOLUTION . '.log';
                switch( $conv -> ESOBJECT_CONVERSION_FORMAT) {
                    case 'mp4' :
                        $tmpName = CC_RENDER_PATH . '/tmp_conversion/' . uniqid($conv->ESOBJECT_CONVERSION_OBJECT_ID, true) . '.mp4';
                        exec($this -> timeout . FFMPEG_BINARY . " " . "-i" . " " . $conv -> ESOBJECT_CONVERSION_FILENAME . " -f mp4 -vcodec libx264" . " " . $this->threads . " " . "-crf 24 -preset veryfast -vf \"scale=-2:'min(" . $conv -> ESOBJECT_CONVERSION_RESOLUTION . "\,if(mod(ih\,2)\,ih-1\,ih))'\" -c:a aac -b:a 160k" . " " . $tmpName . " " ."2>>" . $logfile, $output, $code);
                        $this->setConversionStatus($code, $conv, $tmpName,$output);
                        break;
                    case 'webm' :
                        $tmpName = CC_RENDER_PATH . '/tmp_conversion/' . uniqid($conv->ESOBJECT_CONVERSION_OBJECT_ID, true) . '.webm';
                        exec($this -> timeout  . FFMPEG_BINARY  . " " . "-i" . " " . $conv -> ESOBJECT_CONVERSION_FILENAME . " -vcodec libvpx" . " " . $this->threads ." " . "-crf 40 -b:v 0 -deadline realtime -cpu-used 8 -vf \"scale=-1:'min(" . $conv -> ESOBJECT_CONVERSION_RESOLUTION . ",ih)'\" -c:a libvorbis -b:a 128k" . " " . $tmpName . " " ."2>>" . $logfile, $output, $code);
                        $this->setConversionStatus($code, $conv, $tmpName,$output);
                        break; 
                    default :
                    	$this->logger->error('Unhandled format: '.$conv -> ESOBJECT_CONVERSION_FORMAT);
                        throw new Exception('Unhandled format.');
                }

                break;
            default :
                $this->logger->error('no valid mimetype specified: '.$conv -> ESOBJECT_CONVERSION_MIMETYPE);
                echo 'no valid mimetype specified';
        }

        return true;

    }

    protected function setConversionStatus($code, $conv, $tmpName, $output){
        error_log('conversionStatus: '.print_r($code . ' ('. $conv -> ESOBJECT_CONVERSION_OBJECT_ID .')', true));
        if ($code !== 0) {
            $this->logger-> error("Error Converting ". $conv-> ESOBJECT_CONVERSION_OBJECT_ID . " (" . $conv -> ESOBJECT_CONVERSION_FILENAME .")" );
            $this->logger-> error($output);
        } else {
            $this->logger-> debug("Conversion success: ". $conv-> ESOBJECT_CONVERSION_OBJECT_ID . " (" . $conv -> ESOBJECT_CONVERSION_FILENAME .")" );
            $this->logger-> debug($output);
        }
        $object = new ESObject($conv -> ESOBJECT_CONVERSION_OBJECT_ID);
        if($code > 0) {
            if(file_exists($tmpName)){
                unlink($tmpName);
            }
            $object -> setConversionStateError($conv -> ESOBJECT_CONVERSION_OBJECT_ID, $conv -> ESOBJECT_CONVERSION_FORMAT, $code, $conv -> ESOBJECT_CONVERSION_RESOLUTION);
        } else {
            rename($tmpName, $conv -> ESOBJECT_CONVERSION_OUTPUT_FILENAME);
            $object -> setConversionStateProcessed($conv -> ESOBJECT_CONVERSION_OBJECT_ID, $conv -> ESOBJECT_CONVERSION_FORMAT, $conv -> ESOBJECT_CONVERSION_RESOLUTION);
        }
    }

    /*
     * Loads one item with status ESObject::CONVERSION_STATUS_WAIT from database
     *
     * @return boolean or row
     */
    public function getNextFromConversionQueue() {

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'SELECT * FROM "ESOBJECT_CONVERSION" WHERE "ESOBJECT_CONVERSION_STATUS" = :status ORDER BY "ESOBJECT_CONVERSION_RESOLUTION" ASC';
            $sql = $pdo -> queryLimit($sql, 1, 0);
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':status', ESObject::CONVERSION_STATUS_WAIT);
            $stmt -> execute();
            $result = $stmt -> fetchObject();
             
            if (!$result) {
                return false;
            }

            return $result;
            
        } catch(PDOException $e) {
            throw new Exception($e -> getMessage());
        }

    }

    public function converterIsOccupied() {
        
        $this -> markStuckConversions();

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'SELECT "ESOBJECT_CONVERSION_ID" from "ESOBJECT_CONVERSION" WHERE "ESOBJECT_CONVERSION_STATUS" = :status';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindValue(':status', ESObject::CONVERSION_STATUS_PROCESSING);
            $stmt -> execute();
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return false;
            }
            return true;
        } catch (PDOException $e) {
            throw new Exception($e -> getMessage());
        } 
    }
    
    private function markStuckConversions() {
        if(empty($this -> timeout))
            return;
        
        $timeoutThreshold = time() - FFMPEG_EXEC_TIMEOUT;

        $sql = 'SELECT "ESOBJECT_CONVERSION_OBJECT_ID", "ESOBJECT_CONVERSION_FORMAT", "ESOBJECT_CONVERSION_RESOLUTION" FROM "ESOBJECT_CONVERSION" WHERE "ESOBJECT_CONVERSION_STATUS" = :status AND "ESOBJECT_CONVERSION_TIME" <= :threshold';
        $pdo = RsPDO::getInstance();
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindValue(':status', ESObject::CONVERSION_STATUS_PROCESSING);
        $stmt -> bindValue(':threshold', $timeoutThreshold);
        $stmt -> execute();
        $result = $stmt -> fetchAll();
        
        if (!$result)
            return;

        foreach ($result as $row) {
            $object = new ESObject($row['ESOBJECT_CONVERSION_OBJECT_ID']);
            $object -> setConversionStateStuck($row['ESOBJECT_CONVERSION_OBJECT_ID'], $row['ESOBJECT_CONVERSION_FORMAT'], $row['ESOBJECT_CONVERSION_RESOLUTION']);
        }
        
    }

}

set_time_limit(0);
$c = new converter();
$c -> startup();
exit();
