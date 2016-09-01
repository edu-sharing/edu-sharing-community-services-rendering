<?php

/**
 * Validates an theme-name, e.g. "default" or "green".
 *
 *
 */
class ESRender_Validator_Theme
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid theme-id.')
	{
		parent::__construct('/^[a-z0-9]+$/ui', '/', $CustomErrorMessage);
	}

}
