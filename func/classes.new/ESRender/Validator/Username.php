<?php

/**
 * Validates an encrypted username, e.g. "6UQjk4tAvgg=".
 *
 *
 */
class ESRender_Validator_Username
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid username.')
	{
		// @todo validate correctly
		parent::__construct('/^.*$/ui', '/', $CustomErrorMessage);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Regex_Abstract::validate()
	 */
	public function validate($Value)
	{
		if ( ! parent::validate($Value) )
		{
			error_log('Found invalid username "'.$Value.'".');
			return false;
		}

		return true;
	}

}
