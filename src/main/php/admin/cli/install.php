<?php
define('CLI_MODE', true);
//error_reporting(E_ERROR);

function recurse_copy($src,$dst) {
    $dir = opendir($src);
    if (!mkdir($dst) && !is_dir($dst)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $dst));
    }
    while( $dir && ($file = readdir($dir) ) !== false) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . DIRECTORY_SEPARATOR . $file) ) {
                recurse_copy($src . DIRECTORY_SEPARATOR . $file,$dst . DIRECTORY_SEPARATOR . $file);
            }
            else {
                copy($src . DIRECTORY_SEPARATOR . $file,$dst . DIRECTORY_SEPARATOR . $file);
                echo '[OK] Copy ' . $src . DIRECTORY_SEPARATOR . $file . PHP_EOL;
            }
        }
    }
    closedir($dir);
}

function replaceValuesInFile($file, $needle, $replace) {
    $str = file_get_contents($file);
    $str = str_replace($needle, $replace, $str);
    file_put_contents($file, $str);
    echo '[OK] Write ' . $file . PHP_EOL;
}

function checkExtension($extension) {
    if(!extension_loaded($extension))
        echo '[WARNING] Extension ' . $extension . ' not enabled' . PHP_EOL;
    else
        echo '[OK] Extension ' . $extension . ' enabled' . PHP_EOL;
}

function checkModule($module) {
    if ( ! function_exists('apache_get_modules') ) {
        return true;
    }
    $installedModules = apache_get_modules();
    if (!in_array($module, $installedModules))
        echo '[WARNING] Module ' . $module . ' not enabled' . PHP_EOL;
    else
        echo '[OK] Module ' . $module . ' enabled' . PHP_EOL;
}

