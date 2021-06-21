<?php

/**
 * Validates an application-id, e.g. "alf".
 *
 *
 */
class ESRender_Validator_ApplicationId
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid application-id.')
	{
		parent::__construct('/^[a-z0-9\._\-]+$/ui', '/', $CustomErrorMessage);
	}

}
