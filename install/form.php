<?php
/*
* $McLicense$
*
* $Id$
*
*/

class form
    extends Step
{
    // IMPORTANT NOTE : the first step does not need a function check() !
    // the check function triggers the fallback to the previous step - so you see, eh? :)


    /**
     *
     */
    function check($p_post) {

        if ($p_post['terms_accepted'] == '0')
        {
            $this->error(install_err_terms_not_accepted);
            return false;
        }

        $this->writeLog('_REQUEST', $p_post);

        if ( ! $this->checkApacheModules(
            // required extensions (space separated)
            'mod_rewrite mod_ssl',
            // optional extensions (space separated)
            ''
        ) )
        {
            return false;
        }

        if ( !$this->checkPhpExtensions(
            // required extensions (space separated)
            'session dom soap sockets iconv gd mbstring fileinfo mcrypt openssl zip',
            // optional extensions (space separated)
            'mysql pgsql zlib wddx'
        ) )
        {
            return false;
        }

        return true;
    } // end method check


    /**
     *
     */
    function getPage($p_post, $p_step)
    {
        global $default_url_scheme, $default_url_host;
        global $default_base_uri, $default_base_dir;
        global $default_db_host, $default_db_port, $default_db_user, $default_db_pass, $default_db_name;
        global $default_debug, $default_dump;
        global $supported_lang;
        global $repository_url;
        global $repository_user;
        global $repository_pwd;
        global $data_dir;

        $base_uri_preset = dirname(dirname($_SERVER["SCRIPT_NAME"]));
        $base_dir_preset = dirname(dirname(__FILE__));

        if (basename($base_dir_preset) == 'maintenance') {
            // change presets if installer runs inside maintenance
            $base_uri_preset = dirname($base_uri_preset);
            $base_dir_preset  = dirname($base_dir_preset);
            if (empty($p_post['DB_NAME']) ) {
                $default_db_name =  basename($default_base_uri).'_install_check';
            }
        }


        $default_url_scheme = 'http';
        if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == "on") {
            $default_url_scheme .= 's';
        }

        $default_url_host = $_SERVER["SERVER_ADDR"];
        $default_base_uri = $base_uri_preset;       
       
        $rsurl_preset = $default_url_scheme . '://' . $default_url_host . $default_base_uri;
       
        $default_url = empty($p_post['RS_URL']) ? $rsurl_preset    : $p_post['RS_URL'];
        $default_base_dir = empty($p_post['BASE_DIR']) ? $base_dir_preset    : $p_post['BASE_DIR'];
        $default_db_host  = empty($p_post['DB_HOST'])  ? '127.0.0.1'         : $p_post['DB_HOST'];
        $default_db_port  = empty($p_post['DB_PORT'])  ? '3306'              : $p_post['DB_PORT'];
        $default_db_user  = empty($p_post['DB_USER'])  ? ''              : $p_post['DB_USER'];
        $default_db_pass  = empty($p_post['DB_PASS'])  ? ''                  : $p_post['DB_PASS'];
        $default_db_name  = empty($p_post['DB_NAME'])  ? 'esrender'   : $p_post['DB_NAME'];
        $default_lang     = empty($p_post['DEF_LANG']) ? 1                   : intval($_REQUEST['DEF_LANG']);
        $repository_url   = empty($p_post['REPO_URL'])  ? $default_url_scheme.'://'.$default_url_host.'/edu-sharing/' : $p_post['REPO_URL'];
        $data_dir         = empty($p_post['DATA_DIR'])  ? '/var/cache/esrender/'     : $p_post['DATA_DIR'];

        // add missing leading and/or trailing slashes to uri
        $default_base_uri = $this->normalizeUri($default_base_uri);

        // add missing trailing slash to basedir
        $default_base_dir = $this->normalizeDir($default_base_dir);

        $lang_options = array();
        foreach ($supported_lang as $lang_id => $lang_iso)
        {
            $selected = ($lang_id == $default_lang) ? 'selected="selected"' : '';
            $lang_options[] = '<option value="'.$lang_id.'" '.$selected.'>'.$lang_iso.'</option>';
        } // end foreach

          $label_05  = install_label_05;
          $label_05a = install_label_05a;
          $label_06  = install_label_06;
          $label_07  = install_label_07;
          $label_21  = install_label_21;


       /* $default_url_scheme = '<input name="URL_SCHEME" type="text" value="'.$default_url_scheme.'"   style="width:100%;">';
        $default_url_host   = '<input name="URL_HOST"   type="text" value="'.$default_url_host.'"   style="width:100%;">';
        $default_base_uri   = '<input name="BASE_URI"   type="text" value="'.$default_base_uri.'"   style="width:100%;">';*/
        
        $default_url        = '<input name="RS_URL"   type="text" value="'.$default_url.'"   style="width:100%;">';
        
        $default_base_dir   = '<input name="BASE_DIR"   type="text" value="'.$default_base_dir.'"   style="width:100%;">';
        $repository_url     = '<input name="REPO_URL"   type="text" value="'.$repository_url.'"   style="width:100%;">';
        $data_dir           = '<input name="DATA_DIR"   type="text" value="'.$data_dir.'"   style="width:100%;">';

        if(isset($p_post['DB_DRIVER']) && $p_post['DB_DRIVER'] == 'pgsql') {
            $db_driver = '<input type="radio" name="DB_DRIVER" value="mysql">&nbsp;MySQL/MariaDB&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="DB_DRIVER" value="pgsql" checked> PostgreSQL';
        } else {
            $db_driver = '<input type="radio" name="DB_DRIVER" value="mysql" checked>&nbsp;MySQL/MariaDB&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="DB_DRIVER" value="pgsql"> PostgreSQL';
        }


        if (ini_get('sql.safe_mode'))
        {
            $safe_mode = install_msg_sql_safe_mode;
            $default_db_host = 'localhost';
            $default_db_port = '3306';
            $default_db_user = get_current_user();
        }
        else
        {
            $safe_mode = '';
            $default_db_host = '<input name="DB_HOST" type="text" value="'.$default_db_host.'" style="width:100%;">';
            $default_db_port = '<input name="DB_PORT" type="text" value="'.$default_db_port.'" style="width:100%;">';
            $default_db_user = '<input name="DB_USER" type="text" value="'.$default_db_user.'" style="width:100%;">';
            $default_db_pass = '<input name="DB_PASS" type="password" value="'.$default_db_pass.'" style="width:100%;">';
        }

        $default_db_name = '<input name="DB_NAME" type="text" value="'.$default_db_name.'" style="width:100%;">';

        return strtr(file_get_contents('./_layout/form.lay'), array(
            '{step}' => $p_step,
           // '{intro}'    => install_intro,
            '{label_01}' => install_label_01,
            '{label_03}' => install_label_03,
            '{label_04}' => install_label_04,
            '{label_05}' => $label_05,
            '{label_05a}' => $label_05a,
            '{label_06}' => $label_06,
            '{label_07}' => $label_07,
            '{label_08}' => install_label_08,
            '{label_09}' => install_label_09,
            '{label_11}' => install_label_11,
            '{label_12}' => install_label_12,
            '{label_16}' => install_label_16,
            '{label_17}' => install_label_17,
            '{label_18}' => install_label_18,
            '{label_19}' => install_label_19,
            '{label_20}' => install_label_20,
            '{label_21}' => install_label_21,
            '{label_22}' => install_label_22,
            '{label_23}' => install_label_23,
            '{label_24}' => install_label_24,
            '{label_25}' => install_label_25,
            '{label_26}' => install_label_26,
            /*'{url_scheme}' => $default_url_scheme,
            '{url_host}'   => $default_url_host,
            '{base_uri}'   => $default_base_uri,*/
            '{rs_url}'   => $default_url,
            '{base_dir}'   => $default_base_dir,
            '{repo_url}'   => $repository_url,
            '{data_dir}'   => $data_dir,
            '{db_host}' => $default_db_host,
            '{db_port}' => $default_db_port,
            '{db_user}' => $default_db_user,
            '{db_pass}' => $default_db_pass,
            '{db_name}' => $default_db_name,
            '{safe_mode}'   => $safe_mode,
            '{form_name}'   => 'settings',
            '{form_target}' => '_self',
            '{form_action}' => 'install.php',
            '{form_submit}' => install_submit,
            '{inst_path}'    => MC_BASE_DIR,
            '{lang_options}' => (implode("\n", $lang_options)),
            '{module_config}' => module_config,
            '{lang_id}' => INST_LANG_ID,
            '{db_driver}' => $db_driver,
            '{db_driver_label}' => db_driver_label
        ));
    } // end method getPage



    /**
     *
     */
    function checkPhpExtensions($strRequired, $strOptional)
    {
        $arrRequired = array_flip(explode(' ', $strRequired));
        $arrOptional = array_flip(explode(' ', $strOptional));

        $l_required = true;
        $l_optional = true;

        foreach ($arrRequired as $libname => $libdesc)
        {
            $l_msg_lib = '<b>'.strtoupper($libname).'</b>';
            if ( !extension_loaded($libname) )
            {
                $l_required = false;
                $this->error(sprintf(install_err_check_phpext_miss, $l_msg_lib, $libdesc));
            }
        }

        if ($l_required)
        {
            $this->info(sprintf(install_msg_check_phpext_has_req, implode(', ', array_keys($arrRequired))));
        }

        foreach ($arrOptional as $libname => $libdesc)
        {
            $l_msg_lib = '<b>'.strtoupper($libname).'</b>';
            if ( !extension_loaded($libname) )
            {
                $l_optional = false;
                $this->warning(sprintf(install_err_check_phpext_miss, $l_msg_lib, $libdesc));
            }
        }

        if ($l_optional)
        {
            $this->info(sprintf(install_msg_check_phpext_has_opt, implode(', ', array_keys($arrOptional))));
        }

        return $l_required;
    } // end method checkPhpExtensions



    /**
     *
     */
    function checkApacheModules($strRequired, $strOptional)
    {
        if ( ! function_exists('apache_get_modules') ) {
            return true;
        }

    if (empty($strRequired) )
    {
        $arrRequired = array();
        }
    else
    {
          $arrRequired = array_flip(explode(' ', $strRequired));
        }

    if (empty($strOptional))
    {
        $arrOptional = array();
        }
    else
    {
          $arrOptional = array_flip(explode(' ', $strOptional));
        }



        $l_required = true;
        $l_optional = true;

        $installed_modules = apache_get_modules();

        //$this->msg(install_msg_check_apachemod_req);

        foreach ($arrRequired as $libname => $libdesc)
        {
            $l_msg_lib = '<b>'.strtoupper($libname).'</b>';
            if ( !in_array($libname, $installed_modules) )
            {
                $l_required = false;
                $this->error(sprintf(install_err_check_apachemod_miss, $l_msg_lib, $libdesc));
            }
        }

        if ($l_required)
        {
            $this->info(sprintf(install_msg_check_apachemod_has_req, implode(', ', array_keys($arrRequired))));
        }

        foreach ($arrOptional as $libname => $libdesc)
        {
            $l_msg_lib = '<b>'.strtoupper($libname).'</b>';
            if ( !in_array($libname, $installed_modules) )
            {
                $l_optional = false;
                $this->warning(sprintf(install_err_check_apachemod_miss, $l_msg_lib, $libdesc));
            }
        }

        if ($l_optional && !empty($arrOptional))
        {
            $this->info(sprintf(install_msg_check_apachemod_has_opt, implode(', ', array_keys($arrOptional))));
        }

        return $l_required;
    } // end method checkApacheModules



} // end class form

