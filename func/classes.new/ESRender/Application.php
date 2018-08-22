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
    public function trackObject($RepositoryId, $ApplicationId, $esObjectId, $ObjectId, $ObjectName, $ObjectVersion, $ModuleId, $ModuleName, $UserId, $UserName, $CourseId = null) {
        $Logger = $this -> getLogger();

        if ($Logger) {
            $Logger -> debug('Start ESRender_Application::trackObject()');
        }

        try {

            if (DISABLE_TRACK_ANONYMIZATION === true) {
                $Logger -> info('Track object full.');
            } else {
                $Logger -> info('Track object anonymized.');
                $UserName = NULL;
                $UserId = NULL;
            }

            $params = array("estrack_app_id" => $ApplicationId, "estrack_rep_id" => $RepositoryId, "estrack_esobject_id" => $esObjectId, "estrack_object_id" => $ObjectId, "estrack_name" => $ObjectName, "estrack_version" => $ObjectVersion, "estrack_modul_id" => $ModuleId, "estrack_modul_name" => $ModuleName, "estrack_user_id" => $UserId, "estrack_user_name" => $UserName, "estrack_lms_course_id" => $CourseId);
            $ret = $this -> setTracking($params);
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

    private function setTracking($p_obj) {
        $pdo = RsPdo::getInstance();
        try {
            $arr = array('ESTRACK_APP_ID' => $p_obj['estrack_app_id'],
                'ESTRACK_ESOBJECT_ID' => (string)$p_obj['estrack_esobject_id'],
                'ESTRACK_REP_ID' => $p_obj['estrack_rep_id'],
                'ESTRACK_LMS_COURSE_ID' => (string)$p_obj['estrack_lms_course_id'],
                'ESTRACK_OBJECT_ID' => $p_obj['estrack_object_id'],
                'ESTRACK_NAME' => $p_obj['estrack_name'],
                'ESTRACK_MODUL_ID' => (string)$p_obj['estrack_modul_id'],
                'ESTRACK_MODUL_NAME' => $p_obj['estrack_modul_name'],
                'ESTRACK_VERSION' => $p_obj['estrack_version'],
                'ESTRACK_USER_NAME' => $p_obj['estrack_user_name'],
                'ESTRACK_USER_ID' => $p_obj['estrack_user_id'],
                'STATE' => 'Y');

            $extendedTrackingParams = Config::get('extendedTracking');
            if(!empty($extendedTrackingParams)) {
                foreach ($extendedTrackingParams as $k => $v) {
                    $arr[$k] = $v;
                }
            }
            $sql = 'INSERT INTO `ESTRACK` (`';
            $sql .= implode('`,`', array_keys($arr));
            $sql .= '`) VALUES (:';
            $sql .= implode(',:', array_keys($arr));
            $sql .= ')';
    
            $stmt = $pdo -> prepare($pdo -> formatQuery($sql));
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
