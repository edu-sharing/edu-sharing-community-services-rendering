<?php

/**
 * Tests if class exists.
 *
 *
 */
class Phools_Test_Php_Class_Exists
implements Phools_Test_Interface
{

	/**
	 *
	 * @param string $Class
	 */
	public function __construct($Class)
	{
		$this->setClass($Class);
	}

	/**
	 * Free memory.
	 *
	 */
	public function __destruct()
	{
		$this->Class = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Test_Interface::execute()
	 */
	public function execute()
	{
		return class_exists($this->getClass());
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Class = '';

	/**
	 *
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
	 *
	 * @return string
	 */
	protected function getClass()
	{
		return $this->Class;
	}

}
