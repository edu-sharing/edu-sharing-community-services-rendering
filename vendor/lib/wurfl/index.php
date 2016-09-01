<?php
// Include the configuration file
include_once dirname(__FILE__) . '/inc/wurfl_config_standard.php';

$wurflInfo = $wurflManager -> getWURFLInfo();

$ua = $_SERVER['HTTP_USER_AGENT'];
// This line detects the visiting device by looking at its HTTP Request ($_SERVER)
$requestingDevice = $wurflManager -> getDeviceForHttpRequest($_SERVER);

// run this manually to get all capabilities
   // foreach($requestingDevice->getAllCapabilities() as $h => $k) {
   //     echo $h . ': ' . $k . '<br/>';
   // }


//echo $requestingDevice -> getCapability('playback_mp4');

$wurflConfig->capabilityFilter(array(
        "playback_mp4",
        "model_name",
        "is_wireless_device"
));

return $requestingDevice;
