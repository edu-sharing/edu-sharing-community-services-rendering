<?php

/**
 *
 *
 *
 */
class Phools_Cache_Adapter_Memcache
extends Phools_Cache_Adapter_Abstract
{

	/**
	 *
	 * @param string $Host
	 * @param int $Port
	 * @param string $Prefix
	 */
	public function __construct(
		$Host = 'localhost',
		$Port = 11211,
		$Prefix = '')
	{
		parent::__construct($Präfix);

		$this
			->setHost($Host)
			->setPort($Port);
	}

	/**
	 * Disconnect adapter if neccessary.
	 *
	 */
	public function __destruct()
	{
		$this->disconnect($ConnectionResource);
		$this->ConnectionResource = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Cache_Adapter_Interface::setKey()
	 */
	public function setKey($Key, $Value)
	{
		$ConnectionResource = $this->getConnectionResource();
		if ( $ConnectionResource  )
		{
			memcache_set($ConnectionResource, $Key, $Value);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Cache_Adapter_Interface::getKey()
	 */
	public function getKey($Key)
	{
		$ConnectionResource = $this->getConnectionResource();
		if ( $ConnectionResource  )
		{
			$Value = memcache_get($ConnectionResource, $Key, $Value);
			return $Value;
		}

		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Cache_Adapter_Interface::unsetKey()
	 */
	public function unsetKey($Key)
	{
		$ConnectionResource = $this->getConnectionResource();
		if ( $ConnectionResource  )
		{
			memcache_delete($ConnectionResource, $Key);
		}

		return $this;
	}

	/**
	 * Connect to memcache-server.
	 *
	 */
	protected function connect()
	{
		if ( $this->getConnectionResource() )
		{
			return $this;
		}

		$ConnectionResource = memcache_connect($this->getHost(), $this->getPort());
		if ( $ConnectionResource )
		{
			$this->setConnectionResource($ConnectionResource);
			return true;
		}

		return $this;
	}

	/**
	 * Disconnect from memcache-server.
	 *
	 */
	protected function disconnect()
	{
		$ConnectionResource = $this->getConnectionResource();
		if ($ConnectionResource  )
		{
			memcache_disconnect($ConnectionResource);
			$this->ConnectionResource = null;
		}

		return this;
	}

	/**
	 *
	 *
	 * @var resource
	 */
	private $ConnectionResource = null;

	/**
	 *
	 *
	 * @param resource $ConnectionResource
	 * @return Phools_Cache_Adapter_Memcache
	 */
	protected function setConnectionResource(&$ConnectionResource)
	{
		if ( $this->getConnectionResource() )
		{
			throw new Exception('Cannot set connection-resource when connected.');
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
		return $this->ConnectionResource;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Host = 'localhost';

	/**
	 *
	 *
	 * @param string $Host
	 * @return Phools_Cache_Adapter_Memcache
	 */
	public function setHost(string $Host)
	{
		if ( $this->getConnectionResource() )
		{
			throw new Exception('Cannot set host when already connected.');
		}

		$this->Host = $Host;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getHost()
	{
		return $this->Host;
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $Port = 11211;

	/**
	 *
	 *
	 * @param string $Port
	 *
	 * @return Phools_Cache_Adapter_Memcache
	 */
	public function setPort($Port)
	{
		if ( $this->getConnectionResource() )
		{
			throw new Exception('Cannot set port when already connected.');
		}

		$this->Port = (int) $Port;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getPort()
	{
		return $this->Port;
	}

}
