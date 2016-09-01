<?php

/**
 * Match a single token.
 *
 */
class Phools_Parser_Rule_Definition
extends Phools_Parser_Rule_Abstract
{

	/**
	 *
	 * @param string $Name of token to match.
	 */
	public function __construct($Name)
	{
		$this->setName($Name);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Rule_Interface::parse()
	 */
	public function parse(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer)
	{
		if ( $Parser->parse($this->getName(), $InputBuffer) )
		{
			return true;
		}

		return false;
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
		$this->Name = (string) $Name;
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
