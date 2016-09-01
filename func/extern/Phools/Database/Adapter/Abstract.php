<?php

/**
 *
 *
 *
 */
abstract class Phools_Database_Adapter_Abstract
implements Phools_Database_Adapter_Interface
{

	/**
	 *
	 * @param string $Database
	 */
	public function __construct($Database)
	{
		$this->setDatabase($Database);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Database = null;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Database = '';

	/**
	 *
	 * @param string $Database
	 */
	public function setDatabase($Database)
	{
		assert( is_string($Database) );

		$this->Database = (string) $Database;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getDatabase()
	{
		return $this->Database;
	}

	/**
	 *
	 *
	 * @var resource
	 */
	protected $ConnectionResource = null;

	/**
	 *
	 *
	 * @param resource $ConnectionResource
	 *
	 * @return Phools_Database_Adapter_Abstract
	 */
	protected function setConnectionResource(&$ConnectionResource)
	{
		assert( is_resource($ConnectionResource) );

		$this->ConnectionResource = $ConnectionResource;
		return $this;
	}

	/**
	 *
	 * @return resource
	 */
	protected function &getConnectionResource()
	{
		return $this->ConnectionResource;
	}

}
