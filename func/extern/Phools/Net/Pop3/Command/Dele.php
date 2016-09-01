<?php

/**
 * Implement POP3-command "DELE".
 * @see http://tools.ietf.org/html/rfc1939#page-8
 *
      DELE msg

         Arguments:
             a message-number (required) which may NOT refer to a
             message marked as deleted

         Restrictions:
             may only be given in the TRANSACTION state

         Discussion:
             The POP3 server marks the message as deleted.  Any future
             reference to the message-number associated with the message
             in a POP3 command generates an error.  The POP3 server does
             not actually delete the message until the POP3 session
             enters the UPDATE state.

         Possible Responses:
             +OK message deleted
             -ERR no such message

         Examples:
             C: DELE 1
             S: +OK message 1 deleted
                ...
             C: DELE 2
             S: -ERR message 2 already deleted
 *
 */
class Phools_Net_Pop3_Command_Dele
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 *
	 * @param int $Message
	 */
	public function __construct($Message = 1)
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
		$Data = 'DELE ' . $this->getMessage();
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $Message = 1;

	/**
	 *
	 *
	 * @param int $Message
	 * @return Phools_Net_Pop3_Command_Dele
	 */
	public function setMessage($Message)
	{
		assert( is_int($Message) );

		$this->Message = $Message;
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
