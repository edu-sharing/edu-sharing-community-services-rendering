<?php
			define('comment',  'comment');
			define('trustedclient',  'trustedclient [true|false]');
			define('type',  'type [REPOSITORY|SERVICE|LMS]');
			define('authenticationwebservice',  'authenticationws');
			define('host',  'IP-adress');
			define('port',  'port');
			define('wspath',  'rel. path');
			define('alfrescocontext',  'alfresco context');
			define('searchclass',  'searchclass');
			define('contenturl',  'RS Content Url');
			define('previewurl',  'RS preview Url');
			define('nodeid_key',  'Node ID Key');
			define('is_home_node',  'Home Node[true|false]');
			define('appcaption',  'caption');
			define('appid',  'Applikation ID');
			define('username',  'username');
			define('password',  'password');
			define('authenticationwebservice_wsdl',  'auth-servic url');
			define('ccusagewebservice',  'usage-service url');

global $Translate;
$Translate
    ->addTranslation('en', 'Error', 'Error')
	->addTranslation('en', 'Missing parameter ":name".', 'Missing parameter ":name".')
	->addTranslation('en', 'Invalid parameter ":name".', 'Invalid parameter ":name".')
	->addTranslation('en', 'Error loading configuration.', 'Error loading configuration.')
	->addTranslation('en', 'Error loading config for application ":app_id".', 'Error loading config for application ":app_id".')
	->addTranslation('en', 'A network error occured.', 'A network error occured.')
	->addTranslation('en', 'You\'re not authorized to access this resource.', 'You\'re not authorized to access this resource.')
	->addTranslation('en', 'An internal server error occured.', 'An internal server error occured.')
	->addTranslation('en', 'The requested version of ":title" is corrupt or missing.', 'The requested version of ":title" is corrupt or not present.')
    ->addTranslation('en', 'The object to which this collection object refers is no longer present.', 'The object to which this collection object refers is no longer present.')
	->addTranslation('en', 'authored_by', 'by')
	->addTranslation('en', 'author', 'author')
    ->addTranslation('en', 'Resource is being converted for your view ...', 'Resource is being converted for your view ...')
    ->addTranslation('en', 'Loading player ...', 'Loading player ...')
    ->addTranslation('en', 'No usage-information retrieved.', 'Resource is not available.')
    ->addTranslation('en', 'back', 'back')
    ->addTranslation('en', 'print', 'print')
    ->addTranslation('en', 'saveToDisk', 'save to disk')
    ->addTranslation('en', 'Chapter', 'Chapter')
    ->addTranslation('en', 'Object does not exist in repository', 'Object does not exist in repository.')
    ->addTranslation('en', 'Error fetching object properties', 'Error fetching object properties.')
    ->addTranslation('de', 'Set video resolution', 'Set video resolution')
    ->addTranslation('en', 'Video player cannot play back this video.', 'Video player cannot play back this video.')
    ->addTranslation('en', 'Fit image size to browser window (esc)', 'Fit image size to browser window (esc)')
    ->addTranslation('en', 'Show image in original size', 'Show image in original size')
    ->addTranslation('en', 'published under a', 'published under a')
    ->addTranslation('en', 'custom license', 'custom license')
    ->addTranslation('en', 'title', 'Title')
	->addTranslation('en', 'toDownload', 'Download')
	->addTranslation('en', 'cannotOpenObject', 'This material can not be displayed in the browser.')
	->addTranslation('en', 'cannotOpenObjectText', 'In order to use the material please download it.')
	->addTranslation('en', 'goToOrigin', 'Go to link location')
    ->addTranslation('en', 'showDocument', 'Show document')
    ->addTranslation('en', 'showInLearningAppsOrg', 'Open in learningapps.org')
    ->addTranslation('en', 'hasNoContentLicense', 'You\'re not allowed to use the content because of licence restrictions.')
    ->addTranslation('en', 'startScorm', 'Start SCORM')
    ->addTranslation('en', 'Omega plugin error', 'Error in Omega plugin')
    ->addTranslation('en', 'API respsonse is empty', 'API response is empty')
    ->addTranslation('en', 'Wrong identifier', 'API response contains wrong identifier')
    ->addTranslation('en', 'urls empty', 'API response contains empty streamURL and empty downloadURL')
    ->addTranslation('en', 'Property replicationsourceid is empty', 'Object property replicationsourceid is not set')
    ->addTranslation('en', 'given streamURL is invalid', 'streamURL is invalid - HTTP status')
    ->addTranslation('en', 'jumpToDataProvider :dataProvider', 'Jump to data provider (:dataProvider)')
    ->addTranslation('en', 'ltiGotoProvider', 'Jump to provider')
    ->addTranslation('en', 'dataProtectionRegulations1 :providerName', 'Yes, show contents of :providerName.')
    ->addTranslation('en', 'dataProtectionRegulations2 :providerName', 'You\'re about to load and show content of :providerName. Personal data could be exchanged with :providerName and processed there.')
    ->addTranslation('en', 'dataProtectionRegulations1default', 'Yes, show content from external source')
    ->addTranslation('en', 'dataProtectionRegulations2default', 'You\'re about to load and show content of an external source. Personal data could be exchanged with :providerName and processed there.')
    ->addTranslation('en', 'dataProtectionRegulationsHintDefault', 'Further information can be found in the privacy policy of the provider.')
    ->addTranslation('en', 'dataProtectionRegulations3', 'Find further information here:')
    ->addTranslation('en', 'dataProtectionRegulations4', 'Agree and continue')
    ->addTranslation('en', 'dataProtectionRegulations', 'Privacy policy')
    ->addTranslation('en', 'abort', 'Abort')
    ->addTranslation('en', 'of', 'of')
    ->addTranslation('en', 'h5p_ie_hint', 'In case of display problems, please use a current browser.')
    ->addTranslation('en', 'seqenceChildren :count', ':count more materials belong to this')
    ->addTranslation('en', 'seqenceViewChildren', 'View all and download')
    ->addTranslation('en', 'directoryOpen', 'Open directory and download')
    ->addTranslation('en', 'createdBy', 'Created by')
    ->addTranslation('en', 'goToCourse', 'Go to course')
    ->addTranslation('en', 'inConversionQueue', 'Format is being converted.')
    ->addTranslation('en', 'conversionError', 'There was an error during conversion.')
    ->addTranslation('en', 'Postition in queue', 'Postition in queue')
    ->addTranslation('en', 'Resource is waiting for conversion.', 'Resource is waiting for conversion.')
    ->addTranslation('en', 'ViewerJS_Actual Size', 'Actual Size')
    ->addTranslation('en', 'ViewerJS_Automatic', 'Automatic')
    ->addTranslation('en', 'ViewerJS_Full Width', 'Full Width')
    ->addTranslation('en', 'ViewerJS_Next Page', 'Next Page')
    ->addTranslation('en', 'ViewerJS_Previous Page', 'Previous Page')
    ->addTranslation('en', 'ViewerJS_Zoom In', 'Zoom in')
    ->addTranslation('en', 'ViewerJS_Zoom Out', 'Zoom out')
    ->addTranslation('en', 'imageDescriptionNotAvailable', 'An image description is not available.');
