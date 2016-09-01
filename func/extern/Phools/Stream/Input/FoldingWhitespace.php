<?php

/**
 *
 *
 *
 */
class Phools_Stream_Input_FoldingWhitespace
extends Phools_Stream_Input_Buffer
{

	/**
	 *
	 * @param Phools_Stream_Input_Interface $WrappedStream
	 */
	public function __construct(Phools_Stream_Input_Interface $WrappedStream)
	{
		$this->setWrappedStream($WrappedStream);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Buffer::eof()
	 */
	public function eof()
	{
		if ( ! $this->getWrappedStream()->eof() )
		{
			return false;
		}

		return parent::eof();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::read()
	 */
	public function read($Length = 1)
	{
		if ( ! $this->available() )
		{

		}

		return parent::read($Length);
	}

	/**
	 *
	 *
	 * @var Phools_Stream_Input_Interface
	 */
	protected $WrappedStream = null;

	/**
	 *
	 *
	 * @param Phools_Stream_Input_Interface $WrappedStream
	 * @return Phools_Stream_Input_FoldingWhitespace
	 */
	public function setWrappedStream(Phools_Stream_Input_Interface $WrappedStream)
	{
		$this->WrappedStream = $WrappedStream;
		return $this;
	}

	/**
	 *
	 * @return Phools_Stream_Input_Interface
	 */
	protected function getWrappedStream()
	{
		return $this->WrappedStream;
	}

}
