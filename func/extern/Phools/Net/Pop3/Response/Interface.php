<?php

interface Phools_Net_Pop3_Response_Interface
{

	const CRLF = "\r\n";

	/**
	 *
	 * @param Phools_Stream_Input_Interface $Input
	 */
	public function receive(Phools_Stream_Input_Interface $Input);

}
