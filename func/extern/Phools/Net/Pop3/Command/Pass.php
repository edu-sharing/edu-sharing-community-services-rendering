<?php

/**
 * Implement POP3-command "PASS".
 * @see http://tools.ietf.org/html/rfc1939#page-14
 *
      PASS string

         Arguments:
             a server/mailbox-specific password (required)

         Restrictions:
             may only be given in the AUTHORIZATION state immediately
             after a successful USER command

         Discussion:
             When the client issues the PASS command, the POP3 server
             uses the argument pair from the USER and PASS commands to
             determine if the client should be given access to the
             appropriate maildrop.

             Since the PASS command has exactly one argument, a POP3
             server may treat spaces in the argument as part of the
             password, instead of as argument separators.

         Possible Responses:
             +OK maildrop locked and ready
             -ERR invalid password
             -ERR unable to lock maildrop

         Examples:
             C: USER mrose
             S: +OK mrose is a real hoopy frood
             C: PASS secret
             S: -ERR maildrop already locked
               ...
             C: USER mrose
             S: +OK mrose is a real hoopy frood
             C: PASS secret
             S: +OK mrose's maildrop has 2 messages (320 octets)
 *
 */
class Phools_Net_Pop3_Command_Pass
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 *
	 * @param string $Password
	 */
	public function __construct($Password)
	{
		$this->setPassword($Password);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Password = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'PASS ' . $this->getPassword();
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Password = '';

	/**
	 *
	 *
	 * @param string $Password
	 * @return Phools_Net_Pop3_Command_Dele
	 */
	public function setPassword($Password)
	{
		$this->Password = (string) $Password;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getPassword()
	{
		return $this->Password;
	}

}
