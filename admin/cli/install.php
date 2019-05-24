<?php
echo '                      
    %%%%%%%%%%%%%                     
   %%%%%%%%%%%%%%%                    
  %%%%%%%%%%%%%%%%%                   
 %%%%%%%%%%%%%%%%%%%                  
 %%%%%%%%%%%%%%%%%%%         
  #%%%%%%%%%%%%%%%%    ///////////   
   %%%%%%%%%%%%%%%   ///////////////   
    %%%%%%%%%%%%   /////////////////  
                   ///////////////////
                   ///////////////////
                    /////////////////  
    *************    ///////////////   
   ****************   ////////////    
  ******************        
 ********************                  
 *******************                   
  *****************                    
    **************   ';

echo PHP_EOL;

//echo 'Do you want to install the rendering service with the values specified in config.ini?'.PHP_EOL.'If yes type "yes", "yeah" or "y"!'.PHP_EOL;
//$handle = fopen("php://stdin", "r");
//$line = fgets($handle);

function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function replaceValues($file, $needle, $replace) {
    $str=file_get_contents($file);
    $str=str_replace($needle, $replace, $str);
    file_put_contents($file, $str);
}

if (1 == 2 &&/*test*/ strtolower(trim($line)) != 'yes' && strtolower(trim($line)) != 'y' && strtolower(trim($line) != 'yeah')) {
    echo 'OK, not.';
} else {
    $config = parse_ini_file ('config.ini');
    if(!$config) {
        echo 'config.ini could not nbe read.';
    }
    $confTemplateDir = $config['application_document_root'] . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . '_tmpl' . DIRECTORY_SEPARATOR . 'conf';
    $confDir = $config['application_document_root'] . DIRECTORY_SEPARATOR . 'conf';

    recurse_copy($confTemplateDir, $confDir);

    //database
    $file = $confDir . DIRECTORY_SEPARATOR . 'db.conf.php';
    $needle = array('[[[TOKEN_DBDRIVER]]]', '[[[TOKEN_DBHOST]]]', '[[[TOKEN_DBPORT]]]', '[[[TOKEN_DBNAME]]]', '[[[TOKEN_DBUSER]]]', '[[[TOKEN_DBPASS]]]');
    $replace = array($conf['db_driver'], $conf['db_host'], $conf['db_port'], $conf['db_name'], $conf['db_user'], $conf['db_password']);
    replaceValuesInFile($file, $needle, $replace);


}


