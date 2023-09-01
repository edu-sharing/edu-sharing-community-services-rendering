<?php

/**
 * 
 * 
 *
 */
class Phools_Validator_GreaterThan
extends Phools_Validator_Abstract
{
	
	/**
	 * Error-message to show when value NOT lesser then given max-value;
	 * 
	 * @var string
	 */
	const ERROR_NOT_GREATER_THAN = 'Value not greater than';
	
	/**
	 * 
	 * @param mixed $MinValue
	 */
	public function __construct($MinValue)
	{
		$this->setMinValue($MinValue);
	}
	
	public function __destruct()
	{
		$this->MinValue = null;
		
		parent::__destruct();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Abstract::validate()
	 */
	public function validate($Value)
	{
		$Result = true;
		
		if ( $this->getMinValue() >= $Value )
		{
			$this->addErrorMessage(self::ERROR_NOT_GREATER_THAN);
			$Result = false;
		}

		$Result = $Result && parent::validate($Value);

		return $Result;
	}

	/**
	 *
	 *
	 * @var mixed
	 */
	protected $MinValue = 0;
	
	/**
	 *
	 *
	 * @param mixed $MinValue
	 * @return Phools_Validator_GreaterThan
	 */
	public function setMinValue($MinValue)
	{
		$this->MinValue = $MinValue;
		return $this;
	}
	
	/**
	 *
	 * @return mixed
	 */
	protected function getMinValue()
	{
		return $this->MinValue;
	}
	
}
