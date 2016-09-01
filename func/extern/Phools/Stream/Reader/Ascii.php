<?php

/**
 *
 *
 *
 */
class Phools_Stream_Reader_Ascii
extends Phools_Stream_Reader_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Reader_Interface::read()
	 */
	public function read($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		$Bytes = $this->getInputStream()->read($Length);

		$Chars = str_split($Bytes, 1);

		return $Chars;
	}

}
