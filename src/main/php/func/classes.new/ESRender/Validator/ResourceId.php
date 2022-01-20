<?php

/**
 *
 *
 */
class ESRender_Validator_ResourceId
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid resource-id.')
	{
		parent::__construct('/^[a-z0-9-_]+$/ui', '/', $CustomErrorMessage);
	}

}
