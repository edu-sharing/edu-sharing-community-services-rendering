<?php

/**
 *
 *
 *
 */
class ESRender_LicenseFactory_Implementation
implements ESRender_LicenseFactory_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see ESRender_LicenseFactory_Interface::getLicense()
	 */
	public function getLicense($Name, $Author, $permaLink = '', $fileName = '')
	{
		$License = null;

		$IconUrl = MC_URL . '/theme/default/license/';
		switch( strtoupper($Name) )
		{
			case ESRender_License_Interface::COMMON_LICENSE_CUSTOM:
                $Url = NULL;
				$IconUrl = NULL;
				$License = new ESRender_License_Edusharing('custom license', $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::COMMON_LICENSE_EDU_NC:
                $Url = 'http://edu-sharing.net/portal/web/edu-sharing.net/licenses';
				$IconUrl .= 'edu-sharing/' . strtolower(ESRender_License_Interface::COMMON_LICENSE_EDU_NC) . '.svg';
				$License = new ESRender_License_Edusharing($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::COMMON_LICENSE_EDU_NC_ND:
                $Url = 'http://edu-sharing.net/portal/web/edu-sharing.net/licenses';
				$IconUrl .= 'edu-sharing/' . strtolower(ESRender_License_Interface::COMMON_LICENSE_EDU_NC_ND) . '.svg';
				$License = new ESRender_License_Edusharing($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::COMMON_LICENSE_EDU_P_NR:
                $Url = 'http://edu-sharing.net/portal/web/edu-sharing.net/licenses';
				$IconUrl .= 'edu-sharing/' . strtolower(ESRender_License_Interface::COMMON_LICENSE_EDU_P_NR) . '.svg';
				$License = new ESRender_License_Edusharing($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::COMMON_LICENSE_EDU_P_NR_ND:
                $Url = 'http://edu-sharing.net/portal/web/edu-sharing.net/licenses';
				$IconUrl .= 'edu-sharing/' . strtolower(ESRender_License_Interface::COMMON_LICENSE_EDU_P_NR_ND) . '.svg';
				$License = new ESRender_License_Edusharing($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::CC_BY:
				$Url = 'http://creativecommons.org/licenses/by/3.0/';
				$IconUrl .= 'creative_commons/by.png';
				$License = new ESRender_License_CreativeCommons($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::CC_BY_NC:
				$Url = 'http://creativecommons.org/licenses/by-nc/3.0/';
				$IconUrl .= 'creative_commons/by-nc.png';
				$License = new ESRender_License_CreativeCommons($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::CC_BY_NC_ND:
				$Url = 'http://creativecommons.org/licenses/by-nc-nd/3.0/';
				$IconUrl .= 'creative_commons/by-nc-nd.png';
				$License = new ESRender_License_CreativeCommons($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::CC_BY_NC_SA:
				$Url = 'http://creativecommons.org/licenses/by-nc-sa/3.0/';
				$IconUrl .= 'creative_commons/by-nc-sa.png';
				$License = new ESRender_License_CreativeCommons($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::CC_BY_ND:
				$Url = 'http://creativecommons.org/licenses/by-nd/3.0/';
				$IconUrl .= 'creative_commons/by-nd.png';
				$License = new ESRender_License_CreativeCommons($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
				break;

			case ESRender_License_Interface::CC_BY_SA: 				     
    			$Url = 'http://creativecommons.org/licenses/by-sa/3.0/'; 				 
    			$IconUrl .='creative_commons/by-sa.png'; 			     
	   	        $License = new ESRender_License_CreativeCommons($Name, $Author, $IconUrl, $Url, $permaLink, $fileName);
	   	        break;

			default:
				error_log('Unhandled or unknown license.');
				return false;
		}

		return $License;
	}

}
