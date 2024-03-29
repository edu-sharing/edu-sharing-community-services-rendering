<?php

define('INSTITUTION_SEAPARTOR', '@');

class ESRender_Plugin_ExtendedTracking extends ESRender_Plugin_Abstract {

    public function __construct() {
        $pdo = RsPDO::getInstance();
        $query = $pdo->query("SHOW COLUMNS FROM `ESTRACK` LIKE '%ESTRACK_AFFILIATION%'");
        $result_array = $query->fetchAll(PDO::FETCH_ASSOC);
        if(empty($result_array)) {
            $query = "ALTER TABLE `ESTRACK`
                ADD COLUMN `ESTRACK_PERSISTENTID` VARCHAR(200), 
                ADD COLUMN `ESTRACK_AFFILIATION_0` VARCHAR(200),
		ADD COLUMN `ESTRACK_AFFILIATION_1` VARCHAR(200), 
		ADD COLUMN `ESTRACK_AFFILIATION_2` VARCHAR(200),
		ADD COLUMN `ESTRACK_AFFILIATION_3` VARCHAR(200),
		ADD COLUMN `ESTRACK_AFFILIATION_4` VARCHAR(200),
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
                if($property->key == '{http://www.campuscontent.de/model/1.0}learnunit_id')
                    $learningmodule = $property->value;
                if($property->key == '{http://www.campuscontent.de/model/1.0}original')
                    $element  = $property->value;
            }
            $extendedTrackingParams = array();
            $extendedTrackingParams['ESTRACK_PERSISTENTID'] = $params['user_id'];

	    $affiliation = (is_array(Config::get('renderInfoLMSReturn') -> remoteRoles -> item)) ? Config::get('renderInfoLMSReturn') -> remoteRoles -> item : array(Config::get('renderInfoLMSReturn') -> remoteRoles -> item);
	    
	    foreach($affiliation as $k => $v) {
		if($k < 5)
		    $extendedTrackingParams['ESTRACK_AFFILIATION_'.$k] = $v;
	    }
	    $extendedTrackingParams['ESTRACK_INSTITUTION'] = (strpos($params['user_id'], INSTITUTION_SEAPARTOR) > -1) ? substr($params['user_id'], strpos($params['user_id'], INSTITUTION_SEAPARTOR) + 1) : '';
            if(empty($extendedTrackingParams['ESTRACK_INSTITUTION']))
		$extendedTrackingParams['ESTRACK_INSTITUTION'] = 'local';
	    $extendedTrackingParams['ESTRACK_LEARNINGMODULE'] = $learningmodule;
            $extendedTrackingParams['ESTRACK_ELEMENT'] = $element;
            $extendedTrackingParams['ESTRACK_VIEWTYPE'] = $params['view_type'];
            Config::set('extendedTracking', $extendedTrackingParams);
        }
    }
}