<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Response_Ehlo
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
			case	'250':
				$Input->forward(3);
				break;

			case '502':
				throw new Phools_Net_Smtp_Exception_CommandNotImplemented($Line);

			case '504':
				throw new Phools_Net_Smtp_Exception_CommandParameterNotImplemented($Line);

			case '550':
				throw new Phools_Net_Smtp_Exception_MailboxNotAvailable($Line);

			default:
				throw new Phools_Net_Smtp_Exception_UnhandledResponseCode();
		}

		do
		{
			$Line = $this->readLine($Input);
		}
		while( ' ' != substr($Line, 3, 1) );

		return parent::receive($Input);
	}

}
