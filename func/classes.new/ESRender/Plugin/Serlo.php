<?php

/**
 *Handle Serlo Materials
 *
 *
 */
class ESRender_Plugin_Serlo
    extends ESRender_Plugin_Abstract
{

    
    /**
     *
     * @param string $Url
     */
    public function __construct() { }

    /**
     * (non-PHPdoc)
     * @see ESRender_Plugin_Abstract::postRetrieveObjectProperties()
     */
    public function postRetrieveObjectProperties(EsApplication &$remote_rep, &$app_id,ESContentNode &$contentNode, &$course_id, &$resource_id, &$username) {

	if($contentNode->getProperty('ccm:replicationsource') === 'serlo') {
    	    $this -> iconUrl = $remote_rep->prop_array['clientprotocol'] .'://' . $remote_rep->prop_array['domain'] . ':' . $remote_rep->prop_array['clientport'] . '/edu-sharing/assets/images/sources/serlo.png';
    	    $logger = $this->getLogger();
	    $unique = uniqid();
	    $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
	    $dialog = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($unique, 'Serlo', 'https://de.serlo.org/datenschutz');
	    $replicationSourceId = $contentNode->getProperty('ccm:replicationsourceid');
	    $url = $contentNode->getProperty('ccm:wwwurl');
	    Config::set('urlEmbedding', $dialog.'<iframe id="'.$unique.'" style="display:none" src="https://de.serlo.org/'.$replicationSourceId.'?contentOnly&hideBreadcrumbs" width="100%" height="800px" border="0"></iframe>');
	}
    }
}
