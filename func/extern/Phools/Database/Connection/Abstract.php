<?php

abstract class Phools_Database_Connection_Abstract
implements Phools_Database_Connection_Interface
{

	/**
	 *
	 * @param string $Database
	 * @param string $Username
	 * @param string $Password
	 */
	public function __construct($Database, $Username = '', $Password = '')
	{
		$this
			->setDatabase($Database)
			->setUsername($Username)
			->setPassword($Password);
	}

	/**
	 * Disconnect if required.
	 *
	 */
	public function __destruct()
	{
		$ConnectionResource = $this->getConnectionResource();
		if ( $ConnectionResource )
		{
			$this->getAdapter()->disconnect($ConnectionResource);
		}

		$this->ConnectionResource = null;
		$this->Adapter = null;
	}

	/**
	 * Protect yourself.
	 *
	 * @throws Phools_Database_Exception
	 */
	public function __clone()
	{
		throw new Phools_Database_Exception('Results cannot be cloned.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::open()
	 */
	public function open(Phools_Database_Adapter_Interface &$Adapter)
	{
		$ConnectionResource = $Adapter->connect(
			$this->getDatabase(),
			$this->getUsername(),
			$this->getPassword());

		$this
			->setAdapter($Adapter)
			->setConnectionResource($ConnectionResource);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::close()
	 */
	public function close()
	{
		$Adapter = $this->getAdapter();
		$Adapter->disconnect($ConnectionResource);

		$this->setConnectionResource(null);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::isEstablished()
	 */
	public function isEstablished()
	{
		if ( $this->ConnectionResource )
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
	private $Database = '';

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::setDatabase()
	 */
	public function setDatabase($Database)
	{
		if ( $this->isEstablished() )
		{
			throw new Phools_Database_Exception('Cannot change database on established connection.');
		}

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
	 * @var string
	 */
	private $Username = '';

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::setUsername()
	 */
	public function setUsername($Username)
	{
		if ( $this->isEstablished() )
		{
			throw new Phools_Database_Exception('Cannot change username on established connection.');
		}

		$this->Username = (string) $Username;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUsername()
	{
		return $this->Username;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Password = '';

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::setPassword()
	 */
	public function setPassword($Password)
	{
		if ( $this->isEstablished() )
		{
			throw new Phools_Database_Exception('Cannot change password on established connection.');
		}

		$this->Password = (string) $Password;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getPassword()
	{
		return $this->Password;
	}

	/**
	 *
	 *
	 * @var Phools_Database_Adapter_Interface
	 */
	private $Adapter = null;

	/**
	 *
	 *
	 * @param Phools_Database_Adapter_Interface $Adapter
	 * @return Phools_Database_Connection_Abstract
	 */
	protected function setAdapter(Phools_Database_Adapter_Interface &$Adapter)
	{
		$this->Adapter = $Adapter;
		return $this;
	}

	/**
	 *
	 * @return Phools_Database_Adapter_Interface
	 */
	protected function &getAdapter()
	{
		if ( ! $this->Adapter )
		{
			throw new Phools_Database_Exception('No adapter set.');
		}

		return $this->Adapter;
	}

	/**
	 *
	 *
	 * @var resource
	 */
	private $ConnectionResource = '';

	/**
	 *
	 * @param resource $ConnectionResource
	 * @return Phools_Database_Connection_Abstract
	 */
	protected function setConnectionResource(&$ConnectionResource)
	{
		if ( ! is_resource($ConnectionResource) )
		{
			throw new Phools_Database_Exception('Connection-resource is not a resource-type.');
		}

		$this->ConnectionResource = $ConnectionResource;
		return $this;
	}

	/**
	 *
	 * @return resource
	 */
	protected function &getConnectionResource()
	{
		if ( ! $this->ConnectionResource )
		{
			throw new Phools_Database_Exception('No connection-resource set.');
		}

		return $this->ConnectionResource;
	}

}
