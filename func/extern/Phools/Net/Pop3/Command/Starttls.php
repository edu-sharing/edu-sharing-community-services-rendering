<?php

/**
 *
 *
 */
class Phools_Net_Pop3_Command_Starttls
extends Phools_Net_Pop3_Command_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Pop3_Command_Interface::send()
	 */
	public function send(Phools_Stream_Output_Interface $Output)
	{
		$Data = 'STLS';
		$Output->write($Data . Phools_Net_Pop3_Command_Interface::CRLF);

		return $this;
	}

}
