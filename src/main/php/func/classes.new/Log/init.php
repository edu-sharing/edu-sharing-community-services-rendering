<?php
// init LOGGER
require_once(dirname(__FILE__) . '/../../../vendor/autoload.php');

Logger::configure(dirname(__FILE__) . '/../../../conf/de.metaventis.esrender.log4php.xml');
if(isset($_SERVER['HTTP_X_B3_TRACEID']) && isset($_SERVER['HTTP_X_B3_SPANID'])) {
    LoggerMDC::put("TraceId", $_SERVER['HTTP_X_B3_TRACEID']);
    LoggerMDC::put("SpanId", $_SERVER['HTTP_X_B3_SPANID']);
}
return Logger::getLogger('de.metaventis.esrender.index');
