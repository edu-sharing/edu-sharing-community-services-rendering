<?php

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
    {
        $Logger = $this->getLogger();

        $Filename = CC_RENDER_PATH . DIRECTORY_SEPARATOR;
        $Filename .= $this->getName() . DIRECTORY_SEPARATOR;
        $Filename .= $this->_ESOBJECT->getEsobjectFilePath() . DIRECTORY_SEPARATOR;
        $Filename .= $this->_ESOBJECT->getObjectID() . $this->_ESOBJECT->getObjectVersion();

        return str_replace('\\','/',$Filename);
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::createInstance()
     */
    public function createInstance(array $requestData) {
        ini_set('memory_limit', '4000M');
        $Logger = $this->getLogger();

        $upath = date('Y/m/d/H/i/s');

        // update esobject member data
        $this->_ESOBJECT->setFilePath($upath);
        $this->_ESOBJECT->setSubUri($upath);

        $this->filename = $this->_ESOBJECT->getObjectIdVersion();

        // real path
        $this->render_path = CC_RENDER_PATH . DIRECTORY_SEPARATOR
            . $this->_ESOBJECT->ESModule->getName()
            .DIRECTORY_SEPARATOR
            .$upath;

        if ( ! file_exists($this->render_path) )
        {
            if ( ! mkdir($this->render_path, 0777, true) )
            {
                $Logger->error('Error creating path "'.$this->render_path.'".');
                return false;
            }

            if ( ! chmod($this->render_path, 0777) )
            {
                $Logger->error('Error changing permissions on "'.$this->render_path.'".');
                return false;
            }
        }
        
        try {       
            $timestamp = round(microtime(true) * 1000);
            $signData = $requestData['object_id'] . $timestamp;
            $pkeyid = openssl_get_privatekey($requestData['private_key']);      
            openssl_sign($signData, $signature, $pkeyid);
            $signature = urlencode(base64_encode($signature));
            openssl_free_key($pkeyid); 
            $cacheFile = $this->getCacheFileName();

            $url =  current(explode("/services/", $requestData['homerepoConf']['authenticationwebservice']));       
            $path = '/content?';
            $params = 'appId='.$requestData['renderAppId'] . '&nodeId=' . $requestData['object_id'] . '&timeStamp=' . $timestamp . '&authToken=' . $signature . '&version=' . $requestData['version'];
            $url .= $path . $params;
            
            $handle = fopen($cacheFile, "wb");
            
            $content = $this->getContent($url);
            
            if($content === false) {
                fclose($handle);    
                $Logger->info('Error fetching content.');
                return false;
            }
            
            fwrite($handle, $content);
            fclose($handle);    
            $Logger->info('Stored content in file "'.$cacheFile.'".');

        } catch (Exception $e) {
            $Logger->info('Error storing content in file "'.$cacheFile.'".');
            return false;
        }
        
        return true;
    }
    
    protected function getContent($url) {
        $Logger = $this->getLogger();   
        $handle = fopen($url, "rb");
        if($handle === false) {
            $Logger->error('Cannot open ' . $url); 
        }
        
        $result = stream_get_contents($handle);
        fclose($handle);
        return $result;
    }

    protected function renderUrl(array $requestData)
    {
        $url = MC_URL . '/application/esmain?';

        if ( ! $requestData['object_id'] )
        {
            throw new ESRender_Exception_MissingRequestParam('obj_id');
        }

        $url .= 'obj_id=' . urlencode($requestData['object_id']);

        if ( ! $requestData['app_id'] )
        {
            throw new ESRender_Exception_MissingRequestParam('app_id');
        }

        $url .= '&app_id=' . urlencode($requestData['app_id']);

        if ( ! $requestData['rep_id'] )
        {
            throw new ESRender_Exception_MissingRequestParam('rep_id');
        }

        $url .= '&rep_id=' . urlencode($requestData['rep_id']);

        if ( ! $requestData['session'] )
        {
           // throw new ESRender_Exception_MissingRequestParam('session');
        }

        $url .= '&session=' . urlencode($requestData['session']);

        if ( $requestData['course_id'] )
        {
            $url .= '&course_id=' . urlencode($requestData['course_id']);
        }

        if ( $requestData['resource_id'] )
        {
            $url .= '&resource_id=' . urlencode($requestData['resource_id']);
        }
        
        if ( $requestData['usernameEncrypted'] )
        {
            $url .= '&u=' . urlencode($requestData['usernameEncrypted']);
        }

        $url .= '&token=' . $requestData['token'];

        $redirector = '{{{LMS_INLINE_HELPER_SCRIPT}}}&';
        
        $url = $redirector . 'url=' . urlencode($url);     
        
        return $url;
    }

    /**
     * @param array $requestData
     */
    protected function download(array $requestData)
    {
        $Logger = $this->getLogger();
        $url = $this->_ESOBJECT -> getPathfile() .  '?' . 'token=' . $requestData['token'];
        $Logger->debug('Redirecting to location: "' . $url . '"');

        header('HTTP/1.1 303 See other');
        header('Location: ' . $url);

        exit(0);
    }

    /**
     * @param array $requestData
     */
    protected function display(array $requestData)
    {
        $Logger = $this->getLogger();

        $url = $this->_ESOBJECT->getPath() . '?' . 'token=' . $requestData['token'];
        $Logger->debug('Redirecting to location: "' . $url . '"');

        header('HTTP/1.1 303 See other');
        header('Location: ' . $url);

        return true;
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Module_Base::inline()
     */
    protected function inline(array $requestData)
    {
        $Logger = $this->getLogger();
        
        $data = array(
        		'title' => $this->_ESOBJECT->getTitle(),
            	'url' => $this->renderUrl($requestData));

        if(ENABLE_METADATA_RENDERING) {
        	$metadata = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate());
        	$data['metadata'] = $metadata;
        }
        
        $license = $this->_ESOBJECT->ESOBJECT_LICENSE;
        if(!empty($license)) {
        	$data['license'] = $license -> renderFooter($this -> getTemplate());
        }

        $snippet = $this->getTemplate()->render('/module/default/inline', $data);
                
        $Logger->debug('ESRender_Module_Base::inline Snippet "' . $snippet . '"');

        echo $snippet;

        return true;
    }
    
    
    protected function dynamic(array $requestData) {
    	 
       $Logger = $this->getLogger();
       $Logger->debug('ESRender_Module_Base::dynamic Snippet "' . $snippet . '"');
       
       $data = array();
       $data['url'] = $this->_ESOBJECT->getPath() . '?' . 'token=' . $requestData['token'];
       $data['metadata'] = $this -> _ESOBJECT -> metadatahandler -> render($this -> getTemplate(), '/metadata/dynamic', $valuesToShow);  
       $data['previewUrl'] = $this->_ESOBJECT->getPreviewUrl();
       echo $this->getTemplate()->render('/module/default/dynamic', $data);
       
       return true;
    	
    }


}
