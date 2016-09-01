<?php

/**
 *
 *
 *
 */
abstract class Phools_Auth_Adapter_Abstract
implements Phools_Auth_Adapter_Interface
{

	/**
	 *
	 * @param Phools_Hashing_Interface $Hashing
	 */
	public function __construct(Phools_Hashing_Interface $Hashing = null)
	{
		$this->setHashing($Hashing);
	}

	/**
	 * Garbage-collect hashing.
	 */
	public function __destruct()
	{
		$this->Hashing = null;
	}

	/**
	 *
	 *
	 * @var Phools_Hashing_Interface
	 */
	private $Hashing = null;

	/**
	 * Set a hashing-method to use.
	 *
	 * @param Phools_Hashing_Interface $Hashing
	 * @return Phools_Auth_Adapter_Abstract
	 */
	public function setHashing(Phools_Hashing_Interface $Hashing = null)
	{
		$this->Hashing = $Hashing;
		return $this;
	}

	/**
	 *
	 * @return Phools_Hashing_Interface
	 */
	protected function getHashing()
	{
		return $this->Hashing;
	}

}
