<?php

/**
 * Interface defines methods to be provided by clients wishing to
 * faciliate a SASL-mechanism for authentication.
 *
 *
 */
interface Phools_Net_Sasl_Client_Interface
{

	/**
	 * Return initial response if $Mechanism started successfully, false
	 * otherwise.
	 *
	 * @param string $Mechanism
	 * @param string $Challenge
	 *
	 * @return string | false
	 */
	public function startSaslExchange($Mechanism, $Challenge = '');

	/**
	 *
	 * @param string $Data
	 */
	public function sendSaslCommand($Data);

	/**
	 *
	 * @return string
	 */
	public function readSaslResponse();

	/**
	 * Return true when authenticated.
	 *
	 * @return bool
	 */
	public function finalizeSaslExchange();

}
