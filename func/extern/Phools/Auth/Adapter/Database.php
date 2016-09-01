<?php

/**
 *
 *
 *
 */
class Phools_Auth_Adapter_Database
extends Phools_Auth_Adapter_Abstract
{

	/**
	 *
	 * @param Phools_Database_Connection_Interface $Connection
	 * @param string $Table
	 * @param string $IdentityColumn
	 * @param string $CredentialColumn
	 */
	public function __construct(
		Phools_Database_Connection_Interface $Connection,
		$Table,
		$IdentityColumn,
		$CredentialColumn,
		Phools_Hashing_Interface $Hashing = null)
	{
		parent::__construct($Hashing);

		$this
			->setConnection($Connection)
			->setTable($Table)
			->setIdentityColumn($IdentityColumn)
			->setCredentialColumn($CredentialColumn);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Connection = null;

		parent::__destruct();
	}

	/**
	 * Works by fetching credential from the database and comparing it against
	 * the given one. Applies hashing if hashing-method is set.
	 *
	 * (non-PHPdoc)
	 * @see Phools_Auth_Adapter_Interface::authenticate()
	 */
	public function authenticate($Identity, $Credential)
	{
		$Hashing = $this->getHashing();
		if ( $Hashing )
		{
			$Credential = $Hashing->hash($Credential);
		}

		$Connection = $this->getConnection();

		$Sql = 'select '
			. $Connection->quoteIdentifier($this->getCredentialColumn())
			. ' from '
			. $Connection->quoteIdentifier($this->getTable())
			. ' where '
			. $Connection->quoteIdentifier($this->getIdentityColumn()) .'='
			. $Connection->quote($Identity)
			. ';';

		$Result = $Connection->query($Sql);
		if ( $Result->isError() )
		{
			return false;
		}

		$Row = $Result->fetchRow(Phools_Database_FetchMode::NUMBERED);
		if ( ! $Row )
		{
			return false;
		}

		if ( ( (string) $Credential == (string) $Row[0] ) )
		{
			return true;
		}

		return false;
	}

	/**
	 *
	 *
	 * @var Phools_Database_Connection_Interface
	 */
	private $Connection = null;

	/**
	 *
	 *
	 * @param Phools_Database_Connection_Interface $Connection
	 * @return Phools_Auth_Adapter_Database
	 */
	public function setConnection(
		Phools_Database_Connection_Interface $Connection)
	{
		$this->Connection = $Connection;
		return $this;
	}

	/**
	 *
	 * @return Phools_Database_Connection_Interface
	 */
	protected function getConnection()
	{
		return $this->Connection;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Table = '';

	/**
	 *
	 *
	 * @param string $Table
	 * @return Phools_Auth_Adapter_Database
	 */
	public function setTable($Table)
	{
		$this->Table = (string) $Table;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getTable()
	{
		return $this->Table;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $IdentityColumn = '';

	/**
	 *
	 *
	 * @param string $IdentityColumn
	 * @return Phools_Auth_Adapter_Database
	 */
	public function setIdentityColumn($IdentityColumn)
	{
		$this->IdentityColumn = (string) $IdentityColumn;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getIdentityColumn()
	{
		return $this->IdentityColumn;
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $CredentialColumn = '';

	/**
	 *
	 *
	 * @param string $CredentialColumn
	 * @return Phools_Auth_Adapter_Database
	 */
	public function setCredentialColumn($CredentialColumn)
	{
		$this->CredentialColumn = (string) $CredentialColumn;
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	protected function getCredentialColumn()
	{
		return $this->CredentialColumn;
	}

}
