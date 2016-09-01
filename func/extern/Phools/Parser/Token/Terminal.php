<?php

/**
 *
 *
 *
 */
class Phools_Parser_Token_Terminal
implements Phools_Parser_Token_Interface
{

	/**
	 *
	 * @param string $Value
	 */
	public function __construct($Value)
	{
		$this->setValue($Value);
	}

	/**
	 * Free tokens
	 */
	public function __destruct()
	{
		$this->Value = null;
	}

	protected function getLength()
	{
		return strlen($this->getValue());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Token_Interface::consume()
	 */
	public function consume(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer)
	{
		$InputBuffer->skip($this->getLength());

		return $Parser->onTerminal($this->getValue(), $InputBuffer);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Token_Interface::rewind()
	 */
	public function rewind(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer)
	{
		$InputBuffer->rewind($this->getLength());

		return $this;
	}

	/**
	 *
	 * @var string
	 */
	private $Value = '';

	/**
	 *
	 * @param string $Value
	 */
	protected function setValue($Value)
	{
		assert( is_string($Value) );
		assert( 0 < strlen($Value) );

		$this->Value = $Value;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->Value;
	}

}
