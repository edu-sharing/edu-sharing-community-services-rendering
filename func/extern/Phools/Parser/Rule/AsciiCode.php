<?php

/**
 *
 *
 *
 */
class Phools_Parser_Rule_AsciiCode
extends Phools_Parser_Rule_Abstract
{

	/**
	 *
	 * @param int $Code
	 */
	public function __construct($Code)
	{
		$this->setCode($Code);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Rule_Interface::parse()
	 */
	public function parse(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer)
	{
		if ( $InputBuffer->eof() )
		{
			return false;
		}

		$Char = $InputBuffer->read(1);
		if ( false === $Char )
		{
			throw new Phools_Exception('Error reading from input-buffer.');
		}

		$Byte = ord($Char);

		if ( $this->getCode() !== $Byte )
		{
			return false;
		}

		$InputBuffer->forward(1);

		$Parser->push(new Phools_Parser_Token_Terminal($Char));
// var_dump($Char);

		return true;
	}

	/**
	 *
	 * @var int
	 */
	private $Code = '';

	/**
	 *
	 * @param int $Code
	 */
	public function setCode($Code)
	{
		assert( is_int($Code) );
		assert( 0 <= $Code );

		$this->Code = $Code;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getCode()
	{
		return $this->Code;
	}

}
