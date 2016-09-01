<?php

/**
 * Implement POP3-command "TOP".
 * @see http://tools.ietf.org/html/rfc1939#page-11
 *
      TOP msg n

         Arguments:
             a message-number (required) which may NOT refer to to a
             message marked as deleted, and a non-negative number
             of lines (required)

         Restrictions:
             may only be given in the TRANSACTION state

         Discussion:
             If the POP3 server issues a positive response, then the
             response given is multi-line.  After the initial +OK, the
             POP3 server sends the headers of the message, the blank
             line separating the headers from the body, and then the
             number of lines of the indicated message's body, being
             careful to byte-stuff the termination character (as with
             all multi-line responses).

             Note that if the number of lines requested by the POP3
             client is greater than than the number of lines in the
             body, then the POP3 server sends the entire message.

         Possible Responses:
             +OK top of message follows
             -ERR no such message

         Examples:
             C: TOP 1 10
             S: +OK
             S: <the POP3 server sends the headers of the
                message, a blank line, and the first 10 lines
                of the body of the message>
             S: .
                ...
             C: TOP 100 3
             S: -ERR no such message

 *
 */
class Phools_Net_Pop3_Command_Top
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 *
	 * @param int $Message
	 * @param int $Lines
	 */
	public function __construct($Message, $Lines = 0)
	{
		$this
			->setMessage($Message)
			->setLines();
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Lines = null;
		$this->Message = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'TOP ' . $this->getMessage() . ' ' . $this->getLines();
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
	 *
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

	/**
	 *
	 *
	 * @var int
	 */
	protected $Lines = 0;

	/**
	 *
	 *
	 * @param int $Lines
	 *
	 * @return Phools_Net_Pop3_Command_Dele
	 */
	public function setLines($Lines)
	{
		assert( is_int($Lines) );

		$this->Lines = (int) $Lines;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getLines()
	{
		return $this->Lines;
	}

}
