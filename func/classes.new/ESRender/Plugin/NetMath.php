<?php

/**
 *Handle NetMath Materials
 *
 *
 */
class ESRender_Plugin_NetMath
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
        if($contentNode->getProperty('{http://www.campuscontent.de/model/1.0}replicationsource') === 'NetMath') {
            $this -> iconUrl = $remote_rep->prop_array['clientprotocol'] .'://' . $remote_rep->prop_array['domain'] . ':' . $remote_rep->prop_array['clientport'] . '/edu-sharing/assets/images/sources/netmath.png';
            $logger = $this->getLogger();
            $unique = uniqid();
            $dataProtectionRegulationHandler = new ESRender_DataProtectionRegulation_Handler();
            $dialog = $dataProtectionRegulationHandler->getApplyDataProtectionRegulationsDialog($unique, 'NetMath', 'https://www.netmath.de/datenschutzerklaerung/');
            $url = $contentNode->getProperty('{http://www.campuscontent.de/model/1.0}wwwurl');
            Config::set('urlEmbedding', $dialog.'<iframe id="'.$unique.'" style="display:none; background:#fff" src="'.$url.'" width="100%" height="800px" border="0"></iframe>');
        }
    }
}
