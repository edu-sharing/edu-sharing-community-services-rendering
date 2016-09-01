<?php

/**
 *
 *
 *
 */
class Phools_Parser_Rule_Keyword
extends Phools_Parser_Rule_Abstract
{

	/**
	 *
	 * @param int $Keyword
	 */
	public function __construct($Keyword)
	{
		$this->setKeyword($Keyword);
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

		$Length = strlen($this->getKeyword());

		$String = $InputBuffer->read($Length);
		if ( false === $String )
		{
			throw new Phools_Exception('Error reading from input-buffer.');
		}

		if ( $this->getKeyword() !== $String )
		{
			return false;
		}

		$Parser->push(new Phools_Parser_Token_Terminal($String));

		$InputBuffer->forward($Length);

		return true;
	}

	/**
	 *
	 * @var Keyword
	 */
	private $Keyword = '';

	/**
	 *
	 * @param Keyword $Keyword
	 */
	public function setKeyword($Keyword)
	{
		assert( is_string($Keyword) );
		assert( 0 <= $Keyword );

		$this->Keyword = $Keyword;

		return $this;
	}

	/**
	 *
	 * @return Keyword
	 */
	protected function getKeyword()
	{
		return $this->Keyword;
	}

}
