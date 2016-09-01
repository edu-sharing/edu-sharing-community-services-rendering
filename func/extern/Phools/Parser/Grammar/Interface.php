<?php

/**
 *
 *
 *
 */
interface Phools_Parser_Grammar_Interface
{

	/**
	 *
	 *
	 * @param Phools_Parser_Interface &$Parser
	 * @param Phools_Stream_Input_Buffer $InputBuffer
	 * @param string $Name
	 *
	 * @return bool
	 */
	public function match(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer,
		$Name);

	/**
	 * Define given $Rule as $Name.
	 *
	 * @param string $Name
	 * @param Phools_Parser_Rule_Interface $Rule
	 *
	 * @throws Phools_Parser_Exception when $Name already defined
	 *
	 * @return Phools_Parser_Grammar_Interface
	 */
	public function define($Name, Phools_Parser_Rule_Interface $Rule);

	/**
	 *
	 * @param string $Name
	 *
	 * @return Phools_Parser_Rule_Interface
	 */
	public function getRule($Name);

	/**
	 *
	 * @param string $Name
	 * @param Phools_Parser_Rule_Interface $Rule
	 *
	 * @return Phools_Parser_Grammar_Interface
	 */
	public function redefine($Name, Phools_Parser_Rule_Interface $Rule);

}
