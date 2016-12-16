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
    ->addTranslation('de', 'Error', 'Error')
	->addTranslation('en', 'Missing parameter ":name".', 'Missing parameter ":name".')
	->addTranslation('en', 'Invalid parameter ":name".', 'Invalid parameter ":name".')
	->addTranslation('en', 'Error loading configuration.', 'Error loading configuration.')
	->addTranslation('en', 'Error loading config for application ":app_id".', 'Error loading config for application ":app_id".')
	->addTranslation('en', 'A network error occured.', 'A network error occured.')
	->addTranslation('en', 'You\'re not authorized to access this resource.', 'You\'re not authorized to access this resource.')
	->addTranslation('en', 'An internal server error occured.', 'An internal server error occured.')

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
    ->addTranslation('en', 'Video player cannot play back this video.', 'Video player cannot play back this video.')
    ->addTranslation('en', 'Fit image size to browser window (esc)', 'Fit image size to browser window (esc)')
    ->addTranslation('en', 'Show image in original size', 'Show image in original size')
    ->addTranslation('en', 'published under a', 'published under a')
    ->addTranslation('en', 'custom license', 'custom license')
    ->addTranslation('en', 'title', 'Title')
    ->addTranslation('en', 'showMetadata', 'Show metadata')
    ->addTranslation('en', 'hideMetadata', 'Hide metadata')
	->addTranslation('en', 'showInformation', 'Show information')
	->addTranslation('en', 'hideInformation', 'Hide information')
	->addTranslation('en', 'toDownload', 'Download')
	->addTranslation('en', 'cannotOpenObject', 'This material can not be displayed in the browser.')
	->addTranslation('en', 'cannotOpenObjectText', 'In order to use the material please download it.')
	->addTranslation('en', 'goToOrigin', 'Go to origin');
