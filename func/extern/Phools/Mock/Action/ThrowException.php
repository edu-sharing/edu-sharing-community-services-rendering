<?php

/**
 *
 *
 */
class Phools_Mock_Action_ThrowException
extends Phools_Mock_Action_Abstract
{

	/**
	 *
	 * @param Exception $Exception
	 */
	public function __construct(Exception $Exception)
	{
		$this->setException($Exception);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Exception = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Abstract::onCall()
	 */
	public function onCall($Method, array $Arguments = array())
	{
		throw $this->getException();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Abstract::onGet()
	 */
	public function onGet($Property)
	{
		throw $this->getException();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Mock_Action_Abstract::onSet()
	 */
	public function onSet($Property, $Exception)
	{
		throw $this->getException();
	}

	/**
	 *
	 * @var mixed
	 */
	protected $Exception = null;

	/**
	 *
	 * @param Exception $Exception
	 * @return Phools_Mock_Action_ReturnException
	 */
	public function setException(Exception $Exception)
	{
		$this->Exception = $Exception;
		return $this;
	}

	/**
	 *
	 * @return Exception
	 */
	public function getException()
	{
		return $this->Exception;
	}

}
