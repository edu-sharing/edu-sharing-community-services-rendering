<?php

/**
 * Validates a PHP-session-id.
 *
 *
 */
class Phools_Validator_SessionId
extends Phools_Validator_RegEx_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid session-id.')
	{
		parent::__construct('/[a-z]{32}/iu', '/', $CustomErrorMessage);
	}

}
