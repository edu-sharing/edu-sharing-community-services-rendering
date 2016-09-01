<?php

/**
 *
 *
 *
 */
class Phools_Net_Http_Connection_Socket
extends Phools_Net_Connection_Socket
implements Phools_Net_Http_Connection_Interface
{

	/**
	 *
	 * @var string
	 */
	const CRLF = "\r\n";

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Http_Connection_Interface::writeLine()
	 */
	public function writeLine($Data)
	{
		$Data .= self::CRLF;

		// repeat until all $Data sent
		$BytesTotal = 0;
		do {
			$BytesWritten = $this->write($Data);
			if ( false === $BytesWritten )
			{
				throw new Phools_Net_Http_Exception_Line('Error writing line.');
			}

			$BytesTotal += $BytesWritten;

			$Data = substr($Data, $BytesWritten);
		}
		while ( strlen($Data) );

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Http_Connection_Interface::readLine()
	 */
	public function readLine()
	{
		$Line = '';

		// read until CRLF appears
		do
		{
			$Char = $this->read(1);
			if ( false === $Char )
			{
				throw new Phools_Net_Http_Exception_Line('Error reading line.');
			}

			$Line .= $Char;
		}
		while ( self::CRLF != substr($Line, -2) );

		// strip CRLF
		$Line = substr($Line, 0, -2);

		return $Line;
	}

}
