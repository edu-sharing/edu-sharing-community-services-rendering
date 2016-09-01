<?php

/**
 * Implement POP3-command "RSET".
 * http://tools.ietf.org/html/rfc1939#page-9
 *
      RSET

         Arguments: none

         Restrictions:
             may only be given in the TRANSACTION state

         Discussion:
             If any messages have been marked as deleted by the POP3
             server, they are unmarked.  The POP3 server then replies
             with a positive response.

         Possible Responses:
             +OK

         Examples:
             C: RSET
             S: +OK maildrop has 2 messages (320 octets)
 *
 */
class Phools_Net_Pop3_Command_Rset
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'RSET';
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

}
