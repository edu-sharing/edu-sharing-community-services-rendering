<?php

/**
 * Tests if function exists.
 *
 *
 */
class Phools_Test_Php_Function_Exists
implements Phools_Test_Interface
{

	/**
	 *
	 * @param string $Function
	 */
	public function __construct($Function)
	{
		$this->setFunction($Function);
	}

	/**
	 * Free memory.
	 *
	 */
	public function __destruct()
	{
		$this->Function = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Test_Interface::execute()
	 */
	public function execute()
	{
		return function_exists($this->getFunction());
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Function = '';

	/**
	 *
	 *
	 * @param string $Function
	 * @return Phools_Test_Php_Function_Exists
	 */
	public function setFunction($Function)
	{
		$this->Function = (string) $Function;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getFunction()
	{
		return $this->Function;
	}

}
