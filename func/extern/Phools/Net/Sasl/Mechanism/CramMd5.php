<?php

/**
 * Implements mechanism "CRAM-MD5".
 *
 * @see http://tools.ietf.org/html/rfc2195
 *
 */
class Phools_Net_Sasl_Mechanism_CramMd5
extends Phools_Net_Sasl_Mechanism_Abstract
{

	/**
	 *
	 * @param string $Identity
	 * @param string $Credential
	 */
	public function __construct($Identity, $Credential)
	{
		parent::__construct('CRAM-MD5', $Identity, $Credential);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Sasl_Mechanism_Interface::authenticate()
	 */
	public function authenticate(
		Phools_Net_Sasl_Client_Interface $Client)
	{
		$Response = $Client->startSaslExchange($this->getName());
		if ( ! $Response )
		{
			return false;
		}

		$Credential = $this->getCredential();

		while( 16 > strlen($Credential) )
		{
			$Credential .= "\0";
		}

		$Data =	$this->getIdentity() . ' '
			. hash_hmac('md5', $Response, $Credential);

		$Client->sendSaslCommand($Data);
		if ( $Client->finalizeSaslExchange() )
		{
			return true;
		}

		return false;
	}

}
