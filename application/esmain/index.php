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

    $data = json_decode(file_get_contents('php://input'));

    // init translate
    global $Translate, $LanguageCode;
    $Translate = new Phools_Translate_Array();

    // LANGUAGE
    $LanguageCode = mc_Request::fetch('language', 'CHAR', 'en');
    $Locale = new Phools_Locale_Default(strtolower($LanguageCode), strtoupper($LanguageCode), ',', '.');
    require_once (dirname(__FILE__) . '/../../locale/esmain/'.strtoupper($LanguageCode).'/lang.common.php');

    // init templating
    $TemplateDirectory = dirname(__FILE__) . '/../../theme';
    $Template = new Phools_Template_Script($TemplateDirectory);
    $Template -> setTheme('default') -> setLocale($Locale) -> setTranslate($Translate);
    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin ' . get_class($Plugin) . '::setTemplate()');
        $Plugin -> setTemplate($Template);
    }
    
    // init DATABASE INTERFACE
    $pdo = new RsPDO();
    
    // init APPLICATION
    $RenderApplication = new ESRender_Application($pdo);
    $RenderApplication -> setLogger($Logger);


    // Display parameters
    Config::set('showMetadata', true);
    if(mc_Request::fetch('showMetadata', 'CHAR') === 'false')
        Config::set('showMetadata', false);

    Config::set('showDownloadButton', true);
    if(mc_Request::fetch('showDownloadButton', 'CHAR') === 'false')
        Config::set('showDownloadButton', false);

    Config::set('showDownloadAdvice', true);
    if(mc_Request::fetch('showDownloadAdvice', 'CHAR') === 'false')
        Config::set('showDownloadAdvice', false);

    Config::set('forcePreview', false);
    if(mc_Request::fetch('forcePreview', 'CHAR') === 'true')
        Config::set('forcePreview', true);

    Config::set('hasContentLicense', false);
    if(in_array('ReadAll', $data -> node -> access))
        Config::set('hasContentLicense', true);

    $CurrentDirectoryName = basename(dirname(__FILE__));
    $application = new ESApp();
    $application -> getApp($CurrentDirectoryName);

    $Logger -> debug('Initialized application.');

    $homeConfig = $application -> getHomeConf();
    if (!$homeConfig) {
        $Logger -> error('Error loading home-configuration.');
        throw new ESRender_Exception_HomeConfigNotLoaded();
    }
    Config::set('homeConfig', $homeConfig);
    $Logger -> debug('Successfully loaded home-configuration.');

    if($data -> node -> remote) {
        Config::set('remote', $data -> node -> remote);
    }

    // load repository-config
    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin ' . get_class($Plugin) . '::preLoadRepository()');
        $Plugin -> preLoadRepository($data->node->ref->repo, mc_Request::fetch('app_id', 'CHAR'), $data->node->ref->id, mc_Request::fetch('course_id', 'CHAR'), mc_Request::fetch('resource_id', 'CHAR'), $data->user->authorityName);
    }

    $Logger -> debug('Successfully loaded repository by id "' . $data->node->ref->repo . '".');

    $homeRepId = $homeConfig -> prop_array['homerepid'];
    $homeRep = $application -> getAppByID($homeRepId);
    if (!$homeRep) {
        $Logger -> error('Error loading application by requested repository-id "' . $homeRepId . '".');
        throw new ESRender_Exception_AppConfigNotLoaded($homeRepId);
    }

    $homeRep->url = str_replace('/services/authbyapp', '', $homeRep->prop_array['authenticationwebservice']);
    Config::set('homeRepository', $homeRep);

    $Logger -> debug('Successfully loaded home repository by id "' . $homeRepId . '".');

    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin ' . get_class($Plugin) . '::postLoadRepository()');
        $Plugin -> postLoadRepository($remote_rep, mc_Request::fetch('app_id', 'CHAR'), $data->node->ref->id, mc_Request::fetch('course_id', 'CHAR'), mc_Request::fetch('resource_id', 'CHAR'), $data->user->authorityName);
    }

    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin ' . get_class($Plugin) . '::preSslVerification()');
        $Plugin -> preSslVerification($remote_rep, mc_Request::fetch('app_id', 'CHAR'), $data->node->ref->id, mc_Request::fetch('course_id', 'CHAR'), mc_Request::fetch('resource_id', 'CHAR'), $data->user->authorityName, $homeRep);
    }    

    $skipSslVerification = false; /////check
