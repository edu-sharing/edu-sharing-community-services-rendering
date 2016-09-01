<?php
/*
* $McLicense$
*
* $Id$
*
*/


class TemplateFile
{

    private $ERROR = false;

    function __construct()
    {
        return true;
    } // end constructor



    private function getArrTokenReplace($Obj)
    {
    // set token values and load file token map
        $TOKEN_DBHOST = $Obj->getDbHost();
        $TOKEN_DBPORT = $Obj->getDbPort();
        $TOKEN_DBUSER = $Obj->getDbUser();
        $TOKEN_DBPASS = $Obj->getDbPass();
        $TOKEN_DBNAME = $Obj->getDbName();
        $TOKEN_DBDRIVER = $Obj -> getDbDrvr();
        $TOKEN_URL = $Obj->getUrl();
        $TOKEN_BASE_DIR = $Obj->getBaseDir();
        $TOKEN_DEFAULT_LANG = $Obj->getLangId();
        $TOKEN_REPO_URL    = $Obj->getRepoUrl();
        $TOKEN_REPO_HOST   = $Obj->getRepoHost();
        $TOKEN_REPO_PORT   = $Obj->getRepoPort();
        
        $TOKEN_FFMPEG_EXEC = '';
        
        $parsedUrl = parse_url($Obj->getUrl());
        
        $TOKEN_HOST = $parsedUrl['host'];
        $TOKEN_PORT = $parsedUrl['port'];
        $TOKEN_SCHEME = $parsedUrl['scheme'];

        $TOKEN_REPO_SCHEME = $Obj->getRepoScheme();
        $TOKEN_DATA_DIR    = rtrim($Obj->getDataDir(), '\\');
        
        require_once(dirname(__FILE__) . '/../../func/classes.new/Helper/AppPropertyHelper.php');
        $appPropertyHelper = new AppPropertyHelper();
        $sslKeypair = $appPropertyHelper -> getSslKeypair();
        $TOKEN_PRIVATE_KEY = $sslKeypair['privateKey'];
        if(empty($TOKEN_PRIVATE_KEY))
            SysMsg::showWarning(install_warning_ssl_keys);
        $TOKEN_PUBLIC_KEY = $sslKeypair['publicKey'];

     
        include(INST_PATH_INC.'token_maps.php');
        $replace = $arrToken2Values;

        return $replace;
    }



    function writeTplReplace($Obj)
    {
        $replace = $this->getArrTokenReplace($Obj);
        $templateFiles = array(
            // needs system config token
            'db.conf.php'      => 'conf'.DIRECTORY_SEPARATOR.'db.conf.php',
            'system.conf.php'  => 'conf'.DIRECTORY_SEPARATOR.'system.conf.php',
            'esmain/ccapp-registry.properties.xml'  => 'conf'.DIRECTORY_SEPARATOR.'esmain'.DIRECTORY_SEPARATOR.'ccapp-registry.properties.xml',
            'esmain/homeApplication.properties.xml' => 'conf'.DIRECTORY_SEPARATOR.'esmain'.DIRECTORY_SEPARATOR.'homeApplication.properties.xml',

            // logger-configuration
            'de.metaventis.esrender.log4php.properties' => 'conf'.DIRECTORY_SEPARATOR.'de.metaventis.esrender.log4php.properties',

            // module-config
            'modules/audio/config.php' => 'modules/audio/config.php',
            'modules/video/config.php' => 'modules/video/config.php',
            'modules/picture/config.php' => 'modules/picture/config.php',
        );

        $tmplDir = MC_BASE_DIR.'/install/_tmpl/conf'.DIRECTORY_SEPARATOR;

        foreach ($templateFiles as $templateName => $fileName)
        {
            $file = MC_BASE_DIR . $fileName;
            $bakFile  = $file . '.' . time() . '.BAK';
            $tmplFile = $tmplDir . $templateName;

            if (file_exists($file) ) {
                copy($file, $bakFile);
                chmod($bakFile, 0000);
            }

            $content = file_get_contents($tmplFile);

            if ($handle = fopen($file, 'w') )
            {
                $content = $this->replace($content, $replace);
                fwrite($handle, $content);
                fclose($handle);
                $Obj->info('writing file '.basename($file));
            } // end if

        } // end foreach


        // check for configuration restriction
        if (getenv('safe_mode') == true) {
            $this->handleSafeMode(MC_BASE_DIR);
        }
        else if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            $this->handleSafeMode(MC_BASE_DIR);
        }

        clearstatcache();

