<?php

/**
 *
 *
 *
 */
class Phools_Validator_Locale_LanguageCode
extends Phools_Validator_Regex_Abstract
{

	/**
	 *
	 * @param string $CustomErrorMessage
	 */
	public function __construct($CustomErrorMessage = 'Invalid language-code.')
	{
		parent::__construct('/^[a-z]{1,3}$/iu', '/', $CustomErrorMessage);
	}

}
