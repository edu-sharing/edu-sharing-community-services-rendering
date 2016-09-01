<?php

/**
 * Tests if current php-version is lower than given max.
 *
 *
 */
class Phools_Test_Php_Version_Max
implements Phools_Test_Interface
{

	/**
	 *
	 * @param string $MaxVersion
	 */
	public function __construct($MaxVersion)
	{
		$this->setMaxVersion($MaxVersion);
	}

	public function __destruct()
	{
		$this->MaxVersion = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Test_Interface::execute()
	 */
	public function execute()
	{
		return version_compare(phpversion(), $this->getMaxVersion(), '<=');
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $MaxVersion = '';

	/**
	 *
	 *
	 * @param string $MaxVersion
	 * @return Phools_Test_Php_MaxVersion
	 */
	public function setMaxVersion($MaxVersion)
	{
		$this->MaxVersion = (string) $MaxVersion;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getMaxVersion()
	{
		return $this->MaxVersion;
	}

}
