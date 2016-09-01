<?php

/**
 *
 *
 */
interface Phools_Database_Adapter_Interface
{

	/**
	 * Connect to this adapter.
	 *
	 * @return bool
	 */
	public function open();

	/**
	 * Disconnect from this adapter.
	 *
	 * @return Phools_Database_Adapter_Interface
	 */
	public function close();

	/**
	 * Throws a Phools_Database_Exception when an error occured. Returns NULL
	 * if command successful, return a result-resource for query.
	 *
	 * @param string $Sql
	 *
	 * @throws Phools_Database_Exception
	 *
	 * @return Phools_Database_Result_Interface
	 */
	public function query($Sql);

	/**
	 * Not all adapters deliver a result-based error-message, but a connection-
	 * based error-message. So adapters which require a result-resource to
	 * deliver an error-message are allowed to throw an exception if
	 * result-resource is emtpy.
	 *
	 * @param resource $ResultResource
	 *
	 * @throws Phools_Database_Exception
	 *
	 * @return string
	 */
	public function getErrorMessage(&$ResultResource = null);

	/**
	 * Not all adapters deliver a result-based error-code, but a connection-
	 * based error-code. So adapters which require a result-resource to deliver
	 * an error-message are allowed to throw an exception if result-resource is
	 * emtpy.
	 *
	 * @param resource $ResultResource
	 *
	 * @throws Phools_Database_Exception
	 *
	 * @return string
	 */
	public function getErrorCode(&$ResultResource = null);

	/**
	 *
	 * @param resource $ResultResource
	 *
	 * @throws Phools_Database_Exception
	 *
	 * @return int
	 */
	public function affectedRows(&$ResultResource = null);

	/**
	 *
	 * @param resource $ResultResource
	 *
	 * @throws Phools_Database_Exception
	 *
	 * @return int
	 */
	public function rowCount(&$ResultResource);

	/**
	 *
	 * @param resource $ResultResource
	 * @param int $Offset
	 *
	 * @return string
	 */
	public function fetchField(&$ResultResource, $Offset = 0);

	/**
	 *
	 * @param resource $ResultResource
	 * @param string $FetchMode
	 * @param string $ClassName
	 * @param array $Args
	 *
	 * @return array
	 */
	public function fetchRow(&$ResultResource,
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = '',
		array $Args = array());

	/**
	 *
	 * @param resource $ResultResource
	 * @param string $FetchMode
	 * @param string $ClassName
	 * @param array $Args
	 *
	 * @return array
	 */
	public function fetchAll(&$ResultResource,
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = '',
		array $Args = array());

	/**
	 *
	 * @param resource $ResultResource
	 *
	 * @return Phools_Database_Adapter_Interface
	 */
	public function freeResult(&$ResultResource);

}
