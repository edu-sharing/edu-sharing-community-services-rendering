<?php

/**
 * Match by ASCII-range.
 *
 *
 */
class Phools_Parser_Rule_AsciiRange
extends Phools_Parser_Rule_Abstract
{

	/**
	 *
	 * @param int $Minimum
	 * @param int $Maximum
	 */
	public function __construct($Minimum = 0, $Maximum = PHP_INT_MAX)
	{
		$this
			->setMinimum($Minimum)
			->setMaximum($Maximum);
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

		if ( ( $this->getMinimum() > $Byte ) )
		{
			return false;
		}

		if ( $this->getMaximum() < $Byte )
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
	private $Minimum = 0;

	/**
	 *
	 * @param int $Ascii
	 */
	public function setMinimum($Ascii)
	{
		assert( is_int($Ascii) );
		assert( 0 <= $Ascii );

		if ( $Ascii > $this->getMaximum() )
		{
			throw new Phools_Exception('Minimum ascii-value must be less than or equal maximum.');
		}

		$this->Minimum = $Ascii;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getMinimum()
	{
		return $this->Minimum;
	}

	/**
	 *
	 * @var int
	 */
	private $Maximum = PHP_INT_MAX;

	/**
	 *
	 * @param int $Maximum
	 */
	public function setMaximum($Ascii)
	{
		assert( is_int($Ascii) );
		assert( 0 <= $Ascii );

		if ( $Ascii < $this->getMinimum() )
		{
			throw new Phools_Exception('Minimum ascii-value must be greater than or equal maximum.');
		}

		$this->Maximum = $Ascii;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getMaximum()
	{
		return $this->Maximum;
	}

}
