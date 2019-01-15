<?php

/**
 *
 *
 *
 */
class Phools_Message_Param_String
extends Phools_Message_Param_Abstract
{

	/**
	 *
	 * @param string $Identifier
	 * @param string $String
	 */
	public function __construct($Identifier, $String)
	{
		parent::__construct($Identifier);

		$this->setString($String);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Message_Param_Abstract::format()
	 */
	public function format(Phools_Locale_Interface $Locale = null)
	{
		return $this->getString();
	}

	/**
	 *
	 *
	 * @var
	 */
	protected $String = '';

	/**
	 *
	 *
	 * @param  $String
	 * @return Phools_Message_Param_String
	 */
	public function setString($String)
	{
		$this->String = (string) $String;
		return $this;
	}

	/**
	 *
	 * @return
	 */
	protected function getString()
	{
		return $this->String;
	}

}
