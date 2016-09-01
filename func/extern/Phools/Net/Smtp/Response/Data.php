<?php

/**
 *
 *
 *
 */
class Phools_Net_Smtp_Response_Data
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
			case '354':
				$Input->forward(3);
				break;

			default:
				throw new Phools_Net_Smtp_Exception('Unhandled response-code "'.$Code.'".');
		}

		$this->skipWhitespace($Input);
		$Line = $this->readLine($Input);

		return $this;
	}

}
