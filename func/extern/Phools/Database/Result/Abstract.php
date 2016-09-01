<?php

/**
 *
 *
 *
 */
abstract class Phools_Database_Result_Abstract
implements Phools_Database_Result_Interface
{

	/**
	 *
	 * @param Phools_Database_Connection_Interface $Connection
	 * @param resource $ResultResource
	 */
	public function __construct(
		Phools_Database_Connection_Interface &$Connection, $IsError = false)
	{
		$this
			->setConnection($Connection)
			->setIsError($IsError);
	}

	/**
	 *
	 */
	public function __destruct()
	{
		$this->Connection = null;
	}

	/**
	 * Protect yourself.
	 *
	 * @throws Phools_Database_Exception
	 */
	public function __clone()
	{
		throw new Phools_Database_Exception('Results cannot be cloned.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::getErrorCode()
	 */
	public function getErrorCode()
	{
		throw new Phools_Database_Exception('No implemented on this result-type.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::getErrorMessage()
	 */
	public function getErrorMessage()
	{
		throw new Phools_Database_Exception('No implemented on this result-type.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::affectedRows()
	 */
	public function affectedRows()
	{
		throw new Phools_Database_Exception('No implemented on this result-type.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::rowCount()
	 */
	public function rowCount()
	{
		throw new Phools_Database_Exception('No implemented on this result-type.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::fetchField()
	 */
	public function fetchField($Offset = 0)
	{
		throw new Phools_Database_Exception('No implemented on this result-type.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::fetchRow()
	 */
	public function fetchRow(
		$FetchMode = Phools_Database_FetchMode::ASSOC,
		$ClassName = 'stdClass',
		array $Args = array())
	{
		throw new Phools_Database_Exception('No implemented on this result-type.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::fetchAll()
	 */
	public function fetchAll(
		$FetchMode = Phools_Database_FetchMode::ASSOC,
		$ClassName = 'stdClass',
		array $Args = array())
	{
		throw new Phools_Database_Exception('No implemented on this result-type.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::free()
	 */
	public function free()
	{
		throw new Phools_Database_Exception('No implemented on this result-type.');
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::isFree()
	 */
	public function isFree()
	{
		throw new Phools_Database_Exception('No implemented on this result-type.');
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
	 * @return Phools_Database_Result_Abstract
	 */
	protected function setConnection(Phools_Database_Connection_Interface $Connection)
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
	 * @var bool
	 */
	protected $IsError = false;

	/**
	 *
	 *
	 * @param bool $IsError
	 * @return Phools_Database_Result_Abstract
	 */
	protected function setIsError($IsError)
	{
		$this->IsError = (bool) $IsError;
		return $this;
	}

	/**
	 *
	 * @return bool
	 */
	public function isError()
	{
		return $this->IsError;
	}

}
