<?php

/**
 * Implementing mechanism "LOGIN".
 *
 *
 */
class Phools_Net_Sasl_Mechanism_Login
extends Phools_Net_Sasl_Mechanism_Abstract
{

	/**
	 *
	 * @param string $Identity
	 * @param string $Credential
	 */
	public function __construct($Identity, $Credential)
	{
		parent::__construct('LOGIN', $Identity, $Credential);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Sasl_Mechanism_Interface::authenticate()
	 */
	public function authenticate(
		Phools_Net_Sasl_Client_Interface $Client)
	{
		$Response = $Client->startSaslExchange('LOGIN');
		if ( 'Username:' != $Response )
		{
			throw new Phools_Net_Smtp_Exception('Unhandled response "'.$Response.'"');
		}

		$Client->sendSaslCommand($this->getIdentity());

		$Response = $Client->readSaslResponse();
		if ( 'Password:' != $Response )
		{
			throw new Phools_Net_Smtp_Exception('Unhandled response "'.$Response.'"');
		}

		$Client->sendSaslCommand($this->getCredential());

		$Result = $Client->finalizeSaslExchange();

		return $Result;
	}

}
