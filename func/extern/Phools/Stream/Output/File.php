<?php

/**
 *
 *
 *
 */
class Phools_Stream_Output_File
implements Phools_Stream_Output_Interface
{

	/**
	 *
	 * @param string $Filename
	 */
	public function __construct($Filename)
	{
		$FileHandle = fopen($Filename, 'rb');
		if ( ! $Handle )
		{
			return false;
		}

		$this->setFileHandle($FileHandle);
	}

	/**
	 * Free filename.
	 */
	public function __destruct()
	{
		$this->Filename = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Interface::open()
	 */
	public function open()
	{
		if ( $this->getFileHandle() )
		{
			return true;
		}

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Interface::close()
	 */
	public function close()
	{
		$FileHandle = $this->getFileHandle();
		if ( $FileHandle )
		{
			fclose($FileHandle);
		}

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::write()
	 */
	public function write($Data)
	{
		while( 0 < strlen($Data) )
		{
			$BytesWritten = fwrite($this->getFileHandle(), $Data);
			if ( false === $BytesWritten )
			{
				throw new Phools_Stream_Exception_ErrorWriting();
			}

			$Data = substr($Data, $BytesWritten);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::flush()
	 */
	public function flush()
	{
		return $this;
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
	 * @return Phools_Stream_Output_File
	 */
	public function setFileHandle(&$FileHandle)
	{
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
	 * @return Phools_Stream_Output_File
	 */
	public function setFilename(string $Filename)
	{
		$this->Filename = $Filename;
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
