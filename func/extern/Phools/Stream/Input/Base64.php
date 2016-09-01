<?php

/**
 * Read base64 encoded streams.
 *
 *
 */
class Phools_Stream_Input_Base64
implements Phools_Stream_Input_Interface
{

	/**
	 * The Base64 Alphabet as $Char => $Value
	 *
	 * @var array
	 */
	protected static $Alphabet = array(
		'A' => 0,	'R' => 17,	'i' => 34,	'z' => 51,
		'B' => 1,	'S' => 18,	'j' => 35,	'0' => 52,
		'C' => 2,	'T' => 19,	'k' => 36,	'1' => 53,
		'D' => 3,	'U' => 20,	'l' => 37,	'2' => 54,
		'E' => 4,	'V' => 21,	'm' => 38,	'3' => 55,
		'F' => 5,	'W' => 22,	'n' => 39,	'4' => 56,
		'G' => 6,	'X' => 23,	'o' => 40,	'5' => 57,
		'H' => 7,	'Y' => 24,	'p' => 41,	'6' => 58,
		'I' => 8,	'Z' => 25,	'q' => 42,	'7' => 59,
		'J' => 9,	'a' => 26,	'r' => 43,	'8' => 60,
		'K' => 10,	'b' => 27,	's' => 44,	'9' => 61,
		'L' => 11,	'c' => 28,	't' => 45,	'+' => 62,
		'M' => 12,	'd' => 29,	'u' => 46,	'/' => 63,
		'N' => 13,	'e' => 30,	'v' => 47,
		'O' => 14,	'f' => 31,	'w' => 48,
		'P' => 15,	'g' => 32,	'x' => 49,
		'Q' => 16,	'h' => 33,	'y' => 50,
	);

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
	 *
	 * @var bool
	 */
	private $Eof = false;

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::eof()
	 */
	public function eof()
	{
		if ( $this->getStream()->eof() )
		{
			$this->Eof = true;
		}

		return $this->Eof;
	}

	/**
	 *
	 * @param bool $Eof
	 */
	protected function setEof($IsEof)
	{
		$this->Eof = (bool) $IsEof;

		return $this;
	}

	/**
	 *
	 * @var int
	 */
	private $Position = 0;

	/**
	 *
	 * @var string
	 */
	private $Remainder = 0;

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::read()
	 */
	public function read($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		$String = '';

		while( 0 < $Length )
		{
			switch( $this->Position % 4 )
			{
				case 0:
					$Char = $this->getStream()->read(1);
					if ( ! array_key_exists($Char, self::$Alphabet) )
					{
						throw new Phools_Stream_Exception_InvalidByteStream();
					}

					$Value = self::$Alphabet[$Char];
					$this->Remainder = $Value << 2 & 0xfc;

					$this->Position++;

				case 1:
					$Char = $this->getStream()->read(1);
					if ( ! array_key_exists($Char, self::$Alphabet) )
					{
						throw new Phools_Stream_Exception_InvalidByteStream();
					}

					$Value = self::$Alphabet[$Char];

					$Byte = $this->Remainder | $Value >> 4 & 0x03;
					$this->Remainder = $Value << 4 & 0xf0;

					$this->Position++;

					break;

				case 2:
					$Char = $this->getStream()->read(1);
					if ( '=' == $Char )
					{
						$Value = 0;
						$this->setEof(true);
					}
					else
					{
						if ( ! array_key_exists($Char, self::$Alphabet) )
						{
							throw new Phools_Stream_Exception_InvalidByteStream();
						}

						$Value = self::$Alphabet[$Char];
					}

					$Byte = $this->Remainder | $Value >> 2 & 0x0f;
					$this->Remainder = $Value << 6 & 0xc0;

					$this->Position++;

					break;

				case 3:
					$Char = $this->getStream()->read(1);
					if ( '=' == $Char )
					{
						$Value = 0;
						$this->setEof(true);
					}
					else
					{
						if ( ! array_key_exists($Char, self::$Alphabet) )
						{
							throw new Phools_Stream_Exception_InvalidByteStream();
						}

						$Value = self::$Alphabet[$Char];
					}

					$Byte = $this->Remainder | $Value & 0x3f;

					$this->Position++;

					break;

				default:
					throw new Exception('Error.');
			}

			$String .= chr($Byte);

			$Length--;
		}

		return $String;
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
	 * @param Phools_Stream_Input_Interface $Stream
	 * @return Phools_Stream_Input_Base64
	 */
	public function setStream(Phools_Stream_Input_Interface $Stream)
	{
		$this->Stream = $Stream;
		return $this;
	}

	/**
	 *
	 * @return Phools_Stream_Input_Interface
	 */
	protected function getStream()
	{
		return $this->Stream;
	}

}
