<?php

/**
 *
 *
 *
 */
class Phools_Database_Result_Error
extends Phools_Database_Result_Abstract
{

	/**
	 *
	 * @param Phools_Database_Connection_Interface $Connection
	 */
	public function __construct(
		Phools_Database_Connection_Interface &$Connection,
		$ErrorMessage,
		$ErrorCode = '')
	{
		parent::__construct($Connection, true);

		$this
			->setErrorMessage($ErrorMessage)
			->setErrorCode($ErrorCode);
	}

	/**
	 *
	 * @var string
	 */
	private $ErrorMessage = '';

	/**
	 *
	 * @param string $ErrorMessage
	 * @return Phools_Database_Result_Error
	 */
	protected function setErrorMessage($ErrorMessage)
	{
		$this->ErrorMessage = (string) $ErrorMessage;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::getErrorMessage()
	 */
	public function getErrorMessage()
	{
		return $this->ErrorMessage;
	}

	/**
	 *
	 * @var string
	 */
	private $ErrorCode = '';

	/**
	 *
	 * @param string $ErrorCode
	 * @return Phools_Database_Result_Error
	 */
	protected function setErrorCode($ErrorCode = '')
	{
		$this->ErrorCode = (string) $ErrorCode;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::getErrorCode()
	 */
	public function getErrorCode()
	{
		return $this->ErrorCode;
	}

}
