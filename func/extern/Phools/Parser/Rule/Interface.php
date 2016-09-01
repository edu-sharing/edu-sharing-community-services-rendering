<?php

/**
 *
 *
 *
 */
interface Phools_Parser_Rule_Interface
{

	/**
	 * As grammar's work directly on $Parser and $InputBuffer they are given
	 * by reference.
	 *
	 * @param Phools_Parser_Interface $Parser
	 * @param Phools_Stream_Input_Buffer $InputBuffer
	 *
	 * @return Phools_Parser_Token_Abstract
	 */
	public function parse(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer);

	/**
	 *
	 * @return bool
	 */
	public function onStart();
	/**
	 *
	 * @return bool
	 */
	public function onStop();

}