//check
    if(!empty(mc_Request::fetch('token', 'CHAR', ''))) {
    	if(mc_Request::fetch('token', 'CHAR', '') == $_SESSION['esrender']['token'] || empty($_SESSION['esrender']['token'])) {
        	$skipSslVerification = true;
    	} else {
    		$Logger->error('Token not valid!');
    	}
    }

    Config::set('token', md5(uniqid()));

    if(!$skipSslVerification) {

        $ts = mc_Request::fetch('ts', 'CHAR');
        if (empty($ts)) {
            $Logger -> error('Missing request-param "timestamp".');
            throw new ESRender_Exception_MissingRequestParam('timestamp');
        }

        if(empty($_GET['sig'])) {
            $Logger -> error('Missing request-param "sig".');
            throw new ESRender_Exception_MissingRequestParam('sig');
        }

        try {
            $pubkeyid = openssl_get_publickey($homeRep -> prop_array['public_key']);
            $signature = rawurldecode($_GET['sig']);
            $signature = base64_decode($signature);
            $ok = openssl_verify($data->node->ref->repo . $data->node->ref->id  . $ts, $signature, $pubkeyid);
        } catch (Exception $e) {
            throw new ESRender_Exception_SslVerification('Error checking signature');
        }

        if ($ok != 1) {
            throw new ESRender_Exception_SslVerification('SSL signature check failed');
        }

        $now = microtime(true) * 1000;

        $message_send_offset_ms = 10000;
        if(isset($homeRep -> prop_array['message_send_offset_ms']))
            $message_send_offset_ms = $homeRep -> prop_array['message_send_offset_ms'];
        if($now + $message_send_offset_ms < $ts) {
            throw new ESRender_Exception_SslVerification('Timestamp sent bigger than current timestamp');
        }

        $message_offset_ms = 10000;
        if(isset($homeRep -> prop_array['message_offset_ms']))
            $message_offset_ms = $homeRep -> prop_array['message_offset_ms'];
        if($now - $ts > $message_offset_ms) {
            throw new ESRender_Exception_SslVerification('Token expired');
        }

    }

    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin ' . get_class($Plugin) . '::postSslVerification()');
        $Plugin -> postSslVerification($homeRep, mc_Request::fetch('app_id', 'CHAR'), $data->node->ref->id, mc_Request::fetch('course_id', 'CHAR'), mc_Request::fetch('resource_id', 'CHAR'), $data->user->authorityName, $homeRep);
    }

    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin ' . get_class($Plugin) . '::preRetrieveObjectProperties()');
        $Plugin -> preRetrieveObjectProperties($homeRep, mc_Request::fetch('app_id', 'CHAR'), $data->node->ref->id, mc_Request::fetch('course_id', 'CHAR'), mc_Request::fetch('resource_id', 'CHAR'), $data->user->authorityName);
    }

    require_once(dirname(__FILE__) . '/../../func/classes.new/ESContentNode.php');
    $contentNode = new ESContentNode($data);
    $ESObject = new ESObject($contentNode);

    //version
    if($contentNode->getNode()->isDirectory || $contentNode->getNodeProperty('ccm:remoterepositorytype'))
        $contentNode->getNode()->content->version = '';

    $ESObject = new ESObject($contentNode);

    //@todo
    $eduscopename = $contentNode -> getNodeProperty('ccm:eduscopename');
    if($eduscopename === 'safe') {
        if(!empty($CC_RENDER_PATH_SAFE))
            $CC_RENDER_PATH = $CC_RENDER_PATH_SAFE;
    }

    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin ' . get_class($Plugin) . '::postRetrieveObjectProperties()');
        $Plugin -> postRetrieveObjectProperties($remote_rep, mc_Request::fetch('app_id', 'CHAR'), $contentNode, mc_Request::fetch('course_id', 'CHAR'), mc_Request::fetch('resource_id', 'CHAR'), $data->user->authorityName);
    }

    $Logger -> info('Successfully initialized instance.');

    $originalDeleted = $ESObject -> getContentNode() -> getNodeProperty('{virtualproperty}originaldeleted');
    if(!empty($originalDeleted)) {
        $ESObject -> renderOriginalDeleted($data->dummy, mc_Request::fetch('display', 'CHAR', 'window'), $Template);
    }

    // find appropriate module
    $ESObject -> setModule();
    $moduleName = $ESObject -> module -> getName();
    if (empty($moduleName)) {
        $Logger -> error('No module found');
        $Logger -> debug('Object mime-type: "' . $ESObject -> getMimeType() . '", resource-type: "' . $ESObject -> getResourceType() . '", resource-version: "' . $data->node->content->version . '".');
        throw new Exception('Could not load module to render object.');
    }

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

    $Logger -> info('Loaded module "' . $moduleName . '".');

    // create new object instance if not existent
    if (!$Module -> instanceExists($ESObject)
        && !$Module -> instanceLocked($ESObject)) {

        //ensure that instance is not created several times
        $Module -> instanceLock($ESObject);
        
        foreach ($Plugins as $name => $Plugin) {
            $Logger -> debug('Running plugin ' . get_class($Plugin) . '::preInstanciateObject()');
            $Plugin -> preInstanciateObject();
        }

        try {
            $Logger -> info('Instance does not yet exists. Attempting to create new object-instance.');

            if (!$Module -> createInstance($ESObject)) {
                $Logger -> error('Error creating new object-instance. Attempting to remove created object.');
                if (!$ESObject -> deleteFromDb()) {
                    $Logger -> error('Error removing object-instance "' . $ESObject -> getObjectID() . '".');
                }
                $Module -> instanceUnlock($ESObject);
                $Logger -> info('Successfully removed created object.');
                throw new Exception('Error creating instance.');
            }

            if (!$ESObject -> setData2Db()) {
                $Module -> instanceUnlock($ESObject);
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
            $Logger -> debug('Running plugin ' . get_class($Plugin) . '::postInstanciateObject()');
            $Plugin -> postInstanciateObject();
        }

        $Module -> instanceUnlock($ESObject);
    }

    $ESObject ->update();

    $Logger -> info('Successfully fetched instance.');

    $Logger -> info('Preparing render-session.');

    // prepare module render data
    $_SESSION['esrender'] = array(
        'file_name' => $ESObject -> getFilename(),
        'mod_name' => $moduleName,// relative path to DOC_ROOT, e.g. '/esrender/modules/doc/files/'
        'mod_path' => MC_PATH . $ESObject -> module -> getTmpFilepath() . DIRECTORY_SEPARATOR,// absolute path e.g. '/srv/www/htdocs/esrender/modules/doc/files/'
        'mod_root' => MC_ROOT_PATH . $ESObject -> module -> getTmpFilepath() . DIRECTORY_SEPARATOR,// absolute path, e.g. '/srv/www/docs/'.$mod_name.'/'
        'src_root' => $CC_RENDER_PATH . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR,
        'TOU' => $Module -> getTimesOfUsage(),// times of usage (0:forbidden, -1:unlimited)
        'check' => parse_url($ESObject -> getPathfile(), PHP_URL_PATH),
        'display_kind' => mc_Request::fetch('display', 'CHAR', 'window'),// real module path, independent from cache
        'moduleRoot' => realpath(dirname(__FILE__) . '/../../modules/' . $moduleName),
    	'token' => Config::get('token')
    );

    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin ' . get_class($Plugin) . '::preProcessObject()');
        $Plugin -> preProcessObject();
    }

    $Logger -> info('Processing render-object.');
    if (!$Module -> process(mc_Request::fetch('display', 'CHAR', 'window'), $ESObject, $Module -> instanceLocked($ESObject))) {
        $Logger -> error('Error processing object "' . $data->node->ref->id . '".');
        throw new Exception('Error processing object.');
    }
    foreach ($Plugins as $name => $Plugin) {
        $Logger -> debug('Running plugin ' . get_class($Plugin) . '::postProcessObject()');
        $Plugin -> postProcessObject();
    }

    if (ENABLE_TRACK_OBJECT) {
        //filter locked Objects and inline requests that result from dynamic view
        if(!Config::get('locked') && !(($remote_rep -> prop_array['appid'] == mc_Request::fetch('app_id', 'CHAR')) && mc_Request::fetch('display', 'CHAR', 'window') == 'inline')) {
            foreach ($Plugins as $name => $Plugin) {
                $Logger -> debug('Running plugin ' . get_class($Plugin) . '::postInstanciateObject()');
                $Plugin -> preTrackObject(array('user_id'=>$user_id, 'view_type' => mc_Request::fetch('display', 'CHAR', 'window'), 'object_id' => $data->node->ref->id));
            }
            $RenderApplication -> trackObject($remote_rep -> prop_array['appid'], mc_Request::fetch('app_id', 'CHAR'), $ESObject -> getId(), $data->node->ref->id, $ESObject -> getFilename(), $data->node->content->version, $ESObject -> module -> getModuleId(), $ESObject -> module -> getName(), $user_id, $data->user->authorityName, mc_Request::fetch('course_id', 'CHAR'));
        }
    }

    $Logger -> info('Shutting down.');

    exit(0);

} catch(ESRender_Exception_MissingRequestParam $exception) {
    $Logger -> error('Missing parameter "' . $exception -> getParamName() . '"');
    $Logger -> error($exception);
    $Message = new Phools_Message_Default('Missing parameter ":name".', array(new Phools_Message_Param_String(':name', $exception -> getParamName())));
    echo $Template -> render('/error/default', array('technicalDetail' => $Message -> localize($Locale, $Translate), 'i18nName' => 'invalid_parameters'));
} catch(ESRender_Exception_SslVerification $exception) {
    $Logger -> error('SSL verification error "' . $exception -> getMessage() . '"');
    $Logger -> error($exception);
    $Message = new Phools_Message_Default($exception -> getMessage());
    echo $Template -> render('/error/default', array('technicalDetail' => $Message -> localize($Locale, $Translate), 'i18nName' => 'encryption'));
} catch(ESRender_Exception_InvalidRequestParam $exception) {
    $Logger -> error('Invalid parameter "' . $exception -> getParamName() . '"');
    $Logger -> error($exception);
    $Message = new Phools_Message_Default('Invalid parameter ":technicalDetail".', array(new Phools_Message_Param_String(':name', $exception -> getParamName())));
    echo $Template -> render('/error/default', array('error' => $Message -> localize($Locale, $Translate), 'i18nName' => 'invalid_parameters'));
} catch(ESRender_Exception_HomeConfigNotLoaded $exception) {
    $Logger -> error('Error loading home-configuration.');
    $Logger -> error($exception);
    $Message = new Phools_Message_Default('Error loading configuration.');
    echo $Template -> render('/error/default', array('technicalDetail' => $Message -> localize($Locale, $Translate), 'i18nName' => 'internal'));
} catch(ESRender_Exception_AppConfigNotLoaded $exception) {
    $Logger -> error('Error loading config for application "' . $exception -> getAppId() . '".');
    $Logger -> error($exception);
    $Message = new Phools_Message_Default('Error loading config for application ":app_id".', array(new Phools_Message_Param_String(':app_id', $exception -> getAppId())));
    echo $Template -> render('/error/default', array('technicalDetail' => $Message -> localize($Locale, $Translate), 'i18nName' => 'internal'));
} catch(ESRender_Exception_ConfigParamInvalidOrMissing $exception) {
    $Logger -> error('Missing or wrong config parameter "' . $exception -> getParam() . '".');
    $Logger -> error($exception);
    $Message = new Phools_Message_Default('The config param ":param" for app ":app" is invalid or missing. Please contact your system-administrator.');
    echo $Template -> render('/error/default', array('technicalDetail' => $Message -> localize($Locale, $Translate), 'i18nName' => 'internal'));
} catch(ESRender_Exception_Omega $exception) {
    $Logger -> error($exception -> getMessage());
    $Logger -> debug($exception);
    $MessageDefault = new Phools_Message_Default('Omega plugin error');
    $Message = new Phools_Message_Default($exception -> getMessage());
    $code = '';
    if($exception -> getCode() != 0)
        $code = $exception -> getCode();
    echo $Template -> render('/error/default', array('i18nName' => 'internal', 'technicalDetail' => $MessageDefault -> localize($Locale, $Translate) . ' - ' . $Message -> localize($Locale, $Translate) . ' ' .  $code));
} catch(Exception $exception) {
    $Logger -> error('An internal server error occurred.');
    $Logger -> debug($exception);
    $Message = new Phools_Message_Default($e->getMessage());
    echo $Template -> render('/error/default', array('technicalDetail' => $Message -> localize($Locale, $Translate), 'i18nName' => 'internal'));
}

$Logger -> info('Shutting down.');

exit(255);
