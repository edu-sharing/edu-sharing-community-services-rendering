<?php

/**
 * Interface defining methods to allow integration of the different
 * auth-methods into clients.
 *
 *
 */
interface Phools_Net_Sasl_Mechanism_Interface
{

	/**
	 *
	 * @param Phools_Net_Sasl_Client_Interface $Client
	 *
	 * @return bool
	 */
	public function authenticate(
		Phools_Net_Sasl_Client_Interface $Client);

}
