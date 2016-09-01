<?php

/**
 *
 *
 */
class Phools_Mock_Action_AssertArgument
extends Phools_Mock_Action_Abstract
{

	/**
	 *
	 * @param mixed $Name
	 * @param mixed $Value
	 */
	public function __construct($Name, $Value)
	{
		$this
			->setName($Name)
			->setValue($Value);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Value = null;
		$this->Name = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Abstract::onCall()
	 */
	public function onCall($Method, array $Arguments = array())
	{
		if ( $this->getValue() === $Arguments[$this->getName()] )
		{
			return true;
		}

		return false;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Name = '';

	/**
	 *
	 *
	 * @Name string|int $Name
	 * @return Phools_Mock_Action_AssertArgument
	 */
	public function setName($Name)
	{
		assert( is_string($Name) || is_int($Name) );

		$this->Name = $Name;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getName()
	{
		return $this->Name;
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
