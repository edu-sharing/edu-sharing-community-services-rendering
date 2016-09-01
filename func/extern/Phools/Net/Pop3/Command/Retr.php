<?php

/**
 * Implement POP3-command "RETR".
 * @see http://tools.ietf.org/html/rfc1939#page-8
 *
      RETR msg

         Arguments:
             a message-number (required) which may NOT refer to a
             message marked as deleted

         Restrictions:
             may only be given in the TRANSACTION state

         Discussion:
             If the POP3 server issues a positive response, then the
             response given is multi-line.  After the initial +OK, the
             POP3 server sends the message corresponding to the given
             message-number, being careful to byte-stuff the termination
             character (as with all multi-line responses).

         Possible Responses:
             +OK message follows
             -ERR no such message

         Examples:
             C: RETR 1
             S: +OK 120 octets
             S: <the POP3 server sends the entire message here>
             S: .
 *
 */
class Phools_Net_Pop3_Command_Retr
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 *
	 * @param int $Message
	 */
	public function __construct($Message)
	{
		$this->setMessage($Message);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Message = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'RETR ' . $this->getMessage();
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
	 * @return Phools_Net_Pop3_Command_Dele
	 */
	public function setMessage($Message)
	{
		assert( is_int($Message) );

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
