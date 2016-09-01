<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Response_Quit
extends Phools_Net_Smtp_Response_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Smtp_Response_Interface::receive()
	 *
	 * @throws Phools_Net_Smtp_Exception
	 */
	public function receive(Phools_Stream_Input_Interface &$Input)
	{
		$Code = $Input->peek(3);
		switch( $Code )
		{
			case '221':
				$Input->forward(3);
				break;

			default:
				throw new Phools_Net_Smtp_Exception('Unhandled response-code.');
		}

		$this->skipWhitespace($Input);
		$Line = $this->readLine($Input);

		return parent::receive($Input);
	}

}
