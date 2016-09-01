<?php

/**
 *
 *
 */
abstract class Phools_Parser_BufferedParser
extends Phools_Parser_Packrat
{

	/**
	 * Concatenate $Value's in $this->Buffer for delayed consumption.
	 *
	 * (non-PHPdoc)
	 * @see Phools_Parser_Interface::onTerminal()
	 */
	public function onTerminal($Data, Phools_Stream_Input_Buffer &$InputBuffer)
	{
		$this->append($Data);

		return $this;
	}

	/**
	 *
	 * @var string
	 */
	private $Buffer = '';

	/**
	 * Get currently buffered data.
	 *
	 * @return string
	 */
	public function getBuffer()
	{
		return $this->Buffer;
	}

	protected function append($Data)
	{
		assert( is_string($Data) );

		$this->Buffer .= $Data;
	}

	/**
	 * Discard currently buffered string.
	 *
	 * return string
	 */
	protected function flush()
	{
		$this->Buffer = '';

		return $this;
	}

}
