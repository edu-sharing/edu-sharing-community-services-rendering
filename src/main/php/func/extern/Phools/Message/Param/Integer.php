<?php

/**
 *
 *
 *
 */
class Phools_Message_Param_Integer
extends Phools_Message_Param_Abstract
{

	/**
	 *
	 * @param string $Identifier
	 * @param int $Value
	 */
	public function __construct($Identifier, $Value)
	{
		parent::__construct($Identifier);

		$this->setValue($Value);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Message_Param_Abstract::format()
	 */
	public function format(Phools_Locale_Interface $Locale = null)
	{
		if ( $Locale )
		{
			return $Locale->formatNumber($this->getValue(), 0);
		}

		return $this->getValue();
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $Value = 0;

	/**
	 *
	 *
	 * @param int $Value
	 * @return Phools_Message_Param_Integer
	 */
	protected function setValue($Value)
	{
		$this->Value = (int) $Value;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function getValue()
	{
		return $this->Value;
	}

}
