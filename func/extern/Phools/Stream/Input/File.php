<?php

/**
 *
 *
 *
 */
class Phools_Stream_Input_File
implements Phools_Stream_Input_Interface
{

	/**
	 *
	 * @param string $Filename
	 */
	public function __construct($Filename)
	{
		assert( is_string($Filename) );

		$FileHandle = fopen($Filename, 'rb');
		if ( ! $FileHandle )
		{
			throw new Exception('Error opening file "'.$this->getFilename().'".');
		}

		$this->setFileHandle($FileHandle);
	}

	/**
	 * Free filename.
	 */
	public function __destruct()
	{
		fclose($this->getFileHandle());

		$this->FileHandle = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::eof()
	 */
	public function eof()
	{
		$FileHandle = $this->getFileHandle();
		if ( ! $FileHandle )
		{
			throw new Phools_Stream_Exception('Stream not open.');
		}

		return feof($FileHandle);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::read()
	 */
	public function read($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		$FileHandle = $this->getFileHandle();
		if ( ! $FileHandle )
		{
			throw new Phools_Stream_Exception('Stream not open()-ed.');
		}

		$Data = fread($FileHandle, $Length);

		return $Data;
	}

	/**
	 *
	 *
	 * @var resource
	 */
	protected $FileHandle = null;

	/**
	 *
	 *
	 * @param resource $FileHandle
	 * @return Phools_Stream_Input_File
	 */
	public function setFileHandle(&$FileHandle)
	{
		assert( is_resource($FileHandle) );

		$this->FileHandle = $FileHandle;
		return $this;
	}

	/**
	 *
	 * @return resource
	 */
	protected function &getFileHandle()
	{
		return $this->FileHandle;
	}

	/**
	 *
	 *
	 * @var string
	 */
	protected $Filename = '';

	/**
	 *
	 *
	 * @param string $Filename
	 * @return Phools_Stream_Input_File
	 */
	public function setFilename($Filename)
	{
		assert( is_string($Filename) );

		$this->Filename = (string) $Filename;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getFilename()
	{
		return $this->Filename;
	}

}
