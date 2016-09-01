<?php

/**
 * Validates an version-info, e.g. "1.3".
 *
 *
 */
class ESRender_Validator_Version
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid version-info.')
	{
		// @todo validate correctly
        parent::__construct('', '/', $CustomErrorMessage);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Regex_Abstract::validate()
	 */
	public function validate($Value)
	{
		if (!is_numeric($Value) )
		{
			error_log('Found invalid version-info "'.$Value.'".');
			return false;
		}

		return true;
	}

}
