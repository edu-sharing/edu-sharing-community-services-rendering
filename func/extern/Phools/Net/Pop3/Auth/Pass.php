<?php

/**
 *
 *
 *
 */
class Phools_Net_Pop3_Auth_UserPass
implements Phools_Net_Pop3_Auth_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Auth_Interface::authenticate()
	 */
	public function authenticate(
		Phools_Net_Connection_Interface $Connection,
		$Username,
		$Password)
	{
		$Command = new Phools_Net_Pop3_Command_User($Username);
		$Command->send($Connection);

		$Command = new Phools_Net_Pop3_Command_Pass($Password);
		$Command->send($Connection);

		return true;
	}

}
