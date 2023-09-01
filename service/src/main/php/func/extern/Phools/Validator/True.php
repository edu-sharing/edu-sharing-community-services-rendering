<?php

/**
 *
 *
 *
 */
class Phools_Validator_True
extends Phools_Validator_Abstract
{

	/**
	 *
	 * @var string
	 */
	const ERROR_NOT_TRUE = 'Value not true.';

	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Abstract::validate()
	 */
	public function validate($Value)
	{
		$Result = true;

		if ( ! (bool) $Value )
		{
			$this->setErrorMessage(self::ERROR_NOT_TRUE);
			$Result = false;
		}

		$Result = $Result && parent::validate($Value);

		return $Result;
	}

}
