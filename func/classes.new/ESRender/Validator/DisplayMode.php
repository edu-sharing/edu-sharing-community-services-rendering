<?php

/**
 * Validates an application-id, e.g. "123456-abcdef-789-foo".
 *
 *
 */
class ESRender_Validator_DisplayMode
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid display-mode.')
	{
		parent::__construct(
				'/^'.ESRender_Application_Interface::DISPLAY_MODE_DOWNLOAD
				.'|'.ESRender_Application_Interface::DISPLAY_MODE_INLINE
				.'|'.ESRender_Application_Interface::DISPLAY_MODE_DYNAMIC
				.'|'.ESRender_Application_Interface::DISPLAY_MODE_WINDOW.'$/ui',
				'/', $CustomErrorMessage);
	}

}
