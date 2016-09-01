<?php

/**
 *
 *
 *
 */
class Phools_Stream_Output_Base64
implements Phools_Stream_Output_Interface
{

	/**
	 * The Base64 Alphabet as $Value => $Encoding
	 *
	 * @var array
	 */
	protected static $Alphabet = array(
		0	=>	'A',	17	=>	'R',	34	=>	'i',	51	=>	'z',
		1	=>	'B',	18	=>	'S',	35	=>	'j',	52	=>	'0',
		2	=>	'C',	19	=>	'T',	36	=>	'k',	53	=>	'1',
		3	=>	'D',	20	=>	'U',	37	=>	'l',	54	=>	'2',
		4	=>	'E',	21	=>	'V',	38	=>	'm',	55	=>	'3',
		5	=>	'F',	22	=>	'W',	39	=>	'n',	56	=>	'4',
		6	=>	'G',	23	=>	'X',	40	=>	'o',	57	=>	'5',
		7	=>	'H',	24	=>	'Y',	41	=>	'p',	58	=>	'6',
		8	=>	'I',	25	=>	'Z',	42	=>	'q',	59	=>	'7',
		9	=>	'J',	26	=>	'a',	43	=>	'r',	60	=>	'8',
		10	=>	'K',	27	=>	'b',	44	=>	's',	61	=>	'9',
		11	=>	'L',	28	=>	'c',	45	=>	't',	62	=>	'+',
		12	=>	'M',	29	=>	'd',	46	=>	'u',	63	=>	'/',
		13	=>	'N',	30	=>	'e',	47	=>	'v',
		14	=>	'O',	31	=>	'f',	48	=>	'w',
		15	=>	'P',	32	=>	'g',	49	=>	'x',
		16	=>	'Q',	33	=>	'h',	50	=>	'y',
	);


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
	 *
	 * @var string
	 */
	private $Remainder = 0;

	protected function writeByte($Byte)
	{
		$Byte = ord($Byte);

		switch( $this->getPosition() % 3 )
		{
			case 0:
				$Value = $Byte >> 2 & 0x3f;
				if ( ! array_key_exists($Value, self::$Alphabet) )
				{
					throw new Phools_Stream_Exception_InvalidByteStream('Value not found in base64-alphabet.');
				}

				$this->getStream()->write(self::$Alphabet[$Value]);

				$this->Remainder = $Byte << 4 & 0x30;

				$this->increasePosition(1);

				break;

			case 1:
				$Value = $this->Remainder | $Byte >> 4 & 0x0f;
				if ( ! array_key_exists($Value, self::$Alphabet) )
				{
					throw new Phools_Stream_Exception_InvalidByteStream('Value not found in base64-alphabet.');
				}

				$this->getStream()->write(self::$Alphabet[$Value]);

				$this->Remainder = $Byte << 2 & 0x3c;

				$this->increasePosition(1);

				break;

			case 2:
				$Value = $this->Remainder | $Byte >> 6 & 0x0f;
				if ( ! array_key_exists($Value, self::$Alphabet) )
				{
					throw new Phools_Stream_Exception_InvalidByteStream('Value not found in base64-alphabet.');
				}

				$this->getStream()->write(self::$Alphabet[$Value]);

				$Value = $Byte & 0x3f;
				if ( ! array_key_exists($Value, self::$Alphabet) )
				{
					throw new Phools_Stream_Exception_InvalidByteStream('Value not found in base64-alphabet.');
				}

				$this->getStream()->write(self::$Alphabet[$Value]);

				$this->increasePosition(1);

				break;

			default:
				throw new RuntimeException('LOGIC ERROR');
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::write()
	 */
	public function write($Data)
	{
		assert( is_string($Data) );

		while( 0 < strlen($Data) )
		{
			$Byte = substr($Data, 0, 1);
			if ( false === $Byte )
			{
				throw new Phools_Stream_Exception_InvalidByteStream($message, $code, $previous);
			}

			$this->writeByte($Byte);

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
		switch( $this->getPosition() % 3 )
		{
			case 0:
				break;

			case 1:
				$this->getStream()->write(self::$Alphabet[$this->Remainder]);
				$this->getStream()->write('=');
				$this->getStream()->write('=');
				break;

			case 2:
				$this->getStream()->write(self::$Alphabet[$this->Remainder]);
				$this->getStream()->write('=');
				break;

			default:
				throw new RuntimeException('LOGIC ERROR');
		}

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Stream_Output_Interface
	 */
	protected $Stream = null;

	/**
	 *
	 *
	 * @param Phools_Stream_Output_Interface $Stream
	 * @return Phools_Stream_Output_Base64
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
