<?php

class AppPropertyHelper {
    
    private $configFile = '';
    
    public function __construct($configFile = '') {
        $this -> configFile = $configFile;
    }

    public function addSslKeypairToHomeConfig() {
        $sslKeypair = $this -> getSslKeypair();
        $xml = simplexml_load_file(dirname(__FILE__) . '/../../../conf/esmain/homeApplication.properties.xml');
        $pubKey = $xml->addChild("entry");
        $pubKey -> addAttribute("key", "public_key");
        $pubKey[0] = $sslKeypair['publicKey'];
        $privateKey = $xml->addChild("entry");
        $privateKey -> addAttribute("key", "private_key");
        $privateKey[0] = $sslKeypair['privateKey'];
        $xml->asXML(dirname(__FILE__) . '/../../../conf/esmain/homeApplication.properties.xml');
    }
    
    public function getSslKeypair() {
        $sslKeypair = array();
        $res=openssl_pkey_new();
        openssl_pkey_export($res, $privatekey);
        $publickey=openssl_pkey_get_details($res);
        $publickey=$publickey["key"];
        $sslKeypair['privateKey'] = $privatekey;
        $sslKeypair['publicKey'] = $publickey;
        return $sslKeypair;
    }
    
}
