<?php

/**
 *
 *
 *
 */
class Phools_Stream_Reader_Utf8
extends Phools_Stream_Reader_Abstract
{

	/**
	 *
	 */
	protected function readSequentialBytes($FirstByte)
	{
		return $FirstByte;
	}

	/**
	 *
	 */
	protected function readChar()
	{
		$FirstByte = $this->getInputStream()->read(1);

		if ( ord($FirstByte) <= 127 )
		{
			$Char = $FirstByte;
		}
		else
		{
			$Char = $this->readSequentialBytes($FirstByte);
		}

		return $Char;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Reader_Interface::read()
	 */
	public function read($Length = 1)
	{
		$Chars = array();
		while( 0 < $Length )
		{
			$Chars[] = $this->readChar();
			$Length--;
		}

		return $Chars;
	}

}
