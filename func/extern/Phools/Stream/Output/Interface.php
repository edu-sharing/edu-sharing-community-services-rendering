<?php

/**
 *
 *
 *
 */
interface Phools_Stream_Output_Interface
{

	/**
	 * Append given $Data to output-buffer.
	 *
	 * @throws Phools_Stream_Exception on write-error.
	 *
	 * @return Phools_Stream_Output_Interface
	 */
	public function write($Data);

	/**
	 * Write remaining buffer to stream.
	 *
	 * @throws Phools_Stream_Exception on error.
	 *
	 * @return Phools_Stream_Output_Interface
	 */
	public function flush();

}
