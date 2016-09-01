<?php

/**
 *
 *
 *
 */
class Phools_Database_Result_Connection
extends Phools_Database_Result_Abstract
{

	/**
	 *
	 * @param Phools_Database_Connection_Interface $Connection
	 */
	public function __construct(
		Phools_Database_Connection_Interface &$Connection)
	{
		parent::__construct($Connection, false);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Database_Result_Interface::affectedRows()
	 */
	public function affectedRows()
	{
		$Connection = $this->getConnection();
		return $Connection->affectedRows();
	}

}
