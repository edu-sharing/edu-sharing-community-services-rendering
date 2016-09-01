<?php

/**
 * Open a local unix-domain-socket.
 *
 *
 */
class Phools_Net_Connection_Fifo
implements Phools_Net_Connection_Interface
{

	public function __construct($Filename)
	{
		$this->setFilename($Filename);
	}

	public function __destruct()
	{
		$this->Filename = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::open()
	 */
	public function open()
	{
		$Filehandle = fopen($this->getFilename(), 'wb');
		if ( ! $Filehandle )
		{
			return false;
		}

		$this->setFilehandle($Filehandle);

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::isEstablished()
	 */
	public function isEstablished()
	{
		if ( $this->getFilehandle() )
		{
			return true;
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::close()
	 */
	public function close()
	{
		$Filehandle = $this->getFilehandle();
		if ( $Filehandle )
		{
			fclose($Filehandle);
			$this->setFilehandle(null);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::read()
	 */
	public function read($Length = 1)
	{
		$Filehandle = $this->getFilehandle();
		if ( ! $Filehandle )
		{
			throw new Phools_Net_Connection_Exception('No file-handle to write to.');
		}

		$Data = fgets($Filehandle, $Length);
		if ( false === $Data )
		{
			throw new Phools_Net_Connection_Exception('Error reading data.');
		}

		return $Data;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::write()
	 */
	public function write($Data)
	{
		$Filehandle = $this->getFilehandle();
		if ( ! $Filehandle )
		{
			throw new Phools_Net_Connection_Exception('No file-handle to write to.');
		}

		$BytesWritten = fwrite($Filehandle, $Data);
		if ( false === $BytesWritten )
		{
			throw new Phools_Net_Connection_Exception('Error writing data.');
		}

		return $BytesWritten;
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
	 * @return Phools_Net_Connection_Fifo
	 */
	public function setFilename($Filename)
	{
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

	/**
	 *
	 *
	 * @var resource
	 */
	private $Filehandle = null;

	/**
	 *
	 *
	 * @param resource $Filehandle
	 * @return Phools_Net_Connection_Fifo
	 */
	protected function setFilehandle($Filehandle = null)
	{
		$this->Filehandle = $Filehandle;
		return $this;
	}

	/**
	 *
	 * @return resource
	 */
	protected function getFilehandle()
	{
		return $this->Filehandle;
	}

}
