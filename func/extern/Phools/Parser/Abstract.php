<?php

/**
 *
 *
 *
 */
abstract class Phools_Parser_Abstract
implements Phools_Parser_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Interface::parse()
	 */
	public function parse($Name, Phools_Stream_Input_Buffer &$InputBuffer)
	{
// var_dump($Name, $InputBuffer->getPosition());
		assert( is_string($Name) );
		assert( 0 < strlen($Name) );

		$Start = $InputBuffer->getPosition();

		$this->push(new Phools_Parser_Token_Start($Name));

		// try each registered grammar
		foreach( $this->getGrammars() as $Grammar )
		{
			if ( $Grammar->match($this, $InputBuffer, $Name) )
			{
				$this->push(new Phools_Parser_Token_Stop($Name));

				return true;
			}
		}

		array_pop($this->Tokens);

		$InputBuffer->seek($Start);

		return false;
	}

	/**
	 *
	 * @var array
	 */
	private $Tokens = array();

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Interface::push()
	 */
	public function push(Phools_Parser_Token_Interface $Token)
	{
		// Current position is NOT length of $this->Tokens after push()'ing,
		// its 1 less ;).
		$this->Tokens[] = $Token;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Interface::getPosition()
	 */
	public function getPosition()
	{
		return sizeof($this->Tokens);
	}

	/**
	 * Consume all tokens on stack.
	 *
	 * @return string
	 */
	public function consume(Phools_Stream_Input_Buffer &$InputBuffer)
	{
		// shifting $this->Tokens empties it, so enforces one-time-consumption
		foreach( $this->Tokens as $Token )
		{
			$Token->consume($this, $InputBuffer);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Interface::fallback()
	 */
	public function fallback($Position, Phools_Stream_Input_Buffer &$InputBuffer)
	{
		assert( is_int($Position) );
		assert( 0 <= $Position );

		while( $Position < sizeof($this->Tokens) )
		{
			$Token = array_pop($this->Tokens);

			$Token->rewind($this, $InputBuffer);
		}

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Parser_Grammar_Interface
	 */
	protected $Grammars = array();

	/**
	 *
	 *
	 * @param Phools_Parser_Grammar_Interface $Grammars
	 * @return Phools_Parser_Abstract
	 */
	public function register(Phools_Parser_Grammar_Interface $Grammar)
	{
		array_unshift($this->Grammars, $Grammar);

		return $this;
	}

	/**
	 *
	 * @return Phools_Parser_Grammar_Interface
	 */
	protected function getGrammars()
	{
		return $this->Grammars;
	}

}
