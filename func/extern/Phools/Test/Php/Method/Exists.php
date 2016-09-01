<?php

/**
 * Test if a class provides a certain method.
 *
 *
 */
class Phools_Test_Php_Method_Exists
implements Phools_Test_Interface
{

	/**
	 *
	 *
	 * @param string $Class Name of the class which must provide $Method.
	 * @param string $Method The method to be provided by given $Class.
	 */
	public function __construct($Class, $Method)
	{
		$this
			->setClass($Class)
			->setMethod($Method);
	}

	/**
	 * Free memory.
	 *
	 */
	public function __destruct()
	{
		$this->Method = null;
		$this->Class = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Test_Interface::execute()
	 */
	public function execute()
	{
		return method_exists($this->getClass(), $this->getMethod());
	}

	/**
	 * Store the classname.
	 *
	 * @var string
	 */
	protected $Class = '';

	/**
	 * Set the class which is to be tested for providing method.
	 *
	 * @param string $Class
	 * @return Phools_Test_Php_Class_Exists
	 */
	public function setClass($Class)
	{
		$this->Class = (string) $Class;
		return $this;
	}

	/**
	 * Get the class' name to test against.
	 *
	 * @return string
	 */
	protected function getClass()
	{
		return $this->Class;
	}

	/**
	 * The method's name.
	 *
	 * @var string
	 */
	protected $Method = '';

	/**
	 * Set the method which is to be provided by class.
	 *
	 * @param string $Method
	 * @return Phools_Test_Php_Method_Exists
	 */
	public function setMethod($Method)
	{
		$this->Method = (string) $Method;
		return $this;
	}

	/**
	 * Get the method-name to be tested.
	 *
	 * @return string
	 */
	protected function getMethod()
	{
		return $this->Method;
	}

}
