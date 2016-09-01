<?php

/**
 *
 *
 */
class Phools_Mock_Action_AssertInstanceOf
extends Phools_Mock_Action_Abstract
{

	/**
	 *
	 * @param mixed $Interface
	 */
	public function __construct($Interface)
	{
		$this->setInterface($Interface);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Interface = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Abstract::onSet()
	 */
	public function onSet($Property, $Value)
	{
		$Interface = $this->getInterface();
		if ( $Value instanceof $Interface )
		{
			return true;
		}

		return false;
	}

	/**
	 *
	 * @var mixed
	 */
	protected $Interface = null;

	/**
	 *
	 * @param mixed $Interface
	 * @return Phools_Mock_Action_AssertInstanceOf
	 */
	public function setInterface($Interface)
	{
		$this->Interface = $Interface;
		return $this;
	}

	/**
	 *
	 * @return mixed
	 */
	public function getInterface()
	{
		return $this->Interface;
	}

}
