<?php

/**
 *
 *
 *
 */
class Phools_Stream_Output_Connection
implements Phools_Stream_Output_Interface
{

	/**
	 *
	 * @param Phools_Net_Connection_Interface $Connection
	 */
	public function __construct(Phools_Net_Connection_Interface $Connection)
	{
		$this->setConnection($Connection);
	}

	/**
	 * Free connection.
	 *
	 */
	public function __destruct()
	{
		$this->Connection = null;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Output_Interface::write()
	 */
	public function write($Data)
	{
		while ( 0 < strlen($Data) )
		{
			$BytesWritten = $this->getConnection()->write($Data);
			if ( false === $BytesWritten )
			{
				throw new Phools_Stream_Exception_ReadError('Error writing to socket.');
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
	 * @var Phools_Net_Connection_Interface
	 */
	protected $Connection = null;

	/**
	 *
	 *
	 * @param Phools_Net_Connection_Interface $Connection
	 * @return Phools_Stream_Output_Socket
	 */
	public function setConnection(Phools_Net_Connection_Interface $Connection)
	{
		$this->Connection = $Connection;
		return $this;
	}

	/**
	 *
	 * @return Phools_Net_Connection_Interface
	 */
	protected function getConnection()
	{
		return $this->Connection;
	}

}
