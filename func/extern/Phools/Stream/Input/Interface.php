<?php

/**
 * Input-stream allow the byte-wise consumption of data-streams.
 *
 *
 *
 */
interface Phools_Stream_Input_Interface
{

	/**
	 * Test if end of stream is reached.
	 *
	 * @return bool
	 */
	public function eof();

	/**
	 * Read up to $Length bytes from stream.
	 *
	 * @param int $Length
	 *
	 * @throws Phools_Stream_Exception_EndOfStream
	 *
	 * @return string
	 */
	public function read($Length = 1);

}
