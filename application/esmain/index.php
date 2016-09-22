<?php

/**
 * This product Copyright 2010 metaVentis GmbH. For detailed notice,
 * see the "NOTICE" file with this distribution.
 *
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: *");



try {
    include_once ('../../conf.inc.php');

    // start session
    if (empty($ESRENDER_SESSION_NAME)) {
        error_log('ESRENDER_SESSION_NAME not set in conf/system.conf.php');
        $ESRENDER_SESSION_NAME = 'ESSID';
    }
    
    session_name($ESRENDER_SESSION_NAME);

    $sessid = mc_Request::fetch($ESRENDER_SESSION_NAME, 'CHAR', '');
    if(!empty($sessid))
    	session_id($sessid);
 
    
    if (!session_start()) {
        throw new Exception('Could not start session.');
    }

    $esrenderSessionId = session_id();
    if (!$esrenderSessionId) {
        throw new Exception('Could not get current session_id().');
    }

    // init LOGGER
    require_once (dirname(__FILE__) . '/../../func/extern/apache-log4php-2.0.0-incubating/src/main/php/Logger.php');
    Logger::configure(dirname(__FILE__) . '/../../conf/de.metaventis.esrender.log4php.properties');
    $Logger = Logger::getLogger('de.metaventis.esrender.index');

    $Logger -> info('Starting up.');
    $Logger -> debug($_SERVER['REQUEST_URI']);

    // init PROXY
    $ProxyConfig = '../../conf/proxy.conf.php';
    if (file_exists($ProxyConfig)) {
        include_once ($ProxyConfig);
    }

    require_once (MC_LIB_PATH . 'ESApp.php');
    require_once (MC_LIB_PATH . 'EsApplications.php');
    require_once (MC_LIB_PATH . 'EsApplication.php');

    require_once (MC_LIB_PATH . 'ESModule.php');
    require_once (MC_LIB_PATH . 'ESObject.php');

    unset($CFG);

    // init PLUGINS
    $Plugins = array();
    $PluginConfig = '../../conf/plugins.conf.php';
    if (file_exists($PluginConfig)) {
        require_once ($PluginConfig);
    }

    /*
     * as this is the first plugin-loop we'll try to set the plugin's
     * logger here to assure each plugin has logging-capability.
     */
    foreach ($Plugins as $name => $Plugin) {
        $Plugin -> setDefaultLogger($Logger);
    }

    // init translate
    global $Translate, $LanguageCode;
    $Translate = new Phools_Translate_Array();

    //get language from config if none requested
    switch($DEFAULT_LANG) {
        case 1 :
            $defaultLangConf = 'de';
            break;
        case 2 :
            $defaultLangConf = 'zh';
            break;
        case 4 :
            $defaultLangConf = 'fr';
            break;
        case 3 :
        default :
            $defaultLangConf = 'en';
    }

    // LANGUAGE
    $LanguageCode = mc_Request::fetch('language', 'CHAR', $defaultLangConf);
    switch( strtolower($LanguageCode) ) {
        case 'de' :
            $Locale = new Phools_Locale_Default('de', 'DE', ',', '.');
            require_once (dirname(__FILE__) . '/../../locale/esmain/DE/lang.common.php');
            break;
        case 'zh' :
            $Locale = new Phools_Locale_Default('zh', 'ZH', ',', '.');
            require_once (dirname(__FILE__) . '/../../locale/esmain/ZH/lang.common.php');
            break;
        case 'fr' :
            $Locale = new Phools_Locale_Default('fr', 'FR', ',', '.');
            require_once (dirname(__FILE__) . '/../../locale/esmain/FR/lang.common.php');
            break;
        case 'en' :
        default :
            $Locale = new Phools_Locale_Default('en', 'EN', '.', '`');
            require_once (dirname(__FILE__) . '/../../locale/esmain/EN/lang.common.php');
    }
    // init templating
    $TemplateDirectory = dirname(__FILE__) . '/../../theme';
    $Template = new Phools_Template_Script($TemplateDirectory);
    $Template -> setTheme('default') -> setLocale($Locale) -> setTranslate($Translate);
    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin "' . $name . '"::setTemplate()');
        $Plugin -> setTemplate($Template);
    }
    
    // init DATABASE INTERFACE
    $pdo = new RsPDO();
    
    // init APPLICATION
    $RenderApplication = new ESRender_Application($pdo);
    $RenderApplication -> setLogger($Logger);

    // REPOSITORY-ID
    $req_data['rep_id'] = mc_Request::fetch('rep_id', 'CHAR');
    if (!$req_data['rep_id']) {
        $Logger -> error('Missing request-param "rep_id".');
        throw new ESRender_Exception_MissingRequestParam('rep_id');
    }

    $Validator = new ESRender_Validator_ApplicationId();
    if (!$Validator -> validate($req_data['rep_id'])) {
        $Logger -> error('Invalid request-param "rep_id".');
        throw new ESRender_Exception_InvalidRequestParam('rep_id');
    }

    // APPLICATION-ID
    $req_data['app_id'] = mc_Request::fetch('app_id', 'CHAR');
    if (!$req_data['app_id']) {
        $Logger -> info('Using repository-id as requested application-id, because request parameter "app-id" was empty.');
        $req_data['app_id'] = $req_data['rep_id'];
    }

    $Validator = new ESRender_Validator_ApplicationId();
    if (!$Validator -> validate($req_data['app_id'])) {
        $Logger -> error('Invalid request-param "app_id".');
        throw new ESRender_Exception_InvalidRequestParam('app_id');
    }

    // OBJECT-ID
    $req_data['obj_id'] = mc_Request::fetch('obj_id', 'CHAR');
    if (!$req_data['obj_id']) {
        $Logger -> error('Missing request-param "obj_id".');
        throw new ESRender_Exception_MissingRequestParam('obj_id');
    }

    $Validator = new ESRender_Validator_ObjectId();
    if (!$Validator -> validate($req_data['obj_id'])) {
        $Logger -> error('Invalid request-param "obj_id".');
        throw new ESRender_Exception_InvalidRequestParam('obj_id');
    }

    // RESOURCE-ID (optional)
    $req_data['resource_id'] = mc_Request::fetch('resource_id', 'CHAR');
    if (!empty($req_data['resource_id'])) {
        $Validator = new ESRender_Validator_ResourceId();
        if (!$Validator -> validate($req_data['resource_id'])) {
            $Logger -> error('Invalid request-param "resource_id".');
            throw new ESRender_Exception_InvalidRequestParam('resource_id');
        }
    }

    // COURSE-ID (optional)
    $req_data['course_id'] = mc_Request::fetch('course_id', 'CHAR');
    if (!empty($req_data['course_id'])) {
        $Validator = new ESRender_Validator_CourseId('/^[a-z0-9-]+$/ui');
        if (!$Validator -> validate($req_data['course_id'])) {
            $Logger -> error('Invalid request-param "course_id".');
            throw new ESRender_Exception_InvalidRequestParam('course_id');
        }
    }

    // USERNAME
    $req_data['username'] = $req_data['usernameEncrypted'] = mc_Request::fetch('u', 'CHAR');    
    if (empty($req_data['username'])) {
        throw new ESRender_Exception_MissingRequestParam('username');
    } else {
        $Validator = new ESRender_Validator_Username();
        if (!$Validator -> validate($req_data['username'])) {
            $Logger -> error('Invalid request-param "u".');
            $Logger -> debug('Given username "' . $req_data['username'] . '"');
            throw new ESRender_Exception_InvalidRequestParam('u');
        }
    }
    
    //BACKLINK
    $req_data['backLink'] = mc_Request::fetch('backLink', 'CHAR'); 

    try {
        $handler = mcrypt_module_open('blowfish', '', 'cbc', '');
        $secretKey = ES_KEY;
        $iv = ES_IV;
        mcrypt_generic_init($handler, $secretKey, $iv);
        $decrypted = mdecrypt_generic($handler, base64_decode($req_data['username']));
        mcrypt_generic_deinit($handler);
        $user_name = trim($decrypted);
        mcrypt_module_close($handler);      
    } catch(Exception $e) {
        echo 'Decryption error';
        exit();
    }
    
    //metacoon fix
    $user_name = str_replace('{{{at}}}', '@', $user_name);
    
    // VERSION (optional)
    $req_data['version'] = mc_Request::fetch('version', 'CHAR');
    if ($req_data['version']) {
        $Validator = new ESRender_Validator_Version();
        if (!$Validator -> validate($req_data['version'])) {
            $Logger -> error('Invalid request-param "version".');
            $Logger -> debug('Given version "' . $req_data['version'] . '"');

            throw new ESRender_Exception_InvalidRequestParam('version');
        }
    }

    // THEME
    $Theme = mc_Request::fetch('theme', 'CHAR');
    if ($Theme) {
        $Validator = new ESRender_Validator_Theme();
        if (!$Validator -> validate($Theme)) {
            throw new ESRender_Exception_InvalidRequestParam('theme');
        }
        $Template -> setTheme($Theme);
    }

    // DISPLAY MODE
    $display_kind = mc_Request::fetch('display', 'CHAR', 'window');
    if ($display_kind) {
        $Validator = new ESRender_Validator_DisplayMode();
        if (!$Validator -> validate($display_kind)) {
            throw new ESRender_Exception_InvalidRequestParam('display');
        }
    }
    
    $req_data['token'] = mc_Request::fetch('token', 'CHAR', '');
    
    // WIDTH
    $req_data['width'] = mc_Request::fetch('width', 'INT', 0);

    // HEIGHT
    $req_data['height'] = mc_Request::fetch('height', 'INT', 0);

    $CurrentDirectoryName = basename(dirname(__FILE__));
    $application = new ESApp();
    $application -> getApp($CurrentDirectoryName);

    $Logger -> debug('Initialized application.');

    $hc = $application -> getHomeConf();
    if (!$hc) {
        $Logger -> error('Error loading home-configuration.');

        throw new ESRender_Exception_HomeConfigNotLoaded();
    }

    $Logger -> debug('Successfully loaded home-configuration.');

    // load repository-config
    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin "' . $name . '"::preLoadRepository()');
        $Plugin -> preLoadRepository($req_data['rep_id'], $req_data['app_id'], $req_data['obj_id'], $req_data['course_id'], $req_data['resource_id'], $user_name);
    }

    $remote_rep = $application -> getAppByID($req_data['rep_id']);
    if (!$remote_rep) {
        $Logger -> error('Error loading application by requested repository-id "' . $req_data['rep_id'] . '".');

        throw new ESRender_Exception_AppConfigNotLoaded($req_data['rep_id']);
    }

    $Logger -> debug('Successfully loaded repository by id "' . $req_data['rep_id'] . '".');
    
    
    $homeRepId = $hc -> prop_array['homerepid'];
    if($homeRepId === $req_data['rep_id'] || empty($homeRepId)) {
        $homeRep = $remote_rep;
    } else {
        $homeRep = $application -> getAppByID($homeRepId);
        if (!$homeRep) {
            $Logger -> error('Error loading application by requested repository-id "' . $homeRepId . '".');
            throw new ESRender_Exception_AppConfigNotLoaded($homeRepId);
        }
    }

    $Logger -> debug('Successfully loaded home repository by id "' . $homeRepId . '".');


    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin "' . $name . '"::postLoadRepository()');
        $Plugin -> postLoadRepository($remote_rep, $req_data['app_id'], $req_data['obj_id'], $req_data['course_id'], $req_data['resource_id'], $user_name);
    }


    
    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin "' . $name . '"::preSslVerification()');
        $Plugin -> preSslVerification($remote_rep, $req_data['app_id'], $req_data['obj_id'], $req_data['course_id'], $req_data['resource_id'], $user_name, $homeRep);
    }    
    
    $skipSslVerification = false;
    
    if(!empty($req_data['token'])) {
    	if($req_data['token'] == $_SESSION['esrender']['token'] || empty($_SESSION['esrender']['token'])) {
        	$skipSslVerification = true;
    	} else {
    		$Logger->error('Token not valid!');
    	}
    }
    
    try {
    	$token = md5(uniqid());
    } catch (Exception $e) {
    	throw new Exception('Cannot generate token.');
    }

    
    if(!$skipSslVerification) {
    
        $req_data['timestamp'] = mc_Request::fetch('ts', 'CHAR');   
        if (empty($req_data['timestamp'])) {
            $Logger -> error('Missing request-param "timestamp".');
            throw new ESRender_Exception_MissingRequestParam('timestamp');
        }
    
        if(empty($_GET['sig'])) {
            $Logger -> error('Missing request-param "sig".');
            throw new ESRender_Exception_MissingRequestParam('sig');
        }

        try {   
            $pubkeyid = openssl_get_publickey($remote_rep -> prop_array['public_key']);
            $signature = rawurldecode($_GET['sig']);
            $dataSsl = urldecode($req_data['rep_id']);
            $signature = base64_decode($signature);
            $ok = openssl_verify($dataSsl . $req_data['timestamp'], $signature, $pubkeyid);
        } catch (Exception $e) {
            throw new ESRender_Exception_SslVerification('SSL signature check failed');
        }
        
        if ($ok != 1) {
            throw new ESRender_Exception_SslVerification('SSL signature check failed');
        }
        
        $now = microtime(true) * 1000;
        
        $message_send_offset_ms = 10000;
        if(isset($remote_rep -> prop_array['message_send_offset_ms']))
            $message_send_offset_ms = $remote_rep -> prop_array['message_send_offset_ms'];
        if($now + $message_send_offset_ms < $req_data['timestamp']) {
            throw new ESRender_Exception_SslVerification('Timestamp sent bigger than current timestamp');
        }
        
        $message_offset_ms = 10000;
        if(isset($remote_rep -> prop_array['message_offset_ms']))
            $message_offset_ms = $remote_rep -> prop_array['message_offset_ms'];
        if($now - $req_data['timestamp'] > $message_offset_ms) {
            throw new ESRender_Exception_SslVerification('Token expired');
        }

    }

    //init wurfl
    require_once (dirname(__FILE__) . '/../../vendor/lib/wurfl/index.php');

   
	
    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin "' . $name . '"::postSslVerification()');
        $Plugin -> postSslVerification($remote_rep, $req_data['app_id'], $req_data['obj_id'], $req_data['course_id'], $req_data['resource_id'], $user_name, $homeRep);
    }

    $SoapClientParams = array();
    if ( defined('USE_HTTP_PROXY') && USE_HTTP_PROXY ) {
        require_once(dirname(__FILE__) . '/../../func/classes.new/Helper/ProxyHelper.php');
        $proxyHelper = new ProxyHelper($remote_rep->prop_array['renderinfowebservice_wsdl']);
        $SoapClientParams = $proxyHelper -> getSoapClientParams();
    }

    $client = new SoapClient(
        $remote_rep->prop_array['renderinfowebservice_wsdl'],
        $SoapClientParams); 

    try
    {
        $timestamp = round(microtime(true) * 1000);
        $signData = $hc->prop_array['appid'] . $timestamp;
        $priv_key = $hc -> prop_array['private_key'];
        $pkeyid = openssl_get_privatekey($priv_key);      
        openssl_sign($signData, $signature, $pkeyid);
        $signature = base64_encode($signature);
        openssl_free_key($pkeyid);    

        $headers = array();    
        $headers[] = new SOAPHeader('http://render.webservices.edu_sharing.org', 'appId', $hc->prop_array['appid']);
        $headers[] = new SOAPHeader('http://render.webservices.edu_sharing.org', 'timestamp', $timestamp); 
        $headers[] = new SOAPHeader('http://render.webservices.edu_sharing.org', 'signature', $signature); 
        $headers[] = new SOAPHeader('http://render.webservices.edu_sharing.org', 'signed', $signData);       
        $headers[] = new SOAPHeader('http://render.webservices.edu_sharing.org', 'locale', $Locale->getLanguageTwoLetters() . '_' . $Locale->getCountryTwoLetters());

        $client->__setSoapHeaders($headers); 
        
        if(empty($req_data['version']))
            $req_data['version'] = '-1';

         $params = array(
            "userName" => $user_name,
            "nodeId" => $req_data['obj_id'],
            "lmsId" => $req_data['app_id'],
            "courseId" => $req_data['course_id'],
            "resourceId" => $req_data['resource_id'],
            "version" => $req_data['version']
        );
        $renderInfoLMSReturn = $client->getRenderInfoLMS($params);
        
        
        /*
         * For collection refs call service again with original node id.
         * Should be handled in teh repository for consistency.
         * 
         * */
        $ref = false;
        foreach($renderInfoLMSReturn->getRenderInfoLMSReturn->properties->item as $item) {
        	if($item->key == '{http://www.campuscontent.de/model/lom/1.0}format' && $item->value == 'edu/ref') {
        		$ref = true;
        	}
        	if($item->key == '{http://www.campuscontent.de/model/1.0}original')
        		$original = $item->value;
        }
        if($ref) {
        	$params['nodeId'] = $original;
        	$renderInfoLMSReturn = $client->getRenderInfoLMS($params);
        	$req_data['obj_id'] = $original;
        }
        

    } catch (Exception $e) {
        throw new ESRender_Exception_InfoLms($e);
    }
    
    // check usage
    if ($req_data['rep_id'] != $req_data['app_id']) {
        // non-repositories MUST supply usage-info
        foreach ($Plugins as $name => $Plugin) {
            $Logger -> debug('Running plugin "' . $name . '"::postCheckPermission()');
            $Plugin -> preCheckUsage($remote_rep, $req_data['app_id'], $req_data['obj_id'], $req_data['course_id'], $req_data['resource_id'], $user_name);
        }
        if (empty($renderInfoLMSReturn -> getRenderInfoLMSReturn -> usage)) {
            throw new ESRender_Exception_UsageError('No usage-information retrieved.');
        }

        $dummy_courseId = $renderInfoLMSReturn -> getRenderInfoLMSReturn -> usage -> courseId;
        $dummy_lmsId = $renderInfoLMSReturn -> getRenderInfoLMSReturn -> usage -> lmsId;
        $dummy_resourceId = $renderInfoLMSReturn -> getRenderInfoLMSReturn -> usage -> resourceId;

        $xmlParams = simplexml_load_string($renderInfoLMSReturn -> getRenderInfoLMSReturn -> usage -> usageXmlParams);
        if (!$xmlParams) {
            throw new Exception('Error loading usageXmlParams.');
        }

        foreach ($Plugins as $name => $Plugin) {
            $Logger -> debug('Running plugin "' . $name . '"::preRetrieveObjectProperties()');
            $Plugin -> postCheckUsage($remote_rep, $req_data['app_id'], $renderInfoLMSReturn -> getRenderInfoLMSReturn -> usage, $req_data['obj_id'], $req_data['course_id'], $req_data['resource_id'], $user_name);
        }
    } else {
        $Logger -> info('No usage-informations retrieved.');
        $dummy_courseId = 0;
        $dummy_lmsId = 0;
    }

    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin "' . $name . '"::preRetrieveObjectProperties()');
        $Plugin -> preRetrieveObjectProperties($remote_rep, $req_data['app_id'], $req_data['obj_id'], $req_data['course_id'], $req_data['resource_id'], $user_name);
    }

    require_once(dirname(__FILE__) . '/../../func/classes.new/ESContentNode.php');
    $contentNode = new ESContentNode();
    $contentNode -> setProperties($renderInfoLMSReturn->getRenderInfoLMSReturn->properties->item);

    //if not set by usage set it with property value
    if($req_data['version'] < 1) {
        //set alf version
        $req_data['version'] = $contentNode -> getProperty('{http://www.campuscontent.de/model/lom/1.0}version');
        //in case that there is no initial version set es version
        if(empty($req_data['version']))
            $req_data['version'] = $contentNode -> getProperty('{http://www.alfresco.org/model/content/1.0}versionLabel');
    }

    $ESObject = new ESObject($req_data['obj_id'], $req_data['version']);
    $ESObject -> setAlfrescoNode($contentNode);
    $ESObject -> setInfoLmsData($renderInfoLMSReturn);

    if (!$ESObject -> setDataByNode()) {
        $Logger -> error('Error importing Alfresco\'s property-node.');
        throw new ESRender_Exception_Unauthorized('Could not import object-properties.');
    }

    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin "' . $name . '"::postRetrieveObjectProperties()');
        $Plugin -> postRetrieveObjectProperties($remote_rep, $req_data['app_id'], $contentNode, $req_data['course_id'], $req_data['resource_id'], $user_name);
    }

    // set partial object data
    $ObjectData = array('ESOBJECT_REP_ID' => $req_data['rep_id'], 'ESOBJECT_LMS_ID' => $req_data['app_id'], 'ESOBJECT_COURSE_ID' => $req_data['course_id'], 'ESOBJECT_RESOURCE_ID' => $req_data['resource_id'], 'ESOBJECT_VERSION' => $req_data['version'], 'ESOBJECT_CONTENT_HASH' => $renderInfoLMSReturn->getRenderInfoLMSReturn->contentHash);

    if (!$ESObject -> setData($ObjectData)) {
        $Logger -> error('Error setting instance-data.');

        throw new ESRender_Exception_Unauthorized('Could not set instance-data.');
    }

    $Logger -> info('Successfully initialized instance.');

    // stop session to allow flawless module-operation
    session_write_close();
    $sessionSavePath = session_save_path();
    // find appropriate module
    $ESObject -> setModule();

    $moduleName = $ESObject -> ESModule -> getName();
    if (empty($moduleName)) {
        //.oO no display modul for this file
        $Logger -> error('No module found');
        $Logger -> debug('Object mime-type: "' . $ESObject -> getMimeType() . '", resource-type: "' . $ESObject -> getResourceType() . '", resource-version: "' . $ESObject -> getResourceVersion() . '".');

        throw new Exception('Could not load module to render object.');
    }

    //.oO include the appropriate display module
    $Logger -> debug('Attempting to use module "' . $moduleName . '".');

    $moduleFile = realpath(dirname(__FILE__) . '/../../modules/' . $moduleName . '/mod_' . $moduleName . '.php');
    $Logger -> debug('Module-file: "' . $moduleFile . '"');
    
    if (!require_once ($moduleFile)) {
        $Logger -> error('Error including module-file.');

        throw new Exception('Error loading module.');
    }

    $Logger -> debug('Successfully included module-file "' . $moduleFile . '".');

    $_mod_class = 'mod_' . $moduleName;
    $Module = new $_mod_class($moduleName, $RenderApplication, $ESObject, $Logger, $Template);
    $Module -> setRequestingDevice($requestingDevice);

    $Logger -> info('Loaded module "' . $moduleName . '".');

    /*For moodle/scorm*/
    $user_email = $user_givenname = $user_surname = $user_name;

    $instanceParams = array(
        'rep_id' => $req_data['rep_id'],
        'app_id' => $req_data['app_id'],
        'course_id' => $req_data['course_id'],
        'object_id' => $req_data['obj_id'],
        'resource_id' => $req_data['resource_id'],
        'user_id' => $user_id,
        'user_name' => $user_name,
        'user_email' => $user_email,
        'user_givenname' => $user_givenname,
        'user_surname' => $user_surname,
        'version' => $req_data['version'],
        'width' => $req_data['width'],
        'height' => $req_data['height']
     );

    // create new object instance if not existent
    if (!$Module -> instanceExists($ESObject, $instanceParams, $renderInfoLMSReturn->getRenderInfoLMSReturn->contentHash)
        && !$Module -> instanceLocked($ESObject, $instanceParams, $renderInfoLMSReturn->getRenderInfoLMSReturn->contentHash)) {

        //ensure that instance is not created several times
        $Module -> instanceLock($ESObject, $instanceParams, $renderInfoLMSReturn->getRenderInfoLMSReturn->contentHash);
        
        foreach ($Plugins as $name => $Plugin) {
            $Logger -> debug('Running plugin "' . $name . '"::preInstanciateObject()');
            $Plugin -> preInstanciateObject();
        }

        try {
            $Logger -> info('Instance does not yet exists. Attempting to create new object-instance.');
            $parsedRepoAuthUrl = parse_url($remote_rep->prop_array['authenticationwebservice_wsdl']);
            
            $paramsCreate = array(
                'rep_id' => $req_data['rep_id'],
                'app_id' => $req_data['app_id'],
                'course_id' => $req_data['course_id'],
                'object_id' => $req_data['obj_id'],
                'resource_id' => $req_data['resource_id'],
                'user_id' => $user_id,
                'user_name' => $user_name,
                'user_email' => $user_email,
                'user_givenname' => $user_givenname,
                'user_surname' => $user_surname,
                'version' => $req_data['version'],
                'session' => $req_data['session'],
                'width' => $req_data['width'],
                'height' => $req_data['height'],
                'remoteRepConf' => $remote_rep->prop_array,
                'private_key' => $hc->prop_array['private_key'],
                'homerepoConf' => $homeRep -> prop_array,
                'renderAppId' => $hc->prop_array['appid']);
            
            if (!$Module -> createInstance($paramsCreate)) {
            
                $Logger -> error('Error creating new object-instance. Attempting to remove created object.');

                if (!$ESObject -> deleteFromDb()) {
                    $Logger -> error('Error removing object-instance "' . $ESObject -> getObjectID() . '".');
                }

                $Logger -> info('Successfully removed created object.');

                throw new Exception('Error creating instance.');
            }

            if (!$ESObject -> setData2Db()) {
                $Logger -> error('Error storing object-data in database.');

                throw new Exception('Error storing instance-data.');
            }
        } catch (Exception $e) {
            $Logger -> error('Error while creating new object-instance.');
            $Logger -> error($e);

            $ESObject -> deleteFromDb();

            throw new Exception('Error creating instance.');
        }

        foreach ($Plugins as $name => $Plugin) {
            $Logger -> debug('Running plugin "' . $name . '"::postInstanciateObject()');
            $Plugin -> postInstanciateObject();
        }

        //unlock instance
        $Module -> instanceUnlock($ESObject, $instanceParams, $renderInfoLMSReturn->getRenderInfoLMSReturn->contentHash);

    }

    $Logger -> info('Successfully fetched instance.');

    // start session to store esrender-data.
    $moduleSessionName = session_name();
    $moduleSessionId = session_id();
    $moduleSessionSavePath = session_save_path();
    session_write_close();
    session_save_path($sessionSavePath);
    session_name($ESRENDER_SESSION_NAME);
    session_id($esrenderSessionId);
    session_start();

    $Logger -> info('Preparing render-session.');

    // prepare module render data
    $_SESSION['esrender'] = array(
        'file_name' => $ESObject -> getFilename(),
        'mod_name' => $moduleName,
        // relative path to DOC_ROOT, e.g. '/esrender/modules/doc/files/'
        'mod_path' => MC_PATH . $ESObject -> ESModule -> getTmpFilepath() . DIRECTORY_SEPARATOR,
        // absolute path e.g. '/srv/www/htdocs/esrender/modules/doc/files/'
        'mod_root' => MC_ROOT_PATH . $ESObject -> ESModule -> getTmpFilepath() . DIRECTORY_SEPARATOR,
        // absolute path, e.g. '/srv/www/docs/'.$mod_name.'/'
        'src_root' => CC_RENDER_PATH . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR,
        'TOU' => $Module -> getTimesOfUsage(), // times of usage (0:forbidden, -1:unlimited)
        'check' => parse_url($ESObject -> getPathfile(), PHP_URL_PATH),
        'display_kind' => $display_kind, 
        // real module path, independent from cache
        'moduleRoot' => realpath(dirname(__FILE__) . '/../../modules/' . $moduleName),
    	'token' => $token
    );
    
    $cookie_path = '/';
    $cookie_expire = 0;
    $cookie_domain = MC_HOST;

    // re-start module-session to finish processing    
    session_write_close();

    session_save_path($moduleSessionSavePath);
    session_name($moduleSessionName);
    session_id($moduleSessionId);
    session_start();

    if (ENABLE_TRACK_OBJECT) {
        $RenderApplication -> trackObject($remote_rep -> prop_array['appid'], $req_data['app_id'], $ESObject -> getId(), $req_data['obj_id'], $ESObject -> getFilename(), $req_data['version'], $ESObject -> ESModule -> getModuleId(), $ESObject -> ESModule -> getName(), $user_id, $user_name, $req_data['course_id']);
    }

    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin "' . $name . '"::preProcessObject()');
        $Plugin -> preProcessObject();
    }

    $Logger -> info('Processing render-object.');
    if (!$Module -> process(
        $display_kind,
        array(
            'rep_id' => $req_data['rep_id'],
            'app_id' => $req_data['app_id'],
            'course_id' => $req_data['course_id'],
            'object_id' => $req_data['obj_id'],
            'resource_id' => $req_data['resource_id'],
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'user_givenname' => $user_givenname,
            'user_surname' => $user_surname,
            'tracking_id' => $TrackingId,
            'session' => $req_data['session'],
            'width' => $req_data['width'],
            'height' => $req_data['height'],
            'callback' => mc_Request::fetch('callback', 'CHAR'),
            'user_name_encr' => $req_data['username'],
            'sessionName' => $ESRENDER_SESSION_NAME,
            'sessionId' => $esrenderSessionId,
            'usernameEncrypted' => $req_data['usernameEncrypted'],
            'version' => $req_data['version'],
            'backLink' => $req_data['backLink'],
        	'token' => $token
        ),
        $Module -> instanceLocked($ESObject, $instanceParams, $renderInfoLMSReturn->getRenderInfoLMSReturn->contentHash))) {
        $Logger -> error('Error processing object "' . $data['parentNodeId'] . '".');
        throw new Exception('Error processing object.');
    }
    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin "' . $name . '"::postProcessObject()');
        $Plugin -> postProcessObject();
    }

    $Logger -> info('Shutting down.');

    exit(0);
} catch(ESRender_Exception_MissingRequestParam $exception) {
    $Logger -> error('Missing parameter "' . $exception -> getParamName() . '"');
    $Logger -> error($exception);

    header('HTTP/1.0 400 Bad Request');

    $Message = new Phools_Message_Default('Missing parameter ":name".', array(new Phools_Message_Param_String(':name', $exception -> getParamName())));

    echo $Template -> render('/error/missing_request_param', array('error' => $Message -> localize($Locale, $Translate), ));
} catch(ESRender_Exception_SslVerification $exception) {
    $Logger -> error('SSL verification error "' . $exception -> getMessage() . '"');
    $Logger -> error($exception);
    header('HTTP/1.0 400 Bad Request');
    $Message = new Phools_Message_Default($exception -> getMessage());
    echo $Template -> render('/error/ssl_verification', array('error' => $Message -> localize($Locale, $Translate), ));
} catch(ESRender_Exception_InvalidRequestParam $exception) {
    $Logger -> error('Invalid parameter "' . $exception -> getParamName() . '"');
    $Logger -> error($exception);

    header('HTTP/1.0 400 Bad Request');

    $Message = new Phools_Message_Default('Invalid parameter ":name".', array(new Phools_Message_Param_String(':name', $exception -> getParamName())));

    echo $Template -> render('/error/invalid_request_param', array('error' => $Message -> localize($Locale, $Translate), ));
} catch(ESRender_Exception_HomeConfigNotLoaded $exception) {
    $Logger -> error('Error loading home-configuration.');
    $Logger -> error($exception);

    header('HTTP/1.0 500 Internal Server Error');

    $Message = new Phools_Message_Default('Error loading configuration.');

    echo $Template -> render('/error/load_home_config', array('error' => $Message -> localize($Locale, $Translate), ));
} catch(ESRender_Exception_AppConfigNotLoaded $exception) {
    $Logger -> error('Error loading config for application "' . $exception -> getAppId() . '".');
    $Logger -> error($exception);

    header('HTTP/1.0 500 Internal Server Error');

    $Message = new Phools_Message_Default('Error loading config for application ":app_id".', array(new Phools_Message_Param_String(':app_id', $exception -> getAppId())));

    echo $Template -> render('/error/load_app_config', array('error' => $Message -> localize($Locale, $Translate), ));
} catch(ESRender_Exception_NetworkError $exception) {
    $Logger -> error('A network error occurred.');
    $Logger -> error($exception);

    header('HTTP/1.0 500 Internal Server Error');

    $Message = new Phools_Message_Default('A network error occurred.');

    echo $Template -> render('/error/network_error', array('error' => $Message -> localize($Locale, $Translate), ));
} catch(ESRender_Exception_Unauthorized $exception) {
    $Logger -> error('You\'re not authorized to access this resource.');
    $Logger -> error($exception);

    header('HTTP/1.0 401 Unauthorized');

    $Message = new Phools_Message_Default('You\'re not authorized to access this resource.');

    echo $Template -> render('/error/unauthorized', array('error' => $Message -> localize($Locale, $Translate), ));
} catch(ESRender_Exception_ConfigParamInvalidOrMissing $exception) {
    $Logger -> error('Missing or wrong config parameter "' . $exception -> getParam() . '".');
    $Logger -> error($exception);

    header('HTTP/1.0 500 Internal Server Error');

    $Message = new Phools_Message_Default('The config param ":param" for app ":app" is invalid or missing. Please contact your system-administrator.');

    echo $Template -> render('/error/config_param_invalid', array('error' => $Message -> localize($Locale, $Translate), ));
} catch(ESRender_Exception_UsageError $exception) {
    $Logger -> error($exception -> getMessage());
    $Logger -> debug($exception);

    header('HTTP/1.0 500 Internal Server Error');

    $Message = new Phools_Message_Default($exception -> getMessage());

    echo $Template -> render('/error/default', array('error' => $Message -> localize($Locale, $Translate), ));
    
} catch(ESRender_Exception_InfoLms $exception) {
    $Logger -> error($exception -> getMessage());
    $Logger -> debug($exception);

    header('HTTP/1.0 500 Internal Server Error');

    if(strpos(strtoupper($exception -> getMessage()), 'NODE_DOES_NOT_EXISTS') !== false)
        $message = 'Object does not exist in repository';
    else
        $message = 'Error fetching object properties';

    $Message = new Phools_Message_Default($message);

    echo $Template -> render('/error/infoLms', array('error' => $Message -> localize($Locale, $Translate), ));

    
} catch(Exception $exception) {
    $Logger -> error('An internal server error occurred.');
    $Logger -> debug($exception);

    header('HTTP/1.0 500 Internal Server Error');

    $Message = new Phools_Message_Default('An internal server error occurred.');

    echo $Template -> render('/error/default', array('error' => $Message -> localize($Locale, $Translate), ));
}

$Logger -> info('Shutting down.');

exit(255);
