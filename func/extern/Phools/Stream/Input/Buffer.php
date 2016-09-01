<?php

/**
 *
 *
 *
 */
class Phools_Stream_Input_Buffer
implements Phools_Stream_Input_Interface
{

	/**
	 *
	 * @param Phools_Stream_Input_Interface $InputStream
	 * @param int $BufferSize
	 */
	public function __construct(
		Phools_Stream_Input_Interface $InputStream,
		$BufferSize = 1024)
	{
		$this
			->setInputStream($InputStream)
			->setBufferSize($BufferSize);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->InputStream = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::eof()
	 */
	public function eof()
	{
		if ( 0 < $this->available() )
		{
			return false;
		}

		return $this->getInputStream()->eof();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::read()
	 */
	public function read($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		if ( $this->getPosition() >= $this->BufferSize )
		{
			throw new Phools_Stream_Exception_BufferOverflow('End of buffer reached.');
		}

		if ( ! $this->available() )
		{
			$Buffer = $this->getInputStream()->read($this->BufferSize);
			if ( false !== $Buffer )
			{
				$this->append($Buffer);
			}
		}

		$Data = substr($this->Buffer, $this->Position, $Length);
// var_dump($Data);

		return $Data;
	}

	public function skip($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		if ( $this->Position >= $this->BufferSize )
		{
			throw new Phools_Stream_Exception_BufferOverflow('End of buffer reached.');
		}

		$Buffer = substr($this->Buffer, $Length);
		if ( false === $Buffer )
		{
			throw new Exception('Error reading from buffer.');
		}

		$this->Buffer = $Buffer;

		return $this;
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $Position = 0;

	/**
	 *
	 * @param int $Position
	 *
	 * @return Phools_Stream_Input_Buffer
	 */
	public function seek($Position)
	{
		assert( is_int($Position) );
		assert( 0 <= $Position );

		$this->Position = $Position;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function getPosition()
	{
		return $this->Position;
	}

	/**
	 *
	 * @param int $Length
	 */
	public function forward($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 <= $Length );

		$this->Position += $Length;

		return $this;
	}

	/**
	 *
	 * @param int $Length
	 */
	public function rewind($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 <= $Length );

		$this->Position -= $Length;

		return $this;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Buffer = '';

	/**
	 *
	 * @return string
	 */
	protected function getBuffer()
	{
		return $this->Buffer;
	}

	/**
	 *
	 * @param string $Data
	 *
	 * @return Phools_Stream_Input_Buffer
	 */
	protected function append($Data)
	{
		assert( is_string($Data) );
		assert( 0 < strlen($Data) );

		$this->Buffer .= $Data;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function available()
	{
		return strlen($this->Buffer) - $this->Position;
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $BufferSize = 1024;

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Interface::setBufferSize()
	 */
	public function setBufferSize($BufferSize)
	{
		assert( is_int($BufferSize) );
		assert( 0 < $BufferSize );

		$this->BufferSize = (int) $BufferSize;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getBufferSize()
	{
		return $this->BufferSize;
	}

	/**
	 *
	 *
	 * @var Phools_Stream_Input_Interface
	 */
	protected $InputStream = null;

	/**
	 *
	 *
	 * @param Phools_Stream_Input_Interface $InputStream
	 * @return Phools_Stream_Input_Buffer
	 */
	public function setInputStream(Phools_Stream_Input_Interface $InputStream)
	{
		$this->InputStream = $InputStream;
		return $this;
	}

	/**
	 *
	 * @return Phools_Stream_Input_Interface
	 */
	protected function getInputStream()
	{
		return $this->InputStream;
	}

}
