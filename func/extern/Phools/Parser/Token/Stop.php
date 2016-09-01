<?php

/**
 *
 *
 */
class Phools_Parser_Token_Stop
extends Phools_Parser_Token_Marker_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Token_Interface::consume()
	 */
	public function consume(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer)
	{
		$Parser->onStop($this->getName(), $InputBuffer);
	}

}
