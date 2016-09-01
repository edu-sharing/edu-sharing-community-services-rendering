<?php

/**
 *
 *
 *
 */
class Phools_Stream_Output_String
implements Phools_Stream_Output_Interface
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_output_Interface::write()
	 */
	public function write($Data)
	{
		$this->String .= $Data;

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_output_Interface::flush()
	 */
	public function flush()
	{
		// no buffering, no flush
		return true;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $String = '';

	/**
	 *
	 *
	 * @param string $String
	 * @return Phools_Stream_Output_String
	 */
	protected function setString($String)
	{
		$this->String = (string) $String;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function getString()
	{
		return $this->String;
	}

}
