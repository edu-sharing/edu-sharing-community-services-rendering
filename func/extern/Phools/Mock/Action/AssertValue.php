<?php

/**
 *
 *
 */
class Phools_Mock_Action_AssertValue
extends Phools_Mock_Action_Abstract
{

	/**
	 *
	 * @param mixed $Value
	 */
	public function __construct($Value)
	{
		$this->setValue($Value);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Value = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Abstract::onSet()
	 */
	public function onSet($Property, $Value)
	{
		if ( $this->getValue() !== $Value )
		{
			return false;
		}

		return true;
	}

	/**
	 *
	 * @var mixed
	 */
	protected $Value = null;

	/**
	 *
	 * @param mixed $Value
	 *
	 * @return Phools_Mock_Action_ReturnValue
	 */
	public function setValue($Value)
	{
		$this->Value = $Value;
		return $this;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->Value;
	}

}
