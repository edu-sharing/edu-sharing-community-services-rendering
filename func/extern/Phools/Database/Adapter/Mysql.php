<?php

/**
 *
 *
 *
 */
class Phools_Database_Adapter_Mysql
extends Phools_Database_Adapter_Abstract
{

	/**
	 *
	 * @param string $Host
	 * @param int $Port
	 */
	public function __construct(
		$Database,
		$Host = 'localhost',
		$Port = 3306,
		$Username = '',
		$Password = '')
	{
		parent::__construct($Database);

		$this
			->setHost($Host)
			->setPort($Port)
			->setUsername($Username)
			->setPassword($Password);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Abstract::__destruct()
	 */
	public function __destruct()
	{
		$this->Password = null;
		$this->Username = null;
		$this->Port = null;
		$this->Host = null;

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::open()
	 */
	public function open()
	{
		$Host = $this->getHost();
		$Host .= ':' . $this->getPort();

		$ConnectionResource = mysql_connect($Host, $this->getUsername(), $this->getPassword());
		if ( ! $ConnectionResource )
		{
			return false;
		}

		if ( ! mysql_select_db($this->getDatabase(), $ConnectionResource) )
		{
			return false;
		}

		return true;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::close()
	 */
	public function close()
	{
		$ConnectionResource = $this->getConnectionResource();
		if ( $ConnectionResource )
		{
			mysql_close($ConnectionResource);
		}

		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::query()
	 */
	public function query($Sql)
	{
		assert( is_string($Sql) );

		$ConnectionResource = $this->getConnectionResource();

		$ResultResource = mysql_query($Sql, $ConnectionResource);
		if ( false === $ResultResource )
		{
			throw new Phools_Database_Exception('Error executing query.');
		}

		return $ResultResource;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::getErrorCode()
	 */
	public function getErrorCode(&$ResultResource = null)
	{
		if ( $ResultResource )
		{
			assert( is_resource($ResultResource) );
		}

		$ConnectionResource = $this->getConnectionResource();

		$ErrorCode = mysql_errno($ConnectionResource);
		return $ErrorCode;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::getErrorMessage()
	 */
	public function getErrorMessage(&$ResultResource = null)
	{
		if ( $ResultResource )
		{
			assert( is_resource($ResultResource) );
		}

		$ConnectionResource = $this->getConnectionResource();

		$ErrorMessage = mysql_error($ConnectionResource);
		return $ErrorMessage;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::affectedRows()
	 */
	public function affectedRows(&$ResultResource = null)
	{
		if ( $ResultResource )
		{
			assert( is_resource($ResultResource) );
		}

		$ConnectionResource = $this->getConnectionResource();

		$AffectedRows = mysql_affected_rows($ConnectionResource);
		if ( $AffectedRows == -1 )
		{
			throw new Phools_Database_Exception('Previous query failed.');
		}

		return $AffectedRows;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::rowCount()
	 */
	public function rowCount(&$ResultResource)
	{
		assert( is_resource($ResultResource) );

		$RowCount = mysql_num_rows($ResultResource);
		if ( false === $RowCount )
		{
			throw new Phools_Database_Exception('Previous query failed.');
		}

		return $RowCount;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::fetchField()
	 */
	public function fetchField(&$ResultResource, $Offset = 0)
	{
		assert( is_resource($ResultResource) );
		assert( is_int($Offset) );

		$Object = mysql_fetch_field($ResultResource, $Offset);
		if ( ! $Object )
		{
			throw new Phools_Database_Exception('Field "'.$Offset.'" not found.');
		}

		return $Object;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::fetchRow()
	 */
	public function fetchRow(
		&$ResultResource,
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array())
	{
		assert( is_resource($ResultResource) );
		assert( is_string($ClassName) );

		$Row = null;
		switch( $FetchMode )
		{
			case Phools_Database_FetchMode::ASSOC:
				$Row = mysql_fetch_assoc($ResultResource);
				break;
			case Phools_Database_FetchMode::NUMBERED:
				$Row = mysql_fetch_array($ResultResource);
				break;
			case Phools_Database_FetchMode::OBJECT:
				$Row = mysql_fetch_object($ResultResource);
				break;
			default:
				throw new Phools_Database_Exception('Invalid fetch-mode given.');
		}

		return $Row;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::fetchAll()
	 */
	public function fetchAll(
		&$ResultResource,
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array())
	{
		assert( is_resource($ResultResource) );
		assert( is_string($ClassName) );

		$Rowset = null;
		switch( $FetchMode )
		{
			case Phools_Database_FetchMode::ASSOC:
				$Row = mysql_fetc($ResultResource);
				break;
			case Phools_Database_FetchMode::NUMBERED:
				$Row = mysql_fetch_array($ResultResource);
				break;
			case Phools_Database_FetchMode::OBJECT:
				$Row = mysql_fetch_object($ResultResource);
				break;
			default:
				throw new Phools_Database_Exception('Invalid fetch-mode given.');
		}

		return $Rowset;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::freeResult()
	 */
	public function freeResult(&$ResultResource)
	{
		assert( is_resource($ResultResource) );

		mysql_free_result($ResultResource);

		return $this;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Host = '';

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::setHost()
	 */
	public function setHost($Host)
	{
		assert( is_string($Host) );

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
	 *
	 * @var int
	 */
	private $Port = 3306;

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Adapter_Interface::setPort()
	 */
	public function setPort($Port)
	{
		assert( is_int($Port) );

		$this->Port = (int) $Port;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getPort()
	{
		return $this->Port;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Username = '';

	/**
	 *
	 * @param string $Username
	 */
	public function setUsername($Username)
	{
		assert( is_string($Username) );

		$this->Username = (string) $Username;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getUsername()
	{
		return $this->Username;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Password = '';

	/**
	 *
	 * @param string $Password
	 */
	public function setPassword($Password)
	{
		assert( is_string($Password) );

		$this->Password = (string) $Password;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getPassword()
	{
		return $this->Password;
	}

}
