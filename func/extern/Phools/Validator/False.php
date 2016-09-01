<?php

class Phools_Validator_False
extends Phools_Validator_Abstract
{
	
	const ERROR_NOT_FALSE = 'Value not false.';
	
	public function validate($Value)
	{
		$Result = true;
		
		if ( (bool) $Value )
		{
			$this->addErrorMessage(self::ERROR_NOT_FALSE);
			$Result = false;
		}
		
		$Result = $Result && parent::validate($Value);
		
		return $Result;
	}
	
}
