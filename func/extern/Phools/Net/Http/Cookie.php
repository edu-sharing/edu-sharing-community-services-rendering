<?php

/**
 *
 *
 *
 */
class Phools_Net_Http_Cookie
{

	/**
	 *
	 * @param string $Name
	 * @param string $Value
	 * @param string $ExpirationTimestamp
	 * @param string $Path
	 * @param string $Domain
	 */
	public function __construct(
		$Name,
		$Value,
		$ExpirationTimestamp = 0,
		$Path = '/',
		$Domain = '')
	{
		$this->setName($Name)
			->setValue($Value)
			->setExpirationTimestamp($ExpirationTimestamp)
			->setPath($Path)
			->setDomain($Domain);
	}

	/**
	 *
	 * @return bool
	 */
	public function set()
	{
		if ( headers_sent() )
		{
			return false;
		}

		setcookie(
			$this->getName(),
			$this->getValue(),
			$this->getExpirationTimestamp(),
			$this->getPath(),
			$this->getDomain());

		return true;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Name = '';

	/**
	 *
	 *
	 * @param string $Name
	 * @return Phools_Net_Http_Cookie
	 */
	public function setName($Name)
	{
		$this->Name = (string) $Name;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getName()
	{
		return $this->Name;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Value = '';

	/**
	 *
	 *
	 * @param string $Value
	 * @return Phools_Net_Http_Cookie
	 */
	public function setValue($Value)
	{
		$this->Value = (string) $Value;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getValue()
	{
		return $this->Value;
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $ExpirationTimestamp = 0;

	/**
	 *
	 *
	 * @param int $ExpirationTimestamp
	 * @return Phools_Net_Http_Cookie
	 */
	public function setExpirationTimestamp($ExpirationTimestamp)
	{
		$this->ExpirationTimestamp = (int) $ExpirationTimestamp;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getExpirationTimestamp()
	{
		return $this->ExpirationTimestamp;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Path = '';

	/**
	 *
	 *
	 * @param string $Path
	 * @return Phools_Net_Http_Cookie
	 */
	public function setPath($Path)
	{
		$this->Path = (string) $Path;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getPath()
	{
		return $this->Path;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Domain = '';

	/**
	 *
	 *
	 * @param string $Domain
	 * @return Phools_Net_Http_Cookie
	 */
	public function setDomain($Domain)
	{
		$this->Domain = (string) $Domain;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getDomain()
	{
		return $this->Domain;
	}

}
