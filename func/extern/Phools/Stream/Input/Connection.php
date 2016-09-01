<?php

/**
 *
 *
 *
 */
class Phools_Stream_Input_Connection
implements Phools_Stream_Input_Interface
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
	 * @see Phools_Stream_Input_Interface::eof()
	 */
	public function eof()
	{
		return $this->getConnection()->eof();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Stream_Input_Interface::read()
	 */
	public function read($Length = 1)
	{
		assert( is_int($Length) );
		assert( 0 < $Length );

		if ( $this->eof() )
		{
			throw new Phools_Stream_Exception_EndOfStream('');
		}

		$Data = $this->getConnection()->read($Length);

		return $Data;
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
	 *
	 * @return Phools_Stream_Input_Socket
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
