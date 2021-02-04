<?php

/**
 *
 *
 *
 */
class ESRender_Application
extends ESRender_Application_Abstract {

    /**
     * (non-PHPdoc)
     * @see ESRender_Application_Interface::trackObject()
     */
    public function trackObject($esObjectId) {
        $Logger = $this -> getLogger();

        if ($Logger) {
            $Logger -> debug('Start ESRender_Application::trackObject()');
        }

        try {

            $ret = $this -> setTracking($esObjectId);
            $params['estrack_id'] = $ret;

            if ($Logger) {
                $Logger -> info('Successfully tracked object.');
            }

            return $ret;
        } catch (Exception $e) {
            if ($Logger) {
                $Logger -> error('Error while tracking object.');
                $Logger -> error('Given message: "' . $e -> getMessage() . '".');
            }
        }

        return false;
    }

    private function setTracking($esObjId) {
        $pdo = RsPdo::getInstance();
        try {
            $arr = array(
                'ESTRACK_ESOBJECT_ID' => (string)$esObjId
               );

            $sql = 'INSERT INTO "ESTRACK" ("';
            $sql .= implode('","', array_keys($arr));
            $sql .= '") VALUES (:';
            $sql .= implode(',:', array_keys($arr));
            $sql .= ')';
    
            $stmt = $pdo -> prepare($sql);
            foreach($arr as $key => $value) {
                $stmt -> bindvalue(':'.$key, $value);
            }
    
            $result = $stmt -> execute();
            if(!$result)
                throw new Exception('Error inserting tracking-data. ' . print_r($stmt -> errorInfo(), true));
    
        } catch (PDOException $e) {
            throw new Exception($e -> getMessage());
        }
        
    }

}
