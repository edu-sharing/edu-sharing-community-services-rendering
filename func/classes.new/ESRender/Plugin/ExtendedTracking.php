<?php

define('INSTITUTION_SEAPARTOR', '@');

class ESRender_Plugin_ExtendedTracking extends ESRender_Plugin_Abstract {

    public function __construct() {
        $pdo = RsPDO::getInstance();
        $query = $pdo->query("SHOW COLUMNS FROM `ESTRACK` LIKE 'ESTRACK_AFFILIATION'");
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);
        if(empty($result_array)) {
            $query = "ALTER TABLE `ESTRACK`
                ADD COLUMN `ESTRACK_PERSISTENTID` VARCHAR(200), 
                ADD COLUMN `ESTRACK_AFFILIATION` VARCHAR(200), 
                ADD COLUMN `ESTRACK_INSTITUTION` VARCHAR(200), 
                ADD COLUMN `ESTRACK_LEARNINGMODULE` VARCHAR(200), 
                ADD COLUMN `ESTRACK_ELEMENT` VARCHAR(200), 
                ADD COLUMN `ESTRACK_VIEWTYPE` VARCHAR(200) 
                ";
            $pdo -> query($query);
        }
    }

    public function preTrackObject($params = array()) {
        if(ENABLE_TRACK_OBJECT && DISABLE_TRACK_ANONYMIZATION) {
            $learningmodule = '';
            foreach (Config::get('renderInfoLMSReturn')->properties->item as $property) {
                if($property->key == '{virtualproperty}primaryparent_nodeid')
                    $learningmodule = $property->value;
            }
            $extendedTrackingParams = array();
            $extendedTrackingParams['ESTRACK_PERSISTENTID'] = $params['user_id'];
            $extendedTrackingParams['ESTRACK_AFFILIATION'] = strtoupper(Config::get('renderInfoLMSReturn') -> eduSchoolPrimaryAffiliation);
            $extendedTrackingParams['ESTRACK_INSTITUTION'] = substr($params['user_id'], strpos($params['user_id'], INSTITUTION_SEAPARTOR) + 1);
            $extendedTrackingParams['ESTRACK_LEARNINGMODULE'] = $learningmodule;
            $extendedTrackingParams['ESTRACK_ELEMENT'] = $params['object_id'];
            $extendedTrackingParams['ESTRACK_VIEWTYPE'] = ($params['view_type'] == 'download') ? 'DOWNLOAD' : 'SHOW';
            Config::set('extendedTracking', $extendedTrackingParams);
        }
    }
}