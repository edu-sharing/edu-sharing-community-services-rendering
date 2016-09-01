<?php

/**
 * Parsers "transform" the input-stream into "structure" by using, possibly
 * multiple, grammars.
 *
 *
 */
interface Phools_Parser_Interface
{

	/**
	 * Register a grammar to use for parsing.
	 *
	 * Return $this to allow chaining.
	 *
	 * @param string $Name the definitions name
	 * @param Phools_Parser_Rule_Interface $Grammar
	 *
	 * @return Phools_Parser_Interface
	 */
	public function register(Phools_Parser_Grammar_Interface $Grammar);

	/**
	 * Parse $InputBuffer using rule named $Name.
	 *
	 * @param string $Name
	 * @param Phools_Stream_Input_Interface $InputStream
	 *
	 * @return Phools_Parser_Token_Abstract
	 */
	public function parse($Name, Phools_Stream_Input_Buffer &$InputBuffer);

	/**
	 *
	 * @return int
	 */
	public function getPosition();

	/**
	 *
	 * @param Phools_Parser_Token_Interface $Token
	 *
	 * @return Phools_Parser_Interface
	 */
	public function push(Phools_Parser_Token_Interface $Token);

	/**
	 *
	 * @param Phools_Stream_Input_Buffer $InputBuffer
	 *
	 * @return string
	 */
	public function consume(Phools_Stream_Input_Buffer &$InputBuffer);

	/**
	 *
	 * @param int $Position
	 * @param Phools_Stream_Input_Buffer $InputBuffer
	 */
	public function fallback($Position, Phools_Stream_Input_Buffer &$InputBuffer);

	/**
	 * Callback to signal the start of a $Grammar named $Name.
	 *
	 * @param string $Name
	 * @param Phools_Stream_Input_Buffer $InputBuffer
	 */
	public function onStart($Name, Phools_Stream_Input_Buffer &$InputBuffer);

	/**
	 * Callback to signal the stop of a $Grammar named $Name.
	 *
	 * @param string $Name
	 * @param Phools_Stream_Input_Buffer $InputBuffer
	 */
	public function onStop($Name, Phools_Stream_Input_Buffer &$InputBuffer);

	/**
	 * Callback to pass $Data to parser.
	 *
	 * @param string $Data
	 * @param Phools_Stream_Input_Buffer $InputBuffer
	 */
	public function onTerminal($Data, Phools_Stream_Input_Buffer &$InputBuffer);

}
