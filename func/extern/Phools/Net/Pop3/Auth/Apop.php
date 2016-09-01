<?php

/**
 *
 *
 *
 */
class Phools_Net_Pop3_Auth_Apop
implements Phools_Net_Pop3_Auth_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Auth_Interface::authenticate()
	 */
	public function authenticate(Phools_Net_Connection_Interface $Connection, $Username, $Password)
	{
		$Command = new Phools_Net_Pop3_Command_Apop();
		$Command->send($Connection);
	}

}
