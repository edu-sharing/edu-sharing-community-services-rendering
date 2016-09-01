<?php

/**
 * Markers allow a parser to leave start()-/stop()-"bread-crumbs" on their way
 * through the tokens. The markers will notify them when something like
 * consume() or fallback() occur.
 *
 *
 */
abstract class Phools_Parser_Token_Marker_Abstract
implements Phools_Parser_Token_Interface
{

	/**
	 *
	 * @param string $Name
	 */
	public function __construct($Name)
	{
		$this->setName($Name);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Token_Interface::rewind()
	 */
	public function rewind(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer)
	{
		return $this;
	}

	/**
	 *
	 * @var string
	 */
	private $Name = '';

	/**
	 *
	 * @param string $Name
	 */
	protected function setName($Name)
	{
		assert( is_string($Name) );
		assert( 0 < strlen($Name) );

		$this->Name = $Name;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->Name;
	}

}
