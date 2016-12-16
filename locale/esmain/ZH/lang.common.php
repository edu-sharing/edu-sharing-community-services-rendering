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
    ->addTranslation('zh', 'Error', '错误')
    ->addTranslation('zh', 'Missing parameter ":name".', '缺少参数 ":name".')
	->addTranslation('zh', 'Invalid parameter ":name".', '无效参数 ":name".')
	->addTranslation('zh', 'Error loading configuration.', '载入配置信息失败')
	->addTranslation('zh', 'Error loading config for application ":app_id".', '载入应用配置失败":app_id".')
	->addTranslation('zh', 'A network error occured.', '发生网络错误')
	->addTranslation('zh', 'You\'re not authorized to access this resource.', '您无权访问该资源')
	->addTranslation('zh', 'An internal server error occured.', '发生服务器内部错误')

	->addTranslation('zh', 'authored_by', '由')
	->addTranslation('zh', 'author', '作者')
    ->addTranslation('zh', 'Resource is being converted for your view ...','资源正在转换 ...')
    ->addTranslation('zh', 'Loading player ...','播放器载入中...')
    ->addTranslation('zh', 'No usage-information retrieved.', '没有获取使用信息')
    ->addTranslation('zh', 'back','退回')
    ->addTranslation('zh', 'print','打印')
    ->addTranslation('zh', 'saveToDisk', '保存到磁盘')
    ->addTranslation('zh', 'Chapter', 'Chapter')
    ->addTranslation('zh', 'Object does not exist in repository', 'Object does not exist in repository')
    ->addTranslation('zh', 'Error fetching object properties', 'Error fetching object properties')
    ->addTranslation('zh', 'title', 'Title')
    ->addTranslation('zh', 'showMetadata', 'Show metadata')
    ->addTranslation('zh', 'hideMetadata', 'hide metadata')
    ->addTranslation('zh', 'showInformation', 'Show information')
    ->addTranslation('zh', 'hideInformation', 'Hide information')
	->addTranslation('zh', 'toDownload', 'Download')
	->addTranslation('zh', 'cannotOpenObject', '此材料无法在浏览器中显示')
	->addTranslation('zh', 'cannotOpenObjectText', '为了使用材料，请下载它')
	->addTranslation('zh', 'goToOrigin', 'Go to origin');
