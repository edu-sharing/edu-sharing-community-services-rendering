<?php

$wurflDir = dirname(__FILE__) . '/../WURFL';
$resourcesDir = dirname(__FILE__) . '/../resources';

require_once $wurflDir.'/Application.php';

$wurflConfigFile = $resourcesDir.'/wurfl-config.xml';

// Set location of the WURFL File
$wurflConfig->wurflFile($resourcesDir.'/wurfl.zip');

// Create WURFL Configuration from an XML config file
$wurflConfig = new WURFL_Configuration_XmlConfig($wurflConfigFile);

// Create a WURFL Manager Factory from the WURFL Configuration
$wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

// Create a WURFL Manager
/* @var $wurflManager WURFL_WURFLManager */
$wurflManager = $wurflManagerFactory->create();