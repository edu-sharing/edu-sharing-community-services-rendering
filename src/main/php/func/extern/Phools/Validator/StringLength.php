<?php

class Phools_Validator_StringLength
extends Phools_Validator_Abstract
{
	
	const ERROR_STRING_LONGER_THAN = 'String longer than';
	const ERROR_STRING_SHORTER_THAN = 'String shorter than';
	
	public function __construct($MaxLength, $MinLength = 0)
	{
		$this->setMaxLength($MaxLength)
			->setMinLength($MinLength);
	}
	
	public function validate($Value)
	{
		$Result = true;
		
		if ( $this->getMaxLength() < strlen($Value) )
		{
			$this->addErrorMessage(self::ERROR_STRING_LONGER_THAN);
			$Result = false;
		}
		
		if ( $this->getMinLength() > strlen($Value) )
		{
			$this->addErrorMessage(self::ERROR_STRING_SHORTER_THAN);
			$Result = false;
		}
		
		$Result = $Result && parent::validate($Value);
		
		return $Result;
	}
	
	/**
	 *
	 *
	 * @var int
	 */
	protected $MaxLength = 0;
	
	/**
	 *
	 *
	 * @param int $MaxLength
	 * @return Phools_Validator_StringLength
	 */
	public function setMaxLength($MaxLength)
	{
		$this->MaxLength = (int) $MaxLength;
		return $this;
	}
	
	/**
	 *
	 * @return int
	 */
	protected function getMaxLength()
	{
		return $this->MaxLength;
	}
	
	/**
	 *
	 *
	 * @var int
	 */
	protected $MinLength = 0;
	
	/**
	 *
	 *
	 * @param int $MinLength
	 * @return Phools_Validator_StringLength
	 */
	public function setMinLength($MinLength)
	{
		$this->MinLength = (int) $MinLength;
		return $this;
	}
	
	/**
	 *
	 * @return int
	 */
	protected function getMinLength()
	{
		return $this->MinLength;
	}
	
}
