<?php

/**
 * Implement POP3-command "QUIT".
 * @see http://tools.ietf.org/html/rfc1939#page-5
 *
      QUIT

         Arguments: none

         Restrictions: none

         Possible Responses:
             +OK

         Examples:
             C: QUIT
             S: +OK dewey POP3 server signing off
 *
 */
class Phools_Net_Pop3_Command_Quit
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'QUIT';
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

}
