<?php

/**
 *
 *
 */
class Phools_Parser_Rule_Sequence
extends Phools_Parser_Rule_Composite_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Parser_Rule_Interface::parse()
	 */
	public function parse(
		Phools_Parser_Interface &$Parser,
		Phools_Stream_Input_Buffer &$InputBuffer)
	{
		$Start = $InputBuffer->getPosition();

		foreach( $this->getGrammars() as $Sequence )
		{
			if ( ! $Sequence->parse($Parser, $InputBuffer) )
			{
				$InputBuffer->seek($Start);

				return false;
			}
		}

		return true;
	}

}
