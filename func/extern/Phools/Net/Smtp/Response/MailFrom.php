<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Response_MailFrom
extends Phools_Net_Smtp_Response_Abstract
{

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

			case '451':
				throw new Phools_Net_Smtp_Exception_LocalErrorInProcessing($Line);

			case '452':
				throw new Phools_Net_Smtp_Exception_InsufficientSystemStorage($Line);

			case '455':
				throw new Phools_Net_Smtp_Exception_UnableToAccomodateParameters($Line);

			case '503':
				throw new Phools_Net_Smtp_Exception_BadCommandSequence($Line);

			case '550':
				throw new Phools_Net_Smtp_Exception_MailboxNotAvailable($Line);

			case '552':
				throw new Phools_Net_Smtp_Exception_StorageAllocationExceeded($Line);

			case '553':
				throw new Phools_Net_Smtp_Exception_MailboxNotAllowed($Line);

			case '555':
				throw new Phools_Net_Smtp_Exception_ParameterNotRecognized($Line);

			default:
				throw new Phools_Net_Smtp_Exception('Unhandled response-code.');
		}

		$this->skipWhitespace($Input);
		$Line = $this->readLine($Input);

		return parent::receive($Input);
	}

}
