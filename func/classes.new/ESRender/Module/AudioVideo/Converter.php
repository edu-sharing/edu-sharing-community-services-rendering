<?php
error_reporting(E_ERROR);
require_once (dirname(__FILE__) . '/../../../../../conf.inc.php');
require_once (dirname(__FILE__) . '/../../../../../modules/video/config.php');
require_once (dirname(__FILE__) . '/../../../../../modules/video/mod_video.php');


/*
 *
 * Loads conversion queue from DB and converts video files
 *
 *
 * */
class converter {

    private $timeout = '';
    private $threads = '';

    public function __construct() {
        $this -> setTimeout();
        $this -> setThreads();
    }

    private function setThreads() {
        if(defined('OPTION_THREADS'))
            $this -> threads = "-threads " . OPTION_THREADS;
        else
            $this -> threads = "-threads 1";
    }
    
    private function setTimeout() {
        if(defined('EXEC_TIMEOUT') && strpos(strtolower(PHP_OS), 'win') === false)
            $this -> timeout = "timeout " . EXEC_TIMEOUT . " ";
    }

    /*
     *
     * Load queue, lock and convert items.
     *
     * @return boolean
     *
     */
    public function convert() {
        
        if($this->converterIsOccupied())
            return;

        if (!$conv = $this -> getNextFromConversionQueue())
            return;

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
                $logfile = dirname(__FILE__) . '/../../../../../log/conversion/' . end(explode(DIRECTORY_SEPARATOR, $conv -> ESOBJECT_CONVERSION_FILENAME)) . '_' . $conv->ESOBJECT_CONVERSION_OBJECT_ID . '_' . ESRender_Module_AudioVideo_Abstract::FORMAT_AUDIO_MP3 . '.log';
                $tmpName = dirname(__FILE__) . '/../../../../../log/conversion/' . uniqid() . '.mp3';
                exec($this -> timeout  . FFMPEG_BINARY . " " . "-i" . " " . $conv -> ESOBJECT_CONVERSION_FILENAME . " " . "-f mp3 -y" . " " . $tmpName . " " ."2>>" . $logfile, $whatever, $code);
                $object = new ESObject($conv -> ESOBJECT_CONVERSION_OBJECT_ID);
                if($code > 0) {
                    unlink($tmpName);
                    $object -> setConversionStateError($conv -> ESOBJECT_CONVERSION_OBJECT_ID, $conv -> ESOBJECT_CONVERSION_FORMAT, $conv -> ESOBJECT_CONVERSION_RESOLUTION, $code);
                } else {
                    rename($tmpName, $conv -> ESOBJECT_CONVERSION_OUTPUT_FILENAME);
                    $object -> setConversionStateProcessed($conv -> ESOBJECT_CONVERSION_OBJECT_ID, $conv -> ESOBJECT_CONVERSION_FORMAT, $conv -> ESOBJECT_CONVERSION_RESOLUTION);
                }
                break; 
            case 'video' :
                $conv -> ESOBJECT_CONVERSION_FILENAME = str_replace(array('\\','/'), DIRECTORY_SEPARATOR, $conv -> ESOBJECT_CONVERSION_FILENAME);
                $logfile = dirname(__FILE__) . '/../../../../../log/conversion/' . end(explode(DIRECTORY_SEPARATOR, $conv -> ESOBJECT_CONVERSION_FILENAME)) . '_' . $conv->ESOBJECT_CONVERSION_OBJECT_ID . '_' . $conv -> ESOBJECT_CONVERSION_FORMAT . '_' . $conv -> ESOBJECT_CONVERSION_RESOLUTION . '.log';
                switch( $conv -> ESOBJECT_CONVERSION_FORMAT) {
                    case ESRender_Module_AudioVideo_Abstract::FORMAT_VIDEO_MP4 :
                        $tmpName = dirname(__FILE__) . '/../../../../../log/conversion/' . uniqid(). '.mp4';
                        exec($this -> timeout . FFMPEG_BINARY . " " . "-i" . " " . $conv -> ESOBJECT_CONVERSION_FILENAME . " " . "-filter:v scale=-2:" . $conv -> ESOBJECT_CONVERSION_RESOLUTION . " " . "-f mp4 -vcodec libx264" . " " . $this->threads . " " . "-acodec aac -movflags faststart -strict -2 -y" . " " . $tmpName . " " ."2>>" . $logfile, $whatever, $code);
                        $object = new ESObject($conv -> ESOBJECT_CONVERSION_OBJECT_ID);
                        if($code > 0) {
                            unlink($tmpName);
                            $object -> setConversionStateError($conv -> ESOBJECT_CONVERSION_OBJECT_ID, $conv -> ESOBJECT_CONVERSION_FORMAT, $conv -> ESOBJECT_CONVERSION_RESOLUTION, $code);
                        } else {
                            rename($tmpName, $conv -> ESOBJECT_CONVERSION_OUTPUT_FILENAME);
                            $object -> setConversionStateProcessed($conv -> ESOBJECT_CONVERSION_OBJECT_ID, $conv -> ESOBJECT_CONVERSION_FORMAT, $conv -> ESOBJECT_CONVERSION_RESOLUTION);
                        }
                        break;                       
                    case ESRender_Module_AudioVideo_Abstract::FORMAT_VIDEO_WEBM :
                        $tmpName = dirname(__FILE__) . '/../../../../../log/conversion/' . uniqid(). '.webm';                        
                        exec($this -> timeout  . FFMPEG_BINARY  . " " . "-i" . " " . $conv -> ESOBJECT_CONVERSION_FILENAME . " " . "-filter:v scale=-2:". $conv -> ESOBJECT_CONVERSION_RESOLUTION . " " ."-vcodec libvpx" . " " . $this->threads ." " . "-acodec libvorbis -y -b:v 1M -crf 10" . " " . $tmpName . " " . "2>>" . $logfile, $whatever, $code);
                        $object = new ESObject($conv -> ESOBJECT_CONVERSION_OBJECT_ID);
                        if($code > 0) {
                            unlink($tmpName);
                            $object -> setConversionStateError($conv -> ESOBJECT_CONVERSION_OBJECT_ID, $conv -> ESOBJECT_CONVERSION_FORMAT, $conv -> ESOBJECT_CONVERSION_RESOLUTION, $code);
                        } else {
                            rename($tmpName, $conv -> ESOBJECT_CONVERSION_OUTPUT_FILENAME);
                            $object -> setConversionStateProcessed($conv -> ESOBJECT_CONVERSION_OBJECT_ID, $conv -> ESOBJECT_CONVERSION_FORMAT, $conv -> ESOBJECT_CONVERSION_RESOLUTION);
                        }
                        break; 
                    default :
                    	var_dump($conv -> ESOBJECT_CONVERSION_FORMAT);
                        throw new Exception('Unhandled format.');
                }

                break;
            default :
                echo 'no valid mimetype specified';
        }

