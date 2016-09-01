<?php

/**
 *
 *
 *
 */
interface Phools_Net_Pop3_Auth_Interface
{

	/**
	 *
	 * @param Phools_Net_Connection_Interface $Connection
	 * @param string $Username
	 * @param string $Password
	 */
	public function authenticate(
		Phools_Net_Connection_Interface $Connection,
		$Username,
		$Password);

}