        return true;
    } // end method writeTplReplace



    function hasError()
    {
        return ($this->ERROR ? true : false);
    }



    function getError()
    {
        return $this->ERROR;
    }



    function copyTplDirectories($obj)
    {
        $replace = $this->getArrTokenReplace($obj);

        $handle = opendir(INST_PATH_TMPL);
      if ( ! $handle ) {
            SysMsg::showError(sprintf(install_msg_cannot_open_path, INST_PATH_TMPL));
        return false;
        }

       $dirs = array();
      while ( ($file = readdir($handle)) !== false)
      {
            if ( is_file(INST_PATH_TMPL . $file) ) {
                continue;
            }

            switch($file)
            {
                case "." :
            case ".." :
            case ".svn" :
            case "sql" :
                    // skip directories if they are local, parent, from CVS or for sql
                continue 2;

                default :
                    $tmpl = INST_PATH_TMPL . $file . DIRECTORY_SEPARATOR;
                    $dir  = MC_BASE_DIR    . $file . DIRECTORY_SEPARATOR;

                    if ( $this->rcopy($tmpl, $dir, $replace) )
                    {
                        $dirs[] = '"' . basename($dir) . '"';
                    }
                    break;
            } // end switch

        } // end while

        
        $obj->info(sprintf(install_msg_directory_template_copied, implode(', ', $dirs)));
        
        return true;
    }



    function rcopy($from_path, $to_path, $replace = array())
    {
        if ($this->createDirectory($to_path) == false)
        {
            return false;
        }

        $handle = opendir($from_path);
      if ( ! $handle ) {
            SysMsg::showError('can not open path "'.$from_path.'"');
        return false;
        }

        $pattern = array_keys($replace);

      while ( ($file = readdir($handle)) !== false)
      {
        if ( ($file == ".") || ($file == "..")  || ($file == ".svn") )
        {
                // local, parent or svn-directory
            continue;
        }

            $src  = $from_path.$file;
            $dest = $to_path.$file;

        if (is_dir($src) )
        {
            if ($this->rcopy($src.DIRECTORY_SEPARATOR, $dest.DIRECTORY_SEPARATOR, $replace))
            {
                    continue;
            }
            return false;
        }

        if (is_file($src) )
        {
            if (copy($src, $dest) )
            {
                    @chmod($dest, 0774);
                    $contentOld = file_get_contents($dest);
                    $contentNew = str_replace($pattern, $replace, $contentOld);
                    file_put_contents($dest, $contentNew);
                }
        }

      } // end while

      closedir($handle);

      return true;
    } // end function rcopy



    function createDirectory($p_dest)
    {

        if ( is_dir($p_dest) )
        {
            // path already exists

            if (is_writable($p_dest))
            {
                // path is writeable
                return true;
            }

            // path is NOT writeable
            SysMsg::showWarning(sprintf(install_msg_dir_not_writeable, $p_dest));
            return false;
        }

        // path creation succeed
        if ( ! mkdir($p_dest, 0774, true) )
        {
            // setting permissions succeed
            SysMsg::showError(sprint(install_msg_dir_nomk, $p_dest));
            exit(0);
        }

        return true;
    } // end function createDirectory



    function replace($string, $arrReplace)  {
        $pattern = array_keys($arrReplace);
        return str_replace($pattern, $arrReplace, $string);
    }



    function handleSafeMode($path)
    {
        $handle = opendir($path);
      if ( ! $handle ) {
            SysMsg::showError("can not open path '{$path}'");
        return false;
        }

        $cfgFile = ".htaccess";

      while ( ($name = readdir($handle) ) !== false)
      {
        if ($name == '.' || $name == '..' || $name == '.svn') {
            continue;
        }

            if (is_dir($name) ) {
                $this->handleSafeMode($path . $name . DIRECTORY_SEPARATOR);
                continue;
            }

        if ($name != $cfgFile) {
            continue;
        }

            $cfgFilePath = $path.$cfgFile;
            $content = file($cfgFilePath);

            foreach($content as $idx => $line)
            {
                if (strpos($line, 'php_value') === 0) {
                    $content[$idx] = '#' . $line;
                    file_put_contents($cfgFilePath, implode('', $content));
                    continue;
                }
                if (strpos($line, 'php_flag') === 0) {
                    $content[$idx] = '#' . $line;
                    file_put_contents($cfgFilePath, implode('', $content));
                    continue;
                }
        } // end foreach
      } // end while

      closedir($handle);

      return true;
    } // end function rcopy
} //  end class TemplateFile

