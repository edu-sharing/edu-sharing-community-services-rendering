<?php

/**
 * Tokens form a double-linked list which allows navigating in a stream of
 * tokens.
 *
 *
 */
interface Phools_Parser_Token_Interface
{

	/**
	 *
	 * @return string
	 */
	public function consume(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer);

	/**
	 *
	 * @param Phools_Stream_Input_Buffer $InputBuffer
	 *
	 * @return Phools_Parser_Token_Interface
	 */
	public function rewind(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer);

}
