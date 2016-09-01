<?php

/**
 *
 *
 *
 */
class Phools_Message_Param_Float
extends Phools_Message_Param_Abstract
{

	/**
	 *
	 *
	 * @param string $Identifier
	 * @param float $Value
	 * @param float $Decimals
	 */
	public function __construct($Identifier, $Value, $Decimals = 0)
	{
		parent::__construct($Identifier);

		$this
			->setValue($Value)
			->setDecimals($Decimals);
	}

	/**
	 * (non-PHPdoc)
	 * @see Phools_Message_Param_Abstract::format()
	 */
	public function format(Phools_Locale_Interface $Locale = null)
	{
		if ( $Locale )
		{
			return $Locale->formatNumber(
				$this->getValue(),
				$this->getDecimals());
		}

		return $this->getValue();
	}

	/**
	 *
	 *
	 * @var float
	 */
	protected $Value = 0.0;

	/**
	 *
	 *
	 * @param float $Value
	 * @return Phools_Message_Param_Integer
	 */
	protected function setValue($Value)
	{
		$this->Value = (float) $Value;
		return $this;
	}

	/**
	 *
	 * @return float
	 */
	public function getValue()
	{
		return $this->Value;
	}

	/**
	 *
	 *
	 * @var int
	 */
	protected $Decimals = 0;

	/**
	 *
	 *
	 * @param int $Decimals
	 * @return Phools_Message_Param_Integer
	 */
	protected function setDecimals($Decimals)
	{
		$this->Decimals = (int) $Decimals;

		return $this;
	}

	/**
	 *
	 * @return int
	 */
	public function getDecimals()
	{
		return $this->Decimals;
	}

}
