<?php

/**
 * Validates an application-id, e.g. "3".
 *
 *
 */
class ESRender_Validator_CourseId
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid course-id.')
	{
		parent::__construct('/^[a-z0-9]+$/ui', '/', $CustomErrorMessage);
	}

}
