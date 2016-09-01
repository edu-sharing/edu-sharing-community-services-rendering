<?php

/**
 * 
 * 
 *
 */
class Phools_Validator_LesserThan
extends Phools_Validator_Abstract
{
	
	/**
	 * Error-message to show when value NOT lesser then given max-value;
	 * 
	 * @var string
	 */
	const ERROR_NOT_LESSER_THAN = 'Value not lesser than';
	
	/**
	 * 
	 * @param mixed $MaxValue
	 */
	public function __construct($MaxValue)
	{
		$this->setMaxValue($MaxValue);
	}
	
	public function __destruct()
	{
		$this->MaxValue = null;
		
		parent::__destruct();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Phools_Validator_Abstract::validate()
	 */
	public function validate($Value)
	{
		$Result = true;
		
		if ( $this->getMaxValue() <= $Value )
		{
			$this->addErrorMessage(self::ERROR_NOT_LESSER_THAN);
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
	protected $MaxValue = 0;
	
	/**
	 *
	 *
	 * @param mixed $MaxValue
	 * @return Phools_Validator_GreaterThan
	 */
	public function setMaxValue($MaxValue)
	{
		$this->MaxValue = $MaxValue;
		return $this;
	}
	
	/**
	 *
	 * @return mixed
	 */
	protected function getMaxValue()
	{
		return $this->MaxValue;
	}
	
}
