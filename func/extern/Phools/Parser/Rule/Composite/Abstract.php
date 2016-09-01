<?php

/**
 *
 *
 *
 */
abstract class Phools_Parser_Rule_Composite_Abstract
extends Phools_Parser_Rule_Abstract
{

	/**
	 *
	 * @param array $Grammars
	 */
	public function __construct(array $Grammars = array())
	{
		foreach( $Grammars as $Grammar )
		{
			$this->append($Grammar);
		}
	}

	/**
	 * Free grammars.
	 */
	public function __destruct()
	{
		$this->Grammars = null;
	}

	/**
	 *
	 * @var array
	 */
	private $Grammars = array();

	/**
	 * Append()-ing a grammar assigns the lowest priority.
	 *
	 * @param Phools_Parser_Rule_Interface $Grammar
	 */
	public function append(Phools_Parser_Rule_Interface $Grammar)
	{
		array_push($this->Grammars, $Grammar);
		return $this;
	}

	/**
	 * Prepend()-ing a grammar will assigns the highest priority.
	 *
	 * @param Phools_Parser_Rule_Interface $Grammar
	 */
	public function prepend(Phools_Parser_Rule_Interface $Grammar)
	{
		array_unshift($this->Grammars, $Grammar);
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getGrammars()
	{
		return $this->Grammars;
	}

}
