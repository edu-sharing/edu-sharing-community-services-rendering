<?php

/**
 *
 *
 *
 */
class Phools_Stream_Output_FoldingWhitespace
implements Phools_Stream_Output_Interface
{

	/**
	 *
	 * @var string
	 */
	const WHITESPACE = ' ';

	/**
	 *
	 * @var string
	 */
	const CRLF = "\r\n";

	/**
	 *
	 * @param Phools_Stream_Output_Interface $WrappedStream
	 * @param int $LineLength
	 */
	public function __construct(
		Phools_Stream_Output_Interface $WrappedStream,
		$LineLength = 32)
	{
		$this
			->setWrappedStream($WrappedStream)
			->setLineLength($LineLength);
	}

	/**
	 * Free wrapped stream.
	 *
	 */
	public function __destruct()
	{
		$this->WrappedStream = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::write()
	 */
	public function write($Data)
	{
		assert( is_string($Data) );

		$this->getWrappedStream()->write($Data);

// 		$this->setRemainder($Remainder);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::flush()
	 */
	public function flush()
	{
// 		$this->setRemainder($this->getLineLength());

		$this->getWrappedStream()->flush();

		return $this;
	}

	/**
	 *
	 *
	 * @var Phools_Stream_Output_Interface
	 */
	protected $WrappedStream = null;

	/**
	 *
	 *
	 * @param Phools_Stream_Output_Interface $WrappedStream
	 * @return Phools_Stream_Ooutput_FoldingWhitespace
	 */
	public function setWrappedStream(Phools_Stream_Output_Interface $WrappedStream)
	{
		$this->WrappedStream = $WrappedStream;
		return $this;
	}

	/**
	 *
	 * @return Phools_Stream_Output_Interface
	 */
	protected function getWrappedStream()
	{
		return $this->WrappedStream;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $LineLength = 76;

	/**
	 *
	 *
	 * @param int $LineLength
	 * @return Phools_Stream_Ooutput_FoldingWhitespace
	 */
	public function setLineLength($LineLength)
	{
		assert( is_int($LineLength) );
		assert( 0 < $LineLength );

		$this->LineLength = (int) $LineLength;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getLineLength()
	{
		return $this->LineLength;
	}

}
