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
	 * @param string $Keyword
	 */
	public function __construct($Keyword)
	{
		$this->setKeyword($Keyword);
	}

	/**
	 * Free tokens
	 */
	public function __destruct()
	{
		$this->Keyword = null;
	}

	protected function getLength()
	{
		return strlen($this->getKeyword());
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

		return $Parser->onKeyword($this->getKeyword(), $InputBuffer);
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
	private $Keyword = '';

	/**
	 *
	 * @param string $Keyword
	 */
	protected function setKeyword($Keyword)
	{
		assert( is_string($Keyword) );
		assert( 0 < strlen($Keyword) );

		$this->Keyword = $Keyword;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getKeyword()
	{
		return $this->Keyword;
	}

}
