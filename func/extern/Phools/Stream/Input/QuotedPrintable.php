<?php

/**
 *
 *
 *
 */
class Phools_Stream_Input_QuotedPrintable
implements Phools_Stream_Input_Interface
{

	/**
	 *
	 * @param Phools_Stream_Input_Buffer $Stream
	 */
	public function __construct(Phools_Stream_Input_Buffer $Stream)
	{
		$this->setStream($Stream);
	}

	/**
	 * Free Stream
	 */
	public function __destruct()
	{
		$this->Stream = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::eof()
	 */
	public function eof()
	{
		return $this->getStream()->eof();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::read()
	 */
	public function read($Length = 1)
	{
		$Data = '';

		while ( 0 < $Length )
		{
			$Char = $this->getStream()->read(1);
			if ( '=' == $Char )
			{
				$Hex = $this->getStream()->read(2);
				if ( 2 != strlen($Hex) )
				{
					throw new Phools_Stream_Exception_InvalidByteStream('Less than 2 expected bytes available.');
				}

				$Data .= chr(hexdec($Hex));
			}
			else
			{
				$Data .= $Char;
			}

			$Length--;
		}

		return $Data;
	}

	/**
	 *
	 *
	 * @var Phools_Stream_Input_Buffer
	 */
	protected $Stream = null;

	/**
	 *
	 *
	 * @param Phools_Stream_Input_Buffer $Stream
	 *
	 * @return Phools_Stream_Input_Base64
	 */
	public function setStream(Phools_Stream_Input_Buffer $Stream)
	{
		$this->Stream = $Stream;
		return $this;
	}

	/**
	 *
	 * @return Phools_Stream_Input_Buffer
	 */
	protected function getStream()
	{
		return $this->Stream;
	}

}
