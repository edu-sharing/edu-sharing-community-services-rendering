<?php

/**
 *
 *
 *
 */
class Phools_Stream_Output_EncodedWord
implements Phools_Stream_Output_Interface
{

	/**
	 *
	 * @param Phools_Stream_Output_Interface $WrappedStream
	 * @param int $LineLength
	 */
	public function __construct(
		Phools_Stream_Output_Interface $WrappedStream,
		$Charset = 'UTF-8')
	{
		$this
			->setWrappedStream($WrappedStream);
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
		$this->getWrappedStream()->write($Data);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::flush()
	 */
	public function flush()
	{
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
	 * @return Phools_Stream_Output_EncodedWord
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

}
