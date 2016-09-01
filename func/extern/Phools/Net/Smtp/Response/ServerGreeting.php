<?php

/**
 * Capsules the server greeting on connect.
 *
 *
 */
class Phools_Net_Smtp_Response_ServerGreeting
extends Phools_Net_Smtp_Response_Abstract
{

	/**
	 *
	 */
	public function __destruct()
	{
		$this->ServerGreeting = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Response_Interface::receive()
	 */
	public function receive(Phools_Stream_Input_Interface &$Input)
	{
		$Code = $Input->peek(3);
		switch( $Code )
		{
			case '220':
				$Input->forward(3);
				break;

			case '554':
				throw new Phools_Net_Smtp_Exception_TransactionFailed('Error receiving server-greeting.');

			default:
				throw new Phools_Net_Smtp_Exception_UnhandledResponseCode();
		}

		$this->skipWhitespace($Input);

		$ServerGreeting = $this->readLine($Input);
		$this->setServerGreeting($ServerGreeting);

		return parent::receive($Input);
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
	 * @return Phools_Net_Smtp_Response_ServerGreeting
	 */
	protected function setServerGreeting($ServerGreeting)
	{
		assert( is_string($ServerGreeting) );

		$this->ServerGreeting = $ServerGreeting;
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
