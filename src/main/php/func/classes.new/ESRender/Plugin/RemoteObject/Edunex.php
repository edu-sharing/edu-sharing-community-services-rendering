<?php

require_once (dirname(__FILE__) . '/../Abstract.php');

/**
 *
 *
 *
 */
class ESRender_Plugin_RemoteObject_Edunex
extends ESRender_Plugin_Abstract {

    /**
     *
     * @param string $EdunexUrl
     * @param string $EdunexPassword
     * @param string $LdapHost
     * @param string $LdapPassword
     * @param string $LdapBaseDn
     * @param string $WsdlSchoolId
     * @param int $LdapPort
     */
    public function __construct($EdunexUrl = NULL, $EdunexPassword = NULL, $LdapHost = NULL, $LdapBaseDn = NULL, $LdapPassword = NULL, $LdapSearchBaseDn = NULL, $WsdlSchoolId = NULL, $LdapPort = 389) {
        if(!defined(EDUNEX_URL) && !EDUNEX_URL || !defined(EDUNEX_PASSWORD) && !EDUNEX_PASSWORD)
            throw new Exception('EDUNEX plugin missconfigured. Please contact site administrator.');
        
        $this -> setEdunexUrl($EdunexUrl) -> setEdunexPassword($EdunexPassword) -> setLdapHost($LdapHost) -> setLdapBaseDn($LdapBaseDn) -> setLdapPassword($LdapPassword) -> setLdapSearchBaseDn($LdapSearchBaseDn) -> setWsdlSchoolId($WsdlSchoolId) -> setLdapPort($LdapPort);
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Abstract::__destruct()
     */
    public function __destruct() {
        $this -> WsdlSchoolId = null;
        $this -> LdapSearchBaseDn = null;
        $this -> LdapPassword = null;
        $this -> LdapBaseDn = null;
        $this -> LdapHost = null;
        $this -> LdapPort = null;
        $this -> EdunexPassword = null;
        $this -> EdunexUrl = null;

        parent::__destruct();
    }

    /**
     * @see http://tools.ietf.org/html/rfc2254#section-4
     *
     * @param string $String
     *
     * @return string
     */
    protected function escapeFilter($String) {
        // have use double-quotes around \x00 to let php interpret value NULL
        $search = array('\\', '*', '(', ')', "\x00");
        $replace = array('\\5c', '\\2a', '\\28', '\\29', '\\00');
        $escaped = str_replace($search, $replace, $String);

        return $escaped;
    }

    /**
     * Query the LDAP-provider to translate between "username" and "GUID.
     *
     * @param string $Username
     *
     * @throws Exception
     *
     * @return string
     */
    protected function getUidFromLdap($Username) {
        if (empty($Username)) {
            throw new Exception('Username cannot be empty.');
        }

        $ConnectionResource = ldap_connect($this -> getLdapHost(), $this -> getLdapPort());
        if (!$ConnectionResource) {
            throw new Exception('Error connecting to LDAP.');
        }

        if (!ldap_bind($ConnectionResource, $this -> getLdapBaseDn(), $this -> getLdapPassword())) {
            ldap_close($ConnectionResource);
            throw new Exception('Error binding to LDAP.');
        }

        $LdapSearchBaseDn = $this -> getLdapSearchBaseDn();
        if (!$LdapSearchBaseDn) {
            ldap_close($ConnectionResource);
            throw new Exception('No base-dn for LDAP-search set.');
        }

        $ResultResource = ldap_search($ConnectionResource, $LdapSearchBaseDn, 'eduSchoolPrincipalName=' . $this -> escapeFilter($Username), array('uid'));
        if (!$ResultResource) {
            ldap_close($ConnectionResource);
            throw new Exception('Error searching in LDAP.');
        }

        if (1 < ldap_count_entries($ConnectionResource, $ResultResource)) {
            ldap_close($ConnectionResource);
            throw new Exception('More than one search result found for unique GUID.');
        }

        $Entry = ldap_first_entry($ConnectionResource, $ResultResource);
        if (!$Entry) {
            throw new Exception('No LDAP-entry found for user "' . $Username . '".');
        }

        $Guid = ldap_get_values($ConnectionResource, $Entry, 'uid');

        ldap_close($ConnectionResource);

        return $Guid[0];
    }

    /**
     * Method will exit script-execution when successful.
     *
     * @param string $object_id
     * @param string $username
     *
     * @throws Exception
     *
     * @return null
     */
    protected function doTheEdmondStuff($object_id, $username) {
        $Logger = $this -> getLogger();

        if (!$object_id) {
            throw new Exception('Empty object-id given.');
        }

        if (!$username) {
            throw new Exception('Empty username given.');
        }

        if ($Logger) {
            $Logger -> debug('Using GUID: ' . $Guid);
        }

        /*$Wsdl = $this->getWsdlSchoolId();
         $Params = array(
         //         'wsdl_cache_enabled' => 1,
         );

         $SoapClient = new SoapClient($Wsdl, $Params);
         $Arguments = array(
         'guid' => $username,
         );

         $Result = $SoapClient->getEdmondPath($Arguments);
         if ( ! empty($Result->return->errorcode) )
         {
         throw new Exception('Error calling SchulnummernService::getEdmondPath(). Given code "'.$Result->return->errorcode.'" with message: "'.$Result->return->errormsg.'".');
         }

         $EdmondContext = $Result->return->path;
         if ( ! $EdmondContext )
         {
         throw new Exception('No context found.');
         }*/

       $EdmondUrl = EDUNEX_URL;
       
       
       $EdmondContext = $this -> getContext();
       
       $EdmondUrl = $EdmondUrl . '/' . $EdmondContext;
       
        $postvars = 'xmlstatement=' . urlencode('<notch identifier="' . $object_id . '" />');

        $result = $this -> postRequest($EdmondUrl, $postvars);

        $xml = new SimpleXMLElement('<root>' . $result . '</root>');
        if ($xml -> error != '') {
            throw new Exception('XML-Fehler: ' . $xml -> error);
        }

        $notch = $xml -> notch;

        $id = $notch['id'];

       // $hashedEdunexPassword = md5($notch . ':' . $this -> getEdunexPassword());
       $hashedEdunexPassword = md5($notch . ':' . EDUNEX_PASSWORD);

        $postvars = 'xmlstatement=' . urlencode('<link id="' . $id . '">' . $hashedEdunexPassword . '</link>');
        $result = $this -> postRequest($EdmondUrl, $postvars);

        //$logo = "<logo></logo>";
       // $logo = "xmlstatement=" . $logo;

        //$logo_result = $this -> postRequest($EdmondUrl, $logo);

        $l_DOMDocument = new DOMDocument('1.0');
        if (!$l_DOMDocument -> loadXml($result)) {
            throw new Exception('Error loading edmond-xml.');
        }

        $objectlist = $l_DOMDocument -> getElementsByTagName("a");
        $root = $l_DOMDocument -> getElementsByTagName("link");
        $size = $root -> item(0) -> getAttribute("size");

        if ($objectlist -> length == 1) {

            $href = $objectlist -> item(0) -> getAttribute("href");
            //    die($href);
            $value = $objectlist -> item(0) -> nodeValue;
            $js = '
    <script type="text/javascript">
     window.location.href="' . $href . '";
    </script>
     ';
            echo $js;
            die();
        }

        $edunexStuff = '';

        foreach ($objectlist as $unit) {
            $href = $unit -> getAttribute("href");
            $val = $unit -> nodeValue;
            
            switch($val) {
                case "direct" :
                    $desc = 'Online-Material';
                    $href_desc = 'anzeigen';
                    break;
                case "download" :
                    $desc = 'Material';
                    $href_desc = 'herunterladen (' . $size . ')';
                    break;
               case "addons" :
                    $desc = 'Addon';
                    $href_desc = 'anzeigen';
                    break;
            }
            $edunexStuff .= '<p>' . $desc . '&nbsp;<a href="' . $href . '" >' . $href_desc . '</a></p>';
        }

        //$edunexStuff .= $logo_result;

        if ($Logger) {
            $Logger -> info('Exiting as processing finished in this plugin.');
        }

        echo $this -> getTemplate() -> render('/plugin/edunex/display', array('edunexStuff' => $edunexStuff, 'title' => ''));

        exit();
    }

    /**
     * Execute POST request.
     *
     * @param string $url
     * @param string|array $params
     */
    protected function postRequest($url, $params) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        if(defined('USE_HTTP_PROXY') && USE_HTTP_PROXY) {
            curl_setopt($ch, CURLOPT_PROXYPORT, HTTP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_PROXY, HTTP_PROXY_HOST);
        }
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // RETURN THE CONTENTS OF THE CALL

        $ret = curl_exec($ch);
        
        if($ret === false)
            echo curl_error($ch);
        
        return $ret;
    }

    /**
     * Method-stub to save ourselfs the implementation as required by interface
     * when plugin won't even use this hook.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Interface::postLoadRepository()
     */
    public function postLoadRepository(&$data) {

        return;

        $remote_rep = $data->node->ref->repo;
        
        $Logger = $this -> getLogger();

        /*
         * The url for external repositories "flips" repository- and application-side,
         * so the remote-app-id
         */
        if (!$remote_rep) {
            return false;
        }

        if (!array_key_exists('repositorytype', $remote_rep -> prop_array)) {
            return false;
        }

        if ($remote_rep -> prop_array['repositorytype'] == null) {
            return false;
        }

        if ($remote_rep -> prop_array['repositorytype'] == 'ALFRESCO') {
            return false;
        }

        if ($Logger) {
            $Logger -> debug('Handling EDMOND object postLoadingApplication().');
        }

       /* if (!empty($remote_app -> prop_array['subtype'])) {
            switch( strtoupper($remote_app->prop_array['subtype']) ) {
                case 'FRONTER' :
                    // MODIFYING $user_name in (external) render-context
                    $resolvedUsername = $this -> getUidFromLdap($username);
                    if (!$resolvedUsername) {
                        throw new Exception('Found no UID in LDAP for username "' . $username . '".');
                    }
                    break;

                default :
                // no sensible defaults here
            }

            $username = 'admin';
        }*/

        $this -> doTheEdmondStuff($object_id, $username);
    }
    
    
    /*
    public function preSslVerification(EsApplication &$remote_rep, &$app_id, &$object_id, &$course_id, &$resource_id, &$username, &$homeRep) {
        //set home repo's public key for ssl verification
        //if($remote_app -> prop_array['type'] == 'edunex')
        //    $remote_app -> prop_array['public_key'] = $homeRep -> prop_array['public_key'];
    }
    */
    
    
     public function postSslVerification(&$data, &$homeRep) {

        $Logger = $this -> getLogger();
        $remote_rep = $data->node->ref->repo;

        if (!$remote_rep) {
            return false;
        }

        if (!array_key_exists('repositorytype', $remote_rep -> prop_array)) {
            return false;
        }

        if ($remote_rep -> prop_array['repositorytype'] == null) {
            return false;
        }

        if ($remote_rep -> prop_array['repositorytype'] == 'ALFRESCO') {
            return false;
        }

        if ($Logger) {
            $Logger -> debug('Handling EDUNEX object postSslVerification().');
        }

        $this -> doTheEdmondStuff($data->node->ref->id, $data->user->authorityName);
    }
    

    /**
     * Translate between some LMS's user_name (a.k.a. email-addresses) to
     * LDAP UID's.
     *
     * (non-PHPdoc)
     * @see ESRender_Plugin_Abstract::postRetrieveUserData()
     */
    public function postRetrieveUserData(EsApplication &$remote_rep, &$app_id, &$object_id, &$course_id, &$resource_id, &$username) {
        /*if (!empty($remote_app -> prop_array['subtype'])) {
            switch( strtoupper($remote_app->prop_array['subtype']) ) {
                case 'FRONTER' :
                    // MODIFYING $user_name in (external) render-context
                    $resolvedUsername = $this -> getUidFromLdap($username);
                    if (!$resolvedUsername) {
                        throw new Exception('Found no UID in LDAP for username "' . $username . '".');
                    }
                    break;

                default :
                // no sensible defaults here
            }

            $username = $resolvedUsername;
        }*/
    }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
     */
    public function postRetrieveObjectProperties(EsApplication &$remote_rep, &$app_id, ESContentNode &$contentNode, &$course_id, &$resource_id, &$username) {
        $Logger = $this -> getLogger();
        $remoterepositorytype = $contentNode -> getNodeProperty('ccm:remoterepositorytype');
        if (empty($remoterepositorytype)) {
            return false;
        }

        $remoterepositorytype = $contentNode -> getNodeProperty('ccm:remoterepositorytype');
        if ('EDUNEX' != $contentNode) {
            return false;
        }

        if ($Logger) {
            $Logger -> debug('Handling EDMOND object postRetrieveObjectProperties().');
        }

        $object_id = $contentNode -> getNodeProperty('ccm:remotenodeid');

        $this -> doTheEdmondStuff($object_id, $username);
    }

    /**
     * Hold the repository-type we'll handle remotely.
     *
     * @var string
     */
    private $RepositoryType = 'EDUNEX';

    /**
     * Set the remote-repository-type to act upon when found in node-properties.
     *
     * @param string $RepositoryType
     */
    public function setRepositoryType($RepositoryType) {
        $this -> RepositoryType = (string)$RepositoryType;
        return $this;
    }

    /**
     *
     * @return string
     */
    protected function getRepositoryType() {
        return $this -> RepositoryType;
    }

    /**
     *
     * @var string
     */
    private $EdunexUrl = '';

    /**
     * Set the url to fetch remote-data from.
     *
     * @param string $EdunexUrl
     */
    public function setEdunexUrl($EdunexUrl) {
        $this -> EdunexUrl = (string)$EdunexUrl;
        return $this;
    }

    /**
     * Get the current url.
     *
     * @return string
     */
    protected function getEdunexUrl() {
        return $this -> EdunexUrl;
    }

    /**
     *
     * @var string
     */
    private $EdunexPassword = '';

    /**
     * Set the edunex-password to use when fetching remote-data.
     *
     * @param string $EdunexPassword
     */
    public function setEdunexPassword($EdunexPassword) {
        $this -> EdunexPassword = (string)$EdunexPassword;
        return $this;
    }

    /**
     * Get the currently set edunex-password.
     *
     * @return string
     */
    protected function getEdunexPassword() {
        return $this -> EdunexPassword;
    }

    /**
     *
     *
     * @var string
     */
    protected $LdapHost = 'localhost';

    /**
     *
     *
     * @param string $LdapHost
     * @return ESRender_Plugin_RemoteObject_Edunex
     */
    public function setLdapHost($LdapHost) {
        $this -> LdapHost = (string)$LdapHost;
        return $this;
    }

    /**
     *
     * @return string
     */
    protected function getLdapHost() {
        return $this -> LdapHost;
    }

    /**
     *
     *
     * @var string
     */
    protected $LdapBaseDn = 'cn=Manager,dc=schulcockpit';

    /**
     *
     *
     * @param string $LdapBaseDn
     * @return ESRender_Plugin_RemoteObject_Edunex
     */
    public function setLdapBaseDn($LdapBaseDn) {
        $this -> LdapBaseDn = (string)$LdapBaseDn;
        return $this;
    }

    /**
     *
     * @return string
     */
    protected function getLdapBaseDn() {
        return $this -> LdapBaseDn;
    }

    /**
     *
     *
     * @var string
     */
    protected $LdapPassword = '';

    /**
     *
     *
     * @param string $LdapPassword
     * @return ESRender_Plugin_RemoteObject_Edunex
     */
    public function setLdapPassword($LdapPassword) {
        $this -> LdapPassword = (string)$LdapPassword;
        return $this;
    }

    /**
     *
     * @return string
     */
    protected function getLdapPassword() {
        return $this -> LdapPassword;
    }

    /**
     *
     *
     * @var int
     */
    protected $LdapPort = 389;

    /**
     *
     *
     * @param int $LdapPort
     * @return ESRender_Plugin_RemoteObject_Edunex
     */
    public function setLdapPort($LdapPort) {
        $this -> LdapPort = (int)$LdapPort;
        return $this;
    }

    /**
     *
     * @return int
     */
    protected function getLdapPort() {
        return $this -> LdapPort;
    }

    /**
     *
     *
     * @var string
     */
    protected $LdapSearchBaseDn = '';

    /**
     *
     *
     * @param string $LdapSearchBaseDn
     * @return ESRender_Plugin_RemoteObject_Edunex
     */
    public function setLdapSearchBaseDn($LdapSearchBaseDn) {
        $this -> LdapSearchBaseDn = (string)$LdapSearchBaseDn;
        return $this;
    }

    /**
     *
     * @return string
     */
    protected function getLdapSearchBaseDn() {
        return $this -> LdapSearchBaseDn;
    }

    /**
     *
     *
     * @var string
     */
    protected $WsdlSchoolId = '';

    /**
     *
     *
     * @param string $WsdlSchoolId
     * @return ESRender_Plugin_RemoteObject_Edunex
     */
    public function setWsdlSchoolId($WsdlSchoolId) {
        $this -> WsdlSchoolId = (string)$WsdlSchoolId;
        return $this;
    }

    /**
     *
     * @return string
     */
    protected function getWsdlSchoolId() {
        return $this -> WsdlSchoolId;
    }

    /**
     *
     *
     * @param Phools_Template_Script $template
     * @return ESRender_Plugin_RemoteObject_Edunex
     */
    public function setTemplate(Phools_Template_Script $template) {
        $this -> template = $template;
        return $this;
    }

    /**
     *
     * @return string
     */
    protected function getTemplate() {
        return $this -> template;
    }
    
    /**
     * @return Context of school
     * @todo implement this
     * 
     */
    protected function getContext() {
        $context = mc_Request::fetch('context', 'CHAR');
        if (!$context) {
            $this -> getLogger() -> error('Missing request-param "context".');
            throw new ESRender_Exception_MissingRequestParam('context');
        }
    
        return trim($context, '/');
    }

}
