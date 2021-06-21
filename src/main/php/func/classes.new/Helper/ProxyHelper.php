<?php

class ProxyHelper {
    
    private $parsedUrl = '';
    
    public function __construct($url) {
        $this -> parsedUrl = parse_url($url);
    }
    
    public function getSoapClientParams() {
        $SoapClientParams = array(
            'proxy_host' => HTTP_PROXY_HOST,
            'proxy_port' => HTTP_PROXY_PORT,
            'proxy_login' => HTTP_PROXY_USER,
            'proxy_password' => HTTP_PROXY_PASS
        );
        
        if(HTTP_PROXY_SNI && 'https' == $this -> parsedUrl['scheme']) {
            $contextOptions = array('ssl' => array('SNI_server_name' => $this -> parsedUrl['host'], 'SNI_enabled' => TRUE));
            $context = stream_context_create($contextOptions);
            $SoapClientParams['stream_context'] = $context;
        }
        
        return $SoapClientParams;
    }
}
