<?php

/**
 * Convinience class to quickly define characters without looking up their
 * ASCII-code.
 *
 */
class Phools_Parser_Rule_Char
extends Phools_Parser_Rule_AsciiCode
{

	/**
	 *
	 * @param string $Char
	 */
	public function __construct($Char)
	{
		// initialize parent with carefully choosen default value and setting
		// the $Char's code later avoids code-duplication for ord().
		parent::__construct(0);

		$this->setChar($Char);
	}

	/**
	 *
	 * @param string $Char
	 */
	public function setChar($Char)
	{
		$Code = ord($Char);

		return $this->setCode($Code);
	}

}
