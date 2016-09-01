<?php

/**
 *
 *
 *
 */
class Phools_Parser_Exception_UnterminatedCharacter
extends Phools_Parser_Exception
{

	public function __construct($Message, $Character)
	{
		parent::__construct($Message);

		$this->setCharacter($Character);
	}

	/**
	 *
	 * @var string
	 */
	private $Character = null;

	/**
	 *
	 * @param string $Character
	 */
	protected function setCharacter($Character)
	{
		$this->Character = (string) $Character;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getCharacter()
	{
		return $this->Character;
	}

}
