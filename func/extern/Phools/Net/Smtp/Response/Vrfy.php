<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Response_Vrfy
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

			case '502':
				throw new Phools_Net_Smtp_Exception_CommandNotImplemented($Line);
				break;

			case '504':
				throw new Phools_Net_Smtp_Exception_CommandParameterNotImplemented($Line);
				break;

			case '550':
				throw new Phools_Net_Smtp_Exception_MailboxNotAvailable($Line);
				break;

			case '551':
				throw new Phools_Net_Smtp_Exception_UserNotLocal($Line);
				break;

			case '553':
				throw new Phools_Net_Smtp_Exception_MailboxNotAllowed($Line);
				break;

			default:
				throw new Phools_Net_Smtp_Exception_UnhandledResponseCode('');
		}

		$this->skipWhitespace($Input);
		$Line = $this->readLine($Input);

		return parent::receive($Input);
	}

}
