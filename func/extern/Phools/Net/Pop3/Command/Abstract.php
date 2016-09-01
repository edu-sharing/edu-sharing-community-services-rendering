<?php

/**
 *
 *
 *
 */
abstract class Phools_Net_Pop3_Command_Abstract
implements Phools_Net_Pop3_Command_Interface
{

	/**
	 *
	 * @param Phools_Net_Connection_Interface $Connection
	 * @param string $Data
	 */
	protected function writeLine(Phools_Net_Connection_Interface $Connection, $Data)
	{
		$Data .= self::CRLF;

		// repeat until all $Data sent
		$BytesTotal = 0;
		do {
			$BytesWritten = $Connection->write($Data);
			if ( false === $BytesWritten )
			{
				throw new Phools_Net_Smtp_Exception('Error writing line.');
			}

			$BytesTotal += $BytesWritten;

			$Data = substr($Data, $BytesWritten);
		}
		while ( strlen($Data) );

		return $BytesTotal;
	}

	/**
	 *
	 *
	 * @param Phools_Net_Connection_Interface $Connection
	 */
	protected function readLine(Phools_Net_Connection_Interface $Connection)
	{
		$Data = '';

		while( self::CRLF != substr($Data, -2) )
		{
			$Data .= $Connection->read(1);
			if ( $this->getMaxLineLength() < strlen($Data) )
			{
				throw new Phools_Net_Pop3_Exception_LineTooLong($Data);
			}
		}

		return $Data;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $MaxLineLength = 512;

	/**
	 *
	 *
	 * @param int $MaxLineLength
	 * @return Phools_Net_Pop3_Command_Abstract
	 */
	public function setMaxLineLength($MaxLineLength)
	{
		$this->MaxLineLength = (int) $MaxLineLength;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getMaxLineLength()
	{
		return $this->MaxLineLength;
	}

}
