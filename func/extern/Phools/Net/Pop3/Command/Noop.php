<?php

/**
 * Implement POP3-command "NOOP".
 * http://tools.ietf.org/html/rfc1939#page-9
 *
      NOOP

         Arguments: none

         Restrictions:
             may only be given in the TRANSACTION state

         Discussion:
             The POP3 server does nothing, it merely replies with a
             positive response.

         Possible Responses:
             +OK

         Examples:
             C: NOOP
             S: +OK

 *
 */
class Phools_Net_Pop3_Command_Noop
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'NOOP';
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

}
