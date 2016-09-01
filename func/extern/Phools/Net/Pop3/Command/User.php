<?php

/**
 * Implement POP3-command "USER".
 * http://tools.ietf.org/html/rfc1939#page-13
 *
      USER name

         Arguments:
             a string identifying a mailbox (required), which is of
             significance ONLY to the server

         Restrictions:
             may only be given in the AUTHORIZATION state after the POP3
             greeting or after an unsuccessful USER or PASS command

         Discussion:
             To authenticate using the USER and PASS command
             combination, the client must first issue the USER
             command.  If the POP3 server responds with a positive
             status indicator ("+OK"), then the client may issue
             either the PASS command to complete the authentication,
             or the QUIT command to terminate the POP3 session.  If
             the POP3 server responds with a negative status indicator
             ("-ERR") to the USER command, then the client may either
             issue a new authentication command or may issue the QUIT
             command.

             The server may return a positive response even though no
             such mailbox exists.  The server may return a negative
             response if mailbox exists, but does not permit plaintext
             password authentication.

         Possible Responses:
             +OK name is a valid mailbox
             -ERR never heard of mailbox name

         Examples:
             C: USER frated
             S: -ERR sorry, no mailbox for frated here
                ...
             C: USER mrose
             S: +OK mrose is a real hoopy frood
 *
 */
class Phools_Net_Pop3_Command_User
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 *
	 * @param string $Username
	 */
	public function __construct($Username)
	{
		$this->setUsername($Username);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Username = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'USER ' . $this->getUsername();
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);
		$Output->flush();

		return $this;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Username = '';

	/**
	 *
	 *
	 * @param string $Username
	 * @return Phools_Net_Pop3_Command_Dele
	 */
	public function setUsername($Username)
	{
		assert( is_string($Username) );

		$this->Username = (string) $Username;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUsername()
	{
		return $this->Username;
	}

}
