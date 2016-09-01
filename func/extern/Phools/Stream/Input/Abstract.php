<?php

abstract class Phools_Stream_Input_Abstract
implements Phools_Stream_Input_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::read()
	 */
	public function read($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		if ( $this->getPosition() >= $this->getBufferSize() )
		{
			throw new Phools_Stream_Exception_BufferOverflow('End of buffer reached.');
		}

		$Data = substr($this->getBuffer(), $this->getPosition(), $Length);
		if ( false !== $Data )
		{
			$this->advancePosition( strlen($Data) );
		}

		return $Data;
	}

	public function skip($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		if ( $this->getPosition() >= $this->getBufferSize() )
		{
			throw new Phools_Stream_Exception_BufferOverflow('End of buffer reached.');
		}

		$Buffer = substr($this->getBuffer(), $Length);
		if ( false === $Buffer )
		{
			throw new Exception('');
		}

		$this->Buffer = $Buffer;

		return $this;
	}

	/**
	 * Hold buffered data.
	 *
	 * @var string
	 */
	private $Buffer = '';

	/**
	 * Get buffered data.
	 *
	 * @return string
	 */
	protected function getBuffer()
	{
		return $this->Buffer;
	}

	/**
	 * Append given $Data to buffer.
	 *
	 * @param string $Data
	 */
	protected function append($Data)
	{
		assert( is_string($Data) );
		assert( 0 < strlen($Data) );

		$this->Buffer .= $Data;

		return $this;
	}

	/**
	 * How many bytes are buffered.
	 *
	 * @return int
	 */
	public function available()
	{
		return strlen($this->getBuffer());
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

}
