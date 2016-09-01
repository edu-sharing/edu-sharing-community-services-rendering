<?php

/**
 * Define common operations for both input- and output-streams.
 *
 *
 */
interface Phools_Stream_Interface
{

	/**
	 * Open the stream.
	 *
	 * @return bool
	 */
	public function open();

	/**
	 * Close the stream.
	 *
	 * @return bool
	 */
	public function close();

}
