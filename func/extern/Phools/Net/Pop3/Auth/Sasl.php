<?php

/**
 *
 *
 *
 */
abstract class Phools_Net_Pop3_Auth_Sasl
implements
	Phools_Net_Pop3_Auth_Interface,
	Phools_Net_Sasl_Client_Interface
{

	/**
	 *
	 * @param Phools_Net_Sasl_Mechanism_Interface $SaslMechanism
	 */
	public function __construct(Phools_Net_Sasl_Mechanism_Interface $SaslMechanism)
	{
		$this->setSaslMechanism($SaslMechanism);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->SaslMechanism = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Auth_Interface::authenticate()
	 */
	public function authenticate($Connection, $Username, $Password)
	{

	}

	/**
	 *
	 *
	 * @var Phools_Net_Sasl_Mechanism_Interface
	 */
	protected $SaslMechanism = null;

	/**
	 *
	 *
	 * @param Phools_Net_Sasl_Mechanism_Interface $SaslMechanism
	 * @return Phools_Net_Pop3_Auth_Sasl
	 */
	public function setSaslMechanism(Phools_Net_Sasl_Mechanism_Interface $SaslMechanism)
	{
		$this->SaslMechanism = $SaslMechanism;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Sasl_Mechanism_Interface
	 */
	protected function getSaslMechanism()
	{
		return $this->SaslMechanism;
	}

}
