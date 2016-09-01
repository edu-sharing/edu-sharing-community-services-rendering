<?php

/**
 *
 *
 *
 */
interface Phools_Net_Pop3_Command_Interface
{

	/**
	 *
	 * @var string
	 */
	const CRLF = "\r\n";

	/**
	 * Send this command over given connection
	 *
	 * @param Phools_Stream_Output_Interface $Output
	 */
	public function send(Phools_Stream_Output_Interface $Output);

}
