<?php

class ProxyHelper {

    private $url = '';

    public function __construct($url)
    {
        $this -> url = $url;
    }

    public function getProxyConfig(): ?string
    {
        global $PROXY_CONFIG;
        if(isset($PROXY_CONFIG)) {
            $parsed = parse_url($this -> url);
            foreach ($PROXY_CONFIG['http.nonproxyhosts'] as $value) {
                if (strcasecmp($parsed['host'], $value) == 0) {
                    return null;
                }
            }
        } else {
            return null;
        }
        if($parsed['scheme'] === 'https') {
            return $PROXY_CONFIG['https.proxy'];
        }
        return $PROXY_CONFIG['http.proxy'];
    }

    public function getSoapClientParams(): array
    {
        $config = $this->getProxyConfig();
        if($config) {
            $parsed = parse_url($config);
            $SoapClientParams = array(
                'proxy_host' => $parsed['host'],
                'proxy_port' => $parsed['port'],
                'proxy_login' => $parsed['user'],
                'proxy_password' => $parsed['pass']
            );
            /*
            if ($config->sni && 'https' == parse_url($this->url)['scheme']) {
                $contextOptions = array('ssl' => array('SNI_server_name' => $this->parsedUrl['host'], 'SNI_enabled' => TRUE));
                $context = stream_context_create($contextOptions);
                $SoapClientParams['stream_context'] = $context;
            }
            */
            return $SoapClientParams;
        }
        return array();
    }

    public function applyToCurl($ch)
    {
        $config = $this->getProxyConfig();
        if($config) {
            curl_setopt($ch, CURLOPT_PROXY, $config);
        }
    }
}