$options = getopt("c:");
if (empty($options['c'])) {
    echo 'To run installation run this script and provide the path to the configuration ini file as argument c.' . PHP_EOL
    . 'Example: php install.php -c "config.ini"';
} else {

    $config = parse_ini_file ($options['c']);
    if(!$config) {
        echo  $options['c'] . ' could not be read.';
        exit();
    }
    echo '[OK] Load config.ini' . PHP_EOL;

    $confTemplateDir = $config['application_root'] . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . '_tmpl' . DIRECTORY_SEPARATOR . 'conf';
    $confDir = $config['application_root'] . DIRECTORY_SEPARATOR . 'conf';
    recurse_copy($confTemplateDir, $confDir);

    //application
    $file = $confDir . DIRECTORY_SEPARATOR . 'system.conf.php';
    $needle = array('[[[TOKEN_URL]]]', '[[[TOKEN_DOCROOT]]]', '[[[TOKEN_DATA_DIR]]]');
    $replace = array($config['application_url_client'], $config['application_root'], $config['application_cache']);
    replaceValuesInFile($file, $needle, $replace);

    $file = $confDir . DIRECTORY_SEPARATOR . 'de.metaventis.esrender.log4php.cachecleaner.properties';
    replaceValuesInFile($file, '[[[TOKEN_BASE_DIR]]]', $config['application_root'] . '/');

    $videoConf = $config['application_root'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . 'config.php';
    $copied = copy($confTemplateDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . 'config.php', $videoConf);
    if($copied)
        echo '[OK] Copy ' . $confTemplateDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'video' . DIRECTORY_SEPARATOR . 'config.php' . PHP_EOL;
    replaceValuesInFile($videoConf, '[[[TOKEN_FFMPEG_EXEC]]]', $config['application_ffmpeg']);

    //database
    $file = $confDir . DIRECTORY_SEPARATOR . 'db.conf.php';
    $needle = array('[[[TOKEN_DBDRIVER]]]', '[[[TOKEN_DBHOST]]]', '[[[TOKEN_DBPORT]]]', '[[[TOKEN_DBNAME]]]', '[[[TOKEN_DBUSER]]]', '[[[TOKEN_DBPASS]]]', '[[[TOKEN_PREPARE]]]');
    $replace = array($config['db_driver'], $config['db_host'], $config['db_port'], $config['db_name'], $config['db_user'], $config['db_password'], $config['db_prepare']);
    replaceValuesInFile($file, $needle, $replace);

    //database
    require_once $config['application_root'] . DIRECTORY_SEPARATOR . 'func' . DIRECTORY_SEPARATOR . 'classes.new' . DIRECTORY_SEPARATOR . 'RsPDO.php';
    require_once $config['application_root'] . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . '_classes' . DIRECTORY_SEPARATOR . 'SysMsg.php';
    require_once $config['application_root'] . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . '_classes' . DIRECTORY_SEPARATOR . 'Step.php';
    require_once $config['application_root'] . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'initdb.php';
    define('INST_PATH_TMPL', $config['application_root'] . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . '_tmpl' . DIRECTORY_SEPARATOR);
    $dbInstaller = new initdb();
    $dbInstaller -> setPdo(RsPDO::getInstance());
    $dbInstaller -> setDbDrvr($config['db_driver']);
    $dbInstaller -> setDbPrepare($config['db_prepare']);
    $dbInstaller -> all_tables = $dbInstaller -> getAllTables();
    $dbInstaller -> createTables();
    $dbInstaller -> loadTableContent();

    //home app
    require_once($config['application_root'] . DIRECTORY_SEPARATOR . 'func' . DIRECTORY_SEPARATOR . 'classes.new' . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR . 'AppPropertyHelper.php');
    $appPropertyHelper = new AppPropertyHelper();
    $sslKeypair = @$appPropertyHelper -> getSslKeypair();
    if(empty($sslKeypair['privateKey'])) {
        echo '[WARNING] Could not generate SSL keys. Please insert key pair manually to homeApplication.properties.xml' . PHP_EOL;
        $sslKeypair = array();
        $sslKeypair['privateKey'] = $sslKeypair['publicKey'] = '';
    }

    $parsedUrl = parse_url($config['application_url_repository']);
    $needle = array('[[[TOKEN_HOST]]]', '[[[TOKEN_PORT]]]', '[[[TOKEN_SCHEME]]]', '[[[TOKEN_PRIVATE_KEY]]]', '[[[TOKEN_PUBLIC_KEY]]]');
    $replace = array($parsedUrl['host'], $parsedUrl['port'], $parsedUrl['scheme'], $sslKeypair['privateKey'], $sslKeypair['publicKey']);
    replaceValuesInFile($confDir . DIRECTORY_SEPARATOR . 'esmain' . DIRECTORY_SEPARATOR . 'homeApplication.properties.xml', $needle, $replace);

    //repo
    require_once $config['application_root'] . DIRECTORY_SEPARATOR .'install' . DIRECTORY_SEPARATOR . '_classes' . DIRECTORY_SEPARATOR . 'RemoteAppPropertyHandler.php';
    require_once $config['application_root'] . DIRECTORY_SEPARATOR .'install' . DIRECTORY_SEPARATOR . 'execute.php';
    define('MC_BASE_DIR', $config['application_root'] . DIRECTORY_SEPARATOR);
    $execute = new execute();
    $execute -> setRepoUrl($config['repository_url']);
    $remoteAppPropertyHandler = new RemoteAppPropertyHandler($execute);
    $remoteAppPropertyHandler -> setHomeRep();

    //check required PHP extensions
    foreach(array('session', 'dom', 'soap', 'sockets', 'iconv', 'gd', 'mbstring', 'fileinfo', 'openssl', 'zip', 'curl', 'pdo') as $extension) {
        checkExtension($extension);
    }

    //check required Apache modules
    foreach(array('mod_rewrite', 'mod_headers') as $module) {
        checkModule($module);
    }

    //cache folder
    if(!file_exists($config['application_cache'])) {
        @$mkDirSuc = mkdir($config['application_cache']);
        if($mkDirSuc)
            echo '[OK] Create cache folder ' . $config['application_cache'];
        else
            echo '[WARNING] Could not create cache folder ' . $config['application_cache'];
    } else {
        echo '[OK] Cache folder exists';
    }

    //save cache folder
    if(!empty($config['application_cache_save'])) {
        if(!file_exists($config['application_cache_save'])) {
            @$mkDirSuc = mkdir($config['application_cache_save']);
            if($mkDirSuc)
                echo '[OK] Create save cache folder ' . $config['application_cache_save'];
            else
                echo '[WARNING] Could not create save cache folder ' . $config['application_cache_save'];
        } else {
            echo '[OK] Save cache folder exists';
        }
    }

    echo PHP_EOL . PHP_EOL . '### Check CLI output for errors or warnings and adjust owner and permissions ###';

    exit(0);


}
