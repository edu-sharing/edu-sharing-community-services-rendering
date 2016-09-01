<?php

/**
 * @see http://tools.ietf.org/html/rfc1939#page-4
 *
   Once the TCP connection has been opened by a POP3 client, the POP3
   server issues a one line greeting.  This can be any positive
   response.  An example might be:

      S:  +OK POP3 server ready
 *
 */
class Phools_Net_Pop3_Response_ServerGreeting
extends Phools_Net_Pop3_Response_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Response_Interface::receive()
	 */
	public function receive(Phools_Stream_Input_Interface $Input)
	{
		parent::receive($Input);

		$ServerGreeting = $this->readLine($Input);
		$this->setServerGreeting($ServerGreeting);

		return $this;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $ServerGreeting = '';

	/**
	 *
	 *
	 * @param string $ServerGreeting
	 *
	 * @return Phools_Net_Pop3_Response_Greeting
	 */
	protected function setServerGreeting($ServerGreeting)
	{
		assert( is_string($ServerGreeting) );

		$this->ServerGreeting = (string) $ServerGreeting;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getServerGreeting()
	{
		return $this->ServerGreeting;
	}

}
