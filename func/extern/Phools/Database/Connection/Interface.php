<?php

/**
 *
 *
 */
interface Phools_Database_Connection_Interface
{

	/**
	 *
	 * @param string $Database
	 *
	 * @throws Phools_Database_Exception
	 *
	 * @return Phools_Database_Connection_Interface
	 */
	public function setDatabase($Database);

	/**
	 *
	 * @param string $Username
	 *
	 * @throws Phools_Database_Exception
	 *
	 * @return Phools_Database_Interface
	 */
	public function setUsername($Username);

	/**
	 *
	 * @param int $Password
	 *
	 * @throws Phools_Database_Exception
	 *
	 * @return Phools_Database_Interface
	 */
	public function setPassword($Password);

	/**
	 *
	 * @param Phools_Database_Adapter_Interface $Adapter
	 *
	 * @return Phools_Database_Adapter_Interface
	 */
	public function open(Phools_Database_Adapter_Interface &$Adapter);

	/**
	 *
	 * @return Phools_Database_Adapter_Interface
	 */
	public function close();

	/**
	 *
	 * @return bool
	 */
	public function isEstablished();

	/**
	 *
	 * @param string $Sql
	 *
	 * @return Phools_Database_Result_Interface
	 */
	public function query($Sql);

	/**
	 *
	 * @param string $ResultResource
	 */
	public function getErrorMessage(&$ResultResource = null);

	/**
	 *
	 * @param string $ResultResource
	 */
	public function getErrorCode(&$ResultResource = null);

	/**
	 *
	 * @return int
	 */
	public function affectedRows();

	/**
	 *
	 * @param resource $ResultResource
	 */
	public function rowCount(&$ResultResource);

	/**
	 *
	 * @param resource $ResultResource
	 * @param string $FetchMode
	 * @param string $ClassName
	 * @param array $Args
	 */
	public function fetchRow(
		&$ResultResource,
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array());

	/**
	 *
	 * @param resource $ResultResource
	 * @param string $FetchMode
	 * @param string $ClassName
	 * @param array $Args
	 */
	public function fetchAll(
		&$ResultResource,
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array());

	/**
	 *
	 * @param resource $ResultResource
	 */
	public function freeResult(&$ResultResource);

	/**
	 *
	 * @param string $String
	 *
	 * @return string
	 */
	public function escape($String);

	/**
	 *
	 * @param string $String
	 *
	 * @return string
	 */
	public function quote($String);

	/**
	 *
	 * @param string $String
	 *
	 * @return string
	 */
	public function quoteIdentifier($String);

}
