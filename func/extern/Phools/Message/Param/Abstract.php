<?php

/**
 *
 *
 *
 */
abstract class Phools_Message_Param_Abstract
implements Phools_Message_Param_Interface
{

	/**
	 *
	 * @param string $Identifier
	 */
	public function __construct($Identifier)
	{
		$this->setIdentifier($Identifier);
	}

	/**
	 *
	 *
	 * @var string
	 */
	private $Identifier = '';

	/**
	 *
	 * @param string $Identifier
	 */
	protected function setIdentifier($Identifier)
	{
		$this->Identifier = (string) $Identifier;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Message_Param_Interface::getIdentifier()
	 */
	public function getIdentifier()
	{
		return $this->Identifier;
	}

}
