<?php

/**
 * Tests if extension exists.
 *
 *
 */
class Phools_Test_Php_Extension_Exists
implements Phools_Test_Interface
{

	/**
	 *
	 * @param string $Extension
	 */
	public function __construct($Extension)
	{
		$this->setExtension($Extension);
	}

	/**
	 * Free memory.
	 *
	 */
	public function __destruct()
	{
		$this->Extension = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Test_Interface::execute()
	 */
	public function execute()
	{
		return extension_loaded($this->getExtension());
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Extension = '';

	/**
	 *
	 *
	 * @param string $Extension
	 * @return Phools_Test_Php_Extension_Exists
	 */
	public function setExtension($Extension)
	{
		$this->Extension = (string) $Extension;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getExtension()
	{
		return $this->Extension;
	}

}
