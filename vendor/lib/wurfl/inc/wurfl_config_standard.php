<?php

$wurflDir = dirname(__FILE__) . '/../WURFL';
$resourcesDir = dirname(__FILE__) . '/../resources';

require_once $wurflDir.'/Application.php';

$persistenceDir = $resourcesDir.'/storage/persistence';
$cacheDir = $resourcesDir.'/storage/cache';

// Create WURFL Configuration
$wurflConfig = new WURFL_Configuration_InMemoryConfig();

// Set location of the WURFL File
$wurflConfig->wurflFile($resourcesDir.'/wurfl.zip');

// Set the match mode for the API ('performance' or 'accuracy')
$wurflConfig->matchMode('accuracy');

// Automatically reload the WURFL data if it changes
$wurflConfig->allowReload(true);


// Optionally specify which capabilities should be loaded
/*$wurflConfig->capabilityFilter(array(
	'is_wireless_device',
	'model_name',
	'mobile_browser_version',
	'full_flash_support',
));
*/

// Setup WURFL Persistence
$wurflConfig->persistence('file', array('dir' => $persistenceDir));

// Setup Caching
$wurflConfig->cache('file', array('dir' => $cacheDir, 'expiration' => 36000));

// Create a WURFL Manager Factory from the WURFL Configuration
$wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

// Create a WURFL Manager
/* @var $wurflManager WURFL_WURFLManager */
$wurflManager = $wurflManagerFactory->create();