        return true;

    }

    /*
     * Loads one item with status ESObject::CONVERSION_STATUS_WAIT from database
     *
     * @return boolean or row
     */
    public function getNextFromConversionQueue() {

        $pdo = RsPDO::getInstance();
        try {
            $sql = 'SELECT * FROM `ESOBJECT_CONVERSION` WHERE `ESOBJECT_CONVERSION_STATUS` = :status ORDER BY `ESOBJECT_CONVERSION_RESOLUTION` ASC';
            $sql = $pdo -> queryLimit($sql, 1, 0);
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
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
            $sql = 'SELECT `ESOBJECT_CONVERSION_ID` from `ESOBJECT_CONVERSION` WHERE `ESOBJECT_CONVERSION_STATUS` = :status';
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
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
       // if(empty($this -> timeout))
        //    return;
        
        $timeoutThreshold = time() - EXEC_TIMEOUT;

        $sql = 'SELECT `ESOBJECT_CONVERSION_OBJECT_ID`, `ESOBJECT_CONVERSION_FORMAT`, `ESOBJECT_CONVERSION_RESOLUTION` FROM `ESOBJECT_CONVERSION` WHERE `ESOBJECT_CONVERSION_STATUS` = :status AND `ESOBJECT_CONVERSION_TIME` <= :threshold';
        $pdo = RsPDO::getInstance();
        $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
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
$c -> convert();
exit();
