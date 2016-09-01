<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Response_Helo
extends Phools_Net_Smtp_Response_Abstract
{

	public function __destruct()
	{
		$this->Message = null;
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
			case '250':
				$Input->forward(3);
				break;

			case '502':
				throw new Phools_Net_Smtp_Exception_CommandNotImplemented($Code);

			case '504':
				throw new Phools_Net_Smtp_Exception_CommandParameterNotImplemented($Code);

			case '550':
				throw new Phools_Net_Smtp_Exception_MailboxNotAvailable($Code);

			default:
				throw new Phools_Net_Smtp_Exception('Unhandled response-code.');
		}

		$this->skipWhitespace($Input);

		$Message = $this->readLine($Input);
		$this->setMessage($Message);

		return parent::receive($Input);
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Message = '';

	/**
	 *
	 *
	 * @param string $Message
	 * @return Phools_Net_Smtp_Response_Helo
	 */
	protected function setMessage($Message)
	{
		assert( is_string($Message) );

		$this->Message = $Message;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->Message;
	}

}
