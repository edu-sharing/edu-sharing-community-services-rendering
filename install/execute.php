<?php
/*
* $McLicense$
*
* $Id$
*
*/

class execute
    extends Step
{

    private $rs_url = '';
    private $base_dir = '';

    private $db_drvr = '';
    private $db_host = '';
    private $db_port = '';
    private $db_name = '';
    private $db_user = '';
    private $db_pass = '';
    private $drop_db = '';

    private $repo_url = '';
    private $repo_host   = '';
    private $repo_scheme = '';
    private $repo_port   = '';
    private $data_dir = '';
    private $lang    = '';
    
    private $connect = false;

    private $default_memory_unit = 'M';

    /**
     *
     */
    function check($post)
    {
        if (empty($post['RS_URL']))
        {
            $this->error(initdb_missing_form_data);
            return false;
        }

        $this->writeLog('_REQUEST', $post);
        
        $this -> rs_url = trim($post['RS_URL']);
        $this->base_dir = $this->normalizeDir($post['BASE_DIR']);

        $this->repo_url = ($post['REPO_URL']);

        $repo = parse_url($this->repo_url);
        $this->repo_host   = gethostbyname($repo['host']);
        $this->repo_scheme = $repo['scheme'];

    if (empty($repo['port'])){
          $this->repo_port   = '80';
        } else{
            $this->repo_port   = $repo['port'];
        };

        $this->data_dir = $this->normalizeDir($post['DATA_DIR']);
        $this->db_drvr = trim($post['DB_DRIVER']);
        $this->db_host = trim($post['DB_HOST']);
        $this->db_port = trim($post['DB_PORT']);
        $this->db_user = trim($post['DB_USER']);
        $this->db_pass = trim($post['DB_PASS']);
        $this->db_name = trim($post['DB_NAME']);
        $this->lang_id = empty($post['DEF_LANG']) ? 1 : intval($_REQUEST['DEF_LANG']);
        $this->drop_tab = empty($post['DROP_TAB']) ? false : true;

        return true;
    } // end method check


    function getBaseDir() { return $this->base_dir; }
    function getUrl() { return $this -> rs_url;}   
    function getRepoUrl()  { return $this->repo_url; }
    function setRepoUrl($repoUrl)  { $this->repo_url = $repoUrl; }
    function getRepoHost() { return $this->repo_host; }
    function getRepoPort() { return $this->repo_port; }
    function getRepoScheme() { return $this->repo_scheme; }
    function getDataDir()  { return $this->data_dir; }


    /**
     *
     */
    function getDbDrvr() { return $this->db_drvr; }
    function getDbHost() { return $this->db_host; }
    function getDbPort() { return $this->db_port; }
    function getDbUser() { return $this->db_user; }
    function getDbPass() { return $this->db_pass; }
    function getDbName() { return $this->db_name; }
    



    /**
     *
     */
    function getLangId() { return $this->lang_id; }

    /**
     *
     */
    function dropTablesIfExists() { return $this->drop_tab; }



    /**
     *
     */
    function process($post)
    {
        require_once(INST_PATH_LIB . "TemplateFile.php");
        require_once(INST_PATH_LIB . "RemoteAppPropertyHandler.php");
        try
        {
            $TmplFile = new TemplateFile();
            $TmplFile->copyTplDirectories($this);
            $remoteAppPropertyHandler = new RemoteAppPropertyHandler($this);
            $remoteAppPropertyHandler -> setHomeRep();
            
            chmod(dirname(__FILE__) . '/../vendor/lib/converter/ffmpeg', '0744');

           
           $this-> info(install_config_success);
           
            return true;
        }
        catch (Exception $e)
        {
            // @TODO : error handling here (no rollback required, overwrite works)
            return false;
        }
    }



    /**
     *
     */
    function getPage($post, $p_step)
    {
        $path = dirname(__FILE__);
        
        $content = '<br/><br/><br/>';
        
        $content .= sprintf(install_msg_all_done, $path, $this->getUrl());
        
        return $content;
    } // end method getPage



} // end class execute
