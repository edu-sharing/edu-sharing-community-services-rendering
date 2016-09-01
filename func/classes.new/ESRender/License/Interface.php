<?php

/**
 *
 *
 */
interface ESRender_License_Interface
{

	/**
	 * License-constants
	 *
	 * @var string
	 */
	const COMMON_LICENSE_CUSTOM = 'CUSTOM';
	const COMMON_LICENSE_EDU_NC = 'EDU_NC';
	const COMMON_LICENSE_EDU_NC_ND = 'EDU_NC_ND';
	const COMMON_LICENSE_EDU_P_NR = 'EDU_P_NR';
	const COMMON_LICENSE_EDU_P_NR_ND = 'EDU_P_NR_ND';

	const CC_BY = 'CC_BY';
	const CC_BY_NC = 'CC_BY_NC';
	const CC_BY_NC_ND = 'CC_BY_NC_ND';
	const CC_BY_NC_SA = 'CC_BY_NC_SA';
	const CC_BY_ND = 'CC_BY_ND';
	const CC_BY_SA = 'CC_BY_SA';

	/**
	 *
	 * @param string $Author
	 * @param Phools_Template_Interface $Template
	 *
	 * @return string
	 */
	public function renderFooter(Phools_Template_Interface $Template);

}
