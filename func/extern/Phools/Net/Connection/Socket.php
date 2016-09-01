<?php

/**
 *
 *
 *
 */
class Phools_Net_Connection_Socket
implements Phools_Net_Connection_Interface
{

	/**
	 *
	 * @var string
	 */
	const TRANSPORT_SSL = 'ssl';

	/**
	 *
	 * @var string
	 */
	const TRANSPORT_SSLv2 = 'sslv2';

	/**
	 *
	 * @var string
	 */
	const TRANSPORT_SSLv3 = 'sslv3';

	/**
	 *
	 * @var string
	 */
	const TRANSPORT_TCP = 'tcp';

	/**
	 *
	 * @var string
	 */
	const TRANSPORT_TLS = 'tls';

	/**
	 *
	 * @var string
	 */
	const TRANSPORT_UDP = 'udp';

	/**
	 *
	 * @param string $Host
	 * @param string $Port
	 * @param string $Transport
	 */
	public function __construct(
		$Host = 'localhost',
		$Port = 80,
		$Transport = self::TRANSPORT_TCP)
	{
		$this
			->setHost($Host)
			->setPort($Port)
			->setTransport($Transport);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::open()
	 */
	public function open()
	{
		if ( $this->isEstablished() )
		{
			throw new Phools_Net_Connection_Exception_ConnectionAlreadyOpen('Socket already opened.');
		}

		$ConnectionString = '';
		switch( $this->getTransport() )
		{
			case self::TRANSPORT_SSL:
				$ConnectionString .= 'ssl';
				break;
			case self::TRANSPORT_SSLv2:
				$ConnectionString .= 'sslv2';
				break;
			case self::TRANSPORT_SSLv3:
				$ConnectionString .= 'sslv3';
				break;
			case self::TRANSPORT_TCP:
				$ConnectionString .= 'tcp';
				break;
			case self::TRANSPORT_TLS:
				$ConnectionString .= 'tls';
				break;
			case self::TRANSPORT_UDP:
				$ConnectionString .= 'udp';
				break;
			default:
				throw new Phools_Net_Connection_Exception_UnknownTransport('Invalid transport set.');
		}

		$ConnectionString .= '://' . $this->getHost();

		$errno = 0;
		$errMsg = '';
		$ConnectionResource = fsockopen(
			$ConnectionString,
			$this->getPort(),
			$errno,
			$errMsg,
			$this->getTimeout());

		if ( 0 != $errno )
		{
			error_log('Error opening Socket. Returned error-Message was: '.$errMsg);
			return false;
		}

		if ( ( false == $ConnectionResource ) || (0 != $errno ) )
		{
			return false;
		}

		$this
			->setConnectionResource($ConnectionResource)
			->setTimeout($this->getTimeout())
			->setIsBlocking(true);

		return $this;
	}

	public function setIsBlocking($isBlocking)
	{
		assert( is_bool($isBlocking) );

		stream_set_blocking($this->getConnectionResource(), $isBlocking);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::isEstablished()
	 */
	public function isEstablished()
	{
		if ( $this->getConnectionResource() )
		{
			return true;
		}

		return false;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::startTls()
	 */
	public function startTls()
	{
		if ( ! $this->isEstablished() )
		{
			throw new Phools_Net_Connection_Exception_NotConnected('Connection must be established. Please call open() before startTls().');
		}

		if ( ! stream_socket_enable_crypto($this->getConnectionResource(), true, STREAM_CRYPTO_METHOD_TLS_CLIENT) )
		{
			return false;
		}

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::close()
	 */
	public function close()
	{
		$ConnectionResource = $this->getConnectionResource();
		if ( $ConnectionResource )
		{
			fclose($ConnectionResource);
		}

		$this->ConnectionResource = null;

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::write()
	 */
	public function write($Data)
	{
// var_dump($Data);

		if ( ! $this->isEstablished() )
		{
			throw new Phools_Net_Exception('Socket not open.');
		}

		$BytesWritten = fwrite($this->getConnectionResource(), $Data);
		if ( false === $BytesWritten )
		{
			throw new Phools_Net_Connection_Exception('Error writing data.');
		}

		return $BytesWritten;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::eof()
	 */
	public function eof()
	{
		if ( ! $this->isEstablished() )
		{
			throw new Phools_Net_Exception_NotAllowed('Socket not open.');
		}

		return feof($this->getConnectionResource());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Net_Connection_Interface::read()
	 */
	public function read($Length = 1)
	{
		if ( ! $this->isEstablished() )
		{
			throw new Phools_Net_Exception_NotAllowed('Socket not open.');
		}

		$Data = fread($this->getConnectionResource(), $Length);
		if ( false === $Data )
		{
			throw new Phools_Net_Connection_Exception('Error reading data.');
		}
// var_dump($Data);

		return $Data;
	}

	/**
	 *
	 * @var resource
	 */
	private $ConnectionResource = null;

	/**
	 *
	 * @param resource $ConnectionResource
	 *
	 * @throws Phools_Net_Exception
	 */
	protected function setConnectionResource(&$ConnectionResource)
	{
		if ( ! is_resource($ConnectionResource) )
		{
			throw new Phools_Net_Exception('Connection-resource is not a resource.');
		}

		if ( $this->ConnectionResource )
		{
			throw new Phools_Net_Exception('Connection-resource already set.');
		}

		$this->ConnectionResource = $ConnectionResource;

		return $this;
	}

	/**
	 *
	 * @return resource
	 */
	protected function &getConnectionResource()
	{
		return $this->ConnectionResource;
	}

	/**
	 *
	 * @var string
	 */
	private $Transport = self::TRANSPORT_TCP;

	/**
	 *
	 * @param string $Transport
	 *
	 * @throws Exception
	 *
	 * @return Phools_Net_Connection_Socket
	 */
	public function setTransport($Transport)
	{
		if ( $this->isEstablished() )
		{
			throw new Exception('Socket open. Call close() first.');
		}

		$this->Transport = (string) $Transport;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getTransport()
	{
		return $this->Transport;
	}

	/**
	 *
	 * @var string
	 */
	private $Host = 'localhost';

	/**
	 *
	 * @param string $Host
	 *
	 * @throws Exception
	 *
	 * @return Phools_Net_Connection_Socket
	 */
	public function setHost($Host)
	{
		if ( $this->isEstablished() )
		{
			throw new Exception('Socket open. Call close() first.');
		}

		$this->Host = (string) $Host;

		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getHost()
	{
		return $this->Host;
	}

	/**
	 *
	 * @var int
	 */
	private $Port = 25;

	/**
	 *
	 * @param int $Port
	 *
	 * @throws Exception
	 *
	 * @return Phools_Net_Connection_Socket
	 */
	public function setPort($Port)
	{
		if ( $this->isEstablished() )
		{
			throw new Exception('Socket open. Call close() first.');
		}

		$this->Port = (int) $Port;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getPort()
	{
		return $this->Port;
	}

	/**
	 *
	 * @var int
	 */
	private $Timeout = 15;

	/**
	 *
	 * @param int $Timeout
	 *
	 * @throws Exception
	 *
	 * @return Phools_Net_Connection_Socket
	 */
	public function setTimeout($Timeout)
	{
		$Timeout = (int) $Timeout;

		$ConnectionResource = $this->getConnectionResource();
		if ( $ConnectionResource )
		{
			$Result = stream_set_Timeout($ConnectionResource, $Timeout);
			if ( false == $Result )
			{
				throw new Exception('Error setting timeout.');
			}
		}

		$this->Timeout = $Timeout;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getTimeout()
	{
		return $this->Timeout;
	}

}
