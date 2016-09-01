<?php

/**
 * Implement POP3-command "UIDL".
 * @see http://tools.ietf.org/html/rfc1939#page-12
 *
      UIDL [msg]

      Arguments:
          a message-number (optional), which, if present, may NOT
          refer to a message marked as deleted

      Restrictions:
          may only be given in the TRANSACTION state.

      Discussion:
          If an argument was given and the POP3 server issues a positive
          response with a line containing information for that message.
          This line is called a "unique-id listing" for that message.

          If no argument was given and the POP3 server issues a positive
          response, then the response given is multi-line.  After the
          initial +OK, for each message in the maildrop, the POP3 server
          responds with a line containing information for that message.
          This line is called a "unique-id listing" for that message.

          In order to simplify parsing, all POP3 servers are required to
          use a certain format for unique-id listings.  A unique-id
          listing consists of the message-number of the message,
          followed by a single space and the unique-id of the message.
          No information follows the unique-id in the unique-id listing.

          The unique-id of a message is an arbitrary server-determined
          string, consisting of one to 70 characters in the range 0x21
          to 0x7E, which uniquely identifies a message within a
          maildrop and which persists across sessions.  This
          persistence is required even if a session ends without
          entering the UPDATE state.  The server should never reuse an
          unique-id in a given maildrop, for as long as the entity
          using the unique-id exists.

          Note that messages marked as deleted are not listed.

          While it is generally preferable for server implementations
          to store arbitrarily assigned unique-ids in the maildrop,
          this specification is intended to permit unique-ids to be
          calculated as a hash of the message.  Clients should be able
          to handle a situation where two identical copies of a
          message in a maildrop have the same unique-id.

      Possible Responses:
          +OK unique-id listing follows
          -ERR no such message

      Examples:
          C: UIDL
          S: +OK
          S: 1 whqtswO00WBw418f9t5JxYwZ
          S: 2 QhdPYR:00WBw1Ph7x7
          S: .
             ...
          C: UIDL 2
          S: +OK 2 QhdPYR:00WBw1Ph7x7
             ...
          C: UIDL 3
          S: -ERR no such message, only 2 messages in maildrop

 *
 */
class Phools_Net_Pop3_Command_Uidl
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'UIDL';
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

}
