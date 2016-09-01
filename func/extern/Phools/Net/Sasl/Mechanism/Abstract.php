<?php

/**
 * Base-class for
 *
 *
 */
abstract class Phools_Net_Sasl_Mechanism_Abstract
implements Phools_Net_Sasl_Mechanism_Interface
{

	/**
	 *
	 * @param string $Name
	 */
	public function __construct($Name, $Identity, $Credential)
	{
		$this
			->setName($Name)
			->setIdentity($Identity)
			->setCredential($Credential);
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
	 * @return Phools_Net_Sasl_Mechanism_Abstract
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

	/**
	 *
	 * @var string
	 */
	private $Identity = '';

	/**
	 *
	 * @param string $Identity
	 */
	public function setIdentity($Identity)
	{
		$this->Identity = (string) $Identity;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getIdentity()
	{
		return $this->Identity;
	}

	/**
	 *
	 * @var string
	 */
	private $Credential = '';

	/**
	 *
	 * @param string $Credential
	 */
	public function setCredential($Credential)
	{
		$this->Credential = (string) $Credential;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getCredential()
	{
		return $this->Credential;
	}

}
