<?php

/**
 * Validates a session-id, e.g. "charsAnd123numbers456foransessionidentifier".
 *
 *
 */
class ESRender_Validator_SessionId
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid session-id.')
	{
		parent::__construct('/^[a-z0-9:_\-\.]+$/ui', '/', $CustomErrorMessage);
	}

}
