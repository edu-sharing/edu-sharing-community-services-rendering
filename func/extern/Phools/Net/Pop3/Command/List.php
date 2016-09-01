<?php

/**
 * Implement POP3-command "LIST".
 * @see http://tools.ietf.org/html/rfc1939#page-6
 *
      LIST [msg]

         Arguments:
             a message-number (optional), which, if present, may NOT
             refer to a message marked as deleted

         Restrictions:
             may only be given in the TRANSACTION state

         Discussion:
             If an argument was given and the POP3 server issues a
             positive response with a line containing information for
             that message.  This line is called a "scan listing" for
             that message.

             If no argument was given and the POP3 server issues a
             positive response, then the response given is multi-line.
             After the initial +OK, for each message in the maildrop,
             the POP3 server responds with a line containing
             information for that message.  This line is also called a
             "scan listing" for that message.  If there are no
             messages in the maildrop, then the POP3 server responds
             with no scan listings--it issues a positive response
             followed by a line containing a termination octet and a
             CRLF pair.

             In order to simplify parsing, all POP3 servers are
             required to use a certain format for scan listings.  A
             scan listing consists of the message-number of the
             message, followed by a single space and the exact size of
             the message in octets.  Methods for calculating the exact
             size of the message are described in the "Message Format"
             section below.  This memo makes no requirement on what
             follows the message size in the scan listing.  Minimal
             implementations should just end that line of the response
             with a CRLF pair.  More advanced implementations may
             include other information, as parsed from the message.

                NOTE: This memo STRONGLY discourages implementations
                from supplying additional information in the scan
                listing.  Other, optional, facilities are discussed
                later on which permit the client to parse the messages
                in the maildrop.

             Note that messages marked as deleted are not listed.

         Possible Responses:
             +OK scan listing follows
             -ERR no such message

         Examples:
             C: LIST
             S: +OK 2 messages (320 octets)
             S: 1 120
             S: 2 200
             S: .
               ...
             C: LIST 2
             S: +OK 2 200
               ...
             C: LIST 3
             S: -ERR no such message, only 2 messages in maildrop

 *
 */
class Phools_Net_Pop3_Command_List
extends Phools_Net_Pop3_Command_Abstract
{

	public function __construct($Message)
	{
		$this->setMessage($Message);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'LIST ' . $this->getMessage();
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $Message = 0;

	/**
	 *
	 *
	 * @param int $Message
	 * @return Phools_Net_Pop3_Command_List
	 */
	public function setMessage($Message)
	{
		$this->Message = (int) $Message;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getMessage()
	{
		return $this->Message;
	}

}
