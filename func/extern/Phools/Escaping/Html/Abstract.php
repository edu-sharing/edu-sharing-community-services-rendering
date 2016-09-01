<?php

/**
 * Base-class for html-escaping objects utilizing htmlspecialchars() and
 * related functions.
 *
 *
 */
abstract class Phools_Escaping_Html_Abstract
extends Phools_Escaping_Abstract
{

	/**
	 *
	 * @param string $Charset
	 * @param int $QuotingStyle
	 */
	public function __construct(
		$Charset = 'UTF-8',
		$QuotingStyle = ENT_COMPAT)
	{
		parent::__construct($Charset);

		$this->setQuotingStyle($QuotingStyle);
	}

	/**
	 *
	 *
	 * @var int
	 */
	private $QuotingStyle = ENT_COMPAT;

	/**
	 *
	 *
	 * @param int $QuotingStyle
	 * @return Phools_Escaping_Html_Abstract
	 */
	public function setQuotingStyle($QuotingStyle)
	{
		$this->QuotingStyle = (int) $QuotingStyle;
		return $this;
	}

	/**
	 *
	 * @return int
	 */
	protected function getQuotingStyle()
	{
		return $this->QuotingStyle;
	}

}
