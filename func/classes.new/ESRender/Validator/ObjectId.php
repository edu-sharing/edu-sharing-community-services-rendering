<?php

/**
 * Validates an application-id, e.g. "123456-abcdef-789-foo".
 *
 *
 */
class ESRender_Validator_ObjectId
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid object-id.')
	{
		parent::__construct('/^[a-z0-9\-]+$/ui', '/', $CustomErrorMessage);
	}

}
