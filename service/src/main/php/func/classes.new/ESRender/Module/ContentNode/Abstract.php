<?php

require_once __DIR__ . "/../Base.php";

/**
 * Base-class for all modules handling objects, which contain binary data,
 * e.g. video/audio/images.
 *
 *
 */
abstract class ESRender_Module_ContentNode_Abstract
extends ESRender_Module_Base
{

    /**
     *
     * @return string
     */
    protected function getCacheFileName()
    {global $CC_RENDER_PATH;
        $Logger = $this->getLogger();

        $Filename = $CC_RENDER_PATH . DIRECTORY_SEPARATOR;
        $Filename .= $this->getName() . DIRECTORY_SEPARATOR;
        $Filename .= $this-> esObject ->getSubUri() . DIRECTORY_SEPARATOR;
        $Filename .= $this-> esObject ->getObjectIdVersion();

        return str_replace('\\','/',$Filename);
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::createInstance()
     */
    public function createInstance() {
    	global $CC_RENDER_PATH, $VIEWER_JS_CONFIG;;
        ini_set('memory_limit', '4000M');
        $Logger = $this->getLogger();

        $upath = date('Y/m/d/H/i/s');

        // update esobject member data
        $this-> esObject ->setSubUri($upath);

        $this->filename = $this-> esObject ->getObjectIdVersion();

        $module = $this->esObject->module->getName();
        $viewer_js_modules = [
            'pdf',
            'office',
            'spreadsheet'
        ];
        if (!ENABLE_VIEWER_JS && strpos($module, 'office') !== false ||
            ENABLE_VIEWER_JS && in_array($module, $viewer_js_modules) && !in_array($module, $VIEWER_JS_CONFIG) ){
                $module = 'doc';
        }

        // real path
        $this->render_path = $CC_RENDER_PATH . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $upath;

        if ( ! file_exists($this->render_path) )
        {
            if ( ! mkdir($this->render_path, 0777, true) )
            {
                $Logger->error('Error creating path "'.$this->render_path.'".');
                return false;
            }
            $Logger->debug('Created path "'.$this->render_path.'".');

            if ( ! chmod($this->render_path, 0777) )
            {
                $Logger->error('Error changing permissions on "'.$this->render_path.'".');
                return false;
            }
            $Logger->debug('Changed permissions on "'.$this->render_path.'".');
        }
        
        try {       
            $timestamp = round(microtime(true) * 1000);
            $signData = $this -> esObject -> getObjectID() . $timestamp;
            $pkeyid = openssl_get_privatekey(Config::get('homeConfig')->prop_array['private_key']);
            openssl_sign($signData, $signature, $pkeyid);
            $signature = urlencode(base64_encode($signature));
            openssl_pkey_free($pkeyid);
            $cacheFile = $this->getCacheFileName();
            $url =  current(explode("/services/", Config::get('homeRepository')->prop_array['authenticationwebservice']));
            $path = '/content?';
            $params = 'repId=' . $this -> esObject -> getNode() -> ref -> repo . '&appId='.Config::get('homeConfig')->prop_array['appid'] . '&nodeId=' .
                $this -> esObject -> getObjectID() . '&timeStamp=' . $timestamp . '&authToken=' . $signature . '&version=' . $this -> esObject -> getObjectVersion();
            $url .= $path . $params;

            try {
                $client = GuzzleHelper::getClient();
                $client->get($url, [
                    'sink' => $cacheFile
                ]);
            } catch(Exception $e){
                $Logger->error('Exception while fetching content: ' . $e->getMessage());
                $Logger->error('Error fetching content from ' . $url);
            }

            $Logger->info('Stored content in file "'.$cacheFile.'". ');

        } catch (Exception $e) {
            $Logger->error('Error storing content in file "'.$cacheFile.'".');
            return false;
        }
        
        return true;
    }

    /**
     * @param ESObject $ESObject
     */
    protected function download()
    {
        $Logger = $this->getLogger();
        $url = $this-> esObject  -> getPathfile() .  '?' . session_name() . '=' . session_id() . '&token=' . Config::get('token');
        $Logger->debug('Redirecting to location: "' . $url . '"');

        header('HTTP/1.1 303 See other');
        header('Location: ' . $url);

        return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::inline()
     */
    protected function inline()
    {
        $Logger = $this->getLogger();
        $data = parent::prepareRenderData();
        $data['url'] = $this->lmsInlineHelper();
        echo $this->getTemplate()->render('/module/default/inline', $data);
        $Logger->debug('ESRender_Module_Base::inline');
        return true;
    }
    
    
    protected function dynamic() {
       $Logger = $this->getLogger();
       $Logger->debug('ESRender_Module_Base::dynamic');
       $data = array();
       $data['url'] = $this-> esObject ->getPath() . '?' . session_name() . '=' . session_id() . '&token=' . Config::get('token');
       if(Config::get('showMetadata'))
       		$data['metadata'] = $this -> esObject -> getMetadataHandler() -> render($this -> getTemplate(), '/metadata/dynamic');
       $data['previewUrl'] = $this-> esObject ->getPreviewUrl();
       $data['title'] = $this-> esObject ->getTitle();
       $user = $this -> esObject -> getUser();
       $data['loginAvailable'] = empty($user -> userName) || $user -> isGuest;
       echo $this->getTemplate()->render('/module/default/dynamic', $data);
       return true;
    }

    protected function embed() {
        $Logger = $this->getLogger();
        $Logger->debug('ESRender_Module_Base::embed');
        $data = parent::prepareRenderData(false);
        $data['url'] = $this-> esObject ->getPath() . '?' . session_name() . '=' . session_id() . '&token=' . Config::get('token');
        $data['previewUrl'] = $this-> esObject ->getPreviewUrl();
        echo $this->getTemplate()->render('/module/default/embed', $data);
        return true;
    }

    protected function prerender() {
        $Logger = $this->getLogger();
        $Logger->debug('ESRender_Module_Base::prerender');
        return true;
    }

    /**
     * override this in your module if you can clearly decide if it should process the particular format
     */
    public static function canProcess($esObject) {
        return false;
    }

}
