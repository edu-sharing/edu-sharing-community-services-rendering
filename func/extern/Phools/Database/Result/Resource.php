<?php

/**
 *
 *
 *
 */
class Phools_Database_Result_Resource
extends Phools_Database_Result_Abstract
{

	/**
	 *
	 * @param Phools_Database_Connection_Interface $Connection
	 * @param resource $ResultResource
	 */
	public function __construct(
		Phools_Database_Connection_Interface &$Connection, &$ResultResource)
	{
		parent::__construct($Connection, false);

		$this
			->setResultResource($ResultResource);
	}

	/**
	 * Free result if required.
	 *
	 */
	public function __destruct()
	{
		if ( ! $this->isFree() )
		{
			$this->free();
		}

		parent::__destruct();
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::rowCount()
	 */
	public function rowCount()
	{
		if ( $this->isFree() )
		{
			throw new Phools_Database_Exception('Cannot count rows from free\'d result');
		}

		$Connection = $this->getConnection();
		return $Connection->rowCount($this->getResultResource());
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::fetchField()
	 */
	public function fetchField($Offset = 0)
	{
		if ( $this->isFree() )
		{
			throw new Phools_Database_Exception('Cannot fetch from free\'d result');
		}

		$Connection = $this->getConnection();
		return $Connection->fetchField($this->getResultResource(), $Offset);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::fetchRow()
	 */
	public function fetchRow(
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array())
	{
		if ( $this->isFree() )
		{
			throw new Phools_Database_Exception('Cannot fetch from free\'d result');
		}

		$Connection = $this->getConnection();
		return $Connection->fetchRow($this->getResultResource(), $FetchMode, $ClassName, $Args);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::fetchAll()
	 */
	public function fetchAll(
		$FetchMode = Phools_Database_FetchMode::NUMBERED,
		$ClassName = 'stdClass',
		array $Args = array())
	{
		if ( $this->isFree() )
		{
			throw new Phools_Database_Exception('Cannot fetch from free\'d result');
		}

		$Connection = $this->getConnection();
		return $Connection->fetchAll($this->getResultResource(), $FetchMode, $ClassName, $Args);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::free()
	 */
	public function free()
	{
		if ( $this->ResultResource )
		{
			$Connection = $this->getConnection();
			$Connection->freeResult($this->ResultResource);

			$this->ResultResource = null;
		}

		return $this;
	}

	public function isFree()
	{
		if ( ! $this->ResultResource )
		{
			return true;
		}

		return false;
	}

	/**
	 *
	 *
	 * @var resource
	 */
	private $ResultResource = null;

	/**
	 *
	 *
	 * @param resource $ResultResource
	 * @return Phools_Database_Result_Abstract
	 */
	protected function setResultResource(&$ResultResource)
	{
		$this->ResultResource = $ResultResource;
		return $this;
	}

	/**
	 *
	 * @return resource
	 */
	protected function &getResultResource()
	{
		if ( ! $this->ResultResource )
		{
			throw new Phools_Database_Exception('No result-resource set.');
		}

		return $this->ResultResource;
	}

}
