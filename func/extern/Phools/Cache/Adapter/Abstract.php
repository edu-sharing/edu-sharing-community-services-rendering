<?php

/**
 *
 *
 *
 */
abstract class Phools_Cache_Adapter_Abstract
implements Phools_Cache_Adapter_Interface
{

	/**
	 *
	 * @param string $Prefix
	 */
	public function __construct($Prefix = '')
	{
		$this->setPrefix($Prefix);
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Prefix = '';

	/**
	 *
	 *
	 * @param string $Prefix
	 * @return Phools_Cache_Adapter_Abstract
	 */
	public function setPrefix(string $Prefix)
	{
		$this->Prefix = $Prefix;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getPrefix()
	{
		return $this->Prefix;
	}

}
