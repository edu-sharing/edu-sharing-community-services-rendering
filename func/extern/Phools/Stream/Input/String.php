<?php

/**
 *
 *
 *
 */
class Phools_Stream_Input_String
implements Phools_Stream_Input_Interface
{

	/**
	 * Construct a stream to read from given $String
	 *
	 * @param string $String
	 */
	public function __construct($String = '')
	{
		$this->setString($String);
	}

	/**
	 * Free string.
	 */
	public function __destruct()
	{
		$this->String = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Interface::eof()
	 */
	public function eof()
	{
		if ( 0 < strlen($this->getString()) )
		{
			return false;
		}

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::read()
	 */
	public function read($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		$Data = substr($this->getString(), 0, $Length);
		if ( false === $Data )
		{
			throw new Phools_Stream_Exception_EndOfStream('Error reading from string.');
		}

		$Remainder = substr($this->getString(), strlen($Data));
		if ( false === $Remainder )
		{
			$Remainder = '';
		}

		$this->setString($Remainder);

		return $Data;
	}

	/**
	 * The string to stream from.
	 *
	 * @var string
	 */
	protected $String = '';

	/**
	 *
	 *
	 * @param string $String
	 *
	 * @return Phools_Stream_Input_String
	 */
	public function setString($String)
	{
		assert( is_string($String) );

		$this->String = $String;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getString()
	{
		return $this->String;
	}

}
