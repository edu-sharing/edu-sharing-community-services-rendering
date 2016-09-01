<?php

/**
 *
 *
 *
 */
class Phools_Database_Connection_Mysql
extends Phools_Database_Connection_Abstract
{

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::query()
	 */
	public function query($Sql)
	{
		$Adapter = $this->getAdapter();

		try {
			$ResultResource = $Adapter->query($Sql, $this->getConnectionResource());
			if ( is_resource($ResultResource) )
			{
				// successful select query with result-set
				$Result = new Phools_Database_Result_Resource($this, $ResultResource);
			}
			else
			{
				// successful query, but without result-set
				$Result = new Phools_Database_Result_Connection($this);
			}
		}
		catch(Exception $Exception)
		{
			$ErrorCode = $this->getErrorCode();
			$ErrorMessage = $this->getErrorMessage();

			$Result = new Phools_Database_Result_Error($this, $ErrorCode, $ErrorMessage);
		}

		return $Result;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::getErrorMessage()
	 */
	public function getErrorMessage(&$ResultResource = null)
	{
		$Adapter = $this->getAdapter();
		return $Adapter->getErrorMessage(
			$this->getConnectionResource(),
			$ResultResource);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::getErrorCode()
	 */
	public function getErrorCode(&$ResultResource = null)
	{
		$Adapter = $this->getAdapter();
		return $Adapter->getErrorCode(
			$this->getConnectionResource(),
			$ResultResource);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::affectedRows()
	 */
	public function affectedRows()
	{
		$Adapter = $this->getAdapter();
		return $Adapter->affectedRows($this->getConnectionResource());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::rowCount()
	 */
	public function rowCount(&$ResultResource)
	{
		$Adapter = $this->getAdapter();
		return $Adapter->rowCount($this->getConnectionResource(), $ResultResource);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::fetchRow()
	 */
	public function fetchRow(
		&$ResultResource,
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array())
	{
		$Adapter = $this->getAdapter();
		return $Adapter->fetchRow(
			$this->getConnectionResource(),
			$ResultResource,
			$FetchMode,
			$ClassName,
			$Args);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::fetchAll()
	 */
	public function fetchAll(
		&$ResultResource,
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array())
	{
		$Adapter = $this->getAdapter();
		return $Adapter->fetchAll(
			$this->getConnectionResource(),
			$ResultResource,
			$FetchMode,
			$ClassName,
			$Args);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::freeResult()
	 */
	public function freeResult(&$ResultResource)
	{
		$Adapter = $this->getAdapter();
		return $Adapter->freeResult($this->getConnectionResource(), $ResultResource);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::escape()
	 */
	public function escape($String)
	{
		$String = mysql_real_escape_string(
			$String,
			$this->getConnectionResource());

		return $String;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::quote()
	 */
	public function quote($String)
	{
		return '"'.$String.'"';
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Connection_Interface::quoteIdentifier()
	 */
	public function quoteIdentifier($String)
	{
		return '`'.$String.'`';
	}

}
