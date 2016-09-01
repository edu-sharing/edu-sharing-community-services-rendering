<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Response_Auth
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

			case '432':
				throw new Phools_Net_Smtp_Exception_PasswordTransitionRequired();
				break;

			case '534':
				throw new Phools_Net_Smtp_Exception_AuthMechanismTooWeak();
				break;

			case '538':
				throw new Phools_Net_Smtp_Exception_EncryptionRequired();
				break;

			case '454':
				throw new Phools_Net_Smtp_Exception_TemporaryAuthenticationFailure();
				break;

			default:
				throw new Phools_Net_Smtp_Exception('Unhandled response-code.');
		}

		$this->skipWhitespace($Input);
		$Line = $this->readLine($Input);

		return $this;
	}

}
