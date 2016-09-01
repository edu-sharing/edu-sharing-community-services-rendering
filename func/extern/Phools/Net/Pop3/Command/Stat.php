<?php

/**
 * Implement POP3-command "STAT".
 * @see http://tools.ietf.org/html/rfc1939#page-6
 *
      STAT

         Arguments: none

         Restrictions:
             may only be given in the TRANSACTION state

         Discussion:
             The POP3 server issues a positive response with a line
             containing information for the maildrop.  This line is
             called a "drop listing" for that maildrop.

             In order to simplify parsing, all POP3 servers are
             required to use a certain format for drop listings.  The
             positive response consists of "+OK" followed by a single
             space, the number of messages in the maildrop, a single
             space, and the size of the maildrop in octets.  This memo
             makes no requirement on what follows the maildrop size.
             Minimal implementations should just end that line of the
             response with a CRLF pair.  More advanced implementations
             may include other information.

                NOTE: This memo STRONGLY discourages implementations
                from supplying additional information in the drop
                listing.  Other, optional, facilities are discussed
                later on which permit the client to parse the messages
                in the maildrop.

             Note that messages marked as deleted are not counted in
             either total.

         Possible Responses:
             +OK nn mm

         Examples:
             C: STAT
             S: +OK 2 320

 *
 */
class Phools_Net_Pop3_Command_Stat
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'STAT';
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

}
