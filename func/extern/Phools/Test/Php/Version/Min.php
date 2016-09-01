<?php

/**
 *
 *
 *
 */
class Phools_Test_Php_Version_Min
implements Phools_Test_Interface
{

	/**
	 *
	 * @param string $MinVersion
	 */
	public function __construct($MinVersion)
	{
		$this->setMinVersion($MinVersion);
	}

	public function __destruct()
	{
		$this->MinVersion = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Test_Interface::execute()
	 */
	public function execute()
	{
		return version_compare(phpversion(), $this->getMinVersion(), '>=');
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $MinVersion = '';

	/**
	 *
	 *
	 * @param string $MinVersion
	 * @return Phools_Test_Php_MinVersion
	 */
	public function setMinVersion($MinVersion)
	{
		$this->MinVersion = (string) $MinVersion;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getMinVersion()
	{
		return $this->MinVersion;
	}

}
