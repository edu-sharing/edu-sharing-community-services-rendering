<?php

/**
 *
 *
 *
 */
class Phools_Stream_Output_QuotedPrintable
implements Phools_Stream_Output_Interface
{

	/**
	 *
	 * @param Phools_Stream_Output_Interface $Stream
	 */
	public function __construct(Phools_Stream_Output_Interface $Stream)
	{
		$this->setStream($Stream);
	}

	/**
	 * Free stream
	 */
	public function __destruct()
	{
		$this->flush();

		$this->Stream = null;
	}

	/**
	 *
	 * @var string
	 */
	private $Position = 0;

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::getPosition()
	 */
	public function getPosition()
	{
		return $this->Position;
	}

	/**
	 *
	 * @param int $Length
	 */
	protected function increasePosition($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		$this->Position += $Length;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_output_Interface::write()
	 */
	public function write($Data)
	{
		assert( is_string($Data) );

		while( 0 < strlen($Data) )
		{
			$Char = substr($Data, 0, 1);
			if ( false === $Char )
			{
				throw new Exception('Error reading char.');
			}

			$Char = ord($Char);
			if (  $Char < 33 )
			{
				$QuotedPrintable = sprintf("=%02X", $Char);
			}
			else if ( $Char < 61 )
			{
				$QuotedPrintable = chr($Char);
			}
			else if ( $Char == 61 )
			{
				$QuotedPrintable = sprintf("=%02X", $Char);
			}
			else if ( $Char < 127 )
			{
				$QuotedPrintable = chr($Char);
			}
			else {
				$QuotedPrintable = sprintf("=%02X", $Char);
			}

			$this->getStream()->write($QuotedPrintable);
			$this->increasePosition(1);

			$Data = substr($Data, 1);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::flush()
	 */
	public function flush()
	{
		return $this->getStream()->flush();
	}

	/**
	 *
	 *
	 * @var Phools_Stream_Interface
	 */
	protected $Stream = null;

	/**
	 *
	 *
	 * @param Phools_Stream_Output_Interface $Stream
	 * @return Phools_Stream_Output_QuotedPrintable
	 */
	public function setStream(Phools_Stream_Output_Interface $Stream)
	{
		$this->Stream = $Stream;
		return $this;
	}

	/**
	 *
	 * @return Phools_Stream_Output_Interface
	 */
	protected function getStream()
	{
		return $this->Stream;
	}

}
