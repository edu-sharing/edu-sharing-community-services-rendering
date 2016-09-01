<?php

/**
 * Implements mechanism "PLAIN".
 *
 *
 */
class Phools_Net_Sasl_Mechanism_Plain
extends Phools_Net_Sasl_Mechanism_Abstract
{

	/**
	 *
	 * @param string $Identity
	 * @param string $Credential
	 */
	public function __construct($Identity, $Credential)
	{
		parent::__construct('PLAIN', $Identity, $Credential);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Sasl_Mechanism_Interface::authenticate()
	 */
	public function authenticate(
		Phools_Net_Sasl_Client_Interface $Client)
	{
		$Data = $this->getIdentity()
			 . "\0" . $this->getIdentity()
			 . "\0" . $this->getCredential();

		if ( ! $Client->startSaslExchange('PLAIN', $Data) )
		{
			return false;
		}

		return true;
	}

}
