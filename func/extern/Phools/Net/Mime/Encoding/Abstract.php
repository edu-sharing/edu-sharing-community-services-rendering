<?php

/**
 *
 *
 */
abstract class Phools_Net_Mime_Encoding_Abstract
implements Phools_Net_Mime_Encoding_Interface
{

	/**
	 *
	 * @param string $Name
	 */
	public function __construct($Name)
	{
		$this->setName($Name);
	}

	/**
	 * Free memory
	 */
	public function __destruct()
	{
		$this->Name = null;
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
	 * @return Phools_Net_Mime_Encoding_Abstract
	 */
	protected function setName($Name)
	{
		$this->Name = (string) $Name;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->Name;
	}

}
