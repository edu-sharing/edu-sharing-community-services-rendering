<?php

/**
 * Grammars define the rules the parser has to adhere.
 *
 *
 */
class Phools_Parser_Grammar_Abstract
implements Phools_Parser_Grammar_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Grammar_Interface::match()
	 */
	public function match(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer,
		$Name)
	{
		$Rule = $this->getRule($Name);
		if ( ! $Rule )
		{
			error_log('Rule "'.$Name.'" not defined.');
			return false;
		}

		if ( $Rule->parse($Parser, $InputBuffer) )
		{
			return true;
		}

		return false;
	}

	/**
	 *
	 * @var Phools_Parser_Rule_Interface
	 */
	private $Grammars = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Grammar_Interface::define()
	 */
	public function define($Name, Phools_Parser_Rule_Interface $Grammar)
	{
		assert( is_string($Name) );
		assert( 0 < strlen($Name) );

		if ( isset($this->Grammars[$Name]) )
		{
			throw new RuntimeException('Grammar "'.$Name.'" already defined.');
		}

		$this->Grammars[$Name] = $Grammar;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Grammar_Interface::getRule()
	 */
	public function getRule($Name)
	{
		assert( is_string($Name) );
		assert( 0 < strlen($Name) );

		if ( empty($this->Grammars[$Name]) )
		{
			return false;
		}

		return $this->Grammars[$Name];
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Grammar_Interface::redefine()
	 */
	public function redefine($Name, Phools_Parser_Rule_Interface $Grammar)
	{
		assert( is_string($Name) );
		assert( 0 < strlen($Name) );

		$this->Grammars[$Name] = $Grammar;

		return $this;
	}

}
