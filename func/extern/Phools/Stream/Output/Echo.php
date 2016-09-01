<?php

/**
 *
 *
 *
 */
class Phools_Stream_Output_Echo
implements Phools_Stream_Output_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::write()
	 */
	public function write($Data)
	{
		echo $Data;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::flush()
	 */
	public function flush()
	{
		return true;
	}

}
